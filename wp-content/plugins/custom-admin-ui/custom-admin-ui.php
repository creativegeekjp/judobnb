<?php
/*
Plugin Name: 	Custom Admin UI
Description: 	Customize the WordPress admin User Interface.
Author: 		Happy Brain S.r.l.
Author URI: 	http://www.happybrain.it
Version: 		1.0
*/

require_once( plugin_dir_path( __FILE__ ) . 'settings.php' );

$options = get_option( 'custom_admin_ui_settings' );

// Left admin footer text
if ($GLOBALS['options']['custom_admin_ui_text_field_0'] != '') {
	add_filter('admin_footer_text', 'left_admin_footer_text_output');
	function left_admin_footer_text_output($text) {
    	return $GLOBALS['options']['custom_admin_ui_text_field_0'];
	}
}

// Right admin footer text 
if ($GLOBALS['options']['custom_admin_ui_text_field_1'] != '') {
	add_filter('update_footer', 'right_admin_footer_text_output', 11);
	function right_admin_footer_text_output($text) {
	    return $GLOBALS['options']['custom_admin_ui_text_field_1'];
	}
}

// Change page title in admin area
if ($GLOBALS['options']['custom_admin_ui_text_field_8'] != '') {
	add_filter('admin_title', 'custom_admin_ui_admin_title', 10, 2);
	function custom_admin_ui_admin_title($admin_title, $title){
		return $title.' &lsaquo; '.get_bloginfo('name').' &#8212; '.$GLOBALS['options']['custom_admin_ui_text_field_8'];
	}
}

// Remove admin color scheme picker
if (isset($GLOBALS['options']['custom_admin_ui_checkbox_field_6'])) {
	remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
}

// Hide Help tab
add_action('admin_head', 'hide_help');
function hide_help() {
	if (isset($GLOBALS['options']['custom_admin_ui_checkbox_field_4'])) {
    	echo '<style type="text/css">#contextual-help-link-wrap { display: none !important; }</style>';
    }
}

// Remove screen options tab
add_filter('screen_options_show_screen', 'remove_screen_options_tab');
function remove_screen_options_tab() {
	if (!isset($GLOBALS['options']['custom_admin_ui_checkbox_field_5'])){
		return TRUE;
	}
}

// Remove the WordPress Logo from the Toolbar
add_action( 'admin_bar_menu', 'remove_wp_logo', 999 );
function remove_wp_logo( $wp_admin_bar ) {
	if (isset($GLOBALS['options']['custom_admin_ui_checkbox_field_7'])) {
		$wp_admin_bar->remove_node( 'wp-logo' );
	}
}

// Customize the admin theme style
if (isset($GLOBALS['options']['custom_admin_ui_checkbox_field_9'])) {
	add_action('admin_enqueue_scripts', 'custom_admin_ui_theme_style');
	function custom_admin_ui_theme_style() {
    	wp_enqueue_style('custom-admin-ui', plugins_url('wp-admin.css', __FILE__));
	}
}
