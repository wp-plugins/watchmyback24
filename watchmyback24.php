<?php

define('WMB_VERSION','0.7.6');

/*
Plugin Name: WatchMyBack24
Plugin URI: http://sit.24stunden.de/watchmyback24
Description: WatchMyBack24 verifies trackbacks and comments by checking against spam keywords, existing backlinks, a special keysum within comment against BBCode and Hooray-Jobs.
Author: Sebastian Schwaner
Author URI: http://sit.24stunden.de/watchmyback24
Version: 0.7.6
*/


/* ----------------------------------------------------------------------------

© Copyright 2006  Sebastian Schwaner

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

THANKS TO: The developers of Akismet and ValidateTB for the inspiration
to combine all the spamfighting technologies in one plugin. Also for all the
support, suggestions and comments by the users of "Quietschbunt",
"wp-plugins.net" and the Wordpress-Community.

---------------------------------------------------------------------------- */

# --- LOAD FILTER
require("filter.php");

# --- ADD FILTERS AND ACTIONS
add_action('admin_menu','wmb_addGUI');
add_filter('pre_comment_approved','wmb_approval');
add_filter('pre_comment_user_ip','wmb_remove_ip');
add_filter('preprocess_comment','wmb_check');

# --- FUNCTIONS
function wmb_addGUI() {

	global $wpdb;
	$result = $wpdb->get_var(" SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'spam' ");
	add_submenu_page( 'edit-comments.php','WatchMyBack24','WatchMyBack24 ('.$result.')',9,__FILE__,'wmb_setGUI' );
	}

function wmb_validate( $from_url, $to_url ) {

	if( $fp = fopen($from_url,"r") ) :
		while( !feof($fp) ) $retr .= fgets($fp,128);
		fclose($fp);
	else :
		return false;
	endif;

	strtolower( $retr );
	strtolower( $to_url );

	if( !substr_count($retr,$to_url) ) return false;

	$html_start = strpos($retr,"<html>",0);
	$html_stop = strpos($retr,"</html>",0);
	$body_start = strpos($retr,"<body>",$html_start);
	$body_stop = strpos($retr,"</body>",$body_start);
	$backlink = strpos($retr,$to_url,$body_start);

	if( $backlink < $html_start || $backlink > $html_stop ) return false;
	if( $backlink < $body_start || $backlink > $body_stop ) return false;

	return true;

}

function wmb_remove_ip( $comment_user_ip ) {
	return $comment_user_ip = "0.0.0.0";
}

function wmb_approval( $comment_approved ) {

	global $wmb_status;

	if( $wmb_status == 'spam' || $wmb_status == '0' ) :
      return $comment_approved = $wmb_status;
   else :
      return $comment_approved;
   endif;
}

function wmb_check( $comment_data ) {

	global $wpdb, $wmb_status, $user_ID, $wmb_comment_filter;
	$wmb_options = get_option('WMB_OPTIONS');
     $wmb_status = '1';  // on incoming comment_status has to be true

	# --- Check comments only
	if( !isset( $user_ID ) && $comment_data['comment_type'] == '' ) :
		$comment = $comment_data['comment_content'];

		# --- check filter
		foreach( $wmb_comment_filter as $var ) :
			if( substr_count( strtolower( $comment ),$var ) > 0 ) :
				$wmb_status = 'spam';
				break;
			endif;
		endforeach;

		# --- check links
		if( substr_count( strtolower( $comment ), 'http://' ) == 1 ) $wmb_status = '0';
		if( substr_count( strtolower( $comment ), 'http://' ) > 1 ) $wmb_status = 'spam';

          # --- block GeoCities-URL with GMail-account
          if( substr_count( $comment_data['comment_author_email'], '@gmail.com') > 0 && substr_count( $comment_data['comment_author_url'], 'geocities.com') > 0 )
               $wmb_status = 'spam';

	endif;


     // if incoming comment is a trackback
	if( $comment_data['comment_type'] == 'trackback') :

          $comment = $comment_data['comment_content'];

          $result = $wpdb->get_results(" SELECT guid FROM $wpdb->posts WHERE ID=".$comment_data['comment_post_ID']);
		$posting = $result[0]->guid;
		$trackback = $comment_data['comment_author_url'];

          // VALIDATE TRACKBACK/PINGBACK
          if( wmb_validate( $trackback,$posting ) == false ) $wmb_status = 'spam';

          // EXECUTE FILTER ON
          foreach( $wmb_comment_filter as $var ) :
               if( substr_count( strtolower( $comment ),$var ) > 0 ) :
                    $wmb_status = 'spam';
                    break;
               endif;
          endforeach;
	endif;


     // if it's spam increase the counter
	if( $wmb_status == 'spam' ) :
		$wmb_options['counter'] += 1;
		update_option('WMB_OPTIONS',$wmb_options);
	endif;

	return $comment_data;

	}

