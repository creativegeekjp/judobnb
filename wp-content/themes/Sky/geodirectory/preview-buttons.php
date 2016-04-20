<?php
    global $wpdb,$post;
    
    $post_type = $post->listing_type;
    
	if(isset($_REQUEST['preview']) && $_REQUEST['preview'] && isset($_REQUEST['pid']) && $_REQUEST['pid'] != ''){
		$form_action_url = geodir_get_ajax_url().'&geodir_ajax=add_listing&ajax_action=update&listing_type='.$post_type;
	}elseif(isset($_REQUEST['preview']) && $_REQUEST['preview']){
		$form_action_url = geodir_get_ajax_url().'&geodir_ajax=add_listing&ajax_action=publish&listing_type='.$post_type;
	}
	
	$form_action_url = apply_filters('geodir_publish_listing_form_action' , $form_action_url ) ;
	
	
?>
<?php do_action('geodir_before_publish_listing_form');
ob_start()// start publish listing form buffering 
?>
<div class="geodir_preview_section" >
	    
	    <form action="<?php echo $form_action_url; ?>" name="publish_listing" id="publish_listing" method="post">

    	<div class="clearfix">
		<input type="hidden" name="pid" value="<?php if(isset($post->pid)){  echo $post->pid;}?>">
        <?php do_action('geodir_publish_listing_form_before_msg') ;?>    
        <?php
	        define('vh_GOING_TO_FREE_MSG',__('This is a preview of your listing and its not published yet. If there is something wrong then "Edit" or if you want to add your listing then click on "Publish". Your %s listing will be published for %s days','vh'));
			define('vh_GOING_TO_UPDATE_MSG',__('This is a preview of your listing and its not updated yet. If there is something wrong then "Edit" or if you want to update listing then click on "Update now"','vh'));
                        $alive_days = UNLIMITED;
                        $type_title = '';
						ob_start();

						echo '
						<div class="wpb_alert wpb_content_element vc_alert_square wpb_alert-info geodir-main-preview-message">
							<div class="messagebox_text">
								<p>';
								if(!isset($_REQUEST['pid']) )
		                            printf(vh_GOING_TO_FREE_MSG, $type_title,$alive_days);
		                        else
		                            printf(vh_GOING_TO_UPDATE_MSG, $type_title ,$alive_days);
		                        echo '
		                        </p>
							</div>
						</div>';
                  
						// $publish_listing_form_message = ob_get_clean();
						// $publish_listing_form_message = apply_filters('geodir_publish_listing_form_message',$publish_listing_form_message ) ;
						// echo $publish_listing_form_message ;
						// echo "test";
					
					do_action('geodir_publish_listing_form_after_msg') ;
						
						ob_start(); // start action button buffering
		?>
              <?php if(isset($_REQUEST['pid']) && $_REQUEST['pid']!='') { ?> 
            
            <input type="submit" name="Submit and Pay" value="<?php echo PRO_UPDATE_BUTTON;?>" class="geodir_button geodir_publish_button wpb_button wpb_btn-primary wpb_btn-small input_button" />
            <?php } else { ?>
			     	<input type="submit" name="Submit and Pay" value="<?php echo PRO_SUBMIT_BUTTON;?>" class=" geodir_button geodir_publish_button wpb_button wpb_btn-primary wpb_btn-small input_button" />
				<?php		
				}
					$publish_listing_form_button = ob_get_clean();
					$publish_listing_form_button = apply_filters('geodir_publish_listing_form_button',$publish_listing_form_button) ;
					echo $publish_listing_form_button ;
						
					
			
					$post_id = '';
					if(isset($post->pid)){
						$post_id = $post->pid;
					}elseif(isset($_REQUEST['pid'])){
						$post_id = $_REQUEST['pid'];
					}
					$postlink = get_permalink( get_option('geodir_add_listing_page') );
					$postlink = geodir_getlink($postlink,array('pid'=>$post_id,'backandedit'=>'1','listing_type'=>$post_type),false);
					
					ob_start(); // start go back and edit / cancel buffering		 
            ?>
            <a href="<?php echo $postlink;?>" class="geodir_goback wpb_button wpb_btn-warning wpb_btn-small" ><?php echo __("Edit", "vh"); ?></a>
           <input type="button" name="Cancel" value="<?php echo (PRO_CANCEL_BUTTON);?>" class="geodir_button geodir_cancle_button wpb_button wpb_btn-inverse wpb_btn-small input_button"  onclick="window.location.href='<?php echo geodir_get_ajax_url().'&geodir_ajax=add_listing&ajax_action=cancel&pid='.$post_id.'&listing_type='.$post_type;?>'" />
        	<?php
            	
					$publish_listing_form_go_back = ob_get_clean();
					$publish_listing_form_go_back = apply_filters('geodir_publish_listing_form_go_back',$publish_listing_form_go_back) ;
					echo $publish_listing_form_go_back ;
						
			?>
        </div>
    </form> 
</div>
<?php 
$publish_listing_form = ob_get_clean();
$publish_listing_form = apply_filters('geodir_publish_listing_form',$publish_listing_form) ;
echo $publish_listing_form ;

do_action('geodir_after_publish_listing_form') ;
?>