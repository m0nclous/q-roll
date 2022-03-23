=== Disable Author Archives ===
Contributors: freemp
Tags: author, archives, pages, links, disable, remove, 404
Requires at least: 2.9
Tested up to: 5.9
Stable tag: trunk
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Disable Author Archives completely removes author archives and makes the web server return status code 404 ('Not Found') instead.

== Description ==

A simple, lightweight WordPress plugin to completely disable author archives/pages (regardless of whether the corresponding author really exists) and make the web server return status code 404 ('Not Found') instead. Related links will also be either disabled or completely removed.

The plugin does not require any configuration. Once activated, it will start doing its job.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/disable-author-archives` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

== Frequently Asked Questions ==

= Where can I find the plugin's configuration settings? =

The plugin does not require any kind of configuration. Author archives are disabled as soon as the plugin is activated.

== Changelog ==

= 1.3.1 =
* Removed View link from admin's Users list. Suggested by @khaiknievel. Thanks!

= 1.3.0 =
* Opened plugin for translations.
* Set filter callback priorities to lowest possible value.

= 1.2.1 =
* Subtle optimization.

= 1.2.0 =
* Resolved redirection conflict.

= 1.1.0 =
* Added no-cache headers.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.2.0 =
* Fixes a redirection issue which might cause instabilities.  Upgrade immediately.
