=== WatchMyBack24 ===
Contributors: ozeflyer
Donate link: http://sit.24stunden.de/watchmyback24
Tags: comments, spam, spamfighting
Requires at least: 2.0.0
Tested up to: 2.5
Stable tag: 0.7.6

WatchMyBack24 is a powerful spamfighting plugin against comment or trackback spam.

== Description ==

WatchMyBack24 verifies trackbacks and comments by checking against spam keywords, existing backlinks, a special keysum within comment against BBCode and Hooray-Jobs.

== Installation ==

1. Download the ZIP archiv from the Wordpress.org plugin repository.
2. Unzip the ZIP-archive.
3. Upload the entire folder to your plugins folder (./wp-content/plugins/)
4. Login into the administrative area of your weblog and activate the plugin in the Plugins section.
5. Well done.

== Frequently Asked Questions ==

== Screenshots ==

== Arbitrary section ==

FROM CHANGE.LOG

Apr 26th, 2008:
     + Version 0.7.6 in progress

Apr 10th, 2008:
     + Version 0.7.5 released
     + using Wordpress 2.5 styles
     + removing wmb_setStats()
     + depricated: auto-updating is no longer supported
     + new additional filters
     + blocking comments that combines gecoities/gmail accounts (porn-shit)


Dec 19th, 2007:
     + Version 0.7.4 released
     + moving the menu item from 'Management' to 'Comments'

Dec 5th, 2007:
     + Version 0.7.3 released
     + Public stats available
     + fixed download URL in updater
     + new filters

Jul 11th, 2007:
     + Version 0.7.2 released
     + new filter.php file
     + automatically checks if a newer version of this plugin exists

May 24th, 2007:
     + Version 0.7.1 released
     + Overview of spammed postings
     + new filter

Mar 23rd, 2007:
     + Version 0.7.0 released
     + new: link counter in comments / 1 link = approve / > 1 link = spam
     + new: using pre_comment_approved
     + new: using pre_comment_user_ip
     + new: using a simple filter array
     + new: details on caught spam
     + classifying trackback or comments as 'spam' or not by click
     + delete single spam by click
     + block future spam by deactivating ping/comment functionality of posting by click
     + disable automatically IP-logging (due to german law)
     + depricated: trackback.log (you can delete this file from your plugins folder)
     + 0.6.x and all earlier versions are depricated

Jan 20th, 2007:
     + Version 0.6.6 released
     + additional "hooray"-job definitions added

Jan 5th,  2007:
     + Version 0.6.5 released
     + better check for "Hooray jobs" and "BBCode"
     + additional "Horray jobs" definitions

Nov 18th, 2006:
     + Version 0.6.4 released
     + Fixes bugs to 0.6.3
     + additional BBcode bans for comments

Nov 16th, 2006:
     + Version 0.6.3 released
     + Fixes bugs from 0.6.2
     + Logging is now mandatory! Removing the logging-flag.
     + New Option: Deny comments with BBCode-links.
     + New Option: Deny comments with hoorays like "Great Site" or "Nice Site"
     + Removing the viewing of logfile in an iframe => now logfile as included html-text
     + Sending error-messages in standard XML for failed trackbacks

Nov 15th, 2006:
     + Version 0.6.2 released
     + New Option: Maxfailures - how many trackbacks have to fail before pings will be closed
     + New logfile with less informations
     + No IP-Logging due changes in german law

Sep 18th, 2006:
     + Version 0.6 released
     + Option: Enable/Disable logfile
     + Option: Enable/Disable auto-trim logfile-size to 512 kB
     + Option: Enable/Disable auto-disabling of ping-ability
     + auto-disabling the ping-ability of postings if a trackback failed 2 times
     + Bug on opening non-existent trackback-URLs fixed.

Sep 1st, 2006:
     + Version 0.5 released
     + banning complete top-leveldomains (very, very, very beta)

Aug 28th, 2006:
     + Version 0.4.1 released
     + Bug on blocking comments fixed

Aug 21,   2006:
     + Version 0.4 released
     + Read thorugh the logs in the admin-area of Wordpress
     + Reset the logfile
     + Testing if our backlink-URL is dynamically integrated into the trackback-URL

Aug 15,   2006:
     + Version 0.3 released
     + a better logfile
     - we disable the viewing of the logfile in the admin-area, sorry for that.

Aug 13,   2006:
     + Version 0.2 released
     + adding a logfile function to log all trackbacks to your Wordpress weblog

Aug 06,   2006:
     + Version 0.1 released


