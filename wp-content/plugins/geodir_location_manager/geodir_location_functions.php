<?php
/**
 * Contains functions related to Location Manager plugin.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */

/**
 * Get location by location ID.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param array $location_result Location table query results.
 * @param string $id Location ID.
 * @return array|mixed
 */
function geodir_get_location_by_id($location_result = array() , $id='')
{
	global $wpdb;
	if($id)
	{
		$get_result = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM ".POST_LOCATION_TABLE." WHERE location_id = %d",
				array($id)
			)
		);
		if(!empty($get_result))
			$location_result = $get_result;

		}
		return $location_result;
}


/**
 * Get location array using arguments.
 *
 * @since 1.0.0
 * @since 1.4.1 Modified to apply country/city & region/city url rules.
 * @since 1.4.4 Updated for the neighbourhood system improvement.
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param null|array $args {
 *    Attributes of args.
 *
 *    @type string $what What do you want to query. Possible values: city, region, country. Default: 'city'.
 *    @type string $city_val City value.
 *    @type string $region_val Region value.
 *    @type string $country_val Country value.
 *    @type string $country_non_restricted Non restricted countries.
 *    @type string $region_non_restricted Non restricted regions.
 *    @type string $city_non_restricted Non restricted cities.
 *    @type bool $filter_by_non_restricted Filter by non restricted?.
 *    @type string $compare_operator Comparison operator.
 *    @type string $country_column_name Country column name.
 *    @type string $region_column_name Region column name.
 *    @type string $city_column_name City column name.
 *    @type bool $location_link_part Location link part.
 *    @type string $order_by Order by value.
 *    @type string $no_of_records No of records to return.
 *    @type string $spage Current page number.
 *    @type array $format {
 *        Attributes of format.
 *
 *        @type string $type Type. Default: 'list'.
 *        @type string $container_wrapper Container wrapper. Default: 'ul'.
 *        @type string $container_wrapper_attr Container wrapper attr.
 *        @type string $item_wrapper Item wrapper. Default: 'li'.
 *        @type string $item_wrapper_attr Item wrapper attr.
 *
 *    }
 *
 * }
 * @param bool $switcher Todo: describe this part.
 * @return array|mixed|string
 */
function geodir_get_location_array( $args = null, $switcher = false ) {
	global $wpdb;
	//escape values
	if(isset($args['city_va'])){$args['city_va'] = esc_sql($args['city_va']);}
	if(isset($args['region_val'])){$args['region_val'] = esc_sql($args['region_val']);}
	if(isset($args['country_val'])){$args['country_val'] = esc_sql($args['country_val']);}

	$permalink_structure = get_option('permalink_structure');
	$hide_country_part = get_option('geodir_location_hide_country_part');
	$hide_region_part = get_option('geodir_location_hide_region_part');
	$neighbourhood_active = get_option('location_neighbourhoods');
	$defaults = array(
					'what' => 'city',
					'city_val' => '',
					'region_val' => '',
					'country_val' => '',
					'country_non_restricted' => '',
					'region_non_restricted' => '',
					'city_non_restricted' => '',
					'filter_by_non_restricted' => true,
					'compare_operator' => 'like',
					'country_column_name' => 'country',
					'region_column_name' => 'region',
					'city_column_name' => 'city',
					'location_link_part' => true,
					'order_by' => 'asc',
					'no_of_records' => '',
					'spage' => '',
					'format' => array(
									'type' => 'list',
									'container_wrapper' => 'ul',
									'container_wrapper_attr' => '',
									'item_wrapper' => 'li',
									'item_wrapper_attr' => ''
								)
				);
	if ($neighbourhood_active) {
		$defaults['neighbourhood_val'] = '';
		$defaults['neighbourhood_column_name'] = 'hood_name';
	}

	$location_args = wp_parse_args( $args, $defaults );
	if (!$neighbourhood_active) {
		if (isset($defaults['neighbourhood_val'])) {
			unset($defaults['neighbourhood_val']);
		}
		
		if ($location_args['what'] == 'neighbourhood') {
			$location_args['what'] = 'city';
		}
	}

	$search_query = '';
	$location_link_column = '';
	$location_default = geodir_get_default_location();

	if( $location_args['filter_by_non_restricted'] ) {
		// Non restricted countries
		if( $location_args['country_non_restricted'] == '' ) {
			if( get_option( 'geodir_enable_country' ) == 'default' ) {
				$country_non_retsricted = isset( $location_default->country ) ? $location_default->country : '';
				$location_args['country_non_restricted']  = $country_non_retsricted;
			} else if( get_option( 'geodir_enable_country' ) == 'selected' ) {
				$country_non_retsricted = get_option( 'geodir_selected_countries' );

				if( !empty( $country_non_retsricted ) && is_array( $country_non_retsricted ) ) {
					$country_non_retsricted = implode(',' , $country_non_retsricted );
				}

				$location_args['country_non_restricted'] = $country_non_retsricted;
			}

			$location_args['country_non_restricted'] = geodir_parse_location_list( $location_args['country_non_restricted'] );
		}

		//Non restricted Regions
		if( $location_args['region_non_restricted'] == '' ) {
			if( get_option( 'geodir_enable_region' ) == 'default' ) {
				$regoin_non_restricted= isset( $location_default->region ) ? $location_default->region : '';
				$location_args['region_non_restricted']  = $regoin_non_restricted;
			} else if( get_option( 'geodir_enable_region' ) == 'selected' ) {
				$regoin_non_restricted = get_option( 'geodir_selected_regions' );
				if( !empty( $regoin_non_restricted ) && is_array( $regoin_non_restricted ) ) {
					$regoin_non_restricted = implode( ',', $regoin_non_restricted );
				}

				$location_args['region_non_restricted']  = $regoin_non_restricted;
			}

			$location_args['region_non_restricted'] = geodir_parse_location_list( $location_args['region_non_restricted'] );
		}

		//Non restricted cities
		if( $location_args['city_non_restricted'] == '' ) {
			if( get_option('geodir_enable_city') == 'default' ) {
				$city_non_retsricted = isset( $location_default->city ) ? $location_default->city : '';
				$location_args['city_non_restricted']  = $city_non_retsricted;
			} else if( get_option( 'geodir_enable_city' ) == 'selected' ) {
				$city_non_restricted = get_option( 'geodir_selected_cities' );

				if( !empty( $city_non_restricted ) && is_array( $city_non_restricted ) ) {
					$city_non_restricted = implode( ',', $city_non_restricted );
				}

				$location_args['city_non_restricted']  = $city_non_restricted;
			}
			$location_args['city_non_restricted'] = geodir_parse_location_list( $location_args['city_non_restricted'] );
		}
	}

	if ( $location_args['what'] == '') {
		$location_args['what'] = 'city';
	}

	if( $location_args['location_link_part'] ) {
		switch( $location_args['what'] ) {
			case 'country':
				if ($permalink_structure != '') {
					$location_link_column = ", CONCAT_WS('/', country_slug) AS location_link ";
				} else {
					$location_link_column = ", CONCAT_WS('&gd_country=', '', country_slug) AS location_link ";
				}
				break;
			case 'region':
				if ($permalink_structure != '') {
					if (!$hide_country_part) {
						$location_link_column = ", CONCAT_WS('/', country_slug, region_slug) AS location_link ";
					} else {
						$location_link_column = ", CONCAT_WS('/', region_slug) AS location_link ";
					}
				} else {
					if (!$hide_country_part) {
						$location_link_column = ", CONCAT_WS('&', CONCAT('&gd_country=', country_slug), CONCAT('gd_region=', region_slug) ) AS location_link ";
					} else {
						$location_link_column = ", CONCAT_WS('&gd_region=', '', region_slug) AS location_link ";
					}
				}
				break;
			case 'city':
			case 'neighbourhood':
				$concat_ws = array();
				
				if ($permalink_structure != '') {
					if (!$hide_country_part) {
						$concat_ws[] = 'country_slug';
					}
					
					if (!$hide_region_part) {
						$concat_ws[] = 'region_slug';
					}
					
					$concat_ws[] = 'city_slug';
					
					if ($location_args['what'] == 'neighbourhood') {
						$concat_ws[] = 'hood_slug';
					}
					
					$concat_ws = implode(', ', $concat_ws);
					
					$location_link_column = ", CONCAT_WS('/', " . $concat_ws . ") AS location_link ";
				} else {
					$amp = '&';
					if (!$hide_country_part) {
						$concat_ws[] = "CONCAT('" . $amp . "gd_country=', country_slug)";
						$amp = '';
					}
					
					if (!$hide_region_part) {
						$concat_ws[] = "CONCAT('" . $amp . "gd_region=', region_slug)";
						$amp = '';
					}
					
					$concat_ws[] = "CONCAT('" . $amp . "gd_city=', city_slug)";
					
					if ($location_args['what'] == 'neighbourhood') {
						$amp = '';
						$concat_ws[] = "CONCAT('" . $amp . "gd_neighbourhood=', hood_slug)";
					}
					
					$concat_ws = implode(', ', $concat_ws);
					
					$location_link_column = ", CONCAT_WS('&', " . $concat_ws . ") AS location_link ";
				}
				
				break;
		}
	}

	switch( $location_args['compare_operator'] ) {
		case 'like' :
			if (isset( $location_args['country_val'] ) && $location_args['country_val'] != '') {
				$countries_search_sql = geodir_countries_search_sql( $location_args['country_val'] );
				$countries_search_sql = $countries_search_sql != '' ? " OR FIND_IN_SET(country, '" . $countries_search_sql . "')" : '';
				$translated_country_val = sanitize_title( trim( wp_unslash( $location_args['country_val'] ) ) );
				$search_query .= " AND (LOWER(" . $location_args['country_column_name'] . ") LIKE \"%" . geodir_strtolower($location_args['country_val']) . "%\" OR  LOWER(country_slug) LIKE \"" . $translated_country_val . "%\" " . $countries_search_sql . ") ";
			}

			if (isset($location_args['region_val']) &&  $location_args['region_val'] != '') {
				$search_query .= " AND LOWER(".$location_args['region_column_name'] . ") LIKE \"%" . geodir_strtolower($location_args['region_val']) . "%\" ";
			}

			if (isset($location_args['city_val']) && $location_args['city_val'] != '') {
				$search_query .= " AND LOWER(" . $location_args['city_column_name'] . ") LIKE \"%" . geodir_strtolower($location_args['city_val']) . "%\" ";
			}
			
			if (isset($location_args['neighbourhood_val']) && $location_args['neighbourhood_val'] != '') {
				$search_query .= " AND LOWER(" . $location_args['neighbourhood_column_name'] . ") LIKE \"%" . geodir_strtolower($location_args['neighbourhood_val']) . "%\" ";
			}
			break;
		case 'in' :
			if (isset($location_args['country_val'])  && $location_args['country_val'] != '') {
				$location_args['country_val'] = geodir_parse_location_list($location_args['country_val']) ;
				$search_query .= " AND LOWER(" . $location_args['country_column_name'] . ") IN($location_args[country_val]) ";
			}

			if (isset($location_args['region_val']) && $location_args['region_val'] != '') {
				$location_args['region_val'] = geodir_parse_location_list($location_args['region_val']) ;
				$search_query .= " AND LOWER(" . $location_args['region_column_name'] . ") IN($location_args[region_val]) ";
			}

			if (isset($location_args['city_val']) && $location_args['city_val'] != '') {
				$location_args['city_val'] = geodir_parse_location_list($location_args['city_val']) ;
				$search_query .= " AND LOWER(" . $location_args['city_column_name'] . ") IN($location_args[city_val]) ";
			}
			
			if (isset($location_args['neighbourhood_val']) && $location_args['neighbourhood_val'] != '') {
				$location_args['neighbourhood_val'] = geodir_parse_location_list($location_args['neighbourhood_val']) ;
				$search_query .= " AND LOWER(" . $location_args['neighbourhood_column_name'] . ") IN($location_args[neighbourhood_val]) ";
			}

			break;
		default :
			if (isset($location_args['country_val']) && $location_args['country_val'] !='') {
				$countries_search_sql = geodir_countries_search_sql( $location_args['country_val'] );
				$countries_search_sql = $countries_search_sql != '' ? " OR FIND_IN_SET(country, '" . $countries_search_sql . "')" : '';
				$translated_country_val = sanitize_title( trim( wp_unslash( $location_args['country_val'] ) ) );
				$search_query .= " AND ( LOWER(" . $location_args['country_column_name'] . ") = '" . geodir_strtolower($location_args['country_val']) . "' OR LOWER(country_slug) LIKE \"" . $translated_country_val . "%\" " . $countries_search_sql . " ) ";
			}

			if (isset($location_args['region_val']) && $location_args['region_val'] != '') {
				$search_query .= " AND LOWER(" . $location_args['region_column_name'] . ") = \"" . geodir_strtolower($location_args['region_val']) . "\" ";
			}

			if (isset($location_args['city_val']) && $location_args['city_val'] != '') {
				$search_query .= " AND LOWER(" . $location_args['city_column_name'] . ") = \"" . geodir_strtolower($location_args['city_val']) . "\" ";
			}
			
			if (isset($location_args['neighbourhood_val']) && $location_args['neighbourhood_val'] != '') {
				$search_query .= " AND LOWER(" . $location_args['neighbourhood_column_name'] . ") = \"" . geodir_strtolower($location_args['neighbourhood_val']) . "\" ";
			}
			break ;
	} // end of switch
/*
echo '
'.
$search_query
.'
';*/

	if ($location_args['country_non_restricted'] != '') {
		$search_query .= " AND LOWER(country) IN ($location_args[country_non_restricted]) ";
	}

	if ($location_args['region_non_restricted'] != '') {
		if ($location_args['what'] == 'region') {
			$search_query .= " AND LOWER(region) IN ($location_args[region_non_restricted]) ";
		}
	}

	if ($location_args['city_non_restricted'] != '') {
		if ($location_args['what'] == 'city') {
			$search_query .= " AND LOWER(city) IN ($location_args[city_non_restricted]) ";
		}
	}

	//page
	if ($location_args['no_of_records']){
		$spage = $location_args['no_of_records'] * $location_args['spage'];
	} else {
		$spage = 0;
	}

	// limit
	$limit = $location_args['no_of_records'] != '' ? ' LIMIT ' . $spage . ', ' . (int)$location_args['no_of_records'] . ' ' : '';

	// display all locations with same name also
	$search_field = $location_args['what'];
	
	if ($switcher) {
		$select = $search_field . $location_link_column;
		$group_by = $search_field;
		$order_by = $search_field;
		
		if ( $search_field == 'city' ) {
			$select .= ', country, region, city, country_slug, region_slug, city_slug';
			$group_by = 'country, region, city';
			$order_by = 'city, region, country';
		} else if ( $search_field == 'neighbourhood' ) {
			$select = "hood_name AS neighbourhood " . $location_link_column;
			$select .= ', country, region, city, hood_name AS neighbourhood, country_slug, region_slug, city_slug, hood_slug AS neighbourhood_slug';
			$group_by = 'country, region, city, hood_name';
			$order_by = 'hood_name, city, region, country';
		} else if( $search_field == 'region' ) {
			$select .= ', country, region, country_slug, region_slug';
			$group_by = 'country, region';
			$order_by = 'region, country';
		} else if( $search_field == 'country' ) {
			$select .= ', country, country_slug';
			$group_by = 'country';
			$order_by = 'country';
		}
		
		if ($search_field == 'neighbourhood') {
			$main_location_query = "SELECT " . $select . " FROM " . POST_NEIGHBOURHOOD_TABLE . " AS h LEFT JOIN " . POST_LOCATION_TABLE . " AS l ON l.location_id = h.hood_location_id WHERE 1=1 " . $search_query . " GROUP BY " . $group_by . " ORDER BY " . $order_by . " " . $location_args['order_by'] . " " . $limit;
		} else {
			$main_location_query = "SELECT " . $select . " FROM " .POST_LOCATION_TABLE." WHERE 1=1 " . $search_query . " GROUP BY " . $group_by . " ORDER BY " . $order_by . " " . $location_args['order_by'] . " " . $limit;
		}
	} else {
		if ($search_field == 'neighbourhood') {
			$main_location_query = "SELECT hood_name AS neighbourhood " . $location_link_column . " FROM " . POST_NEIGHBOURHOOD_TABLE . " AS h LEFT JOIN " .POST_LOCATION_TABLE." AS l ON l.location_id = h.hood_location_id WHERE 1=1 " .  $search_query . " GROUP BY hood_name ORDER BY hood_name " . $location_args['order_by'] . " " . $limit;
		} else {
			$main_location_query = "SELECT $location_args[what] $location_link_column FROM " .POST_LOCATION_TABLE." WHERE 1=1 " .  $search_query . " GROUP BY $location_args[what] ORDER BY $location_args[what] $location_args[order_by] $limit";
		}
	}
	/*echo '
'.$main_location_query;*/

	$locations = $wpdb->get_results( $main_location_query );

	if( $switcher && !empty( $locations ) ) {
		$new_locations = array();

		foreach( $locations as $location ) {
			$new_location = $location;
			$label = $location->{$search_field};
			if( ( $search_field == 'city' || $search_field == 'neighbourhood' || $search_field == 'region' ) && (int)geodir_location_check_duplicate( $search_field, $label ) > 1 ) {

				if( $search_field == 'neighbourhood' ) {
					$label .= ', ' . $location->city;
				} else if( $search_field == 'city' ) {
					$label .= ', ' . $location->region;
				} else if( $search_field == 'region' ) {
					$country_iso2 = geodir_location_get_iso2( $location->country );
					$country_iso2 = $country_iso2 != '' ? $country_iso2 : $location->country;
					$label .= $country_iso2 != '' ? ', ' . $country_iso2 : '';
				}
			}
			$new_location->title = $location->{$search_field};
			$new_location->{$search_field} = $label;
			$new_location->label = $label;
			$new_locations[] = $new_location;
		}
		$locations = $new_locations;
	}

	$location_as_formated_list = "";
	if (!empty($location_args['format'])) {
		if ($location_args['format']['type'] == 'array')
			return $locations ;
		elseif ($location_args['format']['type'] == 'jason')
			return json_encode($locations) ;
		else {
			$base_location_link = geodir_get_location_link('base');
			$container_wrapper = '' ;
			$container_wrapper_attr = '' ;
			$item_wrapper = '' ;
			$item_wrapper_attr = '' ;

			if (isset($location_args['format']['container_wrapper']) && !empty($location_args['format']['container_wrapper']))
				$container_wrapper = $location_args['format']['container_wrapper'] ;

			if (isset($location_args['format']['container_wrapper_attr']) && !empty($location_args['format']['container_wrapper_attr']))
				$container_wrapper_attr = $location_args['format']['container_wrapper_attr'] ;

			if (isset($location_args['format']['item_wrapper']) && !empty($location_args['format']['item_wrapper']))
				$item_wrapper = $location_args['format']['item_wrapper'] ;

			if (isset($location_args['format']['item_wrapper_attr']) && !empty($location_args['format']['item_wrapper_attr']))
				$item_wrapper_attr = $location_args['format']['item_wrapper_attr'] ;


			if (!empty($container_wrapper))
				$location_as_formated_list = "<" . $container_wrapper . " " . $container_wrapper_attr . ">";

			if (!empty($locations)) {
				foreach ($locations as $location) {
					if (!empty($item_wrapper))
						$location_as_formated_list .= "<" . $item_wrapper . " " . $item_wrapper_attr . ">";
					
					if (isset($location->location_link)) {
						$location_as_formated_list .= "<a href='" . geodir_location_permalink_url( $base_location_link. $location->location_link ). "'><i class='fa fa-caret-right'></i> ";
					}

					$location_as_formated_list .= $location->{$location_args['what']};

					if (isset($location->location_link)) {
						$location_as_formated_list .= "</a>";
					}

					if (!empty($item_wrapper))
						$location_as_formated_list .= "</" . $item_wrapper . ">";
				}
			}
			
			if (!empty($container_wrapper))
				$location_as_formated_list .= "</" . $container_wrapper . ">";

			return $location_as_formated_list;
		}
	}
	return $locations ;
}

/**
 * Get ISO2 country code for the given country.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $country Country name.
 * @return null|string
 */
function geodir_location_get_iso2( $country ) {
	global $wpdb;
	$sql = $wpdb->prepare( "SELECT ISO2 FROM " . GEODIR_COUNTRIES_TABLE . " WHERE Country LIKE %s", $country );
	$result = $wpdb->get_var( $sql );
	return $result;
}

/**
 * Check location duplicates.
 *
 * @since 1.0.0
 * @since 1.4.4 Updated for the neighbourhood system improvement.
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $field The field to check for duplicates.
 * @param string $location The location value to check for duplicates.
 * @return int Total rows found.
 */
function geodir_location_check_duplicate( $field, $location ) {
	global $wpdb;

	$sql = '';
	$result = 0;
	if( $field == 'city' ) {
		$sql = $wpdb->prepare( "SELECT COUNT(*) AS total FROM " . POST_LOCATION_TABLE . " WHERE " . $field . "=%s GROUP BY " . $field, $location, $location );
		$row = $wpdb->get_results( $sql );
		if( !empty( $row ) && isset( $row[0]->total ) ) {
			$result = (int)$row[0]->total;
		}
	} else if( $field == 'region' ) {
		$sql = $wpdb->prepare( "SELECT COUNT(*) AS total FROM " . POST_LOCATION_TABLE . " WHERE " . $field . "=%s GROUP BY country, " . $field, $location, $location );
		$row = $wpdb->get_results( $sql );
		if( !empty( $row ) && count( $row ) > 0 ) {
			$result = (int)count( $row );
		}
	} else if( $field == 'neighbourhood' ) {
		$field = 'hood_name';
		
		$sql = $wpdb->prepare( "SELECT COUNT(*) AS total FROM " . POST_NEIGHBOURHOOD_TABLE . " WHERE " . $field . "=%s GROUP BY " . $field, $location, $location );
		$row = $wpdb->get_results( $sql );
		if( !empty( $row ) && isset( $row[0]->total ) ) {
			$result = (int)$row[0]->total;
		}
	}
	return $result;
}

/**
 * Returns countries array.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $from Get countries from table or option?
 * @return array Countries array.
 */
function geodir_get_countries_array( $from = 'table' ) {
    global $wpdb;

    if ( $from == 'table' ) {
        $countries = $wpdb->get_col( "SELECT Country FROM " . GEODIR_COUNTRIES_TABLE . " ORDER BY Country ASC" );
    } else {
        $countries = get_option( 'geodir_selected_countries' );
    }
    
    $countires_array = '' ;
    
    foreach ( $countries as $key => $country ) {
        $countires_array[$country] = __( $country, 'geodirectory' ) ;
    }
    asort($countires_array);

    return $countires_array ;
}

/**
 * Get countries in a dropdown.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param $selected_option
 */
function geodir_get_limited_country_dl( $selected_option ) {
    global $wpdb;

    $selected_countries = geodir_get_countries_array( 'saved_option' );
    $rows = $wpdb->get_results("SELECT Country,ISO2 FROM " . GEODIR_COUNTRIES_TABLE . " ORDER BY Country ASC");
    
    $ISO2 = array();
    $countries = array();
    
    foreach ($rows as $row) {
        if (isset($selected_countries[$row->Country])) {
            $ISO2[$row->Country] = $row->ISO2;
            $countries[$row->Country] = $selected_countries[$row->Country];
        }
    }
    
    asort($countries);
    
    $out_put = '<option ' . selected('', $selected_option, false) . ' value="">' . __('Select Country', 'geodirectory') . '</option>';
    
    foreach ($countries as $country => $name) {
        $ccode = $ISO2[$country];

        $out_put .= '<option ' . selected($selected_option, $country, false) . ' value="' . esc_attr($country) . '" data-country_code="' . $ccode . '">' . $name . '</option>';
    }

    echo $out_put;
}

/**
 * Get location data as an array or object.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $which Location type. Possible values are 'country', 'region', 'city'. Default: 'country'.
 * @param string $format Output format. Possible values are 'array', 'object'. Default: 'array'.
 * @return object|string|array Location array or object.
 */
function geodir_get_limited_location_array($which = 'country' , $format = 'array') {
    $location_array = '' ;
    $locations = '' ;
    
    switch($which) {
        case 'country':
            $locations = get_option('geodir_selected_countries');
            break;
        case 'region':
            $locations = get_option('geodir_selected_regions');
            break;
        case 'city':
            $locations = get_option('geodir_selected_cities');
            break;
    }

    if (!empty($locations) && is_array($locations)) {
        foreach($locations as $location)
            $location_array[$location] = $location ;
    }

    if ($format=='object')
        $location_array = (object)$location_array ;

    return $location_array ;
}


/**
 * Handles location form data.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_location_form_submit_handler()
{
	if(isset($_REQUEST['geodir_location_merge']) && $_REQUEST['geodir_location_merge'] == 'merge')
	{
		include_once('geodir_merge_field.php');
		exit;
	}

	if(isset($_REQUEST['location_ajax_action']))
	{
		switch($_REQUEST['location_ajax_action']):
			case 'settings':

				geodir_update_options(geodir_location_default_options());
				
				// Flush rewrite rules to generate new rewrite rules.
				flush_rewrite_rules(false);

				$msg = GD_LOCATION_SETTINGS_SAVED;

				$msg = urlencode($msg);

				$location = admin_url()."admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_setting&location_success=".$msg;
				wp_redirect($location);
				gd_die();

			break;
			case 'location':
				geodir_add_location();
			break;
			case 'add_hood':
				geodir_add_neighbourhood();
			break;
			case 'set_default':
				geodir_set_default();
			break;
			case 'merge':
				geodir_merge_location();
			break;
			case 'delete':
				geodir_delete_location();
			break;
			case 'delete_hood':
				geodir_delete_hood();
			break;
			case 'merge_cities':
				include_once('geodir_merge_field.php');
				exit();
			break;
			case 'set_region_on_map':
				geodir_get_region_on_map();
			break;
			case 'geodir_set_location_seo':
				geodir_set_location_seo_settings();
			break;
			case 'geodir_save_cat_location':
				geodir_save_cat_location();
			break;
			case 'geodir_change_cat_location':
				geodir_change_cat_location();
			break;


		endswitch;
	}
}


/**
 * Get location SEO information using location slug.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $slug Location slug.
 * @param string $location_type Location type. Possible values 'gd_city','gd_region','gd_country'.
 * @param string $country_slug Country slug.
 * @param string $region_slug Region slug.
 * @return mixed|null
 */
function geodir_location_seo_by_slug($slug, $location_type='city', $country_slug='', $region_slug='')
{
	global $wpdb;
	if ($slug=='') {
		return NULL;
	}

	$whereField = '1';
	$whereVal = array();

	switch($location_type) {
		case 'country': {
			$whereField .= ' AND location_type=%s AND country_slug=%s';
			$whereVal[] = $location_type;
			$whereVal[] = $slug;
		}
		break;
		case 'region': {
			$whereField .= ' AND location_type=%s AND region_slug=%s';
			$whereVal[] = $location_type;
			$whereVal[] = $slug;
			if ($country_slug!='') {
				$whereField .= ' AND country_slug=%s';
				$whereVal[] = $country_slug;
			}
		}
		break;
		case 'city': {
			$whereField .= ' AND location_type=%s AND city_slug=%s';
			$whereVal[] = $location_type;
			$whereVal[] = $slug;
			if ($country_slug!='') {
				$whereField .= ' AND country_slug=%s';
				$whereVal[] = $country_slug;
			}
			if ($region_slug!='') {
				$whereField .= ' AND region_slug=%s';
				$whereVal[] = $region_slug;
			}
		}
		break;
	}
	if (empty($whereVal)) {
		return NULL;
	}

	$sql = $wpdb->prepare( "SELECT seo_id, seo_title, seo_desc, seo_image, seo_image_tagline FROM ".LOCATION_SEO_TABLE." WHERE ".$whereField." ORDER BY seo_id LIMIT 1", $whereVal );

	$row = $wpdb->get_row($sql);
	if (is_object($row)) {
		return $row;
	}
	return NULL;
}

/**
 * Get location SEO information from current or from location array.
 *
 * @since 1.4.5
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param array|null $loc The location array of slugs or null.
 * @return object|null The object of the seo results or null.
 */
