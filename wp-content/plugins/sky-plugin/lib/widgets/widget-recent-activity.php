<?php
// Creating the widget 
class wpb_widget_recent_activity extends WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'sky-directory-recent-activity', 

			// Widget name will appear in UI
			__('Sky - Recent activity', 'vh'), 

			// Widget description
			array( 'description' => __( 'Just a simple widget that displays recent activity for opened listing.', 'vh' ), ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		global $post, $wpdb, $wp_query;
		$maintitle      = $instance['maintitle'];
		$secondarytitle = $instance['secondarytitle'];
		$limit          = $instance['limit'];
		$thePostID      = $wp_query->post->ID;

		// before and after widget arguments are defined by themes

		echo $args['before_widget'];
		
		if ( !empty($maintitle) ) {
			if ( !empty($secondarytitle) ) {
				$secondarytitlehtml = '<span>'.$secondarytitle.'</span>';
			} else {
				$secondarytitlehtml = '';
			}
			echo '<div class="item-title-bg">';
			echo '<h4>' . $maintitle . $secondarytitlehtml . '</h4>';
			echo '</div>';
		}

		if ( empty($limit) ) {
			$limit = 5;
		}

		$querystr = "SELECT c.comment_post_ID as PostID, null as PostTitle, pd.post_country as ListingCountry, pd.post_city as ListingCity, null as OverallRating, c.comment_content as CommentContent, c.comment_ID as CommentID, c.comment_date as ItemDate, c.comment_author as Author, c.user_id as AuthorID FROM " . $wpdb->prefix . "comments c," . $wpdb->prefix .  "geodir_".get_post_type( $thePostID )."_detail pd WHERE pd.post_id=c.comment_post_ID AND c.comment_post_id=".$thePostID." AND c.comment_approved=1 AND c.comment_parent != 0 UNION ALL SELECT pr.post_id as PostID,pr.post_title as PostTitle, pd.post_country as ListingCountry, pd.post_city as ListingCity, pr.overall_rating as OverallRating, null as CommentContent, pr.id as CommentID, pr.post_date as ItemDate, c.comment_author as Author, c.user_id as AuthorID FROM " . $wpdb->prefix .  "geodir_post_review pr," . $wpdb->prefix .  "geodir_".get_post_type()."_detail pd," . $wpdb->prefix . "comments c WHERE pr.post_id=pd.post_id AND pr.post_id=".$thePostID." AND c.comment_ID=pr.comment_id AND pr.status=1 ORDER BY ItemDate DESC LIMIT ".$limit;
		$queryresults = $wpdb->get_results($querystr);

		if ( !empty($queryresults) ) {
			foreach ($queryresults as $value) {
				$comment_data = get_comment($value->CommentID);

				if ( $value->OverallRating != null ) {
					if ( $value->OverallRating == 1 ) {
						$rating_text = get_listing_rating_stars($value->OverallRating, false) . '<span>' . $value->OverallRating . ' ' . __('star. Very bad!', 'vh') . '</span>';
					} elseif ( $value->OverallRating == 2 ) {
						$rating_text = get_listing_rating_stars($value->OverallRating, false) . '<span>' . $value->OverallRating . ' ' . __('stars. Bad!', 'vh') . '</span>';
					} elseif ( $value->OverallRating == 3 ) {
						$rating_text = get_listing_rating_stars($value->OverallRating, false) . '<span>' . $value->OverallRating . ' ' . __('stars. Good!', 'vh') . '</span>';
					} elseif ( $value->OverallRating == 4 ) {
						$rating_text = get_listing_rating_stars($value->OverallRating, false) . '<span>' . $value->OverallRating . ' ' . __('stars. Very good!', 'vh') . '</span>';
					} elseif ( $value->OverallRating == 5 ) {
						$rating_text = get_listing_rating_stars($value->OverallRating, false) . '<span>' . $value->OverallRating . ' ' . __('stars. Excellent!', 'vh') . '</span>';
					}

					if ( $value->AuthorID != '0' ) {
						if ( get_option('permalink_structure') ) {
							$dash_symbol = '?';
						} else {
							$dash_symbol = '&';
						}
						$author_link = "<a href='". get_author_posts_url( $value->AuthorID ).$dash_symbol."geodir_dashbord=true' class='item-author'>". get_userdata( $value->AuthorID )->display_name . "</a>";
					} else {
						$author_link = '<span class="guest-activity">'. $value->Author.'</span>';
					}

					echo "
					<div class='recent-activities ratings'>
						<div class='info-container'>
							<div class='main-item-text'>
								".$author_link."
								<span class='item-text'>" . __('rated', 'vh') . "</span>
								<a href='".get_permalink($thePostID)."' class='item-link'>" . get_the_title($thePostID) . "</a>
								<span class='activity-location'>(" . $value->ListingCountry . ", " . $value->ListingCity . ")</span>
							</div>
							<div class='separator-container'>
								<span class='item-seperator icon-star'></span>
							</div>
							<div class='activity-item-text'>" . $rating_text . "</div>
							<div class='clearfix'></div>
						</div>
					</div>";
				} elseif ( $comment_data->comment_parent != '0' && $value->CommentContent != null ) {
					if ( strlen($value->CommentContent) > 80 ) {
						$comment_content = substr($value->CommentContent, 0, 80) . '..';
					} else {
						$comment_content = $value->CommentContent;
					}

					if ( $comment_data->user_id != '0' ) {
						if ( get_option('permalink_structure') ) {
							$dash_symbol = '?';
						} else {
							$dash_symbol = '&';
						}
						$author_link = "<a href='". get_author_posts_url( $comment_data->user_id ).$dash_symbol."geodir_dashbord=true' class='item-author'>". get_userdata( $comment_data->user_id )->display_name . "</a>";
					} else {
						$author_link = '<span class="guest-activity">'.$comment_data->comment_author.'</span>';
					}

					echo "
					<div class='recent-activities comments'>
						<div class='info-container'>
							<div class='main-item-text'>
								".$author_link."
								<span class='item-text'>" . __('commented on', 'vh') . "</span>
								<a href='".get_permalink($thePostID)."' class='item-link'>" . get_the_title($thePostID) . "</a>
								<span class='activity-location'>(" . $value->ListingCountry . ", " . $value->ListingCity . ")</span>
							</div>
							<div class='separator-container'>
								<span class='item-seperator icon-comment-1'></span>
							</div>
							<div class='activity-item-text'>" . $value->CommentContent . "</div>
							<div class='clearfix'></div>
						</div>
					</div>";
				}

			}
		} else {
			echo "
			<div class='recent-activities'>
				<div class='info-container empty'>
					<div class='main-item-text'>";
						_e('There hasn\'t been any activity in this listing.', 'vh');
					echo "
					</div>
					<div class='clearfix'></div>
				</div>
			</div>";
		}
	
		echo $args['after_widget'];
	}
		
	// Widget Backend 
	public function form( $instance ) {

		if ( isset( $instance[ 'maintitle' ] ) ) {
			$maintitle = $instance[ 'maintitle' ];
		} else {
			$maintitle = '';
		}

		if ( isset( $instance[ 'secondarytitle' ] ) ) {
			$secondarytitle = $instance[ 'secondarytitle' ];
		} else {
			$secondarytitle = '';
		}

		if ( isset( $instance[ 'limit' ] ) ) {
			$limit = $instance[ 'limit' ];
		} else {
			$limit = '';
		}

		// Widget admin form
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'maintitle' ); ?>"><?php _e( 'Main title:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'maintitle' ); ?>" name="<?php echo $this->get_field_name( 'maintitle' ); ?>" type="text" value="<?php echo esc_attr( $maintitle ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'secondarytitle' ); ?>"><?php _e( 'Secondary title:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'secondarytitle' ); ?>" name="<?php echo $this->get_field_name( 'secondarytitle' ); ?>" type="text" value="<?php echo esc_attr( $secondarytitle ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Whats the limit?:', 'vh' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" />
		</p>

		<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['maintitle'] = ( ! empty( $new_instance['maintitle'] ) ) ? strip_tags( $new_instance['maintitle'] ) : '';
		$instance['secondarytitle'] = ( ! empty( $new_instance['secondarytitle'] ) ) ? strip_tags( $new_instance['secondarytitle'] ) : '';
		$instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : '';

		return $instance;
	}
} // Class wpb_widget ends here

// Register and load the widget
function wpb_load_widget_recent_activity() {
	register_widget( 'wpb_widget_recent_activity' );
}
add_action( 'widgets_init', 'wpb_load_widget_recent_activity' );
?>