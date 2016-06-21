<?php 

include("wp-blog-header.php");
$redirect_to = 'index.php';
$redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '';
$location = str_replace('&amp;', '&', wp_logout_url($redirect_to));;
header("Location: $location") ;
 
?>