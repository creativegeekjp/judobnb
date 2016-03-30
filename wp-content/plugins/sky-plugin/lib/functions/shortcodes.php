<?php

// Label
function vh_gap($atts, $content = null, $code) {
	extract( shortcode_atts( array(
		'height' => 10,
	), $atts ) );

	$output = '<div class="gap" style="line-height: ' . absint($height) . 'px; height: ' . absint($height) . 'px;"></div>';

	return $output;
}
add_shortcode('vh_gap', 'vh_gap');

// Featured properties
function vh_featured_properties($atts, $content = null, $code) {
	extract( shortcode_atts( array(
		'listing_ptitle'     => '',
		'listing_stitle'     => '',
		'listing_tags'       => '',
		'listing_limit'      => '',
		'module_type'        => '',
		'listing_post_type'  => 'gd_place'
	), $atts ) );

	global $post, $wpdb;
	$count = $loop_count = 1;
	$random = rand();
	$output = $new_title = '';

	if ( $listing_limit == '' ) {
		$limit = -1;
	} else {
		$limit = $listing_limit;
	}

	if ( $module_type == 'author-dash' ) {
		$author_id = get_query_var( 'author' );
		if ( $author_id == "" ) {
			$author_id = '1';
		}
		$listing_count = 0;
		$where_extra = '';
		$custom_tables = '';
		$custom_where = '';
		$i = 1;
		$geodir_post_types = get_geodir_custom_post_types();
		$queryresults = array();
		foreach ($geodir_post_types as $custom_post_value) {
			$custom_tables = $wpdb->prefix .  "geodir_" . $custom_post_value . "_detail";
			$custom_where = "post_status = 'publish'";
			$where_extra = " AND post_id IN(".vh_get_user_listing_id( $author_id, $custom_post_value ).")";

			$querystr = "SELECT * FROM " . $custom_tables . " WHERE ".$custom_where.$where_extra;
			$queryresults = array_merge($queryresults, $wpdb->get_results($querystr));
			$listing_count = $listing_count+intval(vh_get_user_listings( $author_id, $custom_post_value));
		}
		 
		$listing_ptitle = $listing_count.' '.__('listings', 'vh');
		$listing_stitle = __('created by', 'vh').' '.get_the_author_meta( 'display_name', $author_id );
	} else {
		$where_extra = " AND ( post_tags LIKE '%".$listing_tags."%' OR is_featured='1' )";
		$custom_tables = $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail";
		$custom_where = "post_status = 'publish'";

		$querystr = "SELECT * FROM " . $custom_tables . " WHERE ".$custom_where.$where_extra;
		$queryresults = $wpdb->get_results($querystr);
	}

	$count_class = '';
	if ( count($queryresults) == 1 ) {
		$count_class = 'one';
	} elseif ( count($queryresults) == 2 ) {
		$count_class = 'two';
	}

	if( !empty($queryresults) ) {
		$output .= '<div class="featured-properties-main">';

		$output .= '
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				if ( jQuery(".featured-properties-carousel-container").length ) {
					jQuery(".featured-properties-carousel-container").jcarousel({
						wrap: "circular",
						animation: {
							duration: 0
						}
					}).on("jcarousel:scrollend", function(event, carousel) {
						setTimeout(function() {
							jQuery(".featured-properties-carousel-container").removeClass("scrolling");
						}, 700);
					});

					jQuery(".featured-properties-controls .listing-next").click(function() {
						if ( !jQuery(".featured-properties-carousel-container").hasClass("scrolling") ) {
							jQuery(".featured-properties-carousel-container").jcarousel("scroll", "+=1");
							jQuery(".featured-properties-carousel-container").hide().fadeIn(700);
						}
						jQuery(".featured-properties-carousel-container").addClass("scrolling");
					});

					jQuery(".featured-properties-controls .listing-prev").click(function() {
						if ( !jQuery(".featured-properties-carousel-container").hasClass("scrolling") ) {
							jQuery(".featured-properties-carousel-container").jcarousel("scroll", "-=1");
							jQuery(".featured-properties-carousel-container").hide().fadeIn(700);
						}
						jQuery(".featured-properties-carousel-container").addClass("scrolling");
					});
				}
			});
		</script>';

		$secondary_title = '<span>' . $listing_stitle . '</span>';

		$output .= '<h1 class="main-module-title">' . $listing_ptitle . $secondary_title . '</h1>';
	   
		$output .= '<div class="featured-properties-controls">
			<a href="javascript:void(0)" class="listing-next icon-angle-right"></a>
			<a href="javascript:void(0)" class="listing-prev icon-angle-left"></a>
		</div>';

		$output .= '<div class="clearfix"></div>';
		$output .= '<div class="featured-properties-carousel-container '.$count_class.'">';
		$output .= '<div class="featured-properties-carousel">';
		$output .= '<div class="featured-properties-row">';

		foreach ($queryresults as $listing_value) {

			$class = 'wide';
			$version_class = '';
			$listing_type = '';

			if ( get_option("vh_theme_version") == "SkyDirectory" ) {
				$version_class = ' skydirectory';
			}

			if ( $count > 3 ) {
				$count = 1;
			}

			if ( $count < 3 ) {
				$class = 'half';
			}

			if ( $count == 2 ) {
				$class .= ' square-half';
			}

			if ( get_option("vh_theme_version") == "SkyEstate" && $listing_value->geodir_listing_type != null ) {
				$listing_type = strtolower($listing_value->geodir_listing_type);
			}

			if ( $count == 1 || $count == 2 || $count == 3 ) {
				if ( function_exists('geodir_get_images') ) {
					$post_images = geodir_get_images( $listing_value->post_id, '', get_option( 'geodir_listing_no_img' ) );
				} else {
					$post_images = array();
				}
				if ( count((array)$post_images) == 1 ) {
					$class .= ' animation';
				};
			}

			$output .= '<div class="featured-properties-container ' . $class . " " . $listing_type . '">';
			$output .= '<div class="featured-properties-image">';
			if ( $count == 1 ) {
				if ( function_exists('geodir_get_images') ) {
					$post_images = geodir_get_images( $listing_value->post_id, 'featured-property-thin', '' );
				} else {
					$post_images = array();
				}
				
				if ( !empty($post_images) && count((array)$post_images) > 1 ) {
					$output .= '<div class="featured-properties-inner-carousel-container">';
						$output .= '<div class="featured-properties-inner-carousel">';
						foreach ($post_images as $image_value) {
							$image = vh_imgresize($image_value->src, 293, 492, true);
							$output .= '<img src="'.$image.'" alt="">';
						}
						$output .= '</div>';
					$output .= '</div>';
				} elseif ( empty($post_images) && get_option( 'geodir_listing_no_img', '' ) ) {
					$image = vh_imgresize(get_option( 'geodir_listing_no_img', '' ), 293, 492, true);
					$output .= '<img src="'.$image.'" alt="">';
				} else {
					$output .= get_the_post_thumbnail( $listing_value->post_id, 'featured-property-thin' );
				}
			} elseif ( $count == 2 ) {
				if ( function_exists('geodir_get_images') ) {
					$post_images = geodir_get_images( $listing_value->post_id, 'featured-property-square', '' );
				} else {
					$post_images = array();
				}
				
				if ( !empty($post_images) && count((array)$post_images) > 1 ) {
					$output .= '<div class="featured-properties-inner-carousel-container">';
						$output .= '<div class="featured-properties-inner-carousel">';
						foreach ($post_images as $image_value) {
							$image = vh_imgresize($image_value->src, 293, 246, true);
							$output .= '<img src="'.$image.'" alt="">';
						}
						$output .= '</div>';
					$output .= '</div>';
				} elseif ( empty($post_images) && get_option( 'geodir_listing_no_img', '' ) ) {
					$image = vh_imgresize(get_option( 'geodir_listing_no_img', '' ), 293, 246, true);
					$output .= '<img src="'.$image.'" alt="">';
				} else {
					$output .= get_the_post_thumbnail( $listing_value->post_id, 'featured-property-square' );
				}
			} elseif ( $count == 3 ) {
				if ( function_exists('geodir_get_images') ) {
					$post_images = geodir_get_images( $listing_value->post_id, 'featured-property-wide', '' );
				} else {
					$post_images = array();
				}
				
				if ( !empty($post_images) && count((array)$post_images) > 1 ) {
					$output .= '<div class="featured-properties-inner-carousel-container">';
						$output .= '<div class="featured-properties-inner-carousel">';
						foreach ($post_images as $image_value) {
							$image = vh_imgresize($image_value->src, 585, 246, true);
							$output .= '<img src="'.$image.'" alt="">';
						}
						$output .= '</div>';
					$output .= '</div>';
				} elseif ( empty($post_images) && get_option( 'geodir_listing_no_img', '' ) ) {
					$image = vh_imgresize(get_option( 'geodir_listing_no_img', '' ), 585, 246, true);
					$output .= '<img src="'.$image.'" alt="">';
				} else {
					$output .= get_the_post_thumbnail( $listing_value->post_id, 'featured-property-wide' );
				}
			}
			if ( get_option("vh_theme_version") == "SkyEstate" ) {
				if ( $listing_type == __("for sale", "vh") ) {
					$output .= '<span class="listing-type">' . __("sale price", "vh") . '</span>';
				} elseif ( $listing_type == "for rent" ) {
					$output .= '<span class="listing-type">' . __("rent per month", "vh") . '</span>';
				}
			}
			if ( ( get_option("vh_theme_version") == "SkyVacation" || get_option("vh_theme_version") == "SkyEstate" ) && !empty($listing_value->geodir_listing_price) ) {
				$output .= '<span class="listing-price">' . get_option('vh_currency_symbol') . $listing_value->geodir_listing_price . '</span>';
			}
			if ( $count == 1 || $count == 2 || $count == 3 ) {
				if ( function_exists('geodir_get_images') ) {
					$post_images_count = geodir_get_images( $listing_value->post_id, 'featured-property-thin', get_option( 'geodir_listing_no_img' ) );
				} else {
					$post_images_count = array();
				}
				$expired = false;
				if ( $listing_value->expire_date != 'Never' && strtotime($listing_value->expire_date) > time() ) {
					$expired = true;
				}
				if ( count((array)$post_images) > 1 ) {
					$output .= '<div class="featured-carousel-controls">';
						$output .= '<a href="javascript:void(0)" class="featured-carousel-next icon-angle-right"></a>';
						$output .= '<a href="javascript:void(0)" class="featured-carousel-prev icon-angle-left"></a>';
						if ( $module_type == 'author-dash' ) {
							$author_id = get_query_var( 'author' );
							$current_user_id = get_current_user_id();
							if ( $author_id == $current_user_id ) {
								$addplacelink = get_permalink( get_option('geodir_add_listing_page') );
								$editlink = geodir_getlink($addplacelink,array('pid'=>$listing_value->post_id, 'listing_type'=>$listing_post_type),false);
								$updatelink = geodir_getlink($editlink,array('upgrade'=>'1'),false);
								$ajaxlink = geodir_get_ajax_url();
								$deletelink = geodir_getlink($ajaxlink,array('geodir_ajax'=>'add_listing','ajax_action'=>'delete','pid'=>$listing_value->post_id),false);

								$output .= '<a href="'.$updatelink.'" class="update-listing icon-upload" title="Upgrade"></a>';
								$output .= '<a href="'.$editlink.'" class="edit-listing icon-pencil" title="Edit"></a>';
								$output .= '<a href="'.$deletelink.'" class="delete-listing icon-trash-empty" title="Delete"></a>';
							}
						}
					$output .= '</div>';
					$output .= '<span class="featured-image-overlay"></span>';
					if ( get_option('geodir_payment_expire_date_on_listing', '0') && $module_type == 'author-dash' && $expired ) {
						$output .= '<span class="listing-expire-date">asd</span>';
					}
				} else {
					$output .= '<div class="featured-carousel-controls">';
						if ( $module_type == 'author-dash' ) {
							$author_id = get_query_var( 'author' );
							$current_user_id = get_current_user_id();
							if ( $author_id == $current_user_id ) {
								$addplacelink = get_permalink( get_option('geodir_add_listing_page') );
								$editlink = geodir_getlink($addplacelink,array('pid'=>$listing_value->post_id, 'listing_type'=>$listing_post_type),false);
								$updatelink = geodir_getlink($editlink,array('upgrade'=>'1'),false);
								$ajaxlink = geodir_get_ajax_url();
								$deletelink = geodir_getlink($ajaxlink,array('geodir_ajax'=>'add_listing','ajax_action'=>'delete','pid'=>$listing_value->post_id),false);

								$output .= '<a href="'.$updatelink.'" class="update-listing icon-upload" title="Upgrade"></a>';
								$output .= '<a href="'.$editlink.'" class="edit-listing icon-pencil" title="Edit"></a>';
								$output .= '<a href="'.$deletelink.'" class="delete-listing icon-trash-empty" title="Delete"></a>';
							}
						}
					$output .= '</div>';
					if ( get_option('geodir_payment_expire_date_on_listing', '0') && $module_type == 'author-dash' && $expired ) {
						$output .= '<span class="listing-expire-date">'.__('Expires', 'vh').': '.$listing_value->expire_date.'</span>';
					}
				}
			}
			$output .= '</div>';
			$output .= '<div class="item-info-container">';
			if ( $listing_value->overall_rating != 0 ) {
				$overall_rating = $listing_value->overall_rating;
			} else {
				$overall_rating = 0;
			}
			$output .= '<div class="item-rating">' . get_listing_rating_stars($overall_rating, false) . '</div>';
			$output .= '<div class="item-title"><a href="' . get_permalink( $listing_value->post_id ) . '">' . get_the_title( $listing_value->post_id ) . '</a></div>';
			$output .= '<div class="item-location">' . $listing_value->post_city . ', ' . $listing_value->post_country . '</div>';
			$output .= '<div class="item-view'.$version_class.'"><a href="' . get_permalink( $listing_value->post_id ) . '" class="wpb_button wpb_btn-primary wpb_btn-small">' . __('View', 'vh') . '</a></div>';

			if ( get_option("vh_theme_version") == "SkyVacation" ) {
				$output .= get_geodir_show_listing_fields( $listing_value->post_id, 'vacation', '', get_post_type( $listing_value->post_id ) );
			} elseif ( get_option("vh_theme_version") == "SkyEstate" ) {
				$output .= get_geodir_show_listing_fields( $listing_value->post_id, 'estate', '', get_post_type( $listing_value->post_id ) );
			}

			$output .= '</div>';
			$output .= '</div>';

			if ( $count > 2 && $count % 3 == 0 && count($queryresults) != $loop_count ) {
				$output .= '</div><div class="featured-properties-row">';
			}

			$count++;
			$loop_count++;
		}
		$output .= '</div></div></div></div>';
	} elseif ( $module_type == 'author-dash' ) {
		$secondary_title = '<span>' . $listing_stitle . '</span>';
		$output .= '<h1 class="main-module-title">' . $listing_ptitle . $secondary_title . '</h1>';
	}

	return $output;
}
add_shortcode('vh_featured_properties', 'vh_featured_properties');

