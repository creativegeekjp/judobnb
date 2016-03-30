<?php
/*
 * Plugin Name: Profile Editor
 * Version: 1.1
 * Plugin URI: http://www.cohhe.com/
 * Description: This is a handy plugin, with a simple interface, for managing user profile.
 * Author: Cohhe
 * Author URI: http://www.cohhe.com/
 * Requires at least: 4.0
 * Tested up to: 4.2.2
 *
 * Text Domain: profile-editor
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Cohhe
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-profile-editor.php' );
require_once( 'includes/class-profile-editor-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-profile-editor-admin-api.php' );
require_once( 'includes/lib/class-profile-editor-post-type.php' );
require_once( 'includes/lib/class-profile-editor-taxonomy.php' );

// Load widgets
require_once( 'includes/widgets/widget-frontend-login.php' );

// Create profile editor table if it's not created
function pe_add_wp_table() {
	global $wpdb;

	$profile_editor_fields = $wpdb->prefix . 'profile_editor_fields';
	$profile_editor_data = $wpdb->prefix . 'profile_editor_data';

	if ( $wpdb->get_var("SHOW TABLES LIKE '".$profile_editor_fields."'") != $profile_editor_fields ) {
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE ".$profile_editor_fields." (
					ID bigint(20) NOT NULL AUTO_INCREMENT,
					F_ORDER bigint(20) NOT NULL,
					NAME varchar(20) DEFAULT NULL,
					TYPE varchar(20) DEFAULT NULL,
					LABEL text,
					VALUE text,
					PLACEHOLDER text,
					RULES text,
					DESCRIPTION text,
					PRIMARY KEY (ID),
					KEY F_ORDER (F_ORDER),
					KEY NAME (NAME)
				) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

}
register_activation_hook( __FILE__, 'pe_add_wp_table' );

/**
 * Returns the main instance of Profile_editor to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Profile_editor
 */
function Profile_editor () {
	$instance = Profile_editor::instance( __FILE__, '1.1' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Profile_editor_Settings::instance( $instance );
	}

	return $instance;
}

function profile_editor_get_template( $template ) {
	
	// Get the template slug
	$template_slug = rtrim( $template, '.php' );
	$template = $template_slug . '.php';
 
	// Check if a custom template exists in the theme folder, if not, load the plugin template file
	if ( $theme_file = locate_template( array( 'profile-editor/' . $template ) ) ) {
		$file = $theme_file;
	} else {
		$file = plugin_dir_path( __FILE__ ) . 'templates/' . $template;
	}
 
	include_once( $file );
}

Profile_editor();