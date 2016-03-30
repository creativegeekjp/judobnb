<?php
// Creating the widget 
class wpb_widget_useful_links extends WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'sky-directory-useful-links', 

			// Widget name will appear in UI
			__('Sky - Useful links', 'vh'), 

			// Widget description
			array( 'description' => __( 'Just a simple widget that displays useful links.', 'vh' ), ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$title = apply_filters('widget_title', $instance['title'] );
		$link1 = $instance['link1'];
		$link1name = $instance['link1name'];
		$link2 = $instance['link2'];
		$link2name = $instance['link2name'];
		$link3 = $instance['link3'];
		$link3name = $instance['link3name'];
		$link4 = $instance['link4'];
		$link4name = $instance['link4name'];
		$link5 = $instance['link5'];
		$link5name = $instance['link5name'];
		$halfwidth = $instance['halfwidth'];
		$class = '';

		// before and after widget arguments are defined by themes
		if ( !empty($halfwidth) ) {
			$class = ' style="width: 50%;float:left;">';
		}

		echo rtrim($args['before_widget'], '>').$class;
		
		if ( !empty($title) ) {
			echo '
			<div class="useful-links-title">
				' . $title . '
			</div>';
		}

		if ( !empty($link1) && !empty($link1name) ) {
			echo '<a href="' . $link1 . '">' . $link1name . '</a><br />';
		}

		if ( !empty($link2) && !empty($link2name) ) {
			echo '<a href="' . $link2 . '">' . $link2name . '</a><br />';
		}

		if ( !empty($link3) && !empty($link3name) ) {
			echo '<a href="' . $link3 . '">' . $link3name . '</a><br />';
		}

		if ( !empty($link4) && !empty($link4name) ) {
			echo '<a href="' . $link4 . '">' . $link4name . '</a><br />';
		}

		if ( !empty($link5) && !empty($link5name) ) {
			echo '<a href="' . $link5 . '">' . $link5name . '</a><br />';
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

		if ( isset( $instance[ 'link1' ] ) ) {
			$link1 = $instance[ 'link1' ];
		} else {
			$link1 = '';
		}

		if ( isset( $instance[ 'link1name' ] ) ) {
			$link1name = $instance[ 'link1name' ];
		} else {
			$link1name = '';
		}

		if ( isset( $instance[ 'link2' ] ) ) {
			$link2 = $instance[ 'link2' ];
		} else {
			$link2 = '';
		}

		if ( isset( $instance[ 'link2name' ] ) ) {
			$link2name = $instance[ 'link2name' ];
		} else {
			$link2name = '';
		}

		if ( isset( $instance[ 'link3' ] ) ) {
			$link3 = $instance[ 'link3' ];
		} else {
			$link3 = '';
		}

		if ( isset( $instance[ 'link3name' ] ) ) {
			$link3name = $instance[ 'link3name' ];
		} else {
			$link3name = '';
		}

		if ( isset( $instance[ 'link4' ] ) ) {
			$link4 = $instance[ 'link4' ];
		} else {
			$link4 = '';
		}

		if ( isset( $instance[ 'link4name' ] ) ) {
			$link4name = $instance[ 'link4name' ];
		} else {
			$link4name = '';
		}

		if ( isset( $instance[ 'link5' ] ) ) {
			$link5 = $instance[ 'link5' ];
		} else {
			$link5 = '';
		}

		if ( isset( $instance[ 'link5name' ] ) ) {
			$link5name = $instance[ 'link5name' ];
		} else {
			$link5name = '';
		}

		if ( isset( $instance[ 'halfwidth' ] ) ) {
			$halfwidth = $instance[ 'halfwidth' ];
		} else {
			$halfwidth = '';
		}

		// Widget admin form
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'link1name' ); ?>"><?php _e( 'Link 1 title:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'link1name' ); ?>" name="<?php echo $this->get_field_name( 'link1name' ); ?>" type="text" value="<?php echo esc_attr( $link1name ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'link1' ); ?>"><?php _e( 'Link 1:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'link1' ); ?>" name="<?php echo $this->get_field_name( 'link1' ); ?>" type="text" value="<?php echo esc_attr( $link1 ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'link2name' ); ?>"><?php _e( 'Link 2 title:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'link2name' ); ?>" name="<?php echo $this->get_field_name( 'link2name' ); ?>" type="text" value="<?php echo esc_attr( $link2name ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'link2' ); ?>"><?php _e( 'Link 2:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'link2' ); ?>" name="<?php echo $this->get_field_name( 'link2' ); ?>" type="text" value="<?php echo esc_attr( $link2 ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'link3name' ); ?>"><?php _e( 'Link 3 title:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'link3name' ); ?>" name="<?php echo $this->get_field_name( 'link3name' ); ?>" type="text" value="<?php echo esc_attr( $link3name ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'link3' ); ?>"><?php _e( 'Link 3:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'link3' ); ?>" name="<?php echo $this->get_field_name( 'link3' ); ?>" type="text" value="<?php echo esc_attr( $link3 ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'link4name' ); ?>"><?php _e( 'Link 4 title:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'link4name' ); ?>" name="<?php echo $this->get_field_name( 'link4name' ); ?>" type="text" value="<?php echo esc_attr( $link4name ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'link4' ); ?>"><?php _e( 'Link 4:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'link4' ); ?>" name="<?php echo $this->get_field_name( 'link4' ); ?>" type="text" value="<?php echo esc_attr( $link4 ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'link5name' ); ?>"><?php _e( 'Link 5 title:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'link5name' ); ?>" name="<?php echo $this->get_field_name( 'link5name' ); ?>" type="text" value="<?php echo esc_attr( $link5name ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'link5' ); ?>"><?php _e( 'Link 5:', 'vh' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'link5' ); ?>" name="<?php echo $this->get_field_name( 'link5' ); ?>" type="text" value="<?php echo esc_attr( $link5 ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'halfwidth' ); ?>"><?php _e( 'Is this widget half width?:', 'vh' ); ?></label>
			<input class="checkbox" type="checkbox" <?php checked($instance['halfwidth'], 'on'); ?> id="<?php echo $this->get_field_id('halfwidth'); ?>" name="<?php echo $this->get_field_name('halfwidth'); ?>" />
		</p>

		<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['link1'] = ( ! empty( $new_instance['link1'] ) ) ? strip_tags( $new_instance['link1'] ) : '';
		$instance['link1name'] = ( ! empty( $new_instance['link1name'] ) ) ? strip_tags( $new_instance['link1name'] ) : '';
		$instance['link2'] = ( ! empty( $new_instance['link2'] ) ) ? strip_tags( $new_instance['link2'] ) : '';
		$instance['link2name'] = ( ! empty( $new_instance['link2name'] ) ) ? strip_tags( $new_instance['link2name'] ) : '';
		$instance['link3'] = ( ! empty( $new_instance['link3'] ) ) ? strip_tags( $new_instance['link3'] ) : '';
		$instance['link3name'] = ( ! empty( $new_instance['link3name'] ) ) ? strip_tags( $new_instance['link3name'] ) : '';
		$instance['link4'] = ( ! empty( $new_instance['link4'] ) ) ? strip_tags( $new_instance['link4'] ) : '';
		$instance['link4name'] = ( ! empty( $new_instance['link4name'] ) ) ? strip_tags( $new_instance['link4name'] ) : '';
		$instance['link5'] = ( ! empty( $new_instance['link5'] ) ) ? strip_tags( $new_instance['link5'] ) : '';
		$instance['link5name'] = ( ! empty( $new_instance['link5name'] ) ) ? strip_tags( $new_instance['link5name'] ) : '';
		$instance['halfwidth'] = ( ! empty( $new_instance['halfwidth'] ) ) ? strip_tags( $new_instance['halfwidth'] ) : '';

		return $instance;
	}
} // Class wpb_widget ends here

// Register and load the widget
function wpb_load_widget_useful_links() {
	register_widget( 'wpb_widget_useful_links' );
}
add_action( 'widgets_init', 'wpb_load_widget_useful_links' );
?>