// Popular destinations
function vh_popular_destinations($atts, $content = null, $code) {
	extract( shortcode_atts( array(
		'popular_dest_ptitle'    => '',
		'popular_dest_stitle'    => '',
		'popular_dest_tags'      => '',
		'popular_dest_countries' => '',
		'popular_dest_limit'     => '',
		'listing_post_type'      => 'gd_place'
	), $atts ) );

	global $post, $wpdb;
	$count = 1;
	$random = rand();
	$output = $new_title = $version_class = '';

	$args = array(
	'post_type' => $listing_post_type,
	$listing_post_type.'_tags' => $popular_dest_tags,
	'posts_per_page' => $popular_dest_limit
	);

	if ( get_option("vh_theme_version") == "SkyDirectory" ) {
		$version_class = " skydirectory";
	}

	$the_query = new WP_Query( $args );

	if( $the_query->have_posts() ) {
		$output .= '<div class="popular-destinations-main">';

		$secondary_title = '<span>' . $popular_dest_stitle . '</span>';

		$output .= '<h1 class="main-module-title">' . $popular_dest_ptitle . $secondary_title . '</h1>';
		$output .= '<div class="clearfix"></div>';

		while ( $the_query->have_posts() ) {
			$the_query->the_post();

			$listing_info = "SELECT * FROM " . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail WHERE post_status = 'publish' AND post_id=".$post->ID;
			$listing_results = $wpdb->get_results($listing_info);


			if ( $count == 1 || $count == 5 || $count == 6 ) {
				$class = 'city';
			} elseif ( $count == 2 ||  $count == 7 ) {
				$class = 'half-left';
			} else {
				$class = 'half-right';
			}

			if ( $count == 3 ) {
				$class .= ' full-right';
			} elseif ( $count == 7 ) {
				$class .= ' last';
			}

			if ( !empty($listing_results['0']->geodir_listing_type) && get_option("vh_theme_version") == "SkyEstate" && $listing_results['0']->geodir_listing_type != null ) {
				$listing_type = strtolower($listing_results['0']->geodir_listing_type);
			} else {
				$listing_type = '';
			}

			if ( $count == 1 || $count == 5 || $count == 6 ) {
				$output .= '<div class="popular-destinations-container ' . $class . " " . $listing_type . ' listing_no' . $count . '">';
				$output .= '<div class="popular-destinations-image">';
				$countries = get_option('vh_countries_options');
				$info = trim($countries['pu_textbox'],',');
				$info_arr = json_decode('['.$info.']',true);
				$country_count = count(explode(',',$popular_dest_countries));
				$post_countries = explode(',',$popular_dest_countries);

				if ( $count == 1 && $country_count > 0 ) {
					foreach ($info_arr as $value) {
						$countries = explode(':', $value['country']);
						$split_country = explode(':', $post_countries['0']);
						if ( $countries['0'] == $split_country['0'] ) {
							$image_id = $countries['1'];

							if ( get_option("vh_theme_version") == "SkyEstate" ) {
								$listing_sale_price_query = "SELECT min(geodir_listing_price) as SmallestPrice FROM " . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail WHERE post_status = 'publish' AND post_country='" . $countries['0'] . "' AND geodir_listing_type='".__('For Sale', 'vh')."' ORDER BY geodir_listing_price";
								$listing_sale_price = $wpdb->get_results($listing_sale_price_query);

								$listing_rent_price_query = "SELECT min(geodir_listing_price) as SmallestPrice FROM " . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail WHERE post_status = 'publish' AND post_country='" . $countries['0'] . "' AND geodir_listing_type='".__('For Rent', 'vh')."' ORDER BY geodir_listing_price";
								$listing_rent_price = $wpdb->get_results($listing_rent_price_query);

								$country_sale_price = $listing_sale_price['0']->SmallestPrice;
								$country_rent_price = $listing_rent_price['0']->SmallestPrice;
							} else {
								$listing_price_query = "SELECT geodir_listing_price FROM " . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail WHERE post_status = 'publish' AND post_country='".$split_country['0']."' ORDER BY geodir_listing_price";
								$listing_price = $wpdb->get_results($listing_price_query);
								$country_price = $listing_price['0']->geodir_listing_price;
							}

							$country_display = $split_country['0'];
							$city_display = $split_country['1'];
						}
					}
				} elseif ( $count == 5 && $country_count > 1 ) {
					foreach ($info_arr as $value) {
						$countries = explode(':', $value['country']);
						$split_country = explode(':', $post_countries['1']);
						if ( $countries['0'] == $split_country['0'] ) {
							$image_id = $countries['1'];

							if ( get_option("vh_theme_version") == "SkyEstate" ) {
								$listing_sale_price_query = "SELECT min(geodir_listing_price) as SmallestPrice FROM " . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail WHERE post_status = 'publish' AND post_country='" . $countries['0'] . "' AND geodir_listing_type='".__('For Sale', 'vh')."' ORDER BY geodir_listing_price";
								$listing_sale_price = $wpdb->get_results($listing_sale_price_query);

								$listing_rent_price_query = "SELECT min(geodir_listing_price) as SmallestPrice FROM " . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail WHERE post_status = 'publish' AND post_country='" . $countries['0'] . "' AND geodir_listing_type='".__('For Sale', 'vh')."' ORDER BY geodir_listing_price";
								$listing_rent_price = $wpdb->get_results($listing_rent_price_query);

								$country_sale_price = $listing_sale_price['0']->SmallestPrice;
								$country_rent_price = $listing_rent_price['0']->SmallestPrice;
							} else {
								$listing_price_query = "SELECT geodir_listing_price FROM " . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail WHERE post_status = 'publish' AND post_country='".$split_country['0']."' ORDER BY geodir_listing_price";
								$listing_price = $wpdb->get_results($listing_price_query);
								$country_price = $listing_price['0']->geodir_listing_price;
							}

							$country_display = $split_country['0'];
							$city_display = $split_country['1'];
						}
					}
				} elseif ( $count == 6 && $country_count > 2 ) {
					foreach ($info_arr as $value) {
						$countries = explode(':', $value['country']);
						$split_country = explode(':', $post_countries['2']);
						if ( $countries['0'] == $split_country['0'] ) {
							$image_id = $countries['1'];

							if ( get_option("vh_theme_version") == "SkyEstate" ) {
								$listing_sale_price_query = "SELECT min(geodir_listing_price) as SmallestPrice FROM " . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail WHERE post_status = 'publish' AND post_country='" . $countries['0'] . "' AND geodir_listing_type='For Sale' ORDER BY geodir_listing_price";
								$listing_sale_price = $wpdb->get_results($listing_sale_price_query);

								$listing_rent_price_query = "SELECT min(geodir_listing_price) as SmallestPrice FROM " . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail WHERE post_status = 'publish' AND post_country='" . $countries['0'] . "' AND geodir_listing_type='For Rent' ORDER BY geodir_listing_price";
								$listing_rent_price = $wpdb->get_results($listing_rent_price_query);

								$country_sale_price = $listing_sale_price['0']->SmallestPrice;
								$country_rent_price = $listing_rent_price['0']->SmallestPrice;
							} else {
								$listing_price_query = "SELECT geodir_listing_price FROM " . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail WHERE post_status = 'publish' AND post_country='".$split_country['0']."' ORDER BY geodir_listing_price";
								$listing_price = $wpdb->get_results($listing_price_query);
								$country_price = $listing_price['0']->geodir_listing_price;
							}

							$country_display = $split_country['0'];
							$city_display = $split_country['1'];
						}
					}
				}
				if ( !empty($image_id) ) {
					if ( wp_attachment_is_image( $image_id ) ) {
						$output .= wp_get_attachment_image( $image_id, 'popular-destinations-square' );
					} else {
						$attachment_info = wp_get_attachment_metadata( $image_id );
						if ( isset($attachment_info['fileformat']) && $attachment_info['fileformat'] == 'mp4' || $attachment_info['fileformat'] == 'webm' || $attachment_info['fileformat'] == 'ogg' ) {
							$video_url = wp_get_attachment_url( $image_id );
							$output .= '
							<div class="video-block-preview">
								<video width="' . $attachment_info['width'] . '" height="' . $attachment_info['height'] . '" preload="auto" autoplay="1" loop="1" muted="1">
									<source src="' . $video_url . '" type="video/' . $attachment_info['fileformat'] . '">
								</video>
							</div>';
						}
					}
				}
				if ( get_option("vh_theme_version") != "SkyDirectory" ) {
					$output .= '<span class="listing-price">$</span>';
					if ( get_option("vh_theme_version") == "SkyEstate" ) {
						if ( !empty($country_rent_price) ) {
							$output .= '<span class="city-rent-price">' . __('from', 'vh') . ' ' . get_option('vh_currency_symbol') . $country_rent_price . ' ' . __('per month', 'vh') . '</span>';
						}
						if ( !empty($country_sale_price) ) {
							$output .= '<span class="city-sale-price">' . __('from', 'vh') . ' ' . get_option('vh_currency_symbol') . $country_sale_price . ' ' . __('sale price', 'vh') . '</span>';
						}
					} else {
						$output .= '<span class="city-price">' . __('from', 'vh') . ' ' . get_option('vh_currency_symbol') . $country_price . ' ' . __('per night', 'vh') . '</span>';
					}
				}
				$output .= '<span class="city-country">' . $city_display . '</span>';
				$output .= '<span class="city-country country">' . $country_display . '</span>';
				$output .= '<a href="javascript:void(0)" class="city-text">' . __('View properties') . '</a>';
				$output .= '</div>';
				$output .= '</div>';
				$count++;
				continue;
			} 
				$output .= '<div class="popular-destinations-container ' . $class . " " . $listing_type . ' listing_no' . $count . '">';
				$output .= '<div class="popular-destinations-image">';
				if ( $count == 1 || $count == 5 || $count == 6 ) {
					$output .= get_the_post_thumbnail( $post->ID, 'popular-destinations-square' );
				} else {
					if ( get_the_post_thumbnail( $post->ID, 'featured-property-square' ) ) {
						$output .= get_the_post_thumbnail( $post->ID, 'featured-property-square' );
					} elseif ( get_option('geodir_listing_no_img', '') ) {
						$image = vh_imgresize(get_option( 'geodir_listing_no_img', '' ), 291, 246, true);
						$output .= '<img src="'.$image.'" class="attachment-featured-property-square wp-post-image" alt="popular-destination">';
					}
					
				}
				$output .= '<span class="listing-price">' . get_option('vh_currency_symbol') . $listing_results['0']->geodir_listing_price . '</span>';
				$output .= '</div>';
				if ( $count != 1 && $count != 5 && $count != 6 ) {
					$output .= '<div class="item-info-container">';
					if ( $listing_results['0']->overall_rating != 0 ) {
						$overall_rating = $listing_results['0']->overall_rating;
					} else {
						$overall_rating = 0;
					}
					$output .= '<div class="item-rating">' . get_listing_rating_stars($overall_rating, false) . '</div>';
					$output .= '<div class="item-title"><a href="' . get_permalink( $post->ID ) . '">' . get_the_title() . '</a></div>';
					$output .= '<div class="item-location">' . $listing_results['0']->post_city . ', ' . $listing_results['0']->post_country . '</div>';
					$output .= '<div class="item-view'.$version_class.'"><a href="' . get_permalink( $post->ID ) . '" class="wpb_button wpb_btn-primary wpb_btn-small">' . __('View', 'vh') . '</a></div>';

					if ( get_option("vh_theme_version") == "SkyVacation" ) {
						$output .= get_geodir_show_listing_fields( $post->ID, 'vacation', '', $listing_post_type );
					} elseif ( get_option("vh_theme_version") == "SkyEstate" ) {
						$output .= get_geodir_show_listing_fields( $post->ID, 'estate', '', $listing_post_type );
					}

					$output .= '</div>';
				}
				$output .= '</div>';
			

			$count++;
		}
		$output .= '<div class="clearfix"></div></div>';
	}

	wp_reset_query();
	wp_reset_postdata();

	return $output;
}
add_shortcode('vh_popular_destinations', 'vh_popular_destinations');