function geodir_get_location_seo($loc=array())
{
    global $wpdb;


    if(empty($loc)){$loc = geodir_get_current_location_terms();}


    if(empty($loc)){return NULL;}

    $slug ='';

    if(isset($loc['gd_country']) && $loc['gd_country']){
        $location_type = 'country';
        $country_slug = $loc['gd_country'];
        $slug = $loc['gd_country'];
    }
    if(isset($loc['gd_region']) && $loc['gd_region']){
        $location_type = 'region';
        $region_slug = $loc['gd_region'];
        $slug = $loc['gd_region'];
    }
    if(isset($loc['gd_city']) && $loc['gd_city']){
        $location_type = 'city';
        $slug = $loc['gd_city'];
    }

    if(!$slug){return NULL;}



    $whereField = '1';
    $whereVal = array();

    switch($location_type) {
        case 'country': {
            $whereField .= ' AND location_type=%s AND country_slug=%s';
            $whereVal[] = $location_type;
            $whereVal[] = $slug;
        }
            break;
        case 'region': {
            $whereField .= ' AND location_type=%s AND region_slug=%s';
            $whereVal[] = $location_type;
            $whereVal[] = $slug;
            if ($country_slug!='') {
                $whereField .= ' AND country_slug=%s';
                $whereVal[] = $country_slug;
            }
        }
            break;
        case 'city': {
            $whereField .= ' AND location_type=%s AND city_slug=%s';
            $whereVal[] = $location_type;
            $whereVal[] = $slug;
            if ($country_slug!='') {
                $whereField .= ' AND country_slug=%s';
                $whereVal[] = $country_slug;
            }
            if ($region_slug!='') {
                $whereField .= ' AND region_slug=%s';
                $whereVal[] = $region_slug;
            }
        }
            break;
    }
    if (empty($whereVal)) {
        return NULL;
    }

    $sql = $wpdb->prepare( "SELECT seo_id, seo_title, seo_desc, seo_image, seo_image_tagline FROM ".LOCATION_SEO_TABLE." WHERE ".$whereField." ORDER BY seo_id LIMIT 1", $whereVal );

    $row = $wpdb->get_row($sql);
    if (is_object($row)) {
        return $row;
    }
    return NULL;
}

/**
 * Get location city information using location slug.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $slug Location slug.
 * @param string $country_slug Country slug.
 * @param string $region_slug Region slug.
 * @return mixed|null
 */
function geodir_city_info_by_slug($slug, $country_slug='', $region_slug='')
{
	global $wpdb;

	if ($slug=='') {
		return NULL;
	}

	$whereVal = array();
	$whereField = 'city_slug=%s';
	$whereVal[] = $slug;

	if ($country_slug!='') {
		$whereField .= ' AND country_slug=%s';
		$whereVal[] = $country_slug;
	}
	if ($region_slug!='') {
		$whereField .= ' AND region_slug=%s';
		$whereVal[] = $region_slug;
	}

	$row = $wpdb->get_row(
		$wpdb->prepare( "SELECT location_id, country_slug, region_slug, city_slug, country, region, city, city_meta, city_desc FROM ".POST_LOCATION_TABLE." WHERE ".$whereField." ORDER BY location_id LIMIT 1", $whereVal )
	);
	if (is_object($row)) {
		return $row;
	}
	return NULL;
}

/**
 * Get region on map.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function geodir_get_region_on_map() {
    global $wpdb;

    if (isset($_REQUEST['country']) && $_REQUEST['country'] != '' && isset($_REQUEST['city']) && $_REQUEST['city'] != '') {
        $region = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT region FROM ".POST_LOCATION_TABLE." WHERE country=%s AND city=%s",
                array($_REQUEST['country'], $_REQUEST['city'])
            )
        );

        if (!$region)
            $region = sanitize_text_field($_REQUEST['state']);

        echo $region;
    }
    exit;
}


/**
 * Handles 'add neighbourhood' form data.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 */
function geodir_add_neighbourhood() {
    global $wpdb, $plugin_prefix;

    if (isset($_REQUEST['location_addedit_nonce']) && current_user_can( 'manage_options' )) {
        if (!wp_verify_nonce( $_REQUEST['location_addedit_nonce'], 'location_add_edit_nonce')) {
            return;
        }

        $hood_name = stripslashes(sanitize_text_field($_REQUEST['hood_name']));
        $gd_latitude = stripslashes(sanitize_text_field($_REQUEST['gd_latitude']));
        $gd_longitude = stripslashes(sanitize_text_field($_REQUEST['gd_longitude']));
        $city_id = (int)$_REQUEST['update_city'];
        $hood_id = (int)$_REQUEST['update_hood'];
        $hood_slug = create_location_slug($hood_name);

        $countslug = $wpdb->get_var(
            $wpdb->prepare(
            "SELECT COUNT(hood_id) AS total from " . POST_NEIGHBOURHOOD_TABLE . " WHERE hood_slug LIKE %d",
            array($hood_slug.'%')
            )
        );

        if ($countslug > 0) {
            $number = $countslug + 1;
            $hood_slug = $hood_slug . '-' . $number;
        }

        if ($hood_id) {
            $duplicate = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT hood_id from " . POST_NEIGHBOURHOOD_TABLE . " WHERE hood_location_id = %d AND hood_name=%s AND hood_id!=%d",
                    array($city_id, $hood_name, $hood_id)
                )
            );
        } else {
            $duplicate = $wpdb->get_var(
                $wpdb->prepare(
                "SELECT hood_id from " . POST_NEIGHBOURHOOD_TABLE . " WHERE hood_location_id = %d AND hood_name=%s",
                array($city_id, $hood_name)
                )
            );
        }

        if (!empty($duplicate)) {
            $setid = $hood_id ? '&hood_id=' . $hood_id : '';

            $msg = urlencode(GD_NEIGHBOURHOOD_EXITS);

            $location = admin_url() . "admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_addedit&add_hood=true&location_error=".$msg."&id=" . $city_id . $setid;
            wp_redirect($location);
            exit;
        }

        if ($_POST['location_ajax_action'] == 'add_hood') {
            if ($hood_id) {
                $sql = $wpdb->prepare("UPDATE " . POST_NEIGHBOURHOOD_TABLE . " SET
                hood_location_id=%d,
                hood_name=%s,
                hood_latitude=%s,
                hood_longitude=%s,
                hood_slug=%s
                WHERE hood_id = %d",
                array($city_id, $hood_name, $gd_latitude, $gd_longitude, $hood_slug, $hood_id));

                $location_hood = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT l.city, n.hood_slug FROM " . POST_LOCATION_TABLE . " l, " . POST_NEIGHBOURHOOD_TABLE . " n WHERE n.hood_location_id=l.location_id AND hood_id=%d",
                        array($hood_id)
                    )
                );

                $geodir_posttypes = geodir_get_posttypes();

                foreach ($geodir_posttypes as $geodir_posttype) {

                    $table = $plugin_prefix . $geodir_posttype . '_detail';

                    if ($wpdb->get_var("SHOW COLUMNS FROM " . $table . " WHERE field = 'post_neighbourhood'")) {
                        if (!empty($location_hood)) {
                            foreach($location_hood as $hood_del){
                                $wpdb->query(
                                    $wpdb->prepare(
                                        "UPDATE " . $table . " SET post_neighbourhood=%s WHERE post_city=%s AND post_neighbourhood=%s",
                                        array($hood_slug, $hood_del->city, $hood_del->hood_slug)
                                    )
                                );
                            }
                        }
                    }
                }
                $msg = MSG_NEIGHBOURHOOD_UPDATED;
            } else {
                $sql = $wpdb->prepare("INSERT INTO " . POST_NEIGHBOURHOOD_TABLE . " SET
                    hood_location_id=%d,
                    hood_name=%s,
                    hood_slug=%s,
                    hood_latitude=%s,
                    hood_longitude=%s",
                    array($city_id, $hood_name, $hood_slug, $gd_latitude, $gd_longitude));

                $msg = MSG_NEIGHBOURHOOD_ADDED;
            }

            $wpdb->query($sql);

            $msg = urlencode($msg);
            $location = admin_url() . "admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_manager&location_success=" . $msg . "&city_hood=hoodlist&id=" . $city_id;
            wp_redirect($location);
            gd_die();
        }
    } else {
        wp_redirect(geodir_login_url());
		gd_die();
    }
}

/**
 * Get neighbourhoods in dropdown.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $city
 * @param string $selected_id
 * @param bool $echo
 * @return string
 */
function geodir_get_neighbourhoods_dl($city='', $selected_id='', $echo = true)
{
	global $wpdb;


	$neighbourhoods = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM ".POST_NEIGHBOURHOOD_TABLE." hood, ".POST_LOCATION_TABLE." location WHERE hood.hood_location_id = location.location_id AND location.city=%s ORDER BY hood_name ",
			array($city)
		)
	);

	$selectoptions = '';
	if(!empty($neighbourhoods)){
		$selectoptions = '<option value="">'.__('Select Neighbourhood','geodirlocation').'</option>';

		foreach($neighbourhoods as $neighbourhood)
		{
			$selected = '';
			if($neighbourhood->hood_slug == $selected_id)
				$selected = ' selected="selected" ';

			$selectoptions.= '<option value="'.$neighbourhood->hood_slug.'" '.$selected.'>'.$neighbourhood->hood_name.'</option>';

		}
	}

	if($echo)
		echo $selectoptions;
	else
		return $selectoptions;
}


/**
 * Handles 'add location' form data.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 */
function geodir_add_location() {
    global $wpdb, $plugin_prefix;

    if (isset($_REQUEST['location_addedit_nonce']) && current_user_can( 'manage_options' )) {
        if (!wp_verify_nonce($_REQUEST['location_addedit_nonce'], 'location_add_edit_nonce')) {
            return;
        }

        $gd_city = stripslashes(sanitize_text_field($_REQUEST['gd_city']));
        $gd_region = stripslashes(sanitize_text_field($_REQUEST['gd_region']));
        $gd_country = stripslashes(sanitize_text_field($_REQUEST['gd_country']));
        $gd_latitude = stripslashes(sanitize_text_field($_REQUEST['gd_latitude']));
        $gd_longitude = stripslashes(sanitize_text_field($_REQUEST['gd_longitude']));
        $city_meta = stripslashes(sanitize_text_field($_REQUEST['city_meta']));
        $city_desc = stripslashes(wp_filter_post_kses($_REQUEST['city_desc']));
        
        $id = (int)$_REQUEST['update_city'];

        if ($id) {
            $duplicate = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT location_id from " . POST_LOCATION_TABLE . " WHERE city = %s AND region=%s AND country=%s AND location_id!=%d",
                    array($gd_city, $gd_region, $gd_country,$id)
                )
            );
        } else {
            $duplicate = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT location_id from " . POST_LOCATION_TABLE . " WHERE city = %s AND region=%s AND country=%s",
                    array($gd_city, $gd_region, $gd_country)
                )
            );
        }

        if ($duplicate != '') {
            $setid = $id ? '&id=' . $id : '';
            $msg = urlencode(GD_LOCATION_EXITS);
            
            $location = admin_url() . "admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_addedit&location_error=" . $msg . $setid;
            wp_redirect($location);
            exit;
        }

        if ($_POST['location_ajax_action'] == 'location') {
            $country_slug = create_location_slug($gd_country);
            $region_slug = create_location_slug($gd_region);
            $city_slug = create_location_slug($gd_city);

            if ($id) {
                $old_location = geodir_get_location_by_id('' , $id);

                $sql = $wpdb->prepare("UPDATE " . POST_LOCATION_TABLE . " SET
                    country=%s,
                    region=%s,
                    city=%s,
                    city_latitude=%s,
                    city_longitude=%s,
                    country_slug = %s,
                    region_slug = %s,
                    city_slug = %s,
                    city_meta=%s,
                    city_desc=%s WHERE location_id = %d",
                    array($gd_country, $gd_region, $gd_city, $gd_latitude, $gd_longitude, $country_slug, $region_slug, $city_slug, $city_meta, $city_desc, $id)
                );

                $wpdb->query($sql);

                $geodir_location = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . POST_LOCATION_TABLE . " WHERE is_default='1' AND location_id = %d", array($id)), "OBJECT" );

                if (!empty($geodir_location)) {
                    update_option('geodir_default_location', $geodir_location); // UPDATE DEFAULT LOCATION OPTION
                }

                $msg = GD_LOCATION_UPDATED;

                //UPDATE AND DELETE LISTING
                $posttype = geodir_get_posttypes();
                if (isset($_REQUEST['listing_action']) && $_REQUEST['listing_action'] == 'delete') {
                    foreach ($posttype as $posttypeobj) {
                        $post_locations = '['.$city_slug.'],['.$region_slug.'],['.$country_slug.']'; // set all overall post location

                        $sql = $wpdb->prepare(
                                "UPDATE " . $plugin_prefix . $posttypeobj . "_detail SET post_city=%s, post_region=%s, post_country=%s, post_locations=%s
                                WHERE post_location_id=%d AND ( post_city!=%s OR post_region!=%s OR post_country!=%s)",
                                array($gd_city, $gd_region, $gd_country, $post_locations, $id, $gd_city, $gd_region, $gd_country)
                            );
                        $wpdb->query($sql);
                    }
                }
            } else {
                $location_info = array();
                $location_info['city'] = $gd_city;
                $location_info['region'] = $gd_region;
                $location_info['country'] = $gd_country;
                $location_info['country_slug'] = $country_slug;
                $location_info['region_slug'] = $region_slug;
                $location_info['city_slug'] = $city_slug;
                $location_info['city_latitude'] = $gd_latitude;
                $location_info['city_longitude'] = $gd_longitude;
                $location_info['is_default'] = 0;
                $location_info['city_meta'] = $city_meta;
                $location_info['city_desc'] = $city_desc;

                geodir_add_new_location_via_adon($location_info);

                $msg = GD_LOCATION_SAVED;
            }

            $msg = urlencode($msg);

            $location = admin_url() . "admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_manager&location_success=" . $msg;
            wp_redirect($location);
			gd_die();
        }
    } else {
        wp_redirect(geodir_login_url());
		gd_die();
    }
}

/**
 * Delete neighbourhood by ID.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param int $hood_id The neighbourhood ID.
 */
function geodir_neighbourhood_delete($hood_id)
{

	global $wpdb,$plugin_prefix;

	$location_hood = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT l.city, n.hood_slug FROM ".POST_LOCATION_TABLE." l, ".POST_NEIGHBOURHOOD_TABLE." n WHERE n.hood_location_id=l.location_id AND hood_id=%d",
			array($hood_id)
		)
	);

	$geodir_posttypes = geodir_get_posttypes();

	foreach($geodir_posttypes as $geodir_posttype){

		$table = $plugin_prefix . $geodir_posttype . '_detail';

		if($wpdb->get_var("SHOW COLUMNS FROM ".$table." WHERE field = 'post_neighbourhood'"))
		{
			if(!empty($location_hood)){
				foreach($location_hood as $hood_del){

					$wpdb->query(
						$wpdb->prepare(
							"UPDATE ".$table." SET post_neighbourhood='' WHERE post_city=%s AND post_neighbourhood=%s",
							array($hood_del->city,$hood_del->hood_slug)
						)
					);

				}
			}

		}
 }

 $wpdb->query($wpdb->prepare("DELETE FROM ".POST_NEIGHBOURHOOD_TABLE." WHERE hood_id=%d",array($hood_id)));

}

/**
 * Merge locations.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 */
function geodir_merge_location()
{

	global $wpdb,$plugin_prefix;
	if(isset($_REQUEST['location_merge_nonce']) && current_user_can( 'manage_options' )){

		if ( !wp_verify_nonce( $_REQUEST['location_merge_nonce'], 'location_merge_wpnonce' ) )
		exit;

		$geodir_location_merge_ids = trim($_REQUEST['geodir_location_merge_ids'], ',');

		$gd_merge = $_REQUEST['gd_merge'];

		$gd_city = $_REQUEST['gd_city'];

		$gd_region = $_REQUEST['gd_region'];

		$gd_country = $_REQUEST['gd_country'];

		$gd_lat = $_REQUEST['gd_lat'];

		$gd_log = $_REQUEST['gd_log'];

		$geodir_postlocation_merge_ids = array();

		$geodir_merge_ids_array = explode(',',$geodir_location_merge_ids);

		$geodir_merge_ids_length = count($geodir_merge_ids_array);
		$format = array_fill(0, $geodir_merge_ids_length, '%d');
		$format = implode(',', $format);

		$geodir_postlocation_merge_ids = $geodir_merge_ids_array;
		$geodir_postlocation_merge_ids[] = $gd_merge;

		$gd_location_sql = $wpdb->prepare("select * from ".POST_LOCATION_TABLE." WHERE location_id IN ($format) AND location_id!=%d", $geodir_postlocation_merge_ids );

		 $gd_locationinfo = $wpdb->get_results($gd_location_sql);

		 $check_default = '';
		 foreach($gd_locationinfo as $gd_locationinfo_obj)
		 {

			$locationid = $gd_locationinfo_obj->location_id;

			if(!$check_default){

				$check_default = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT location_id FROM ".POST_LOCATION_TABLE." WHERE is_default='1' AND location_id = %d",
						array($locationid)
					)
				);

			}


			/*$location_hood = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT hood_id FROM ".POST_NEIGHBOURHOOD_TABLE." WHERE hood_location_id=%d",
					array($locationid)
				)
			);

			if(!empty($location_hood)){
				foreach($location_hood as $hood_del){

					geodir_neighbourhood_delete($hood_del->hood_id);

				}
			}*/


			$gd_location_del = $wpdb->prepare("DELETE FROM ".POST_LOCATION_TABLE." WHERE  location_id = %d",array($locationid));

			$wpdb->query($gd_location_del);

		 }


		 	$country_slug = create_location_slug($gd_country);
			$region_slug = create_location_slug($gd_region);
			$city_slug = create_location_slug($gd_city);

		 //FILL SELECTED CITY IN MERGE LOCATIONS POST
		 $geodir_posttypes = geodir_get_posttypes();

		 foreach($geodir_posttypes as $geodir_posttype){

			 $table = $plugin_prefix . $geodir_posttype . '_detail';

			 $gd_placedetail_sql = $wpdb->prepare(
			 					"select * from ". $table." WHERE post_location_id IN ($format)",
								$geodir_merge_ids_array
								);

			 $gd_placedetailinfo = $wpdb->get_results($gd_placedetail_sql);

			 foreach($gd_placedetailinfo as $gd_placedetailinfo_obj)
			 {
				$postid = $gd_placedetailinfo_obj->post_id;

				$post_locations =  '['.$city_slug.'],['.$region_slug.'],['.$country_slug.']'; // set all overall post location

				 $gd_rep_locationid = $wpdb->prepare("UPDATE ". $table." SET
										post_location_id=%d,
										post_city	= %s,
										post_region	= %s,
										post_country	= %s,
										post_locations = %s
										WHERE  post_id = %d",
										array($gd_merge,$gd_city,$gd_region,$gd_country,$post_locations,$postid));

				$wpdb->query($gd_rep_locationid);

			 }

		 }


		$setdefault = '';
		if(isset($check_default) && $check_default!='')
		{
			$setdefault = ", is_default='1'";

		}

		//UPDATE SELECTED LOCATION

		$sql = $wpdb->prepare("UPDATE ".POST_LOCATION_TABLE." SET
				country=%s,
				region=%s,
				city=%s,
				city_latitude=%s,
				city_longitude=%s,
				country_slug = %s,
				region_slug = %s,
				city_slug = %s
				".$setdefault."
				WHERE location_id = %d",
				array($gd_country,$gd_region,$gd_city,$gd_lat,$gd_log,$country_slug,$region_slug,$city_slug,$gd_merge));

		$wpdb->query($sql);

		if($setdefault != '')
			geodir_location_set_default($gd_merge);

		/* ----- update hooks table ---- */

		$location_hood_info = $wpdb->query(
			$wpdb->prepare(
				"UPDATE ".POST_NEIGHBOURHOOD_TABLE." SET hood_location_id=".$gd_merge." WHERE hood_location_id IN ($format)",
				$geodir_merge_ids_array
			)
		);


			$msg = MSG_LOCATION_MERGE_SUCCESS;
			$msg = urlencode($msg);

		 $location = admin_url()."admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_manager&location_success=".$msg;

		 wp_redirect($location);

		 exit;

		}else{
			wp_redirect(geodir_login_url());
			exit();
		}
}


/**
 * Set default location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param int $locationid Location ID.
 */
function geodir_location_set_default($locationid)
{

	global $wpdb;

	$wpdb->query("update ".POST_LOCATION_TABLE." set is_default='0'");

	$gd_location_default = $wpdb->prepare("UPDATE ".POST_LOCATION_TABLE." SET
							is_default='1'
							WHERE  location_id = %d", array($locationid) );

	$wpdb->query($gd_location_default);

	$geodir_location = $wpdb->get_row("SELECT * FROM ".POST_LOCATION_TABLE." WHERE is_default='1'", "OBJECT" );

	update_option('geodir_default_location', $geodir_location); // UPDATE DEFAULT LOCATION OPTION

}

/**
 * Handles set default location request.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function geodir_set_default()
{
	global $wpdb;

	if(isset($_REQUEST['_wpnonce']) && isset($_REQUEST['id']) && current_user_can( 'manage_options' )){

		$locationid = $_REQUEST['id'];

		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'location_action_'.$_REQUEST['id'] ) )
				return;

		geodir_location_set_default($locationid);

		$msg = MSG_LOCATION_SET_DEFAULT;
		$msg = urlencode($msg);

		$location = admin_url()."admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_manager&location_success=".$msg;

		wp_redirect($location);

		gd_die();

	}else{
		wp_redirect(geodir_login_url());
		exit();
	}

}


/**
 * Handles location deletion request.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @return bool
 */
function geodir_delete_location() {
	global $wpdb, $plugin_prefix;
	
	if ( isset( $_REQUEST['_wpnonce'] ) && isset( $_REQUEST['id'] ) && current_user_can( 'manage_options' ) ) {
		if ( is_array( $_REQUEST['id'] ) && !empty( $_REQUEST['id'] ) ) {
			$ids = $_REQUEST['id'];
			
			if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'location_action_bulk_delete' ) ) {
				return false;
			}
			
			$success = 0;
			foreach ( $ids as $id ) {				
				if ( geodir_location_delete_by_id( $id ) ) {					
					$success++;
				}
			}
			
			$message = __( 'No location deleted.', 'geodirlocation' );
			
			if ( $success > 0 ) {
				$message = $success > 1 ? wp_sprintf( __( '%d locations deleted successfully.', 'geodirlocation' ), $success ) : __( 'Location deleted successfully', 'geodirlocation' );
			}
			
			$message = urlencode( $message );
			
			if ( isset( $_REQUEST['return'] ) && !empty( $_REQUEST['return'] ) ) {
				$location = $_REQUEST['return'] . '&location_success=' . $message;
			} else {
				$location = admin_url() . 'admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_manager&location_success=' . $message;
			}		
		} else {
			$id = $_REQUEST['id'];
	
			if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'location_action_' . $id ) )
				return false;
				
			$message = __( 'No location deleted.', 'geodirlocation' );
			
			if ( geodir_location_delete_by_id( $id ) ) {
				$message = MSG_LOCATION_DELETED;
			}
	
			$message = urlencode( $message );
			$location = admin_url() . "admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_manager&location_success=" . $message;
		}
		
		wp_redirect( $location );
		exit;
	} else {
		wp_redirect( geodir_login_url() );
		exit;
	}
}

//DELETE NEIGHBOURHOOD FUNCTION

/**
 * Handles neighbourhood deletion request.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function geodir_delete_hood()
{
	global $wpdb;

	if(isset($_REQUEST['_wpnonce']) && isset($_REQUEST['id']) && isset($_REQUEST['city_id']) && current_user_can( 'manage_options' )){

	if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'neighbourhood_delete_'.$_REQUEST['id'] ) )
				return;

	$hoodid = $_REQUEST['id'];
	$city_id = $_REQUEST['city_id'];

	if($hoodid)
	{

		geodir_neighbourhood_delete($hoodid);

		$msg = MSG_NEIGHBOURHOOD_DELETED;
		$msg = urlencode($msg);

		$location = admin_url()."admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_manager&location_success=".$msg."&city_hood=hoodlist&id=".$city_id;
		wp_redirect($location);

		exit;
	}

	}else{
		wp_redirect(geodir_login_url());
		exit();
	}

}

/**
 * Get neighbourhoods for the given location ID.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string|int $location Location ID.
 * @return bool|mixed
 */
function geodir_get_neighbourhoods($location = '')
{

	global $wpdb;

	$neighbourhoods = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".POST_NEIGHBOURHOOD_TABLE." WHERE hood_location_id = %d ORDER BY hood_name ", array($location)));

	return (!empty($neighbourhoods)) ?  $neighbourhoods : false;

}


/**
 * Default settings for location manager.
 *
 * @since 1.0.0
 * @since 1.4.4 Options added to exclude location pages from Yoast SEO XML sitemap.
 * @package GeoDirectory_Location_Manager
 *
 * @param array $arr
 * @return array
 */
