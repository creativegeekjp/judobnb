<?php 

include("wp-blog-header.php");
 
unset($_COOKIE['C_CURRENCY']);
setcookie('C_CURRENCY', '' , time()+3600 * 24 * 365);


if ( wp_get_referer() )
{
    wp_safe_redirect( wp_get_referer() );
}
else
{
    wp_safe_redirect( get_home_url() );
}


?>