=== WP JW Player ===
Contributors: Mario Mansour
Donate link: http://www.tubepress.net/
Tags: wordpress, flash player, wp player, video player, tubepress, wp flash player
Requires at least: 2.8
Tested up to: 3.1
Stable tag: trunk

WP JW Player is customizable flash player with embed function, rss feeds which allows you to publish video and text content at the same time.

== Description ==

[WP JW Player](http://www.tubepress.net/wp-jw-player) is customizable flash player with embed function, rss feeds which allows you to publish video and text content at the same time.

== Installation ==

The plugin is simple to install:

1. Download `wp-jw-player.zip`
2. Unzip
3. Upload 'wp-jw-player' directory to your '/wp-content/plugins' directory
4. Go to the plugin management page and enable the plugin
5. Configure the plugin from 'Settings->WP JW Player'

== Change Log ==

1.6 -	Added image attribute to allow showing a static image at video init. (This parameter is optional)
1.5 -	Fixed bug of not having unique ids for the div element when embedding multiple videos in the same post.
1.4 - 	Added autoplay option
		Fixed embed option in admin panel
		Added documentation and usage
1.3 - 	Updated flash player 
1.2 - 	Added attribution link to embed code
1.1 - 	Fixed bug with div id to allow multiple embeds on the same page
1.0	- 	First version

== Usage ==

Add JW Player to a post/page by inserting **[wp-jw-player src="http://www.example.com/video.flv"]** into the post/page.

You can even add a Youtube video with custom width and height like this **[wp-jw-player src="http://www.youtube.com/watch?v=WsGmZO4N0g0" width="400" height="350"]**.

If you want to allow your visitors to embed the player on their websites and blog you can simply do that by adding **[wp-jw-player src="http://www.youtube.com/watch?v=WsGmZO4N0g0" width="400" height="350" embed="true"]**.

What about fetching rss feeds related to a certain keyword **[wp-jw-player src="http://www.youtube.com/watch?v=WsGmZO4N0g0" width="400" height="350" rsstag="JW Player"]**.

When you use the `rsstag` parameter, WP JW Player will use the default feed source defined in the settings to fetch the feeds. By default, the plugin uses Google News as a feed source,
but you can change that to any source you like. You should be careful to use %keyword as the query keyword when you defined the default source. %keyword will be replaced by the `rsstag` parameter later on.

Another way to do it is to include an absolute rss feed source like this **[wp-jw-player src="http://www.youtube.com/watch?v=WsGmZO4N0g0" feed="http://www.tubepress.net/feed"]**.

All the parameter can have default values to be used when you omit them. Defaults can be defined in **'Settings->WP JW Player'**.


Full documentation can be found on the [WP JW Player](http://www.tubepress.net/wp-jw-player) page | Powered By [TubePress.NET](http://www.tubepress.net/)