// Recent activities
function vh_recent_activities($atts, $content = null, $code) {
	extract( shortcode_atts( array(
		'recent_activities_ptitle' => '',
		'recent_activities_stitle' => '',
		'recent_activities_limit'  => '',
		'listing_post_type'        => 'gd_place'
	), $atts ) );

	global $post, $wpdb;
	$output = $new_title = $listing_type = $activity_permalink = '';
	$loop_count = 0;

	if ( $recent_activities_limit == '' ) {
		$limit = 99999;
	} else {
		$limit = $recent_activities_limit;
	}

	if ( get_option("vh_theme_version") == "SkyEstate" ) {
		$q2 = "SELECT c.comment_post_ID as PostID, null as PostTitle, pd.post_country as ListingCountry, pd.post_city as ListingCity, null as OverallRating, c.comment_content as CommentContent, c.comment_ID as CommentID, c.comment_date as ItemDate, c.comment_author as Author, c.user_id as AuthorID, null as SquareFeet, null as BedroomCount, null as BathroomCount, null as BedCount, null as ListingType FROM " . $wpdb->prefix . "comments c," . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail pd WHERE pd.post_id=c.comment_post_ID AND c.comment_approved=1 AND c.comment_parent != 0 UNION ALL SELECT pr.post_id as PostID, pr.post_title as PostTitle, pd.post_country as ListingCountry, pd.post_city as ListingCity, pr.overall_rating as OverallRating, null as CommentContent, pr.id as CommentID, pr.post_date as ItemDate, c.comment_author as Author, c.user_id as AuthorID, null as SquareFeet, null as BedroomCount, null as BathroomCount, null as BedCount, null as ListingType FROM " . $wpdb->prefix .  "geodir_post_review pr," . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail pd," . $wpdb->prefix . "comments c WHERE pr.post_id=pd.post_id AND c.comment_ID=pr.comment_id AND pr.status=1 UNION ALL SELECT post_id as PostID, post_title as PostTitle, post_country as ListingCountry, post_city as ListingCity, null as OverallRating, null as CommentContent, null as CommentID, FROM_UNIXTIME(submit_time) as ItemDate, null as Author, null as AuthorID, geodir_square_feet as SquareFeet, geodir_listing_bedroom_count as BedroomCount, geodir_bathroom_count as BathroomCount, geodir_listing_bed_count as BedCount, geodir_listing_type as ListingType FROM " . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail WHERE post_status = 'publish' ORDER BY ItemDate DESC";
		$q2r = $wpdb->get_results($q2);
	} elseif ( get_option("vh_theme_version") == "SkyDirectory" ) {
		$q2 = "SELECT c.comment_post_ID as PostID, null as PostTitle, pd.post_country as ListingCountry, pd.post_city as ListingCity, null as OverallRating, c.comment_content as CommentContent, c.comment_ID as CommentID, c.comment_date as ItemDate, c.comment_author as Author, c.user_id as AuthorID FROM " . $wpdb->prefix . "comments c," . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail pd WHERE pd.post_id=c.comment_post_ID AND c.comment_approved=1 AND c.comment_parent != 0 UNION ALL SELECT pr.post_id as PostID, pr.post_title as PostTitle, pd.post_country as ListingCountry, pd.post_city as ListingCity, pr.overall_rating as OverallRating, null as CommentContent, pr.id as CommentID, pr.post_date as ItemDate, c.comment_author as Author, c.user_id as AuthorID FROM " . $wpdb->prefix .  "geodir_post_review pr," . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail pd," . $wpdb->prefix . "comments c WHERE pr.post_id=pd.post_id AND c.comment_ID=pr.comment_id AND pr.status=1 UNION ALL SELECT post_id as PostID, post_title as PostTitle, post_country as ListingCountry, post_city as ListingCity, null as OverallRating, null as CommentContent, null as CommentID, FROM_UNIXTIME(submit_time) as ItemDate, null as Author, null as AuthorID FROM " . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail WHERE post_status = 'publish' ORDER BY ItemDate DESC";
		$q2r = $wpdb->get_results($q2);
	} else {
		$q2 = "SELECT c.comment_post_ID as PostID, null as PostTitle, pd.post_country as ListingCountry, pd.post_city as ListingCity, null as OverallRating, c.comment_content as CommentContent, c.comment_ID as CommentID, c.comment_date as ItemDate, c.comment_author as Author, c.user_id as AuthorID, null as GuestCount, null as BedroomCount, null as BedCount FROM " . $wpdb->prefix . "comments c," . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail pd WHERE pd.post_id=c.comment_post_ID AND c.comment_approved=1 AND c.comment_parent != 0 UNION ALL SELECT pr.post_id as PostID, pr.post_title as PostTitle, pd.post_country as ListingCountry, pd.post_city as ListingCity, pr.overall_rating as OverallRating, null as CommentContent, pr.id as CommentID, pr.post_date as ItemDate, c.comment_author as Author, c.user_id as AuthorID, null as GuestCount, null as BedroomCount, null as BedCount FROM " . $wpdb->prefix .  "geodir_post_review pr," . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail pd," . $wpdb->prefix . "comments c WHERE pr.post_id=pd.post_id AND c.comment_ID=pr.comment_id AND pr.status=1 UNION ALL SELECT post_id as PostID, post_title as PostTitle, post_country as ListingCountry, post_city as ListingCity, null as OverallRating, null as CommentContent, null as CommentID, FROM_UNIXTIME(submit_time) as ItemDate, null as Author, null as AuthorID, geodir_listing_guest_count as GuestCount, geodir_listing_bedroom_count as BedroomCount, geodir_listing_bed_count as BedCount FROM " . $wpdb->prefix .  "geodir_" . $listing_post_type . "_detail WHERE post_status = 'publish' ORDER BY ItemDate DESC";
		$q2r = $wpdb->get_results($q2);
	}

	if ( empty($q2r) ) {
		return;
	}

	$output .= "<div class='recent-activities-main'>";

	$secondary_title = '<span>' . $recent_activities_stitle . '</span>';

	$output .= '<h1 class="main-module-title">' . $recent_activities_ptitle . $secondary_title . '</h1>';
	$output .= '<div class="clearfix"></div>';
	$output .= "<ul class='recent-activities-container'>";

	foreach ($q2r as $recent_activities) {
		if ( $loop_count == $limit ) {
			break;
		}

		$post_status = get_post_status($recent_activities->PostID);
		if ( $post_status == 'trash' || $post_status == false ) {
			continue;
		}

		if ( get_option("vh_theme_version") == "SkyEstate" && $recent_activities->ListingType != null ) {
			$listing_type = strtolower($recent_activities->ListingType);
		} else {
			$listing_type = '';
		}

		if ( $recent_activities->CommentID != null ) {
			$comment_data = get_comment($recent_activities->CommentID);
		} else {
			$comment_data = (object)array('comment_parent' => null );
		}

		if ( $recent_activities->PostID != null ) {
			$activity_permalink = get_permalink($recent_activities->PostID);

			if ( empty($activity_permalink) ) {
				$activity_permalink = get_permalink( get_page_by_title( get_the_title($recent_activities->PostID) ) );
			}
		} else {
			$activity_permalink = "#";
		}

		if ( $recent_activities->OverallRating != null ) {
			if ( $recent_activities->AuthorID != '0' ) {
				if ( get_option('permalink_structure') ) {
					$dash_symbol = '?';
				} else {
					$dash_symbol = '&';
				}
				$author_link = "<a href='". get_author_posts_url( $recent_activities->AuthorID ).$dash_symbol."geodir_dashbord=true&stype=" . $listing_post_type . "' class='item-author'>". get_userdata( $recent_activities->AuthorID )->display_name . "</a>";
			} else {
				$author_link = '<span class="guest-activity">'. $recent_activities->Author.'</span>';
			}
			$loop_count++;
			if ( $recent_activities->OverallRating == 1 ) {
				$rating_text = get_listing_rating_stars($recent_activities->OverallRating, false) . '<span>' . $recent_activities->OverallRating . ' ' . __('star. Very bad!', 'vh') . '</span>';
			} elseif ( $recent_activities->OverallRating == 2 ) {
				$rating_text = get_listing_rating_stars($recent_activities->OverallRating, false) . '<span>' . $recent_activities->OverallRating . ' ' . __('stars. Bad!', 'vh') . '</span>';
			} elseif ( $recent_activities->OverallRating == 3 ) {
				$rating_text = get_listing_rating_stars($recent_activities->OverallRating, false) . '<span>' . $recent_activities->OverallRating . ' ' . __('stars. Good!', 'vh') . '</span>';
			} elseif ( $recent_activities->OverallRating == 4 ) {
				$rating_text = get_listing_rating_stars($recent_activities->OverallRating, false) . '<span>' . $recent_activities->OverallRating . ' ' . __('stars. Very good!', 'vh') . '</span>';
			} elseif ( $recent_activities->OverallRating == 5 ) {
				$rating_text = get_listing_rating_stars($recent_activities->OverallRating, false) . '<span>' . $recent_activities->OverallRating . ' ' . __('stars. Excellent!', 'vh') . '</span>';
			}

			$output .= "
			<li class='recent-activities ratings " . $listing_type . "'>
				<div class='image-container'>
					<div class='user-image-container'>".get_avatar(get_userdata( get_post_field( 'post_author', $recent_activities->PostID ) )->ID, 135)."</div>
					<div class='item-image'>";
						if ( get_the_post_thumbnail( $recent_activities->PostID, 'gallery-medium' ) ) {
							$output .= get_the_post_thumbnail( $recent_activities->PostID, 'gallery-medium' );
						} elseif ( get_option('geodir_listing_no_img', '') ) {
							$image = vh_imgresize(get_option( 'geodir_listing_no_img', '' ), 125, 125, true);
							$output .= '<img src="'.$image.'" class="attachment-gallery-medium wp-post-image" alt="recent-activity">';
						}
					$output .= "</div>
					<div class='clearfix'></div>
				</div>
				<div class='info-container'>
					<div class='main-item-text'>
						". $author_link ."
						<span class='item-text'>" . __('rated', 'vh') . "</span>
						<a href='".$activity_permalink."' class='item-link'>" . $recent_activities->PostTitle . "</a>
						<span class='activity-location'>(" . $recent_activities->ListingCity . ", " . $recent_activities->ListingCountry . ")</span>
					</div>
					<div class='separator-container'>
						<span class='item-seperator icon-star'></span>
					</div>
					<div class='activity-item-text'>" . $rating_text . "</div>
					<div class='clearfix'></div>
				</div>
			</li>";
		} elseif ( $comment_data->comment_parent != '0' && $recent_activities->CommentContent != null ) {
			$loop_count++;
			if ( strlen($recent_activities->CommentContent) > 80 ) {
				$comment_content = substr($recent_activities->CommentContent, 0, 80) . '..';
			} else {
				$comment_content = $recent_activities->CommentContent;
			}

			if ( $comment_data->user_id != '0' ) {
				if ( get_option('permalink_structure') ) {
					$dash_symbol = '?';
				} else {
					$dash_symbol = '&';
				}
				$author_link = "<a href='". get_author_posts_url( $comment_data->user_id ).$dash_symbol."geodir_dashbord=true&stype=" . $listing_post_type . "' class='item-author'>". get_userdata( $comment_data->user_id )->display_name . "</a>";
			} else {
				$author_link = '<span class="guest-activity">'. $recent_activities->Author.'</span>';
			}

			$output .= "
			<li class='recent-activities comments " . $listing_type . "'>
				<div class='image-container'>
					<div class='user-image-container'>".get_avatar(get_userdata( get_post_field( 'post_author', $recent_activities->PostID ) )->ID, 135)."</div>
					<div class='item-image'>";
						if ( get_the_post_thumbnail( $recent_activities->PostID, 'gallery-medium' ) ) {
							$output .= get_the_post_thumbnail( $recent_activities->PostID, 'gallery-medium' );
						} elseif ( get_option('geodir_listing_no_img', '') ) {
							$image = vh_imgresize(get_option( 'geodir_listing_no_img', '' ), 125, 125, true);
							$output .= '<img src="'.$image.'" class="attachment-gallery-medium wp-post-image" alt="recent-activity">';
						}
					$output .= "</div>
					<div class='clearfix'></div>
				</div>
				<div class='info-container'>
					<div class='main-item-text'>
						". $author_link ."
						<span class='item-text'>" . __('commented on', 'vh') . "</span>
						<a href='".$activity_permalink."' class='item-link'>" . get_the_title($recent_activities->PostID) . "</a>
						<span class='activity-location'>(" . $recent_activities->ListingCity . ", " . $recent_activities->ListingCountry . ")</span>
					</div>
					<div class='separator-container'>
						<span class='item-seperator icon-comment-1'></span>
					</div>
					<div class='activity-item-text'><span class='activity-comment-text'>" . $comment_content . "</span></div>
					<div class='clearfix'></div>
				</div>
			</li>";
		} elseif ( $recent_activities->CommentID == null ) {
			if ( get_option('permalink_structure') ) {
				$dash_symbol = '?';
			} else {
				$dash_symbol = '&';
			}
			$author_link = "<a href='". get_author_posts_url( get_post_field( 'post_author', $recent_activities->PostID ) ).$dash_symbol."geodir_dashbord=true&stype=" . $listing_post_type . "' class='item-author'>". get_userdata( get_post_field( 'post_author', $recent_activities->PostID ) )->display_name . "</a>";

			$loop_count++;
			$output .= "
			<li class='recent-activities listings " . $listing_type . "'>
				<div class='image-container'>
					<div class='user-image-container'>".get_avatar(get_userdata( get_post_field( 'post_author', $recent_activities->PostID ) )->ID, 135)."</div>
					<div class='item-image'>";
						if ( get_the_post_thumbnail( $recent_activities->PostID, 'gallery-medium' ) ) {
							$output .= get_the_post_thumbnail( $recent_activities->PostID, 'gallery-medium' );
						} elseif ( get_option('geodir_listing_no_img', '') ) {
							$image = vh_imgresize(get_option( 'geodir_listing_no_img', '' ), 125, 125, true);
							$output .= '<img src="'.$image.'" class="attachment-gallery-medium wp-post-image" alt="recent-activity">';
						}
					$output .= "</div>
					<div class='clearfix'></div>
				</div>
				<div class='info-container'>
					<div class='main-item-text'>
						". $author_link ."
						<span class='item-text'>" . __('added new listing', 'vh') . "</span>
						<a href='".$activity_permalink."' class='item-link'>" . $recent_activities->PostTitle . "</a>
						<span class='activity-location'>(" . $recent_activities->ListingCity . ", " . $recent_activities->ListingCountry . ")</span>
					</div>
					<div class='separator-container'>
						<span class='item-seperator icon-home'></span>
					</div>
					<a href='".$activity_permalink."' class='item-bottom-link'>" . $recent_activities->PostTitle . "</a>
					<span class='item-bottom-location'>" . $recent_activities->ListingCity . ", " . $recent_activities->ListingCountry . "</span>";

					if ( get_option("vh_theme_version") == "SkyVacation" ) {
						$output .= get_geodir_show_listing_fields( $recent_activities->PostID, 'vacation', '', $listing_post_type );
					} elseif ( get_option("vh_theme_version") == "SkyEstate" ) {
						$output .= get_geodir_show_listing_fields( $recent_activities->PostID, 'estate', '', $listing_post_type );
					}

					$output .= "
					<div class='clearfix'></div>
				</div>
			</li>";
		}
	}
	
	$output .= "</ul></div>";

	return $output;
}
add_shortcode('vh_recent_activities', 'vh_recent_activities');