function geodir_location_default_options($arr=array()) {
	$country_array = geodir_get_countries_array();

	$args = array(
				'what' => 'region' ,
				'echo' => false,
				'filter_by_non_restricted' => false,
				'format' => array('type' => 'array')
			);

	$region_obj = (array)geodir_get_location_array($args);
	$region_array = '' ;
	
	foreach ( $region_obj as $region) {
		$region_array[$region->region] = $region->region ;
	}
	$args = array(
				'what' => 'city' ,
				'echo' => false,
				'filter_by_non_restricted' => false,
				'format' => array('type' => 'array')
			);

	$city_obj = (array)geodir_get_location_array($args);
	$city_array = '' ;
	
	foreach ( $city_obj as $city) {
		$city_array[$city->city] = $city->city ;
	}

	$arr[] = array( 'name' => __( 'Location Settings', 'geodirlocation' ), 'type' => 'no_tabs', 'desc' => '', 'id' => 'location_setting_options' );

    $arr[] = array( 'name' => __( 'Home URL Settings', 'geodirlocation'), 'type' => 'sectionstart', 'id' => 'location_setting_switcher_options_home_go');

    $arr[] = array(
        'name' => __('Home page should go to', 'geodirlocation'),
        'desc' => __('Site root (ex: mysite.com/)', 'geodirlocation'),
        'id' => 'geodir_home_go_to',
        'type' => 'radio',
        'value' => 'root',
        'std' => 'location',
        'radiogroup' => 'start'
    );
    $arr[] = array(
        'name' => __('Current location page', 'geodirlocation'),
        'desc' => __('Current location page (ex: mysite.com/location/glasgow/)', 'geodirlocation'),
        'id' => 'geodir_home_go_to',
        'type' => 'radio',
        'std' => 'location',
        'value' => 'location',
        'radiogroup' => 'end'
    );


    $arr[] = array( 'type' => 'sectionend', 'id' => 'location_setting_switcher_options_home_go');

	$arr[] = array( 'name' => __( 'Main Navigation Settings', 'geodirlocation'), 'type' => 'sectionstart', 'id' => 'location_setting_switcher_options');

	$arr[] = array(
		'name' => __( 'Show location switcher in menu', 'geodirlocation' ),
		'desc' 		=> sprintf(__( 'Show change location navigation in main menu? (untick to disable) If you disable this option, none of the change location link will appear in main navigation.', 'geodirlocation' )),
		'id' 		=> 'geodir_show_changelocation_nave',
		'std' 		=> '',
		'type' 		=> 'checkbox',
		'value' => '1',
	);


	$arr[] = array(
		'name' => 	'',
		'desc' 		=> __( 'List drilled-down Regions, Cities.', 'geodirlocation' ),
		'id' 		=> 'geodir_location_switcher_list_mode',
		'std' 		=> '',
		'type' 		=> 'radio',
		'value'		=> 'drill',
		'radiogroup'		=> 'start'
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'List all Countries, Regions, Cities.', 'geodirlocation' ),
			'id' 		=> 'geodir_location_switcher_list_mode',
			'std' 		=> '',
			'type' 		=> 'radio',
			'value'		=> 'all',
			'radiogroup'		=> 'end'
		);

	$arr[] = array( 'type' => 'sectionend', 'id' => 'location_setting_switcher_options');

	/* -------- start location settings ----- */
	$arr[] = array( 'name' => __('Geo Directory Location Settings', 'geodirlocation'), 'type' => 'sectionstart', 'id' => 'geodir_location_setting');

	$arr[] = array(
		'name' => __( 'Home Page Results', 'geodirlocation' ),
		'desc' 		=> __( 'Show default location results on home page (First time only, if geodirectory home page is your site home page and user comes to home page).', 'geodirlocation' ),
		'id' 		=> 'geodir_result_by_location',
		'std' 		=> 'everywhere',
		'type' 		=> 'radio',
		'value'		=> 'default',
		'radiogroup'		=> 'start'
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Show everywhere location results on home page (First time only, if geodirectory home page is your site home page and user comes to home page).', 'geodirlocation' ),
			'id' 		=> 'geodir_result_by_location',
			'std' 		=> 'everywhere',
			'type' 		=> 'radio',
			'value'		=> 'everywhere',
			'radiogroup'		=> 'end'
		);

	$arr[] = array('name' => '',
	'id' 		=> '',
	'type' => 'field_seperator',
	);

	$arr[] = array(
		'name' => __( 'Country', 'geodirlocation' ),
		'desc' 		=> __( 'Enable default country (country drop-down will not appear on add listing and location switcher).', 'geodirlocation' ),
		'id' 		=> 'geodir_enable_country',
		'std' 		=> 'multi',
		'type' 		=> 'radio',
		'value'		=> 'default',
		'radiogroup'		=> 'start'
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Enable Multi Countries', 'geodirlocation' ),
			'id' 		=> 'geodir_enable_country',
			'std' 		=> 'multi',
			'type' 		=> 'radio',
			'value'		=> 'multi',
			'radiogroup'		=> ''
		);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Enable Selected Countries', 'geodirlocation' ),
			'id' 		=> 'geodir_enable_country',
			'std' 		=> 'multi',
			'type' 		=> 'radio',
			'value'		=> 'selected',
			'radiogroup'		=> 'end'
		);


	$arr[] = array(
	'name' => '',
		'desc' 		=> __( 'Only select countries will appear in country drop-down on add listing page and location switcher. Make sure to have default country in your selected countries list for proper site functioning.', 'geodirlocation' ),
		'tip' 		=> '',
		'id' 		=> 'geodir_selected_countries',
		'css' 		=> 'min-width:300px;',
		'std' 		=> array(),
		'type' 		=> 'multiselect',
		'placeholder_text' => __( 'Select Countries', 'geodirlocation' ),
		'class'		=> 'chosen_select',
		'options' =>  $country_array
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Add everywhere option in location switcher country drop-down.', 'geodirlocation' ),
			'id' 		=> 'geodir_everywhere_in_country_dropdown',
			'std' 		=> '1',
			'type' 		=> 'checkbox',
			'value'		=> '1',
		);
	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Hide country part of url for LISTING, CPT and LOCATION pages?', 'geodirlocation' ),
			'id' 		=> 'geodir_location_hide_country_part',
			'std' 		=> '1',
			'type' 		=> 'checkbox',
			'value'		=> '1',
		);

	$arr[] = array('name' => '',
	'id' 		=> '',
	'type' => 'field_seperator',
	);

	/*state*/
	$arr[] = array(
		'name' => __( 'Region', 'geodirlocation' ),
		'desc' 		=> __( 'Enable default region (region drop-down will not appear on add listing and location switcher).', 'geodirlocation' ),
		'id' 		=> 'geodir_enable_region',
		'std' 		=> 'multi',
		'type' 		=> 'radio',
		'value'		=> 'default',
		'radiogroup'		=> 'start'
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Enable Multi Regions', 'geodirlocation' ),
			'id' 		=> 'geodir_enable_region',
			'std' 		=> 'multi',
			'type' 		=> 'radio',
			'value'		=> 'multi',
			'radiogroup'		=> ''
		);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Enable Selected Regions', 'geodirlocation' ),
			'id' 		=> 'geodir_enable_region',
			'std' 		=> 'multi',
			'type' 		=> 'radio',
			'value'		=> 'selected',
			'radiogroup'		=> 'end'
		);

	$arr[] = array(
	'name' => '',
		'desc' 		=> __( 'Only select regions will appear in region drop-down on add listing page and location switcher. Make sure to have default region in your selected regions list for proper site functioning', 'geodirlocation' ),
		'tip' 		=> '',
		'id' 		=> 'geodir_selected_regions',
		'css' 		=> 'min-width:300px;',
		'std' 		=> array(),
		'type' 		=> 'multiselect',
		'placeholder_text' => __( 'Select Regions', 'geodirlocation' ),
		'class'		=> 'chosen_select',
		'options' => $region_array
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Add everywhere option in location switcher region drop-down.', 'geodirlocation' ),
			'id' 		=> 'geodir_everywhere_in_region_dropdown',
			'std' 		=> '1',
			'type' 		=> 'checkbox',
			'value'		=> '1',
		);
	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Hide region part of url for LISTING, CPT and LOCATION pages?', 'geodirlocation' ),
			'id' 		=> 'geodir_location_hide_region_part',
			'std' 		=> '1',
			'type' 		=> 'checkbox',
			'value'		=> '1',
		);

	$arr[] = array('name' => '',
	'id' 		=> '',
	'type' => 'field_seperator',
	);

	/*city*/
	$arr[] = array(
		'name' => __( 'City', 'geodirlocation' ),
		'desc' 		=> __( 'Enable default city (City drop-down will not appear on add listing and location switcher).', 'geodirlocation' ),
		'id' 		=> 'geodir_enable_city',
		'std' 		=> 'multi',
		'type' 		=> 'radio',
		'value'		=> 'default',
		'radiogroup'		=> 'start'
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Enable Multicity', 'geodirlocation' ),
			'id' 		=> 'geodir_enable_city',
			'std' 		=> 'multi',
			'type' 		=> 'radio',
			'value'		=> 'multi',
			'radiogroup'		=> ''
		);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Enable Selected City', 'geodirlocation' ),
			'id' 		=> 'geodir_enable_city',
			'std' 		=> 'multi',
			'type' 		=> 'radio',
			'value'		=> 'selected',
			'radiogroup'		=> 'end'
		);

	$arr[] = array(
	'name' => '',
		'desc' 		=> __( 'Only select cities will appear in city drop-down on add listing page and location switcher. Make sure to have default city in your selected cities list for proper site functioning', 'geodirlocation' ),
		'tip' 		=> '',
		'id' 		=> 'geodir_selected_cities',
		'css' 		=> 'min-width:300px;',
		'std' 		=> array(),
		'type' 		=> 'multiselect',
		'placeholder_text' => __( 'Select Cities', 'geodirlocation' ),
		'class'		=> 'chosen_select',
		'options' => $city_array
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Add everywhere option in location switcher city drop-down.', 'geodirlocation' ),
			'id' 		=> 'geodir_everywhere_in_city_dropdown',
			'std' 		=> '1',
			'type' 		=> 'checkbox',
			'value'		=> '1',
		);

	$arr[] = array('name' => '',
	'id' 		=> '',
	'type' => 'field_seperator',
	);

	$arr[] = array(
			'name'  => __('Wish to enable neighbourhoods ?', 'geodirlocation'),
			'desc' 	=> __("Select the option if you wish to enable neighbourhood options.", 'geodirlocation'),
			'id' 	=> 'location_neighbourhoods',
			'std' 		=> '',
			'type' 	=> 'checkbox',
			'std' 	=> 'yes'
		);

	$arr[] = array( 'type' => 'sectionend', 'id' => 'geodir_location_setting');

	$arr[] = array( 'name' => __( 'Add listing form settings', 'geodirlocation' ), 'type' => 'sectionstart', 'id' => 'geodir_location_setting_add_listing');

	$arr[] = array(
			'name'  => __( 'Disable Google address autocomplete?', 'geodirlocation' ),
			'desc' 	=> __( 'This will stop the address sugestions when typing in address box on add listing page.', 'geodirlocation' ),
			'id' 	=> 'location_address_fill',
			'std' 		=> '',
			'type' 	=> 'checkbox'
		);

	$arr[] = array(
			'name'  => __( 'Show all locations in dropdown?', 'geodirlocation' ),
			'desc' 	=> __( 'This is usefull if you have a small directory but can break your site if you have many locations', 'geodirlocation' ),
			'id' 	=> 'location_dropdown_all',
			'std' 		=> '',
			'type' 	=> 'checkbox'
		);

	$arr[] = array(
			'name'  => __( 'Disable set address on map from changing address fields', 'geodirlocation' ),
			'desc' 	=> __( 'This is usefull if you have a small directory and you have custom locations or your locations are not known by the Google API and they break the address. (highly recommended not to enable this)', 'geodirlocation' ),
			'id' 	=> 'location_set_address_disable',
			'std' 		=> '',
			'type' 	=> 'checkbox'
		);

    $arr[] = array(
        'name'  => __( 'Disable move map pin from changing address fields', 'geodirlocation' ),
        'desc' 	=> __( 'This is usefull if you have a small directory and you have custom locations or your locations are not known by the Google API and they break the address. (highly recommended not to enable this)', 'geodirlocation' ),
        'id' 	=> 'location_set_pin_disable',
        'std' 		=> '',
        'type' 	=> 'checkbox'
    );

	$arr[] = array('type' => 'sectionend', 'id' => 'geodir_location_setting_add_listing');
	
	if (function_exists('wpseo_init')) { // check if Yoast SEO active
		$arr[] = array('name' => __('Yoast SEO XML Sitemaps Settings', 'geodirlocation'), 'type' => 'sectionstart', 'id' => 'geodir_location_seo_xml_sitemap');
		$arr[] = array(
			'name' => __('Exclude location pages in xml sitemap', 'geodirlocation'),
			'desc' => __('Please check the box if you do NOT want to include location pages in your xml sitemap', 'geodirlocation'),
			'id' => 'gd_location_sitemap_exclude_location',
			'std' => '',
			'type' => 'checkbox'
		);
		$arr[] = array(
			'name' => __('Exclude categories location pages in xml sitemap', 'geodirlocation'),
			'desc' => __('Please check the box if you do NOT want to include categories location pages in your xml sitemap', 'geodirlocation'),
			'id' => 'gd_location_sitemap_exclude_cats',
			'std' => '',
			'type' => 'checkbox'
		);
		$arr[] = array(
			'name' => __('Exclude tags location pages in xml sitemap', 'geodirlocation'),
			'desc' => __('Please check the box if you do NOT want to include tags location pages in your xml sitemap', 'geodirlocation'),
			'id' => 'gd_location_sitemap_exclude_tags',
			'std' => '',
			'type' => 'checkbox',
		);
		$arr[] = array('type' => 'sectionend', 'id' => 'geodir_location_seo_xml_sitemap');
	}


	/* -------- end location settings ----- */


	$arr = apply_filters('geodir_location_default_options' ,$arr );

	return $arr;
}


/**
 * Get locations by keyword.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $term Search type. Possible values are 'country', 'region', 'city'.
 * @param string $search Keyword.
 * @param bool $single Return only single row? Default: false.
 * @return bool|mixed
 */
function geodir_get_locations($term = '', $search = '', $single = false)
{

	global $wpdb;

	$where = $group_by = '';

	$where_array = array();

	switch($term):
		case 'country':
			if($search !='' ){
				$where = $wpdb->prepare(" AND ( country = %s OR country_slug = %s )", array($search,$search));
			}else{ $group_by = " GROUP BY country ";}
		break;
		case 'region':
			if($search !='' ){
				$where = $wpdb->prepare(" AND ( region = %s OR region_slug = %s ) ", array($search,$search));
			}else{ $group_by = " GROUP BY region ";}
		break;
		case 'city':
			if($search !='' ){
				$where = $wpdb->prepare(" AND ( city = %s OR city_slug = %s ) ", array($search,$search));
			}else{ $group_by = " GROUP BY city ";}
		break;
	endswitch;

	$locations = $wpdb->get_results(
			"SELECT * FROM ".POST_LOCATION_TABLE." WHERE 1=1 ".$where.$group_by." ORDER BY city "
	);

	return (!empty($locations)) ?  $locations : false;

}
/**/

/**
 * Get default location latitude.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 * @global object $gd_session GeoDirectory Session object.
 *
 * @param float $latitude Default latitude.
 * @param string $is_default Is default?
 * @return string Default location latitude.
 */
function geodir_location_default_latitude($latitude, $is_default) {
	global $gd_session;
	
	if ($is_default == '1' && $gd_session->get('gd_multi_location') && !isset($_REQUEST['pid']) && !isset($_REQUEST['backandedit']) && !$gd_session->get('listing')) {
		if ($gd_ses_city = $gd_session->get('gd_city'))
			$location = geodir_get_locations('city', $gd_ses_city);
		else if ($gd_ses_region = $gd_session->get('gd_region'))
			$location = geodir_get_locations('region', $gd_ses_region);
		else if ($gd_ses_country = $gd_session->get('gd_country'))
			$location = geodir_get_locations('country', $gd_ses_country);

		if (isset($location) && $location)
			$location = end($location);

		$latitude = isset($location->city_latitude) ? $location->city_latitude : '';
	}

	return $latitude;
}

/**
 * Get default location longitude.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 * @global object $gd_session GeoDirectory Session object.
 *
 * @param float $lon Default longitude.
 * @param string $is_default Is default?
 * @return string Default location longitude.
 */
function geodir_location_default_longitude($longitude, $is_default) {
	global $gd_session;
	
	if ($is_default == '1' && $gd_session->get('gd_multi_location') && !isset($_REQUEST['pid']) && !isset($_REQUEST['backandedit']) && !$gd_session->get('listing')) {
		if ($gd_ses_city = $gd_session->get('gd_city'))
			$location = geodir_get_locations('city', $gd_ses_city);
		else if ($gd_ses_region = $gd_session->get('gd_region'))
			$location = geodir_get_locations('region', $gd_ses_region);
		else if ($gd_ses_country = $gd_session->get('gd_country'))
			$location = geodir_get_locations('country', $gd_ses_country);

		if (isset($location) && $location)
			$location = end($location);

		$longitude = isset($location->city_longitude) ? $location->city_longitude : '';
	}

	return $longitude;
}


/**
 * Function for addons to add new location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param array $location_info Location information.
 * @return mixed
 */
function geodir_add_new_location_via_adon($location_info) {
	global $wpdb;
	
	if (!empty($location_info)) {
        $location_info= is_array($location_info) ? array_map('stripslashes_deep', $location_info) : stripslashes($location_info);

		$get_location_info = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . POST_LOCATION_TABLE . " WHERE city LIKE %s AND region LIKE %s AND country LIKE %s", array($location_info['city'], $location_info['region'], $location_info['country'])), "OBJECT");

		if (empty($get_location_info)) {
			$city_meta = isset($location_info['city_meta']) ? $location_info['city_meta'] : '';
			$city_desc = isset($location_info['city_desc']) ? $location_info['city_desc'] : '';

			$wpdb->query(
				$wpdb->prepare("INSERT INTO " . POST_LOCATION_TABLE . " SET
					city = %s,
					region = %s,
					country = %s,
					country_slug = %s,
					region_slug = %s,
					city_slug = %s,
					city_latitude = %s,
					city_longitude = %s,
					is_default	=	%s ,
					city_meta = %s,
					city_desc = %s",
					array($location_info['city'], $location_info['region'], $location_info['country'], $location_info['country_slug'], $location_info['region_slug'], $location_info['city_slug'], $location_info['city_latitude'], $location_info['city_longitude'], $location_info['is_default'], $city_meta, $city_desc)
				)
			);

			$last_location_id = $wpdb->insert_id;
			$location_info = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . POST_LOCATION_TABLE . " WHERE location_id=%d", array($last_location_id)), "OBJECT");
		} else {
			$location_info = $get_location_info;
		}
	}

	return $location_info;
}

/**
 * Adds extra
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param $address
 * @param $field_info
 */
function geodir_location_address_extra_admin_fields($address, $field_info)
{
		(isset($field_info->is_admin) && $field_info->is_admin=='1') ? $display_field = 'style="display:none;"' : $display_field = '';?>

			<tr <?php echo $display_field;?> >
					<td ><strong><?php _e('Display City :', 'geodirlocation');?></strong></td>
					<td align="left">
						<input type="checkbox"  name="extra[show_city]" id="show_city"  value="1" <?php if(isset($address['show_city']) && $address['show_city']=='1'){ echo 'checked="checked"';}?> />
						<span><?php _e('Select if you want to show city field in address section.', 'geodirlocation');?></span>
					</td>
			</tr>

			<tr>
					<td ><strong><?php _e('City label :', 'geodirlocation');?></strong></td>
					<td align="left">
						<input type="text" name="extra[city_lable]" id="city_lable"  value="<?php if(isset($address['city_lable'])){ echo $address['city_lable'];}?>" />
						<span><?php _e('Enter city field label in address section.', 'geodirlocation');?></span>
					</td>
			</tr>

		 <?php (isset($field_info->is_admin) && $field_info->is_admin=='1') ? $display_field = 'style="display:none;"' : $display_field = '';?>
			<tr <?php echo $display_field;?> >
					<td ><strong><?php _e('Display Region :', 'geodirlocation');?></strong></td>
					<td align="left">
						<input type="checkbox"  name="extra[show_region]" id="show_region"  value="1" <?php if(isset($address['show_region']) && $address['show_region']=='1'){ echo 'checked="checked"';}?>/>
						<span><?php _e('Select if you want to show region field in address section.', 'geodirlocation');?></span>
					</td>
			</tr>

			<tr>
					<td ><strong><?php _e('Region label :', 'geodirlocation');?></strong></td>
					<td align="left">
						<input type="text" name="extra[region_lable]" id="region_lable"  value="<?php if(isset($address['region_lable'])){ echo $address['region_lable'];}?>" />
						<span><?php _e('Enter region field label in address section.', 'geodirlocation');?></span>
					</td>
			</tr>

		 <?php (isset($field_info->is_admin) && $field_info->is_admin=='1') ? $display_field = 'style="display:none;"' : $display_field = '';?>
			<tr <?php echo $display_field;?> >
					<td ><strong><?php _e('Display Country :', 'geodirlocation');?></strong></td>
					<td align="left">
						<input type="checkbox"  name="extra[show_country]" id="show_country"  value="1" <?php if(isset($address['show_country']) && $address['show_country']=='1'){ echo 'checked="checked"';}?>/>
						<span><?php _e('Select if you want to show country field in address section.', 'geodirlocation');?></span>
					</td>
			</tr>

		 <tr>
				<td ><strong><?php _e('Country label :', 'geodirlocation');?></strong></td>
				<td align="left">
					<input type="text" name="extra[country_lable]" id="country_lable"  value="<?php if(isset($address['country_lable'])) {echo $address['country_lable'];}?>" />
					<span><?php _e('Enter country field label in address section.', 'geodirlocation');?></span>
				</td>
		</tr>
	<?php
}




//// Location DB requests

/**
 * Parse location list for DB request.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param $list
 * @return string
 */
function geodir_parse_location_list($list)
{
	$list_for_query ='';
	if(!empty($list))
	{
		$list_array = explode(',' , $list);
		if(!empty($list_array ))
		{
			foreach($list_array as $list_item)
			{
				$list_for_query .= "," . "'".geodir_strtolower($list_item )."'" ;
			}
		}
	}
	if(!empty($list_for_query))
		$list_for_query  = trim($list_for_query , ',');

	return $list_for_query ;
}

/**
 * Get current location city or region or country info.
 *
 * @since 1.0.0
 * @since 1.4.4 Updated for the neighbourhood system improvement.
 * @package GeoDirectory_Location_Manager
 *
 * @return string city or region or country info.
 */
function geodir_what_is_current_location($neighbourhood = '') {
	if ($neighbourhood && get_option('location_neighbourhoods')) {
		$neighbourhood = geodir_get_current_location(array('what' => 'neighbourhood' , 'echo' => false));
		if(!empty($neighbourhood))
			return 'neighbourhood';
	}
	
	$city = geodir_get_current_location(array('what' => 'city' , 'echo' => false));
	if(!empty($city))
		return 'city';
	
	$region = geodir_get_current_location(array('what' => 'region' , 'echo' => false));
	if(!empty($region))
		return 'region' ;
	
	$country = geodir_get_current_location(array('what' => 'country' , 'echo' => false)) ;
	if(!empty($country))
		return 'country' ;

	return '';

}

add_filter('geodir_seo_meta_location_description', 'geodir_set_location_meta_desc', 10,1);
/**
 * Add location information to the meta description.
 *
 * @since 1.0.0
 * @since 1.4.1 Return original meta if blank or default settings.
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global object $wp WordPress object.
 *
 * @param string $seo_desc Meta description text.
 * @return null|string Altered meta desc.
 */
function geodir_set_location_meta_desc( $meta_desc ){
	global $wpdb, $wp;

	$gd_country = get_query_var( 'gd_country' );
	$gd_region = get_query_var( 'gd_region' );
	$gd_city = get_query_var( 'gd_city' );
    $seo_desc = '';
	
	if ($gd_city) {
		$info = geodir_city_info_by_slug($gd_city, $gd_country, $gd_region);
		
		if (!empty($info)) {
			$seo_desc .= !empty($info->city_meta) ? $info->city_meta : $info->city_desc;
		}
	} else if (!$gd_city && $gd_region) {
		$info = geodir_location_seo_by_slug($gd_region, 'region', $gd_country);
		
		if (!empty($info)) {
			$seo_desc .= !empty($info->seo_title) ? $info->seo_title : $info->seo_desc;
		}
	} else if (!$gd_city && !$gd_region && $gd_country) {
		$info = geodir_location_seo_by_slug($gd_country, 'country');
		
		if (!empty($info)) {
			$seo_desc .= !empty($info->seo_title) ? $info->seo_title : $info->seo_desc;
		}
	}
	$seo_desc = sanitize_text_field($seo_desc);
	$meta_desc = $seo_desc != '' ? $seo_desc : $meta_desc;
	
	return $meta_desc;
}

/**
 * Save category location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function geodir_save_cat_location() {
	global $wpdb;

	$wpnonce = isset($_REQUEST['wpnonce']) ? $_REQUEST['wpnonce'] : '';
	$locid = isset($_REQUEST['locid']) ? (int)$_REQUEST['locid'] : '';
	$catid = isset($_REQUEST['catid']) ? (int)$_REQUEST['catid'] : '';
	$posttype = isset($_REQUEST['posttype']) ? $_REQUEST['posttype'] : '';
	$content = isset($_REQUEST['content']) ? $_REQUEST['content'] : '';
	$loc_default = isset($_REQUEST['loc_default']) ? $_REQUEST['loc_default'] : '';

	$category_taxonomy = geodir_get_taxonomies($posttype);
	$taxonomy = isset($category_taxonomy[0]) && $category_taxonomy[0] ? $category_taxonomy[0] : 'gd_placecategory';

	if((is_admin() || defined('GD_TESTING_MODE')) && $wpnonce && current_user_can( 'manage_options' ) && $locid>0 && $catid>0 && $posttype) {
		$option = array();
		$option['gd_cat_loc_default'] = (int)$loc_default;
		$option['gd_cat_loc_cat_id'] = $catid;
		$option['gd_cat_loc_post_type'] = $posttype;
		$option['gd_cat_loc_taxonomy'] = $taxonomy;
		$option_name = 'geodir_cat_loc_'.$posttype.'_'.$catid;

		update_option($option_name, $option);

		$option = array();
		$option['gd_cat_loc_loc_id'] = (int)$locid;
		$option['gd_cat_loc_cat_id'] = (int)$catid;
		$option['gd_cat_loc_post_type'] = $posttype;
		$option['gd_cat_loc_taxonomy'] = $taxonomy;
		$option['gd_cat_loc_desc'] = $content;
		$option_name = 'geodir_cat_loc_'.$posttype.'_'.$catid.'_'.$locid;

		update_option($option_name, $option);

		echo 'OK';
		gd_die();
	}
	echo 'FAIL';
	gd_die();
}

/**
 * Change category location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function geodir_change_cat_location() {
	global $wpdb;

	$wpnonce = isset($_REQUEST['wpnonce']) ? $_REQUEST['wpnonce'] : '';
	$gd_location = isset($_REQUEST['locid']) ? (int)$_REQUEST['locid'] : '';
	$term_id = isset($_REQUEST['catid']) ? (int)$_REQUEST['catid'] : '';
	$post_type = isset($_REQUEST['posttype']) ? $_REQUEST['posttype'] : '';

	if((is_admin() || defined('GD_TESTING_MODE')) && $wpnonce && current_user_can( 'manage_options' ) && $gd_location>0 && $term_id>0 && $post_type) {
		$option_name = 'geodir_cat_loc_'.$post_type.'_'.$term_id.'_'.$gd_location;
		$option = get_option($option_name);
		$gd_cat_loc_desc = !empty($option) && isset($option['gd_cat_loc_desc']) ? $option['gd_cat_loc_desc'] : '';
		echo stripslashes_deep($gd_cat_loc_desc);
		gd_die();
	}
	echo 'FAIL';
	gd_die();
}

/**
 * Get actual location name.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param $type
 * @param $term
 * @param bool $translated
 * @return null|string|void
 */
function get_actual_location_name($type, $term, $translated=false) {
	if ($type=='' || $term=='') {
		return NULL;
	}
	$row = geodir_get_locations($type, $term);
	$value = !empty($row) && !empty($row[0]) && isset($row[0]->{$type}) ? $row[0]->{$type} : '';
	if( $translated ) {
		$value = __( $value, 'geodirectory' );
	}
	return $value;
}

/**
 * Get location count for a country.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param string $country Country name.
 * @param string $country_slug Country slug.
 * @param bool $with_translated Return with translation? Default: true.
 * @return int Listing count.
 */
function count_listings_by_country( $country, $country_slug='', $with_translated=false ) {
	global $wpdb, $plugin_prefix;

	$geodir_posttypes = geodir_get_posttypes();

	$total = 0;
	if ( $country == '' ) {
		return $total;
	}

	foreach( $geodir_posttypes as $geodir_posttype ) {
		$table = $plugin_prefix . $geodir_posttype . '_detail';

		if( $with_translated ) {
			$country_translated = __( $country, 'geodirectory');
			$sql = "SELECT COUNT(*) FROM " . $table . " WHERE post_country LIKE '".$country."' OR post_country LIKE '".$country_translated."' OR post_locations LIKE '%,[".$country_slug."]'";
		} else {
			$sql = $wpdb->prepare( "SELECT COUNT(*) FROM " . $table . " WHERE post_country LIKE %s", array( $country ) );
		}
		$count = (int)$wpdb->get_var( $sql );

		$total += $count;
	}
	return $total;
}

/**
 * Get countries from post location table.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @return array Countries array.
 */
function get_post_location_countries() {
	global $wpdb;
	$sql = "SELECT country, country_slug, count(location_id) AS total FROM " . POST_LOCATION_TABLE . " WHERE country_slug != '' && country != '' GROUP BY country_slug ORDER BY country ASC";
	$rows = $wpdb->get_results( $sql );
	return $rows;
}

/**
 * Get post country using country slug.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $country_slug Country slug.
 * @return null|string Country name.
 */
function get_post_country_by_slug( $country_slug ) {
	global $wpdb;
	$sql = $wpdb->prepare( "SELECT country FROM " . POST_LOCATION_TABLE . " WHERE country_slug != '' && country_slug = %s GROUP BY country_slug ORDER BY country ASC", array( $country_slug ) );
	$value = $wpdb->get_var( $sql );
	return $value;
}

/**
 * Update location with translated string.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param $country_slug
 * @return bool
 */
function geodir_update_location_translate( $country_slug ) {
	global $wpdb, $plugin_prefix;
	if( $country_slug == '' ) {
		return false;
	}

	$country = get_post_country_by_slug( $country_slug );
	if( $country == '' ) {
		return false;
	}

	$geodir_posttypes = geodir_get_posttypes();

	$country_translated = __( $country, 'geodirectory' );
	$country_translated = trim( wp_unslash( $country_translated ) );
	$country_slug_translated = sanitize_title( $country_translated );

	$country_slug = apply_filters( 'geodir_filter_update_location_translate', $country_slug, $country, $country_translated, $country_slug_translated );

	do_action( 'geodir_action_update_location_translate', $country_slug, $country, $country_translated, $country_slug_translated );

	if( $country_slug == $country_slug_translated ) {
		return false;
	}

	$sql = $wpdb->prepare( "SELECT location_id FROM " . POST_LOCATION_TABLE . " WHERE country_slug=%s", array( $country_slug ) );
	$location_ids = $wpdb->get_col( $sql );

	/* update in post locations table */
	$update_locations = false;
	//$sql = $wpdb->prepare( "UPDATE " . POST_LOCATION_TABLE . " SET country=%s, country_slug=%s WHERE country_slug=%s", array( $country_translated, $country_slug_translated, $country_slug ) );
	$sql = $wpdb->prepare( "UPDATE " . POST_LOCATION_TABLE . " SET country_slug=%s WHERE country_slug=%s", array( $country_slug_translated, $country_slug ) );
	$update_locations = $wpdb->query($sql);

	/* update in post listings table */
	$update_listings = false;
	if( !empty( $location_ids ) ) {
		$location_ids = implode( ",", $location_ids );
		foreach( $geodir_posttypes as $geodir_posttype ) {
			$table = $plugin_prefix . $geodir_posttype . '_detail';

			$sql = "SELECT post_id, post_locations, post_location_id FROM " . $table . " WHERE post_location_id IN(" . $location_ids  . ")";
			$listings = $wpdb->get_results( $sql );

			if( !empty( $listings ) ) {
				foreach( $listings as $listing ) {
					$post_id = $listing->post_id;
					$location_id = $listing->post_location_id;
					$post_locations = $listing->post_locations;
					if( $post_locations != '' ) {
						$post_locations_arr = explode( ",", $post_locations );

						if( isset( $post_locations_arr[2] ) && trim($post_locations_arr[2]) != '[]' ) {
							$post_locations_arr[2] = '[' . $country_slug_translated . ']';
							$post_locations = implode( ",", $post_locations_arr );
						} else {
							$post_locations = '';
						}
					}

					if( $post_locations == '' ) {
						$location_info = geodir_get_location_by_id( '', $location_id );
						if( !empty( $location_info ) && isset( $location_info->location_id ) ) {
							$post_locations = '['. $location_info->city_slug .'],['. $location_info->region_slug .'],['. $country_slug_translated .']';
						}
					}

					$sql = $wpdb->prepare( "UPDATE " . $table . " SET post_locations=%s, post_country=%s WHERE post_id=%d", array( $post_locations, $country_translated, $post_id ) );
					$update_locations = $wpdb->query($sql);
				}
			}
		}
		$update_locations = true;
	}

	/* update in location seo table */
	$update_location_seo = false;
	$sql = $wpdb->prepare( "UPDATE " . LOCATION_SEO_TABLE . " SET country_slug=%s WHERE country_slug=%s", array( $country_slug_translated, $country_slug ) );
	$update_location_seo = $wpdb->query($sql);

	if( $update_locations || $update_listings || $update_location_seo ) {
		return true;
	}
	return false;
}

