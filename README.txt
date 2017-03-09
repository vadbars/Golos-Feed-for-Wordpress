=== Golos Feed ===
Contributors: minitekgr, vadbars
Tags: Golos, Golos feed, Golos posts, Golos articles, Golos widget
Requires at least: 4.6
Tested up to: 4.7.3
Stable tag: 1.0.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A simple Wordpress plugin that displays a feed of your Golos posts.

== Description ==

Display Golos posts from any Golos username.

== Installation ==

1. Install the Golos Feed plugin either via the WordPress plugin directory, or by uploading the files to your web server (in the `/wp-content/plugins/` directory).
2. Activate the Golos Feed plugin through the 'Plugins' menu in WordPress.
3. Navigate to the 'Golos Feed' settings page and configure your settings.
4. Use the shortcode `[golos-feed]` in your page, post or widget to display your Golos posts.
5. You can display multiple Golos feeds by using shortcode options, for example: `[golos-feed username="wordpress-tips" postscount="5"]`

= Display your Feed =

**Single Golos Feed**

Copy and paste the following shortcode directly into the page, post or widget where you'd like the Golos feed to show up: `[golos-feed]`

**Multiple Golos Feeds**

If you'd like to display multiple Golos feeds then you can set different settings directly in the shortcode like so: `[golos-feed username="wordpress-tips" postscount="5"]`

You can display as many different Golos feeds as you like by just using the shortcode options below. For example:
`[golos-feed]`
`[golos-feed username="another_username"]`
`[golos-feed username="another_username" postscount="5" postimage="false" postreward="false"]`

See the table below for a full list of available shortcode options:

= Shortcode Options =
* **General Options**
* **username** - A Golos Username - Example: `[golos-feed username="wordpress-tips"]`
*
* **Post Options**
* **postscount** - Total posts in feed (integer) - Example: `[golos-feed postscount="5"]`
* **postimage** - Show post image (true or false) - Example: `[golos-feed postimage="true"]`
* **posttitle** - Show post title (true or false) - Example: `[golos-feed posttitle="true"]`
* **postcontent** - Show post content (true or false) - Example: `[golos-feed postcontent="false"]`
* **wordlimit** - Word limit for post content (integer) - Example: `[golos-feed wordlimit="20"]`
* **postreward** - Show post reward (true or false) - Example: `[golos-feed postreward="false"]`
* **postdate** - Sort post date (true or false) - Example: `[golos-feed postdate="true"]`
* **postauthor** - Show post author (true or false) - Example: `[golos-feed postauthor="false"]`
* **posttag** - Show post tag (true or false) - Example: `[golos-feed posttag="true"]`
* **postvotes** - Show post votes (true or false) - Example: `[golos-feed postvotes="false"]`
* **postreplies** - Show post replies (true or false) - Example: `[golos-feed postreplies="true"]`

== Changelog ==

= 1.0.0 =
* Launched the Golos Feed plugin.