// Blog carousel
function vh_blog_carousel($atts, $content = null, $code) {
	extract( shortcode_atts( array(
		'blog_carousel_ptitle'      => '',
		'blog_carousel_stitle'      => '',
		'blog_carousel_categories' => '',
		'blog_carousel_limit'      => ''
	), $atts ) );

	global $wp_query, $post;
	$random = rand();
	$count = $loop_count = 1;

	query_posts(array(
		'post_type' => 'post',
		'category_name' => $blog_carousel_categories,
		'posts_per_page' => $blog_carousel_limit
	));

	if ( !have_posts() ) {
		wp_reset_query();
		wp_reset_postdata();
		return;
	}

	$output = '';

	$output .= '
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			jQuery(".blog-carousel-container").on("jcarousel:scroll", function(event, carousel) {
				jQuery(".blog-carousel").parent().hide().fadeIn(700);
			}).jcarousel({
				wrap: "circular",
				animation: {
					duration: 0
				}
			}).on("jcarousel:scrollend", function(event, carousel) {
				setTimeout(function() {
					jQuery(".blog-carousel-container").removeClass("scrolling");
				}, 700);
			});

			jQuery(".blog-carousel-controls .blog-next").click(function() {
				if ( !jQuery(".blog-carousel-container").hasClass("scrolling") ) {
					jQuery(".blog-carousel-container").jcarousel("scroll", "+=1");
				}
				jQuery(".blog-carousel-container").addClass("scrolling");
			});

			jQuery(".blog-carousel-controls .blog-prev").click(function() {
				if ( !jQuery(".blog-carousel-container").hasClass("scrolling") ) {
					jQuery(".blog-carousel-container").jcarousel("scroll", "-=1");
				}
				jQuery(".blog-carousel-container").addClass("scrolling");
			});
		});
	</script>';

	$secondary_title = '<span>' . $blog_carousel_stitle . '</span>';

	$output .= '<h1 class="main-module-title">' . $blog_carousel_ptitle . $secondary_title . '</h1>';
   
	$output .= '<div class="blog-carousel-controls">
		<a href="javascript:void(0)" class="blog-next icon-angle-right"></a>
		<a href="javascript:void(0)" class="blog-prev icon-angle-left"></a>
	</div>';
	$output .= '<div class="clearfix"></div>';
	$output .= '<div class="blog-carousel-container">';
	$output .= '<div class="blog-carousel">';
	$output .= '<div class="blog-row">';
	while(have_posts()) {
		the_post();

		$class = 'wide';

		if ( $count > 3 ) {
			$count = 1;
		}

		if ( $count == 1 ) {
			$class = 'wide';
		} elseif ( $count == 2 ) {
			$class = 'half-right';
		} elseif ( $count == 3 ) {
			$class = 'half-left';
		}
		
		$output .= '<div class="blog-inner-container ' . $class . '">';
		$output .= '<div class="blog-image">';
		if ( $count == 1 ) {
			$output .= get_the_post_thumbnail( $post->ID, 'popular-destinations-square' );
			$output .= '<div class="blog-picture-time">' . human_time_diff(get_the_time('U',$post->id),current_time('timestamp')) . ' ' . __('ago', 'vh') . '</div>';
			$output .= '<div class="blog-picture-title"><a href="' . get_permalink( $post->ID ) . '">' . get_the_title() . '</a></div>';
			$output .= '<div class="blog-picture-read">' . __('Read article', 'vh') . '</div>';
			$output .= '<div class="blog-author image">';
			$output .= '<div class="blog-author-image">';
			$output .= get_avatar(get_userdata( get_post_field( 'post_author', $post->ID ) )->ID, 70);
			$output .= '</div>';
			$output .= '<div class="blog-author-info">';
				$output .= '<div class="blog-category icon-folder-open">';
					$output .= get_the_category_list( __( ', ', 'vh' ) );
				$output .= '</div>';
				$output .= '<div class="blog-comments icon-comment-1">';
					$tc = wp_count_comments($post->ID);
					$output .= $tc->approved;
				$output .= '</div>';
				$output .= '<div class="blog-author-inner">';
					$output .= '<span class="author-text">' . __('Author:', 'vh') . '</span>';
					$output .= '<span class="author-name"><a href="'. get_author_posts_url( get_post_field( 'post_author', $post->ID ) ) . '">' . get_userdata( get_post_field( 'post_author', $post->ID ) )->display_name . '</a></span>';
				$output .= '</div>';
				$output .= '<div class="clearfix"></div>';
			$output .= '</div>';
			$output .= '<div class="clearfix"></div>';
		$output .= '</div>';
		} else {
			$output .= get_the_post_thumbnail( $post->ID, 'featured-property-square' );
			$output .= '<div class="blog-picture-title"><a href="' . get_permalink( $post->ID ) . '">' . __('Read article', 'vh') . '</a></div>';
		}
		$output .= '</div>';
		$output .= '<div class="blog-inner-side">';
		$output .= '<div class="blog-date">' . human_time_diff(get_the_time('U',$post->id),current_time('timestamp')) . ' ' . __('ago', 'vh') . '</div>';
		$output .= '<div class="blog-title"><a href="' . get_permalink( $post->ID ) . '">' . get_the_title() . '</a></div>';
		$output .= '<div class="blog-excerpt">';
		if ( strlen($post->post_content) > 85 ) {
			$post->post_content = substr($post->post_content, 0, 85) . '..';
		} else {
			$post->post_content;
		}
		$output .= $post->post_content;
		$output .= '</div>';
		$output .= '<div class="blog-author">';
			$output .= '<div class="blog-author-image">';
			$output .= get_avatar(get_userdata( get_post_field( 'post_author', $post->ID ) )->ID, 70);
			$output .= '</div>';
			$output .= '<div class="blog-author-info">';
				$output .= '<div class="blog-category icon-folder-open">';
					$output .= get_the_category_list( __( ', ', 'vh' ) );
				$output .= '</div>';
				$output .= '<div class="blog-comments icon-comment-1">';
					$tc = wp_count_comments($post->ID);
					$output .= $tc->approved;
				$output .= '</div>';
				$output .= '<div class="blog-author-inner">';
					$output .= '<span class="author-text">' . __('Author:', 'vh') . '</span>';
					$output .= '<span class="author-name"><a href="'. get_author_posts_url( get_post_field( 'post_author', $post->ID ) ) . '">' . get_userdata( get_post_field( 'post_author', $post->ID ) )->display_name . '</a></span>';
				$output .= '</div>';
				$output .= '<div class="clearfix"></div>';
			$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		if ( $count > 2 && $count % 3 == 0 && count(isset($the_query->posts)) != $loop_count ) {
			$output .= '</div><div class="blog-row">';
		}

		$count++;
		$loop_count++;

	}
	$output .= '</div></div></div>';

	wp_reset_query();
	wp_reset_postdata();

	return $output;
}
add_shortcode('vh_blog_carousel', 'vh_blog_carousel');

