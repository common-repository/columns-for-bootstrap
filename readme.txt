=== Columns for Bootstrap ===
Contributors: CWRU CAS IT Group
Tags: columns, shortcode, bootstrap
Requires at least: 3.5
Tested up to: 4.6
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This WordPress plugin adds a columns shortcode that utilizes Bootstrap for mobile responsiveness.

== Description ==

Code is adapted from the plugin 'Columns' by Konstantin Kovshenin.

Bootstrap is an HTML, CSS, and JS framework that is used to develop responsive, mobile websites. It employs a grid system that divides the viewport into twelve distinct columns. On desktops, the columns scale proportionally to the width of the viewing area while on mobile devices and tablets the columns stack on top of one another. This plugin takes advantage of that grid system to allow for WordPress content to be split up equally between the twelve columns.

To create a group of columns, use the **[column-group]** shortcode. To add columns to a group, use the **[column]** shortcode. For example:

	[column-group]
        	[column]This is the first column[/column]
        	[column]This is the second column[/column]
	[/column-group]

The first column will occupy the first six columns of the Bootstrap grid while the second column will fill the remaining six.

= Spanning Columns =

The plugin’s columns can also be manually spanned across the Bootstrap grid. By adding the *span* attribute to the starting column shortcode, the number of Bootstrap columns that the WordPress columns take up can be assigned. For example:

	[column-group]
        	[column span=“4”]This is the first column.[/column]
        	[column span=“6”]This is the second column.[/column]
        	[column span=“2”]This is the third column.[/column]
	[/column-group]

The columns will take up four, six, and two of the Bootstrap columns respectively. Note: The span value is actually a ratio so if the total value of the spans in a column group does not equal twelve, then the plugin will assign the column sizes by means of percentage.

== Installation ==

1. Make sure that the current WordPress theme is built with Bootstrap.
2. Download archive and unzip in wp-content/plugins or install via Plugins - Add New.
3. Activate the **Columns for Bootstrap** plugin.
4. Check the top of the Plugins page for any warnings.
5. Begin using the **[column-group]** and **[column]** shortcodes.

== Screenshots ==

1. Columns and Bootstrap example
2. Editor example

== Changelog ==

= 1.0.1 =
* Replaced the deprecated wp_get_sites with get_sites

= 1.0 =
* First version
