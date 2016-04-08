<?php
/*
   Plugin Name: default
   Plugin URI: http://wordpress.org/extend/plugins/default/
   Version: 0.1
   Author: Saturnino Adrales
   Description: Do all site specific codes here
   Text Domain: default
   License: GPLv3
  */

/*
    "WordPress Plugin Template" Copyright (C) 2016 Michael Simpson  (email : michael.d.simpson@gmail.com)

    This following part of this file is part of WordPress Plugin Template for WordPress.

    WordPress Plugin Template is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WordPress Plugin Template is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database Extension.
    If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/

$Default_minimalRequiredPhpVersion = '5.0';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function Default_noticePhpVersionWrong() {
    global $Default_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "default" requires a newer version of PHP to be running.',  'default').
            '<br/>' . __('Minimal version of PHP required: ', 'default') . '<strong>' . $Default_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'default') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}


function Default_PhpVersionCheck() {
    global $Default_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $Default_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'Default_noticePhpVersionWrong');
        return false;
    }
    return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function Default_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('default', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// Initialize i18n
add_action('plugins_loadedi','Default_i18n_init');

// Run the version check.
// If it is successful, continue with initialization for this plugin
if (Default_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('default_init.php');
    Default_init(__FILE__);
}


function my_page_template_redirect()
{
        
    if(isset($_GET['resource'])){

        wp_redirect('/book-now/');
        exit();
    }
}
add_action( 'template_redirect', 'my_page_template_redirect' );