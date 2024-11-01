<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>

<div class="wrap">
	<?php screen_icon(); ?>
	
  <h2><?php printf (__ ('%s | General Options', WP_JW_PLAYER_TEXT_DOMAIN), WP_JW_PLAYER_OPTIONS); ?></h2>	
	<form method="post" action="<?php echo $this->url ($_SERVER['REQUEST_URI']); ?>">
	<?php wp_nonce_field (WP_JW_PLAYER_ADMIN_REFERRER); ?>

	<table border="0" cellspacing="5" cellpadding="5" class="form-table">
		<tr>
			<th valign="top" align="right"><label for="default_jw_player_width"><?php _e ('Default Width', WP_JW_PLAYER_TEXT_DOMAIN) ?></label>
			</th>
			<td valign="top">
				<input type="text" id="default_jw_player_width" name="default_jw_player_width" value="<?php echo($options['default_jw_player_width']); ?>" />
			</td>
		</tr>
		<tr>
			<th valign="top" align="right"><label for="default_jw_player_height"><?php _e ('Default Height', WP_JW_PLAYER_TEXT_DOMAIN) ?></label>
			</th>
			<td valign="top">
				<input type="text" id="default_jw_player_height" name="default_jw_player_height" value="<?php echo($options['default_jw_player_height']); ?>" />
			</td>
		</tr>
		<tr>
			<th valign="top" align="right"><label for="default_jw_player_image"><?php _e ('Default Image', WP_JW_PLAYER_TEXT_DOMAIN) ?></label>
			</th>
			<td valign="top">
				<input type="text" size="60" id="default_jw_player_image" name="default_jw_player_image" value="<?php echo($options['default_jw_player_image']); ?>" />
			</td>
		</tr>
		<tr>
			<th valign="top" align="right"><label for="default_jw_player_autoplay"><?php _e ('Default Allow Autoplay', WP_JW_PLAYER_TEXT_DOMAIN) ?></label>
			</th>
			<td valign="top">
				<select id="default_jw_player_autoplay" name="default_jw_player_autoplay">
					<option value="0" <?php if($options['default_jw_player_autoplay'] == 0) echo 'selected'?>>False</option>
					<option value="1" <?php if($options['default_jw_player_autoplay'] == 1) echo 'selected'?>>True</option>
				</select>
			</td>
		</tr>
		<tr>
			<th valign="top" align="right"><label for="default_jw_player_embed"><?php _e ('Default Allow Embed', WP_JW_PLAYER_TEXT_DOMAIN) ?></label>
			</th>
			<td valign="top">
				<select id="default_jw_player_embed" name="default_jw_player_embed">
					<option value="0" <?php if($options['default_jw_player_embed'] == 0) echo 'selected'?>>False</option>
					<option value="1" <?php if($options['default_jw_player_embed'] == 1) echo 'selected'?>>True</option>
				</select>
			</td>
		</tr>
		<tr>
			<th valign="top" align="right"><label for="default_jw_player_feed"><?php _e ('Default Feed', WP_JW_PLAYER_TEXT_DOMAIN) ?></label>
			</th>
			<td valign="top">
				<input type="text" size="60" id="default_jw_player_feed" name="default_jw_player_feed" value="<?php echo($options['default_jw_player_feed']); ?>" />
			</td>
		</tr>
		<tr>
			<th valign="top" align="right"><label for="default_jw_player_rsstag"><?php _e ('Default RSS Tag', WP_JW_PLAYER_TEXT_DOMAIN) ?></label>
			</th>
			<td valign="top">
				<input type="text" id="default_jw_player_rsstag" name="default_jw_player_rsstag" value="<?php echo($options['default_jw_player_rsstag']); ?>" />
			</td>
		</tr>
		<tr>
			<th/>
			<td>
				<input class="button-primary" type="submit" name="update" value="<?php echo __('Update Options &raquo;', WP_JW_PLAYER_TEXT_DOMAIN)?>" />
			</td>
		</tr>
	</table>
</form>
<h2>WP JW Player Documentation</h2>
<table border="0" cellspacing="10" cellpadding="10">
<tr>
	<th valign="top" align="right"><strong>Usage:</strong></th>
	<td valign="top">
		[wp-jw-player src="http://www.example.com/video.flv" image="http://www.example.com/preview.jpg"]<br />
		[wp-jw-player src="http://www.youtube.com/watch?v=WsGmZO4N0g0" width="400" height="350" image="http://i.ytimg.com/vi/WsGmZO4N0g0/default.jpg" autoplay="false" embed="true" rsstag="JW Player"]<br/>
	</td>
</tr>
</table>
<table border="0" cellspacing="5" cellpadding="5" class="form-table">
<tr><th valign="top" align="right"><strong>src</strong></th><td valign="top"><?php _e('URL of the video file') ?></td></tr>
<tr><th valign="top" align="right"><strong>width</strong></th><td valign="top"><?php _e('Width of the player') ?></td></tr>
<tr><th valign="top" align="right"><strong>height</strong></th><td valign="top"><?php _e('Height of the player') ?></td></tr>
<tr><th valign="top" align="right"><strong>image</strong></th><td valign="top"><?php _e('Preview Image') ?></td></tr>
<tr><th valign="top" align="right"><strong>autoplay</strong></th><td valign="top"><?php _e('Autostart the video (not recommended if you have multiple videos on the same page)') ?></td></tr>
<tr><th valign="top" align="right"><strong>embed</strong></th><td valign="top"><?php _e('Place embed html code under player') ?></td></tr>
<tr><th valign="top" align="right"><strong>feed</strong></th><td valign="top"><?php _e('Overwrites the default news feed source') ?></td></tr>
<tr><th valign="top" align="right"><strong>rsstag</strong></th><td valign="top"><?php _e('Keyword to replace %keyword in the default news feed source') ?></td></tr>
</table>
<br />
<a href="http://www.tubepress.net/wp-jw-player" title="WP JW Player">Need help using WP JW Player ?</a>
</div>