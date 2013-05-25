=== Wysija Newsletters ===
Contributors: wysija, benheu, jon1op
Tags: newsletter, newsletters, manager newsletter, newsletter signup, newsletter widget, subscribers, post notification, email subscription, email alerts, automatic newsletter, auto newsletter, autoresponder, autoresponders, follow up, email marketing, email, emailing, subscription
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: 2.1.2

Send your post notifications or newsletters from WordPress easily, and beautifully.

== Description ==

Drag and drop your posts, images, social icons in our visual editor. Pick one of 20 themes. Change fonts and colors on the fly. Send the latest posts when you want or as a single newsletter. Configuration is dummy proof. This baby is fully supported.


= 2 minute video demo =

http://vimeo.com/35054446

= Wysija's post notification demo =

http://vimeo.com/46247528

= Features =

* Drag & drop visual editor, an HTML-free experience
* Post notifications, like Feedburner, Subscribe2 or MailChimp's RSS-to-Email
* [Selection of over 20 themes](http://www.wysija.com/newsletter-templates-wordpress/). Photoshop files included
* Get stats for each newsletter: opens, clicks, unreads, unsubscribes
* Add a subscription form in your sidebar or pages
* Your newsletters look the same in Gmail, iPhone, Android, Outlook, Yahoo, Hotmail, etc.
* Your WordPress users have their own list
* Import subscribers from MailChimp, Aweber, etc.
* One click import from MailPress, Tribulant, Satollo, Subscribe2, etc.
* Single or double opt-in, your choice
* Send with your web host, Gmail or SMTP
* Segment your lists based on opened, clicked & bounced
* Autoresponders, i.e. "Send email 3 days after someone subscribes"
* Free version is limited to 2000 subscribers

= Premium version =

[Wysija Premium](http://www.wysija.com/wordpress-newsletter-plugin-premium/) offers these nifty extra features:

* Unlimited number of subscribers
* Stats for individual subscribers (opened, clicked)
* Total clicks for each link in your newsletter
* Access to Premium themes
* Automated bounce handling. Keeps your list clean, avoid being labeled a spammer
* Unlimited spam score tests with mail-tester.com
* Improve deliverability with DKIM signature
* We trigger your email queue, like a real cron job
* Don't reinstall. Simply activate!
* Priority support

[Visit our Premium page](http://www.wysija.com/wordpress-newsletter-plugin-premium/).

= Upcoming major release =

* Subscriber profiles, ie. gender, city, or whatever you want
* Dozens of mini improvements based on user feedback
* Possibility to insert your own HTML in newsletter

= Future releases =

* New stats page
* Custom post types support
* Display a list of past newsletters sent in a page of your site (shortcode)

= Support =

We got a dedicated website just to help you out. And we're quite quick to reply.

[support.wysija.com](http://support.wysija.com/)

= Translations in your language =

Translations are included in the plugin. Join the translation teams on [our Transifex page](https://www.transifex.com/projects/p/wysija/).

* Your language: [get a Premium license in exchange for your translation](http://support.wysija.com/knowledgebase/translations-in-your-language/)
* Chinese
* Czech
* Danish
* Dutch
* French
* German
* Greek
* Hungarian
* Italian
* Norwegian
* Polish
* Portuguese PT
* Portuguese BR
* Romanian
* Russian
* Slovak
* Spanish
* Swedish
* Turkish

== Installation ==

There are 3 ways to install this plugin:

Note: premium users don't need to reinstall anything. It's the same plugin.

= 1. The super easy way =
1. In your Admin, go to menu Plugins > Add
1. Search for `Wysija`
1. Click to install
1. Activate the plugin
1. A new menu `Wysija` will appear in your Admin

= 2. The easy way =
1. Download the plugin (.zip file) on the right column of this page
1. In your Admin, go to menu Plugins > Add
1. Select the tab "Upload"
1. Upload the .zip file you just downloaded
1. Activate the plugin
1. A new menu `Wysija` will appear in your Admin

= 3. The old and reliable way (FTP) =
1. Upload `wysija-newsletters` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. A new menu `Wysija` will appear in your Admin

== Frequently Asked Questions ==

= Got questions? =

Our [support site](http://support.wysija.com/) has plenty of articles and a ticketing system.

= Submit your feature request =

We got a User Voice page where you can [add or vote for new features](http://wysija.uservoice.com/forums/150107-feature-request).

== Screenshots ==

1. Sample newsletters.
2. The drag & drop editor.
3. Subscriber management.
4. Newsletter statistics.
5. Subscriber statistics (Premium version).
6. Sending method configuration in Settings.
7. Importing subscribers with a CSV.

== Changelog ==

= 2.1.2 - 2012-09-05 =

* major speed improvement and cache plugin compatibility
* added utf-8 encoding in iframe loaded subscription form.
* added security check for translated links (dutch translation issue with view in browser link)
* removed _nonce non sense in the visitors subscription forms.
* fixed loading issue in subscription form
* fixed styling issue in subscription form
* fixed accents issue in subscription form
* fixed DKIM activation settings not being saved
* fixed non translated unsubscribe and view in browser links
* fixed warning showing up on some servers configuration when sending a preview of the newsletter
* fixed popups in IE8 and improved overall display
* fixed openssl_error_string function breaking our settings screen on some configurations.
* fixed error with dkim on server without openssl functions
* fixed bounce error with the rule unsubscribe user

= 2.1.1 - 2012-09-02 =

* fixed update 2.1 error : Duplicate column name "is_public" may have caused some big slow down on some servers and some auto post to facebook (deepest apologies).
* fixed Outlook issue where text blocks would not have the proper width

= 2.1 - 2012-08-31 =

* added ability for subscribers to change their email and lists.
* added "View it in your browser" option.
* added advanced access rights with capabilities for subscribers management, newsletter management, settings and subscription widget.
* added new WordPress 3.3 plupload used when possible to use.
* added mail-tester.com integration for Premium (fight against spam).
* added DKIM signature for Premium to improve deliverability.
* added the possibility to preview your newsletter without images in visual editor.
* added background colors for blocks within the visual editor.
* added alternate background colors for automatic latest post widget.
* added possibility to add total number of subscribers in widget with shortcode.
* added widget option "Display label within for Email field".
* improved email rendering and email clients compatibility including the new Outlook 2013
* improved image upload with ssl.
* improved compatibility with access rights plugins like "Advanced Access Manager" or "User Role Editor".
* improved import system with clearer message.
* improved subscription widget, added security if there is no list selected.
* improved Auto newsletter edition, warning added before pausing it.
* improved popups for the visual editor (themes, images, add link,...)
* updated TinyMCE to latest version, the editor now reflects the newsletter styles
* compatibility with [Magic Action Box](http://wordpress.org/extend/plugins/magic-action-box/).
* fixed links style in headings.
* fixed no default value in optin form when JS disabled.
* fixed issue with automatic latest post widget where one article could appear more than once.

= 2.0.9.5 - 2012-08-15 =

* fixed post notification hook when post's status change from publish to draft and back to publish.
* fixed firewall 2 avoid troubles with image uploader automatically
* fixed problem of confirmation page on some servers when pretty links activated on wysijap post. Default is params link now.

= 2.0.9 - 2012-08-03 =

* improved debug mode with different level for different needs
* added logging function to monitor post notification process for instance
* improved send immediately post notification (in some case the trigger was not working... using different WordPress hook now)
* fixed post notification interface (step1 and step3) not compatible with WordPress lower than 3.3
* fixed issue when duplicating sent post notifications. You should not be able to copy a child email and then change it's type like an automatic newsletter etc...
* fixed zip format error when uploading your own theme (this error was happenning on various browsers)

= 2.0.8 - 2012-07-27 =

* added default style for subscription notification which was lost
* fixed php error on subscription form creation
* fixed php error on helper back

= 2.0.7 - 2012-07-21 =

* fixed strict error appearing on servers below php version 5.4
* fixed on export to a csv translate fields and don't get the columns namekeys
* added non translated 'Loading...' string on subscription's frontend

= 2.0.6 - 2012-07-20 =

* fixed unreliable WP_PLUGIN_URL when dealing with https constants now using plugins_url() instead
* fixed automatic newsletter resending itself on unsubscribe
* fixed when unsubscribing and registering to some lists, you will not be re-registered to your previous lists
* fixed issue with small height images not displaying in email
* fixed issue with post excerpt in automatic posts
* improved php 5.4 strictness compatibility

= 2.0.5 - 2012-07-13 =

* added extended check of caching plugin activation
* added security to disallow directory browsing
* added subscription form working now with Quick-cache and Hyper cache(Already working with WP Super Cache && W3 Total Cache)
* added onload attribute on iframe subscription form which seems more reliable
* added independant cron manager wysija_cron.php
* added cleaning the queue of deleted users or deleted emails through phpmyadmin for instance
* added theme menu erasing Wysija's menu when in the position right below ours

= 2.0.4 - 2012-07-05 =

* added for dummies check that list exists or subscription form widget not editable
* fixed problem with plugin wordpress-https when doing ajax subscription
* fixed issue with scheduled articles not being sent in post notification
* fixed rare issue when inserting a WordPress post would trigger an error
* fixed issue wrong count of ignored emails when importing
* fixed multi forms several send confirmation emails on one subscribing request
* fixed subject title in email template

= 2.0.3 - 2012-06-26 =

* fixed theme activation not working
* fixed google analytics code on iframe subscription forms
* fixed post notification bug with wrong category selected when fetching articles
* fixed issue regarding category selection in auto responder / post notifications
* fixed dollar sign being stripped in post titles
* fixed warning and notices when adding a list
* fixed on some server unsubscribe page or confirmation page redirecting to 404
* improved iframe system works now with short url and multiple forms

= 2.0.2 - 2012-06-21 =

* fixed missing title on widget when cache plugin activated
* fixed update procedure to Wysija version "2.0" failed! on some MySQL servers
* fixed W3C validation for subscription form with empty action: replace with #wysija
* fixed forbidden iframe subfolder corrected to a home url with params
* improved theme installation with PclZip
* fixed missing previously sent auto newsletter on newsletters page
* fixed broken url for images uploaded in WordPress 3.4
* fixed "nl 2 br" on unsubscribed notification messages for admins
* added meta noindex on iframe forms to avoid polluting Google Analytics
* added validation of lists on subscription form
* fixed issue with image alignment in automatic newsletters
* fixed url & alternative text encoding in header/footer
* fixed images thumbs not displaying in Images tab
* fixed popups' CSS due to WordPress 3.4 update
* fixed issues when creating new lists from segment

= 2.0.1 - 2012-06-16 =

* fixed subscribers not added to the lists on old type of widget

= 2.0 - 2012-06-15 =

* Added post notifications
* Added auto responders
* Added scheduling (send in future)
* allow subscribers to select lists
* embed subscription form outside your WordPress site (find code in the widget)
* Subscription forms compatibility with W3 Total Cache and WP Supercache
* Load social bookmarks from theme automatically
* Several bug fixes and micro improvements
* Ability to send snail mail

= 1.1.5 - 2012-05-21 =

* improved report after importing csv
* fixed Warning: sprintf() /helpers/back.php on some environnements
* fixed roles for creating newsletters or managing subscribers "parent roles can edit as well as child roles if a child role is selected"
* fixed cron wysija's frequencies added in a cleaner way to avoid conflict with other plugins
* fixed w3c validation on confirmation and unsubscription page
* improved avoiding duplicates on environment with high sending frequencies
* removed php show errors lost in resolveConflicts

= 1.1.4 - 2012-05-14 =

* added last name to recipient name in header
* fixed automatic redirection for https links in newsletter
* fixed conflict with Advanced Custom Fields (ACF) plugin in the newsletter editor
* fixed conflict with the WpToFacebook plugin
* fixed validation on import of addresses with trim
* fixed dysfunctional unsubscribe link when Google Analytics campaign inserted
* added alphanumeric validation on Google Analytics input
* display clicked links in stats without Google Analytics parameters
* fixed page/post newsletter subscription widget when javascript conflict returns base64 string
* fixed WP users synch when subscriber with same email already exists
* fixed encoded url recorded in click stats
* added sending status In Queue to differentiate with Not Sent
* fixed automatic bounce handling
* added custom roles and permissions

= 1.1.3 - 2012-03-31 =

* fixed unsubscribe link redirection
* fixed rare issue preventing Mac users from uploading images
* added Norwegian translation
* added Slovak translation

= 1.1.2 - 2012-03-26 =

* fixed automatically recreates the subscription page when accidentally deleted
* fixed more accurate message about folder permissions in wp-content/uploads
* fixed possibility to delete synchronisable lists
* fixed pagination on subscribers lists' listing
* fixed google analytics tracking code
* fixed relative path to image in newsletter now forced to absolute path
* fixed widget alignment when labels not within field default value is now within field
* fixed automatic bounce handling error on some server.
* fixed scripts enqueuing in frontend, will print as long as there is a wp_footer function call in your theme
* fixed theme manager returns error on install
* fixed conflict with the SmallBiz theme
* fixed conflict with the Events plugin (wp-events)
* fixed conflict with the Email Users plugin (email-users)
* fixed outlook 2007 rendering issue

= 1.1.1 - 2012-03-13 =

* fixed small IE8 and IE9 compatibility issues
* fixed fatal error for new installation
* fixed wysija admin white screen on wordpres due to get_current_screen function
* fixed unsubscribe link disappearing because of qtranslate fix
* fixed old separators just blocked the email wizard
* fixed unsubscribe link disappearing because of default color
* fixed settings panel redirection
* fixed update error message corrected :"An error occured during the update" sounding like update failed even though it succeeded
* fixed rendering of aligned text
* fixed daily report email information
* fixed export: first line with comma, the rest with semi colon now is all semi colon
* fixed filter by list on subscribers when going on next pages with pagination
* fixed get_avatar during install completely irrelevant
* fixed wordpress post in editor when an article had an image with height 0px
* fixed when domain does not exist, trying to send email, we need to flag it as undelivered after 3 tries and remove it from the queue
* fixed user tags [user:firstname | defaul:subscriber] left over when sent through queue and on some users
* fixed get_version when wp-admin folder doesn't exist...
* fixed Bulk Unsubscribe from all list "why can't I add him"

= 1.1 - 2012/03/03 =

* support for first and last names
* 14 new themes. First Premium themes
* added social bookmarks widget
* added new divider widget
* added first name and last name feature in subscription form, newsletter content and email subject
* header is now image only and not text/image
* small changes in Styles tab of visual editor
* new full width footer image area (600px)
* added transparency feature to header, footer, newsletter
* newsletter width for content narrowed to 564px
* improved line-height for titles in text editor
* fixed Outlook and Hotmail padding issue with images
* improved speed of editor
* possibility to import automatically and keep in Sync lists from all major plugins: MailPress, Satollo, WP-Autoresponder, Tribulant, Subscribe2, etc.
* possibility to change "Unsubscribe" link text in footer
* choose which role can edit subscribers
* preview of newsletter in new window and not in popup
* added possibility to choose between excerpt or full article on inserting WP post
* theme management with API. Themes are now externalized from plugin.
* removed numbered lists from text editor because of inconsistent display, notably Outlook

= 1.0.1 - 2012/01/18 =

* added SMTP TLS support, useful for instance with live.com smtp
* added support for special Danish chars in email subscriptions
* fixed menu position conflict with other themes and plugins
* fixed subscription form works with jquery 1.3, compatible for themes that use it
* fixed issue of drag & drop of WP post not working with php magic quotes
* fixed permissions issue. Only admins could use the plugin despite changing the permissions in Settings > Advanced.
* fixed display of successful subscription in widget displays better in most theme
* fixed synching of WordPress user registering through frontend /wp-login.php?action=register
* fixed redirection unsubscribe link from preview emails
* fixed cross site scripting security threat
* fixed pagination on newsletter statistics's page
* fixed javascript conflict with Tribulant's javascript's includes
* improved detection of errors during installation

= 1.0 - 2011/12/23 =
* Premium upgrade available
* fix image selector width in editor
* fix front stats of email when email preview and show errors all
* fix front stats of email when show errors all
* fix import ONLY subscribed from external plugins such as Tribulant or Satollo
* fix retrieve wp.posts when time is different on mysql server and apache server
* fix changing encoding from utf8 to another was not sending
* newsletter background colour now displays in new Gmail
* less confusing queue sending status
* updated language file (pot) with 20 or so modifications

= 0.9.6 - 2011/12/18 =
* fixed subscribe from a wysija confirmation page bug
* fixed campaigns "Column does not exists in model .."
* fixed address and unsubscribe links appearing at bottom of newsletter a second time
* fixed menu submenu no wysija but newsletters no js
* fixed bug statistics opened_at not inserted
* fixed bug limit subscribers updated on subscribers delete
* fixed daily cron scandir empty dir
* fixed subscribe from frontend without javascript error
* fixed subscribe IP server validation when trying in local
* fixed CSS issues with Wordpress 3.3
* improving interface of email sending in the newsletter's listing
* added delete newsletter option
* added language pot file
* added french translation

= 0.9.2 - 2011/12/12 =
* fixed issue with synched users on multisite(each site synch its users only)
* fixed compatibility issue with wordpress 3.3(thickbox z-index)
* fixed issue with redundant messages after plugin import
* fixed version number display

= 0.9.1 - 2011/12/7 =
* fixed major issue with browser check preventing Safari users from using the plugin
* fixed issue with wp_attachment function affecting Wordpress post insertion
* fixed issue when importing subscribers (copy/paste from Gmail)
* fixed issue related to Wordpress MU
* minor bugfixes

= 0.9 - 2011/12/23 =
* Hello World.
