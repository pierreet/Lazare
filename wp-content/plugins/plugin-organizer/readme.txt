=== Plugin Organizer ===
Contributors: foomagoo
Donate link: 
Tags: plugin organizer, load order, organize plugins, plugin order, sort plugin, group plugin, disable plugins by post, disable plugins by page, disable plugins by custom post type, turn off plugins for post, turn off plugins for page, turn off plugins for custom post type
Requires at least: 3.1
Tested up to: 3.4.2
Stable tag: 2.3.2

This plugin allows you to do the following:
1. Change the order that your plugins are loaded.
2. Selectively disable plugins by any post type or wordpress managed URL.

== Description ==

This plugin allows you to do the following:
1. Change the order that your plugins are loaded.
2. Selectively disable plugins by any post type or wordpress managed URL.
3. Adds grouping to the plugin admin age.

Icons provided by http://www.iconizer.net
Icon Set: http://www.userinterfaceicons.com/download.php
Icon Set: http://www.fatcow.com/free-icons/
Icon Set: http://bogo-d.deviantart.com/

== Installation ==

1. Extract the downloaded Zip file.
2. Upload the 'plugin-organizer' directory to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. You can either use the menu item under settings in the WordPress admin called Organize Plugins or there will be a drop down list for each plugin in the plugin admin page to select the load order.

NOTE: To enable selective plugin loading you must move the /wp-content/plugins/plugin-organizer/lib/PluginOrganizerMU.class.php file to /wp-content/mu-plugins.  If the mu-plugins directory does not exist you can create it.  The plugin will attempt to create this directory and move the file itself when activated.  Depending on your file permissions it may not be successful.


== Frequently Asked Questions ==
Q. How do I enable the selective plugin loading functionality?

A. Go to the Plugin Organizer settings page and check the enable radio button under selective plugin loading.  Then visit your homepage.  Finally return to the Plugin Organizer settings page and see if the enable radio button is still checked.  If it is not then you are running an old version of the MU component.  Copy the PluginOrganizerMU.class.php file to the mu-plugins folder then deactivate and reactivate the plugin.  Repeat these steps to ensure that the plugin is working.  Remember that you will need to update the PluginOrganizerMU.class.php file whenever the plugin is updated and check your settings afterward.

Q. Does this plugin work with wordpress multi-site?

A. Yes it has been tested on several multi-site installs.  Both subdomain and sub folder types.

Q. Does this plugin work with custom post types?

A. Yes it has been tested with custom post types.  You can add support for your custom post types on the settings page.

Q. Does this only apply to WP MU or all types of WP installs?
"NOTE: To enable selective plugin loading you must move the /wp-content/plugins/plugin-organizer/lib/PluginOrganizerMU.class.php file to /wp-content/mu-plugins. If the mu-plugins directory does not exist you can create it.  The plugin will attempt to create this directory and move the file itself when activated.  Depending on your file permissions it may not be successful."

A. The mu-plugins folder contains "Must Use" plugins that are loaded before regular plugins. The mu is not related to WordPress MU. This was added to regular WordPress in 3.0 I believe. I only placed this one class in the MU folder because I wanted to have my plugin run as a normal plugin so it could be disabled if needed. 


Q. In what instance would this plugin be useful?

A. 
  Example 1: If you have a large number of plugins and don't want them all to load for every page you can disable the unneeded plugins for each individual page.  Or you can globally disable them and enable them for each post or page you will need them on.
  Example 2: If you have plugins that conflict with eachother then you can disable the plugins that are conflicting for each indivdual post or page.
  Example 3: If you have plugins that conflict with eachother then you can disable the plugins globally and activate them only on posts or pages where they will be used.

== Screenshots ==

1. Plugin admin page example.
2. Settings page example.
3. Global plugins page.
4. URL admin page.
5. Page edit screen.

== Changelog ==

= 2.3.2 =
When the user hadnt set the number of plugins displayed per page it was being defaulted to 20.  Changed it to default to 999.
Set $endChar to an empty string in PluginOrganizerMU.class.php to prevent debug notices.

