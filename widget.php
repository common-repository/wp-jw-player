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

class wpjp_JWPlayerWidget extends WP_Widget {
	/**
	* Declares the widget class.
	*
	*/
	function wpjp_JWPlayerWidget() {
		$widget_ops = array('classname' => 'wpjp_JWPlayerWidget', 'description' => __('Display WP JW Player in your sidebar.', WP_JW_PLAYER_TEXT_DOMAIN));
	    $control_ops = array('width' => 300, 'height' => 300);
	    $this->WP_Widget('wpjp_JWPlayerWidget', __('WP Mini Game', WP_JW_PLAYER_TEXT_DOMAIN), $widget_ops, $control_ops);
	}
	
	/**
	* Displays the Widget
	*
	*/
	function widget($args, $instance) {
		extract($args);
		
		$title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title']);
		$jw_player = empty($instance['videoPlayer']) ? WP_JW_PLAYER_DEFAULT_JW_PLAYER : $instance['videoPlayer'];
		$jw_player_width = empty($instance['videoPlayerWidth']) ? WP_JW_PLAYER_DEFAULT_WIDTH : (int)$instance['videoPlayerWidth'];
		$jw_player_height = empty($instance['videoPlayerHeight']) ? WP_JW_PLAYER_DEFAULT_HEIGHT : (int)$instance['videoPlayerHeight'];
		
		echo($before_widget);
		if ( $title )
			echo($before_title . $title . $after_title);
		
		echo(JWPlayer::get_embed_code($jw_player, $jw_player_width, $jw_player_height));
		echo($after_widget);
	}
	
	/**
	* Saves the widgets settings.
	*
	*/
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['videoPlayer'] = strip_tags(stripslashes($new_instance['videoPlayer']));
		$instance['videoPlayerWidth'] = strip_tags(stripslashes($new_instance['videoPlayerWidth']));
		$instance['videoPlayerHeight'] = strip_tags(stripslashes($new_instance['videoPlayerHeight']));
		
		return $instance;
	}
	
	/**
	* Creates the edit form for the widget.
	*
	*/
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array('title'=>__('WP Mini Game', WP_JW_PLAYER_TEXT_DOMAIN), 'videoPlayer'=>'texas-holdem-poker') );
		
		$title = htmlspecialchars($instance['title']);
		
		$videoPlayers = JWPlayer::get_games();
		
		$videoPlayerWidth = htmlspecialchars(empty($instance['videoPlayerWidth']) ? (string)WP_JW_PLAYER_DEFAULT_WIDTH : $instance['videoPlayerWidth']);
		$videoPlayerHeight = htmlspecialchars(empty($instance['videoPlayerHeight']) ? (string)WP_JW_PLAYER_DEFAULT_HEIGHT : $instance['videoPlayerHeight']);
		
		echo('<p><label for="' . $this->get_field_name('title') . '">' . __('Title', WP_JW_PLAYER_TEXT_DOMAIN) . ':<br /><input style="width: 250px;" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></label></p>');
		
		$currentMiniGame = htmlspecialchars($instance['videoPlayer']);
		
		echo('<p><label for="' . $this->get_field_name('videoPlayer') . '">' . __('Mini Game', WP_JW_PLAYER_TEXT_DOMAIN) . ':<br />
		<select name="' . $this->get_field_name('videoPlayer') . '" id="' . $this->get_field_id('videoPlayer') . '">');
		
		foreach($videoPlayers as $key => $videoPlayer) {
			if($currentMiniGame == $key)
				$selected = ' selected';
			else
				$selected = '';
			
			echo('<option value="' . $key . '"' . $selected . '>' . $videoPlayer . '</option>');
		}
		
		echo('</select></label></p>');
		
		echo '<p><label for="' . $this->get_field_name('videoPlayerWidth') . '">' . __('Mini Game Width', WP_JW_PLAYER_TEXT_DOMAIN) . ':<br /><input style="width: 100px;" id="' . $this->get_field_id('videoPlayerWidth') . '" name="' . $this->get_field_name('videoPlayerWidth') . '" type="text" value="' . $videoPlayerWidth . '" /></label></p>';
		echo '<p><label for="' . $this->get_field_name('videoPlayerHeight') . '">' . __('Mini Game Height', WP_JW_PLAYER_TEXT_DOMAIN) . ':<br /><input style="width: 100px;" id="' . $this->get_field_id('videoPlayerHeight') . '" name="' . $this->get_field_name('videoPlayerHeight') . '" type="text" value="' . $videoPlayerHeight . '" /></label></p>';
	}
}

function wpjpJWPlayerWidgetInit() {
	register_widget('wpjp_JWPlayerWidget');
}

add_action('widgets_init', 'wpjpJWPlayerWidgetInit');
?>