/**
 * Returns countries search SQL.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $search Search string.
 * @param bool $array Return as array?. Default false.
 * @return array|string Search SQL
 */
function geodir_countries_search_sql( $search = '', $array = false ) {
	$countries = geodir_get_countries_array();
	$return = $array ? array() : '';

	$search = strtolower( trim( $search ) );
	if( $search == '' ) {
		return $return;
	}

	if( !empty( $countries ) ) {
		foreach( $countries as $row => $value ) {
			$strfind = strtolower( $value );

			if( $row != $value && strpos( $strfind, $search ) === 0 ) {
				$return[] = $row;
			}
		}
	}
	if( $array ) {
		return $return;
	}
	$return = !empty( $return ) ? implode( ",", $return ) : '';
	return $return;
}

/**
 * Clean up location permalink url.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $url Permalink url.
 * @return null|string Url.
 */
function geodir_location_permalink_url( $url ) {
	if ( $url == '' ) {
		return NULL;
	}

	if ( get_option( 'permalink_structure' ) != '' ) {
		$url = trim( $url );
		$url = rtrim( $url, '/' ) . '/';
	}

	$url = apply_filters( 'geodir_location_filter_permalink_url', $url );

	return $url;
}

add_action( 'wp_ajax_gd_location_manager_set_user_location', 'gd_location_manager_set_user_location' );
add_action( 'wp_ajax_nopriv_gd_location_manager_set_user_location', 'gd_location_manager_set_user_location' );

/**
 * Set user location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global object $gd_session GeoDirectory Session object.
 */
function gd_location_manager_set_user_location() {
	global $gd_session;
	
	$gd_session->set('user_lat', $_POST['lat']);
	$gd_session->set('user_lon', $_POST['lon']);
	$gd_session->set('user_pos_time', time());
	
	if (isset($_POST['myloc']) && $_POST['myloc']) {
		$gd_session->set('my_location', 1);
	} else {
		$gd_session->set('my_location', 0);
	}
	exit;
}

/**
 * Remove location and its data using location ID.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 * @global object $gd_session GeoDirectory Session object.
 *
 * @param int $id Location ID.
 * @return bool Returns true when successful deletion.
 */
function geodir_location_delete_by_id( $id ) {
	global $wpdb, $plugin_prefix, $gd_session;
	
	if ( !current_user_can( 'manage_options' ) || !$id > 0 ) {
		return false;
	}

	$geodir_posttypes = geodir_get_posttypes();
	
	do_action( 'geodir_location_before_delete', $id );
	
	$location_info = $wpdb->get_row( $wpdb->prepare( "SELECT city_slug, is_default FROM " . POST_LOCATION_TABLE . " WHERE location_id = %d", array( $id ) ) );
	if ( !empty( $location_info ) && !empty( $location_info->is_default ) ) {
		return false; // Default location
	}
	
	foreach( $geodir_posttypes as $geodir_posttype ) {
		
		$table = $plugin_prefix . $geodir_posttype . '_detail';
		
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM " . $table . " WHERE post_location_id = %d", array( $id ) ) );
		
		if ( !empty( $rows ) ) {
			foreach ( $rows as $row ) {
				wp_delete_post( $row->post_id ); // Delete post
			}
		}
	}
	
	// Remove neighbourhood location
	$wpdb->query( $wpdb->prepare( "DELETE FROM " . POST_NEIGHBOURHOOD_TABLE . " WHERE hood_location_id = %d", array( $id ) ) );
			
	// Remove current location data
	if ( !empty( $location_info ) && !empty( $location_info->city_slug ) && $gd_session->get('gd_city') == $location_info->city_slug ) {
		geodir_unset_location();
	}
	
	// Remove post location data
	$wpdb->query( $wpdb->prepare( "DELETE FROM " . POST_LOCATION_TABLE . " WHERE location_id = %d", array( $id ) ) );
	
	do_action( 'geodir_location_after_delete', $id );
	
	return true;
}

/**
 * Get location countries.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param bool $list Return as list? Default: false.
 * @return array|mixed
 */
function geodir_post_location_countries( $list = false ) {
	global $wpdb;
	$sql = "SELECT country, country_slug, count(location_id) AS total FROM " . POST_LOCATION_TABLE . " WHERE country_slug != '' && country != '' GROUP BY country_slug ORDER BY country ASC";
	$rows = $wpdb->get_results( $sql );
	
	$items = array();
	if ( $list && !empty( $rows ) ) {
		foreach( $rows as $row ) {
			$items[$row->country_slug] = get_actual_location_name( 'country', $row->country_slug, true );
		}
		
		asort( $items );
		
		$rows = $items;
	}
	
	return $rows;	
}

/**
 * Count neighbourhoods using location ID.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param int $location_id Location ID.
 * @return null|string|int Neighbourhood Count.
 */
function geodir_count_hood_by_location( $location_id ) {
	global $wpdb;
	
	if ( !(int)$location_id > 0 ) {
		return NULL;
	}
	
	$sql = $wpdb->prepare( "SELECT COUNT(hood_id) FROM " . POST_NEIGHBOURHOOD_TABLE . " WHERE hood_location_id = %d", array( $location_id ) );
	$result = $wpdb->get_var( $sql );
	
	return $result;
}

/**
 * Get location list for manager location page.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param array $args Location list query args.
 * @return array Location array.
 */
function geodir_manage_location_get_list( $args = array() ) {
	
	$per_page = isset( $_REQUEST['per_page'] ) ? absint( $_REQUEST['per_page'] ) : 0;
	$search = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
	$country = isset( $_REQUEST['country'] ) ? wp_unslash( trim( $_REQUEST['country'] ) ) : '';
	$per_page = $per_page > 0 ? $per_page : 20;
	
	$pagination_args = wp_parse_args( 
										$args, 
										array(
											'per_page' => $per_page,
											'search' => $search,
											'country' => $country,
										)
									);
	$rows = geodir_location_list( $pagination_args );
	
	return $rows;
}

/**
 * Get locations using given arguments.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param array $args Query args.
 * @return array Location array.
 */
function geodir_location_list( $args = array() ) {
	global $wpdb;

	$where = '';
	
	if ( !empty( $args['search'] ) && $args['search'] != '' ) {
		$where .= "AND ( city LIKE '" . wp_slash( $args['search'] ) . "%' OR region LIKE '" . wp_slash( $args['search'] ) . "%' ) ";
	}
	
	if ( !empty( $args['country'] ) && $args['country'] != '' ) {
		$where .= "AND ( country LIKE '" . wp_slash( $args['country'] ) . "' OR country_slug LIKE '" . wp_slash( $args['country'] ) . "' ) ";
	}
	
	$sql = "SELECT COUNT(location_id) FROM " . POST_LOCATION_TABLE . " WHERE 1=1 " . $where;
	$total_items = $wpdb->get_var( $sql );
	
	if ( !empty( $args['count'] ) ) {
		return $total_items;
	}
	
	$total_pages = ( $total_items > 0 && isset( $args['per_page'] ) && $args['per_page'] > 0 ) ? ceil( $total_items / $args['per_page'] ) : 0;
	$args['total_pages'] = $total_pages;
	
	$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;
	
	if ( isset( $args['total_pages'] ) && $pagenum > $args['total_pages'] ) {
		$pagenum = $args['total_pages'];
	}
	
	$pagenum = max( 1, $pagenum );
	$args['total_items'] = $total_items;
	$args['pagenum'] = $pagenum;
	
	$limits = '';
	if ( isset( $args['per_page'] ) && $args['per_page'] > 0 ) {
		$offset = ( $pagenum - 1 ) * $args['per_page'];
		if ( $offset > 0 ) {
			$limits = 'LIMIT ' . $offset . ',' . $args['per_page'];
		} else {
			$limits = 'LIMIT ' . $args['per_page'];
		}
	}
	
	$sql = "SELECT * FROM " . POST_LOCATION_TABLE . " WHERE 1=1 " . $where . " ORDER BY city, region, country ASC " . $limits;

	$items = $wpdb->get_results( $sql );
	$result = array();
	$result['items'] = $items;
	$result['total_items'] = $total_items;
	$result['total_pages'] = $total_pages;
	$result['pagenum'] = $pagenum;	
	$result['pagination'] = geodir_location_admin_pagination( $args );
	$result['pagination_top'] = geodir_location_admin_pagination( $args, 'top' );
	$result['filter_box'] = geodir_location_admin_search_box( __( 'Filter', 'geodirlocation' ), 'location' );

	return $result;
}

/**
 * Admin location pagination.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param array $args Pagination arguments.
 * @param string $which Pagination position.
 * @return string Pagination HTML.
 */
function geodir_location_admin_pagination( $args, $which = 'bottom' ) {
	if ( empty( $args ) || empty( $args['total_items'] ) ) {
		return;
	}

	$total_items = $args['total_items'];
	$total_pages = $args['total_pages'];
	$infinite_scroll = false;
	if ( isset( $args['infinite_scroll'] ) ) {
		$infinite_scroll = $args['infinite_scroll'];
	}

	$output = '<span class="displaying-num">' . sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

	$current = $args['pagenum'];

	$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

	$current_url = esc_url( remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first', 'location_success' ), $current_url ), '', '' );

	$page_links = array();

	$disable_first = $disable_last = '';
	if ( $current == 1 ) {
		$disable_first = ' disabled';
	}
	if ( $current == $total_pages ) {
		$disable_last = ' disabled';
	}
	$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
		'first-page' . $disable_first,
		esc_attr__( 'Go to the first page', 'geodirlocation' ),
		esc_url( remove_query_arg( 'paged', $current_url ) ),
		'&laquo;'
	);

	$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
		'prev-page' . $disable_first,
		esc_attr__( 'Go to the previous page', 'geodirlocation' ),
		esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
		'&lsaquo;'
	);

	$html_current_page = $current;
	
	$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
	$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . '</span>';

	$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
		'next-page' . $disable_last,
		esc_attr__( 'Go to the next page', 'geodirlocation' ),
		esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
		'&rsaquo;'
	);

	$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
		'last-page' . $disable_last,
		esc_attr__( 'Go to the last page', 'geodirlocation' ),
		esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
		'&raquo;'
	);

	$pagination_links_class = 'pagination-links';
	if ( ! empty( $infinite_scroll ) ) {
		$pagination_links_class = ' hide-if-js';
	}
	$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

	if ( $total_pages ) {
		$page_class = $total_pages < 2 ? ' one-page' : '';
	} else {
		$page_class = ' no-pages';
	}
	$pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

	return $pagination;
}

/**
 * 'Manage location' page search form.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $text Submit button text.
 * @param string $text_input_id HTML id for input box.
 * @return string search form HTML.
 */
function geodir_location_admin_search_box( $text, $text_input_id ) {
	$input_id = $text_input_id . '-search-input';
	$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );
	$current_url_search = esc_url( remove_query_arg( array( 's', 'country', 'paged' ), $current_url ), '', '' );
	$current_url = esc_url( $current_url);
	
	$countries = geodir_post_location_countries( true );
	$country = isset( $_REQUEST['country'] ) ? wp_unslash( trim( $_REQUEST['country'] ) ) : '';
	
	ob_start();
	?>
	<label class="screen-reader-text" for="geodir_country"><?php echo __( 'Select Country', 'geodirlocation' ); ?></label>
	<select id="geodir_country" name="geodir_country">
		<option style="color:#888888" value=""><?php echo __( 'Country', 'geodirlocation' ); ?></option>
		<?php if ( !empty( $countries ) ) { ?>
			<?php foreach ( $countries as $country_slug => $country_text ) { ?>
				<option value="<?php echo $country_slug; ?>" <?php echo ( $country_slug == $country ? 'selected="selected"' : '' ); ?>><?php echo $country_text; ?></option>
			<?php } ?>
		<?php } ?>
	</select>
	<input type="search" onkeypress="return geodir_filter_location(event)" id="<?php echo $input_id ?>" placeholder="<?php echo esc_attr__( 'City or Region', 'geodirlocation' ); ?>" name="s" value="<?php _admin_search_query(); ?>" />&nbsp;&nbsp;<input type="button" value="<?php echo $text; ?>" class="button" id="<?php echo $text_input_id . '-search-submit'; ?>" name="<?php echo $text_input_id . '_search_submit'; ?>" onclick="return geodir_filter_location()" />&nbsp;&nbsp;<input type="button" value="<?php _e( 'Reset', 'geodirlocation' ); ?>" class="button" id="<?php echo $text_input_id . '-search-reset'; ?>" name="<?php echo $text_input_id . '_search_reset'; ?>" onclick="jQuery('#geodir_country').val('');jQuery('#location-search-input').val('');return geodir_filter_location();" /><input type="hidden" id="gd_location_page_url" value="<?php echo $current_url;?>" /><input type="hidden" id="gd_location_bulk_url" value="<?php echo esc_url( admin_url().'admin-ajax.php?action=geodir_locationajax_action&location_ajax_action=delete&_wpnonce=' . wp_create_nonce( 'location_action_bulk_delete' ) ); ?>" />
	<script type="text/javascript"> function geodir_filter_location(e) { 
	if( typeof e=='undefined' || ( typeof e!='undefined' && e.keyCode == '13' ) ) { if( typeof e!='undefined' ) { e.preventDefault(); } window.location.href = '<?php echo $current_url_search;?>&s='+jQuery('#location-search-input').val()+'&country='+jQuery('#geodir_country').val(); } } </script>
	<?php 
	$content = ob_get_clean();
	
	return $content;
}

add_filter('geodir_get_full_location','geodir_location_get_full_location',10,1);
function geodir_location_get_full_location($location){


    return $location;
}


$geodir_location_geo_home_link = '';
add_filter( 'home_url', 'geodir_location_geo_home_link',100000,2 );
function geodir_location_geo_home_link( $url, $path){
if(is_admin()){return $url;}
    // If direct home path then we edit it.
    global $post;
    if((!$path || $path=='/') && get_option('geodir_home_go_to','location')=='location' && isset($post->ID)){

        $what_is_current_location = geodir_what_is_current_location();
        if($what_is_current_location) {
            global $geodir_location_geo_home_link;
            if ($geodir_location_geo_home_link){return $geodir_location_geo_home_link;}

            
            $country_val = geodir_get_current_location(array('what' => 'country', 'echo' => false));
            $region_val = geodir_get_current_location(array('what' => 'region', 'echo' => false));
            $city_val = geodir_get_current_location(array('what' => 'city', 'echo' => false));


            if ($what_is_current_location == 'country' && $country_val != '') {
                $args_current_location = array(
                    'what' => $what_is_current_location,
                    'country_val' => $country_val,
                    'compare_operator' => '=',
                    'no_of_records' => '1',
                    'echo' => false,
                    'format' => array('type' => 'array')
                );
                $current_location_array = geodir_get_location_array($args_current_location);
            }

            if ($what_is_current_location == 'region' && $region_val != '') {
                $args_current_location = array(
                    'what' => $what_is_current_location,
                    'country_val' => $country_val,
                    'region_val' => $region_val,
                    'compare_operator' => '=',
                    'no_of_records' => '1',
                    'echo' => false,
                    'format' => array('type' => 'array')
                );

                $current_location_array = geodir_get_location_array($args_current_location);
            }
            if ($what_is_current_location == 'city' && $city_val != '') {
                $args_current_location = array(
                    'what' => $what_is_current_location,
                    'country_val' => $country_val,
                    'region_val' => $region_val,
                    'city_val' => $city_val,
                    'compare_operator' => '=',
                    'no_of_records' => '1',
                    'echo' => false,
                    'format' => array('type' => 'array')
                );
                $current_location_array = geodir_get_location_array($args_current_location);

            }

            $current_location = $current_location_array[0];
            if(isset($current_location_array[0])) {
                $gd_page_id = geodir_location_page_id();
                $pst = get_post($gd_page_id);
                $slug = $pst->post_name;
                remove_filter('home_url', 'geodir_location_geo_home_link', 100000);
                $base_url = get_bloginfo('url');
                add_filter('home_url', 'geodir_location_geo_home_link', 100000, 2);
                $base_location = trailingslashit($base_url)."$slug/";
                $geodir_location_geo_home_link = geodir_location_permalink_url($base_location . $current_location->location_link);
                return $geodir_location_geo_home_link;
            }
        }
    }


    return $url;

}

/**
 * Get location list for manager location page.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param array $args Location list query args.
 * @return array Location array.
 */
function geodir_location_seo_settings_list($args = array()) {
	$per_page = isset( $_REQUEST['per_page'] ) ? absint( $_REQUEST['per_page'] ) : 0;
	$search = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
	$country = isset( $_REQUEST['country'] ) ? wp_unslash( trim( $_REQUEST['country'] ) ) : '';
	$per_page = $per_page > 0 ? $per_page : 10;
	
	$pagination_args = wp_parse_args( 
										$args, 
										array(
											'per_page' => $per_page,
											'search' => $search,
											'country' => $country,
										)
									);
	$rows = geodir_location_seo_get_locations($pagination_args);
	return $rows;
}

/**
 * Get locations using given arguments.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param array $args Query args.
 * @return array Location array.
 */
function geodir_location_seo_get_locations($args = array()) {
	global $wpdb;
	
	$location_type = !empty($args['location_type']) && in_array($args['location_type'], array('country', 'region', 'city')) ? $args['location_type'] : 'country';

	$where = '';
	$orderby = '';
	$distinct_field = 'location_id';
	
	if ( !empty( $args['search'] ) && $args['search'] != '' ) {
		if ($location_type == 'region') {
			$where .= "AND ( region LIKE '" . wp_slash( $args['search'] ) . "%' OR region LIKE '% " . wp_slash( $args['search'] ) . "%' ) ";
		} else if ($location_type == 'city') {
			$where .= "AND ( city LIKE '" . wp_slash( $args['search'] ) . "%' OR city LIKE '% " . wp_slash( $args['search'] ) . "%' OR region LIKE '" . wp_slash( $args['search'] ) . "%' OR region LIKE '% " . wp_slash( $args['search'] ) . "%' ) ";
		}
	}
	
	if ( !empty( $args['country'] ) && $args['country'] != '' ) {
		$where .= "AND ( country LIKE '" . wp_slash( $args['country'] ) . "' OR country_slug LIKE '" . wp_slash( $args['country'] ) . "' ) ";
	}
	
	switch ($location_type) {
		case 'city':
			$distinct_field = 'CONCAT(city_slug, "_", region_slug, "_", country_slug)';
			$orderby = 'city, region, country ASC';
		break;
		case 'region':
			$distinct_field = 'CONCAT(region_slug, "_", country_slug)';
			$orderby = 'region, country ASC';
		break;
		default:
		case 'country':
			$distinct_field = 'country_slug';
			$orderby = 'country ASC';
		break;
	}
	$groupby = ' GROUP BY ' . $distinct_field;
	$orderby = ' ORDER BY ' . $orderby . ', location_id ASC';
	
	$sql = "SELECT COUNT(DISTINCT " . $distinct_field . ") FROM " . POST_LOCATION_TABLE . " WHERE 1=1 " . $where;
	$total_items = $wpdb->get_var( $sql );
	
	if ( !empty( $args['count'] ) ) {
		return $total_items;
	}
	
	$total_pages = ( $total_items > 0 && isset( $args['per_page'] ) && $args['per_page'] > 0 ) ? ceil( $total_items / $args['per_page'] ) : 0;
	$args['total_pages'] = $total_pages;
	
	$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;
	
	if ( isset( $args['total_pages'] ) && $pagenum > $args['total_pages'] ) {
		$pagenum = $args['total_pages'];
	}
	
	$pagenum = max( 1, $pagenum );
	$args['total_items'] = $total_items;
	$args['pagenum'] = $pagenum;
	
	$limits = '';
	if ( isset( $args['per_page'] ) && $args['per_page'] > 0 ) {
		$offset = ( $pagenum - 1 ) * $args['per_page'];
		if ( $offset > 0 ) {
			$limits = ' LIMIT ' . $offset . ',' . $args['per_page'];
		} else {
			$limits = ' LIMIT ' . $args['per_page'];
		}
	}
	
	$sql = "SELECT * FROM " . POST_LOCATION_TABLE . " WHERE 1=1 " . $where . $groupby . $orderby . $limits;
	
	$items = $wpdb->get_results( $sql );
	$result = array();
	$result['items'] = $items;
	$result['total_items'] = $total_items;
	$result['total_pages'] = $total_pages;
	$result['pagenum'] = $pagenum;	
	$result['pagination'] = geodir_location_admin_pagination( $args );
	$result['pagination_top'] = geodir_location_admin_pagination( $args, 'top' );
	$result['filter_box'] = geodir_location_seo_settings_search_box(__( 'Filter', 'geodirlocation' ), 'location', $location_type);

	return $result;
}

/**
 * 'Seo settings' page search form.
 *
 * @since 1.4.2
 * @package GeoDirectory_Location_Manager
 *
 * @param string $text Submit button text.
 * @param string $text_input_id HTML id for input box.
 * @return string search form HTML.
 */
function geodir_location_seo_settings_search_box($text, $text_input_id, $location_type = 'country') {
	$input_id = $text_input_id . '-search-input';
	$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first', 'location_success' ), $current_url );
	$current_url_loctype = esc_url( remove_query_arg( array( 's', 'country', 'paged', 'gd_loc' ), $current_url ), '', '' );
		
	ob_start();
	?>
	<select id="gd_loc" name="gd_loc" onchange="window.location.href='<?php echo $current_url_loctype;?>&gd_loc=' + this.value;">
		<option value="country" <?php selected('country', $location_type);?>><?php _e('Countries SEO', 'geodirlocation'); ?> </option>
		<option value="region" <?php selected('region', $location_type);?>><?php _e('Regions SEO', 'geodirlocation'); ?> </option>
		<option value="city" <?php selected('city', $location_type);?>><?php _e('Cities SEO', 'geodirlocation'); ?> </option>
	</select>
	<?php if ($location_type != 'country') {
		$current_url_search = esc_url( remove_query_arg( array( 's', 'country', 'paged' ), $current_url ), '', '' );
		$current_url = esc_url( $current_url);
		$countries = geodir_post_location_countries( true );
		$country = isset( $_REQUEST['country'] ) ? wp_unslash( trim( $_REQUEST['country'] ) ) : '';
		
		$placeholder = $location_type == 'region' ? __( 'Region', 'geodirlocation' ) : __( 'City or Region', 'geodirlocation' );
	?>
	<label class="screen-reader-text" for="geodir_country"><?php echo __( 'Select Country', 'geodirlocation' ); ?></label>
	<select id="geodir_country" name="geodir_country">
		<option style="color:#888888" value=""><?php echo __( 'Country', 'geodirlocation' ); ?></option>
		<?php if ( !empty( $countries ) ) { ?>
			<?php foreach ( $countries as $country_slug => $country_text ) { ?>
				<option value="<?php echo $country_slug; ?>" <?php echo ( $country_slug == $country ? 'selected="selected"' : '' ); ?>><?php echo $country_text; ?></option>
			<?php } ?>
		<?php } ?>
	</select>
	<input type="search" onkeypress="return geodir_filter_location_seo(event)" id="<?php echo $input_id ?>" placeholder="<?php echo esc_attr($placeholder); ?>" name="s" value="<?php _admin_search_query(); ?>" />&nbsp;&nbsp;<input type="button" value="<?php echo $text; ?>" class="button" id="<?php echo $text_input_id . '-search-submit'; ?>" name="<?php echo $text_input_id . '_search_submit'; ?>" onclick="return geodir_filter_location_seo()" />&nbsp;&nbsp;<input type="button" value="<?php _e( 'Reset', 'geodirlocation' ); ?>" class="button" id="<?php echo $text_input_id . '-search-reset'; ?>" name="<?php echo $text_input_id . '_search_reset'; ?>" onclick="jQuery('#geodir_country').val('');jQuery('#location-search-input').val('');return geodir_filter_location_seo();" /><input type="hidden" id="gd_location_page_url" value="<?php echo $current_url;?>" /><input type="hidden" id="gd_location_bulk_url" value="<?php echo esc_url( admin_url().'admin-ajax.php?action=geodir_locationajax_action&location_ajax_action=delete&_wpnonce=' . wp_create_nonce( 'location_action_bulk_delete' ) ); ?>" />
	<script type="text/javascript"> function geodir_filter_location_seo(e) { 
	if( typeof e=='undefined' || ( typeof e!='undefined' && e.keyCode == '13' ) ) { if( typeof e!='undefined' ) { e.preventDefault(); } window.location.href = '<?php echo $current_url_search;?>&s='+jQuery('#location-search-input').val()+'&country='+jQuery('#geodir_country').val(); } } </script>
	<?php }
	$content = ob_get_clean();
	
	return $content;
}

