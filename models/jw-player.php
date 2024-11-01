<?php
// ======================================================================================
// This library is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public
// License as published by the Free Software Foundation; either
// version 2.1 of the License, or(at your option) any later version.
//
// This library is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
// Lesser General Public License for more details.

define('WP_JW_PLAYER_OPTION_NAME', 'wpjp_jw_player_options');

class JWPlayer {
	/**
	* Class declaration.
	*
	*/
	private function JWPlayer() {
		
	}
	
	/**
     * Get the default options.
     *
     * @return $default_options
     **/
	function get_default_options() {
		//$jw_player = JWPlayer::get_games();
		
		$default_options = array(
		//	'jw_player'					=> $jw_player,
			'default_jw_player'			=> WP_JW_PLAYER_DEFAULT_JW_PLAYER,
			'default_jw_player_width'	=> WP_JW_PLAYER_DEFAULT_WIDTH,
			'default_jw_player_height'	=> WP_JW_PLAYER_DEFAULT_HEIGHT,
			'default_jw_player_image'	=> WP_JW_PLAYER_DEFAULT_IMAGE,
			'default_jw_player_autoplay'=> WP_JW_PLAYER_DEFAULT_AUTOPLAY,
			'default_jw_player_embed'	=> WP_JW_PLAYER_DEFAULT_EMBED,
			'default_jw_player_rsstag'	=> WP_JW_PLAYER_DEFAULT_RSSTAG,
			'default_jw_player_feed'	=> WP_JW_PLAYER_DEFAULT_FEED
		);
        
        return $default_options;
	}
	
	/**
     * Get options.
     *
     * @return $options
     **/
	function get_options() {
		$options = get_option(WP_JW_PLAYER_OPTION_NAME);
        
        if ($options === false)
            $options = array();
        
        $default_options = JWPlayer::get_default_options();
        
        foreach ($default_options AS $key => $value) {
            if (!isset ($options[$key]))
                $options[$key] = $value;
        }
        
        return $options;
	}
	
	/**
     * Function to update options.
     *
     * @return void
     **/
    function update_options($options) {
        if (isset($_POST['update']) && check_admin_referer (WP_JW_PLAYER_ADMIN_REFERRER)) {
            $_POST = stripslashes_deep($_POST);
            
            $current_options = JWPlayer::get_options();
            
            foreach ($options AS $key => $value) {
	            $current_options[$key] = $value;
	        }
	        
            update_option(WP_JW_PLAYER_OPTION_NAME, $current_options);
            
            $this->render_message(__('Your options have been updated', WP_JW_PLAYER_TEXT_DOMAIN));
        }
    }
    
    /**
     * Function to get country code.
     *
     * @return $country_code
     **/
    function get_country_code() {
		$country_code = strtolower(substr(get_bloginfo('language'), stripos(get_bloginfo('language'), '-') + 1));
		
		return $country_code;
	}
	
	/**
	* Get the embed code.
	*
	* @return $jw_player_embed_code
	*/
	function get_embed_code($jw_player, $width, $height, $image, $autoplay, $embed, $feed, $rsstag) {
		global $post;
		$attribution_link = JWPlayer::get_attribution_link();
		$tmp = isset($post->ID) ? $post->ID : rand(1,9999);
		$id = md5($tmp.$jw_player);
		$jw_player_embed_code = "<div class=\"wpjp-embed-code\">
		<div id=\"wpjp-player-".$id."\"><a href=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=shockwaveFlash\" rel=\"nofollow\">Get The Latest Flash Player</a></div>
		<script type=\"text/javascript\">
		var so = new SWFObject(".WP_JW_PLAYER_DEFAULT_JW_PLAYER.",'wpjp_player','".$width."','".$height."','9');
		so.addParam('allowfullscreen','true');
		so.addParam('allowscriptaccess','always');
		so.addParam('wmode','transparent');
		so.addParam('flashvars','file=".$jw_player."&image=".$image."&playlist=none&autostart=".(bool)$autoplay."');
		so.write('wpjp-player-".$id."');
		</script>";
		
		$embed_code = '<object width="'.$width.'" height="'.$height.'"><param name="movie" value="'.$this->url().'/swf/player.swf"></param><param name="allowFullScreen" value="true"></param><param name="flashvars" value="file='.$jw_player.'&image='.$image.'&playlist=none&autostart=false"></param><embed src="'.$this->url().'/swf/player.swf" width="'.$width.'" height="'.$height.'" allowscriptaccess="always" allowfullscreen="true" flashvars="file='.$jw_player.'&image='.$image.'&playlist=none&autostart=false"></embed></object>';
		if(!empty($post->ID)) {
			$embed_code .= '<div class="wpjp-attribution-text"><p style="font-size:8px;text-align:center;"><a href="'.get_permalink($post->ID).'">'.get_the_title($post->ID).'</a></p></div>';
		}
		
		$jw_player_embed_code .= '<div class="wpjp-attribution-text">' . $attribution_link . '</div>';
		
		if(strtolower($embed) == 'true' || strtolower($embed) == '1') {
			$jw_player_embed_code .= '<div class="wpjp-share">Embed: <input type="text" size="'.floor($width/8).'" value="'.htmlentities($embed_code).'" onclick="this.focus();this.select();" readonly="true" /></div>';
		}
		
		if(!empty($rsstag) || !empty($feed)) {
			$jw_player_embed_code .= JWPlayer::get_rss_feed($feed,$rsstag);
		}
		
		$jw_player_embed_code .= '</div>';

		$jw_player_embed_code = str_replace('%%WP_JW_PLAYER_EMBED_CODE%%', htmlentities($jw_player_embed_code), $jw_player_embed_code);
		
		return $jw_player_embed_code;
	}

