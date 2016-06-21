<?php
/**
 * Social Widget
 *
 * @package      Sky Functionality
 * @since        1.0.0
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class vh_event_rating extends WP_Widget {
	
    /**
     * Constructor
     *
     * @return void
     **/
	function vh_event_rating() {
		$widget_ops = array( 'classname' => 'widget_event_rating', 'description' => 'Event rating widget' );
		parent::__construct( 'event-rating-widget', 'Event rating', $widget_ops );
	}

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme 
     * @param array  An array of settings for this widget instance 
     * @return void Echoes it's output
     **/
	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		echo $before_widget;
		
		if( $instance['title'] )
			echo $before_title . esc_attr( $instance['title'] ) . $after_title;
			
		echo get_most_rated_events('movies');

		echo $after_widget;
	}

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings 
     * @return array The validated and (if necessary) amended settings
     **/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title'] = esc_attr( $new_instance['title'] );

		return $instance;
	}
	
    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void Echoes it's output
     **/
	function form( $instance ) {
	
		$defaults = array( 'title' => '' );
		
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		
		echo '<p><label for="' . $this->get_field_id( 'title' ) . '">Title: <input class="widefat" id="' . $this->get_field_id( 'title' ) .'" name="' . $this->get_field_name( 'title' ) . '" value="' . esc_attr( $instance['title'] ) . '" /></label></p>';
		
	}
}

function vh_register_post_rating_widget() {
	register_widget('vh_event_rating');
}
add_action( 'widgets_init', 'vh_register_post_rating_widget' );