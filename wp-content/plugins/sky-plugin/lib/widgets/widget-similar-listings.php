<?php
// Creating the widget 
class wpb_widget_similar_listings extends WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'sky-directory-similar-listings', 

			// Widget name will appear in UI
			__('Sky - Similar listings', 'vh'), 

			// Widget description
			array( 'description' => __( 'Just a simple widget that displays similar listings.', 'vh' ), ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		global $post, $wpdb;
		$title           = $instance['title'];
		$pricerange      = $instance['pricerange'];
		$pricerangevalue = $instance['pricerangevalue'];
		$wlocation       = $instance['location'];
		$title_lower     = '';
		$version_class   = "";
		if ( get_option("vh_theme_version") == "SkyDirectory" ) {
			$version_class = " skydirectory";
		}

		$location_where = $pricerange_where = '';

		// before and after widget arguments are defined by themes

		echo $args['before_widget'];

		if ( $wlocation == "on" && $pricerange == "on" ) {
			$title_lower .= 'Location, Price range';
		} elseif ( $wlocation == "on" && $pricerange == "" ) {
			$title_lower .= 'Location';
		} elseif ( $wlocation == "" && $pricerange == "on" ) {
			$title_lower .= 'Price range';
		}

		if ( empty($pricerangevalue) ) {
			$pricerangevalue = 50;
		}
		
		if ( !empty($title) ) {
			echo '<div class="item-title-bg">';
			echo '<h4>' . $title . '<span>'.$title_lower.'</span></h4>';
			echo '</div>';
		}

		$location_id = geodir_get_post_meta( $post->ID, 'post_location_id', true );
		$location = geodir_get_location( $location_id );

		if ( $wlocation == "on" ) {
			$location_where = " AND post_region LIKE \"%".$location->region."%\"";
		}

		if ( $pricerange == "on" && get_option("vh_theme_version") != "SkyDirectory" ) {
			$queryprice = "SELECT geodir_listing_price FROM " . $wpdb->prefix .  "geodir_".get_post_type()."_detail WHERE post_status = 'publish' AND post_id=".$post->ID;
			$querypriceresults = $wpdb->get_results($queryprice);
			if ( $querypriceresults["0"]->geodir_listing_price-$pricerangevalue < 0 ) {
				$low_price = 0;
			} else {
				$low_price = $querypriceresults["0"]->geodir_listing_price-$pricerangevalue;
			}
			$high_price = $querypriceresults["0"]->geodir_listing_price+$pricerangevalue;
			$pricerange_where = " AND geodir_listing_price<".$high_price;
		}

		if ( get_post_type() == 'gd_event' ) {
			$querystr = "SELECT overall_rating,rating_count,post_city,post_country,post_id,is_featured,post_tags FROM " . $wpdb->prefix .  "geodir_".get_post_type()."_detail WHERE post_status = 'publish'".$location_where.$pricerange_where." LIMIT 5";
		} elseif ( get_option("vh_theme_version") != "SkyDirectory" ) {
			$querystr = "SELECT overall_rating,rating_count,geodir_listing_price,post_city,post_country,post_id,is_featured,post_tags FROM " . $wpdb->prefix .  "geodir_".get_post_type()."_detail WHERE post_status = 'publish'".$location_where.$pricerange_where." LIMIT 5";
		} else {
			$querystr = "SELECT overall_rating,rating_count,post_city,post_country,post_id,is_featured,post_tags FROM " . $wpdb->prefix .  "geodir_".get_post_type()."_detail WHERE post_status = 'publish'".$location_where.$pricerange_where." LIMIT 5";
		}

		$queryresults = $wpdb->get_results($querystr);
		
		if ( !empty($queryresults) ) {
			foreach ($queryresults as $value) {
				echo '<div class="google-map-main similar">';
					echo '<div class="google-map-container">';
					echo '<div class="google-map-image">';
					if ( get_the_post_thumbnail( $value->post_id, 'gallery-medium' ) ) {
						echo get_the_post_thumbnail( $value->post_id, 'gallery-medium' );
					} elseif ( get_option('geodir_listing_no_img', '') ) {
						$image = vh_imgresize(get_option( 'geodir_listing_no_img', '' ), 123, 125, true);
						echo '<img src="'.$image.'" class="attachment-gallery-medium wp-post-image" alt="similar-listing">';
					}
					if ( get_option("vh_theme_version") != "SkyDirectory" && get_post_type() != 'gd_event' ) {
						echo '<span class="listing-price'.$version_class.'">' . get_option('vh_currency_symbol') . $value->geodir_listing_price . '</span>';
					} elseif ( get_option("vh_theme_version") != "SkyDirectory" && get_post_type() == 'gd_event' ) {
						echo '<span class="listing-price'.$version_class.'">' . get_geodir_event_date( $value->post_id ) . '</span>';
					}
					if ( $value->is_featured == "1" || strpos(strtolower($value->post_tags), 'featured') !== false ) {
						echo '<div class="map-listing-featured">'.__('Featured', 'vh').'</div>';
					} 
					if ( $value->overall_rating != 0 ) {
						$overall_rating = $value->overall_rating;
					} else {
						$overall_rating = 0;
					}
					echo '</div>';
					echo '<div class="google-map-info">';
					echo '
					<div class="google-map-rating">';
						for ($i=0; $i < $overall_rating; $i++) { 
							echo "<span class=\"listing-item-star icon-star\"></span>";
						}
						if ( 5 - $overall_rating != 0 ) {
							for ($i=0; $i < 5 - $overall_rating; $i++) { 
								echo "<span class=\"listing-item-star icon-star-empty\"></span>";
							}
						}
						echo "<span class=\"listing-item-star text\">".$overall_rating."</span>";
					echo '
					</div>';
					echo '<div class="google-map-title"><a href="' . get_permalink( $value->post_id ) . '">' . get_the_title($value->post_id) . '</a></div>';
					echo '<div class="google-map-location">' . $value->post_city . ', ' . $value->post_country . '</div>';
					echo '</div><div class="clearfix"></div></div>';
				echo '</div>';
			}

			echo "<a href=\"javascript:void(0)\" class=\"similar-listings-view single-listing-contact-author wpb_button\">".__("View all", "vh")."</a>";
		} else {
			echo '<div class="google-map-main similar">';
				echo '<div class="google-map-container empty">';
					_e('No listings to display.', 'vh');
				echo '</div>
				<div class="clearfix"></div>';
			echo '</div>';
		}

	
		echo $args['after_widget'];
	}
		
	// Widget Backend 
	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = '';
		}

		if ( isset( $instance[ 'pricerange' ] ) ) {
			$pricerange = $instance[ 'pricerange' ];
		} else {
			$pricerange = '';
		}

		if ( isset( $instance[ 'pricerangevalue' ] ) ) {
			$pricerangevalue = $instance[ 'pricerangevalue' ];
		} else {
			$pricerangevalue = '';
		}

		if ( isset( $instance[ 'location' ] ) ) {
			$location = $instance[ 'location' ];
		} else {
			$location = '';
		}

		// Widget admin form
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'pricerange' ); ?>"><?php _e( 'Search listings by price range?:', 'vh' ); ?></label>
			<input class="checkbox" type="checkbox" <?php checked($instance['pricerange'], 'on'); ?> id="<?php echo $this->get_field_id('pricerange'); ?>" name="<?php echo $this->get_field_name('pricerange'); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'pricerangevalue' ); ?>"><?php _e( 'Whats the pricing range?:', 'vh' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'pricerangevalue' ); ?>" name="<?php echo $this->get_field_name( 'pricerangevalue' ); ?>" type="text" value="<?php echo esc_attr( $pricerangevalue ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'location' ); ?>"><?php _e( 'Search listings by location?:', 'vh' ); ?></label>
			<input class="checkbox" type="checkbox" <?php checked($instance['location'], 'on'); ?> id="<?php echo $this->get_field_id('location'); ?>" name="<?php echo $this->get_field_name('location'); ?>" />
		</p>

		<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['pricerange'] = ( ! empty( $new_instance['pricerange'] ) ) ? strip_tags( $new_instance['pricerange'] ) : '';
		$instance['pricerangevalue'] = ( ! empty( $new_instance['pricerangevalue'] ) ) ? strip_tags( $new_instance['pricerangevalue'] ) : '';
		$instance['location'] = ( ! empty( $new_instance['location'] ) ) ? strip_tags( $new_instance['location'] ) : '';

		return $instance;
	}
} // Class wpb_widget ends here

// Register and load the widget
function wpb_load_widget_similar_listings() {
	register_widget( 'wpb_widget_similar_listings' );
}
add_action( 'widgets_init', 'wpb_load_widget_similar_listings' );
?>