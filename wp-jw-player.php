<?php
/*
Plugin Name: WP JW Player
Plugin URI: http://www.tubepress.net/wp-jw-player
Description: WP JW Player is customizable flash player with embed function, rss feeds which allows you to publish video and text content at the same time.
Version: 1.7
Author: Mario Mansour
Author URI: http://www.mariomansour.org/
============================================================================================================
1.7 -	Removed SWF files from the plugin due to Copyright issues, replaced with external player link. 
1.6 -	Added image attribute to allow showing a static image at video init. (This parameter is optional)
1.5 -	Fixed bug of not having unique ids for the div element when embedding multiple videos in the same post.
1.4 - 	Added autoplay option
		Fixed embed option in admin panel
		Added documentation and usage
1.3 - 	Updated flash player 
1.2 - 	Added attribution link to embed code
1.1 - 	Fixed bug with div id to allow multiple embeds on the same page
1.0	- 	First version

============================================================================================================
This software is provided "as is" and any express or implied warranties, including, but not limited to, the
implied warranties of merchantibility and fitness for a particular purpose are disclaimed. In no event shall
the copyright owner or contributors be liable for any direct, indirect, incidental, special, exemplary, or
consequential damages (including, but not limited to, procurement of substitute goods or services; loss of
use, data, or profits; or business interruption) however caused and on any theory of liability, whether in
contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of
this software, even if advised of the possibility of such damage.

For full license details see license.txt
============================================================================================================ */

include dirname (__FILE__).'/plugin.php';
include dirname (__FILE__).'/models/jw-player.php';
//include dirname (__FILE__).'/widget.php';

define('WP_JW_PLAYER_TEXT_DOMAIN', 'wp-jw-player');
define('WP_JW_PLAYER_DEFAULT_WIDTH', 450);
define('WP_JW_PLAYER_DEFAULT_HEIGHT', 350);
define('WP_JW_PLAYER_DEFAULT_IMAGE', '');
define('WP_JW_PLAYER_DEFAULT_AUTOPLAY',0);
define('WP_JW_PLAYER_DEFAULT_EMBED', 'true');
define('WP_JW_PLAYER_DEFAULT_FEED', 'http://news.google.com/news?q=%keyword&pz=1&cf=all&ned=us&hl=en&cf=all&output=rss');
define('WP_JW_PLAYER_DEFAULT_RSSTAG', '');
define('WP_JW_PLAYER_DEFAULT_JW_PLAYER', 'http://player.longtailvideo.com/player.swf');
define('WP_JW_PLAYER_ADMIN_REFERRER', 'wpjp_jw_player_options');
/**
 * The TP Mini Games plugin
 *
 * @package wp-jw-player
 **/

class wpjp_JWPlayerAdmin extends wpjp_JWPlayerPlugin
{
	/**
	 * Constructor sets up page types, starts all filters and actions
	 *
	 * @return void
	 **/
	function wpjp_JWPlayerAdmin() {
		$this->register_plugin (WP_JW_PLAYER_TEXT_DOMAIN, __FILE__);
		
		$this->add_action('wp_print_scripts');
		$this->add_action('wp_print_styles');
		
		$this->add_shortcode('wp-jw-player', 'shortcode');
		
		if (is_admin ()) {
			$this->add_action('admin_menu');
			$this->add_filter('admin_head');
			
			$this->add_action('init', 'init', 15);
			$this->add_action('wp_dashboard_setup');
			
			$this->add_filter('contextual_help', 'contextual_help', 10, 2);
			$this->register_plugin_settings( __FILE__ );
		}
	}
	
