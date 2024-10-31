=== Publish approval ===
Tags: content, quality control, publish, approval
Requires at least: 4.6
Tested up to: 4.9
Requires PHP: 5.6
Stable tag: trunk
License: MIT
License URI: https://opensource.org/licenses/MIT

Disallow publishing content until it is approved by a specified number of people.

== Description ==
This plugin allows selecting content types (post, page, custom) that are subject to quality control, where specified
editors have to approve them before they cna be published.

Features:
1. Separate configuration for each content type.
2. Define list of users who can approve content.
3. Define the minimum number of approvals requires for the content to be publishable.

== Screenshots ==

1. Settings page
2. Publish widget when the logged-in user can approve a post
4. Publish widget with blocked approval because the logged-in user can't approve your own posts
5. Publish widget with post ready to be published or unapproved

== Changelog ==

= 1.1 =
* Fixed critical bug which made it impossible to save settings for the first time due to HTML validation of hidden elements
* Added an option to allow authors to approve their own content
* Added integration with Polylang so that approval metadata is not copied over to new translations

= 1.0 =
* Initial release

== Installation ==
After activating the plugin go to its settings to decide which content types should be subject to the approval process.