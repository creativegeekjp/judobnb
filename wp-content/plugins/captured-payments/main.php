<?php
/*
Plugin Name: Captured Payments
Plugin URI: Plugins Url
Description: Approved paypal payments 
Version: 1.0
Author: Jino Lacson
Author URI: Author's Website
License:GPL2
*/

add_action( 'admin_menu', 'my_admin_menu' );


function my_admin_menu()
{
   
    add_menu_page( 'Captured', 'Captured Payments', 'manage_options', 'pages/captured.php', 'intro',   plugin_dir_url( __FILE__ ).'images/logo.png' );
    add_submenu_page( 'pages/captured.php', 'Manage Lists',  'Manage Lists', 'manage_options', 'list', 'lists' );
    add_submenu_page( 'pages/captured.php', 'Manage Lists',  'Manage Lists', 'manage_options', 'list', 'lists' );

}

function lists()
{
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
    
    $page = isset($_GET['page']) ? $_GET['page'] : 0;
    
    switch ($page) {
        case 'capture':
             include 'captured.php';
            break;
        
        default:
           
            break;
    }
}
    
   // echo  $page = isset($_GET['page']) ? $_GET['page'] : 0;
    
?>