// Blog posts
function vh_blog_posts($atts, $content = null, $code) {
	extract( shortcode_atts( array(
		'blog_posts_title'      => '',
		'blog_posts_categories' => '',
		'blog_posts_limit'      => ''
	), $atts ) );

	global $wp_query, $post;
	$random = rand();
	$count = $loop_count = 1;

	query_posts(array(
		'post_type' => 'post',
		'category_name' => $blog_posts_categories,
		'posts_per_page' => $limit
	));

	if ( !have_posts() ) {
		return;
	}

	$output = '';

	if ( count(explode(' ', $blog_posts_title)) > 2 ) {
		for ($i=0; $i < 2; $i++) {
			$exploded_title = explode(' ', $blog_posts_title);
			$new_title .= $exploded_title[$i].' ';
		}
	}

	$new_title .= '<span>';
	for ($i=2; $i < count(explode(' ', $blog_posts_title)); $i++) {
		$exploded_title = explode(' ', $blog_posts_title);
		$new_title .= $exploded_title[$i] . ' ';
	}
	$new_title .= '</span>';

	$output .= '<h1 class="main-module-title">' . $new_title . '</h1>';
	$output .= '<div id="blog_post_container"></div>';
	$output .= '<input id="posts_to_load" type="hidden" value="1" />';
	$output .= '<input id="blog_posts_categories" type="hidden" value="'.$blog_posts_categories.'" />';
	$output .= '<input id="blog_posts_limit" type="hidden" value="'.$blog_posts_limit.'" />';
	$output .= '<div class="clearfix"></div><div class="blog-button"><a id="load_blog_posts" class="wpb_button wpb_btn-inverse wpb_btn-small" href="javascript:void(0)">Load more</a></div>';

	wp_reset_query();
	wp_reset_postdata();

	return $output;
}
add_shortcode('vh_blog_posts', 'vh_blog_posts');