#--- GUI
function wmb_setGUI() {

   global $wpdb;
   $pos = 1;
   $col = "#F1F1F1";
   $wmb_options = get_option('WMB_OPTIONS');

   if( empty($wmb_options['counter'] ) ) :
            $wmb_options['counter'] = 0;
            update_option('WMB_OPTIONS',$wmb_options);
   endif;


   if( @$_POST['cmd'] == 'delall' ) :
            $wpdb->query(" DELETE FROM $wpdb->comments WHERE comment_approved = 'spam' ");
   endif;

   if( @$_GET['cmd'] == 'nonspam' ) :
            $wpdb->query(" UPDATE $wpdb->comments SET comment_approved = '1' WHERE comment_ID=".$_GET['cid'] );
            $wpdb->query(" UPDATE $wpdb->posts SET comment_count=comment_count + 1 WHERE ID=".$_GET['pid'] );

   elseif( @$_GET['cmd'] == 'del' ) :
            $wpdb->query(" DELETE FROM $wpdb->comments WHERE comment_ID=".$_GET['cid'] );

   elseif( @$_GET['cmd'] == 'block' ) :
            $wpdb->query(" UPDATE $wpdb->posts SET comment_status='closed', ping_status='closed' WHERE ID=".$_GET['pid'] );

   endif;

?>


<?php // OUTPUT OF ALL SPAM CLASSIFIED COMMENTS ?>
<?php $result = $wpdb->get_results(" SELECT * FROM $wpdb->comments WHERE comment_approved = 'spam' ORDER BY comment_date DESC"); ?>

<div id="wpbody">
     <div class="wrap">
          <h2>Manage your SPAM attacks</small></h2>
          <p>WatchMyBack24 has caught <strong><?php echo $wmb_options['counter']; ?> spam comments</strong> for you since you first installed it.</p>

          <?php if( ! count($result) ) : ?><strong>Congratulation!</strong> It seems to be a lucky day for you.<?php endif; ?>
     </div>
</div>

<?php if( $result ) : ?>
<div id="wpbody">
     <div class="wrap">

          <div class="tablenav">
               <form method="post">
               <input type="hidden" name="cmd" value="delall">
               <input type="submit" value="Delete all SPAM comments" class="button-secondary" />
               </form>
          </div>

          <br/>

          <table  class="widefat">
               <thead>
                    <tr>
                         <th scrope="col">#</th>
                         <th scope="col">Kommentar</th>
                         <th scope="col">Datum</th>
                         <th scope="col" class="action-links">Aktionen</th>
                    </tr>
               </thead>
               <tbody id="the-comment-list" class="list:comment">

               <?php foreach( $result as $var ) : ?>
               <?php $post = $wpdb->get_results(" SELECT guid,post_title FROM $wpdb->posts WHERE ID=$var->comment_post_ID "); ?>
               <?php $spammed_postings[$post[0]->guid]++; ?>
                    <tr class="comment">
                         <td style="text-align:center;"><?php echo "$pos."; $pos++; ?></td>
                         <td class="comment">
                              <p class="comment-author">
                                   <span class='row-title' href=""><?php echo $var->comment_author; ?></span><br/>
                                   <?php if( $var->comment_author_email ) : ?><a href="mailto:<?php echo $var->comment_author_email; ?>"><?php echo $var->comment_author_email; ?></a><?php endif; ?> |
                                   <?php if( $var->comment_author_url ) : ?><a href="<?php echo $var->comment_author_url; ?>"><?php echo $var->comment_author_url; ?></a><?php endif; ?><br/>
                              </p>
                              <p><?php echo $var->comment_content; ?></p>
                              <p class="comment-author"><strong>Posting:</strong> <a href="<?php echo $post[0]->guid; ?>" title="See posting"><?php echo $post[0]->post_title; ?></a></p>
                         </td>
                         <td><?php echo date("M d, Y",strtotime($var->comment_date)); ?></td>
                         <td class="action-links">
                              <a href="?page=watchmyback24/watchmyback24.php&cmd=nonspam&cid=<?php echo $var->comment_ID; ?>&pid=<?php echo $var->comment_post_ID; ?>" title="Classify as Non-Spam">Accept</a> |
                              <a href="?page=watchmyback24/watchmyback24.php&cmd=del&cid=<?php echo $var->comment_ID; ?>" title="Delete this comment">Delete</a> |
                              <a href="?page=watchmyback24/watchmyback24.php&cmd=block&pid=<?php echo $var->comment_post_ID; ?>" title="Deactivate Comments/Trackbacks for this Posting">Close</a>
                         </td>
                    </tr>
               <?php endforeach; ?>
               </tbody>
          </table>
     </div>
</div>



<?php // CLASSIFIED SPAM COMMENTS ORDERED BY ATTACKED POSTING ?>
<br/><br/>
<div id="wpbody">
     <div class="wrap">
          <h2>Attacked postings</h2>

          <p>Here's the list of the attacked postings ordered by SPAM comments. You're able to close the attacked posting for comments or track-/pingbacks with a click on the "CLOSE"-action.</p>

          <table class="widefat">
               <thead>
                    <tr>
                         <th scope="col">Attacked Posting</th>
                         <th scope="col">SPAMs</th>
                         <th scope="col">Actions</th>
                    </tr>
               </thead>
               <tbody id="the-comment-list" class="list:comment">
               <?php arsort($spammed_postings); ?>
               <?php while(list($key, $val) = each($spammed_postings)) : ?>
               <?php $pid = $wpdb->get_var(" SELECT ID FROM $wpdb->posts WHERE guid='$key' "); ?>
               <?php $comment_status = $wpdb->get_var(" SELECT comment_status FROM $wpdb->posts WHERE guid='$key' "); ?>
               <?php $ping_status = $wpdb->get_var(" SELECT ping_status FROM $wpdb->posts WHERE guid='$key' "); ?>
                    <tr class="comment">
                         <td class="comment"><a href="<?php echo $key; ?>" title="Gehe zu"><?php echo $key; ?></a></td>
                         <td><?php echo $val; ?></td>
                         <td>
                              <?php if( $comment_status != 'closed' && $ping_status != 'closed' ) : ?><a href="?page=watchmyback24/watchmyback24.php&cmd=block&pid=<?php echo $pid; ?>" title="Deactivate Comments/Trackbacks for this Posting">Close</a>
                         <?php else : ?>Disabled.<?php endif; ?>
                         </td>
                    </tr>
               <?php endwhile; ?>
               </tbody>
          </table>
     </div>
</div>

<?php endif; // IF SPAM EXISTS ?>

<br/><br/>
<div id="wpbody">
     <div class="wrap">
     <p>This Wordpress weblog is saved by the plugin <strong><a href="http://sit.24stunden.de/watchmyback24/">WatchMyBack24 <?php echo WMB_VERSION; ?></a></strong><br/>
     &copy; Copyright 2008 by sit. small it-solutions. </p>
     </div>
</div>

<?php } # end GUI ?>
