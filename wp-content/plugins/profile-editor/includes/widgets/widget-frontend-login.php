<?php

/**
* About Us widget
*/

class profile_editor_login extends WP_Widget {
	public function profile_editor_login() {
		$widget_options = array(
			'classname'   => 'profile_editor_login',
			'description' => __('Displays login form with/without register link.', 'profile-editor')
		);
		parent::__construct('profile_editor_login', __('Profile editor - Login', 'profile-editor') , $widget_options);
	}

	public function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$hide_widget = $instance['hide_widget'];

		if ( $hide_widget && is_user_logged_in() ) {
			return;
		}

		echo $before_widget;

		if ($title)
			echo $before_title . $title . $after_title;

		if ( !is_user_logged_in() ) {
			profile_editor_get_template('login-form');
		} else {
			profile_editor_get_template('loggedin-state');
		}
		

		echo $after_widget;
	}

	public function update($new_instance, $old_instance) {
		$instance              = $old_instance;
		$instance['hide_widget']     = strip_tags($new_instance['hide_widget']);

		return $instance;
	}

	public function form($instance) {
		$title  = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$hidewidget = isset($instance['hide_widget']) ? esc_attr($instance['hide_widget']) : '';

		?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'profile-editor'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'hide_widget' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'hide_widget' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_widget' ) ); ?>" type="checkbox" value="checked" <?php echo $hidewidget; ?> /> <?php _e("Hide widget if user logged in", 'profile-editor'); ?>
			</label>
		</p>

	<?php
	}
}

add_action( 'widgets_init', function(){
     register_widget( 'profile_editor_login' );
});