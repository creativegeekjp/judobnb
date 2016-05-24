<?php 
include("wp-blog-header.php");
 
 
 if( $_COOKIE['C_CURRENCY'] == 'JPY'  )
 {
     unset($_COOKIE['C_CURRENCY']);
 }
 setcookie('C_CURRENCY', 'USD' , time()+3600 * 24 * 365);

if ( wp_get_referer() )
{
    wp_safe_redirect( wp_get_referer() );
}
else
{
    wp_safe_redirect( get_home_url() );
}


?>