<?php

/**
 * Profile editor - Widget login form
 *
 * Displays the login form for the widget
 *
 * @since 1.0
 */

$pe_settings = new Profile_editor_Settings(null);
$login_json = $pe_settings->profile_editor_login_form_login();
$login_error = json_decode($login_json);

?>

<div class="profile-editor">
	<?php if ( $login_error != null && !empty($login_error->message) ) { ?>
			<span class="pe-error-message"><strong><?php _e('ERROR: '); ?></strong><?php echo $login_error->message; ?></span>
	<?php } ?>
	<form class="pe-login-form" id="widget-login" action="" method="post">
		<input type="text" name="pe-login" placeholder="<?php _e( 'Username', 'profile-editor' ); ?>"><br>
		<input type="password" name="pe-password" placeholder="<?php _e( 'Password', 'profile-editor' ); ?>"><br>
		<input type="checkbox" id="pe-rememberme" class="rememberme" name="pe-rememberme">
		<label for="pe-rememberme"><?php _e( 'Remember me', 'profile-editor' ); ?></label><br>
		<a href="<?php echo wp_lostpassword_url(); ?>"><?php _e( 'Forgot password?', 'profile-editor' ); ?></a>
		<?php if ( get_option('pe_register_page_url') != '' ) { ?>
			<a href="<?php echo get_option('pe_register_page_url'); ?>"><?php _e( 'Not a member?', 'profile-editor' ); ?></a>
		<?php } ?>

		<?php wp_nonce_field( 'pe-login-nonce', 'pe-login-security' ); ?>
		<input class="grey-button" type="submit" name="commit" value="<?php _e( 'Sign In', 'profile-editor' ); ?>">
	</form>
</div>