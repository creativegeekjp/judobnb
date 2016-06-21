<?php
/*
 * Donations metabox
 */

$config = array(
	'id'       => 'vh_donations',
	'title'    => __('Donations', 'vh'),
	'pages'    => array('donations'),
	'context'  => 'side',
	'priority' => 'high',
);

$options = array(array(
	'name' => __('How many donations are needed?', 'vh'),
	'id'   => '_how_many_donations_are_needed',
	'type' => 'donations',
	'only' => 'donations',
),array(
	'name' => __('Donation so far', 'vh'),
	'id'   => '_donations_so_far',
	'type' => 'donations',
	'only' => 'donations',
),array(
	'name' => __('Fundrisers', 'vh'),
	'id'   => '_fundraisers',
	'type' => 'donations',
	'only' => 'donations',
),array(
	'name' => __('PayPal email', 'vh'),
	'id'   => '_custom_paypal_email',
	'type' => 'donations',
	'only' => 'donations',
));

require_once(VH_METABOXES . '/add_metaboxes.php');
new create_meta_boxes($config, $options);