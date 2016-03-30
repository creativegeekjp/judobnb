<?php
// Creating the widget 
class wpb_widget_other_listings extends WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'sky-directory-other-listings', 

			// Widget name will appear in UI
			__('Sky - Other listings', 'vh'), 

			// Widget description
			array( 'description' => __( 'Just a simple widget that displays other listings.', 'vh' ), ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		global $post, $wpdb;
		$title = $instance['title'];
		$title_lower = '';
		$version_class = "";
		if ( get_option("vh_theme_version") == "SkyDirectory" ) {
			$version_class = " skydirectory";
		}

		$title_lower = $wlocation = '';

		// before and after widget arguments are defined by themes

		echo $args['before_widget'];

		if ( $wlocation == "on" ) {
			$title_lower .= 'Location';
		}

		if ( $wlocation == "on" ) {
			$title_lower .= ', Price range';
		}

		if ( get_post_type() == 'gd_event' ) {
			$querystr = "SELECT pd.overall_rating,pd.rating_count,pd.post_city,pd.post_country,pd.post_id,pd.post_tags,pd.is_featured FROM " . $wpdb->prefix .  "geodir_" . get_post_type() . "_detail as pd, ".$wpdb->prefix."posts as p WHERE pd.post_status = 'publish' AND p.ID = pd.post_id AND p.post_author = \"".$post->post_author."\" LIMIT 5";
		} elseif ( get_option("vh_theme_version") != "SkyDirectory" ) {
			$querystr = "SELECT pd.overall_rating,pd.rating_count,pd.geodir_listing_price,pd.post_city,pd.post_country,pd.post_id,pd.post_tags,pd.is_featured FROM " . $wpdb->prefix .  "geodir_" . get_post_type() . "_detail as pd, ".$wpdb->prefix."posts as p WHERE pd.post_status = 'publish' AND p.ID = pd.post_id AND p.post_author = \"".$post->post_author."\" LIMIT 5";
		} else {
			$querystr = "SELECT pd.overall_rating,pd.rating_count,pd.post_city,pd.post_country,pd.post_id,pd.post_tags,pd.is_featured FROM " . $wpdb->prefix .  "geodir_" . get_post_type() . "_detail as pd, ".$wpdb->prefix."posts as p WHERE pd.post_status = 'publish' AND p.ID = pd.post_id AND p.post_author = \"".$post->post_author."\" LIMIT 5";
		}

		$queryresults = $wpdb->get_results($querystr);
		
		if ( !empty($title) ) {
			echo '<div class="item-title-bg">';
			echo '<h4>' . $title . '<span>'.__("by", "vh")." ".get_userdata( get_post_field( 'post_author', $queryresults["0"]->post_id ) )->display_name.'</span></h4>';
			echo '</div>';
		}

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
					echo '<div class="google-map-title"><a href="' . get_permalink( $value->post_id ) . '">' . get_the_title( $value->post_id ) . '</a></div>';
					echo '<div class="google-map-location">' . $value->post_city . ', ' . $value->post_country . '</div>';
					echo '</div><div class="clearfix"></div></div>';
				echo '</div>';
			}
		} else {
			echo '<div class="google-map-main similar">';
				echo '<div class="google-map-container empty">';
					_e('This user hasn\'t added other listings.', 'vh');
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

		<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
} // Class wpb_widget ends here

// Register and load the widget
function wpb_load_widget_other_listings() {
	register_widget( 'wpb_widget_other_listings' );
}
add_action( 'widgets_init', 'wpb_load_widget_other_listings' );
?>