	/**
	* Get the RSS Feed.
	*
	* @return $rsstag
	*/
	function get_rss_feed($feedsrc,$rsstag) {
		$feed = "";
		$rss = fetch_feed( str_replace('%keyword',urlencode($rsstag), $feedsrc) );
		$items = $rss->get_items(0, 5);
		if(!empty($items)) {
			foreach($items as $item) {
				$link =  $item->get_link();
				$title = $item->get_title();
				$date = htmlentities(strip_tags($item->get_date()));
				$date = strtotime($date);
				$date = gmdate("d/m/y", $date);
				$feed .= '<p class="wpjp-feed-line">'.$date.': <a title="'.$title.'" href="'.$link.'">'.$title.'</a></p>';
			}
		}
		return $feed;
	}
	
	/**
	* Get the attribution link.
	*
	* @return $attribution_link
	*/
	function get_attribution_link() {
		$country_code = JWPlayer::get_country_code();
		
		$supported_country_codes = array(
			'ar' 	=> '&#216;&#185;&#216;&#177;&#216;&#168;&#217;',
			//'bg'	=> '&#1041;&#1098;&#1083;&#1075;&#1072;&#1088;&#1089;&#1082;&#1080;',
			'de'	=> 'Deutsch', 
			//'ca'	=> 'Catal&#224;', // ca is Canada
			//'cz'	=> '&#268;esky',
			//'dk'	=> 'Dansk', 
			'es'	=> 'Espa&#241;ol',
			'fr'	=> 'Fran&#231;ais', 
			//'gr'	=> '&#917;&#955;&#955;&#951;&#957;&#953;&#954;&#940;',
			//'hr'	=> 'Hrvatski',
			//'hu'	=> 'Magyar',
			'it'	=> 'Italiano', 
			//'nl'	=> 'Nederlands',
			//'no'	=> 'Norsk', 
			//'pl'	=> 'Polski', 
			'pt'	=> 'Portugu&#234;s',
			//'ro'	=> 'Rom&#226;n&#259;',
			'ru'	=> '&#1056;&#1091;&#1089;&#1089;&#1082;&#1080;&#1081;',
			'se'	=> 'Svenska',
			//'sk'	=> 'Slovensky',
			//'sl'	=> 'Slovenski',
			//'tr'	=> 'T&#252;rk&#231;e'
		);
		
		$wpjp_base_url = 'http://www.tubepress.net/';
		$plugin_base_url = 'http://www.tubepress.net/wp-jw-player';
		
		$country_code_keys = array_keys($supported_country_codes);
		
		if(in_array($country_code, $country_code_keys)) {
			$wpjp_url = $wpjp_base_url . $country_code;
			$plugin_url = $plugin_base_url;
		} else {
			$wpjp_url = $wpjp_base_url;
			$plugin_url = $plugin_base_url;
		}
		
		$attribution_link = sprintf(__('<p style="font-size:8px;text-align:center;"><a href="%s" target="_blank">WP JW Player Plugin</a> Powered by <a href="%s" target="_blank">TubePress.NET</a></p>', WP_JW_PLAYER_TEXT_DOMAIN), $plugin_url, $wpjp_url);
		
		return $attribution_link;
	}
	
	/**
	* Get the singleton object.
	*
	*/
	function &get () {
	    static $instance;
		
	    if (!isset ($instance)) {
			$c = __CLASS__;
			$instance = new $c;
	    }
		
	    return $instance;
	}
}

// Cause the singleton to fire
JWPlayer::get();
?>