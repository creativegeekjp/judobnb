<?php
// Creating the widget 
class wpb_widget_contact_us extends WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'sky-directory-contact-us', 

			// Widget name will appear in UI
			__('Sky - Contact us', 'vh'), 

			// Widget description
			array( 'description' => __( 'Just a simple widget that displays your contact information.', 'vh' ), ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$phone = $instance['phone'];
		$email = $instance['email'];
		$facebook = $instance['facebook'];
		$twitter = $instance['twitter'];

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		
		echo '
		<div class="contact-us-widget-main">
			<div class="contact-us-phone">
				<span class="phone-icon icon-mobile"></span>';
				if ( !empty($phone) ) {
					echo '
					<div class="phone-info-container">
						<span class="phone-text">'.__('Call us', 'vh').':</span>
						<span class="phone-number">' . $phone . '</span>
					</div>
					';
				}
				echo '
				<div class="clearfix"></div>
			</div>
			<div class="contact-us-lower">
				<div class="contact-us-social">';
					if ( !empty($facebook) ) {
						echo '<a href="' . $facebook . '" class="contact-us-facebook icon-facebook"></a>';
					}
					if ( !empty($twitter) ) {
						echo '<a href="' . $twitter . '" class="contact-us-twitter icon-twitter-1"></a>';
					}
					echo '
					<div class="clearfix"></div>
				</div>';
				if ( !empty($email) ) {
					echo '
					<div class="contact-us-email">
						<a href="mailto:' . $email . '" class="email-address">
							<span class="email-text">Or send an email to:</span>'
						. $email .
						'</a>
					</div>';
				}
			echo '
			</div>
			<div class="clearfix"></div>
		</div>';

		echo $args['after_widget'];
	}
		
	// Widget Backend 
	public function form( $instance ) {

		if ( isset( $instance[ 'phone' ] ) ) {
			$phone = $instance[ 'phone' ];
		} else {
			$phone = '';
		}

		if ( isset( $instance[ 'email' ] ) ) {
			$email = $instance[ 'email' ];
		} else {
			$email = '';
		}

		if ( isset( $instance[ 'facebook' ] ) ) {
			$facebook = $instance[ 'facebook' ];
		} else {
			$facebook = '';
		}

		if ( isset( $instance[ 'twitter' ] ) ) {
			$twitter = $instance[ 'twitter' ];
		} else {
			$twitter = '';
		}

		// Widget admin form
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'phone' ); ?>"><?php _e( 'Phone:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'phone' ); ?>" name="<?php echo $this->get_field_name( 'phone' ); ?>" type="text" value="<?php echo esc_attr( $phone ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'email' ); ?>"><?php _e( 'Email:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'email' ); ?>" name="<?php echo $this->get_field_name( 'email' ); ?>" type="text" value="<?php echo esc_attr( $email ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'facebook' ); ?>"><?php _e( 'Facebook link:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'facebook' ); ?>" name="<?php echo $this->get_field_name( 'facebook' ); ?>" type="text" value="<?php echo esc_attr( $facebook ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter' ); ?>"><?php _e( 'Twitter link:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'twitter' ); ?>" name="<?php echo $this->get_field_name( 'twitter' ); ?>" type="text" value="<?php echo esc_attr( $twitter ); ?>" />
		</p>

		<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['phone'] = ( ! empty( $new_instance['phone'] ) ) ? strip_tags( $new_instance['phone'] ) : '';
		$instance['email'] = ( ! empty( $new_instance['email'] ) ) ? strip_tags( $new_instance['email'] ) : '';
		$instance['facebook'] = ( ! empty( $new_instance['facebook'] ) ) ? strip_tags( $new_instance['facebook'] ) : '';
		$instance['twitter'] = ( ! empty( $new_instance['twitter'] ) ) ? strip_tags( $new_instance['twitter'] ) : '';

		return $instance;
	}
} // Class wpb_widget ends here

// Register and load the widget
function wpb_load_widget_contact_us() {
	register_widget( 'wpb_widget_contact_us' );
}
add_action( 'widgets_init', 'wpb_load_widget_contact_us' );
?>