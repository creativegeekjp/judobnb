<?php 
include("wp-blog-header.php");
$redirect_to = '

    index.php

';

if ( isset( $_REQUEST['redirect_to'] ) )
$redirect_to = $_REQUEST['redirect_to'];

?>