/**
 * Handles location SEO settings form data.
 *
 * @since 1.4.2
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function geodir_set_location_seo_settings() {
	global $wpdb;
	
	if (isset($_POST['_wpnonce']) && current_user_can('manage_options') && !empty($_POST['gd_seo']) && !empty($_POST['gd_loc'])) {
		if (!wp_verify_nonce($_POST['_wpnonce'], 'geodir-settings')) {
			echo 'FAIL';
			exit;
		}

		$location_type = $_POST['gd_loc'];
		$gd_seo = $_POST['gd_seo'];
		$files = $_FILES['gd_seo'];

		if (in_array($location_type, array('country', 'region', 'city'))) {
			foreach ($gd_seo as $key => $data) {
				$location_slug = isset($data['location_slug']) ? $data['location_slug'] : '';
				$region_slug = isset($data['region_slug']) ? $data['region_slug'] : '';
				$country_slug = isset($data['country_slug']) ? $data['country_slug'] : '';
				$meta_desc = isset($data['meta_desc']) ? sanitize_text_field($data['meta_desc']) : '';
				$loc_desc = isset($data['loc_desc']) ? $data['loc_desc'] : '';
				$loc_tagline = isset($data['loc_tagline']) ? $data['loc_tagline'] : '';

				$image_id = null;
				
				if (strlen($meta_desc) > 140) {
					$meta_desc = mb_substr($meta_desc, 0, 140);
				}
				if (strlen($loc_desc) > 100000) {
					$loc_desc = mb_substr($loc_desc, 0, 100000);
				}
				if (strlen($loc_tagline) > 100000) {
					$loc_tagline = mb_substr($loc_tagline, 0, 100000);
				}
				
				if (!$location_slug)
					continue;

				if ($files['name'][$key]) {
					$file = array(
							'name' => $files['name'][$key],
							'type' => $files['type'][$key],
							'tmp_name' => $files['tmp_name'][$key],
							'error' => $files['error'][$key],
							'size' => $files['size'][$key]
					);
					$_FILES = array ("gd_seo" => $file);
					foreach ($_FILES as $file => $array) {
						$image_id = geodir_handle_attachment($file, 0);
					}
				}
				
				$current_date = date_i18n('Y-m-d H:i:s', current_time('timestamp'));
				$seo_info = geodir_location_seo_by_slug($location_slug, $location_type, $country_slug, $region_slug);
				
				$seo_data = array();
				$seo_data['location_type'] = $location_type;
				$seo_data['country_slug'] = $country_slug;
				$seo_data['region_slug'] = '';
				$seo_data['city_slug'] = '';
				$seo_data['seo_title'] = $meta_desc;
				$seo_data['seo_desc'] = $loc_desc;
				if ($image_id) {
					$seo_data['seo_image'] = $image_id;
				}
				$seo_data['seo_image_tagline'] = $loc_tagline;
				
				if ($location_type == 'country') {
					$seo_data['country_slug'] = $location_slug;
				} elseif ($location_type == 'region') {
					$seo_data['region_slug'] = $location_slug;
				} else if ($location_type == 'city') {
					$seo_data['region_slug'] = $region_slug;
					$seo_data['city_slug'] = $location_slug;
					
					$city_data = array();
					$city_data['city_meta'] = $meta_desc;
					$city_data['city_desc'] = $loc_desc;
					
					$wpdb->update(POST_LOCATION_TABLE, $city_data, array('country_slug' => $country_slug, 'region_slug' => $region_slug, 'city_slug' => $location_slug));
				}
				
				if (!empty($seo_info)) {
					$seo_data['date_updated'] = $current_date;
					
					$wpdb->update(LOCATION_SEO_TABLE, $seo_data, array('seo_id' => $seo_info->seo_id));
				} else {
					$seo_data['date_created'] = $current_date;
					
					$wpdb->insert(LOCATION_SEO_TABLE, $seo_data);
				}
			}
		}
		
		$msg = urlencode(__('Location SEO updated successfully.','geodirlocation'));
		
		$wp_redirect = $_POST['_wp_http_referer'];
		$wp_redirect = remove_query_arg(array('location_success'), $wp_redirect);
		$wp_redirect = add_query_arg(array('location_success' => $msg), $wp_redirect);
		
		wp_redirect($wp_redirect);
		gd_die();
	}
}

function gd_seo_remove_image() {
	global $wpdb;

	if (isset($_GET['gd_loc_nonce']) && wp_verify_nonce($_GET['gd_loc_nonce'], 'gd_seo_image_remove')) {

		$seo_id = $_GET['seo_id'];
		$seo_data = array();
		$seo_data['seo_image'] = '';

		$wpdb->update(LOCATION_SEO_TABLE, $seo_data, array('seo_id' => $seo_id));
		$msg = urlencode(__('Location SEO image removed successfully.','geodirlocation'));

		$wp_redirect = wp_get_referer();
		$wp_redirect = remove_query_arg(array('location_success'), $wp_redirect);
		$wp_redirect = add_query_arg(array('location_success' => $msg), $wp_redirect);

		wp_redirect($wp_redirect);
		exit;
	}
}
add_action('init', 'gd_seo_remove_image');

function geodir_handle_attachment($file_handler, $post_id) {
// check to make sure its a successful upload
	if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');

	$attach_id = media_handle_upload( $file_handler, $post_id );
	if ( is_numeric( $attach_id ) ) {
		return $attach_id;
	}
}

function geodir_sitemap_page($install_check = false, $check_location_sitemap = false) {
	if(!function_exists('wpseo_init')) {
		return false;
	}
	
	if ($install_check) {
		return true;
	}
	
	if (!isset($_SERVER['REQUEST_URI'])) {
		return false;
	}
	
	$request_uri = $_SERVER['REQUEST_URI'];
	$extension   = substr($request_uri, -4);

	$return = false;
	if (false !== stripos($request_uri, 'sitemap') && (in_array($extension, array('.xml', '.xsl')))) {
		$return = true;
	}

	if ($return && $check_location_sitemap && (!in_array(basename($request_uri), array('sitemap_index.xml', 'sitemap_index.xsl'))) ) {
		if (false !== stripos($request_uri, '_location_')) {
			$return = true;
		} else {
			$return = false;
		}
	}
	
	return $return;
}

// Functions
function geodir_sitemap_init() {
	if (!geodir_sitemap_page(false, true)) {
		return;
	}

	add_filter('wpseo_enable_xml_sitemap_transient_caching', '__return_false');
	
	$sitemap_exclude_location = get_option('gd_location_sitemap_exclude_location');
	$sitemap_exclude_cats = get_option('gd_location_sitemap_exclude_cats');
	$sitemap_exclude_tags = get_option('gd_location_sitemap_exclude_tags');
	if ($sitemap_exclude_location && $sitemap_exclude_cats && $sitemap_exclude_tags) {
		return;
	}
	
	// try to set higher limits for import
    $max_input_time = ini_get('max_input_time');
    $max_execution_time = ini_get('max_execution_time');
    $memory_limit= ini_get('memory_limit');

    if(!$max_input_time || $max_input_time<3000){
        try {
            ini_set('max_input_time', 3000);
        } catch(Exception $e) {
            // Error
        }
    }

    if(!$max_execution_time || $max_execution_time<3000){
        try {
            ini_set('max_execution_time', 3000);
        } catch(Exception $e) {
            // Error
        }
    }

    if($memory_limit && str_replace('M','',$memory_limit)){
        if(str_replace('M','',$memory_limit)<512){
            try {
                ini_set('memory_limit', '512M');
            } catch(Exception $e) {
                // Error
            }
        }
    }
	
	global $wpseo_sitemaps, $gd_wpseo_options, $gd_wpseo_timezone, $gd_wpseo_max_entries, $gd_wpseo_location_type, $gd_wpseo_taxonomies, $gd_taxonomies_data;

	$gd_wpseo_options = WPSEO_Options::get_all();
	$gd_wpseo_timezone = new WPSEO_Sitemap_Timezone();
	$gd_wpseo_max_entries = $gd_wpseo_options['entries-per-page'];
	$gd_wpseo_taxonomies = geodir_sitemap_get_taxonomies();
	
	$gd_wpseo_location_type = geodir_sitemap_get_location_type();

	switch ($gd_wpseo_location_type) {
		case 'country_city':
			if (!$sitemap_exclude_location) {
				$wpseo_sitemaps->register_sitemap( 'gd_location_country', 'geodir_sitemap_gd_location_country' );
				$wpseo_sitemaps->register_sitemap( 'gd_location_country_city', 'geodir_sitemap_gd_location_country_city' );
			}
			
			if (!empty($gd_wpseo_taxonomies)) {
				geodir_sitemap_load_sitemap_data('country');
				geodir_sitemap_load_sitemap_data('country_city');
			}
		break;
		case 'region_city':
			if (!$sitemap_exclude_location) {
				$wpseo_sitemaps->register_sitemap( 'gd_location_region', 'geodir_sitemap_gd_location_region' );
				$wpseo_sitemaps->register_sitemap( 'gd_location_region_city', 'geodir_sitemap_gd_location_region_city' );
			}
			
			if (!empty($gd_wpseo_taxonomies)) {
				geodir_sitemap_load_sitemap_data('region');
				geodir_sitemap_load_sitemap_data('region_city');
			}
		break;
		case 'city':
			if (!$sitemap_exclude_location) {
				$wpseo_sitemaps->register_sitemap( 'gd_location_city', 'geodir_sitemap_gd_location_city' );
			}
			
			if (!empty($gd_wpseo_taxonomies)) {
				geodir_sitemap_load_sitemap_data('city');
			}
		break;
		default:			
			if (!$sitemap_exclude_location) {
				$wpseo_sitemaps->register_sitemap( 'gd_location_country', 'geodir_sitemap_gd_location_country' );
				$wpseo_sitemaps->register_sitemap( 'gd_location_country_region', 'geodir_sitemap_gd_location_country_region' );
				$wpseo_sitemaps->register_sitemap( 'gd_location_full', 'geodir_sitemap_gd_location_full' );
			}
			
			if (!empty($gd_wpseo_taxonomies)) {
				geodir_sitemap_load_sitemap_data('country');
				geodir_sitemap_load_sitemap_data('country_region');
				geodir_sitemap_load_sitemap_data('full');
			}
		break;
	}
	
	if (!empty($gd_wpseo_taxonomies) && !empty($gd_taxonomies_data)) {
		foreach ($gd_taxonomies_data as $location_type => $location_data) {
			if (!empty($location_data)) {
				foreach ($location_data as $taxonomy => $taxonomy_data) {
					if (!empty($taxonomy_data)) {
						foreach ($taxonomy_data as $taxonomy_name => $ids) {
							if (!empty($ids['rows'])) {
								$wpseo_sitemaps->register_sitemap( $taxonomy_name . '_location_' . $location_type, 'geodir_sitemap_taxonomy_location' );
							}
						}
					}
				}
			}
		}
	}
}

function geodir_sitemap_index($sitemap = '') {
	if (!geodir_sitemap_page()) {
		return $sitemap;
	}
	
	$sitemap_exclude_location = get_option('gd_location_sitemap_exclude_location');
	$sitemap_exclude_cats = get_option('gd_location_sitemap_exclude_cats');
	$sitemap_exclude_tags = get_option('gd_location_sitemap_exclude_tags');
	if ($sitemap_exclude_location && $sitemap_exclude_cats && $sitemap_exclude_tags) {
		return $sitemap;
	}
	global $gd_wpseo_location_type;
	
	$sitemap_taxonomies = array();
	
	switch ($gd_wpseo_location_type) {
		case 'country_city':
			if (!$sitemap_exclude_location) {
				$sitemap .= geodir_sitemap_location_sitemap_index('country');
				$sitemap .= geodir_sitemap_location_sitemap_index('country_city');
			}
			
			$sitemap_taxonomies['country'] = geodir_sitemap_taxonomies_sitemap_index('country');
			$sitemap_taxonomies['country_city'] = geodir_sitemap_taxonomies_sitemap_index('country_city');
		break;
		case 'region_city':
			if (!$sitemap_exclude_location) {
				$sitemap .= geodir_sitemap_location_sitemap_index('region');
				$sitemap .= geodir_sitemap_location_sitemap_index('region_city');
			}
			
			$sitemap_taxonomies['region'] = geodir_sitemap_taxonomies_sitemap_index('region');
			$sitemap_taxonomies['region_city'] = geodir_sitemap_taxonomies_sitemap_index('region_city');
		break;
		case 'city':
			if (!$sitemap_exclude_location) {
				$sitemap .= geodir_sitemap_location_sitemap_index('city');
			}
			
			$sitemap_taxonomies['city'] = geodir_sitemap_taxonomies_sitemap_index('city');
		break;
		default:
			if (!$sitemap_exclude_location) {
				$sitemap .= geodir_sitemap_location_sitemap_index('country');
				$sitemap .= geodir_sitemap_location_sitemap_index('country_region');
				$sitemap .= geodir_sitemap_location_sitemap_index('full');
			}
			
			$sitemap_taxonomies['country'] = geodir_sitemap_taxonomies_sitemap_index('country');
			$sitemap_taxonomies['country_region'] = geodir_sitemap_taxonomies_sitemap_index('country_region');
			$sitemap_taxonomies['full'] = geodir_sitemap_taxonomies_sitemap_index('full');
		break;
	}
		
	if (!empty($sitemap_taxonomies)) {
		$sitemap_category = '';
		$sitemap_tag = '';

		foreach ($sitemap_taxonomies as $key => $sitemap_taxonomy) {			
			if (is_array($sitemap_taxonomy) && !empty($sitemap_taxonomy)) {
				if (isset($sitemap_taxonomy['category']) && !empty($sitemap_taxonomy['category'])) {
					$sitemap_category .= $sitemap_taxonomy['category'];
				}
				
				if (isset($sitemap_taxonomy['tag']) && !empty($sitemap_taxonomy['tag'])) {
					$sitemap_tag .= $sitemap_taxonomy['tag'];
				}
			}
		}

		if (!empty($sitemap_category)) {
			$sitemap .= $sitemap_category;
		}
		
		if (!empty($sitemap_tag)) {
			$sitemap .= $sitemap_tag;
		}
	}
	
	return $sitemap;
}

function geodir_sitemap_location_sitemap_index($location_type) {
	global $gd_wpseo_timezone, $gd_wpseo_max_entries, $gd_wpseo_location_type;
	
    if (version_compare(WPSEO_VERSION, 3.2, '<')) {
        $date = $gd_wpseo_timezone->get_datetime_with_timezone(date_i18n('Y-m-d H:i', time(), true));
    } else {
        $date = $gd_wpseo_timezone->format_date(date_i18n('Y-m-d H:i', time(), true));
    }
	
	$args = array();
	$args['what'] = $location_type;
	$args['count_only'] = true;
	
	switch ($location_type) {
		case 'country_region':
		case 'region':
			$args['group_by'] = "CONCAT(country_slug, '-', region_slug)";
		break;
		case 'country_city':
			$args['group_by'] = "CONCAT(country_slug, '-', city_slug)";
		break;
		case 'region_city':
			$args['group_by'] = "CONCAT(region_slug, '-', city_slug)";
		break;
		case 'country':
			$args['group_by'] = "country_slug";
		break;
		case 'city':
		case 'full':
			$args['group_by'] = "CONCAT(country_slug, '-', region_slug, '-', city_slug)";
		break;
	}
	$count = (int)geodir_sitemap_get_locations_array($args);
	global $wpdb;
	if (!empty($wpdb->queries)) {
		$wpdb->queries = array();
	}

	if (!$count > 0) {
		return;
	}
	
	$n = ( $count > $gd_wpseo_max_entries ) ? (int)ceil( $count / $gd_wpseo_max_entries ) : 1;
	
	$sitemap = '';
	for ( $i = 0; $i < $n; $i++ ) {
		$count = ( $n > 1 ) ? ( $i + 1 ) : '';
		
		$sitemap .= "\t<sitemap>\n";
		$sitemap .= "\t\t<loc>" . wpseo_xml_sitemaps_base_url( "gd_location_" . $location_type . "-sitemap" . $count . ".xml" ) . "</loc>\n";
		$sitemap .= "\t\t<lastmod>" . htmlspecialchars( $date ) . "</lastmod>\n";
		$sitemap .= "\t</sitemap>\n";
	}
	
	unset( $count, $n, $i, $date );
	
	return $sitemap;
}

function geodir_sitemap_get_taxonomies() {
	global $gd_wpseo_options;
	
	$gd_post_types = geodir_get_posttypes();
		
	$category = array();
	$tag = array();
		
	foreach ($gd_post_types as $gd_post_type) {
		$location_allowed = function_exists( 'geodir_cpt_no_location' ) && geodir_cpt_no_location($gd_post_type) ? false : true;
		if (!$location_allowed) {
			continue;
		}
		
		$gd_cat_taxonomy = $gd_post_type . 'category';
		$gd_tag_taxonomy = $gd_post_type . '_tags';
		
		$include_cat = true;
		if (apply_filters('wpseo_sitemap_exclude_taxonomy', false, $gd_cat_taxonomy)) {
			$include_cat = false;
		}

		if (isset($gd_wpseo_options['taxonomies-' . $gd_cat_taxonomy . '-not_in_sitemap']) && $gd_wpseo_options['taxonomies-' . $gd_cat_taxonomy . '-not_in_sitemap'] === true) {
			$include_cat = false;
		}
		
		$include_tag = true;
		if (apply_filters('wpseo_sitemap_exclude_taxonomy', false, $gd_tag_taxonomy)) {
			$include_tag = false;
		}

		if (isset($gd_wpseo_options['taxonomies-' . $gd_tag_taxonomy . '-not_in_sitemap'] ) && $gd_wpseo_options['taxonomies-' . $gd_tag_taxonomy . '-not_in_sitemap'] === true) {
			$include_tag = false;
		}
		
		if ($include_cat) {
			$category[] = $gd_post_type;
		}
		
		if ($include_tag) {
			$tag[] = $gd_post_type;
		}
	}
	
	$taxonomies = array();
	if (!get_option('gd_location_sitemap_exclude_cats') && !empty($category)) {
		$taxonomies['category'] = $category;
	}
	if (!get_option('gd_location_sitemap_exclude_tags') && !empty($tag)) {
		$taxonomies['tag'] = $tag;
	}
	
	return $taxonomies;
}

function geodir_sitemap_load_sitemap_data($location_type) {
	global $wpdb, $gd_wpseo_timezone, $gd_wpseo_max_entries, $gd_wpseo_location_type, $gd_wpseo_taxonomies, $gd_taxonomies_data;
	
	$gd_post_types = geodir_get_posttypes();
	
	$gd_cat_taxonomies = !empty($gd_wpseo_taxonomies) && isset($gd_wpseo_taxonomies['category']) ? $gd_wpseo_taxonomies['category'] : NULL;
	$gd_tag_taxonomies = !empty($gd_wpseo_taxonomies) && isset($gd_wpseo_taxonomies['tag']) ? $gd_wpseo_taxonomies['tag'] : NULL;
	
	$args = array();
	$args['what'] = $location_type;
	$args['fields'] = 'country_slug, region_slug, city_slug';
	
	$post_locations = "[gd_city],[gd_region],[gd_country]";
	
	switch ($location_type) {
		case 'country_region':
		case 'region':
			$args['group_by'] = "CONCAT(country_slug, '-', region_slug)";
			$args['order'] = "region_slug, country_slug";
			$post_locations = "%,[gd_region],[gd_country]";
		break;
		case 'country_city':
			$args['group_by'] = "CONCAT(country_slug, '-', city_slug)";
			$args['order'] = "city_slug, country_slug";
		break;
		case 'region_city':
			$args['group_by'] = "CONCAT(region_slug, '-', city_slug)";
			$args['order'] = "city_slug, region_slug";
			$post_locations = "[gd_city],[gd_region],%";
		break;
		case 'country':
			$args['order'] = "country_slug";
			$args['group_by'] = "country_slug";
			$post_locations = "%,[gd_country]";
		break;
		case 'city':
			$args['what'] = 'city';
			$args['group_by'] = "CONCAT(country_slug, '-', region_slug, '-', city_slug)";
			$args['order'] = "city_slug, region_slug, country_slug";
		break;
		case 'full':
			$args['what'] = 'full';
			$args['group_by'] = "CONCAT(country_slug, '-', region_slug, '-', city_slug)";
			$args['order'] = "city_slug, region_slug, country_slug";
		break;
	}
		
	$locations = geodir_sitemap_get_locations_array($args);
	if (!empty($wpdb->queries)) {
		$wpdb->queries = array();
	}
	
	$location_type_ids = array();
	
	$cat_ids = array();
	$tag_ids = array();
	if (!empty($locations)) {
		foreach ($locations as $location) {
			if (!empty($gd_cat_taxonomies)) {
				foreach ($gd_cat_taxonomies as $gd_post_type) {
					$taxonomy = $gd_post_type . 'category';
					$table = $wpdb->prefix . "geodir_" . $gd_post_type . "_detail";
					
					if (!isset($cat_ids[$taxonomy])) {
						$cat_ids[$taxonomy] = array();
					}
					
					if (!isset($cat_ids[$taxonomy]['count'])) {
						$cat_ids[$taxonomy]['count'] = 0;
					}
					
					if (!isset($cat_ids[$taxonomy]['rows'])) {
						$cat_ids[$taxonomy]['rows'] = array();
					}
					
					$match_locations = str_replace('gd_country', $location->country_slug, $post_locations);
					$match_locations = str_replace('gd_region', $location->region_slug, $match_locations);
					$match_locations = str_replace('gd_city', $location->city_slug, $match_locations);
					
					$where = "AND pd.post_locations LIKE '" . $match_locations . "'";
					
					$query = "SELECT t.term_id FROM `" . $wpdb->terms . "` AS t INNER JOIN `" . $wpdb->term_taxonomy . "` AS tt ON t.term_id = tt.term_id LEFT JOIN `" . $wpdb->term_relationships . "` as tr ON tr.term_taxonomy_id = tt.term_taxonomy_id LEFT JOIN `" . $wpdb->posts . "` as p ON p.ID = tr.object_id INNER JOIN `" . $table . "` as pd ON pd.post_id = p.ID WHERE tt.taxonomy = '" . $taxonomy . "' AND p.post_status = 'publish' " . $where . " GROUP BY t.term_id ORDER BY COUNT(t.term_id) DESC";
					
					$results = $wpdb->get_results($query);
					
					$wpdb->flush();
					if (!empty($wpdb->queries)) {
						$wpdb->queries = array();
					}
					if (!empty($results)) {
						$row = array();
						$row['count'] = count($results);
						$row['location'] = $location;
						$row['ids'] = $results;
												
						$cat_ids[$taxonomy]['count'] = $cat_ids[$taxonomy]['count'] + $row['count'];
						$cat_ids[$taxonomy]['rows'][] = $row;
					}
				}
			}
			
			if (!empty($gd_tag_taxonomies)) {				
				foreach ($gd_tag_taxonomies as $gd_post_type) {
					$taxonomy = $gd_post_type . '_tags';
					$table = $wpdb->prefix . "geodir_" . $gd_post_type . "_detail";
					
					if (!isset($tag_ids[$taxonomy])) {
						$tag_ids[$taxonomy] = array();
					}
					
					if (!isset($tag_ids[$taxonomy]['count'])) {
						$tag_ids[$taxonomy]['count'] = 0;
					}
					
					if (!isset($tag_ids[$taxonomy]['rows'])) {
						$tag_ids[$taxonomy]['rows'] = array();
					}
					
					$match_locations = str_replace('gd_country', $location->country_slug, $post_locations);
					$match_locations = str_replace('gd_region', $location->region_slug, $match_locations);
					$match_locations = str_replace('gd_city', $location->city_slug, $match_locations);
					
					$where = "AND pd.post_locations LIKE '" . $match_locations . "'";
					
					$query = "SELECT t.term_id FROM `" . $wpdb->terms . "` AS t INNER JOIN `" . $wpdb->term_taxonomy . "` AS tt ON t.term_id = tt.term_id LEFT JOIN `" . $wpdb->term_relationships . "` as tr ON tr.term_taxonomy_id = tt.term_taxonomy_id LEFT JOIN `" . $wpdb->posts . "` as p ON p.ID = tr.object_id INNER JOIN `" . $table . "` as pd ON pd.post_id = p.ID WHERE tt.taxonomy = '" . $taxonomy . "' AND p.post_status = 'publish' " . $where . " GROUP BY t.term_id ORDER BY COUNT(t.term_id) DESC";
					
					$results = $wpdb->get_results($query);
					
					$wpdb->flush();
					if (!empty($wpdb->queries)) {
						$wpdb->queries = array();
					}
					if (!empty($results)) {
						$row = array();
						$row['count'] = count($results);
						$row['location'] = $location;
						$row['ids'] = $results;
												
						$tag_ids[$taxonomy]['count'] = $tag_ids[$taxonomy]['count'] + $row['count'];
						$tag_ids[$taxonomy]['rows'][] = $row;
					}
				}
			}
		}
	}
	
	$gd_taxonomies_data = (array)$gd_taxonomies_data;
	$gd_taxonomies_data[$location_type]['category'] = $cat_ids;
	$gd_taxonomies_data[$location_type]['tag'] = $tag_ids;

	return $gd_taxonomies_data;
}

function geodir_sitemap_taxonomies_sitemap_index($location_type) {
	global $gd_wpseo_timezone, $gd_wpseo_max_entries, $gd_taxonomies_data;
	
	if (empty($gd_taxonomies_data)) {
		return;
	}
	
	if (!isset($gd_taxonomies_data[$location_type])) {
		return;
	}
	
    if (version_compare(WPSEO_VERSION, 3.2, '<')) {
        $date = $gd_wpseo_timezone->get_datetime_with_timezone(date_i18n('Y-m-d H:i', time(), true));
    } else {
        $date = $gd_wpseo_timezone->format_date(date_i18n('Y-m-d H:i', time(), true));
    }
	
	$cat_ids = isset($gd_taxonomies_data[$location_type]['category']) ? $gd_taxonomies_data[$location_type]['category'] : NULL;
	$tag_ids = isset($gd_taxonomies_data[$location_type]['tag']) ? $gd_taxonomies_data[$location_type]['tag'] : NULL;
	
	$sitemap_category = '';
	if (!empty($cat_ids)) {
		foreach ($cat_ids as $taxonomy => $taxonomy_data) {
			$total = isset($taxonomy_data['count']) ? $taxonomy_data['count'] : NULL;
			
			if ((int)$total > 0) {
				$n = ( $total > $gd_wpseo_max_entries ) ? (int)ceil( $total / $gd_wpseo_max_entries ) : 1;
				
				for ( $i = 0; $i < $n; $i++ ) {
					$count = ( $n > 1 ) ? ( $i + 1 ) : '';
					
					$sitemap_category .= "\t<sitemap>\n";
					$sitemap_category .= "\t\t<loc>" . wpseo_xml_sitemaps_base_url( $taxonomy . "_location_" . $location_type . "-sitemap" . $count . ".xml" ) . "</loc>\n";
					$sitemap_category .= "\t\t<lastmod>" . htmlspecialchars( $date ) . "</lastmod>\n";
					$sitemap_category .= "\t</sitemap>\n";
				}
			}
		}
	}
	
	$sitemap_tag = '';
	if (!empty($tag_ids)) {
		foreach ($tag_ids as $taxonomy => $taxonomy_data) {
			$total = isset($taxonomy_data['count']) ? $taxonomy_data['count'] : NULL;
			
			if ($total > 0) {
				$n = ( $total > $gd_wpseo_max_entries ) ? (int)ceil( $total / $gd_wpseo_max_entries ) : 1;
				
				for ( $i = 0; $i < $n; $i++ ) {
					$count = ( $n > 1 ) ? ( $i + 1 ) : '';
					
					$sitemap_tag .= '<sitemap>' . "\n";
					$sitemap_tag .= '<loc>' . wpseo_xml_sitemaps_base_url( $taxonomy . '_location_' . $location_type . '-sitemap' . $count . '.xml' ) . '</loc>' . "\n";
					$sitemap_tag .= '<lastmod>' . htmlspecialchars( $date ) . '</lastmod>' . "\n";
					$sitemap_tag .= '</sitemap>' . "\n";
				}
			}
		}
	}
	
	$return = array();
	$return['category'] = $sitemap_category;
	$return['tag'] = $sitemap_tag;
	
	return $return;
}

function geodir_sitemap_taxonomy_location() {
	global $wpseo_sitemaps;

	$sitemap = get_query_var('sitemap');
	if (empty($sitemap)) {
		$wpseo_sitemaps->bad_sitemap = true;
		return NULL;
	}
	
	$sitemaps = explode('_location_', $sitemap, 2);
	if (count($sitemaps) != 2) {
		$wpseo_sitemaps->bad_sitemap = true;
		return NULL;		
	}
	
	$taxonomy = $sitemaps[0];
	$location_type = $sitemaps[1];
	
	$sitemap = geodir_sitemap_taxonomy_sitemap_content($sitemap, $location_type, $taxonomy);
	if (empty($sitemap)) {
		$wpseo_sitemaps->bad_sitemap = true;
		return NULL;
	}
	
	$wpseo_sitemaps->set_sitemap($sitemap);
}

function geodir_sitemap_taxonomy_sitemap_content($sitemap_key, $location_type, $taxonomy) {
	global $wp, $wpseo_sitemaps, $gd_wpseo_timezone, $gd_wpseo_max_entries, $gd_taxonomies_data, $gd_session;
	
	if (empty($gd_taxonomies_data) || empty($taxonomy) || empty($location_type)) {
		return NULL;
	}
	
	$taxonomy_type = 'category';
	if (substr($taxonomy, -8) == 'category') {
		$taxonomy_type = 'category';
	} else if (substr($taxonomy, -5) == '_tags') {
		$taxonomy_type = 'tag';
	}

	$taxonomy_ids = NULL;
	$term_ids = NULL;
	if (isset($gd_taxonomies_data[$location_type]) && isset($gd_taxonomies_data[$location_type][$taxonomy_type])) {
		$taxonomy_ids = $gd_taxonomies_data[$location_type][$taxonomy_type];
		
		if (isset($taxonomy_ids[$taxonomy]) && !empty($taxonomy_ids[$taxonomy])) {
			$taxonomy_ids = $taxonomy_ids[$taxonomy];
			
			if (!empty($taxonomy_ids)) {
				if (!empty($taxonomy_ids['rows'])) {
					foreach ($taxonomy_ids['rows'] as $taxonomy_row) {
						if (!empty($taxonomy_row['ids'])) {
							foreach ($taxonomy_row['ids'] as $taxonomy_id) {
								$term_data = array();
								$term_data['term_id'] = (int)$taxonomy_id->term_id;
								$term_data['gd_country'] = $taxonomy_row['location']->country_slug;
								$term_data['gd_region'] = $taxonomy_row['location']->region_slug;
								$term_data['gd_city'] = $taxonomy_row['location']->city_slug;
								
								$term_ids[] = $term_data;
							}
						}
					}
				}
			}
		}
	}
	
	if (empty($term_ids)) {
		return NULL;
	}
		
	$unset_location_terms = array();
	switch ($location_type) {
		case 'country_region':
		case 'region':
			$unset_location_terms = array('gd_city');
		break;
		case 'country_city':
			$unset_location_terms = array('gd_region');
		break;
		case 'region_city':
			$unset_location_terms = array('gd_country');
		break;
		case 'country':
			$unset_location_terms = array('gd_region', 'gd_city');
		break;
		case 'city':
			$unset_location_terms = array('gd_country', 'gd_region');
		break;
	}
	
	$old_wp = $wp;
	$old_session = $gd_session;
	
	$gd_session->set('gd_multi_location', 1);
		
	$permalink_structure = get_option('permalink_structure');
	$n = (int)get_query_var('sitemap_n');
	$slice_start = $n;
	
	if ($slice_start > 0) {
		$slice_start = $slice_start - 1;
	}
	if ( $slice_start < 0 ) {
		$slice_start = 0;
	}

	// Apply 'pagination'.
	$term_ids = array_slice( $term_ids, ( $slice_start * $gd_wpseo_max_entries ), $gd_wpseo_max_entries );
	$output = '';
	foreach ($term_ids as $term_data) {
		$term_id = $term_data['term_id'];
		
		$gd_session->set('gd_country', $term_data['gd_country']);
		$gd_session->set('gd_region', $term_data['gd_region']);
		$gd_session->set('gd_city', $term_data['gd_city']);
		
		$wp->query_vars['gd_country'] = $term_data['gd_country'];
		$wp->query_vars['gd_region'] = $term_data['gd_region'];
		$wp->query_vars['gd_city'] = $term_data['gd_city'];
				
		if (!empty($unset_location_terms)) {
			foreach ($unset_location_terms as $location_term) {
				$gd_session->set($location_term, '');
				$wp->query_vars[$location_term] = '';
			}
		}
	
		$term_link = get_term_link( $term_id, $taxonomy );
		
		$url = array(
			'loc' => $term_link,
			'pri' => 0.6,
			'chf' => geodir_sitemap_filter_frequency( $taxonomy . '_term', 'weekly', $term_link ),
			'mod' => date( 'c', time() ),
		);
		// Use this filter to adjust the entry before it gets added to the sitemap.
		$url = apply_filters( 'wpseo_sitemap_entry', $url, $sitemap_key, $term_data );

		if ( is_array($url) && $url !== array()) {
			$output .= $wpseo_sitemaps->sitemap_url( $url );
		}
	}

	if (!empty($output)) {
		$sitemap = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
		$sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
		$sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		$sitemap .= $output;

		// Filter to allow adding extra URLs, only do this on the first XML sitemap, not on all.
		if ( $n === 1 || $n === 0 ) {
			$sitemap .= apply_filters( 'wpseo_sitemap_' . $sitemap_key . '_content', '' );
		}

		$sitemap .= '</urlset>';
	} else {
		$sitemap = NULL;
	}
	
	global $wp;
	
	$wp = $old_wp;
	$gd_session = $old_session;
	
	return $sitemap;
}

function geodir_sitemap_location_sitemap_content($location_type) {
	global $wpseo_sitemaps, $gd_wpseo_options, $gd_wpseo_timezone, $gd_wpseo_max_entries, $gd_wpseo_location_type;
	
	$sitemap_key = 'gd_location_' . $location_type;
	$n = (int)get_query_var( 'sitemap_n' );
	
	$permalink_structure = get_option('permalink_structure');
	$location_base_link = geodir_get_location_link('base');
	
	if ((int)$n > 0) {
		$page = $n - 1;
	} else {
		$page = 0;
	}
	$args = array();
	$args['no_of_records'] = $gd_wpseo_max_entries;
	$args['spage'] = $page;
	$args['what'] = $location_type;
	
	$unset_location_terms = array();
	
	switch ($location_type) {
		case 'country_region':
		case 'region':
			$args['group_by'] = "CONCAT(country_slug, '-', region_slug)";
			$args['order'] = "region_slug, country_slug";
			$unset_location_terms = array('gd_city');
		break;
		case 'country_city':
			$args['group_by'] = "CONCAT(country_slug, '-', city_slug)";
			$args['order'] = "city_slug, country_slug";
			$unset_location_terms = array('gd_region');
		break;
		case 'region_city':
			$args['group_by'] = "CONCAT(region_slug, '-', city_slug)";
			$args['order'] = "city_slug, region_slug";
			$unset_location_terms = array('gd_country');
		break;
		case 'country':
			$args['order'] = "country_slug";
			$args['group_by'] = "country_slug";
			$unset_location_terms = array('gd_region', 'gd_city');
		break;
		case 'city':
			$args['what'] = 'city';
			$args['group_by'] = "CONCAT(country_slug, '-', region_slug, '-', city_slug)";
			$args['order'] = "city_slug, region_slug, country_slug";
			$unset_location_terms = array('gd_country', 'gd_region');
		break;
		case 'full':
			$args['what'] = 'full';
			$args['group_by'] = "CONCAT(country_slug, '-', region_slug, '-', city_slug)";
			$args['order'] = "city_slug, region_slug, country_slug";
		break;
	}
	
	$rows = geodir_sitemap_get_locations_array($args);
	global $wpdb;
	if (!empty($wpdb->queries)) {
		$wpdb->queries = array();
	}
	
	$output = '';
	if ( !empty($rows) ) {
		foreach ( $rows as $location ) {
			$location_terms = array();
			$location_terms['gd_country'] = $location->country_slug;
			$location_terms['gd_region'] = $location->region_slug;
			$location_terms['gd_city'] = $location->city_slug;
			
			if (!empty($unset_location_terms)) {
				foreach ($unset_location_terms as $location_term) {
					unset($location_terms[$location_term]);
				}
			}
			
			$location_link = geodir_sitemap_location_link($location_terms, $permalink_structure);
			
			$url = array(
				'loc' => $location_link,
				'pri' => 0.8,
				'chf' => geodir_sitemap_filter_frequency( 'locationpage', 'daily', $location_link ),
				'mod' => date( 'c', time() ),
			);
			// Use this filter to adjust the entry before it gets added to the sitemap.
			$url = apply_filters( 'wpseo_sitemap_entry', $url, $sitemap_key, $location );

			if ( is_array( $url ) && $url !== array() ) {
				$output .= $wpseo_sitemaps->sitemap_url( $url );
			}
		}
		unset( $location, $location_link, $url );
	}

	if ( empty( $output ) ) {
		$wpseo_sitemaps->bad_sitemap = true;
		return;
	}
	
	$sitemap = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
	$sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
	$sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
	$sitemap .= $output;

	// Filter to allow adding extra URLs, only do this on the first XML sitemap, not on all.
	if ( $n === 1 || $n === 0 ) {
		$sitemap .= apply_filters( 'wpseo_sitemap_' . $sitemap_key . '_content', '' );
	}

	$sitemap .= '</urlset>';
	
	return $sitemap;
}

function geodir_sitemap_gd_location_full() {
	global $wpseo_sitemaps;
	
	$sitemap = geodir_sitemap_location_sitemap_content('full');
	if ( empty( $sitemap ) ) {
		$wpseo_sitemaps->bad_sitemap = true;
		return NULL;
	}
	
	$wpseo_sitemaps->set_sitemap($sitemap);
}

function geodir_sitemap_gd_location_country_region() {
	global $wpseo_sitemaps;
	
	$sitemap = geodir_sitemap_location_sitemap_content('country_region');
	if ( empty( $sitemap ) ) {
		$wpseo_sitemaps->bad_sitemap = true;
		return NULL;
	}
	
	$wpseo_sitemaps->set_sitemap($sitemap);
}

function geodir_sitemap_gd_location_country_city() {
	global $wpseo_sitemaps;
	
	$sitemap = geodir_sitemap_location_sitemap_content('country_city');
	if ( empty( $sitemap ) ) {
		$wpseo_sitemaps->bad_sitemap = true;
		return NULL;
	}
	
	$wpseo_sitemaps->set_sitemap($sitemap);
}

function geodir_sitemap_gd_location_region_city() {
	global $wpseo_sitemaps;
	
	$sitemap = geodir_sitemap_location_sitemap_content('region_city');
	if ( empty( $sitemap ) ) {
		$wpseo_sitemaps->bad_sitemap = true;
		return NULL;
	}
	
	$wpseo_sitemaps->set_sitemap($sitemap);
}

function geodir_sitemap_gd_location_country() {
	global $wpseo_sitemaps;
	
	$sitemap = geodir_sitemap_location_sitemap_content('country');
	if ( empty( $sitemap ) ) {
		$wpseo_sitemaps->bad_sitemap = true;
		return NULL;
	}
	
	$wpseo_sitemaps->set_sitemap($sitemap);
}

function geodir_sitemap_gd_location_region() {
	global $wpseo_sitemaps;
	
	$sitemap = geodir_sitemap_location_sitemap_content('region');
	if ( empty( $sitemap ) ) {
		$wpseo_sitemaps->bad_sitemap = true;
		return NULL;
	}
	
	$wpseo_sitemaps->set_sitemap($sitemap);
}

function geodir_sitemap_gd_location_city() {
	global $wpseo_sitemaps;
	
	$sitemap = geodir_sitemap_location_sitemap_content('city');
	if ( empty( $sitemap ) ) {
		$wpseo_sitemaps->bad_sitemap = true;
		return NULL;
	}
	
	$wpseo_sitemaps->set_sitemap($sitemap);
}

function geodir_sitemap_location_link($location_terms, $permalink_structure = true, $with_base = true, $remove_location_terms = true) {	
	$location_link = '';
	
	if (empty($location_terms)) {
		return $location_link;
	}
	
	if ($remove_location_terms) {
		$location_terms = geodir_remove_location_terms($location_terms);
	}
	
	if ($permalink_structure) {
		$location_link = implode("/", array_values($location_terms)) . '/';
	} else {
		foreach ($location_terms as $term => $value) {
			$location_link .= '&' . $term . '=' . $value;
		}
	}
	
	if ($with_base) {
		$location_base_link = geodir_get_location_link('base');
		
		$location_link = $permalink_structure ? trailingslashit($location_base_link) . $location_link : $location_base_link . $location_link;
	}
	
	return $location_link;
}

/**
 * Function to dynamically filter the change frequency
 *
 * @param string $filter  Expands to wpseo_sitemap_$filter_change_freq, allowing for a change of the frequency for numerous specific URLs.
 * @param string $default The default value for the frequency.
 * @param string $url     The URL of the current entry.
 *
 * @return mixed|void
 */
