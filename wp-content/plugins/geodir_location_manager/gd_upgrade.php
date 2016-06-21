<?php
/**
 * Contains functions related to Location Manager plugin upgrade.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
global $wpdb;

if(get_option('geodirlocation'.'_db_version') != GEODIRLOCATION_VERSION){
	//ini_set("display_errors", "1");error_reporting(E_ALL); // for error checking
	add_action( 'plugins_loaded', 'geolocation_upgrade_all' );
	update_option( 'geodirlocation'.'_db_version',  GEODIRLOCATION_VERSION );
}

/**
 * Handles upgrade for all location manager versions.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geolocation_upgrade_all(){
	geodir_location_activation_script();
	geolocation_upgrade_1_3_2();
}

/**
 * Handles upgrade for location manager versions <= 1.3.2.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function geolocation_upgrade_1_3_2(){
    global $wpdb;

    /*
     * We clear the term meta so it's rebuilt as there was a bug.
     */
    $wpdb->query('TRUNCATE TABLE '.GEODIR_TERM_META);
}
