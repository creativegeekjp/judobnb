<?php
$unames = isset($_GET['unames']) ?  $_GET['unames'] : "" ;
?>

<form action="<?php bp_messages_form_action('compose'); ?>" method="post" id="send_message_form" class="standard-form" role="main" enctype="multipart/form-data">

	<?php do_action( 'bp_before_messages_compose_content' ); ?>

	<label for="send-to-input"><?php _e("Send To", 'buddypress'); ?></label>
	<ul class="first acfb-holder">
		<li>
			<?php bp_message_get_recipient_tabs(); ?>
			<input type="text" name="send-to-input" class="send-to-input" <?php if(strlen($unames)>0): ?> readonly <?php endif ?> id="send-to-input" value="<?php echo $unames; ?>" />
		</li>
	</ul>

	<?php if ( bp_current_user_can( 'bp_moderate' ) ) : ?>
		<?php 
			
			$current_user = wp_get_current_user();
			
			if ( is_user_logged_in() && $current_user->ID == 1 || current_user_can( 'administrator' )  ) 
			{ 
				?>
					<input type="checkbox" id="send-notice" name="send-notice" value="1" /> <?php _e( "This is a notice to all users.", "buddypress" ); ?>
				<?php
			}
		?>
	
	<?php endif; ?>

	<label for="subject"><?php _e( 'Enter subject here', 'buddypress'); ?></label>
	<input type="text" name="subject" id="subject" value="<?php bp_messages_subject_value(); ?>" />

	<label for="content"><?php _e( 'Message', 'buddypress'); ?> </label>

    <script type="text/javascript">

	function composetranslate() {
		
		
		var text = document.getElementById('message_content');
		var subject = document.getElementById('subject');
	//	http://api.microsofttranslator.com/V2/Ajax.svc/Translate?oncomplete=jsonp1461320276071&_=1461320337152&text=test&appId=E8DB680F742769E3F9B95BFDB55798C13FEB0E5C&from=en&to=ja
		var translateurl = 'http://api.microsofttranslator.com/V2/Ajax.svc/Translate?_=1461320337152&appId=E8DB680F742769E3F9B95BFDB55798C13FEB0E5C&from=en&to=ja&text=';
		
		jQuery.get(translateurl + encodeURI(text.value),function(data){
			text.value = eval(data);
		});

	}
	</script>
	
					    
	<textarea name="content" id="message_content" rows="15" cols="40"><?php bp_messages_content_value(); ?></textarea>

	<input type="hidden" name="send_to_usernames" id="send-to-usernames" value="<?php bp_message_get_recipient_usernames(); ?>" class="<?php bp_message_get_recipient_usernames() . " $unames" ; ?>" />

	<?php do_action( 'bp_after_messages_compose_content' ); ?>

	<div class="submit">
		<input type="submit" value="<?php esc_attr_e( "Send Message", 'buddypress' ); ?>" name="send" id="send" />
	</div>
	
	<?php wp_nonce_field( 'messages_send_message' ); ?>
</form>

<script type="text/javascript">
	document.getElementById("send-to-input").focus();
</script>

