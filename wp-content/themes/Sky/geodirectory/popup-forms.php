<?php 
$post_id = $_REQUEST['post_id']; 
$post_info = get_post($post_id);

if($_REQUEST['popuptype'] == 'b_sendtofriend'){ ?>

	<div id="basic-modal-content" class="clearfix">
	<form name="send_to_frnd" id="send_to_frnd" action="<?php echo get_permalink($post_info->ID); ?>" method="post" >
		<input type="hidden" name="sendact" value="email_frnd" />
		<input type="hidden" id="send_to_Frnd_pid" name="pid" value="<?php echo $post_info->ID;?>" />
		<div class="gdmodal-title"><?php echo apply_filters('geodir_send_to_friend_page_title',SEND_TO_FRIEND);?></div>
			<p id="reply_send_success" class="sucess_msg" style="display:none;"></p>
        <?php do_action('geodir_before_stf_form_field' , 'to_name') ;?>
		<div class="row clearfix" >
			<div class="geodir_popup_field">
			<input class="is_required" field_type="text" name="to_name" id="to_name" type="text" value=""  placeholder="<?php _e('Friend Name','vh');?>" />
			<span class="message_error2" id="to_nameInfo"></span>
			<span class="input-required"></span>
		</div>
		</div>
        <?php do_action('geodir_after_stf_form_field' , 'to_name') ;?>
        <?php do_action('geodir_before_stf_form_field' , 'to_email') ;?>
        <div class="row  clearfix" >
			<div class="geodir_popup_field">
			<input class="is_required" field_type="email" name="to_email" id="to_email" type="text" value="" placeholder="<?php _e('Email','vh');?>" />
			<span class="message_error2" id="to_emailInfo"></span>
			<span class="input-required"></span>
		</div>
		</div>
        <?php do_action('geodir_after_stf_form_field' , 'to_email') ;?>
        <?php do_action('geodir_before_stf_form_field' , 'yourname') ;?>
		<div class="row  clearfix" >
			<div class="geodir_popup_field">
			<input class="is_required" field_type="text" name="yourname" id="yourname" type="text" value="" placeholder="<?php _e('Your Name','vh');?>" />
			<span class="message_error2" id="yournameInfo"></span>
			<span class="input-required"></span>
		</div>
		</div>
        <?php do_action('geodir_after_stf_form_field' , 'yourname') ;?>
        <?php do_action('geodir_before_stf_form_field' , 'youremail') ;?>
		<div class="row  clearfix" >
			<div class="geodir_popup_field">
			<input class="is_required" field_type="email" name="youremail" id="youremail" type="text" value="" placeholder="<?php _e('Email','vh');?>" />
			<span class="message_error2" id="youremailInfo"></span>
			<span class="input-required"></span>
		</div>
		</div>
         <?php do_action('geodir_after_stf_form_field' , 'youremail') ;?>
         <?php do_action('geodir_before_stf_form_field' , 'frnd_subject') ;?> 
		<div class="row  clearfix" >
			<div class="geodir_popup_field">
			<input  class="is_required" field_type="text" name="frnd_subject" value="<?php echo __('About','vh').' '.$post_info->post_title;?>" placeholder="<?php _e('Subject','vh');?>" id="frnd_subject" type="text" value="" />
			<span class="message_error2" id="frnd_subjectInfo"></span>
			<span class="input-required"></span>
		</div>
		</div>
        <?php do_action('geodir_after_stf_form_field' , 'frnd_subject') ;?> 
        <?php do_action('geodir_before_stf_form_field' , 'frnd_comments') ;?> 
		<div class="row  clearfix" >
			<div class="geodir_popup_field">
			<textarea class="is_required" field_type="textarea" name="frnd_comments" id="frnd_comments" cols="" rows="" placeholder="<?php _e('Comments','vh');?>" ><?php echo SEND_TO_FRIEND_SAMPLE_CONTENT;?></textarea>
			<span class="message_error2" id="frnd_commentsInfo"></span>
			<span class="input-required"></span>
		</div>
		</div>
        <?php do_action('geodir_after_stf_form_field' , 'frnd_comments') ;?> 
			<?php if(function_exists('geodir_get_captch')){geodir_get_captch('-1'); }?>
		<div class="gmmodal-dialog-lower">
			<input name="Send" type="submit" value="<?php _e('Send message','vh');?>" class="clearfix wpb_button wpb_btn-primary wpb_btn-small" />
			<a href="javascript:void(0)" class="gmmodal-close-dialog wpb_button wpb_btn-inverse wpb_btn-small"><?php _e("Cancel", "vh"); ?></a>
        </div>
	</form>
	</div> <?php 

}elseif($_REQUEST['popuptype'] == 'b_send_inquiry'){ ?>

	<div id="basic-modal-content2" class="clearfix">
<form action="/members/judan/messages/compose/" method="post" id="send_message_form" class="standard-form" enctype="multipart/form-data">
		<input type="hidden" name="subject" value="send_inqury" />
	<input type="hidden" name="send-to-input" value="<?php echo $post_info->post_author;?>" />
		<div class="gdmodal-title"><?php _e("Send message to", "vh"); echo " " . "<span>".get_the_author_meta("display_name", $post_info->post_author)."</span>"; ?></div>
			<p id="inquiry_send_success" class="sucess_msg" style="display:none;"></p>
	    <?php do_action('geodir_before_inquiry_form_field' , 'inq_name') ;?> 
	     
		
	    <?php do_action('geodir_after_inquiry_form_field' , 'inq_phone') ;?>
	    <?php do_action('geodir_before_inquiry_form_field' , 'inq_msg') ;?>
		<div class="row  clearfix" >
		<div class="geodir_popup_field">
		<textarea class="is_required" field_type="textarea" name="content" cols="" rows="" placeholder="<?php _e('Message','vh');?>" ></textarea>
		<span class="message_error2" id="span_agt_mail_msg"></span></div>
	</div>
	<input type="hidden" name="send_to_usernames" id="send-to-usernames" value="" class="">
    <div class="gmmodal-dialog-lower">
		<input name="send" type="submit" value="<?php _e('Send message','vh');?>" class="clearfix wpb_button wpb_btn-primary wpb_btn-small" />
		<a href="javascript:void(0)" class="gmmodal-close-dialog wpb_button wpb_btn-inverse wpb_btn-small"><?php _e("Cancel", "vh"); ?></a>
    </div>
	</form>
	</div> 
	
	<?php
	}
?>