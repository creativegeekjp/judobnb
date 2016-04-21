<?php 


$types = $_REQUEST['type_s'];

if($types=="cancel")
{
    $gd_session->un_set('listing');
    
    if (isset($_REQUEST['pid']) && $_REQUEST['pid'] != '' && get_permalink($_REQUEST['pid']))
        wp_redirect(get_permalink($_REQUEST['pid']));
    else 
    {
        geodir_remove_temp_images();
        wp_redirect(geodir_getlink(get_permalink(geodir_add_listing_page_id()), array('listing_type' => $_REQUEST['listing_type'])));
    }
}

?>