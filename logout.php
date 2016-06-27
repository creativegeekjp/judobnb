<?php 

include("wp-blog-header.php");
$uriParts = explode('/',$_COOKIE['langss']);

if( $uriParts[3]== 'ja' )
    $parts = "/ja/";
else
    $parts = "";
	
$redirect_to = 'index.php';
$redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '';
$location = str_replace('&amp;', '&', wp_logout_url($redirect_to."".$parts));
header("Location: $location") ;

?>