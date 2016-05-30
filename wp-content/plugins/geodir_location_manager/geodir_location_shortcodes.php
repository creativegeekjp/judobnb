<?php
/**
 * Contains functions related to Location Manager plugin update.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


// Add Shortcode for current location name
/**
 * @return string
 * @global object $gd_session GeoDirectory Session object.
 *
 */
function geodir_current_loc_shortcode() {
	global $gd_session;
	$output = geodir_get_current_location(array('echo' => false));
	
	if (trim($output) == '' && ($gd_session->get('my_location') || ($gd_session->get('user_lat') && $gd_session->get('user_lon')))) {
		$output = __('Near Me', 'geodirlocation');
	}
	
	return $output;
}
add_shortcode( 'gd_current_location_name', 'geodir_current_loc_shortcode' );

add_shortcode( 'geodir_location_current', 'geodir_current_location_sc' );
/**
 * @param $args
 * @param string $caption
 * @return string
 */
function geodir_current_location_sc( $args, $caption = '' ) {
	$args['echo'] = false;
	
	$content = geodir_get_current_location( $args ); //its in geodir_location_template_tags.php
	
	return $content;
}


add_shortcode( 'gd_location_switcher', 'geodir_location_switcher_sc' ) ;
add_shortcode( 'geodir_location_switcher', 'geodir_location_switcher_sc' ) ;
/**
 * @param $args
 * @param string $caption
 * @return string
 */
function geodir_location_switcher_sc( $args, $caption = '' ) {
	ob_start();
	geodir_get_location_switcher( $args );
	$content = ob_get_clean();
	//ob_end_clean();
	return $content;
}

add_shortcode( 'gd_location_list', 'geodir_location_list_sc' ) ;
add_shortcode( 'geodir_location_list', 'geodir_location_list_sc' ) ;
/**
 * @param $args
 * @param string $caption
 * @return string
 */
function geodir_location_list_sc( $args, $caption = '' ) {
	ob_start();
	geodir_get_location_list( $args );
	$content = ob_get_clean();
	//ob_end_clean();
	return $content;
}

add_shortcode( 'geodir_location_tab_switcher', 'geodir_location_tab_switcher_sc' ) ;
add_shortcode( 'gd_location_tab_switcher', 'geodir_location_tab_switcher_sc' ) ;
/**
 * @param $args
 * @param string $caption
 * @return string
 */
function geodir_location_tab_switcher_sc( $args, $caption = '' ) {
	$args['echo'] = false;
	
	$content = geodir_location_tab_switcher( $args );
	
	return '<span class="geodir_shortcode_location_tab_container">' . $content . '</span>';
}


add_shortcode( 'gd_location_description', 'geodir_sc_location_description' );
/**
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param $atts
 * @return null|string
 *
 * @global object $wp WordPress object.
 */
