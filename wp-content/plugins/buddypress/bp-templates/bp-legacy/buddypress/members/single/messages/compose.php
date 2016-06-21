<?php
$current_user = wp_get_current_user();

if ( 0 == $current_user->ID ) {
   
   
} else {


    /**
     * @example Safe usage: $current_user = wp_get_current_user();
     * if ( !($current_user instanceof WP_User) )
     *     return;
     */
    // echo 'Username: ' . $current_user->user_login . '<br />';
    // echo 'User email: ' . $current_user->user_email . '<br />';
    // echo 'User first name: ' . $current_user->user_firstname . '<br />';
    // echo 'User last name: ' . $current_user->user_lastname . '<br />';
    // echo 'User display name: ' . $current_user->display_name . '<br />';
    // echo 'User ID: ' . $current_user->ID . '<br />';
}
?>

<?php
/**
 * BuddyPress - Members Single Messages Compose
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>


<form action="<?php bp_messages_form_action('compose' ); ?>" method="post" id="send_message_form" class="standard-form" enctype="multipart/form-data">

	<?php

	/**
	 * Fires before the display of message compose content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_messages_compose_content' ); ?>

	<label for="send-to-input"><?php _e("Send To (Username or Friend's Name)", 'buddypress' ); ?></label>
	<ul class="first acfb-holder">
		<li>
			<?php bp_message_get_recipient_tabs(); ?>
			<input type="text" name="send-to-input" class="send-to-input" id="send-to-input" value="<?php echo $current_user->user_login; ?>"/>
		</li>
	</ul>
	
	<?php if ( bp_current_user_can( 'bp_moderate' ) ) : ?>
			<input type="checkbox" id="send-notice" <?php if( $current_user->ID == 1 || current_user_can( 'administrator' ) ){ echo ""; } else { echo "disabled=disabled"; } ?> name="send-notice" value="1" /> <?php _e( "This is a notice to all users.", "buddypress" ); ?>
	<?php endif; ?>

	<label for="subject"><?php _e( 'Subject', 'buddypress' ); ?></label>
	<input type="text" name="subject" id="subject" value="<?php bp_messages_subject_value(); ?>" />

	<label for="message_content"><?php _e( 'Message', 'buddypress' ); ?></label>
	<textarea name="content" id="message_content" rows="15" cols="40"><?php bp_messages_content_value(); ?></textarea>

	<input type="hidden" name="send_to_usernames" id="send-to-usernames" value="<?php bp_message_get_recipient_usernames(); ?>" class="<?php bp_message_get_recipient_usernames(); ?>" />

	<?php

	/**
	 * Fires after the display of message compose content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_messages_compose_content' ); ?>

	<div class="submit">
		<input type="submit" value="<?php esc_attr_e( "Send Message", 'buddypress' ); ?>" name="send" id="send" />
	</div>

	<?php wp_nonce_field( 'messages_send_message' ); ?>
</form>

<script type="text/javascript">
	document.getElementById("send-to-input").focus();
</script>

