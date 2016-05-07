<?php 

include("wp-blog-header.php");

function user_has_role( $role, $user_id = null ) {
    if ( is_numeric( $user_id ) ) {
        $user = get_userdata( $user_id );
    }
    else {
        $user = wp_get_current_user();
    }

    if ( ! empty( $user ) ) {
        return in_array( $role, (array) $user->roles );
    }
}

$user = wp_get_current_user();

if(isset($user->data->ID)){
  if(!in_array('guest',$user->roles)){
      $user->roles[] = 'guest';
      echo 'User role added';
  }
}

$user->add_role('guest');

if ( wp_get_referer() )
{
    wp_safe_redirect( wp_get_referer() );
}
else
{
    wp_safe_redirect( get_home_url() );
}