function geodir_sc_location_description( $atts ) {
	global $wpdb, $wp;

	$gd_country = isset( $wp->query_vars['gd_country'] ) ? $wp->query_vars['gd_country'] : '';
	$gd_region  = isset( $wp->query_vars['gd_region'] ) ? $wp->query_vars['gd_region'] : '';
	$gd_city    = isset( $wp->query_vars['gd_city'] ) ? $wp->query_vars['gd_city'] : '';

	$location_title = '';
	$seo_title      = '';
	$seo_desc       = '';
	if ( $gd_city ) {
		$info = geodir_city_info_by_slug( $gd_city, $gd_country, $gd_region );
		if ( ! empty( $info ) ) {
			$location_title = $info->city;
			$seo_title      = $info->city_meta;
			$seo_desc       = $info->city_desc;
		}
	} else if ( ! $gd_city && $gd_region ) {
		$info = geodir_location_seo_by_slug( $gd_region, 'region', $gd_country );
		if ( ! empty( $info ) ) {
			$seo_title      = $info->seo_title;
			$seo_desc       = $info->seo_desc;
			$location_title = $wpdb->get_var( $wpdb->prepare( "SELECT region FROM " . POST_LOCATION_TABLE . " WHERE region_slug!='' AND region_slug=%s ORDER BY location_id ASC", array( $gd_region ) ) );
		}
	} else if ( ! $gd_city && ! $gd_region && $gd_country ) {
		$info = geodir_location_seo_by_slug( $gd_country, 'country' );
		if ( ! empty( $info ) ) {
			$seo_title      = $info->seo_title;
			$seo_desc       = $info->seo_desc;
			$location_title = $wpdb->get_var( $wpdb->prepare( "SELECT country FROM " . POST_LOCATION_TABLE . " WHERE country_slug!='' AND country_slug=%s ORDER BY location_id ASC", array( $gd_country ) ) );
		}
	}
	$location_desc = $seo_desc;
	if ( $location_desc == '' ) {
		return null;
	}

	ob_start();
	$location_desc = stripslashes_deep( $location_desc );
	echo '<div class="geodir-category-list-in clearfix geodir-location-desc">' . $location_desc . '</div>';
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

add_shortcode( 'gd_location_neighbourhood', 'geodir_sc_location_neighbourhood' );
add_shortcode( 'gd_location_neighborhood', 'geodir_sc_location_neighbourhood' );
 /**
 * Get the location neighbourhoods.
 *
 * @since 1.0.0
 * @since 1.4.4 Permalink added for location neighbourhood urls.
 *
 * @param array $atts Array of arguements to filter listings.
 * @return string Listings HTML content.
 */
function geodir_sc_location_neighbourhood($atts) {
	ob_start();
	
	$location_id = '';
	$location_terms = geodir_get_current_location_terms();
	if (isset($location_terms['gd_city']) && !empty($location_terms['gd_city'])) {
		$gd_city = $location_terms['gd_city'];
		$gd_region = isset($location_terms['gd_region']) ? $location_terms['gd_region'] : '';
		$gd_country = isset($location_terms['gd_country']) ? $location_terms['gd_country'] : '';
		
		$location_info = geodir_city_info_by_slug($gd_city, $gd_country, $gd_region);
		$location_id = !empty($location_info) ? $location_info->location_id : 0;
	}
	
	$gd_neighbourhoods = $location_id ? geodir_get_neighbourhoods($location_id) : NULL;

	if (!empty($gd_neighbourhoods)) {
		?>
		<div id="geodir-category-list">
			<div class="geodir-category-list-in clearfix">
				<div class="geodir-cat-list clearfix">
				<?php
					$hood_count = 0;
					echo '<ul>';     
					foreach ($gd_neighbourhoods as $gd_neighbourhood) {
						if ($hood_count%15 == 0) {
							echo '</ul><ul>';
						}

						$neigtbourhood_name = __($gd_neighbourhood->hood_name, 'geodirlocation');
						$neigtbourhood_url = geodir_location_get_neighbourhood_url($gd_neighbourhood->hood_slug, true);
						
						echo '<li><a href="' . esc_url($neigtbourhood_url) . '">' . $neigtbourhood_name . '</a></li>';
						$hood_count++;
					}
					echo '</ul>';
					?>
				</div>
			</div>
		</div>
	<?php
	}
	
	$output = ob_get_contents();
	ob_end_clean();
	
	return $output;
}

add_shortcode( 'gd_popular_location', 'geodir_sc_popular_location' );
/**
 *
 * @global object $wp WordPress object.
 *
 * @param $atts
 * @return string
 */
function geodir_sc_popular_location( $atts ) {
	global $wp;

	$location_terms = geodir_get_current_location_terms(); //locations in sessions

	// get all the cities in current region
	$args = array(
		'what'                     => 'city',
		'city_val'                 => '',
		'region_val'               => '',
		'country_val'              => '',
		'country_non_restricted'   => '',
		'region_non_restricted'    => '',
		'city_non_restricted'      => '',
		'filter_by_non_restricted' => true,
		'compare_operator'         => 'like',
		'country_column_name'      => 'country_slug',
		'region_column_name'       => 'region_slug',
		'city_column_name'         => 'city_slug',
		'location_link_part'       => true,
		'order_by'                 => ' asc ',
		'no_of_records'            => '',
		'format'                   => array(
			'type'                   => 'list',
			'container_wrapper'      => 'ul',
			'container_wrapper_attr' => '',
			'item_wrapper'           => 'li',
			'item_wrapper_attr'      => ''
		)
	);
	if ( ! empty( $location_terms ) ) {

		if ( isset( $location_terms['gd_region'] ) && $location_terms['gd_region'] != '' ) {
			$args['region_val']  = $location_terms['gd_region'];
			$args['country_val'] = $location_terms['gd_country'];
		} else if ( isset( $location_terms['gd_country'] ) && $location_terms['gd_country'] != '' ) {
			$args['country_val'] = $location_terms['gd_country'];
		}
	}
	ob_start();
	echo '<div class="geodir-sc-popular-location">';
	echo $geodir_cities_list = geodir_get_location_array( $args, false );
	echo '</div>';
	$output = ob_get_contents();
	ob_end_clean();
	return $output;

}

add_shortcode( 'gd_popular_in_neighbourhood', 'geodir_sc_popular_in_neighbourhood' );
add_shortcode( 'gd_popular_in_neighborhood', 'geodir_sc_popular_in_neighbourhood' );
/**
 *
 * @global object $wpdb WordPress Database object.
 * @global object $gd_session GeoDirectory Session object.
 *
 * @param $atts
 * @return string
 */
function geodir_sc_popular_in_neighbourhood( $atts ) {
	ob_start();
	$defaults = array(
		'post_type'           => 'gd_place',
		'category'            => '0',
		'list_sort'           => 'latest',
		'post_number'         => 5,
		'layout'              => 'gridview_onehalf',
		'character_count'     => 20,
		'add_location_filter' => 1, // Not used
	);

	$params = shortcode_atts( $defaults, $atts );

	/**
	 * Being validating $params
	 */

	// Check we have a valid post_type
	if ( ! ( gdsc_is_post_type_valid( $params['post_type'] ) ) ) {
		$params['post_type'] = 'gd_place';
	}

	// Manage the entered categories
	if ( 0 != $params['category'] || '' != $params['category'] ) {
		$params['category'] = gdsc_manage_category_choice( $params['post_type'], $params['category'] );
	}

	// Validate our sorting choice
	$params['list_sort'] = gdsc_validate_sort_choice( $params['list_sort'] );

	// Post_number needs to be a positive integer
	$params['post_number'] = absint( $params['post_number'] );
	if ( 0 == $params['post_number'] ) {
		$params['post_number'] = 1;
	}

	// Validate our layout choice
	// Outside of the norm, I added some more simple terms to match the existing
	// So now I just run the switch to set it properly.
	$params['layout'] = gdsc_validate_layout_choice( $params['layout'] );

	// Validate character_count
	$params['character_count'] = absint( $params['character_count'] );
	if ( 20 > $params['character_count'] ) {
		$params['character_count'] = 20;
	}

	/**
	 * End validation
	 */

	global $wpdb, $post, $geodir_post_type, $gd_session;

	if ( $geodir_post_type == '' ) {
		$geodir_post_type = 'gd_place';
	}

	$all_postypes = geodir_get_posttypes();

	$location_id = '';

	$not_in_array = array();

	if ( geodir_is_page( 'detail' ) || geodir_is_page( 'preview' ) || geodir_is_page( 'add-listing' ) ) {
		if ( isset( $post->post_type ) && $post->post_type == $params['post_type'] && isset( $post->post_location_id ) ) {
			$not_in_array[] = $post->ID;
			$location_id = $post->post_location_id;
		}
	} elseif ( in_array( $geodir_post_type, $all_postypes ) && $geodir_post_type == $params['post_type'] ) {
		if ( $gd_ses_city = $gd_session->get('gd_city') ) {
			$location_id = $wpdb->get_var( $wpdb->prepare( "SELECT location_id FROM " . POST_LOCATION_TABLE . " WHERE city_slug = %s", array( $gd_ses_city ) ) );
		} else {
			$default_location = geodir_get_default_location();
			$location_id      = $default_location->location_id;
		}
	}

	$gd_neighbourhoods = geodir_get_neighbourhoods( $location_id );

	if ( $gd_neighbourhoods ) {
		?>

		<div class="geodir_locations geodir_location_listing">
			<?php
			$hood_slug_arr = array();
			if ( ! empty( $gd_neighbourhoods ) ) {
				foreach ( $gd_neighbourhoods as $hoodslug ) {
					$hood_slug_arr[] = $hoodslug->hood_slug;
				}
			}

			$query_args = array(
				'posts_per_page'   => $params['post_number'],
				'is_geodir_loop'   => true,
				'post__not_in'     => $not_in_array,
				'gd_neighbourhood' => $hood_slug_arr,
				'gd_location'      => ( $params['add_location_filter'] ) ? true : false,
				'post_type'        => $params['post_type'],
				'order_by'         => $params['list_sort'],
				'excerpt_length'   => $params['character_count'],
			);

			if ( $params['category'] != 0 || $params['category'] != '' ) {

				$category_taxonomy = geodir_get_taxonomies( $params['post_type'] );

				$tax_query = array(
					'taxonomy' => $category_taxonomy[0],
					'field'    => 'id',
					'terms'    => $params['category']
				);

				$query_args['tax_query'] = array( $tax_query );
			}

			global $gridview_columns;

			query_posts( $query_args );

			if ( strstr( $params['layout'], 'gridview' ) ) {

				$listing_view_exp = explode( '_', $params['layout'] );

				$gridview_columns = $params['layout'];

				$layout = $listing_view_exp[0];

			}

			$template = apply_filters( "geodir_template_part-listing-listview", geodir_plugin_path() . '/geodirectory-templates/listing-listview.php' );

			include( $template );

			wp_reset_query();


			?>

		</div>

	<?php
	}
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}







?>