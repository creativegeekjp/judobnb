<?php

/**
 * Profile editor - Widget loggedin state
 *
 * Displays this content if the user is logged in
 *
 * @since 1.0
 */

$current_user = wp_get_current_user();

?>

<div class="profile-editor">
	<ul class="pe-user-options">
		<li><?php _e('Welcome', 'profile-editor'); echo ', ' . $current_user->display_name; ?></li>
		<li><a href="<?php echo wp_logout_url( get_permalink() ); ?>"><?php _e('Logout', 'profile-editor'); ?></a></li>
	</ul>
</div>