function geodir_sitemap_filter_frequency( $filter, $default, $url ) {
	/**
	 * Filter: 'wpseo_sitemap_' . $filter . '_change_freq' - Allow filtering of the specific change frequency
	 *
	 * @api string $default The default change frequency
	 */
	$change_freq = apply_filters( 'wpseo_sitemap_' . $filter . '_change_freq', $default, $url );

	if ( ! in_array( $change_freq, array( 'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never' ) )
	) {
		$change_freq = $default;
	}

	return $change_freq;
}

function geodir_sitemap_get_location_type() {
	$hide_country_part = get_option('geodir_location_hide_country_part');
	$hide_region_part = get_option('geodir_location_hide_region_part');
	
	$type = 'full';
	if ($hide_region_part && $hide_country_part) {
		$type = 'city';
	} else if ($hide_region_part && !$hide_country_part) {
		$type = 'country_city';
	} else if (!$hide_region_part && $hide_country_part) {
		$type = 'region_city';
	}
	
	return $type;
}

function geodir_sitemap_get_locations_array($args = null) {
	global $wpdb;

	$hide_country_part = get_option('geodir_location_hide_country_part');
	$hide_region_part = get_option('geodir_location_hide_region_part');
	
	$defaults = array(
					'fields' => '*',
					'what' => 'full',
					'city_val' => '',
					'region_val' => '',
					'country_val' => '' ,
					'country_non_restricted' => '',
					'region_non_restricted' => '',
					'city_non_restricted' => '',
					'filter_by_non_restricted' => true,
					'compare_operator' => 'like',
					'country_column_name' => 'country',
					'region_column_name' => 'region',
					'city_column_name' => 'city',
					'location_link_part' => true,
					'order' => 'location_id',
					'order_by' => 'asc',
					'group_by' => '',
					'no_of_records' => '',
					'spage' => '',
					'count_only' => false,
				);


	$args = wp_parse_args( $args, $defaults );
	
	if (empty($args['what'])) {
		$args['what'] = 'full';
	}

	$search_query = '';
	$location_link_column = '';
	$location_default = geodir_get_default_location();
	
	$permalink_structure = get_option('permalink_structure');
	$geodir_enable_country = get_option( 'geodir_enable_country' );
	$geodir_enable_region = get_option( 'geodir_enable_region' );
	$geodir_enable_city = get_option( 'geodir_enable_city' );
	$geodir_selected_countries = get_option( 'geodir_selected_countries' );
	$geodir_selected_regions = get_option( 'geodir_selected_regions' );
	$geodir_selected_cities = get_option( 'geodir_selected_cities' );

	if ( $args['filter_by_non_restricted'] ) {
		// Non restricted countries
		if ( $args['country_non_restricted'] == '' ) {
			if( $geodir_enable_country == 'default' ) {
				$country_non_retsricted = isset( $location_default->country ) ? $location_default->country : '';
				$args['country_non_restricted']  = $country_non_retsricted;
			} else if( $geodir_enable_country == 'selected' ) {
				$country_non_retsricted = $geodir_selected_countries;

				if( !empty( $country_non_retsricted ) && is_array( $country_non_retsricted ) ) {
					$country_non_retsricted = implode(',' , $country_non_retsricted );
				}

				$args['country_non_restricted'] = $country_non_retsricted;
			}

			$args['country_non_restricted'] = geodir_parse_location_list( $args['country_non_restricted'] );
		}

		// Non restricted Regions
		if ( $args['region_non_restricted'] == '' ) {
			if( $geodir_enable_region == 'default' ) {
				$regoin_non_restricted= isset( $location_default->region ) ? $location_default->region : '';
				$args['region_non_restricted']  = $regoin_non_restricted;
			} else if( $geodir_enable_region == 'selected' ) {
				$regoin_non_restricted = $geodir_selected_regions;
				
				if( !empty( $regoin_non_restricted ) && is_array( $regoin_non_restricted ) ) {
					$regoin_non_restricted = implode( ',', $regoin_non_restricted );
				}

				$args['region_non_restricted']  = $regoin_non_restricted;
			}

			$args['region_non_restricted'] = geodir_parse_location_list( $args['region_non_restricted'] );
		}

		// Non restricted cities
		if ( $args['city_non_restricted'] == '' ) {
			if( $geodir_enable_city == 'default' ) {
				$city_non_retsricted = isset( $location_default->city ) ? $location_default->city : '';
				$args['city_non_restricted']  = $city_non_retsricted;
			} else if( $geodir_enable_city == 'selected' ) {
				$city_non_restricted = $geodir_selected_cities;

				if( !empty( $city_non_restricted ) && is_array( $city_non_restricted ) ) {
					$city_non_restricted = implode( ',', $city_non_restricted );
				}

				$args['city_non_restricted']  = $city_non_restricted;
			}
			$args['city_non_restricted'] = geodir_parse_location_list( $args['city_non_restricted'] );
		}
	}

	if ( $args['location_link_part'] ) {
		switch( $args['what'] ) {
			case 'country':
				if ($permalink_structure != '') {
					$location_link_column = ", CONCAT_WS('/', country_slug) AS location_link ";
				} else {
					$location_link_column = ", CONCAT_WS('&gd_country=', '', country_slug) AS location_link ";
				}
			break;
			case 'country_region':
				if ($permalink_structure != '') {
					$location_link_column = ", CONCAT_WS('/', country_slug, region_slug) AS location_link ";
				} else {
					$location_link_column = ", CONCAT_WS('&', CONCAT('&gd_country=', country_slug), CONCAT('gd_region=', region_slug) ) AS location_link ";
				}
			break;
			case 'country_city':
				if ($permalink_structure != '') {
					$location_link_column = ", CONCAT_WS('/', country_slug, city_slug) AS location_link ";
				} else {
					$location_link_column = ", CONCAT_WS('&', CONCAT('&gd_country=', city_slug), CONCAT('gd_city=', city_slug) ) AS location_link ";
				}
			break;
			case 'region_city':
				if ($permalink_structure != '') {
					$location_link_column = ", CONCAT_WS('/', region_slug, city_slug) AS location_link ";
				} else {
					$location_link_column = ", CONCAT_WS('&', CONCAT('&gd_region=', city_slug), CONCAT('gd_city=', city_slug) ) AS location_link ";
				}
			break;
			case 'city':
				if ($permalink_structure != '') {
					$location_link_column = ", CONCAT_WS('/', city_slug) AS location_link ";
				} else {
					$location_link_column = ", CONCAT_WS('&gd_city=', '', city_slug) AS location_link ";
				}
			break;
			case 'full':				
				$concat_ws = array();
				
				if ($permalink_structure != '') {
					$concat_ws[] = 'country_slug';
					$concat_ws[] = 'region_slug';					
					$concat_ws[] = 'city_slug';
					
					$concat_ws = implode(', ', $concat_ws);
					
					$location_link_column = ", CONCAT_WS('/', " . $concat_ws . ") AS location_link ";
				} else {
					$concat_ws[] = "CONCAT('&gd_country=', country_slug)";
					$concat_ws[] = "CONCAT('gd_region=', region_slug)";
					$concat_ws[] = "CONCAT('gd_city=', city_slug)";
					
					$concat_ws = implode(', ', $concat_ws);
					
					$location_link_column = ", CONCAT_WS('&', " . $concat_ws . ") AS location_link ";
				}				
			break;
		}
	}

	switch( $args['compare_operator'] ) {
		case 'like' :
			if( isset( $args['country_val'] ) && $args['country_val'] != '' ) {
				$countries_search_sql = geodir_countries_search_sql( $args['country_val'] );
				$countries_search_sql = $countries_search_sql != '' ? " OR FIND_IN_SET(country, '" . $countries_search_sql . "')" : '';
				$translated_country_val = sanitize_title( trim( wp_unslash( $args['country_val'] ) ) );
				$search_query .= " AND ( lower(".$args['country_column_name'].") like  \"%". geodir_strtolower( $args['country_val'] )."%\" OR  lower(country_slug) LIKE \"". $translated_country_val ."%\" " . $countries_search_sql . " ) ";
			}

			if (isset($args['region_val']) &&  $args['region_val'] !='') {
				$search_query .= " AND lower(".$args['region_column_name'].") like  \"%". geodir_strtolower($args['region_val'])."%\" ";
			}

			if (isset($args['city_val']) && $args['city_val'] !='') {
				$search_query .= " AND lower(".$args['city_column_name'].") like  \"%". geodir_strtolower($args['city_val'])."%\" ";
			}
			break;

		case 'in' :
			if (isset($args['country_val'])  && $args['country_val'] !='') {
				$args['country_val'] = geodir_parse_location_list($args['country_val']) ;
				$search_query .= " AND lower(".$args['country_column_name'].") in($args[country_val]) ";
			}

			if (isset($args['region_val']) && $args['region_val'] !='' ) {
				$args['region_val'] = geodir_parse_location_list($args['region_val']) ;
				$search_query .= " AND lower(".$args['region_column_name'].") in($args[region_val]) ";
			}

			if (isset($args['city_val'])  && $args['city_val'] !='' ) {
				$args['city_val'] = geodir_parse_location_list($args['city_val']) ;
				$search_query .= " AND lower(".$args['city_column_name'].") in($args[city_val]) ";
			}

			break;
		default :
			if(isset($args['country_val']) && $args['country_val'] !='' ) {
				$countries_search_sql = geodir_countries_search_sql( $args['country_val'] );
				$countries_search_sql = $countries_search_sql != '' ? " OR FIND_IN_SET(country, '" . $countries_search_sql . "')" : '';
				$translated_country_val = sanitize_title( trim( wp_unslash( $args['country_val'] ) ) );
				$search_query .= " AND ( lower(".$args['country_column_name'].") =  '". geodir_strtolower($args['country_val'])."' OR  lower(country_slug) LIKE \"". $translated_country_val ."%\" " . $countries_search_sql . " ) ";
			}

			if (isset($args['region_val']) && $args['region_val'] !='') {
				$search_query .= " AND lower(".$args['region_column_name'].") =  \"". geodir_strtolower($args['region_val'])."\" ";
			}

			if (isset($args['city_val']) && $args['city_val'] !='' ) {
				$search_query .= " AND lower(".$args['city_column_name'].") =  \"". geodir_strtolower($args['city_val'])."\" ";
			}
			break ;

	}

	if ($args['country_non_restricted'] != '') {
		$search_query .= " AND LOWER(country) IN ($args[country_non_restricted]) ";
	}

	if ($args['region_non_restricted'] != '') {
		$search_query .= " AND LOWER(region) IN ($args[region_non_restricted]) ";
	}

	if ($args['city_non_restricted'] != '') {
		$search_query .= " AND LOWER(city) IN ($args[city_non_restricted]) ";
	}

	// page
	if ($args['no_of_records']){
		$spage = $args['no_of_records'] * $args['spage'];
	} else {
		$spage = "0";
	}

	// limit
	$limit = $args['no_of_records'] != '' ? ' LIMIT ' . $spage . ', ' . (int)$args['no_of_records'] . ' ' : '';
	
	$group_by = !empty($args['group_by']) ? 'GROUP BY ' . $args['group_by'] : '';
	$order_by = 'ORDER BY ';
	$order_by .= !empty($args['order']) ? $args['order'] . ' ' : 'location_id ';
	$order_by .= !empty($args['order_by']) ? $args['order_by'] : 'asc';
	
	if (!empty($args['count_only'])) {
		// query
		$query = "SELECT location_id FROM " . POST_LOCATION_TABLE . " WHERE 1=1 " .  $search_query . " " . $group_by;
		$rows = $wpdb->get_results($query);
		
		$wpdb->flush();
		return !empty($rows) ? count($rows) : NULL;
	}

	// query
	$query = "SELECT " . $args['fields'] . $location_link_column . " FROM " . POST_LOCATION_TABLE . " WHERE 1=1 " .  $search_query . " " . $group_by . " " . $order_by . " " . $limit;
	$rows = $wpdb->get_results($query);
	
	$wpdb->flush();

	return $rows;
}

/**
 * Get neighbour hood location info by id.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param int|string $hood Neighbour hood id or slug.
 * @param bool $is_slug Is hood passed is slug? Default false.
 * @return array|mixed
 */
function geodir_location_get_neighbourhood_by_id($hood_id, $is_slug = false, $location_id = 0) {
	global $wpdb;
	
	if (empty($hood_id)) {
		return NULL;
	}
	
	$field = $is_slug ? 'hood_slug' : 'hood_id';
	$where = '';
	if ($location_id > 0) {
		$where .= "AND h.hood_location_id = '" . (int)$location_id . "'";
	}
	
	$sql = $wpdb->prepare("SELECT h.*, l.*, h.hood_name AS neighbourhood, h.hood_slug AS neighbourhood_slug FROM `" . POST_NEIGHBOURHOOD_TABLE . "` AS h INNER JOIN `" . POST_LOCATION_TABLE . "` AS l ON l.location_id = h.hood_location_id WHERE `h`.`" . $field . "` = %s " . $where . " ORDER BY h.hood_id ASC", array($hood_id));
	$result = $wpdb->get_row($sql);
	
	return $result;
}

/**
 * Get the neighbour hood location url.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @param int|string $hood Neighbour hood id or slug.
 * @param bool $is_slug Is hood passed is slug? Default false.
 * @return null|string Neighbour hood location url.
 */
function geodir_location_get_neighbourhood_url($hood, $is_slug = false) {
	if ($hood == '') {
		return NULL;
	}
	
	$hood_slug = $hood;
	if (!$is_slug) {
		$hood_info = geodir_location_get_neighbourhood_by_id($hood);
		
		if (empty($hood_info)) {
			return NULL;			
		}
		
		$hood_slug = $hood_info->hood_slug;
	}
	
	$permalink_structure = get_option('permalink_structure');
	
	$location_terms = geodir_get_current_location_terms();
	
	if (!empty($location_terms) && isset($location_terms['gd_neighbourhood'])) {
		unset($location_terms['gd_neighbourhood']);
	}
	
	$location_link = geodir_sitemap_location_link($location_terms, $permalink_structure);
	
	if ($permalink_structure != '') {
		$url = trailingslashit($location_link) . $hood_slug . '/';
	} else {
		$url = add_query_arg(array('gd_neighbourhood' => $hood_slug), $location_link);
	}
	
	/**
     * Filter the neighbour hood location url.
     *
     * @since 1.4.4
     * @package GeoDirectory_Location_Manager
     *
     * @param string $url Neighbour hood location url.
	 * @param string $hood int|string $hood Neighbour hood id or slug.
	 * @param bool $is_slug Is hood passed is slug?.
     */
	$url = apply_filters('geodir_location_get_neighbourhood_url', $url, $hood, $is_slug);

	return $url;
}

/**
 * Check the neighbour hood for current city.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @param string $hood Neighbour hood id or slug.
 * @param string $city Current city slug.
 * @param string $reigon Current region slug. Default empty.
 * @param string $country Current country slug. Default empty.
 * @return null|string Neighbour hood location url.
 */
function geodir_location_check_is_neighbourhood($hood_slug, $gd_city, $gd_region = '', $gd_country = '') {
	if (empty($hood_slug) || empty($gd_city)) {
		return false;
	}
	
	$location = geodir_city_info_by_slug($gd_city, $gd_country, $gd_region);
	if (empty($location)) {
		return false;
	}
	
	$hood = geodir_location_get_neighbourhood_by_id($hood_slug, true, $location->location_id);
	if (!empty($hood)) {
		return $hood;
	}
	
	return false;
}

/**
 * Set the neighbourhood location term.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @global object $gd_session GeoDirectory Session object.
 *
 * @param array $location_array {
 *    Attributes of the location_array.
 *
 *    @type string $gd_country The country slug.
 *    @type string $gd_region The region slug.
 *    @type string $gd_city The city slug.
 *
 * }
 * @param string $location_array_from Source type of location terms. Default session.
 * @param string $gd_post_type WP post type.
 */
function geodir_location_set_neighbourhood_term($location_array, $location_array_from, $gd_post_type) {
    global $gd_session;
	
	if (!get_option('location_neighbourhoods') || empty($location_array['gd_city']) || (isset($location_array['gd_city']) && $location_array['gd_city'] == 'me')) {
		return $location_array;
	}
	
    if ($location_array_from == 'session') {
        if ($gd_ses_neighbourhood = $gd_session->get('gd_neighbourhood')) {
			$location_array['gd_neighbourhood'] = urldecode($gd_ses_neighbourhood);
		}
    } else {
		global $wp;
		if (isset($wp->query_vars['gd_neighbourhood']) && $wp->query_vars['gd_neighbourhood'] != '') {
			$location_array['gd_neighbourhood'] = urldecode($wp->query_vars['gd_neighbourhood']);
		}
	   			
		// Fix category link in ajax popular category widget on change post type
		if (empty($location_array['gd_neighbourhood']) && defined('DOING_AJAX') && DOING_AJAX && $gd_ses_neighbourhood = $gd_session->get('gd_neighbourhood')) {
			$location_array['gd_neighbourhood'] = urldecode($gd_ses_neighbourhood);
		}
    }

    return $location_array;
}

add_filter('geodir_current_location_terms', 'geodir_location_set_neighbourhood_term', 10, 3);

/**
 * Set up the location filter for backend cpt listing pages.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @global string $pagenow The current screen.
 */
function geodir_location_admin_location_filter_init() {
	global $pagenow;

	if ($pagenow == 'edit.php' && !empty($_GET['post_type']) && in_array($_GET['post_type'], geodir_get_posttypes())) {
		$location_allowed = function_exists( 'geodir_cpt_no_location' ) && geodir_cpt_no_location($_GET['post_type']) ? false : true;
		
		if ($location_allowed) {
			add_action('restrict_manage_posts', 'geodir_location_admin_location_filter_box', 10);
			
			if (!empty($_GET['_gd_country'])) {
				add_filter('posts_join', 'geodir_location_admin_filter_posts_join', 10, 1);
				add_filter('posts_where', 'geodir_location_admin_filter_posts_where', 10, 1);
			}
		}
	}
}

/**
 * Adds the location filter in backend cpt listing pages.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 */
