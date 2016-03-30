<?php
/**
 * Plugin Name: Sky Functionality
 * Description: This contains all your site's core functionality so that it is theme independent.
 * Version: 2.9.1
 * Author: Cohhe
 * 
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */

// Plugin Directory 
define( 'BE_DIR', dirname( __FILE__ ) );

define('VH_SHORTCODES', get_template_directory() . '/functions/admin/visual-composer');

// Scripts and Styles
include_once( BE_DIR . '/lib/scripts_and_styles.php' );

// Post Types
// include_once( BE_DIR . '/lib/functions/post-types.php' );

// Taxonomies 
//include_once( BE_DIR . '/lib/functions/taxonomies.php' );

// Metaboxes
// include_once( BE_DIR . '/lib/functions/metaboxes.php' );
 
// Shortcodes
include_once( BE_DIR . '/lib/functions/shortcodes.php' );

// Widgets
//include_once( BE_DIR . '/lib/widgets/widget-social.php' );
include_once( BE_DIR . '/lib/widgets/widget-contact-us.php' );
include_once( BE_DIR . '/lib/widgets/widget-useful-links.php' );
include_once( BE_DIR . '/lib/widgets/widget-similar-listings.php' );
include_once( BE_DIR . '/lib/widgets/widget-other-listings.php' );
include_once( BE_DIR . '/lib/widgets/widget-recent-activity.php' );

// Twitter widgets
include_once( BE_DIR . '/lib/widgets/twitter/twitter.php' );

// Editor Style Refresh
include_once( BE_DIR . '/lib/functions/editor-style-refresh.php' );

// General
include_once( BE_DIR . '/lib/functions/general.php' );

// Geodirectory
include_once( BE_DIR . '/lib/functions/geodirectory.php' );

function vh_localize() {
	load_plugin_textdomain( 'vh', false, dirname( plugin_basename( __FILE__ ) ).'/languages' );
}
add_action( 'plugins_loaded', 'vh_localize' );

function vh_countries_theme_menu() {
add_theme_page( 'Countries', 'Countries', 'manage_options', 'vh_countries_options', 'vh_countries_theme_page');
}
add_action('admin_menu', 'vh_countries_theme_menu');

function vh_countries_theme_page() {
?>
	<div class="section panel" style="width:60%">
		<h1>Custom Theme Options</h1>
		<form method="post" enctype="multipart/form-data" action="options.php">
			<?php 
			settings_fields('vh_countries_options'); 
		
			do_settings_sections('vh_countries_options.php');
			?>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
			
		</form>
	</div>
	<?php
}
add_action( 'admin_init', 'pu_register_settings' );

function pu_register_settings() {
	// Register the settings with Validation callback
	register_setting( 'vh_countries_options', 'vh_countries_options' );

	// Add settings section
	add_settings_section( 'vh_text_section', 'Countries', 'vh_display_section', 'vh_countries_options.php' );

	// Create textbox field
	$field_args = array(
	'type'   => 'text',
	'id'        => 'pu_textbox',
	'name'      => 'pu_textbox',
	'desc'      => 'Country name with the picture ID from media library. Example - Spain:11',
	'std'       => '',
	'label_for' => 'pu_textbox',
	'class'     => 'css_class'
	);

	add_settings_field( 'example_textbox', 'Country', 'vh_display_setting', 'vh_countries_options.php', 'vh_text_section', $field_args );
}

function vh_display_section($section){ 

}

function vh_display_setting($args)
{
	extract( $args );

	$option_name = 'vh_countries_options';

	$options = get_option( $option_name );

	switch ( $type ) {  
		  case 'text':  
			$options[$id] = stripslashes($options[$id]);
			$options[$id] = esc_attr( $options[$id]);
			echo "<input type='text' id='countries_input'/>";
			echo "<input type='button' id='country_data_set' value='Set country data'>";
			echo "<input class='regular-text$class' type='hidden' id='$id' name='" . $option_name . "[$id]' value='$options[$id]' />";
			echo ($desc != '') ? "<br /><span class='description'>$desc</span><br />" : "";
			wp_enqueue_script('something', get_template_directory_uri() . '/js/country-admin.js', array('jquery'), '', TRUE);
			echo "<ul id='myTags'>";
			$info = trim($options[$id],',');
			$info = str_replace('&quot;','"',$info);
			$info_arr = json_decode('['.$info.']',true);

			foreach ($info_arr as $value) {
				echo '<li>'.$value['country'].'</li>';
			}
			echo "</ul>";
		break;
	}
}