= 2.3.1 =
Fixed a javascript error on the URL admin page.
Fixed logic for Global plugins where all plugins were disabled none where getting disabled.
Fixed use of REQUEST_URI.  Now it Splits the REQUEST_URI to trim off URL arguments.
Added ability to reset plugin order back to wordpress default.
Renamed some javascript functions and consolidated some of them.

= 2.3 =
Removed the old admin pages.  The plugins can now be managed directly on the plugins page.
Redesigned the settings page to use ajax queries instead of reloading the page to save settings.
Redesigned the URL admin to use ajax to save and edit URL's instead of reloading the page.
Moved most of the javascript out of the main class and into template files.
Added a setting to preserve the plugin data when it is deactivated.  The plugin data including database tables and MU plugin file can now be removed on deactivation.

= 2.2.1 =
Added ability to ignore the protocol when matching the requested URL by checking a checkbox on the settings page.

= 2.2 =
Added Fuzzy URL matching to the arbitrary URL admin.  URLs can now effect their children.
Added nonce checking to URL admin.
Restructured forms on the main settings page.

= 2.1.3 =
Added checks to ensure plugin load order cant be changed when all plugins are not viewable on the page.

= 2.1.2 =
Fixed group view on plugin organizer page when the plugins per page has been set too low or extremely high.
Fixed setting of the show old admin page when either save settings button is clicked.

= 2.1.1 =
Adding option to show the old admin pages.

= 2.1 =
Added better group management to the plugin admin page.
Removed group management pages from the menu.

= 2.0 =
Added drag and drop functionality to the plugin admin page.
Added group links to the top of the plugin admin page that replace the group dropdown.
Added better checking to make sure the plugin load order can only be changed when all plugins are being displayed.

= 1.2.3 =
Fixed URL admin page.  Enabled plugins list wasnt saving on creation.

= 1.2.2 =
Fixed typo in recreate permalinks function.
Centralized the nonce generation so the PluginOrganizer class now holds it.

= 1.2.1 =
Adding license tag to header and replacing global path variables with path variables inside the PluginOrganizer class.

= 1.2 =
Removed a conditional and some whitespace from the main plugin file becasue it may have been causing issues with activation.  
Adding menu and header icons to pretty up the plugin.

= 1.1 =
Added option to settings page so the selective plugin loading can be enabled or disabled for the admin pages.

= 1.0 =
Added ability to disable plugins in the admin using the Arbitrary URL admin page.
Fixed some flow issues and html problems on the PO admin pages.
Properly escaped all queries

= 0.9 =
Added admin area for entering arbitrary URL's to allow plugin management for url's that don't have a post tied to them.
Added some form validation for the admin screens.

= 0.8.3 =
Fixing a bug with globaly disabled plugins not being enabled on individual posts
Fixing bug with version number not updating when plugin is updated.

= 0.8.2 =
Fixing wrong version number on plugins page.
Adding FAQ's

= 0.8.1 =
Added missing tpl/globalPlugins.php file.

= 0.8 =
Adding custom post type support.

= 0.7.3 =
Fixed activation errors when mu-plugins folder is not writable.

= 0.7.2 =
Fixed bug that reordered plugins back to default when plugins were activated or deactivated.
Fixed jQuery loading indicator on plugin admin.
Fixed Bulk Actions on plugin admin

= 0.7.1 =
Removed display of plugin load order functions on plugin admin if the view is paged.  To view load order functions on plugin admin you must display all active plugins on one page.

= 0.7 =
Wordpress 3.1 fixes for jQuery 1.4.4

= 0.6 =
Added functionality to disable plugins globally and selectively enable them for posts and pages.
Added functionality to create the mu-plugins folder and move the MU plugin class when activated.
New databse layout.  Will be created when plugin is activated.

= 0.5 =
Added functionality to selectively disable plugins by post or page.  
There is now a Must Use plugin component that comes with the main plugin.
To enable selective plugin loading you must move the /wp-content/plugins/plugin-organizer/lib/PluginOrganizerMU.class.php file to /wp-content/mu-plugins.
If the mu-plugins directory does not exist you must create it.


= 0.4.1 =
Fixed empty items in plugin list.