	/**
	 * Plugin settings
	 *
	 * @return void
	 **/
	function plugin_settings ($links)	{
		$settings_link = '<a href="options-general.php?page='.basename( __FILE__ ).'">'.__('Settings', WP_JW_PLAYER_TEXT_DOMAIN).'</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
	
	/**
	 * Setup dashboard
	 *
	 * @return void
	 **/
	function wp_dashboard_setup() {
		if (function_exists ('wp_add_dashboard_widget'))
			wp_add_dashboard_widget ('dashboard_wpjp', __ ('WP JW Player', WP_JW_PLAYER_TEXT_DOMAIN), array (&$this, 'wpjp_dashboard'));
	}
	
	/**
	 * Dashboard feeds
	 *
	 * @return void
	 **/
	function wpjp_dashboard() {
		$news = fetch_feed( 'http://www.tubepress.net/feed' );
			
		if ( false === $plugin_slugs = get_transient( 'plugin_slugs' ) ) {
			$plugin_slugs = array_keys( get_plugins() );
			set_transient( 'plugin_slugs', $plugin_slugs, 86400 );
		}
			
		foreach ( array( 'news' => __('News') ) as $feed => $label ) {
			if ( is_wp_error($$feed) || !$$feed->get_item_quantity() )
				continue;
			
			$items = $$feed->get_items(0, 5);
			
			// Pick a random, non-installed plugin
			while ( true ) {
				// Abort this foreach loop iteration if there's no plugins left of this type
				if ( 0 == count($items) )
					continue 2;
			
				$item_key = array_rand($items);
				$item = $items[$item_key];
			
				list($link, $frag) = explode( '#', $item->get_link() );
			
				$link = esc_url($link);
				if ( preg_match( '|/([^/]+?)/?$|', $link, $matches ) )
					$slug = $matches[1];
				else {
					unset( $items[$item_key] );
					continue;
				}
			
				// Is this random plugin's slug already installed? If so, try again.
				reset( $plugin_slugs );
				foreach ( $plugin_slugs as $plugin_slug ) {
					if ( $slug == substr( $plugin_slug, 0, strlen( $slug ) ) ) {
						unset( $items[$item_key] );
						continue 2;
					}
				}
			
				// If we get to this point, then the random plugin isn't installed and we can stop the while().
				break;
			}
			
			// Eliminate some common badly formed plugin descriptions
			while ( ( null !== $item_key = array_rand($items) ) && false !== strpos( $items[$item_key]->get_description(), 'Plugin Name:' ) )
				unset($items[$item_key]);
			
			if ( !isset($items[$item_key]) )
				continue;
			
			// current bbPress feed item titles are: user on "topic title"
			if ( preg_match( '/&quot;(.*)&quot;/s', $item->get_title(), $matches ) )
				$title = $matches[1];
			else // but let's make it forward compatible if things change
				$title = $item->get_title();
			$title = esc_html( $title );
			
			$description = esc_html( strip_tags(@html_entity_decode($item->get_description(), ENT_QUOTES, get_option('blog_charset'))) );
						
			echo "<h4>$label</h4>\n";
			echo "<h5><a href='$link'>$title</a></h5>\n";
			echo "<p>$description</p>\n";
		}	
	}
	
	/**
	 * Render dashboard
	 *
	 * @return void
	 **/
	function dashboard() {
		//$settings  = $wpjp->get_current_settings ();
		//$simple   = $wpjp->modules->get_restricted ($wpjp->get_simple_modules (), $settings, 'page');
		
		$this->render_admin ('dashboard', array ('simple' => $simple, 'advanced' => $advanced));
	}
	
	/**
	 * Initialization function
	 *
	 * @return void
	 **/
	function init() {
		// Allow some customisation over core features
		if (file_exists (dirname (__FILE__).'/settings.php'))
			include dirname (__FILE__).'/settings.php';
		else
		{
			define ('WP_JW_PLAYER_OPTIONS', __ ('WP JW Player', WP_JW_PLAYER_TEXT_DOMAIN));
			define ('WP_JW_PLAYER_ROLE', 'manage_options');
		}
	}
	
	/**
	 * Add WP JW Player menu
	 *
	 * @return void
	 **/
	function admin_menu() {
		add_options_page(WP_JW_PLAYER_OPTIONS, WP_JW_PLAYER_OPTIONS, WP_JW_PLAYER_ROLE, basename (__FILE__), array ($this, 'admin_options'));
	}
	
	/**
	 * Display the options screen
	 *
	 * @return void
	 **/
	function admin_options() {
		// Save
		if (isset($_POST['update']) && check_admin_referer (WP_JW_PLAYER_ADMIN_REFERRER)) {
			$options['default_jw_player_width'] = $_POST['default_jw_player_width'];
			$options['default_jw_player_height'] = $_POST['default_jw_player_height'];
			$options['default_jw_player_autoplay'] = $_POST['default_jw_player_autoplay'];
			$options['default_jw_player_embed'] = $_POST['default_jw_player_embed'];
			$options['default_jw_player_rsstag'] = $_POST['default_jw_player_rsstag'];
			$options['default_jw_player_feed'] = $_POST['default_jw_player_feed'];
			
			JWPlayer::update_options($options);
		}
		
		$this->render_admin('options', array ('options' => JWPlayer::get_options()));
	}
	
	/**
	 * Insert JS into the header
	 *
	 * @return void
	 **/
	function wp_print_scripts() {
		global $wp_scripts;
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('wpjp-popup', $this->url ().'/js/popup.js', array ('jquery'), $this->version());
		wp_enqueue_script('wpjp-swfobject', $this->url ().'/js/swfobject.js', array ('jquery'), $this->version());
		
		// Stop this being called again
		//remove_action('wp_print_scripts', array(&$this, 'wp_print_scripts'));
	}
	
	/**
	 * Insert CSS into the header
	 *
	 * @return void
	 **/
	function wp_print_styles() {
		wp_enqueue_style('wpjp-popup', $this->url ().'/css/popup.css', array (), $this->version ());
		
		// Stop this being called again
		//remove_action('wp_print_styles', array(&$this, 'wp_print_styles'));
	}
	
	/**
	 * Insert CSS and JS into administration page
	 *
	 * @return void
	 **/
	function admin_head() {
		
	}
	
	/**
	 * Get plugin version number
	 *
	 * @return $version
	 **/
	function version() {
		$plugin_data = implode ('', file (__FILE__));
		
		if (preg_match ('|Version:(.*)|i', $plugin_data, $version))
			return trim ($version[1]);
		return '';
	}
	
	/**
	 * Display contextual help
	 *
	 * @return $help
	 **/
	function contextual_help($help, $screen) {
		if ($screen == 'settings_page_wpjp') {
			$help .= '<h5>' . __('WP JW Player Help', WP_JW_PLAYER_TEXT_DOMAIN) . '</h5><div class="metabox-prefs">';
			$help .= '<a href="http://www.tubepress.net/wp-jw-player" target="_blank">'.__ ('WP JW Player Documentation', WP_JW_PLAYER_TEXT_DOMAIN).'</a><br/>';
			$help .= '</div>';
		}
		
		return $help;
	}
	
	/**
     * Function to handle shortcodes.
     *
     * @return void
     **/
    function shortcode($atts) {
    	$options = JWPlayer::get_options();
    	
    	$default_jw_player = $options['default_jw_player'];
    	$default_jw_player_width = $options['default_jw_player_width'];
		$default_jw_player_height = $options['default_jw_player_height'];
		$default_jw_player_image = $options['default_jw_player_image'];
		$default_jw_player_autoplay = $options['default_jw_player_autoplay'];
		$default_jw_player_embed = $options['default_jw_player_embed'];
		$default_jw_player_feed = $options['default_jw_player_feed'];
		$default_jw_player_rsstag = $options['default_jw_player_rsstag'];

    	extract(shortcode_atts(array(
    		'src'		=> $default_jw_player,
            'width'		=> $default_jw_player_width,
			'height'	=> $default_jw_player_height,
			'image'		=> $default_jw_player_image,
			'autoplay'	=> $default_jw_player_autoplay,
			'embed'		=> $default_jw_player_embed,
			'feed'		=> $default_jw_player_feed,
			'rsstag'	=> $default_jw_player_rsstag
        ), $atts));

        $output = JWPlayer::get_embed_code($src, $width, $height, $image, $autoplay, $embed, $feed, $rsstag);
        
        return $output;
    }
}


/**
 * Instantiate the plugin
 *
 * @global
 **/
$wpjp = new wpjp_JWPlayerAdmin;
?>