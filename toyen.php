<?php 
 setcookie('C_CURRENCY', 'JPY' , time()+3600 * 24 * 365); // 1 year
 
 if( $_COOKIE['C_CURRENCY'] == 'USD')
     unset($_COOKIE['C_CURRENCY']);
 else
     setcookie('C_CURRENCY', 'JPY' , time()+3600 * 24 * 365); // 1 year
     
include("wp-blog-header.php");
//redirect to current page
if ( wp_get_referer() )
{
    wp_safe_redirect( wp_get_referer() );
}
else
{
    wp_safe_redirect( get_home_url() );
}


?>