function geodir_location_admin_location_filter_box() {
    $gd_country = isset($_GET['_gd_country']) ? $_GET['_gd_country'] : '';
	$gd_region = isset($_GET['_gd_region']) ? $_GET['_gd_region'] : '';
	$gd_city = isset($_GET['_gd_city']) ? $_GET['_gd_city'] : '';
	
	$gd_countries = geodir_post_location_countries(true);
	
	if (empty($gd_countries)) {
		return;
	}
	
	$region_disabled = 'disabled="disabled"';
	$city_disabled = 'disabled="disabled"';
	
    echo '<select name="_gd_country" class="_gd_country" onchange="javascript:gd_location_admin_filter(this, \'cn\');">';
    echo '<option value="" style="color:#888888">' . __('Country', 'geodirectory') . '</option>';
	foreach ($gd_countries as $slug => $title) {
		echo '<option value="' . esc_attr($slug) . '" ' . selected($gd_country, $slug) . '>' . __($title, 'geodirectory') . '</option>';
	}
    echo '</select>';
	
	$gd_regions = array();
	if ($gd_country != '') {
		$args = array();
		$args['filter_by_non_restricted'] = false;
		$args['location_link_part'] = false;
		$args['compare_operator'] = '=';
		$args['country_column_name'] = 'country_slug';
		$args['region_column_name'] = 'region_slug';
		$args['country_val'] = $gd_country;
		
		$args['fields'] = 'region AS title, region_slug AS slug';
		$args['order'] = 'region';
		$args['group_by'] = 'region_slug';
		$gd_regions = geodir_sitemap_get_locations_array($args);
		
		if (!empty($gd_regions)) {
			$region_disabled = '';
		}
	}
	
	echo '<select name="_gd_region" class="_gd_region" ' . $region_disabled . ' onchange="javascript:gd_location_admin_filter(this, \'rg\');">';
    echo '<option value="" style="color:#888888">' . __('Region', 'geodirectory') . '</option>';
	if (!empty($gd_regions)) {
		foreach ($gd_regions as $region) {
			if ($region->slug == '' || $region->title == '') {
				continue;
			}
			echo '<option value="' . esc_attr($region->slug) . '" ' . selected($gd_region, $region->slug) . '>' . __($region->title, 'geodirectory') . '</option>';
		}
	}
    echo '</select>';
	
	$gd_cities = array();
	if ($gd_country != '' && $gd_region != '') {
		$args['region_val'] = $gd_region;
		$args['fields'] = 'city AS title, city_slug AS slug';
		$args['order'] = 'city';
		$args['group_by'] = 'city_slug';
		$gd_cities = geodir_sitemap_get_locations_array($args);
		
		if (!empty($gd_cities)) {
			$city_disabled = '';
		}
	}
	echo '<select name="_gd_city" class="_gd_city" ' . $city_disabled . '>';
    echo '<option value="" style="color:#888888">' . __('City', 'geodirectory') . '</option>';
	if (!empty($gd_cities)) {
		foreach ($gd_cities as $city) {
			if ($city->slug == '' || $city->title == '') {
				continue;
			}
			echo '<option value="' . esc_attr($city->slug) . '" ' . selected($gd_city, $city->slug) . '>' . __($city->title, 'geodirectory') . '</option>';
		}
	}
    echo '</select>';
}

/**
 * Back end cpt listing location join filter.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 * @global string $pagenow The current screen.
 *
 * @param string $join The join query clause.
 * @return string Modified join query clause.
 */
function geodir_location_admin_filter_posts_join($join) {
	global $wpdb, $plugin_prefix, $pagenow;
	$post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
	$post_types = geodir_get_posttypes();
	
	if ($post_type != '' && in_array($post_type, $post_types) && $pagenow == 'edit.php') {
		$table = $plugin_prefix . $post_type . '_detail';
		$join .= " INNER JOIN " . $table . " ON (" . $table . ".post_id = " . $wpdb->posts . ".ID) ";
	}
	return $join;
}

/**
 * Back end cpt listing location where filter.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 * @global string $pagenow The current screen.
 *
 * @param string $where The where query clause.
 * @return string Modified where query clause.
 */
function geodir_location_admin_filter_posts_where($where) {
	global $wpdb, $plugin_prefix, $pagenow;
	$post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
	$post_types = geodir_get_posttypes();
	
	if ($post_type != '' && in_array($post_type, $post_types) && $pagenow == 'edit.php') {
		$table = $plugin_prefix . $post_type . '_detail';
		
		if (!empty($_GET['_gd_country'])) {
			$_where = '%,[' . $_GET['_gd_country'] . ']';
			
			if (!empty($_GET['_gd_region'])) {
				$_where = '%,[' . $_GET['_gd_region'] . '],[' . $_GET['_gd_country'] . ']';
				
				if (!empty($_GET['_gd_city'])) {
					$_where = '[' . $_GET['_gd_city'] . '],[' . $_GET['_gd_region'] . '],[' . $_GET['_gd_country'] . ']';
				}
			}
			
			$where .= " AND " . $table . ".post_locations LIKE '" . $_where . "'";
		}
	}
	return $where;
}

/**
 * Adds location import & export under GD > Import & Export page.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @param array $gd_posttypes GD post types.
 * @param array $gd_chunksize_options File chunk size options.
 * @param string $nonce Wordpress security token for GD import & export.
 */
function geodir_location_import_export($gd_posttypes, $gd_chunksize_options, $nonce) {
    $neighbourhood_active = get_option('location_neighbourhoods') ? true : false;
    
    $total = (int)geodir_location_imex_count_locations();
    $total_hoods = $neighbourhood_active ? (int)geodir_location_imex_count_neighbourhoods() : 0;

    $gd_chunksize_option = '';
    foreach ($gd_chunksize_options as $value => $title) {
        $gd_chunksize_option .= '<option value="' . $value . '" ' . selected($value, 5000, false) . '>' . $title . '</option>';
    }

    $gd_locations_sample_csv = plugin_dir_url('') . 'geodir_location_manager/images/gd_sample_locations.csv';
    /**
     * Filter sample location data csv file url.
     *
     * @since 1.4.4
     * @package GeoDirectory_Location_Manager
     *
     * @param string $gd_locations_sample_csv Sample location data csv file url.
     */
    $gd_locations_sample_csv = apply_filters( 'geodir_export_locations_sample_csv', $gd_locations_sample_csv );
    
    $gd_neighbourhoods_sample_csv = plugin_dir_url('') . 'geodir_location_manager/images/gd_sample_neighbourhoods.csv';
    /**
     * Filter sample location data csv file url.
     *
     * @since 1.4.5
     * @package GeoDirectory_Location_Manager
     *
     * @param string $gd_locations_sample_csv Sample location data csv file url.
     */
    $gd_neighbourhoods_sample_csv = apply_filters( 'geodir_export_neighbourhoods_sample_csv', $gd_neighbourhoods_sample_csv );
    ?>
    <div id="gd_imex_loc_i" class="metabox-holder">
      <div class="meta-box-sortables ui-sortable">
        <div id="gd_ie_imlocs" class="postbox gd-hndle-pbox">
          <button class="handlediv button-link" type="button"><span class="screen-reader-text"><?php _e( 'Toggle panel - GD Locations: Import CSV', 'geodirectory' );?></span><span aria-hidden="true" class="toggle-indicator"></span></button>
          <h3 class="hndle gd-hndle-click"><span style='vertical-align:top;'><?php echo __( 'GD Locations: Import CSV', 'geodirlocation' );?></span></h3>
          <div class="inside">
            <table class="form-table">
                <tbody>
                  <tr>
                    <td class="gd-imex-box">
                        <div class="gd-im-choices">
                        <p><input type="radio" value="update" name="gd_im_choiceloc" id="gd_im_choiceloc_u" /><label for="gd_im_choiceloc_u"><?php _e( 'Update item if item with location_id/city_slug already exists.', 'geodirlocation' );?></label></p>
                        <p><input type="radio" checked="checked" value="skip" name="gd_im_choiceloc" id="gd_im_choiceloc_s" /><label for="gd_im_choiceloc_s"><?php _e( 'Ignore item if item with location_id/city_slug already exists.', 'geodirlocation' );?></label></p>
                        </div>
                        <div class="plupload-upload-uic hide-if-no-js" id="gd_im_locplupload-upload-ui">
                            <input type="text" readonly="readonly" name="gd_im_loc_file" class="gd-imex-file gd_im_loc_file" id="gd_im_loc" onclick="jQuery('#gd_im_locplupload-browse-button').trigger('click');" />
                            <input id="gd_im_locplupload-browse-button" type="button" value="<?php echo SELECT_UPLOAD_CSV; ?>" class="gd-imex-loc-upload button-primary" /><input type="button" value="<?php echo esc_attr( __( 'Download Sample CSV', 'geodirectory' ) );?>" class="button-secondary" name="gd_imex_loc_sample" id="gd_imex_loc_sample">
                        <input type="hidden" id="gd_imex_loc_csv" value="<?php echo $gd_locations_sample_csv;?>" />
                        <?php
                        /**
                         * Called just after the sample locations CSV download link.
                         *
                         * @since 1.4.4
                         * @package GeoDirectory_Location_Manager
                         */
                        do_action('geodir_sample_locations_csv_download_link');
                        ?>
                            <span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce( 'gd_im_locpluploadan' ); ?>"></span>
                            <div class="filelist"></div>
                        </div>
                        <span id="gd_im_locupload-error" style="display:none"></span>
                        <span class="description"></span>
                        <div id="gd_importer" style="display:none">
                            <input type="hidden" id="gd_total" value="0"/>
                            <input type="hidden" id="gd_prepared" value="continue"/>
                            <input type="hidden" id="gd_processed" value="0"/>
                            <input type="hidden" id="gd_created" value="0"/>
                            <input type="hidden" id="gd_updated" value="0"/>
                            <input type="hidden" id="gd_skipped" value="0"/>
                            <input type="hidden" id="gd_invalid" value="0"/>
                            <input type="hidden" id="gd_images" value="0"/>
                            <input type="hidden" id="gd_terminateaction" value="continue"/>
                        </div>
                        <div class="gd-import-progress" id="gd-import-progress" style="display:none">
                            <div class="gd-import-file"><b><?php _e("Import Data Status :", 'geodirectory');?> </b><font id="gd-import-done">0</font> / <font id="gd-import-total">0</font>&nbsp;( <font id="gd-import-perc">0%</font> )
                            <div class="gd-fileprogress"></div>
                            </div>
                        </div>
                        <div class="gd-import-msg" id="gd-import-msg" style="display:none">
                            <div id="message" class="message fade"></div>
                        </div>
                        <div class="gd-imex-btns" style="display:none;">
                            <input type="hidden" class="geodir_import_file" name="geodir_import_file" value="save"/>
                            <input onclick="gd_imex_PrepareImport(this, 'loc')" type="button" value="<?php echo CSV_IMPORT_DATA; ?>" id="gd_import_data" class="button-primary" />
                            <input onclick="gd_imex_ContinueImport(this, 'loc')" type="button" value="<?php _e( "Continue Import Data", 'geodirectory' );?>" id="gd_continue_data" class="button-primary" style="display:none"/>
                            <input type="button" value="<?php _e("Terminate Import Data", 'geodirectory');?>" id="gd_stop_import" class="button-primary" name="gd_stop_import" style="display:none" onclick="gd_imex_TerminateImport(this, 'loc')"/>
                            <div id="gd_process_data" style="display:none">
                                <span class="spinner is-active" style="display:inline-block;margin:0 5px 0 5px;float:left"></span><?php _e("Wait, processing import data...", 'geodirectory');?>
                            </div>
                        </div>
                    </td>
                  </tr>
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div id="gd_imex_loc_e" class="metabox-holder">
      <div class="meta-box-sortables ui-sortable">
        <div id="gd_imex_locs" class="postbox gd-hndle-pbox">
          <button class="handlediv button-link" type="button"><span class="screen-reader-text"><?php _e( 'Toggle panel - GD Locations: Export CSV', 'geodirectory' );?></span><span aria-hidden="true" class="toggle-indicator"></span></button>
          <h3 class="hndle gd-hndle-click"><span style='vertical-align:top;'><?php echo __( 'GD Locations: Export CSV', 'geodirlocation' );?></span></h3>
          <div class="inside">
            <table class="form-table">
                <tbody>
                   <tr>
                    <td class="fld" style="vertical-align:top"><label><?php _e( 'Max entries per csv file:', 'geodirectory' );?></label></td>
                    <td><select name="gd_chunk_size" id="gd_chunk_size" style="min-width:140px"><?php echo $gd_chunksize_option;?></select><span class="description"><?php _e( 'Please select the maximum number of entries per csv file (defaults to 5000, you might want to lower this to prevent memory issues on some installs)', 'geodirectory' );?></span><input type="hidden" class="gd-imex-total" value="<?php echo $total;?>" /></td>
                  </tr>
                  <tr>
                    <td class="fld" style="vertical-align:top"><label><?php _e( 'Progress:', 'geodirectory' );?></label></td>
                    <td><div id='gd_progressbar_box'><div id="gd_progressbar" class="gd_progressbar"><div class="gd-progress-label"></div></div></div><p style="display:inline-block"><?php _e( 'Elapsed Time:', 'geodirectory' );?></p>&nbsp;&nbsp;<p id="gd_timer" class="gd_timer">00:00:00</p></td>
                  </tr>
                  <tr class="gd-ie-actions">
                    <td style="vertical-align:top">
                        <input type="submit" value="<?php echo esc_attr( __( 'Export CSV', 'geodirectory' ) );?>" class="button-primary" name="gd_imex_locs_submit" id="gd_imex_locs_submit">
                    </td>
                    <td id="gd_ie_ex_files" class="gd-ie-files"></td>
                  </tr>
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <?php if ($neighbourhood_active) { ?>
    <div id="gd_imex_hood_i" class="metabox-holder">
      <div class="meta-box-sortables ui-sortable">
        <div id="gd_ie_imhoods" class="postbox gd-hndle-pbox">
          <button class="handlediv button-link" type="button"><span class="screen-reader-text"><?php _e( 'Toggle panel - GD Neighbourhoods: Import CSV', 'geodirectory' );?></span><span aria-hidden="true" class="toggle-indicator"></span></button>
          <h3 class="hndle gd-hndle-click"><span style='vertical-align:top;'><?php echo __( 'GD Neighbourhoods: Import CSV', 'geodirlocation' );?></span></h3>
          <div class="inside">
            <table class="form-table">
                <tbody>
                  <tr>
                    <td class="gd-imex-box">
                        <div class="gd-im-choices">
                        <p><input type="radio" value="update" name="gd_im_choicehood" id="gd_im_choicehood_u" /><label for="gd_im_choicehood_u"><?php _e( 'Update item if item with neighbourhood_id/neighbourhood_slug already exists.', 'geodirlocation' );?></label></p>
                        <p><input type="radio" checked="checked" value="skip" name="gd_im_choicehood" id="gd_im_choicehood_s" /><label for="gd_im_choicehood_s"><?php _e( 'Ignore item if item with neighbourhood_id/neighbourhood_slug already exists.', 'geodirlocation' );?></label></p>
                        </div>
                        <div class="plupload-upload-uic hide-if-no-js" id="gd_im_hoodplupload-upload-ui">
                            <input type="text" readonly="readonly" name="gd_im_hood_file" class="gd-imex-file gd_im_hood_file" id="gd_im_hood" onclick="jQuery('#gd_im_hoodplupload-browse-button').trigger('click');" />
                            <input id="gd_im_hoodplupload-browse-button" type="button" value="<?php echo SELECT_UPLOAD_CSV; ?>" class="gd-imex-hood-upload button-primary" /><input type="button" value="<?php echo esc_attr( __( 'Download Sample CSV', 'geodirectory' ) );?>" class="button-secondary" name="gd_imex_hood_sample" id="gd_imex_hood_sample">
                        <input type="hidden" id="gd_imex_hood_csv" value="<?php echo $gd_neighbourhoods_sample_csv;?>" />
                        <?php
                        /**
                         * Called just after the sample location neighbourhoods CSV download link.
                         *
                         * @since 1.4.5
                         * @package GeoDirectory_Location_Manager
                         */
                        do_action('geodir_sample_neighbourhoods_csv_download_link');
                        ?>
                            <span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce( 'gd_im_hoodpluploadan' ); ?>"></span>
                            <div class="filelist"></div>
                        </div>
                        <span id="gd_im_hoodupload-error" style="display:none"></span>
                        <span class="description"></span>
                        <div id="gd_importer" style="display:none">
                            <input type="hidden" id="gd_total" value="0"/>
                            <input type="hidden" id="gd_prepared" value="continue"/>
                            <input type="hidden" id="gd_processed" value="0"/>
                            <input type="hidden" id="gd_created" value="0"/>
                            <input type="hidden" id="gd_updated" value="0"/>
                            <input type="hidden" id="gd_skipped" value="0"/>
                            <input type="hidden" id="gd_invalid" value="0"/>
                            <input type="hidden" id="gd_images" value="0"/>
                            <input type="hidden" id="gd_terminateaction" value="continue"/>
                        </div>
                        <div class="gd-import-progress" id="gd-import-progress" style="display:none">
                            <div class="gd-import-file"><b><?php _e("Import Data Status :", 'geodirectory');?> </b><font id="gd-import-done">0</font> / <font id="gd-import-total">0</font>&nbsp;( <font id="gd-import-perc">0%</font> )
                            <div class="gd-fileprogress"></div>
                            </div>
                        </div>
                        <div class="gd-import-msg" id="gd-import-msg" style="display:none">
                            <div id="message" class="message fade"></div>
                        </div>
                        <div class="gd-imex-btns" style="display:none;">
                            <input type="hidden" class="geodir_import_file" name="geodir_import_file" value="save"/>
                            <input onclick="gd_imex_PrepareImport(this, 'hood')" type="button" value="<?php echo CSV_IMPORT_DATA; ?>" id="gd_import_data" class="button-primary" />
                            <input onclick="gd_imex_ContinueImport(this, 'hood')" type="button" value="<?php _e( "Continue Import Data", 'geodirectory' );?>" id="gd_continue_data" class="button-primary" style="display:none"/>
                            <input type="button" value="<?php _e("Terminate Import Data", 'geodirectory');?>" id="gd_stop_import" class="button-primary" name="gd_stop_import" style="display:none" onclick="gd_imex_TerminateImport(this, 'hood')"/>
                            <div id="gd_process_data" style="display:none">
                                <span class="spinner is-active" style="display:inline-block;margin:0 5px 0 5px;float:left"></span><?php _e("Wait, processing import data...", 'geodirectory');?>
                            </div>
                        </div>
                    </td>
                  </tr>
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div id="gd_imex_hood_e" class="metabox-holder">
      <div class="meta-box-sortables ui-sortable">
        <div id="gd_imex_hoods" class="postbox gd-hndle-pbox">
          <button class="handlediv button-link" type="button"><span class="screen-reader-text"><?php _e( 'Toggle panel - GD Neighbourhoods: Export CSV', 'geodirectory' );?></span><span aria-hidden="true" class="toggle-indicator"></span></button>
          <h3 class="hndle gd-hndle-click"><span style='vertical-align:top;'><?php echo __( 'GD Neighbourhoods: Export CSV', 'geodirlocation' );?></span></h3>
          <div class="inside">
            <table class="form-table">
                <tbody>
                   <tr>
                    <td class="fld" style="vertical-align:top"><label><?php _e( 'Max entries per csv file:', 'geodirectory' );?></label></td>
                    <td><select name="gd_chunk_size" id="gd_chunk_size" style="min-width:140px"><?php echo $gd_chunksize_option;?></select><span class="description"><?php _e( 'Please select the maximum number of entries per csv file (defaults to 5000, you might want to lower this to prevent memory issues on some installs)', 'geodirectory' );?></span><input type="hidden" class="gd-imex-total" value="<?php echo $total_hoods;?>" /></td>
                  </tr>
                  <tr>
                    <td class="fld" style="vertical-align:top"><label><?php _e( 'Progress:', 'geodirectory' );?></label></td>
                    <td><div id='gd_progressbar_box'><div id="gd_progressbar" class="gd_progressbar"><div class="gd-progress-label"></div></div></div><p style="display:inline-block"><?php _e( 'Elapsed Time:', 'geodirectory' );?></p>&nbsp;&nbsp;<p id="gd_timer" class="gd_timer">00:00:00</p></td>
                  </tr>
                  <tr class="gd-ie-actions">
                    <td style="vertical-align:top">
                        <input type="submit" value="<?php echo esc_attr( __( 'Export CSV', 'geodirectory' ) );?>" class="button-primary" name="gd_imex_hoods_submit" id="gd_imex_hoods_submit">
                    </td>
                    <td id="gd_ie_ex_files" class="gd-ie-files"></td>
                  </tr>
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
<script type="text/javascript">
var lTimer, lSec, lInt, hTimer, hSec, hInt;
jQuery(function(){
    jQuery('#gd_imex_locs_submit').click(function(){
        lSec = 1;
        var el = jQuery(this).closest('.postbox');
        jQuery(this).prop('disabled', true);
        
        window.clearInterval(lTimer);
        lTimer = window.setInterval(function() {
            jQuery(el).find(".gd_timer").gdlm_timer();
        }, 1000);
        
        var lChunk = parseInt(jQuery('#gd_chunk_size', el).val());
        var lTotal = parseInt(jQuery('.gd-imex-total', el).val());
        
        lChunk = Math.max(50, lChunk);
        lChunk = Math.min(100000, lChunk);
        lChunk = Math.min(lTotal, lChunk);
        
        var pages = Math.ceil(lTotal / lChunk);
        
        gdlm_export_locations(el, lTotal, lChunk, pages, 1);
    });

    function gdlm_export_locations(el, lTotal, lChunk, pages, page) {
        if (page < 2) {
            gd_progressbar(el, 0, '0% (0 / ' + lTotal + ') <i class="fa fa-refresh fa-spin"></i><?php echo esc_attr( __( 'Exporting...', 'geodirectory' ) );?>');
            jQuery(el).find('#gd_timer').text('00:00:01');
            jQuery('#gd_ie_ex_files', el).html('');
        }

        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            data: 'action=geodir_import_export&task=export_locations&_n=' + lChunk + '&_nonce=<?php echo $nonce;?>&_p=' + page,
            dataType : 'json',
            cache: false,
            beforeSend: function (jqXHR, settings) {},
            success: function( data ) {
                jQuery(el).find('input[type="submit"]').prop('disabled', false);
                
                if (typeof data == 'object') {
                    if (typeof data.error != 'undefined' && data.error) {
                        gd_progressbar(el, 0, '<i class="fa fa-warning"></i>' + data.error);
                        window.clearInterval(lTimer);
                    } else {
                        if (pages < page || pages == page) {
                            window.clearInterval(lTimer);
                            gd_progressbar(el, 100, '100% (' + lTotal + ' / ' + lTotal + ') <i class="fa fa-check"></i><?php echo esc_attr( __( 'Complete!', 'geodirectory' ) );?>');
                        } else {
                            var percentage = Math.round(((page * lChunk) / lTotal) * 100);
                            percentage = percentage > 100 ? 100 : percentage;
                            gd_progressbar(el, percentage, '' + percentage + '% (' + ( page * lChunk ) + ' / ' + lTotal + ') <i class="fa fa-refresh fa-spin"></i><?php esc_attr_e( 'Exporting...', 'geodirectory' );?>');
                        }
                        if (typeof data.files != 'undefined' && jQuery(data.files).length ) {
                            var obj_files = data.files;
                            var files = '';
                            for (var i in data.files) {
                                files += '<p>'+ obj_files[i].i +' <a class="gd-ie-file" href="' + obj_files[i].u + '" target="_blank">' + obj_files[i].u + '</a> (' + obj_files[i].s + ')</p>';
                            }
                            jQuery('#gd_ie_ex_files', el).append(files);
                            if (pages > page) {
                                return gdlm_export_locations(el, lTotal, lChunk, pages, (page + 1));
                            }
                            return true;
                        }
                    }
                }
            },
            error: function( data ) {
                jQuery(el).find('input[type="submit"]').prop('disabled', false);
                window.clearInterval(lTimer);
                return;
            },
            complete: function( jqXHR, textStatus  ) {
                return;
            }
        });
    }

    jQuery('#gd_imex_loc_sample').click(function(){
        if (jQuery('#gd_imex_loc_csv').val() != '') {
            window.location.href = jQuery('#gd_imex_loc_csv').val();
            return false;
        }
    });

    jQuery(".gd-imex-loc-upload").click(function () {
        var $this = this;
        var $cont = jQuery($this).closest('.gd-imex-box');
        clearInterval(lInt);
        lInt = setInterval(function () {
            if (jQuery($cont).find('.gd-imex-file').val()) {
                jQuery($cont).find('.gd-imex-btns').show();
            }
        }, 1000);
    });

    jQuery.fn.gdlm_timer = function() {
        lSec++;
        jQuery(this).text(lSec.toString().toHMS());
    }
    
    <?php if ($neighbourhood_active) { ?>
    jQuery('#gd_imex_hoods_submit').click(function(){
        hSec = 1;
        var el = jQuery(this).closest('.postbox');
        jQuery(this).prop('disabled', true);
        
        window.clearInterval(hTimer);
        hTimer = window.setInterval(function() {
            jQuery(el).find(".gd_timer").gdhm_timer();
        }, 1000);
        
        var hChunk = parseInt(jQuery('#gd_chunk_size', el).val());
        var hTotal = parseInt(jQuery('.gd-imex-total', el).val());
        
        hChunk = Math.max(50, hChunk);
        hChunk = Math.min(100000, hChunk);
        hChunk = Math.min(hTotal, hChunk);
        
        var pages = Math.ceil(hTotal / hChunk);
        
        gdlm_export_neighbourhoods(el, hTotal, hChunk, pages, 1);
    });
    
    function gdlm_export_neighbourhoods(el, hTotal, hChunk, pages, page) {
        if (page < 2) {
            gd_progressbar(el, 0, '0% (0 / ' + hTotal + ') <i class="fa fa-refresh fa-spin"></i><?php echo esc_attr( __( 'Exporting...', 'geodirectory' ) );?>');
            jQuery(el).find('#gd_timer').text('00:00:01');
            jQuery('#gd_ie_ex_files', el).html('');
        }

        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            data: 'action=geodir_import_export&task=export_hoods&_n=' + hChunk + '&_nonce=<?php echo $nonce;?>&_p=' + page,
            dataType : 'json',
            cache: false,
            beforeSend: function (jqXHR, settings) {},
            success: function( data ) {
                jQuery(el).find('input[type="submit"]').prop('disabled', false);
                
                if (typeof data == 'object') {
                    if (typeof data.error != 'undefined' && data.error) {
                        gd_progressbar(el, 0, '<i class="fa fa-warning"></i>' + data.error);
                        window.clearInterval(hTimer);
                    } else {
                        if (pages < page || pages == page) {
                            window.clearInterval(hTimer);
                            gd_progressbar(el, 100, '100% (' + hTotal + ' / ' + hTotal + ') <i class="fa fa-check"></i><?php echo esc_attr( __( 'Complete!', 'geodirectory' ) );?>');
                        } else {
                            var percentage = Math.round(((page * hChunk) / hTotal) * 100);
                            percentage = percentage > 100 ? 100 : percentage;
                            gd_progressbar(el, percentage, '' + percentage + '% (' + ( page * hChunk ) + ' / ' + hTotal + ') <i class="fa fa-refresh fa-spin"></i><?php esc_attr_e( 'Exporting...', 'geodirectory' );?>');
                        }
                        if (typeof data.files != 'undefined' && jQuery(data.files).length ) {
                            var obj_files = data.files;
                            var files = '';
                            for (var i in data.files) {
                                files += '<p>'+ obj_files[i].i +' <a class="gd-ie-file" href="' + obj_files[i].u + '" target="_blank">' + obj_files[i].u + '</a> (' + obj_files[i].s + ')</p>';
                            }
                            jQuery('#gd_ie_ex_files', el).append(files);
                            if (pages > page) {
                                return gdlm_export_neighbourhoods(el, hTotal, hChunk, pages, (page + 1));
                            }
                            return true;
                        }
                    }
                }
            },
            error: function( data ) {
                jQuery(el).find('input[type="submit"]').prop('disabled', false);
                window.clearInterval(hTimer);
                return;
            },
            complete: function( jqXHR, textStatus  ) {
                return;
            }
        });
    }
    
    jQuery('#gd_imex_hood_sample').click(function(){
        if (jQuery('#gd_imex_hood_csv').val() != '') {
            window.location.href = jQuery('#gd_imex_hood_csv').val();
            return false;
        }
    });
    
    jQuery(".gd-imex-hood-upload").click(function () {
        var $this = this;
        var $cont = jQuery($this).closest('.gd-imex-box');
        clearInterval(hInt);
        hInt = setInterval(function () {
            if (jQuery($cont).find('.gd-imex-file').val()) {
                jQuery($cont).find('.gd-imex-btns').show();
            }
        }, 1000);
    });
    
    jQuery.fn.gdhm_timer = function() {
        hSec++;
        jQuery(this).text(hSec.toString().toHMS());
    }
    <?php } ?>
});
</script>
<?php
}

/**
 * Retrieve locations data.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @param int $per_page Per page limit. Default 0.
 * @param int $page_no Page number. Default 0.
 * @return array Array of locations data.
 */
