<?php 
define( 'BLOCK_LOAD', true );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php' );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/wp-db.php' );
$wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

$uid = get_current_user_id();
   
$lang = $wpdb->get_var("SELECT value FROM `jd_bp_xprofile_data` WHERE field_id='635' AND user_id = $uid");
 
if($lang=="Japanese"){
     
      $parts = "/ja/";
      
 }else{
     
      $parts = "";
      
 }

$redirect_to = 'index.php';
$redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '';
$location = str_replace('&amp;', '&', wp_logout_url($redirect_to."".$parts));
header("Location: $location") ;

?>