// Social links
function vh_social_links($atts, $content = null, $code) {
	extract( shortcode_atts( array(
		'social_links_fb'      => '',
		'social_links_tw'      => '',
		'social_links_yt'      => '',
		'social_links_fb_text' => '',
		'social_links_tw_text' => '',
		'social_links_yt_text' => ''
	), $atts ) );

	if ( $social_links_fb != "" && $social_links_fb_text != "" ) {
		$output .= '<a href="'.$social_links_fb.'" class="social-links-facebook"><span class="icon-facebook"></span>'.$social_links_fb_text.'</a>';
	}

	if ( $social_links_tw != "" && $social_links_tw_text != "" ) {
		$output .= '<a href="'.$social_links_tw.'" class="social-links-twitter"><span class="icon-twitter-1"></span>'.$social_links_tw_text.'</a>';
	}

	if ( $social_links_yt != "" && $social_links_yt_text != "" ) {
		$output .= '<a href="'.$social_links_yt.'" class="social-links-youtube"><span class="icon-youtube-squared"></span>'.$social_links_yt_text.'</a>';
	}

	return $output;
}
add_shortcode('vh_social_links', 'vh_social_links');

// Categories module
function vh_listing_categories($atts, $content = null, $code) {
	extract( shortcode_atts( array(
		'categories_main_title'      => '',
		'categories_secondary_title' => '',
		'categories_selected_cat'    => '',
		'custom_taxonomy'            => 'gd_placecategory',
		'custom_post'                => 'gd_place'
	), $atts ) );

	$category_count = 1;
	$class_num = 3;
	$output = '';

	$category_list = explode(",", $categories_selected_cat);

	$output .= '<h1 class="main-module-title">'.$categories_main_title.' <span>'.$categories_secondary_title.'</span></h1><div class="clearfix"></div>';
	$output .= '<div class="listing-category-main">';

	foreach ( $category_list as $category_parents ) {
		$listing_count = 0;
		$parent_childs = get_term_children($category_parents, $custom_taxonomy);
		$parent = get_term_by('id', $category_parents, $custom_taxonomy);
		$listing_count += $parent->count;
		if ( $category_count == $class_num || $category_count == $class_num+1 ) {
			$extra_class = " right";
			if ( $category_count == $class_num+1 ) {
				$class_num = $class_num+4;
			}
		} else {
			$extra_class = " left";
		}
		$output .= '<div class="listing-category-container'.$extra_class.'">';
		$parent_icon = get_tax_meta($category_parents, 'ct_cat_default_img', false, $custom_post);
		$output .= '<div class="listing-category-image">';
		if ( isset($parent_icon["id"]) ) {
			$output .= wp_get_attachment_image($parent_icon["id"], 'featured-property-square');
		}
		$output .= '<div class="listing-count">'.$listing_count.' '.__("listings", "vh").'</div>';
		$output .= '</div>';
		$output .= '<div class="listing-category-side">';
		$output .= '<span class="category-parent">' . $parent->name . '</span>';
		foreach ($parent_childs as $child_value) {
			$child = get_term_by('id', $child_value, $custom_taxonomy);
			$listing_count += $child->count;
			$output .= '<a href="'.get_site_url()."/?" . $custom_taxonomy . "=".$child->slug.'" class="category-child">' . $child->name . '</a>';
		}
		$output .= '</div></div>';
		$category_count++;
	}

	$output .= '</div>';

	return $output;
}
add_shortcode('vh_listing_categories', 'vh_listing_categories');

?>