function geodir_location_imex_locations_data($per_page = 0, $page_no = 0) {
	$items = geodir_location_imex_get_locations($per_page, $page_no);

	$rows = array();
	
	if (!empty($items)) {
		$row = array();
		$row[] = 'location_id';
		$row[] = 'latitude';
		$row[] = 'longitude';
		$row[] = 'city';
		$row[] = 'city_slug';
		$row[] = 'region';
		//$row[] = 'region_slug';
		$row[] = 'country';
		//$row[] = 'country_slug';
		$row[] = 'city_meta';
		$row[] = 'city_desc';
		$row[] = 'region_meta';
		$row[] = 'region_desc';
		$row[] = 'country_meta';
		$row[] = 'country_desc';
		
		$rows[] = $row;
		
		$aregion_meta = $aregion_desc = $acountry_meta = $acountry_desc = array();
		
		foreach ($items as $item) {			
			$region_meta = $region_desc = $country_meta = $country_desc = '';
			
			if (($meta = trim($item->region_meta)) != '' && !isset($aregion_meta[$item->country_slug][$item->region_slug])) {
				$region_meta = $meta;
				$aregion_meta[$item->country_slug][$item->region_slug] = true;
			}
			
			if (($desc = trim($item->region_desc)) != '' && !isset($aregion_desc[$item->country_slug][$item->region_slug])) {
				$region_desc = $desc;
				$aregion_desc[$item->country_slug][$item->region_slug] = true;
			}
			
			if (($meta = trim($item->country_meta)) != '' && !isset($acountry_meta[$item->country_slug])) {
				$country_meta = $meta;
				$acountry_meta[$item->country_slug] = true;
			}
			
			if (($desc = trim($item->country_desc)) != '' && !isset($acountry_desc[$item->country_slug])) {
				$country_desc = $desc;
				$acountry_desc[$item->country_slug] = true;
			}
			
			$row = array();
			$row[] = $item->location_id;
			$row[] = $item->city_latitude;
			$row[] = $item->city_longitude;
			$row[] = stripslashes($item->city);
			$row[] = $item->city_slug;
			$row[] = stripslashes($item->region);
			//$row[] = $item->region_slug;
			$row[] = stripslashes($item->country);
			//$row[] = $item->country_slug;
			$row[] = stripslashes($item->city_meta);
			$row[] = stripslashes($item->city_desc);
			$row[] = stripslashes($region_meta);
			$row[] = stripslashes($region_desc);
			$row[] = stripslashes($country_meta);
			$row[] = stripslashes($country_desc);
			
			$rows[] = $row;
		}
	}
	return $rows;
}

/**
 * Retrieve neighbourhoods data.
 *
 * @since 1.4.5
 * @package GeoDirectory_Location_Manager
 *
 * @param int $per_page Per page limit. Default 0.
 * @param int $page_no Page number. Default 0.
 * @return array Array of neighbourhoods data.
 */
function geodir_location_imex_neighbourhoods_data($per_page = 0, $page_no = 0) {
    $items = geodir_location_imex_get_neighbourhoods($per_page, $page_no);

    $rows = array();

    if (!empty($items)) {
        $row = array();
        $row[] = 'neighbourhood_id';
        $row[] = 'neighbourhood_name';
        $row[] = 'neighbourhood_slug';
        $row[] = 'latitude';
        $row[] = 'longitude';
        $row[] = 'location_id';
        $row[] = 'city';
        $row[] = 'region';
        $row[] = 'country';
        
        $rows[] = $row;
                
        foreach ($items as $item) {           
            $row = array();
            $row[] = $item->hood_id;
            $row[] = stripslashes($item->hood_name);
            $row[] = $item->hood_slug;
            $row[] = $item->hood_latitude;
            $row[] = $item->hood_longitude;
            $row[] = (int)$item->hood_location_id > 0 ? $item->hood_location_id : '';
            $row[] = stripslashes($item->city);
            $row[] = stripslashes($item->region);
            $row[] = stripslashes($item->country);
            
            $rows[] = $row;
        }
    }
    return $rows;
}

/**
 * Counts the total locations.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @return int Total number of locations.
 */
function geodir_location_imex_count_locations() {
	global $wpdb;
	
	$query = "SELECT COUNT(location_id) FROM `" . POST_LOCATION_TABLE . "` WHERE country_slug != '' AND region_slug != '' AND city_slug != '' GROUP BY CONCAT('country_slug', '|', 'region_slug', '|', 'city_slug')";
	$value = (int)$wpdb->get_var($query);
	
	return $value;
}

/**
 * Counts the total neighbourhoods.
 *
 * @since 1.4.5
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @return int Total number of neighbourhoods.
 */
function geodir_location_imex_count_neighbourhoods() {
    global $wpdb;
    
    $query = "SELECT COUNT(h.hood_id) FROM `" . POST_NEIGHBOURHOOD_TABLE . "` AS h INNER JOIN `" . POST_LOCATION_TABLE . "` AS l ON l.location_id = h.hood_location_id";
    $value = (int)$wpdb->get_var($query);

    return $value;
}

/**
 * Get the locations data to export as csv file.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param int $per_page Number of records per page.
 * @param int $page_no Current page number. Default 0.
 * @return array Location data.
 */
function geodir_location_imex_get_locations($per_page, $page_no = 0) {
	global $wpdb;
	
	$page_no = max($page_no, 1);
	
	$fields = "l.location_id, l.country, l.region, l.city, l.country_slug, l.region_slug, l.city_slug, l.city_latitude, l.city_longitude, lscn.seo_title AS country_meta, lscn.seo_desc AS country_desc, lsre.seo_title AS region_meta, lsre.seo_desc AS region_desc, lsct.seo_title AS city_meta, lsct.seo_desc AS city_desc";
	
	$join = " LEFT JOIN `" . LOCATION_SEO_TABLE . "` AS lscn ON ( lscn.location_type = 'country' AND lscn.country_slug = l.country_slug )";
	$join .= " LEFT JOIN `" . LOCATION_SEO_TABLE . "` AS lsre ON ( lsre.location_type = 'region' AND lsre.country_slug = l.country_slug AND lsre.region_slug = l.region_slug )";
	$join .= " LEFT JOIN `" . LOCATION_SEO_TABLE . "` AS lsct ON ( lsct.location_type = 'city' AND lsct.country_slug = l.country_slug AND lsct.region_slug = l.region_slug AND lsct.city_slug = l.city_slug )";
	
	$where = "l.country_slug != '' AND l.region_slug != '' AND l.city_slug != ''";
	$groupby = "CONCAT(l.country_slug, '|', l.region_slug, '|', l.city_slug)";
	$orderby = "l.country ASC, l.region ASC, l.city ASC";
	
	if ($where != '') {
		$where = " WHERE " . $where;
	}
	if ($groupby != '') {
		$groupby = " GROUP BY " . $groupby;
	}
	if ($orderby != '') {
		$orderby = " ORDER BY " . $orderby;
	}
	
	$limit = (int)$per_page > 0 ? " LIMIT " . (($page_no - 1) * $per_page) . ", " . $per_page : '';
	
	$query = "SELECT " . $fields . " FROM `" . POST_LOCATION_TABLE . "` AS l " . $join . $where . $groupby . $orderby . $limit;
	$results = $wpdb->get_results($query);
	
	return $results;
}

/**
 * Get the neighbourhoods data to export as csv file.
 *
 * @since 1.4.5
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param int $per_page Number of records per page.
 * @param int $page_no Current page number. Default 0.
 * @return array Neighbourhoods data.
 */
function geodir_location_imex_get_neighbourhoods($per_page, $page_no = 0) {
    global $wpdb;

    $page_no = max($page_no, 1);

    $fields = "h.hood_id, h.hood_name, h.hood_slug, h.hood_latitude, h.hood_longitude, h.hood_location_id, l.location_id, l.location_id, l.country, l.region, l.city, l.country_slug, l.region_slug, l.city_slug, l.city_latitude, l.city_longitude";

    $join = " INNER JOIN `" . POST_LOCATION_TABLE . "` AS l ON l.location_id = h.hood_location_id";

    $where = "";
    $groupby = "";
    $orderby = "h.hood_id ASC";

    if ($where != '') {
        $where = " WHERE " . $where;
    }
    if ($groupby != '') {
        $groupby = " GROUP BY " . $groupby;
    }
    if ($orderby != '') {
        $orderby = " ORDER BY " . $orderby;
    }

    $limit = (int)$per_page > 0 ? " LIMIT " . (($page_no - 1) * $per_page) . ", " . $per_page : '';

    $query = "SELECT " . $fields . " FROM `" . POST_NEIGHBOURHOOD_TABLE . "` AS h " . $join . $where . $groupby . $orderby . $limit;
    $results = $wpdb->get_results($query);

    return $results;
}

/**
 * Get the location slug for location type and name.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $type Location type(city or region or country).
 * @param array $args Location input data. Default empty.
 * @return string Location slug.
 */
function geodir_get_location_by_slug($type, $args = array()) {
	global $wpdb;

	if (!in_array($type, array('city', 'region', 'country'))) {
		return NULL;
	}
	
	if ($type == 'city' && empty($args['city_slug']) && empty($args['city'])) {
		return NULL;
	}
	if ($type == 'region' && empty($args['region_slug']) && empty($args['region'])) {
		return NULL;
	}
	if ($type == 'country' && empty($args['country_slug']) && empty($args['country'])) {
		return NULL;
	}
	
	$params = array();
	$where = '';
	
	$fields = !empty($args['fields']) ? $args['fields'] : '*';
	$operator = !empty($args['sensitive']) ? '=' : 'LIKE';
	
	if (!empty($args['country_slug'])) {
		$params[] = $args['country_slug'];
		$where .= ' AND country_slug = %s';
	}
	
	if (!empty($args['region_slug']) && $type != 'country') {
		$params[] = $args['region_slug'];
		$where .= ' AND region_slug = %s';
	}
	
	if (!empty($args['city_slug']) && $type != 'country' && $type != 'region') {
		$params[] = $args['city_slug'];
		$where .= ' AND city_slug = %s';
	}
	
	if (!empty($args['country'])) {
		$params[] = $args['country'];
		$where .= ' AND country ' . $operator . ' %s';
	}
	
	if (!empty($args['region']) && $type != 'country') {
		$params[] = $args['region'];
		$where .= ' AND region ' . $operator . ' %s';
	}
	
	if (!empty($args['city']) && $type != 'country' && $type != 'region') {
		$params[] = $args['city'];
		$where .= ' AND city ' . $operator . ' %s';
	}
	
	$query = $wpdb->prepare("SELECT " . $fields . " FROM `" . POST_LOCATION_TABLE . "` WHERE 1 " . $where . " ORDER BY is_default DESC, location_id ASC", $params);
	$row = $wpdb->get_row($query);

	return $row;
}

/**
 * Save location data during location import.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param array $args Location data.
 * @param bool $has_seo True if location data contains seo data, otherwise false. Default false.
 * @return bool|int Location if record saved, otherwise false.
 */
function geodir_location_insert_city( $args, $has_seo = false ) {
	global $wpdb;
	
	if ( empty( $args ) )
		return false;
	
	if ( empty( $args['city'] ) || empty( $args['region'] ) || empty( $args['country'] ) || empty( $args['latitude'] ) || empty( $args['longitude'] ) )
		return NULL;
	
	$data = array();
	$data['city'] = $args['city'];
	$data['region'] = $args['region'];
	$data['country'] = $args['country'];
	$data['city_latitude'] = $args['latitude'];
	$data['city_longitude'] = $args['longitude'];
	
	$city_slug = !empty($args['city_slug']) ? $args['city_slug'] : $args['city'];
	
	$data['country_slug'] = empty($args['country_slug']) ? geodir_location_country_slug($args['country']) : $args['country_slug'];
	$data['region_slug'] = empty($args['region_slug']) ? geodir_location_region_slug($args['region'], $data['country_slug'], $data['country']) : $args['region_slug'];
	$data['city_slug'] = geodir_location_city_slug($city_slug);
	
	if ( !empty( $args['city_meta'] ) )
		$data['city_meta'] = mb_strlen( $args['city_meta'] ) > 140 ? mb_substr( $args['city_meta'], 0, 140 ) : $args['city_meta'];

	if ( !empty( $args['city_desc'] ) )
		$data['city_desc'] = mb_strlen( $args['city_desc'] ) > 100000 ? mb_substr( $args['city_desc'], 0, 100000 ) : $args['city_desc'];
	
	if ( !empty( $args['country_ISO2'] ) )
		$data['country_ISO2'] = $args['country_ISO2'];

	if ( !empty( $args['is_default'] ) )
		$data['is_default'] = $args['is_default'];
	
	if ( $wpdb->insert( POST_LOCATION_TABLE, $data ) ) {
		if ( $has_seo ) {
			$seo_data = array();
			$seo_data['country_slug'] = $data['country_slug'];
			
			if ( !empty( $args['country_meta'] ) || !empty( $args['country_desc'] ) ) {				
				$seo_data['location_type'] = 'country';
				
				if ( !empty( $args['country_meta'] ) )
					$seo_data['seo_title'] = mb_strlen( $args['country_meta'] ) > 140 ? mb_substr( $args['country_meta'], 0, 140 ) : $args['country_meta'];

				if ( !empty( $args['country_desc'] ) )
					$seo_data['seo_desc'] = mb_strlen( $args['country_desc'] ) > 100000 ? mb_substr( $args['country_desc'], 0, 100000 ) : $args['country_desc'];
				
				$return = geodir_location_imex_handle_seo_data( 'country', $seo_data );
			}
			
			$seo_data['region_slug'] = $data['region_slug'];
			
			if ( !empty( $args['region_meta'] ) || !empty( $args['region_desc'] ) ) {				
				$seo_data['location_type'] = 'region';
				
				if ( !empty( $args['region_meta'] ) )
					$seo_data['seo_title'] = mb_strlen( $args['region_meta'] ) > 140 ? mb_substr( $args['region_meta'], 0, 140 ) : $args['region_meta'];

				if ( !empty( $args['region_desc'] ) )
					$seo_data['seo_desc'] = mb_strlen( $args['region_desc'] ) > 100000 ? mb_substr( $args['region_desc'], 0, 100000 ) : $args['region_desc'];
				
				$return = geodir_location_imex_handle_seo_data( 'region', $seo_data );
			}
			
			$seo_data['city_slug'] = $data['city_slug'];
			
			if ( !empty( $data['city_meta'] ) || !empty( $data['city_desc'] ) ) {				
				if ( !empty( $data['city_meta'] ) )
					$seo_data['seo_title'] = $data['city_meta'];

				if ( !empty( $data['city_desc'] ) )
					$seo_data['seo_desc'] = $data['city_desc'];
				
				$return = geodir_location_imex_handle_seo_data( 'city', $seo_data );
			}
		}
		
		return (int)$wpdb->insert_id;
	}
	
	return false;
}

/**
 * Update location data during location import.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param array $args Location data.
 * @param bool $has_seo True if location data contains seo data, otherwise false. Default false.
 * @param object $old_location Old location data before updated. Default empty.
 * @return bool True if record update, otherwise false.
 */
function geodir_location_update_city( $args, $has_seo = false, $old_location = array() ) {
	global $wpdb;
	
	if ( empty( $args ) )
		return false;
	
	if ( empty( $args['location_id'] ) ) {
		return false;
	}
	
	$location_id = (int)$args['location_id'];
	
	if ( empty( $old_location ) ) {
		$old_location = geodir_get_location_by_id( '', $location_id );
	}
	
	if ( empty( $old_location ) ) {
		return false;
	}
	
	$data = array();
	if ( !empty( $args['city'] ) && $args['city'] != $old_location->city )
		$data['city'] = $args['city'];
	
	if ( !empty( $args['region'] ) && $args['region'] != $old_location->region )
		$data['region'] = $args['region'];
	
	if ( !empty( $args['country'] ) && $args['country'] != $old_location->country )
		$data['country'] = $args['country'];
	
	if ( !empty( $args['latitude'] ) && $args['latitude'] != $old_location->city_latitude )
		$data['city_latitude'] = $args['latitude'];
	
	if ( !empty( $args['longitude'] ) && $args['longitude'] != $old_location->city_longitude )
		$data['city_longitude'] = $args['longitude'];
		
	if (!empty($data['country']) && empty($args['country_slug'])) {
		$args['country_slug'] = geodir_location_country_slug($data['country']);
	}
	
	if ( !empty($args['country_slug']) && $args['country_slug'] != $old_location->country_slug )
		$data['country_slug'] = $args['country_slug'];
	
	$country = !empty($data['country']) ? $data['country'] : $old_location->country;
	$country_slug = !empty($data['country_slug']) ? $data['country_slug'] : $old_location->country_slug;
	
	if (!empty($data['region']) && empty($args['region_slug'])) {
		$args['region_slug'] = geodir_location_region_slug($data['region'], $country_slug, $country);
	}
	
	if ( !empty($args['region_slug']) && $args['region_slug'] != $old_location->region_slug )
		$data['region_slug'] = $args['region_slug'];
	
	$region_slug = !empty($data['region_slug']) ? $data['region_slug'] : $old_location->region_slug;
	
	if ( !empty($args['city_slug']) && $args['city_slug'] != $old_location->city_slug )
		$data['city_slug'] = geodir_location_city_slug($args['city_slug'], $location_id);
	
	$city_slug = !empty($data['city_slug']) ? $data['city_slug'] : $old_location->city_slug;
	
	if ( !empty( $args['city_meta'] ) && $args['city_meta'] != $old_location->city_meta )
		$data['city_meta'] = mb_strlen( $args['city_meta'] ) > 140 ? mb_substr( $args['city_meta'], 0, 140 ) : $args['city_meta'];

	if ( !empty( $args['city_desc'] ) && $args['city_desc'] != $old_location->city_desc )
		$data['city_desc'] = mb_strlen( $args['city_desc'] ) > 100000 ? mb_substr( $args['city_desc'], 0, 100000 ) : $args['city_desc'];
	
	if ( !empty( $args['country_ISO2'] ) && $args['country_ISO2'] != $old_location->country_ISO2 )
		$data['country_ISO2'] = $args['country_ISO2'];

	if ( !empty( $args['is_default'] ) && $args['is_default'] != $old_location->is_default )
		$data['is_default'] = $args['is_default'];
	
	if ( !empty( $data ) || ( !empty( $args['region_meta'] ) || !empty( $args['region_desc'] ) || !empty( $args['country_meta'] ) || !empty( $args['country_desc'] ) ) ) {
		$updated = !empty( $data ) ? (int)$wpdb->update( POST_LOCATION_TABLE, $data, array( 'location_id' => $location_id ) ) : false;
		if ($updated) {
			$new_location = geodir_get_location_by_id( '', $location_id );
			geodir_location_on_update_location($new_location, $old_location);
		}
		
		if ( $has_seo ) {
			$seo_data = array();
			$seo_data['country_slug'] = $country_slug;
			
			if ( !empty( $args['country_meta'] ) || !empty( $args['country_desc'] ) ) {				
				$seo_data['location_type'] = 'country';
				
				if ( !empty( $args['country_meta'] ) )
					$seo_data['seo_title'] = mb_strlen( $args['country_meta'] ) > 140 ? mb_substr( $args['country_meta'], 0, 140 ) : $args['country_meta'];

				if ( !empty( $args['country_desc'] ) )
					$seo_data['seo_desc'] = mb_strlen( $args['country_desc'] ) > 100000 ? mb_substr( $args['country_desc'], 0, 100000 ) : $args['country_desc'];
				
				$return = geodir_location_imex_handle_seo_data( 'country', $seo_data );
			}
			
			$seo_data['region_slug'] = $region_slug;
			
			if ( !empty( $args['region_meta'] ) || !empty( $args['region_desc'] ) ) {				
				$seo_data['location_type'] = 'region';
				
				if ( !empty( $args['region_meta'] ) )
					$seo_data['seo_title'] = mb_strlen( $args['region_meta'] ) > 140 ? mb_substr( $args['region_meta'], 0, 140 ) : $args['region_meta'];

				if ( !empty( $args['region_desc'] ) )
					$seo_data['seo_desc'] = mb_strlen( $args['region_desc'] ) > 100000 ? mb_substr( $args['region_desc'], 0, 100000 ) : $args['region_desc'];
				
				$return = geodir_location_imex_handle_seo_data( 'region', $seo_data );
			}
			
			$seo_data['city_slug'] = $city_slug;
			
			if ( !empty( $data['city_meta'] ) || !empty( $data['city_desc'] ) ) {				
				if ( !empty( $data['city_meta'] ) )
					$seo_data['seo_title'] = $data['city_meta'];

				if ( !empty( $data['city_desc'] ) )
					$seo_data['seo_desc'] = $data['city_desc'];
				
				$return = geodir_location_imex_handle_seo_data( 'city', $seo_data );
			}
		}
	}
	
	return true;
}

/**
 * Get the city slug for city name.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $name City name.
 * @param int $location_id Location id. Default 0.
 * @return string City slug.
 */
function geodir_location_city_slug( $name, $location_id = 0 ) {
	global $wpdb;
	
	$slug = sanitize_title( $name );
	
	if ( (int)$location_id > 0 ) {
		$check_sql = "SELECT city_slug FROM " . POST_LOCATION_TABLE . " WHERE ( city = %s OR city_slug = %s ) AND location_id != %d LIMIT 1";
		$slug_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $name, $slug, $location_id ) );
	} else {
		$check_sql = "SELECT city_slug FROM " . POST_LOCATION_TABLE . " WHERE ( city = %s OR city_slug = %s ) LIMIT 1";
		$slug_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $name, $slug ) );
	}

	if ( $slug_check ) {
		$suffix = 1;
		do {
			$alt_slug = _truncate_post_slug( $slug, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
			
			if ( (int)$location_id > 0 )
				$slug_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $name, $alt_slug, $location_id ) );
			else
				$slug_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $name, $alt_slug ) );
			
			$suffix++;
		} while ( $slug_check );
		
		$slug = $alt_slug;
	}
	
	return $slug;
}

/**
 * Get the country slug for country name.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $name Country name.
 * @return string Country slug.
 */
function geodir_location_country_slug( $name ) {
	global $wpdb;
	
	$query = $wpdb->prepare("SELECT country_slug FROM " . POST_LOCATION_TABLE . " WHERE country LIKE %s ORDER BY is_default DESC LIMIT 1", array($name));
	if ($slug = $wpdb->get_var($query)) {
		return $slug;
	}
	
	$slug = sanitize_title($name);
		
	return $slug;
}

/**
 * Get the region slug for region name.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $name Region name.
 * @param string $country_slug Country slug. Default empty.
 * @param string $country Country name. Default empty.
 * @return string Region slug.
 */
function geodir_location_region_slug( $name, $country_slug = '', $country = '' ) {
	global $wpdb;
	
	if ($country_slug != '') {
		$query = $wpdb->prepare("SELECT region_slug FROM " . POST_LOCATION_TABLE . " WHERE region LIKE %s AND country_slug = %s ORDER BY is_default DESC LIMIT 1", array($name, $country_slug));
		if ($slug = $wpdb->get_var($query)) {
			return $slug;
		}
	}
	
	if ($country != '') {
		$query = $wpdb->prepare("SELECT region_slug FROM " . POST_LOCATION_TABLE . " WHERE region LIKE %s AND country LIKE %s ORDER BY is_default DESC LIMIT 1", array($name, $country));
		if ($slug = $wpdb->get_var($query)) {
			return $slug;
		}
	}
	
	$slug = sanitize_title($name);
		
	return $slug;
}

/**
 * Save location seo meta description during location import.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $location_type Location type (city or region or country).
 * @param array $seo_data Location seo data.
 * @return bool True if any record updated, otherwise false.
 */
function geodir_location_imex_handle_seo_data( $location_type, $seo_data ) {
	global $wpdb;
	
	$country_slug = isset( $seo_data['country_slug'] ) ? $seo_data['country_slug'] : '';
	$region_slug = isset( $seo_data['region_slug'] ) ? $seo_data['region_slug'] : '';
	$city_slug = isset( $seo_data['city_slug'] ) ? $seo_data['city_slug'] : '';
	
	$slug = '';
	if ( $location_type == 'city' ) {
		$slug = isset( $seo_data['city_slug'] ) ? $seo_data['city_slug'] : '';
	} else if ( $location_type == 'region' ) {
		$slug = isset( $seo_data['region_slug'] ) ? $seo_data['region_slug'] : '';
	} else if ( $location_type == 'country' ) {
		$slug = isset( $seo_data['country_slug'] ) ? $seo_data['country_slug'] : '';
	} else {
		return false;
	}
	
	if ( $slug == '' ) {
		return false;
	}
	
	$today = date_i18n( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
	$seo_data['location_type'] = $location_type;
	
	$seo = geodir_location_seo_by_slug($slug, $location_type, $country_slug, $region_slug);
	
	if ( !empty( $seo ) ) {
		$seo_data['date_updated'] = $today;
		
		$wpdb->update( LOCATION_SEO_TABLE, $seo_data, array( 'seo_id' => (int)$seo->seo_id ) );
	} else {
		$seo_data['date_created'] = $today;
		
		$wpdb->insert( LOCATION_SEO_TABLE, $seo_data );
	}
	
	return true;
}

/**
 * Updates listings location data on location update.
 *
 * @since 1.4.4
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix GeoDirectory plugin table prefix.
 * @global array $gd_post_types GeoDirectory custom post types.
 *
 * @param object $new_location New location info after updated.
 * @param object $old_location Old location info before updated.
 * @return bool True if any record updated, otherwise false.
 */
function geodir_location_on_update_location($new_location, $old_location) {
	global $wpdb, $plugin_prefix, $gd_post_types;
	if (empty($new_location) || empty($old_location)) {
		return false;
	}
	
	$location_id = $new_location->location_id;

	if (empty($gd_post_types)) {
		$gd_post_types = geodir_get_posttypes();
	}
	
	$data = array();
	$data['post_country'] = $new_location->country;
	$data['post_region'] = $new_location->region;
	$data['post_city'] = $new_location->city;
	$data['post_locations'] = '[' . $new_location->city_slug . '],[' . $new_location->region_slug . '],[' . $new_location->country_slug . ']';
	
	foreach ($gd_post_types as $i => $post_type) {
		$table = $plugin_prefix . $post_type . '_detail';
		$updated = (int)$wpdb->update($table, $data, array('post_location_id' => $location_id));
	}
	
	return true;
}

/**
 * Get neighbourhood info by name and location id.
 *
 * @since 1.4.5
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $hood_name Neighbour hood name.
 * @param int $location_id The location id.
 * @return array|mixed Neighbourhood info.
 */
function geodir_location_neighbourhood_by_name_loc_id($hood_name, $location_id = 0) {
    global $wpdb;

    if (empty($hood_name)) {
        return NULL;
    }

    $where = '';
    if ($location_id > 0) {
        $where .= "AND h.hood_location_id = '" . (int)$location_id . "'";
    }

    $sql = $wpdb->prepare("SELECT h.*, l.*, h.hood_name AS neighbourhood, h.hood_slug AS neighbourhood_slug FROM `" . POST_NEIGHBOURHOOD_TABLE . "` AS h INNER JOIN `" . POST_LOCATION_TABLE . "` AS l ON l.location_id = h.hood_location_id WHERE `h`.`hood_name` LIKE %s " . $where . " ORDER BY h.hood_id ASC", array($hood_name));
    $result = $wpdb->get_row($sql);

    return $result;
}

/**
 * Save neighbourhood data.
 *
 * @since 1.4.5
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param array $data Neighbourhood data.
 * @return array Neighbourhood info.
 */
function geodir_location_insert_update_neighbourhood($data) {
    global $wpdb;

    if (empty($data) || empty($data['hood_name'])) {
        return false;
    }
    
    $hood_id = 0;
    if (empty($data['hood_id'])) {
        $data['hood_slug'] = !empty($data['hood_slug']) ? $data['hood_slug'] : $data['hood_name'];
        $data['hood_slug'] = geodir_location_neighbourhood_slug($data['hood_slug']);
        
        if ($wpdb->insert(POST_NEIGHBOURHOOD_TABLE, $data)) {
            $hood_id = (int)$wpdb->insert_id;
        }
    } else {
        $data['hood_slug'] = !empty($data['hood_slug']) ? $data['hood_slug'] : $data['hood_name'];
        $data['hood_slug'] = geodir_location_neighbourhood_slug($data['hood_slug'], $data['hood_id']);
        
        $wpdb->update(POST_NEIGHBOURHOOD_TABLE, $data, array('hood_id' => (int)$data['hood_id']));
        $hood_id = (int)$data['hood_id'];
    }

    $result = array();
    if ($hood_id > 0) {
        $result = geodir_location_get_neighbourhood_by_id($hood_id);
    }

    return $result;
}

/**
 * Get the neighbourhood slug for name.
 *
 * @since 1.4.5
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $name Neighbourhood name.
 * @param int $hood_id Neighbourhood id. Default 0.
 * @return string Neighbourhood slug.
 */
function geodir_location_neighbourhood_slug( $name, $hood_id = 0 ) {
    global $wpdb;

    $slug = sanitize_title( $name );

    if ( (int)$hood_id > 0 ) {
        $check_sql = "SELECT hood_slug FROM " . POST_NEIGHBOURHOOD_TABLE . " WHERE hood_slug = %s AND hood_id != %d LIMIT 1";
        $slug_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug, $hood_id ) );
    } else {
        $check_sql = "SELECT hood_slug FROM " . POST_NEIGHBOURHOOD_TABLE . " WHERE hood_slug = %s LIMIT 1";
        $slug_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug ) );
    }

    if ( $slug_check ) {
        $suffix = 1;
        do {
            $alt_slug = _truncate_post_slug( $slug, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
            
            if ( (int)$hood_id > 0 )
                $slug_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_slug, $hood_id ) );
            else
                $slug_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_slug ) );
            
            $suffix++;
        } while ( $slug_check );
        
        $slug = $alt_slug;
    }

    return $slug;
}
