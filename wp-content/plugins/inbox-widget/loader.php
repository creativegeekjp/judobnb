<?php
/*
Plugin Name: Inbox Widget
Plugin URI: http://buddypress.org
Description: Adds a widget showing three most recent private messages to logged-in users on a site powered by BuddyPress.
Version: 1.5.01
Requires at least: WordPress 3.2.1 / BuddyPress 1.5
Tested up to: WordPress 3.2.1 / BuddyPress 1.5
License: GNU/GPL 2
Author: David Carson
Author URI: http://davidtcarson.com
*/
 
/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */
function inbox_widget_plugin_init() {
    require( dirname( __FILE__ ) . '/bp-inbox-widget.php' );
}
add_action( 'bp_include', 'inbox_widget_plugin_init' );
 
/* If you have code that does not need BuddyPress to run, then add it here. */


