=== Plugin Name ===
Contributors: CrypTech Studios
Donate link: http://cryptechstudios.com/wp-plugins/bliptv-admin-menu
Tags: blip, blip.tv, api, video, upload
Requires at least: 3.0
Tested up to: 4.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds an admin menu for interfacing with the Blip.tv API allowing uploads, deletes and edits of your Blip.tv hosted videos
== Description ==

Adds an admin menu for interfacing with the Blip.tv API allowing uploads, deletes and edits of your Blip.tv hosted videos

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload bliptv-admin-menu.zip to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Admin -> Blip.tv API -> settings and enter your Blip.tv account information.
1. Click "Import Video Info From Blip" to import or refresh all your video data from your Blip.tv account.

== Frequently Asked Questions ==

= Does this download the actual video files from Blip.tv? =

No.  This will only download the data for each video and store that data in a table in your wordpress database.

= Why don't my uploads succeed? =

Uploading of your video files to your hosting server may not work for a couple reasons.
A few things to check are:
* Does your hosting provider allow file uploads?
* Your hosting provider's max upload file size limit: (upload_max_filesize) in php.ini.
* Your hosting provider's max post size limit: (post_max_size) in php.ini.


== Screenshots ==

== Changelog ==
= 2.0 =
* fine tuned and compatability tested for 4.2.2.
= 1.0 =
* updated path info.
= 0.10 =
* Initial version.