= 0.4 =
Added grouping to the plugin admin page.
Improved ajax requests
Added ajax loading image.
Added page to create and organize plugin groups.

= 0.3 =
Added ajax requests to the settings page so both forms now use ajax.
Added nonce checking to the ajax requests.  
Requires user to have activate_plugins capability.

= 0.2 =
Made function to reorder the plugins on plugin admin page in the order they will be loaded.
Redid the sort functions to use PHP's array_multisort.

= 0.1.1 =
improved the ajax requests on the plugin admin page.  

= 0.1 =
Initial version.

== Upgrade Notice ==

= 2.3.2 =
When the user hadnt set the number of plugins displayed per page it was being defaulted to 20.  Changed it to default to 999.
Set $endChar to an empty string in PluginOrganizerMU.class.php to prevent debug notices.

= 2.3.1 =
Fixed a javascript error on the URL admin page.
Fixed logic for Global plugins where all plugins were disabled none where getting disabled.
Fixed use of REQUEST_URI.  Now it Splits the REQUEST_URI to trim off URL arguments.
Added ability to reset plugin order back to wordpress default.
Renamed some javascript functions and consolidated some of them.

= 2.3 =
Removed the old admin pages.  The plugins can now be managed directly on the plugins page.
Redesigned the settings page to use ajax queries instead of reloading the page to save settings.
Redesigned the URL admin to use ajax to save and edit URL's instead of reloading the page.
Moved most of the javascript out of the main class and into template files.
Added a setting to preserve the plugin data when it is deactivated.  The plugin data including database tables and MU plugin file can now be removed on deactivation.

= 2.2.1 =
Added ability to ignore the protocol when matching the requested URL by checking a checkbox on the settings page.

= 2.2 =
Added Fuzzy URL matching to the arbitrary URL admin.  URLs can now effect their children.
Added nonce checking to URL admin.
Restructured forms on the main settings page.

= 2.1.3 =
Added checks to ensure plugin load order cant be changed when all plugins are not viewable on the page.

= 2.1.2 =
Fixed group view on plugin organizer page when the plugins per page has been set too low or extremely high.
Fixed setting of the show old admin page when either save settings button is clicked.

= 2.1.1 =
Adding option to show the old admin pages.

= 2.1 =
Added better group management to the plugin admin page.
Removed group management pages from the menu.

= 2.0 =
Added drag and drop functionality to the plugin admin page.
Added group links to the top of the plugin admin page that replace the group dropdown.
Added better checking to make sure the plugin load order can only be changed when all plugins are being displayed.

= 1.2.3 =
Fixed URL admin page.  Enabled plugins list wasnt saving on creation.

= 1.2.2 =
Fixed typo in recreate permalinks function.
Centralized the nonce generation so the PluginOrganizer class now holds it.

= 1.2.1 =
Adding license tag to header and replacing global path variables with path variables inside the PluginOrganizer class.

= 1.2 =
Removed a conditional and some whitespace from the main plugin file becasue it may have been causing issues with activation.  

= 1.1 =
Added option to settings page so the selective plugin loading can be enabled or disabled for the admin pages seperately to prevent problems.

= 1.0 =
Added ability to disable plugins in the admin using the Arbitrary URL admin page.
Fixed some flow issues and html problems on the PO admin pages.
Properly escaped all queries

= 0.9 =
Added plugin management for url's that don't have a post tied to them.

= 0.8.1 =
Added missing tpl/globalPlugins.php file.

= 0.8 =
Adding custom post type support.

= 0.7.3 =
Fixed activation errors when mu-plugins folder is not writable.

= 0.7.2 =
Bug fixes for plugin admin page.  Plugin order could be lost on activation or deactivation.  Top bulk actions fixed.

= 0.7.1 =
Fixing plugin admin page.  Could cause plugins to be disabled in old version.

= 0.7 =
Fixes for jQuery 1.4.4

= 0.6 =
Added functionality to disable plugins globally and selectively enable them for posts and pages.
Added functionality to create the mu-plugins folder and move the MU plugin class when activated.
New databse layout.  Will be created when plugin is activated.
