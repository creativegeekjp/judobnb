<?php

// Geodirectory functions that needs to be overwritten

function vh_imgresize($url, $width, $height = null, $crop = null, $single = true) {

	//validate inputs
	if (!$url OR !$width)
		return false;

	//define upload path & dir
	$upload_info = wp_upload_dir();
	$upload_dir  = $upload_info['basedir'];
	$upload_url  = $upload_info['baseurl'];

	//check if $img_url is local
	if (strpos($url, $upload_url) === false)
		return false;

	//define path of image
	$rel_path = str_replace($upload_url, '', $url);
	$img_path = $upload_dir . $rel_path;

	//check if img path exists, and is an image indeed
	if (!file_exists($img_path) OR !getimagesize($img_path))
		return false;

	//get image info
	$info                  = pathinfo($img_path);
	$ext                   = $info['extension'];
	list($orig_w, $orig_h) = getimagesize($img_path);

	//get image size after cropping
	$dims  = image_resize_dimensions($orig_w, $orig_h, $width, $height, $crop);
	$dst_w = $dims[4];
	$dst_h = $dims[5];

	//use this to check if cropped image already exists, so we can return that instead
	$suffix       = "{$dst_w}x{$dst_h}";
	$dst_rel_path = str_replace('.' . $ext, '', $rel_path);
	$destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

	if (!$dst_h) {
	//can't resize, so return original url
		$img_url = $url;
		$dst_w   = $orig_w;
		$dst_h   = $orig_h;

	//else check if cache exists
	} elseif (file_exists($destfilename) && getimagesize($destfilename)) {
		$img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";

	//else, we resize the image and return the new resized image url
	} else {

		// Note: pre-3.5 fallback check 
		if (function_exists('wp_get_image_editor')) {

			$editor = wp_get_image_editor($img_path);

			if (is_wp_error($editor) || is_wp_error($editor->resize($width, $height, $crop)))
				return false;

			$resized_file = $editor->save();

			if (!is_wp_error($resized_file)) {
				$resized_rel_path = str_replace($upload_dir, '', $resized_file['path']);
				$img_url          = $upload_url . $resized_rel_path;
			} else {
				return false;
			}
		} else {

			$resized_img_path = image_resize($img_path, $width, $height, $crop);
			if (!is_wp_error($resized_img_path)) {
				$resized_rel_path = str_replace($upload_dir, '', $resized_img_path);
				$img_url          = $upload_url . $resized_rel_path;
			} else {
				return false;
			}
		}
	}

	//return the output
	if ($single) {
	//str return
		$image = $img_url;
	} else {
	//array return
		$image = array(
			0 => $img_url,
			1 => $dst_w,
			2 => $dst_h
		);
	}

	return $image;
}

add_action( 'geodir_add_listing_form_new', 'vh_geodir_action_add_listing_form', 11);
function vh_geodir_action_add_listing_form() {
global $cat_display,$post_cat, $current_user;
 $post = '';
 $title = '';
 $desc = '';
 $kw_tags = '';
 $required_msg = '';
 $submit_button = '';
	
	if(isset($_REQUEST['ajax_action'])) $ajax_action = $_REQUEST['ajax_action'];  else $ajax_action = 'add';
	
	$thumb_img_arr = array();
	$curImages = '';
	
	if(isset($_REQUEST['backandedit'])){
		global $post ;
		$post = (object)unserialize($_SESSION['listing']);
		$listing_type = $post->listing_type;	
		$title = $post->post_title;
		$desc = $post->post_desc;
		/*if(is_array($post->post_category) && !empty($post->post_category))
			$post_cat = $post->post_category;
		else*/
			$post_cat = $post->post_category;	
			
		$kw_tags = $post->post_tags;
		$curImages = isset($post->post_images) ? $post->post_images : '';
	}elseif(isset($_REQUEST['pid']) && $_REQUEST['pid'] != ''){
		
		global $post,$post_images;
		
		/*query_posts(array('p'=>$_REQUEST['pid']));
		if ( have_posts() ) while ( have_posts() ) the_post(); global $post,$post_images;*/
		
		$post = geodir_get_post_info($_REQUEST['pid']);
		$thumb_img_arr = geodir_get_images($post->ID);
		if($thumb_img_arr){
			foreach($thumb_img_arr as $post_img){	
				$curImages .= $post_img->src.',';
			}
		}
		
		$listing_type = $post->post_type;
		$title = $post->post_title;
		$desc = $post->post_content;
		//$post_cat = $post->categories;
		$kw_tags = $post->post_tags;
		$kw_tags = implode(",",wp_get_object_terms($post->ID,$listing_type.'_tags' ,array('fields'=>'names')));

		wp_reset_query();
		wp_reset_postdata();
	}else{
		$listing_type = $_REQUEST['listing_type'];
	}
		
	if($current_user->ID != '0'){$user_login = true;}	
	?>
	<?php
	if ( LAYOUT == 'sidebar-no' ) {
		$span_size = 'vc_col-sm-12';
	} else {
		$span_size = 'vc_col-sm-9';
	}
	?>
	<?php
	wp_reset_postdata();
	if (LAYOUT == 'sidebar-left') {
	?>
	<div class="vc_col-sm-3 <?php echo LAYOUT; ?>">
		<div class="sidebar-inner">
		<?php
			global $vh_is_in_sidebar;
			$vh_is_in_sidebar = true;
			generated_dynamic_sidebar();
		?>
		</div>
	</div><!--end of sidebars-->
	<?php } ?>
<div class="geodirectory-add-property-container <?php echo LAYOUT.' '.$span_size; ?>">
	
	<?php //echo site_url().''.langs()."/listing-preview/" ;  ?>
	
	<form name="propertyform"id="propertyform" action="<?php echo site_url().''.langs()."/listing-preview/" ;?>" method="post" enctype="multipart/form-data">
					<input type="hidden" name="preview" value="<?php echo esc_attr($listing_type);?>" />
					<input type="hidden" name="listing_type" value="<?php echo esc_attr($listing_type);?>" />
					<?php if(isset($_REQUEST['pid']) && $_REQUEST['pid'] !='') { ?>
					<input type="hidden" name="pid" value="<?php echo $_REQUEST['pid'];?>" />
					<?php } ?>
					<?php if(isset($_REQUEST['backandedit'])) { ?>
					<input type="hidden" name="backandedit" value="<?php echo esc_attr($_REQUEST['backandedit']);?>" />
					<?php } ?>
					   <?php do_action('geodir_before_detail_fields');?>
						<div class="geodir_add_listing_fields">
						<h3><?php _e('Property details', 'vh'); ?></h3>
					   
						<?php do_action('geodir_before_main_form_fields');?>
											
						<div id="geodir_post_title_row" class="required_field geodir_form_row clearfix">
							<span class="input-required"></span>
							<input type="text" field_type="text" name="post_title" id="post_title" class="geodir_textfield" placeholder="<?php echo _e(PLACE_TITLE_TEXT,'geodirectory'); ?>" value="<?php echo esc_attr(stripslashes($title)); ?>"  />
							<span class="geodir_message_error"><?php _e($required_msg,'geodirectory');?></span>
						</div>
						
						<?php 
						$show_editor = get_option('geodir_tiny_editor_on_add_listing');
						
						$desc = $show_editor ? stripslashes($desc) : esc_attr(stripslashes($desc));
						$desc_limit = '';
						$desc_limit = apply_filters('geodir_description_field_desc_limit', $desc_limit);
						$desc = apply_filters('geodir_description_field_desc', $desc, $desc_limit);
						$desc_limit_msg = '';
						$desc_limit_msg = apply_filters('geodir_description_field_desc_limit_msg', $desc_limit_msg, $desc_limit);
						?>
						<?php do_action('geodir_before_description_field'); ?>				
						<div id="geodir_post_desc_row" class="<?php if($desc_limit!='0') { echo 'required_field'; }?> geodir_form_row clearfix">
							<?php if($desc_limit!='0') { echo '<span class="input-required"></span>'; }?>				
								<?php												
								if(!empty($show_editor) && in_array($listing_type, $show_editor)){
									
									$editor_settings = array('media_buttons'=>false, 'textarea_rows'=>10);?>
									
									<div class="editor" field_id="post_desc" field_type="editor">
									<?php wp_editor( $desc, "post_desc", $editor_settings ); ?>
									</div>
									<?php if ($desc_limit!='') { ?>
									<script type="text/javascript">jQuery('textarea#post_desc').attr('maxlength', "<?php echo esc_attr($desc_limit);?>");</script>
									<?php } ?>
								<?php } else { ?>
								<textarea field_type="textarea" name="post_desc" id="post_desc" class="geodir_textarea" placeholder="<?php echo _e(PLACE_DESC_TEXT,'geodirectory'); ?>" maxlength="<?php echo esc_attr($desc_limit);?>"><?php echo $desc;?></textarea>
								<?php } ?>
							<?php if ($desc_limit_msg!='') { ?>
							<span class="geodir_message_note"><?php echo _e($desc_limit_msg,'geodirectory');?></span>
							<?php } ?>
						   <span class="geodir_message_error"><?php echo _e($required_msg, 'geodirectory');?></span>
						</div>
						<?php do_action('geodir_after_description_field'); ?>
						
						<?php 
						$kw_tags = esc_attr(stripslashes($kw_tags));
						$kw_tags_count = TAGKW_TEXT_COUNT;
						$kw_tags_msg = TAGKW_MSG;
						$kw_tags_count = apply_filters('geodir_listing_tags_field_tags_count', $kw_tags_count);
						$kw_tags = apply_filters('geodir_listing_tags_field_tags', $kw_tags, $kw_tags_count);
						$kw_tags_msg = apply_filters('geodir_listing_tags_field_tags_msg', $kw_tags_msg, $kw_tags_count);
						?>
						<?php
						do_action('geodir_before_listing_tags_field');
						?>
						<div id="geodir_post_tags_row" class="geodir_form_row clearfix" >
							 <input name="post_tags" id="post_tags" value="<?php echo esc_attr($kw_tags); ?>" onchange="javascript:f();" type="text" class="geodir_textfield" placeholder="<?php echo _e(TAGKW_TEXT,'geodirectory'); ?>" maxlength="<?php echo esc_attr($kw_tags_count);?>"  />
							 <span class="geodir_message_note"><?php echo _e($kw_tags_msg,'geodirectory');?></span>
						</div>
						<?php do_action('geodir_after_listing_tags_field'); ?>
					  
					   <?php 
								
								
								$package_info = array() ;
								
								$package_info = geodir_post_package_info($package_info , $post);
						
							vh_geodir_get_custom_fields_html($package_info->pid,'all',$listing_type);
							?>
				  
						
						<?php 
						// adjust values here
						$id = "post_images"; // this will be the name of form field. Image url(s) will be submitted in $_POST using this key. So if $id == “img1” then $_POST[“img1”] will have all the image urls
						 
						$multiple = true; // allow multiple files upload
						 
						$width = geodir_media_image_large_width(); // If you want to automatically resize all uploaded images then provide width here (in pixels)
						 
						$height = geodir_media_image_large_height(); // If you want to automatically resize all uploaded images then provide height here (in pixels)
						
						$thumb_img_arr = array();
						$totImg = 0;
						if(isset($_REQUEST['backandedit']) && empty($_REQUEST['pid']))
						{
							$post = (object)unserialize($_SESSION['listing']);
							if(isset($post->post_images))
								$curImages  = trim($post->post_images,",");
							
							
							if($curImages != ''){
								$curImages_array = explode(',',$curImages);						
								$totImg = count($curImages_array);
							}
							
							$listing_type = $post->listing_type;
						
						}elseif(isset($_REQUEST['pid']) && $_REQUEST['pid'] != '')
						{
							$post = geodir_get_post_info($_REQUEST['pid']);
							$listing_type = $post->post_type;
							$thumb_img_arr = geodir_get_images($_REQUEST['pid']);
						
						}else
						{
							$listing_type = $_REQUEST['listing_type'];
						}
			
		
						if(!empty($thumb_img_arr))
						{
							foreach($thumb_img_arr as $img){
								//$curImages = $img->src.",";
							}	
							
							$totImg = count((array)$thumb_img_arr);
						}	
						
						if($curImages != '')
						$svalue = $curImages; // this will be initial value of the above form field. Image urls.
						else
						$svalue = '';		
						
						$image_limit = $package_info->image_limit;
						$show_image_input_box = ($image_limit != '0') ;
						$show_image_input_box = apply_filters('geodir_image_uploader_on_add_listing' , $show_image_input_box  ,$listing_type  ) ;
						if($show_image_input_box){
						?>
						
						<h3><?php _e("Images", "vh"); ?></h3>
						 
						<div class="required_field geodir_form_row clearfix" id="<?php echo esc_attr($id); ?>dropbox" align="center" style="border:1px solid #ccc; min-height:100px; height:auto; padding:10px;">
							<span class="input-required" style="background-color: rgb(255, 102, 0);"></span>
							<input type="hidden" name="<?php echo esc_attr($id); ?>" id="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($svalue); ?>" />
							<input type="hidden" name="<?php echo esc_attr($id); ?>image_limit" id="<?php echo esc_attr($id); ?>image_limit" value="<?php echo esc_attr($image_limit); ?>" />
							<input type="hidden" name="<?php echo esc_attr($id); ?>totImg" id="<?php echo esc_attr($id); ?>totImg" value="<?php echo esc_attr($totImg); ?>" />
							<div class="required_field plupload-upload-uic hide-if-no-js <?php if ($multiple): ?>plupload-upload-uic-multiple<?php endif; ?>" id="<?php echo esc_attr($id); ?>plupload-upload-ui">
								<span class="addlisting-upload-text"><?php _e('Drop images here to upload or','vh');?></span> <a href="javascript:void(0)" class="addlisting-upload-button"><?php _e("Select files", "vh"); ?></a>
								<input id="<?php echo esc_attr($id); ?>plupload-browse-button" type="button" value="<?php esc_attr_e('Select Files','vh'); ?>" class="geodir_button"/>
								<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo esc_attr(wp_create_nonce($id.'pluploadan')); ?>"></span>
								<?php if ($width && $height): ?>
									<span class="plupload-resize"></span>
									<span class="plupload-width" id="plupload-width<?php echo esc_attr($width); ?>"></span>
									<span class="plupload-height" id="plupload-height<?php echo esc_attr($height); ?>"></span>
								<?php endif; ?>
								<div class="filelist"></div>
							</div>
						
							<div class="plupload-thumbs <?php if ($multiple): ?>plupload-thumbs-multiple<?php endif; ?> clearfix" id="<?php echo esc_attr($id); ?>plupload-thumbs" style="border-top:1px solid #ccc; padding-top:10px;">
							</div>
							<span id="upload-msg" ><?php _e('Please drag &amp; drop the images to rearrange the order','vh');?></span>
							<span id="<?php echo esc_attr($id); ?>upload-error" style="display:none"></span>
							<span class="geodir_message_error"><?php _e($required_msg,'geodirectory');?></span>
						</div>
											
						<?php } ?>
									
									<h3><?php _e("Select package & Add", "vh"); ?></h3>
									<?php do_action('geodir_after_main_form_fields2');?>
									
									
									<!-- add captcha code -->
									
							<script>
								document.write('<inp'+'ut type="hidden" id="geodir_sp'+'amblocker_top_form" name="geodir_sp'+'amblocker" value="64"/>')
							</script>
							<noscript>
							<div>
							<label><?php _e('Type 64 into this box','vh');?></label>
							<input type="text" id="geodir_spamblocker_top_form" name="geodir_spamblocker" value="" maxlength="10" />
							</div>
							</noscript><input type="text" id="geodir_filled_by_spam_bot_top_form" name="geodir_filled_by_spam_bot" value=""  />
		
		
									<!-- end captcha code -->
									
							<a href="javascript:void(0);" id="submitplace" class="geodir_button wpb_button wpb_btn-warning wpb_regularsize" <?php echo $submit_button;?>><?php _e('Review & Add', 'vh'); ?></a>
							
							<span class="geodir_message_note icon-info submit-note" style="padding-left:0px;"> <?php _e('Note: You will be able to see a preview in the next page','vh');?></span>
							<div class="clearfix"></div>
						</div>
						<div class="clearfix"></div>
						</div>
					</form>
				</div>
				<?php
				if (LAYOUT == 'sidebar-right') {
				?>
				<div class="vc_col-sm-3 pull-right <?php echo LAYOUT; ?>">
					<div class="sidebar-inner">
					<?php
						global $vh_is_in_sidebar;
						$vh_is_in_sidebar = true;
						generated_dynamic_sidebar();
					?>
					<div class="clearfix"></div>
					</div>
				</div><!--end of span3-->
				<?php } ?>
<?php	
wp_reset_query();
}

/**
 * Set post Map Marker Info Html
 **/
if (!function_exists('vh_geodir_get_infowindow_html')) {
	function vh_geodir_get_infowindow_html($postinfo_obj, $post_preview = '') {
		global $preview;
		$srcharr = array("'","/","-",'"','\\');
		$replarr = array("&prime;","&frasl;","&ndash;","&ldquo;",'');
		
		if (isset($_SESSION['listing']) && isset($post_preview) && $post_preview != '') {	
			$ID = '';
			$plink = '';
			
			if (isset($postinfo_obj->pid)) {
				$ID = $postinfo_obj->pid;
				$plink = get_permalink($ID);
			}
		
			$title = str_replace($srcharr,$replarr,($postinfo_obj->post_title));
			$lat = $postinfo_obj->post_latitude;
			$lng = $postinfo_obj->post_longitude;
			$address = str_replace($srcharr,$replarr,($postinfo_obj->post_address));
			$contact = str_replace($srcharr,$replarr,($postinfo_obj->geodir_contact));
			$timing = str_replace($srcharr,$replarr,($postinfo_obj->geodir_timing));
		} else {
			$ID = $postinfo_obj->post_id;
			$title = str_replace($srcharr,$replarr,htmlentities($postinfo_obj->post_title, ENT_COMPAT, 'UTF-8')); // fix by Stiofan
			$plink = get_permalink($ID);
			$lat = htmlentities(geodir_get_post_meta($ID,'post_latitude',true));
			$lng = htmlentities(geodir_get_post_meta($ID,'post_longitude',true));
			$address = str_replace($srcharr,$replarr,htmlentities(geodir_get_post_meta($ID,'post_address',true), ENT_COMPAT, 'UTF-8')); // fix by Stiofan
			$contact = str_replace($srcharr,$replarr,htmlentities(geodir_get_post_meta($ID,'geodir_contact',true), ENT_COMPAT, 'UTF-8'));
			$timing = str_replace($srcharr,$replarr,(geodir_get_post_meta($ID,'geodir_timing',true)));
		}
	
		// filter field as per price package
		global $geodir_addon_list;
		if (isset($geodir_addon_list['geodir_payment_manager']) && $geodir_addon_list['geodir_payment_manager']=='yes') {
			$post_type = get_post_type($ID);
			$package_id = isset($postinfo_obj->package_id) && $postinfo_obj->package_id ? $postinfo_obj->package_id : NULL;		
			$field_name = 'geodir_contact';
			if (!check_field_visibility($package_id, $field_name, $post_type)) {
				$contact = '';
			}
			
			$field_name = 'geodir_timing';
			if (!check_field_visibility($package_id, $field_name, $post_type)) {
				$timing = '';
			}
		}
	
		if ($lat && $lng) {
			ob_start(); ?>
			<div class="bubble">
				<div style="position: relative;">
				<?php
			$comment_count = '';
			$rating_star = '';
			if ($ID != '') {
				$rating_star = '';
				$comment_count = geodir_get_review_count_total($ID);
				$post_ratings = geodir_get_review_total($ID);
				
				if (!$preview) {
					$post_avgratings = geodir_get_commentoverall_number($ID);
					
					$rating_star = geodir_get_rating_stars($post_avgratings,$ID,false);
					$rating_star = apply_filters('geodir_review_rating_stars_on_infowindow', $rating_star, $post_avgratings, $ID);
				}
			}
			?>	
			<div class="geodir-bubble_desc <?php echo get_post_type($ID); ?>">
				
			<?php
			if (isset($_SESSION['listing']) && isset($post_preview) && $post_preview != '') {
				$post_images = array();
				if (!empty($postinfo_obj->post_images)) {
					$post_images = explode(",",$postinfo_obj->post_images);
				}
				
				if (!empty($post_images)) {
				?>
				<div class="geodir-bubble_image">
					<a href="<?php if($plink!= ''){ echo $plink;}else{ echo 'javascript:void(0);';}?>"><img style="max-height:50px;" src="<?php echo esc_attr($post_images[0]);?>" /></a>
				</div> 
				<?php
				}
			} else {
				if ($image = geodir_show_featured_image($ID,'widget-thumb',true,false,$postinfo_obj->featured_image)) {
				?>
				<div class="geodir-bubble_image" >

					<?php
						$post_images = geodir_get_images( $ID, 'gallery-large', get_option( 'geodir_listing_no_img' ) );
						$fimage = wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'gallery-large' );
						$navigation_arrows = true;

						if ( get_option("vh_theme_version") != "SkyDirectory" ) {
							echo "<div class=\"map-listing-carousel-container\">
									<ul class=\"map-listing-carousel\">";
							if ( !empty($post_images) ) {
								foreach ($post_images as $image) {
									// $resized_img = vt_resize('', $image->src, 343, 296, true);
									echo "
									<li class=\"map-listing-item\">
										<img src=\"".vh_imgresize($image->src, '343', '296', true)."\" />
									</li>";
								}
							} elseif ( !empty($fimage['0']) ) {
								echo "
									<li class=\"map-listing-item\">
										<img src=\"".vh_imgresize($fimage['0'], '343', '296', true)."\" />
									</li>";
								$navigation_arrows = false;
							}
							echo "</ul></div>";
						} else {
							if ( !empty($post_images) ) {
								$new_post_images = (array)$post_images;
								echo "
								<img src=\"".vh_imgresize($new_post_images['0']->src, '120', '105', true)."\" />";
							} elseif ( !empty($fimage['0']) ) {
								echo "<img src=\"".vh_imgresize($fimage['0'], '120', '105', true)."\" />";
								$navigation_arrows = false;
							}
						}
					?>
					
				</div>

				<?php 
				}
			}
			?>
			<?php
		// 		if (isset($postinfo_obj->recurring_dates)) {
		// 			$recuring_data = unserialize($postinfo_obj->recurring_dates);
		// 			$output = '';
		// 			$output .= '<div class="geodir_event_schedule">';	
					
		// 			$event_recurring_dates = explode(',', $recuring_data['event_recurring_dates']);
					
		// 			$starttimes = isset($recuring_data['starttime']) ? $recuring_data['starttime'] : '';
		// 			$endtimes = isset($recuring_data['endtime']) ? $recuring_data['endtime'] : '';
		// 			$e=0;		
		// 			foreach ($event_recurring_dates as $key => $date) {
						
		// 				if(strtotime($date) < strtotime(date("Y-m-d"))){continue;} // if the event is old don't show it on the map
		// 				$e++;
		// 				if($e==2){break;}// only show 3 event dates
		// 				$output .=  '<p>';
		// 				//$geodir_num_dates++;
		// 				if(isset($recuring_data['different_times']) && $recuring_data['different_times'] == '1'){
		// 					$starttimes = isset($recuring_data['starttimes'][$key]) ? $recuring_data['starttimes'][$key] : '';
		// 					$endtimes = isset($recuring_data['endtimes'][$key]) ? $recuring_data['endtimes'][$key] : '';
		// 				}	
						
		// 				$sdate = strtotime($date.' '.$starttimes);
		// 				$edate = strtotime($date.' '.$endtimes);
						
		// 				if($starttimes > $endtimes){
		// 					$edate = strtotime($date.' '.$endtimes . " +1 day");
		// 				}
						
						

		// 				global $geodir_date_time_format; 	
						
		// 				$output .=  '<i class="fa fa-caret-right"></i>'.date($geodir_date_time_format, $sdate);
		// 					//$output .=  __(' To', GEODIREVENTS_TEXTDOMAIN).' ';
		// 					$output .= '<br />';
		// 				$output .=  '<i class="fa fa-caret-left"></i>'.date($geodir_date_time_format, $edate);//.'<br />';
		// 				$output .=  '</p>';	
		// 			}
					
		// 			$output .= '</div>';
					
		// 			echo $output;
		// }
		
		
					if($ID){
		
						$post_author = isset($postinfo_obj->post_author) ? $postinfo_obj->post_author : get_post_field( 'post_author', $ID );
						
						?>
				<?php if ( get_option("vh_theme_version") != "SkyDirectory" && get_post_type($ID) != 'gd_event' ) { ?>
				<?php 
				#################Jino edit price inside map################## 
					$values =  dynamic_convert(get_post_meta($ID, 'vh_resource_id', true), $_COOKIE['C_CURRENCY'],vh_get_listing_price( $ID, get_post_type( $ID ) ));
					//get_option('vh_currency_symbol').vh_get_listing_price( $ID, get_post_type( $ID ) );
				?>
					<div class="map-listing-price"><?php echo $values['sign'].''.$values['money']; ?></div>
				<?php }

				$is_featured = geodir_get_post_meta($ID,'is_featured',true);
				$post_tags = geodir_get_post_meta($ID,'post_tags',true);
				if ( $is_featured == "1" || strpos(strtolower($post_tags), 'featured') !== false ) { ?>
					<div class="map-listing-featured"><?php _e('Featured', 'vh'); ?></div>
				<?php }
				if ( get_post_type($ID) == 'gd_event' ) { ?>
					<div class="map-listing-featured"><?php echo get_geodir_event_date( $ID ); ?></div>
				<?php }
				if ( get_option("vh_theme_version") == "SkyDirectory" ) { ?>
					<div class="geodir-listing-side map">
				<?php } ?>
				<div class="map-listing-rating"><?php echo vh_get_listing_rating( $ID, get_post_type( $ID ) ); ?></div>
				<div class="map-listing-favorite"><?php echo geodir_favourite_html(get_current_user_id(), $ID ); ?></div>
				<div class="map-listing-title">
					<?php
					if ( get_option("vh_theme_version") != "SkyDirectory" ) {
						echo $title;
					} else {
						echo '<a href="' . get_permalink( $ID ) . '">' . $title . '</a>';
					}
					?>
				</div>
				<?php if ( get_option("vh_theme_version") != "SkyDirectory" ) { ?>
					<a href="<?php echo esc_url($plink); ?>" class="wpb_button wpb_btn-primary wpb_btn-small"><?php _e("View", "vh"); ?></a>
					<?php if ( $navigation_arrows ) { ?>
						<a href="javascript:void(0)" class="map-listing-next icon-angle-right"></a>
						<a href="javascript:void(0)" class="map-listing-prev icon-angle-left"></a>
					<?php }
				} else { ?>
					<div class="geodir-listing-side-lower">
						<span class="listing-location icon-location"><?php echo $postinfo_obj->post_city . ', ' . $postinfo_obj->post_country; ?></span>
						<?php if ( $postinfo_obj->geodir_contact != null ) { ?>
							<span class="listing-contact icon-phone-1"><?php echo $postinfo_obj->geodir_contact; ?></span>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
				<div class="geodir-bubble-meta-bottom">
				</div>
 
	   <?php }?> 
				   
			</div>             					
		</div>
	</div>
	<?php 
	$html = ob_get_clean();
	$html = apply_filters('geodir_custom_infowindow_html' ,$html ,$postinfo_obj, $post_preview   ) ;
	return $html;
	}
}
}

function vh_get_custom_post_types() {
	global $wpdb;

	$post_types = geodir_get_posttypes('array');
	$default_post_type = get_option('geodir_default_map_search_pt', 'gd_place');
	$exclude = get_option('geodir_exclude_post_type_on_map', '');
	$type_links = '';

	if ( $exclude != '' ) {
		foreach ($exclude as $ex_key => $ex_value) {
			unset($post_types[$ex_value]);
		}
	}

	if ( !empty($post_types) ) {
		foreach ($post_types as $post_key => $post_value) {
			$type_name = str_replace('gd_', '', $post_key);
			$active = '';
			if ( $post_key == $default_post_type ) {
				$active = ' class="active"';
			}
			$type_links .= "<a href=\"javascript:void(0)\"".$active.">".$type_name."</a>";
		}
	}

	echo $type_links;
}


add_action('wp_ajax_geodir_custom_posts', "vh_get_header_form");
add_action( 'wp_ajax_nopriv_geodir_custom_posts', 'vh_get_header_form' );
function vh_get_header_form() {
	global $wpdb;
	$post_value = esc_attr($_POST['post_type']);
	$post_type = 'gd_'.$post_value;
	$post_taxonomy = 'gd_'.$post_value.'category';

	$form_output = '';

	$form_output = '
	<div class="header-form-container">';

		if ( $post_type != 'gd_event' ) {
			$listing_categories = get_terms($post_taxonomy);
			if ( !is_wp_error($listing_categories) ) {
				$listing_category = $listing_categories['0']->name;
			} else {
				$listing_category = '';
			}
		}

		if ( function_exists('geodir_advance_search_filter') ) {
			$advanced_fields = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'geodir_custom_advance_search_fields WHERE post_type="'.$post_type.'" ORDER BY sort_order');
		}

		if ( function_exists('geodir_advance_search_filter') && !empty($advanced_fields) ) {
			$field_count = count($advanced_fields);
			$field_width = 678/$field_count;

			foreach ($advanced_fields as $advanced_value) {
				switch ( $advanced_value->field_site_type ) {
					case 'text':
						$form_output .= '
						<div class="header-input-container advanced" style="width: '.$field_width.'px;">
							<span class="header-input-title">'.$advanced_value->front_search_title.'</span>
							<input type="'.$advanced_value->field_site_type.'" name="'.$advanced_value->site_htmlvar_name.'">
							<div class="clearfix"></div>
						</div>';
						break;
					case 'datepicker':
						$form_output .= '
						<div class="header-input-container advanced" style="width: '.$field_width.'px;">
							<span class="header-input-title">'.$advanced_value->front_search_title.'</span>
							<input type="text" name="'.$advanced_value->site_htmlvar_name.'" id="header-when">
							<input id="startrange" name="listing_date" type="hidden" value="'.esc_attr($_COOKIE['vh_startrange']).'">
							<input id="endrange" type="hidden" value="'.esc_attr($_COOKIE['vh_endrange']).'">';
							ob_start();
							do_action('vh_action_get_listing_when_options');
							$form_output .= ob_get_contents();
							ob_end_clean();
							$form_output .= '
							<div class="clearfix"></div>
						</div>';
						break;
					case 'textarea':
						$form_output .= '
						<div class="header-input-container advanced" style="width: '.$field_width.'px;">
							<span class="header-input-title">'.$advanced_value->front_search_title.'</span>
							<textarea name="'.$advanced_value->site_htmlvar_name.'"></textarea>
							<div class="clearfix"></div>
						</div>';
						break;
					case 'time':
						$form_output .= '
						<div class="header-input-container advanced" style="width: '.$field_width.'px;">
							<span class="header-input-title">'.$advanced_value->front_search_title.'</span>
							<input type="'.$advanced_value->field_site_type.'" name="'.$advanced_value->site_htmlvar_name.'">
							<div class="clearfix"></div>
						</div>';
						break;
					case 'checkbox':
						$form_output .= '
						<div class="header-input-container advanced" style="width: '.$field_width.'px;">
							<span class="header-input-title">'.$advanced_value->front_search_title.'</span>
							<span class="checkbox_box"></span>
							<input type="'.$advanced_value->field_site_type.'" name="'.$advanced_value->site_htmlvar_name.'" class="hidden">
							<div class="clearfix"></div>
						</div>';
						break;
					case 'phone':
						$form_output .= '
						<div class="header-input-container advanced" style="width: '.$field_width.'px;">
							<span class="header-input-title">'.$advanced_value->front_search_title.'</span>
							<input type="tel" name="'.$advanced_value->site_htmlvar_name.'">
							<div class="clearfix"></div>
						</div>';
						break;
					case 'radio':
						$options = $wpdb->get_results('SELECT option_values FROM '.$wpdb->prefix.'geodir_custom_fields WHERE htmlvar_name="'.$advanced_value->site_htmlvar_name.'"');
						$option_values = explode(',', $options['0']->option_values);

						$form_output .= '
						<div class="header-input-container advanced" style="width: '.$field_width.'px;">
							<span class="header-input-title">'.$advanced_value->front_search_title.'</span>';

							foreach ($option_values as $option_value) {
								$form_output .= '<div class="geodir-radio">
													<span class="radiobutton"></span>
													<span class="input-side-text">'.$option_value.'</span>
													<input type="'.$advanced_value->field_site_type.'" name="'.$advanced_value->site_htmlvar_name.'" value="'.$option_value.'" class="hidden">
												</div>';
							}
			
							$form_output .= '
							<div class="clearfix"></div>
						</div>';
						break;
					case 'email':
						$form_output .= '
						<div class="header-input-container advanced" style="width: '.$field_width.'px;">
							<span class="header-input-title">'.$advanced_value->front_search_title.'</span>
							<input type="'.$advanced_value->field_site_type.'" name="'.$advanced_value->site_htmlvar_name.'">
							<div class="clearfix"></div>
						</div>';
						break;
					case 'select':
						$options = $wpdb->get_results('SELECT option_values FROM '.$wpdb->prefix.'geodir_custom_fields WHERE htmlvar_name="'.$advanced_value->site_htmlvar_name.'"');
						$option_values = explode(',', $options['0']->option_values);

						$form_output .= '
						<div class="header-input-container advanced" style="width: '.$field_width.'px;">
							<div class="geodir-chosen-container geodir-chosen-container-single">
								<select name="'.$advanced_value->site_htmlvar_name.'" class="chosen_select" style="display: none;">';

								foreach ($option_values as $option_value) {
									$form_output .= '<option value="'.$option_value.'">'.$option_value.'</option>';
								}

								$form_output .= '
								</select>
							</div>
						</div>';
						break;
					case 'multiselect':
						$options = $wpdb->get_results('SELECT option_values FROM '.$wpdb->prefix.'geodir_custom_fields WHERE htmlvar_name="'.$advanced_value->site_htmlvar_name.'"');
						$option_values = explode(',', $options['0']->option_values);

						$form_output .= '
						<div class="header-input-container advanced" style="width: '.$field_width.'px;">
							<div class="geodir-chosen-container geodir-chosen-container-multi">
								<select name="'.$advanced_value->site_htmlvar_name.'" multiple="multiple" class="chosen_select" style="display: none;">';

								foreach ($option_values as $option_value) {
									$form_output .= '<option value="'.$option_value.'">'.$option_value.'</option>';
								}

								$form_output .= '
								</select>
							</div>
						</div>';
						break;
					case 'taxonomy':
						$option_values = get_categories(array('taxonomy' => $advanced_value->site_htmlvar_name));

						$form_output .= '
						<div class="header-input-container advanced" style="width: '.$field_width.'px;">
							<div class="geodir-chosen-container geodir-chosen-container-single">
								<select name="'.$advanced_value->site_htmlvar_name.'" class="chosen_select" style="display: none;">';

								foreach ($option_values as $option_value) {
									$form_output .= '<option value="'.$option_value->name.'">'.$option_value->name.'</option>';
								}

								$form_output .= '
								</select>
							</div>
						</div>';
						break;
					default:
						$form_output .= '
						<div class="header-input-container advanced" style="width: '.$field_width.'px;">
							<span class="header-input-title">'.$advanced_value->front_search_title.'</span>
							<input type="'.$advanced_value->field_site_type.'" name="'.$advanced_value->site_htmlvar_name.'">
							<div class="clearfix"></div>
						</div>';
						break;
				}
			}
		} elseif ( $post_type == 'gd_event' ) {
			$form_output .= '
			<div class="header-input-container event" style="width: 339px;"><span class="header-input-title">'.__('Keyword:', 'vh').'</span><input type="text" id="header-keyword" name="s" style="width: 100%;"><div class="clearfix"></div></div>
			<div class="header-input-container event" style="width: 339px;"><span class="header-input-title">'.__('Location:', 'vh').'</span><input type="text" id="header-location" style="width: 100%;"><div class="clearfix"></div></div>';
		} elseif ( get_option("vh_theme_version") == "SkyVacation" ) {
			if ( isset($_COOKIE['vh_startrange']) ) {
				$start_range = esc_attr($_COOKIE['vh_startrange']);
			} else {
				$start_range = '';
			}

			if ( isset($_COOKIE['vh_startrange']) ) {
				$end_range = esc_attr($_COOKIE['vh_endrange']);
			} else {
				$end_range = '';
			}

			$form_output .= '
			<div class="header-input-container"><span class="header-input-title">'.__('Location:', 'vh').'</span><input type="text" id="header-location"><div class="clearfix"></div></div>
			<div class="header-input-container"><span class="header-input-title">'.__('When:', 'vh').'</span><input type="text" id="header-when"><input id="startrange" name="listing_date" type="hidden" value="'.$start_range.'"><input id="endrange" type="hidden" value="'.$end_range.'">';
			ob_start();
			do_action('vh_action_get_listing_when_options');
			$form_output .= ob_get_contents();
			ob_end_clean();
			$form_output .= '
			<div class="clearfix"></div></div>
			<div class="header-input-container"><span class="header-input-title">'.__('People:', 'vh').'</span><input type="text" id="header-people" readonly>';
			ob_start();
			vh_get_listing_people_options();
			$form_output .= ob_get_contents();
			ob_end_clean();
			$form_output .= '
			<div class="clearfix"></div></div>';
		} elseif ( get_option("vh_theme_version") == "SkyDirectory" ) {
			$form_output .= '
			<div class="header-input-container"><span class="header-input-title">'.__('Keyword:', 'vh').'</span><input type="text" id="header-keyword" name="s"><div class="clearfix"></div></div>
			<div class="header-input-container"><span class="header-input-title">'.__('Category:', 'vh').'</span><input type="text" id="header-category" value="'.$listing_category.'">';
			ob_start();
			vh_get_search_category( $post_taxonomy );
			$form_output .= ob_get_contents();
			ob_end_clean();
			$form_output .= '
			<div class="clearfix"></div></div>
			<div class="header-input-container"><span class="header-input-title">'.__('Location:', 'vh').'</span><input type="text" id="header-location"><div class="clearfix"></div></div>';
		} elseif ( get_option("vh_theme_version") == "SkyEstate" ) {
			$form_output .= '
			<div class="header-input-container"><span class="header-input-title">'.__('Location:', 'vh').'</span><input type="text" id="header-location"><div class="clearfix"></div></div>
			<div class="header-input-container"><span class="header-input-title">'.__('Type:', 'vh').'</span><input type="text" id="header-type" value="'.$listing_category.'">';
			ob_start();
			vh_get_search_category( $post_taxonomy );
			$form_output .= ob_get_contents();
			ob_end_clean();
			$form_output .= '
			<div class="clearfix"></div></div>
			<div class="header-input-container"><span class="header-input-title">'.__('Contract:', 'vh').'</span><input type="text" id="header-contract" value="'.__('For Sale', 'vh').'" readonly>';
			ob_start();
			vh_get_search_contract();
			$form_output .= ob_get_contents();
			ob_end_clean();
			$form_output .= '
			<div class="clearfix"></div></div>';
		}

		$form_output .= '
		<a href="javascript:void(0)" class="wpb_button wpb_btn-warning icon-search" id="header-submit"> </a>';

		
		if ( get_option("vh_theme_version") == "SkyEstate" ) {
			
			$form_output .= '
			<div class="clearfix"></div>
			<div id="header-more-options">
				<div class="header-more-left">
					<input id="header-more-max-price" type="hidden" value="'.vh_get_geodir_max_price().'">
					<div class="filter-field filter-price">
						<div class="filter-left">
							<span class="filter-text">'.__("Rent Price", "vh").'</span>
							<span class="filter-second-text">'.__("Per month", "vh").'</span>
						</div>
						<div class="filter-right">
							<span class="range-slider-min">'.get_option('vh_currency_symbol').'0</span>
							<span class="range-slider-max">'.get_option('vh_currency_symbol').vh_get_geodir_max_price().'</span>
							<div id="header-range-price"></div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="filter-field filter-area">
						<div class="filter-left">
							<span class="filter-text">'.__("Area", "vh").'</span>
							<span class="filter-second-text">'.__("Square meters", "vh").'</span>
						</div>
						<div class="filter-right">
							<span class="range-slider-min">1</span>
							<span class="range-slider-max">500+</span>
							<div id="header-range-area"></div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="filter-field filter-rooms">
						<div class="filter-left">
							<span class="filter-text">'.__("Rooms", "vh").'</span>
						</div>
						<div class="filter-right">
							<span class="range-slider-min">1</span>
							<span class="range-slider-max">10+</span>
							<div id="header-range-rooms"></div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="filter-field filter-bathrooms">
						<div class="filter-left">
							<span class="filter-text">'.__("Bathrooms", "vh").'</span>
						</div>
						<div class="filter-right">
							<span class="range-slider-min">1</span>
							<span class="range-slider-max">3+</span>
							<div id="header-range-bathrooms"></div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="filter-field filter-bedrooms">
						<div class="filter-left">
							<span class="filter-text">'.__("Bedrooms", "vh").'</span>
						</div>
						<div class="filter-right">
							<span class="range-slider-min">1</span>
							<span class="range-slider-max">5+</span>
							<div id="header-range-bedrooms"></div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
				<div class="header-more-right">';

					if ( function_exists('geodir_post_custom_fields') ) {
						$custom_fields = geodir_post_custom_fields('','custom',$post_type);
					} else {
						$custom_fields = array();
					}
					
					foreach ($custom_fields as $extra_fields) {
						if ( $extra_fields["type"] == "checkbox" && $extra_fields["cat_sort"] == "1" ) {
							$form_output .= '
							<div class="checkbox">
								<span class="checkbox_box"></span>
								<span class="checkbox_text">'.$extra_fields["site_title"].'</span>
								<input class="main_list_selecter" type="checkbox" field_type="checkbox" name="geodir_accept_term_condition" id="geodir_accept_term_condition" value="1" style="display: none">
							</div>';
						}
					}
					$form_output .= '
					<div class="clearfix"></div>
				</div>
				<div class="clearfix"></div>
			</div>
			<span class="header-more-button icon-angle-down">'.__('More options', 'vh').'</span>
			<div class="clearfix"></div>';
		}

		$form_output .= '
		<input type="hidden" id="header-post-type" value="'.$post_type.'">
		<span id="form-loading-effect"></span>
	</div>';

	echo $form_output;
	die();
}

function vh_geodir_get_custom_fields_html($package_id = '', $default = 'custom',$post_type = 'gd_place'){

	global $is_default, $mapzoom;
	
	$show_editors = array();
	$listing_type = $post_type;
	
	$custom_fields = geodir_post_custom_fields($package_id,$default,$post_type); 

	foreach($custom_fields as $key=>$val)
	{
		$name = $val['name'];
		$site_title = $val['site_title'];
		$type = $val['type'];
		$admin_desc = $val['desc'];
		$option_values = $val['option_values'];
		$is_required = $val['is_required'];
		$is_default =  $val['is_default'];
		$is_admin =  $val['is_admin'];
		$required_msg = $val['required_msg'];
		$css_class = $val['css_class'];
		$extra_fields = unserialize($val['extra_fields']);
		$value='';
		
		/* field available to site admin only for edit */
		$for_admin_use = isset( $val['for_admin_use'] ) && (int)$val['for_admin_use'] == 1 ? true : false;
		if ( $for_admin_use && !is_super_admin() ) {
			continue;
		}
		
		if(is_admin()){ 
			
			global $post; 
			
			if(isset($_REQUEST['post']))
				$_REQUEST['pid'] = $_REQUEST['post'];
		}
		
		if( isset($_REQUEST['backandedit']) && $_REQUEST['backandedit'] && isset($_SESSION['listing']) ){ 
			$post = unserialize($_SESSION['listing']);
			$value = isset($post[$name]) ? $post[$name] : '';
		}elseif(isset($_REQUEST['pid']) && $_REQUEST['pid'] != ''){
			$value = geodir_get_post_meta($_REQUEST['pid'], $name,true);
		}else{
			if($value == '' ){ $value= $val['default']; }
		}
		
		do_action('geodir_before_custom_form_field_'.$name, $listing_type, $package_id, $val);
			 
		if($type=='fieldset'){	
		
			?><h3><?php echo $site_title;?>
				<?php if($admin_desc != '')echo '<small>( '.__($admin_desc,'geodirectory').' )</small>';?>
					
			</h3><?php
		
		}elseif($type=='address'){	
				
				$prefix = $name.'_'; 
				
				($site_title != '') ? $address_title = $site_title : $address_title = ucwords($prefix . ' address');
				($extra_fields['zip_lable'] != '') ? $zip_title = $extra_fields['zip_lable'] : $zip_title = ucwords($prefix.' zip/post code ');
				($extra_fields['map_lable'] != '') ? $map_title = $extra_fields['map_lable'] : $map_title = ucwords('set address on map');
				($extra_fields['mapview_lable'] != '') ? $mapview_title = $extra_fields['mapview_lable'] : $mapview_title = ucwords($prefix.' mapview');
				
				$address = '';
				$zip = '';
				$mapview = '';
				$mapzoom = '';
				$lat = '';
				$lng = '';
				
				if(isset($_REQUEST['backandedit']) &&  $_REQUEST['backandedit'] && isset($_SESSION['listing']) ){ 
					
					$post = unserialize($_SESSION['listing']);
					$address = $post[$prefix.'address'];
					$zip = isset($post[$prefix.'zip']) ? $post[$prefix.'zip'] : '';
					$lat = isset($post[$prefix.'latitude']) ? $post[$prefix.'latitude'] : '';
					$lng = isset($post[$prefix.'longitude']) ? $post[$prefix.'longitude'] : '';
					$mapview = isset($post[$prefix.'mapview']) ? $post[$prefix.'mapview'] : '';
					$mapzoom = isset($post[$prefix.'mapzoom']) ? $post[$prefix.'mapzoom'] : '';
					
				}elseif( isset($_REQUEST['pid']) && $_REQUEST['pid']!='' && $post_info = geodir_get_post_info($_REQUEST['pid']) ){ 
					
					$post_info = (array)$post_info;
					
					$address = $post_info[$prefix.'address'];
					$zip = isset($post_info[$prefix.'zip']) ? $post_info[$prefix.'zip'] : '';
					$lat = isset($post_info[$prefix.'latitude']) ? $post_info[$prefix.'latitude'] : '';
					$lng = isset($post_info[$prefix.'longitude']) ? $post_info[$prefix.'longitude'] : '';
					$mapview = isset($post_info[$prefix.'mapview']) ? $post_info[$prefix.'mapview'] : '';
					$mapzoom = isset($post_info[$prefix.'mapzoom']) ? $post_info[$prefix.'mapzoom'] : '';
					
				}
				
				$location = geodir_get_default_location();
				if(empty($city)) $city = isset($location->city) ? $location->city : '';
				if(empty($region)) $region = isset($location->region) ? $location->region : '';
				if(empty($country)) $country = isset($location->country) ? $location->country : '';
				
				$lat_lng_blank = false;
				if(empty($lat) && empty($lng)){$lat_lng_blank = true;}
				
				if(empty($lat)) $lat = isset($location->city_latitude) ? $location->city_latitude : '';
				if(empty($lng)) $lng = isset($location->city_longitude) ? $location->city_longitude : '';
				
				
				
				
				$lat = apply_filters('geodir_default_latitude', $lat, $is_admin);
				$lng = apply_filters('geodir_default_longitude', $lng, $is_admin);
				
				?>

				<h3><?php _e("Property location", "vh"); ?></h3>
			
				<div id="geodir_<?php echo esc_attr($prefix).'address';?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix">
					<?php if($is_required) echo '<span class="input-required"></span>';?>
					<input type="text" field_type="<?php echo esc_attr($type);?>" name="<?php echo esc_attr($prefix).'address';?>" id="<?php echo esc_attr($prefix).'address';?>" class="geodir_textfield" placeholder="<?php echo _e($address_title,'geodirectory'); ?>" value="<?php echo esc_attr(stripslashes($address)); ?>"  />
					<span class="geodir_message_note"><?php echo _e($admin_desc,'geodirectory'); ?></span>
					<?php if($is_required) {?> 
						<span class="geodir_message_error"><?php _e($required_msg,'geodirectory');?></span> 
					<?php } ?>
				</div>
				
							
				<?php
			
				do_action('geodir_address_extra_listing_fields', $val);
						  
				if(isset($extra_fields['show_zip']) && $extra_fields['show_zip']) {?> 
				  
				<div id="geodir_<?php echo esc_attr($prefix).'zip';?>_row" class="<?php /*if($is_required) echo 'required_field';*/?> <?php echo $css_class;?> geodir_form_row clearfix">
					<input type="text" field_type="<?php echo $type;?>" name="<?php echo esc_attr($prefix).'zip';?>" id="<?php echo esc_attr($prefix).'zip';?>" class="geodir_textfield autofill" placeholder="<?php echo _e($zip_title,'geodirectory'); ?>" value="<?php echo esc_attr(stripslashes($zip)); ?>"  />
					<?php /*if($is_required) {?>
					<span class="geodir_message_error"><?php echo _e($required_msg,'vh');?></span> 
					<?php }*/ ?>
				</div>
				<?php } ?>

				<?php 
				/* show lat lng */
				$style_latlng = ((isset($extra_fields['show_latlng']) && $extra_fields['show_latlng']) || is_admin()) ? '' : 'style="display:none"';?>
				<div id="geodir_<?php echo esc_attr($prefix).'latitude';?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix" <?php echo $style_latlng;?>>
					<?php if($is_required) echo '<span class="input-required"></span>';?>
					<input type="text" field_type="<?php echo $type;?>" name="<?php echo esc_attr($prefix).'latitude';?>" id="<?php echo esc_attr($prefix).'latitude';?>" class="geodir_textfield" placeholder="<?php echo esc_attr(PLACE_ADDRESS_LAT); ?>" value="<?php echo esc_attr(stripslashes($lat)); ?>" size="25"  />
					<span class="geodir_message_note"><?php echo _e(GET_LATITUDE_MSG,'geodirectory');?></span>
					<?php if($is_required) {?>
					<span class="geodir_message_error"><?php _e($required_msg,'geodirectory');?></span> 
					<?php } ?>
				 </div>
				 
				 <div id="geodir_<?php echo esc_attr($prefix).'longitude';?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix" <?php echo $style_latlng;?>>
					<?php if($is_required) echo '<span class="input-required"></span>';?>
					<input type="text" field_type="<?php echo $type;?>" name="<?php echo esc_attr($prefix).'longitude';?>" id="<?php echo esc_attr($prefix).'longitude';?>" class="geodir_textfield" placeholder="<?php echo esc_attr(PLACE_ADDRESS_LNG); ?>" value="<?php echo esc_attr(stripslashes($lng)); ?>" size="25"  />
					<span class="geodir_message_note"><?php echo _e(GET_LOGNGITUDE_MSG,'geodirectory');?></span>
					<?php if($is_required) {?>
					<span class="geodir_message_error"><?php _e($required_msg,'geodirectory');?></span> 
					<?php } ?>
				 </div>
				
				<?php if(isset($extra_fields['show_map']) && $extra_fields['show_map']) {?>  
			   
				<div id="geodir_<?php echo esc_attr($prefix).'map';?>_row" class="<?php echo $css_class;?> geodir_form_row clearfix"> 
					<?php include( geodir_plugin_path() . "/geodirectory-functions/map-functions/map_on_add_listing_page.php");
					if($lat_lng_blank){$lat='';$lng='';}
					?>
					<?php if(isset($extra_fields['show_mapview']) && $extra_fields['show_mapview']) {?>

						<a href="javascript:void(0)" class="map-view-default active"><?php _e("Default", "vh"); ?></a>
						<a href="javascript:void(0)" class="map-view-satellite"><?php _e("Satellite", "vh"); ?></a>
						<a href="javascript:void(0)" class="map-view-hybrid"><?php _e("Hybrid", "vh"); ?></a>

						<span class="geodir_user_define default"><input field_type="<?php echo esc_attr($type);?>" type="radio" class="gd-checkbox" name="<?php echo esc_attr($prefix).'mapview';?>" id="<?php echo esc_attr($prefix).'mapview';?>" <?php if($mapview == 'ROADMAP' || $mapview == '' ){echo 'checked="checked"';}?>  value="ROADMAP" size="25"  /> <?php _e('Default Map','vh');?></span>
						<span class="geodir_user_define satellite"> <input field_type="<?php echo esc_attr($type);?>" type="radio"  class="gd-checkbox" name="<?php echo esc_attr($prefix).'mapview';?>" id="map_view1" <?php if($mapview=='SATELLITE'){echo 'checked="checked"';}?> value="SATELLITE" size="25"  /> <?php _e('Satellite Map','vh');?></span>
						<span class="geodir_user_define hybrid"><input field_type="<?php echo esc_attr($type);?>" type="radio" class="gd-checkbox"  name="<?php echo esc_attr($prefix).'mapview';?>" id="map_view2" <?php if($mapview=='HYBRID'){echo 'checked="checked"';}?>  value="HYBRID" size="25"  /> <?php _e('Hybrid Map','vh');?></span>

					<?php }?>
					<span class="geodir_message_note"><?php echo _e(GET_MAP_MSG,'geodirectory');?></span>
				</div> 
		
				<?php } ?>
					
		 <?php if(isset($extra_fields['show_mapzoom']) && $extra_fields['show_mapzoom']) {?>  
				<input type="hidden" value="<?php if(isset($mapzoom)){ echo $mapzoom;}?>" name="<?php echo esc_attr($prefix).'mapzoom';?>" id="<?php echo esc_attr($prefix).'mapzoom';?>" />
				<?php }?>						
		<?php }
		elseif($type=='text'){?>
		 
		 <div id="<?php echo esc_attr($name);?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix">
				<?php $site_title = __($site_title,'geodirectory'); (trim($site_title)) ? $site_title : '&nbsp;'; ?>
				<?php if($is_required) echo '<span class="input-required"></span>';?>
				<!-- text jino edited Listing Price only -->
				<?php 
				if($name=='geodir_listing_price')
				  $site_title= add_listing_price_holder();
				
				?>
				
			<input  field_type="<?php echo esc_attr($type);?>" name="<?php echo esc_attr($name);?>"   id="<?php echo esc_attr($name);?>" value="<?php echo stripslashes($value);?>" placeholder="<?php echo _e($site_title,'geodirectory'); ?>" type="text" class="geodir_textfield" />
			<!-- Determin currency code and validate minimum price-->
			<input type="hidden" id="icl_c" class="icl_c" value="<?php echo $_COOKIE['C_CURRENCY'];?>" />
			<span class="geodir_message_note"><?php echo _e($admin_desc,'geodirectory');?></span>
			<?php if($is_required) {?>
			<span class="geodir_message_error"><?php echo _e($required_msg,'geodirectory'); ?></span> 
		
			<?php } ?>
		 </div>
		
		<?php }
		elseif($type=='email'){
			if($value== $val['default']){$value='';}?>
		 
			<div id="<?php echo esc_attr($name);?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix">

					<?php $site_title = __($site_title,'geodirctory'); (trim($site_title)) ? $site_title : '&nbsp;'; ?>
					<?php if($is_required) echo '<span class="input-required"></span>';?>
	
				<input field_type="<?php echo esc_attr($type);?>" name="<?php echo esc_attr($name);?>" id="<?php echo esc_attr($name);?>" value="<?php echo stripslashes($value);?>" placeholder="<?php echo _e($site_title,'geodirectory'); ?>" type="text" class="geodir_textfield" />
				<span class="geodir_message_note"><?php echo _e($admin_desc,'geodirectory');?></span>
			
				<?php if($is_required) {?>
				<span class="geodir_message_error"><?php echo _e($required_msg,'geodirectory');?></span> 
				<?php } ?>
			</div>
		
		<?php }
		elseif($type=='phone'){ 
				if($value== $val['default']){$value='';} ?>
			  
				<div id="<?php echo esc_attr($name);?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix">
	
						<?php $site_title = __($site_title,'vh'); (trim($site_title)) ? $site_title : '&nbsp;'; ?>
						<?php if($is_required) echo '<span class="input-required"></span>';?>
			
					<input field_type="<?php echo esc_attr($type);?>" name="<?php echo esc_attr($name);?>" id="<?php echo esc_attr($name);?>" value="<?php echo stripslashes($value);?>" placeholder="<?php echo esc_attr($site_title); ?>" type="text" class="geodir_textfield" />
					<span class="geodir_message_note"><?php echo _e($admin_desc,'geodirectory');?></span>
					<?php if($is_required) {?>
					<span class="geodir_message_error"><?php echo _e($required_msg,'geodirectory');?></span> 
					<?php } ?>
				</div> 
		
		<?php }
		elseif($type=='url'){ 
			if($value== $val['default']){$value='';}?>
		
			<div id="<?php echo esc_attr($name);?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix">
				
					<?php $site_title = __($site_title,'geodirectory'); (trim($site_title)) ? $site_title : '&nbsp;'; ?>
					<?php if($is_required) echo '<span class="input-required"></span>';?>
			
				<input field_type="<?php echo $type;?>" name="<?php echo $name;?>" id="<?php echo $name;?>" value="<?php echo stripslashes($value);?>" placeholder="<?php echo _e($site_title,'geodirectory'); ?>" type="text" class="geodir_textfield" />
				<span class="geodir_message_note"><?php echo _e($admin_desc,'geodirectory');?></span>
				<?php if($is_required) {?>
				<span class="geodir_message_error"><?php echo _e($required_msg,'vh');?></span> 
				<?php } ?>
			 </div>
	
		<?php }
		elseif($type=='radio'){ ?>     
			<div id="<?php echo esc_attr($name);?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix">
				
					<?php $site_title = __($site_title,'geodirectory'); (trim($site_title)) ? $site_title : '&nbsp;'; ?>
					<?php if($is_required) echo '<span class="input-required"></span>';?>
			
				
				<?php if($option_values){ $option_values_arr = explode(',',$option_values);
						
						for($i=0;$i<count($option_values_arr);$i++){ 
							if(strstr($option_values_arr[$i],"/")){
								$radio_attr = explode("/",$option_values_arr[$i]);
								$radio_lable = ucfirst($radio_attr[0]);
								$radio_value = $radio_attr[1];
							}else{
								$radio_lable = ucfirst($option_values_arr[$i]);
								$radio_value = $option_values_arr[$i];
							}
								
						?>
					
						<input name="<?php echo esc_attr($name);?>" id="<?php echo esc_attr($name);?>" <?php if($radio_value == $value){ echo 'checked="checked"';}?>  value="<?php echo esc_attr($radio_value); ?>" placeholder="<?php echo _e($site_title,'geodirectory'); ?>" class="gd-checkbox" field_type="<?php echo esc_attr($type);?>" type="radio"  /><?php _e($radio_lable); ?>
						
						<?php	
						} 
					}	
				?>
				<span class="geodir_message_note"><?php echo _e($admin_desc,'geodirectory');?></span>
				<?php if($is_required) {?>
				<span class="geodir_message_error"><?php echo _e($required_msg,'geodirectory');?></span> 
				<?php } ?>
			</div>
			
		<?php }
		elseif($type=='checkbox'){	?>
		
		<div id="<?php echo esc_attr($name);?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix">

				<?php $site_title = __($site_title,'geodirectory'); (trim($site_title)) ? $site_title : '&nbsp;'; ?>
				<?php if($is_required) echo '<span class="input-required"></span>';?>

					<?php	if($value != '1'){	$value = '0';}?>
					<input type="hidden" name="<?php echo esc_attr($name);?>" id="<?php echo esc_attr($name);?>" value="<?php echo esc_attr($value);?>" />
					<span class="checkbox_box"></span>
			<input  <?php if($value == '1'){ echo 'checked="checked"';}?>  value="1" style="display: none" class="gd-checkbox" field_type="<?php echo esc_attr($type);?>" type="checkbox" onchange="if(this.checked){jQuery('#<?php echo $name;?>').val('1');} else{ jQuery('#<?php echo $name;?>').val('0');}" /> 
			<span class="checkbox_text"><?php echo _e($site_title,'geodirectory'); ?></span>
			<span class="geodir_message_note"><?php echo _e($admin_desc,'geodirectory');?></span>
			<?php if($is_required) {?>
			<span class="geodir_message_error"><?php echo _e($required_msg,'geodirectory');?></span> 
			<?php } ?>
		</div>
		
		<?php }
		elseif($type=='textarea'){?>
		
		<div id="<?php echo esc_attr($name);?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix">
		
				<?php $site_title = __($site_title,'geodirectory'); (trim($site_title)) ? $site_title : '&nbsp;'; ?>
				<?php if($is_required) echo '<span class="input-required"></span>';?>
			<?php
			
			
			if(is_array($extra_fields) && in_array('1', $extra_fields)){
				
				$editor_settings = array('media_buttons'=>false, 'textarea_rows'=>10);?>
													
				<div class="editor" field_id="<?php echo esc_attr($name);?>" field_type="editor">
				<?php wp_editor( stripslashes($value), $name, $editor_settings ); ?>
				</div><?php
			
			}else{
				?><textarea field_type="<?php echo esc_attr($type);?>" class="geodir_textarea" name="<?php echo esc_attr($name);?>" placeholder="<?php echo _e($site_title,'geodirectory'); ?>" id="<?php echo esc_attr($name);?>"><?php echo stripslashes($value);?></textarea><?php
			
			}?>
			
			
			<span class="geodir_message_note"><?php echo _e($admin_desc,'geodirectory');?></span>
		
			<?php if($is_required) {?>
			<span class="geodir_message_error"><?php echo _e($required_msg,'geodirectory');?></span> 
		
			<?php } ?>
		</div>     
		
		<?php }
		elseif($type=='select'){	?>
		<div id="<?php echo esc_attr($name);?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row geodir_custom_fields clearfix">
		
				<?php $site_title = __($site_title,'geodirectory'); (trim($site_title)) ? $site_title : '&nbsp;'; ?>
				<?php if($is_required) echo '<span class="input-required"></span>';?>
		
			<select field_type="<?php echo esc_attr($type);?>" name="<?php echo esc_attr($name);?>" id="<?php echo esc_attr($name);?>" class="geodir_textfield textfield_x chosen_select" data-placeholder="<?php echo  _e('Choose','vh' ) .' '._e($site_title,'geodirectory') .'&hellip;' ;?>" option-ajaxchosen="false" >
	
			<?php if($option_values){   $option_values_arr = explode(',',$option_values);
			
					for($i=0;$i<count($option_values_arr);$i++)   {   
					
					if(strstr($option_values_arr[$i],"/")){
							$select_attr = explode("/",$option_values_arr[$i]);
							$select_lable = ucfirst($select_attr[0]);
							$select_value = $select_attr[1];
						}else{
							$select_lable = ucfirst($option_values_arr[$i]);
							$select_value = $option_values_arr[$i];
						}
					
					?>
							<option value="<?php echo _e($select_value,'geodirectory'); ?>" <?php if($value==$select_value){ echo 'selected="selected"';}?>><?php echo _e($select_lable,'geodirectory'); ?></option>
							
					<?php }
				}
			?>
			</select>
			<span class="geodir_message_note"><?php echo _e($admin_desc,'geodirectory');?></span>
			
			<?php if($is_required) {?>
			<span class="geodir_message_error"><?php echo _e($required_msg,'geodirectory');?></span> 
			<?php } ?>
		</div>
			
		<?php 
		} else if( $type == 'multiselect' ) { 
			$multi_display = 'select';
			if( !empty( $val['extra_fields'] ) ) {
				$multi_display = unserialize( $val['extra_fields'] );	
			}
			?>
			<div id="<?php echo esc_attr($name);?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix">
				
					<?php $site_title = __($site_title,'geodirectory'); (trim($site_title)) ? $site_title : '&nbsp;'; ?>
					<?php if($is_required) echo '<span class="input-required"></span>';?>
				
				<input type="hidden" name="gd_field_<?php echo esc_attr($name);?>" value="1" />
				<?php if ( $multi_display == 'select' ) { ?>
				<div class="geodir_multiselect_list">
					<select field_type="<?php echo esc_attr($type);?>" name="<?php echo esc_attr($name);?>[]" id="<?php echo $name;?>" multiple="multiple" class="geodir_textfield textfield_x chosen_select" data-placeholder="<?php _e( 'Select', 'vh' );?>" option-ajaxchosen="false">					
			<?php 
			} else {
				echo '<ul class="gd_multi_choice">';
			}
			
			if ( $option_values ) {
				$option_values_arr = explode( ',', $option_values );
				
				for ( $i=0; $i < count( $option_values_arr ); $i++ ) { 
									
					if ( strstr( $option_values_arr[$i], "/" ) ) {
						$multi_select_attr = explode( "/", $option_values_arr[$i] );
						$multi_select_lable = ucfirst( $multi_select_attr[0] );
						$multi_select_value = $multi_select_attr[1];
					} else {
						$multi_select_lable = ucfirst( $option_values_arr[$i] );
						$multi_select_value = $option_values_arr[$i];
					}
									
					$selected = '';
					$checked = '';
					
					if ( ( !is_array( $value ) && trim( $value ) != '' ) || ( is_array( $value ) && !empty( $value ) ) ) {
						if ( !is_array( $value ) ) {
							$value_array = explode( ',', $value );
						} else {
							$value_array = $value;
						}
						
						if ( is_array( $value_array ) ) {
							if ( in_array( $multi_select_value, $value_array ) ) {
								$selected = 'selected="selected"';
								$checked = 'checked="checked"';
							}
						}
					}
					
					if ( $multi_display == 'select' ) {
					?>
						<option value="<?php echo $multi_select_value; ?>" <?php echo $selected; ?>><?php echo $multi_select_lable; ?></option>
					<?php
					} else {
					?> 
						<li>
							<input name="<?php echo $name;?>[]" <?php echo $checked;?>  value="<?php echo esc_attr($multi_select_value); ?>" class="gd-<?php echo esc_attr($multi_display); ?>" field_type="<?php echo esc_attr($multi_display);?>" type="<?php echo esc_attr($multi_display); ?>" /> <?php echo $multi_select_lable; ?>
						</li>
					<?php
					}
				}
			}
			
			if ( $multi_display == 'select' ) { ?>
				</select></div>
			<?php } else { ?></ul><?php } ?>
			<span class="geodir_message_note"><?php echo _e($admin_desc,'geodirectory');?></span>
			
			<?php if($is_required) {?>
			<span class="geodir_message_error"><?php echo _e($required_msg,'geodirectory');?></span> 
			<?php } ?>
		</div>
		<?php
		} else if( $type=='html' ) {
		?>

		<div id="<?php echo esc_attr($name);?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix">
		
				<?php $site_title = __($site_title,'geodirectory'); (trim($site_title)) ? $site_title : '&nbsp;'; ?>
				<?php if($is_required) echo '<span class="input-required"></span>';?>
	
			
			<?php $editor_settings = array('media_buttons'=>false, 'textarea_rows'=>10); ?>
			
			<div class="editor" field_id="<?php echo esc_attr($name);?>" field_type="editor">
			<?php wp_editor( stripslashes($value), $name, $editor_settings ); ?>
			</div>
			
			<span class="geodir_message_note"><?php echo _e($admin_desc,'geodirectory');?></span>
			<?php if($is_required) {?>
			<span class="geodir_message_error"><?php echo _e($required_msg,'geodirectory');?></span> 
			<?php } ?>
			 
		</div>
		<?php }
		elseif($type=='datepicker'){
					
					if($extra_fields['date_format'] == '')
						$extra_fields['date_format'] = 'yy-mm-dd';
				?>
				<script type="text/javascript" >
				
				jQuery(function() {
                     jQuery( "#geodir_listing_start_date" ).datepicker( {dateFormat: 'yy-mm-dd'});
                     jQuery( "#geodir_listing_end_date" ).datepicker( {dateFormat: 'yy-mm-dd'});
                     
                     //jQuery( "#geodir_listing_start_date" ).datepicker( "option", "dateFormat", 'yy-mm-dd');
                     //jQuery( "#geodir_listing_end_date" ).datepicker( "option", "dateFormat", 'yy-mm-dd');

                    
					// jQuery("#geodir_listing_start_date").datepicker({
					//  minDate: 0,
					//  onSelect: function(dateText, inst) {
					//   var actualDate = new Date(dateText);
					//   var newDate = new Date(actualDate.getFullYear(), actualDate.getMonth(), actualDate.getDate()+1);
					//      jQuery('#geodir_listing_end_date').datepicker('option', 'minDate', newDate );
					//  }
					// });
					
					// jQuery("#geodir_listing_end_date").datepicker();
				    
				    
				  
				});
				
				</script>
		
		 <div id="<?php echo esc_attr($name);?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix">
			
				
				<?php $site_title = __($site_title,'geodirectory'); (trim($site_title)) ? $site_title : '&nbsp;'; ?>
				<?php if($is_required) echo '<span class="input-required"></span>';?>
		  
		   
			<input field_type="<?php echo esc_attr($type);?>" name="<?php echo esc_attr($name);?>" id="<?php echo esc_attr($name);?>" value="<?php echo $value ;?>" placeholder="<?php echo _e($site_title,'geodirectory');?>" type="text" class="geodir_textfield"  />
					
			<span class="geodir_message_note"><?php echo _e($admin_desc,'geodirectory');?></span>
			<?php if($is_required) {?>
			<span class="geodir_message_error"><?php echo _e($required_msg,'geodirectory');?></span> 
			<?php } ?>
		 </div>
		
		<?php }
		elseif($type=='time'){
				
				if($value != '')
					$value = date('H:i',strtotime($value));
		?>
			<script type="text/javascript" >
			jQuery(window).load(function(){
			
				jQuery('#<?php echo $name;?>').timepicker({
						showPeriod: true,
						showLeadingZero: true,
						showPeriod: true
				});

				jQuery('[field_type="time"]').click(function() {
					jQuery('#ui-datepicker-div').addClass('timepicker');
				});
			});
			</script>
		 <div id="<?php echo esc_attr($name);?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix">
			
				
				<?php $site_title = __($site_title,'geodirectory'); (trim($site_title)) ? $site_title : '&nbsp;'; ?>
				<?php if($is_required) echo '<span class="input-required"></span>';?>
		  
			<input readonly="readonly" field_type="<?php echo esc_attr($type);?>" name="<?php echo esc_attr($name);?>" id="<?php echo esc_attr($name);?>" value="<?php echo esc_attr($value);?>" placeholder="<?php echo _e($site_title,'geodirectory'); ?>" type="text" class="geodir_textfield"  />
				
			<span class="geodir_message_note"><?php echo _e($admin_desc,'geodirectory');?></span>
			<?php if($is_required) {?>
			<span class="geodir_message_error"><?php echo _e($required_msg,'geodirectory');?></span> 
			<?php } ?>
		 </div>
		
		<?php } 
		elseif($type=='taxonomy'){	if($value == $val['default']){$value='';} ?>
		<div id="<?php echo esc_attr($name);?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix">
			
				<?php $site_title = __($site_title,'vh'); (trim($site_title)) ? $site_title : '&nbsp;'; ?>
				<?php if($is_required) echo '<span class="input-required"></span>';?>
		
			
			<div id="<?php echo esc_attr($name);?>" class="geodir_taxonomy_field" style="float:left; width:70%;">
			<?php 
			global $wpdb,$post,$cat_display,$post_cat,$package_id, $exclude_cats;
			
			$exclude_cats = array();
			
			if($is_admin == '1'){
			
				$post_type = get_post_type();
				
				$package_info = array() ;
				
				$package_info = (array)geodir_post_package_info($package_info , $post, $post_type);
				
				if(!empty($package_info)){
				
					if(isset($package_info['cat']) && $package_info['cat'] != ''){
						
						$exclude_cats = explode(',',$package_info['cat']);
						
					}	
				}
			}
			
			$cat_display = unserialize($val['extra_fields']);
			
			if(isset($_REQUEST['backandedit']) && (is_array($post_cat[$name]) && !empty($post_cat[$name]))){
					
				$post_cat = implode(",",$post_cat[$name]);
					
			}else{
				if(isset($_REQUEST['pid']) && $_REQUEST['pid'] != '')
				$post_cat = geodir_get_post_meta($_REQUEST['pid'], $name,true);
			}
			
			
			global $geodir_addon_list;
			if(!empty($geodir_addon_list) && array_key_exists('geodir_payment_manager', $geodir_addon_list) && $geodir_addon_list['geodir_payment_manager'] == 'yes') {
				
				$catadd_limit =	$wpdb->get_var(
													$wpdb->prepare(
														"SELECT cat_limit FROM ".GEODIR_PRICE_TABLE." WHERE pid = %d",
														array($package_id)
													)
												);
				
			
			}else{
				$catadd_limit = 0;
			}
			
			
			if($cat_display != '' && $cat_display != 'ajax_chained'){
				
				$required_limit_msg = '';
				if($catadd_limit > 0 && $cat_display!='select' && $cat_display!='radio'){
					
					$required_limit_msg = __('Only select','vh').' '.$catadd_limit.__(' categories for this package.','vh');
				
				}else{
					$required_limit_msg = $required_msg;
				}
				
				echo '<input type="hidden" cat_limit="'.esc_attr($catadd_limit).'" id="cat_limit" value="'.esc_attr($required_limit_msg).'" name="cat_limit['.esc_attr($name).']"  />';
				
				
				if($cat_display == 'select' || $cat_display == 'multiselect')	{
					
					$cat_display == '';
					$multiple = '';
					if($cat_display == 'multiselect')
						$multiple = 'multiple="multiple"';
							
					echo '<select id="'.esc_attr($name).'" '.esc_attr($multiple).' type="'.esc_attr($name).'" name="post_category['.esc_attr($name).'][]" alt="'.esc_attr($name).'" field_type="'.esc_attr($cat_display).'">';
					
					if($cat_display == 'select')
						echo '<option value="">'.__('Select Category','vh').'</option>';
					
				}
				
				echo geodir_custom_taxonomy_walker($name,$catadd_limit=0);
				
				if($cat_display == 'select' || $cat_display == 'multiselect')
					echo '</select>';
			
			}else{
				
				echo vh_geodir_custom_taxonomy_walker2($name,$catadd_limit);
			
			}
			
			?>
			</div>
			
			<span class="geodir_message_note"><?php _e($admin_desc,'geodirectory');?></span>
			<?php if($is_required) {?>
			<span class="geodir_message_error"><?php echo _e($required_msg,'geodirectory');?></span> 
			<?php } ?>
		</div>
			
		<?php }
		elseif($type=='file'){ ?>
		
			<?php
			
		
			 
			// adjust values here
			$file_id = $name; // this will be the name of form field. Image url(s) will be submitted in $_POST using this key. So if $id == “img1” then $_POST[“img1”] will have all the image urls
			 
			if($value != ''){
				
			$file_value = trim($value,","); // this will be initial value of the above form field. Image urls.
			
			}else
				$file_value = '';
			 
			if($extra_fields['file_multiple'])
				$file_multiple = true; // allow multiple files upload
			else
				$file_multiple = false;	
				
			if($extra_fields['image_limit'])	
				$file_image_limit = $extra_fields['image_limit'];
			else
				$file_image_limit = 1;	
			 
			$file_width = geodir_media_image_large_width(); // If you want to automatically resize all uploaded images then provide width here (in pixels)
			 
			$file_height = geodir_media_image_large_height(); // If you want to automatically resize all uploaded images then provide height here (in pixels)
			
			if(!empty($file_value)){
				$curImages = explode(',',$file_value);
				if(!empty($curImages))
				$file_totImg = count($curImages);
			}
			
			?>				
		   <?php /*?> <h5 class="geodir-form_title"> <?php echo $site_title; ?>
				 <?php if($file_image_limit!=0 && $file_image_limit==1 ){echo '<br /><small>('.__('You can upload').' '.$file_image_limit.' '.__('image with this package').')</small>';} ?>
				 <?php if($file_image_limit!=0 && $file_image_limit>1 ){echo '<br /><small>('.__('You can upload').' '.$file_image_limit.' '.__('images with this package').')</small>';} ?>
				 <?php if($file_image_limit==0){echo '<br /><small>('.__('You can upload unlimited images with this package').')</small>';} ?>
			</h5>   <?php */?>
				  
			<div id="<?php echo esc_attr($name);?>_row" class="<?php if($is_required) echo 'required_field';?> <?php echo $css_class;?> geodir_form_row clearfix" >
			
			<div id="<?php echo esc_attr($file_id); ?>dropbox" class="geodir-file-upload" align="center" style="">
				<span class="field-title"><?php $site_title = __($site_title,'geodirectory'); echo $site_title; ?></span>
				<?php if($is_required) echo '<span class="input-required"></span>';?>
				<input class="geodir-custom-file-upload" field_type="file" type="hidden" name="<?php echo esc_attr($file_id); ?>" id="<?php echo esc_attr($file_id); ?>" value="<?php echo esc_attr($file_value); ?>" />
				<input type="hidden" name="<?php echo esc_attr($file_id); ?>image_limit" id="<?php echo esc_attr($file_id); ?>image_limit" value="<?php echo $file_image_limit; ?>" />
				<input type="hidden" name="<?php echo esc_attr($file_id); ?>totImg" id="<?php echo esc_attr($file_id); ?>totImg" value="<?php if(isset($file_totImg)){ echo esc_attr($file_totImg);}else{ echo '0';} ?>" />
				<div style="float:left; width:100%;">
				<div class="required_field plupload-upload-uic hide-if-no-js <?php if ($file_multiple): ?>plupload-upload-uic-multiple<?php endif; ?>" id="<?php echo esc_attr($file_id); ?>plupload-upload-ui" style="float:left; width:100%;">
					<?php /*?><h4><?php _e('Drop files to upload');?></h4><br/><?php */?>
					<input id="<?php echo esc_attr($file_id); ?>plupload-browse-button" type="button" value="<?php esc_attr_e('Select Files','vh'); ?>" class="geodir_button" style="margin-top:10px;"  />
					<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($file_id.'pluploadan'); ?>"></span>
					<?php if ($file_width && $file_height): ?>
						<span class="plupload-resize"></span>
						<span class="plupload-width" id="plupload-width<?php echo esc_attr($file_width); ?>"></span>
						<span class="plupload-height" id="plupload-height<?php echo esc_attr($file_height); ?>"></span>
					<?php endif; ?>
					<div class="filelist"></div>
				</div>			
				<div class="plupload-thumbs <?php if ($file_multiple): ?>plupload-thumbs-multiple<?php endif; ?> " id="<?php echo esc_attr($file_id); ?>plupload-thumbs" style=" clear:inherit; margin-top:0; margin-left:15px; padding-top:10px; float:left; width:50%;">
				</div>
				<?php /*?><span id="upload-msg" ><?php _e('Please drag &amp; drop the images to rearrange the order');?></span><?php */?>
								
				<span id="<?php echo esc_attr($file_id); ?>upload-error" style="display:none"></span>
				
			</div>
			<div class="clearfix"></div>
			</div>
			<span class="geodir_message_note"><?php _e($admin_desc,'vh');?></span>
			<?php if($is_required) {?>
			<span class="geodir_message_error"><?php echo _e($required_msg,'geodirectory');?></span> 
			<?php } ?>
			</div>
		
			
		<?php }
		
		do_action('geodir_after_custom_form_field_'.$name, $listing_type, $package_id, $val);
		 
	}
	
}

/* Category Slection Interface in add/edit listing form */
function vh_geodir_addpost_categories_html($request_taxonomy, $parrent, $selected = false, $main_selected = true, $default = false, $exclude='' ){ 
				
				global $exclude_cats;
				
				if($exclude != ''){
					$exclude_cats = unserialize(base64_decode($exclude));
				}
				
				if((is_array($exclude_cats) && !empty($exclude_cats) && !in_array($parrent, $exclude_cats)) || 
				(!is_array($exclude_cats) || empty($exclude_cats))
				){
?>
	
	<?php $main_cat = get_term( $parrent, $request_taxonomy);?>
	
	<div class="post_catlist_item" style="border:1px solid #CCCCCC; margin:5px auto; padding:5px;">
		<img src="<?php echo esc_attr(geodir_plugin_url()).'/geodirectory-assets/images/move.png';?>" onclick="jQuery(this).closest('div').remove();update_listing_cat();" align="right" /> 
		<span class="checkbox_box checked icon-ok disabled"></span>
		<input type="checkbox" value="<?php echo esc_attr($main_cat->term_id);?>" class="listing_main_cat"  onchange="if(jQuery(this).is(':checked')){jQuery(this).closest('div').find('.post_default_category').prop('checked',false).show();}else{jQuery(this).closest('div').find('.post_default_category').prop('checked',false).hide();};update_listing_cat()" checked="checked" disabled="disabled" />
	   <span class="input-side-text"> 
		<?php printf( __('Add listing in %s category','vh'), ucwords($main_cat->name) );?> 
		</span> 
		<br/>
		<div class="clearfix"></div>
		<div class="post_default_category" >
		<span class="radiobutton"></span>
		<input type="radio" name="post_default_category"  value="<?php echo esc_attr($main_cat->term_id);?>" onchange="update_listing_cat()" <?php if($default) echo ' checked="checked" ';?>   />
		<span class="input-side-text"> 
		<?php printf( __('Set %s as default category','vh'), ucwords($main_cat->name) );?> 
		</span>
		</div>
		
		<br/>
		<?php 
		$cat_terms = get_terms($request_taxonomy, array('parent' => $main_cat->term_id, 'hide_empty' => false, 'exclude'=>$exclude_cats)); 
		if(!empty($cat_terms)) { ?>
			<span> <?php printf( __('Add listing in category','vh') );?></span>
			<?php geodir_get_catlist($request_taxonomy, $parrent, $selected) ?>
		<?php } ?>
	</div>
		
<?php }  }

function vh_geodir_editpost_categories_html($request_taxonomy, $request_postid, $post_categories){ 
	
	if( !empty($post_categories) && array_key_exists($request_taxonomy,$post_categories) ){
		$post_cat_str = $post_categories[$request_taxonomy];
		$post_cat_array = explode("#",$post_cat_str);
		if(is_array($post_cat_array)){
			foreach($post_cat_array as $post_cat_html){
				
				$post_cat_info = explode(":",$post_cat_html);
				$post_maincat_str = $post_cat_info[0];
				
				if(!empty($post_maincat_str)){	
					$post_maincat_info = explode(",",$post_maincat_str);
					$post_maincat_id = $post_maincat_info[0];
					($post_maincat_info[1] == 'y') ? $post_maincat_selected = true : $post_maincat_selected = false ;
					(end($post_maincat_info) == 'd') ? $post_maincat_default = true : $post_maincat_default = false ;
				}
				$post_sub_catid = '';
				if(isset($post_cat_info[1]) &&  !empty($post_cat_info[1])){	
					$post_sub_catid = (int)$post_cat_info[1];
				}
				 
				vh_geodir_addpost_categories_html($request_taxonomy, $post_maincat_id, $post_sub_catid, $post_maincat_selected, $post_maincat_default  );
				 
			}
		}else{
			
			$post_cat_info = explode(":",$post_cat_str);
			$post_maincat_str = $post_cat_info[0];
			
			$post_sub_catid = '';
			
			if(!empty($post_maincat_str)){	
				$post_maincat_info = explode(",",$post_maincat_str);
				$post_maincat_id = $post_maincat_info[0];
				($post_maincat_info[1] == 'y') ? $post_maincat_selected = true : $post_maincat_selected = false ;
				(end($post_maincat_info) == 'd') ? $post_maincat_default = true : $post_maincat_default = false ;
			}
			
			if(isset($post_cat_info[1]) &&  !empty($post_cat_info[1])){	
				$post_sub_catid = (int)$post_cat_info[1];
			}
			
			vh_geodir_addpost_categories_html($request_taxonomy, $post_maincat_id, $post_sub_catid, $post_maincat_selected, $post_maincat_default  );
			
		}	
	}
}

/* secound test */
if (!function_exists('vh_geodir_custom_taxonomy_walker2')) {
function vh_geodir_custom_taxonomy_walker2($cat_taxonomy, $cat_limit = '')
{
	$post_category = '';
	$post_category_str = '';
	global $exclude_cats;
	
	$cat_exclude = '';
	if(is_array($exclude_cats) && !empty($exclude_cats))
		$cat_exclude = serialize($exclude_cats);
	
	if(isset($_REQUEST['backandedit'])){
		$post = (object)unserialize($_SESSION['listing']);
		
		if(!is_array($post->post_category[$cat_taxonomy]))
			$post_category = $post->post_category[$cat_taxonomy];
			
		$post_categories = $post->post_category_str;
		if(!empty($post_categories) && array_key_exists($cat_taxonomy,$post_categories))
			$post_category_str = $post_categories[$cat_taxonomy];
	
	}elseif((geodir_is_page('add-listing') && isset($_REQUEST['pid']) && $_REQUEST['pid'] != '') || (is_admin())) { 
		global $post;
		$post_category = geodir_get_post_meta($post->ID,$cat_taxonomy,true);
		$post_categories = get_post_meta($post->ID,'post_categories',true);
		
		if($post_category != '' && is_array($exclude_cats) && !empty($exclude_cats)){

			$post_category_upd = explode(',', $post_category);
			$post_category_change = '';
			foreach($post_category_upd as $cat){
			
				if(!in_array($cat, $exclude_cats) && $cat != ''){
					$post_category_change .= ','.$cat;
				}	
			}	
			$post_category = $post_category_change;
		}
		
		
		
		if(!empty($post_categories) && array_key_exists($cat_taxonomy,$post_categories))
			$post_category_str = $post_categories[$cat_taxonomy];	
			
	}
	
	echo '<input type="hidden" id="cat_limit" value="'.esc_attr($cat_limit).'" name="cat_limit['.esc_attr($cat_taxonomy).']"  />'; 
	
	echo '<input type="hidden" id="post_category" value="'.esc_attr($post_category).'" name="post_category['.esc_attr($cat_taxonomy).']"  />'; 
	
	echo '<input type="hidden" id="post_category_str" value="'.esc_attr($post_category_str).'" name="post_category_str['.esc_attr($cat_taxonomy).']"  />'; 
	
	
	?>	<div class="main_cat_list" style=" <?php if(isset($style)){ echo $style;}?> "> 
			<?php geodir_get_catlist($cat_taxonomy,0);  // print main categories list ?>
		</div>
		<div class="cat_sublist" > 
			<?php 
				
				$post_id = isset($post->ID) ? $post->ID : '';
				
				if((geodir_is_page('add-listing') || is_admin()) && !empty($post_categories[$cat_taxonomy]) ) { 
				
					vh_geodir_editpost_categories_html($cat_taxonomy, $post_id, $post_categories);
				}	
			?>
		</div>
		<script type="text/javascript">
		
		function show_subcatlist(main_cat){
				if(main_cat != ''){
					var url = '<?php echo esc_url(geodir_get_ajax_url());?>';
					var cat_taxonomy = '<?php echo esc_attr($cat_taxonomy);?>';
					var cat_exclude = '<?php echo base64_encode($cat_exclude);?>'; 
					var cat_limit = jQuery('#'+cat_taxonomy).find('#cat_limit').val();
					jQuery.post(url,{geodir_ajax:'category_ajax',cat_tax:cat_taxonomy,main_catid:main_cat,exclude:cat_exclude},function(data){
						if(data != ''){
							jQuery('#'+cat_taxonomy).find('.cat_sublist').append(data);
							
							setTimeout(function(){
								jQuery('#'+cat_taxonomy).find('.cat_sublist').find('.chosen_select').chosen();
							},200);
							
							
						}	
						maincat_obj = jQuery('#'+cat_taxonomy).find('.main_cat_list');					
						
						if(cat_limit != '' && jQuery('#'+cat_taxonomy).find('.cat_sublist .chosen_select').length >= cat_limit ){
							maincat_obj.find('.chosen_select').chosen('destroy');
							maincat_obj.hide();		
						}else{
							maincat_obj.show();
							maincat_obj.find('.chosen_select').chosen('destroy');
							maincat_obj.find('.chosen_select').prop('selectedIndex', 0);
							maincat_obj.find('.chosen_select').chosen();
						}					
						
					update_listing_cat();
					maincat_obj.hide();	//jino													
					});
				}
			}
			
			function update_listing_cat(){
				var cat_taxonomy = '<?php echo esc_attr($cat_taxonomy);?>';
				var cat_ids = '';	
				var main_cat = '';
				var sub_cat = '';
				var post_cat_str = '';
				var cat_limit = jQuery('#'+cat_taxonomy).find('#cat_limit').val();
				
				jQuery('#'+cat_taxonomy).find('.cat_sublist > div').each(function(){
					main_cat = jQuery(this).find('.listing_main_cat').val();
					
					if(jQuery(this).find('.chosen_select').length > 0)
						sub_cat = jQuery(this).find('.chosen_select').val()
					
					if(post_cat_str != '')
						post_cat_str = post_cat_str + '#';
						
					post_cat_str = post_cat_str + main_cat;
					
					if(jQuery(this).find('.listing_main_cat').is(':checked')){
					  cat_ids = cat_ids + ',' + main_cat;
					  post_cat_str = post_cat_str + ',y';
					  
					  if(jQuery(this).find('.post_default_category input').is(':checked'))
						post_cat_str = post_cat_str + ',d';
					  
					}else{
						post_cat_str = post_cat_str + ',n';
					}  
					  
					if(sub_cat != ''){ 
						cat_ids = cat_ids + ',' + sub_cat;
						post_cat_str = post_cat_str + ':' + sub_cat;		
					}else{
						post_cat_str = post_cat_str + ':';		
					}
					
				});
				
				maincat_obj = jQuery('#'+cat_taxonomy).find('.main_cat_list');					
				
				
				if(cat_limit != '' && jQuery('#'+cat_taxonomy).find('.cat_sublist > div.post_catlist_item').length >= cat_limit && cat_limit != 0 ){
					maincat_obj.find('.chosen_select').chosen('destroy');
					maincat_obj.hide();		
				}else{//inadd ko
					maincat_obj.show();
					maincat_obj.find('.chosen_select').chosen('destroy');
					maincat_obj.find('.chosen_select').prop('selectedIndex', 0);
					maincat_obj.find('.chosen_select').chosen();
					
					maincat_obj.find('.chosen_select').trigger("chosen:updated");
					jQuery('#'+cat_taxonomy).find('#post_category').val(cat_ids);
					jQuery('#'+cat_taxonomy).find('#post_category_str').val(post_cat_str);
				}
						
			
			 
			   
				
			}
			
			
		</script>	
		<?php 
			if( !empty($post_categories) && array_key_exists($cat_taxonomy,$post_categories) ){
				$post_cat_str = $post_categories[$cat_taxonomy];
				$post_cat_array = explode("#",$post_cat_str);
				if(count($post_cat_array) >= $cat_limit && $cat_limit != 0)
					$style = "display:none;";
			}	
		?>
		<?php 
	  
}
}

add_action('geodir_after_main_form_fields2', 'vh_geodir_after_main_form_fields', 20);
function vh_geodir_after_main_form_fields() {
	
	if(get_option('geodir_accept_term_condition')){
		global $post;
		$term_condition = '';
		if(isset($_REQUEST['backandedit'])){
			$post = (object)unserialize($_SESSION['listing']);
			$term_condition = isset($post->geodir_accept_term_condition) ? $post->geodir_accept_term_condition : '';	
		}
		
	?>
	<div id="geodir_accept_term_condition_row" class="required_field geodir_form_row clearfix">
				<div class="geodir_taxonomy_field" style="float:left; width:70%;">
				<span style="display:block">
				<span class="checkbox_box"></span>
				<input class="main_list_selecter" type="checkbox" field_type="checkbox" name="geodir_accept_term_condition" id="geodir_accept_term_condition" class="geodir_textfield" value="1" style="display:inline-block"/>
				<span class="checkbox_text">
					<?php
						if ( !get_option('geodir_term_condition_page', false) ) {
							echo __('Accept Terms and Conditions', 'vh');
						} else {
							echo '<a href="'.get_permalink(get_option('geodir_term_condition_page', false)).'" target="_blank">'.__('Accept Terms and Conditions', 'vh').'</a>';
						}
					?>
				</span>
				</span>
			</div>
			 <span class="geodir_message_error"><?php if(isset($required_msg)){ _e($required_msg,'geodirectory');}?></span>
		</div>
	<?php
	
	}
}

/* ---- Admin Ajax ---- */
 // call for not logged in ajax
remove_action('wp_ajax_geodir_ajax_action', "geodir_ajax_handler");
remove_action( 'wp_ajax_nopriv_geodir_ajax_action', 'geodir_ajax_handler' );
add_action('wp_ajax_geodir_ajax_action', "vh_geodir_ajax_handler");
add_action( 'wp_ajax_nopriv_geodir_ajax_action', 'vh_geodir_ajax_handler' );
function vh_geodir_ajax_handler()
{
	global $wpdb;
	
	if(isset($_REQUEST['gd_listing_view']) && $_REQUEST['gd_listing_view'] != ''){$_SESSION['gd_listing_view'] = $_REQUEST['gd_listing_view'];echo '1';}

	
	if(isset($_REQUEST['geodir_ajax']) && $_REQUEST['geodir_ajax'] == 'category_ajax'){
		
		if(isset($_REQUEST['main_catid']) && isset($_REQUEST['cat_tax']) && isset($_REQUEST['exclude']) )
			vh_geodir_addpost_categories_html($_REQUEST['cat_tax'],$_REQUEST['main_catid'],'','','',$_REQUEST['exclude']);
			
		elseif(isset($_REQUEST['catpid']) && isset($_REQUEST['cat_tax']) )
			geodir_editpost_categories_html($_REQUEST['cat_tax'],$_REQUEST['catpid']);
			
	}
	
	if(( isset($_REQUEST['geodir_ajax']) && $_REQUEST['geodir_ajax'] == 'admin_ajax') || isset($_REQUEST['create_field']) || isset($_REQUEST['sort_create_field'])){
		if(current_user_can( 'manage_options')){
			include_once ( geodir_plugin_path() . '/geodirectory-admin/geodir_admin_ajax.php'); 
		}else{
			wp_redirect(home_url().'/?geodir_signup=true');
			exit();
		}
	}
	
	if(isset($_REQUEST['geodir_autofill']) && $_REQUEST['geodir_autofill']!='' && isset($_REQUEST['_wpnonce'])){
		if(current_user_can( 'manage_options')){
			switch($_REQUEST['geodir_autofill']):
				case "geodir_dummy_delete" :
					if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'geodir_dummy_posts_delete_noncename' ) )
					return;	 
					
					if(isset($_REQUEST['posttype']))
						do_action('geodir_delete_dummy_posts_'.$_REQUEST['posttype']);
				break;
				case "geodir_dummy_insert" :
					if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'geodir_dummy_posts_insert_noncename' ) )
					return;
					
					global $dummy_post_index, $city_bound_lat1,$city_bound_lng1,$city_bound_lat2,$city_bound_lng2 ;
					$dummy_post_index = $_REQUEST['insert_dummy_post_index'];
					$city_bound_lat1 = $_REQUEST['city_bound_lat1'];
					$city_bound_lng1 = $_REQUEST['city_bound_lng1'];
					$city_bound_lat2 = $_REQUEST['city_bound_lat2'];
					$city_bound_lng2 = $_REQUEST['city_bound_lng2'];
					
					if(isset($_REQUEST['posttype']))
						do_action('geodir_insert_dummy_posts_'.$_REQUEST['posttype']);
					
				break;
			endswitch;
		}else{
			wp_redirect(home_url().'/?geodir_signup=true');
			exit();
		}
	}
	
	if(isset($_REQUEST['geodir_import_data']) && $_REQUEST['geodir_import_data']!=''){
		if(current_user_can( 'manage_options')){
			geodir_import_data();
		}else{
			wp_redirect(home_url().'/?geodir_signup=true');
			exit();
		}
	}
	
	if(isset($_REQUEST['popuptype']) && $_REQUEST['popuptype'] != '' && isset($_REQUEST['post_id']) && $_REQUEST['post_id'] != ''){
		
		if($_REQUEST['popuptype'] == 'b_send_inquiry' || $_REQUEST['popuptype'] == 'b_sendtofriend')
			require_once (geodir_plugin_path().'/geodirectory-templates/popup-forms.php');
		
		exit;
	}
	
	/*if(isset($_REQUEST['geodir_ajax']) && $_REQUEST['geodir_ajax'] == 'filter_ajax'){
		include_once ( geodir_plugin_path() . '/geodirectory-templates/advance-search-form.php'); 
	}*/
	
	if(isset($_REQUEST['geodir_ajax']) && $_REQUEST['geodir_ajax'] == 'map_ajax'){
		include_once ( get_template_directory() . '/geodirectory/get_markers.php'); 
	}
	
	
	
	if(isset($_REQUEST['geodir_ajax']) && $_REQUEST['geodir_ajax'] == 'favorite'){
		if(is_user_logged_in())	{
			switch($_REQUEST['ajax_action']):
				case "add" :
					geodir_add_to_favorite($_REQUEST['pid']);
				break;
				case "remove" :
					geodir_remove_from_favorite($_REQUEST['pid']);
				break;
			endswitch;
		}else{
			wp_redirect(home_url().'/?geodir_signup=true');
			exit();
		}
	}
	
	if(isset($_REQUEST['geodir_ajax']) && $_REQUEST['geodir_ajax'] == 'add_listing'){
		
		$is_current_user_owner = true;
		if(isset($_REQUEST['pid']) && $_REQUEST['pid'] != ''){
			$is_current_user_owner = geodir_listing_belong_to_current_user($_REQUEST['pid']);
		}
		
		$request = isset($_SESSION['listing']) ? unserialize($_SESSION['listing']) : '';
					
		if(is_user_logged_in() && $is_current_user_owner)	{
		
			switch($_REQUEST['ajax_action']):
				case "add":
				case "update":
					
					if(isset($request['geodir_spamblocker']) && $request['geodir_spamblocker']=='64' && isset($request['geodir_filled_by_spam_bot']) && $request['geodir_filled_by_spam_bot']=='')
					{
						
						$last_id = geodir_save_listing();
						
						if($last_id){
							//$redirect_to = get_permalink( $last_id );
							 $redirect_to = geodir_getlink( get_permalink( get_option('geodir_success_page') ),array('pid'=>$last_id) );
							
						}elseif(isset($_REQUEST['pid']) && $_REQUEST['pid'] != ''){
							$redirect_to = get_permalink( get_option('geodir_add_listing_page') );
							$redirect_to = geodir_getlink($redirect_to,array('pid'=>$post->pid),false);
						}else
							$redirect_to = get_permalink( get_option('geodir_add_listing_page') );
						
						wp_redirect( $redirect_to );
					
					}else{
						
						if(isset($_SESSION['listing']))
							unset($_SESSION['listing']);
						wp_redirect( home_url() );
					
					}
					
				break;
				case "cancel" :
					
					unset($_SESSION['listing']);
					
					if( isset($_REQUEST['pid']) && $_REQUEST['pid'] != '' && get_permalink( $_REQUEST['pid'] ) )
						wp_redirect( get_permalink( $_REQUEST['pid'] ) );
					else{
						geodir_remove_temp_images();
						wp_redirect( geodir_getlink( get_permalink( get_option('geodir_add_listing_page') ),array('listing_type'=>$_REQUEST['listing_type']) ) );	
					}	
							
				break;
				
				case "publish" :
					
					if(isset($request['geodir_spamblocker']) && $request['geodir_spamblocker']=='64' && isset($request['geodir_filled_by_spam_bot']) && $request['geodir_filled_by_spam_bot']=='')
					{
						
						if( isset($_REQUEST['pid'] ) && $_REQUEST['pid'] != ''){
				
							$new_post = array();
							$new_post['ID'] = $_REQUEST['pid'] ;
							//$new_post['post_status'] = 'publish';
							
							$lastid = wp_update_post( $new_post );
								
							wp_redirect( get_permalink( $lastid  ) );
						}else{
							
							$last_id = geodir_save_listing();
						
							if($last_id){
								//$redirect_to = get_permalink( $last_id );
								 $redirect_to = geodir_getlink( get_permalink( get_option('geodir_success_page') ),array('pid'=>$last_id) );
							}elseif(isset($_REQUEST['pid']) && $_REQUEST['pid'] != ''){
								$redirect_to = get_permalink( get_option('geodir_add_listing_page') );
								$redirect_to = geodir_getlink($redirect_to,array('pid'=>$post->pid),false);
							}else
								$redirect_to = get_permalink( get_option('geodir_add_listing_page') );
							
							wp_redirect( $redirect_to );
						}	
						
					}else{
						
						if(isset($_SESSION['listing']))
							unset($_SESSION['listing']);
						wp_redirect( home_url() );
					
					}
					
				break;
				case "delete" :
					if(isset($_REQUEST['pid']) && $_REQUEST['pid'] != ''){
					
						global $current_user;
						get_currentuserinfo();
						$post_type = get_post_type($_REQUEST['pid']);
						$lastid = wp_delete_post( $_REQUEST['pid'] );
						if($lastid && !is_wp_error( $lastid ))
							wp_redirect($_SERVER['HTTP_REFERER']);
							
							//wp_redirect( geodir_getlink(get_author_posts_url($current_user->ID),array('geodir_dashbord'=>'true','stype'=>$post_type ),false) );
					}		
				break;
			endswitch;
			
		}else{
			wp_redirect(home_url().'/?geodir_signup=true');
			exit();
		}
		
	}
	
	if(isset($_REQUEST['geodir_ajax']) && $_REQUEST['geodir_ajax'] == 'user_login'){
		
		include_once ( geodir_plugin_path().'/geodirectory-functions/geodirectory_reg.php') ;
	}
	
	die;
	
	
}

if ( get_option('geodir_disable_rating', '0') == '0' ) {
	add_action( 'comment_form_logged_in_after', 'vh_geodir_comment_rating_fields', 20 );
	add_action( 'comment_form_before_fields', 'vh_geodir_comment_rating_fields', 20 );
}
function vh_geodir_comment_rating_fields() {
	global $post;
	
	if ( function_exists('geodir_get_posttypes') ) {
		$post_types = geodir_get_posttypes();
	} else {
		$post_types = array();
	}
	
	if(in_array($post->post_type,$post_types)) { ?>
		<div class="review-container">
			<span id="gd_star1" class="star-icon icon-star-empty"></span>
			<span id="gd_star2" class="star-icon icon-star-empty"></span>
			<span id="gd_star3" class="star-icon icon-star-empty"></span>
			<span id="gd_star4" class="star-icon icon-star-empty"></span>
			<span id="gd_star5" class="star-icon icon-star-empty"></span>
			<span class="icon-info"><?php _e("Click on a star to add rating", "vh"); ?></span>
		</div>
		<?php 
	}
}

function vh_get_location_search_terms() {
	global $wpdb;
	$term = sanitize_text_field($_REQUEST['term']);
	$post_type = sanitize_text_field($_REQUEST['vh_post_type']);

	if ( $post_type == 'gd_event' ) {
		$querystr = "SELECT null as Price,post_city as Country,COUNT(post_city) as CountryNum, null as TotalPrice FROM " . $wpdb->prefix .  "geodir_".$post_type."_detail WHERE post_status = \"publish\" AND ( post_city LIKE \"".$term."%\" OR post_country LIKE \"".$term."%\" ) GROUP BY post_city ORDER BY Price ASC";
	} else if ( get_option("vh_theme_version") != "SkyDirectory" ) {
		$querystr = "SELECT MIN(geodir_listing_price) as Price,post_city as Country,COUNT(post_city) as CountryNum, SUM(geodir_listing_price) as TotalPrice FROM " . $wpdb->prefix .  "geodir_".$post_type."_detail WHERE post_status = \"publish\" AND ( post_city LIKE \"".$term."%\" OR post_country LIKE \"".$term."%\" ) GROUP BY post_city ORDER BY geodir_listing_price ASC";
	} else {
		$querystr = "SELECT null as Price,post_city as Country,COUNT(post_city) as CountryNum, null as TotalPrice FROM " . $wpdb->prefix .  "geodir_".$post_type."_detail WHERE post_status = \"publish\" AND ( post_city LIKE \"".$term."%\" OR post_country LIKE \"".$term."%\" ) GROUP BY post_city ORDER BY Price ASC";
	}

	$queryresults = $wpdb->get_results($querystr);
	$terms_json = array();

	if ( !empty($queryresults) ) {
		foreach ($queryresults as $value) {
			$terms_arr["value"] = $value->Country;
			if ( get_option("vh_theme_version") != "SkyDirectory" && $post_type != 'gd_event' ) {
				if ( $value->CountryNum == 1 ) {
					$terms_arr["label"] = $value->Country." <span class=\"label-right\">".$value->CountryNum." ".__("propertie, from", "vh")." ".get_option('vh_currency_symbol').$value->Price." "._e("per night", "vh")."</span><span class=\"listing-bar\" style=\"width:".($value->Price/$value->TotalPrice*100)."%\"></span>";
				} else {
					$terms_arr["label"] = $value->Country." <span class=\"label-right\">".$value->CountryNum." ".__("properties, from", "vh")." ".get_option('vh_currency_symbol').$value->Price." "._e("per night", "vh")."</span><span class=\"listing-bar\" style=\"width:".($value->Price/$value->TotalPrice*100)."%\"></span>";
				}
			} else {
				if ( $post_type != 'gd_event' ) {
					$entries = __("propertie", "vh");
				} else {
					$entries = __("events", "vh");
				}
				if ( $value->CountryNum == 1 ) {
					$terms_arr["label"] = $value->Country." <span class=\"label-right\">".$value->CountryNum." ".$entries."</span>";
				} else {
					$terms_arr["label"] = $value->Country." <span class=\"label-right\">".$value->CountryNum." ".$entries."</span>";
				}
			}
			$terms_json[] = $terms_arr;
		}
	} else {
		$terms_arr["value"] = $term;
		$terms_arr["label"] = __("Sorry, no matches were found.", "vh");
		$terms_json[] = $terms_arr;
	}
	
	$json = json_encode($terms_json);
	echo $json;
	exit();
}
add_action( 'wp_ajax_vh_get_location_search_terms', 'vh_get_location_search_terms' );
add_action( 'wp_ajax_nopriv_vh_get_location_search_terms', 'vh_get_location_search_terms' );

function vh_get_listing_category() {
	global $wpdb;
	$category_term = $_REQUEST['vh_post_type'].'category';
	$term = sanitize_text_field($_REQUEST['term']);

	$querystr = "SELECT name as Name FROM " . $wpdb->prefix .  "terms t, " . $wpdb->prefix . "term_taxonomy tx WHERE t.name LIKE \"".$term."%\" AND ( tx.term_id = t.term_id AND tx.taxonomy = '".$category_term."' ) GROUP BY t.name ORDER BY t.name ASC";
	$queryresults = $wpdb->get_results($querystr);

	$terms_json = array();

	if ( !empty($queryresults) ) {
		foreach ($queryresults as $value) {
			$terms_arr["value"] = $value->Name;
			$terms_json[] = $terms_arr;
		}
	} else {
		$terms_arr["value"] = $term;
		$terms_arr["label"] = __("Sorry, no matches were found.", "vh");
		$terms_json[] = $terms_arr;
	}
	
	$json = json_encode($terms_json);
	echo $json;
	exit();
}
add_action( 'wp_ajax_vh_get_listing_category', 'vh_get_listing_category' );
add_action( 'wp_ajax_nopriv_vh_get_listing_category', 'vh_get_listing_category' );

function get_gmap_listings() {

	global $post, $wpdb;
	$random = rand();

	$version_class = $output = "";
	if ( get_option("vh_theme_version") == "SkyDirectory" ) {
		$version_class = " skydirectory";
	}

	$querystr2 = "SELECT * FROM " . $wpdb->prefix .  "geodir_".get_post_type()."_detail WHERE post_status = 'publish' AND post_city LIKE '%" . sanitize_text_field($post->post_city) . "%'";

	$geodir_dist = get_option('geodir_search_dist');
	if ( $geodir_dist != '' ) {
		$DistanceRadius = geodir_getDistanceRadius(get_option('geodir_search_dist_1'));
		$geodir_table = $wpdb->prefix .  "geodir_".get_post_type()."_detail";
		$search_lat = $post->post_latitude;
		$search_long = $post->post_longitude;
		
		$distance_where = " AND CONVERT((" . $DistanceRadius . " * 2 * ASIN(SQRT( POWER(SIN((ABS($search_lat) - ABS(" . $geodir_table . ".post_latitude)) * pi()/180 / 2), 2) +COS(ABS($search_lat) * pi()/180) * COS( ABS(" . $geodir_table . ".post_latitude) * pi()/180) *POWER(SIN(($search_long - " . $geodir_table . ".post_longitude) * pi()/180 / 2), 2) ))),DECIMAL(64,4)) <= " . $geodir_dist;
		$querystr2 .= $distance_where;
	}

	$queryresults2 = $wpdb->get_results($querystr2);

	if ( !empty($queryresults2) ) {
		$output .= '<input type="hidden" id="geodir-open-listing-location" value="'.$post->post_city.'"/>';
		$output .= '<div class="google-map-main">';
		foreach ($queryresults2 as $similar_listings) {
			if ( $similar_listings->overall_rating != 0 ) {
				$overall_rating = $similar_listings->overall_rating;
			} else {
				$overall_rating = 0;
			}

			
			if ( get_the_post_thumbnail( $similar_listings->post_id, 'gallery-medium' ) ) {
				$listing_image = get_the_post_thumbnail( $similar_listings->post_id, 'gallery-medium' );
			} elseif ( get_option('geodir_listing_no_img', '') ) {
				$image = vh_imgresize(get_option( 'geodir_listing_no_img', '' ), 125, 125, true);
				$listing_image = '<img src="'.$image.'" class="attachment-gallery-medium wp-post-image" alt="google-map-img">';
			}

			$image_class = '';
			if ( $listing_image == '' ) {
				$image_class = ' no-image';
			}

			$output .= '<div id="list-geo-marker-'.$similar_listings->post_id.'" class="google-map-container">';
			$output .= '<div class="google-map-image'.$image_class.'">';
			$output .= $listing_image;
			if ( get_option("vh_theme_version") != "SkyDirectory" && get_post_type( $similar_listings->post_id ) != 'gd_event' ) {
				if ( isset($similar_listings->geodir_listing_price) ) {
					$listing_price = $similar_listings->geodir_listing_price;
				} else {
					$listing_price = 0;
				}
				###########jino edit##############
				$values = dynamic_convert( get_post_meta( $similar_listings->post_id , 'vh_resource_id', true),$_COOKIE['C_CURRENCY'],$listing_price,'');
				$listing_prices = $values['money'];
				$sign = $values['sign'];
												
				//$output .= '<span class="listing-price'.$version_class.'">' . get_option('vh_currency_symbol') . $listing_price . '</span>';
					$output .= '<span class="listing-price'.$version_class.'">' . 	$sign . $listing_prices . '</span>';
			} elseif ( get_option("vh_theme_version") != "SkyDirectory" && get_post_type( $similar_listings->post_id ) == 'gd_event' ) {
				$output .= '<span class="listing-price'.$version_class.'">' . get_geodir_event_date( $similar_listings->post_id ) . '</span>';
			}
			if ( $similar_listings->is_featured == "1" || strpos(strtolower($similar_listings->post_tags), 'featured') !== false ) {
				$output .= '<div class="map-listing-featured">'.__('Featured', 'vh').'</div>';
			}

			$output .= '</div>';
			$output .= '<div class="google-map-info'.$image_class.'">';
			$output .= '
				<div class="google-map-rating">';
					for ($i=0; $i < $overall_rating; $i++) { 
						$output .= "<span class=\"listing-item-star icon-star\"></span>";
					}
					if ( 5 - $overall_rating != 0 ) {
						for ($i=0; $i < 5 - $overall_rating; $i++) { 
							$output .= "<span class=\"listing-item-star icon-star-empty\"></span>";
						}
					}
				$output .= '
				</div>';
			$output .= '<div class="google-map-title"><a href="' . get_permalink( $similar_listings->post_id ) . '">' . get_the_title( $similar_listings->post_id ) . '</a></div>';
			$output .= '<div class="google-map-location">' . $similar_listings->post_city . ', ' . $similar_listings->post_country . '</div>';
			$output .= '</div><div class="clearfix"></div></div>';
		}
		$output .= '</div>';
	}

	return $output;
}

function vh_get_user_listings( $user_id, $post_type = 'gd_place' ) {
	global $wpdb;

	$query = "SELECT count(ID) as ListingCount FROM " . $wpdb->prefix .  "posts WHERE post_status = \"publish\" AND post_type = \"" . $post_type . "\" AND post_author=\"".$user_id."\"";
	$queryresults = $wpdb->get_var($query);

	return $queryresults;
}

function vh_get_user_listing_id( $user_id, $post_type = 'gd_place' ) {
	global $wpdb;

	$query = "SELECT ID FROM " . $wpdb->prefix .  "posts WHERE post_status = \"publish\" AND post_type = \"" . $post_type . "\" AND post_author=\"".$user_id."\"";
	$queryresults = $wpdb->get_results($query);

	$author_listings = '';

	foreach ($queryresults as $listing) {
		$author_listings .= $listing->ID.',';
	}

	$author_listings = trim($author_listings, ',');

	return $author_listings;
}

add_action( 'vh_action_get_listing_when_options', 'vh_get_listing_when_options' );
function vh_get_listing_when_options() {
	global $wpdb;
	$items = array(__("Today", "vh"), __("Tomorrow", "vh"), __("This weekend", "vh"), __("Next week", "vh"));

	$date = time();
	$date += 86400;
	if ( get_option("vh_theme_version") != "SkyDirectory" ) {
		$query = "SELECT geodir_listing_price as Price,COUNT(geodir_listing_price) as CountryNum, SUM(geodir_listing_price) as TotalPrice 
					FROM " . $wpdb->prefix .  "geodir_gd_place_detail WHERE post_status = \"publish\" AND (geodir_listing_start_date<=\"".date("Y-m-d")."\" 
						AND geodir_listing_end_date>=\"".date("Y-m-d")."\") UNION ALL SELECT geodir_listing_price as Price,COUNT(geodir_listing_price) as CountryNum, 
						SUM(geodir_listing_price) as TotalPrice FROM " . $wpdb->prefix .  "geodir_gd_place_detail WHERE post_status = \"publish\" 
						AND (geodir_listing_start_date<=\"".date('Y-m-d', $date)."\" AND geodir_listing_end_date>=\"".date('Y-m-d', $date)."\") 
						UNION ALL SELECT geodir_listing_price as Price,COUNT(geodir_listing_price) as CountryNum, SUM(geodir_listing_price) as TotalPrice 
						FROM " . $wpdb->prefix .  "geodir_gd_place_detail WHERE post_status = \"publish\" 
						AND (geodir_listing_start_date<=\"".date('Y-m-d', strtotime( "next saturday" ))."\" 
						AND geodir_listing_end_date>=\"".date('Y-m-d', strtotime( "next sunday" ))."\") 
						UNION ALL SELECT geodir_listing_price as Price,COUNT(geodir_listing_price) as CountryNum, 
						SUM(geodir_listing_price) as TotalPrice FROM " . $wpdb->prefix .  "geodir_gd_place_detail 
						WHERE post_status = \"publish\" AND (geodir_listing_start_date<=\"".date('Y-m-d', strtotime( "next monday" ))."\" 
						AND geodir_listing_end_date>=\"".date('Y-m-d', strtotime( "next monday" )+604000)."\")";
	}

	$queryresults = $wpdb->get_results($query);
	$extra_class = "";

	echo "<div class=\"search-calendar-container\">";
	echo "
	<div class=\"search-calendar-top\"></div>
	<div class=\"search-calendar-bottom\"></div>";
	echo "
	<div class=\"search-calendar-options\">";
		foreach ($items as $key => $value) {
			if ( $key == 0 ) {
				$extra_class = " today";
			} elseif ( $key == 1 ) {
				$extra_class = " tomorrow";
			} elseif ( $key == 2 ) {
				$extra_class = " this_weekend";
			} elseif ( $key == 3 ) {
				$extra_class = " next_week";
			}
			echo "
			<div class=\"calendar-search-item".$extra_class."\">";
				if ( $key == 0 ) {
					echo "
					<span class=\"calendar-item\">" . $value . "</span>";
					if ( $queryresults["0"]->CountryNum != '0' ) {
						echo "<span class=\"calendar-item-text\">".$queryresults["0"]->CountryNum." " . __("properties, from", "vh") . " " . get_option('vh_currency_symbol') . $queryresults["0"]->Price . " " . _e("per night", "vh") . "</span>";
					} else {
						echo "<span class=\"calendar-item-text\">".__("No properties listed", "vh")."</span>";
					}
					echo "<input type=\"hidden\" class=\"date-value\" value=\"".esc_attr(date("Y-m-d"))."\">";
				} elseif ( $key == 1 ) {
					echo "
					<span class=\"calendar-item\">" . $value . "</span>";
					if ( $queryresults["1"]->CountryNum != '0' ) {
						echo "<span class=\"calendar-item-text\">".$queryresults["1"]->CountryNum." " . __("properties, from", "vh") . " " . get_option('vh_currency_symbol') . $queryresults["1"]->Price . " " . _e("per night", "vh") . "</span>";
					} else {
						echo "<span class=\"calendar-item-text\">".__("No properties listed", "vh")."</span>";
					}
					echo "<input type=\"hidden\" class=\"date-value\" value=\"".esc_attr(date("Y-m-d", time()+86400))."\">";
				} elseif ( $key == 2 ) {
					echo "
					<span class=\"calendar-item\">" . $value . "</span>";
					if ( $queryresults["2"]->CountryNum != '0' ) {
						echo "<span class=\"calendar-item-text\">".$queryresults["2"]->CountryNum." " . __("properties, from", "vh") . " " . get_option('vh_currency_symbol') . $queryresults["2"]->Price . " " . _e("per night", "vh") . "</span>";
					} else {
						echo "<span class=\"calendar-item-text\">".__("No properties listed", "vh")."</span>";
					}
					echo "<input type=\"hidden\" class=\"date-value\" value=\"".esc_attr(date('Y-m-d', strtotime( "next saturday" )))."/".esc_attr(date('Y-m-d', strtotime( "next sunday" )))."\">";
				} elseif ( $key == 3 ) {
					echo "
					<span class=\"calendar-item\">" . $value . "</span>";
					if ( $queryresults["3"]->CountryNum != '0' ) {
						echo "<span class=\"calendar-item-text\">".$queryresults["3"]->CountryNum." " . __("properties, from", "vh") . " " . get_option('vh_currency_symbol') . $queryresults["3"]->Price . " " . _e("per night", "vh") . "</span>";
					} else {
						echo "<span class=\"calendar-item-text\">".__("No properties listed", "vh")."</span>";
					}
					echo "<input type=\"hidden\" class=\"date-value\" value=\"".esc_attr(date('Y-m-d', strtotime( "next monday" )))."/".esc_attr(date('Y-m-d', strtotime( "next monday" )+86400))."/".esc_attr(date('Y-m-d', strtotime( "next monday" )+172800))."/".esc_attr(date('Y-m-d', strtotime( "next monday" )+259200))."/".esc_attr(date('Y-m-d', strtotime( "next monday" )+345600))."/".esc_attr(date('Y-m-d', strtotime( "next monday" )+432000))."/".esc_attr(date('Y-m-d', strtotime( "next monday" )+518400))."\">";
				}
				echo "
				<div class=\"clearfix\"></div>
			</div>";
		}
	echo "
	</div>";
	echo "<div class=\"clearfix\"></div></div>";
}

add_action( 'wp_ajax_nopriv_geodir_search_markers', 'get_geodir_search_markers' );
add_action( 'wp_ajax_geodir_search_markers', 'get_geodir_search_markers' );
function get_geodir_search_markers( $return_data = null ) {
	if ( isset( $_POST['listing_date'] ) ? $listing_date = sanitize_text_field( $_POST['listing_date'] ) : $listing_date = '' );
	if ( isset( $_POST['listing_price'] ) ? $listing_price = sanitize_text_field( $_POST['listing_price'] ) : $listing_price = '' );
	if ( isset( $_POST['listing_guests'] ) ? $listing_guests = sanitize_text_field( $_POST['listing_guests'] ) : $listing_guests = '' );
	if ( isset( $_POST['listing_bedrooms'] ) ? $listing_bedrooms = sanitize_text_field( $_POST['listing_bedrooms'] ) : $listing_bedrooms = '' );
	if ( isset( $_POST['listing_beds'] ) ? $listing_beds = sanitize_text_field( $_POST['listing_beds'] ) : $listing_beds = '' );
	if ( isset( $_POST['search_type'] ) ? $search_type = sanitize_text_field( $_POST['search_type'] ) : $search_type = '' );
	if ( isset( $_POST['search_category'] ) ? $search_category = sanitize_text_field( $_POST['search_category'] ) : $search_category = '' );
	if ( isset( $_POST['return_data'] ) ? $return_type = sanitize_text_field( $_POST['return_data'] ) : $return_type = '' );
	if ( isset( $_POST['search_keyword'] ) ? $search_keyword = sanitize_text_field( urldecode( $_POST['search_keyword'] ) ) : $search_keyword = '' );
	if ( isset( $_POST['listing_search_cat'] ) ? $listing_search_cat = sanitize_text_field( $_POST['listing_search_cat'] ) : $listing_search_cat = '' );
	if ( isset( $_POST['search_location'] ) ? $search_location = sanitize_text_field( urldecode( $_POST['search_location'] ) ) : $search_location = '' );
	if ( isset( $_POST['listing_contract'] ) ? $listing_contract = sanitize_text_field( $_POST['listing_contract'] ) : $listing_contract = '' );
	if ( isset( $_POST['post_type'] ) ? $post_type = sanitize_text_field( $_POST['post_type'] ) : $post_type = 'gd_place' );
	if ( isset( $_POST['search_lat'] ) ? $search_lat = sanitize_text_field( $_POST['search_lat'] ) : $search_lat = '' );
	if ( isset( $_POST['search_long'] ) ? $search_long = sanitize_text_field( $_POST['search_long'] ) : $search_long = '' );
	if ( isset( $_POST['post_id'] ) ? $post_id = sanitize_text_field( $_POST['post_id'] ) : $post_id = '' );
	
	global $post, $wpdb, $wp_query;

	$word_limit = get_option('geodir_search_word_limit', '0');
	if ( (int)$word_limit > 0 ) {
		$search_keyword = preg_replace('~\b[A-ZA-zΑ]{1,'.$word_limit.'}\b\s*~', '', $search_keyword);
		$search_location = preg_replace('~\b[A-ZA-zΑ]{1,'.$word_limit.'}\b\s*~', '', $search_location);
	}

	$markers = array();
	$loop_count = 1;
	$all_markers = array();
	$output = '';
	$price_max = 0;
	$price_min = 9999999999999;
	$property_count = 0;
	$property_ids = '';
	
	$post_category      = $post_type.'category';

	if ( $post_type == '' || $post_type == 'undefined' ) {
		$post_type = 'gd_place';
	}

	$exclude_cat = get_option('geodir_exclude_cat_on_map', array());

	$price_where = $guests_where = $bedrooms_where = $beds_where = $keyword_where = $listing_search_category_where = $location_where = $contract_where = $date_where = $distance_where = $location_page_where = $category_where = '';

	if ( $listing_price != "" && $listing_price != 'undefined' ) {
		$listing_price = str_replace(get_option('vh_currency_symbol'), "", $listing_price);
		$listing_price = str_replace(" per night", "", $listing_price);
		$listing_price = explode("-", $listing_price);
		if($_COOKIE['C_CURRENCY'] == 'JPY'){
			$listing_price["0"]=convertCurrency($listing_price["0"],"JPY","USD");
			$listing_price["1"]=convertCurrency($listing_price["1"],"JPY","USD");
		}
		
		$price_where = " AND (geodir_listing_price>=\"".$listing_price["0"]."\" && geodir_listing_price<=\"".$listing_price["1"]."\")";//jino
		
	
	}

	if ( $listing_guests != "" && $listing_guests != "undefined" ) {
		$listing_guests = str_replace(" guests", "", $listing_guests);
		$listing_guests = explode("-", $listing_guests);
		
		if ( empty($listing_guests["1"]) ) {
			$guests_where = " AND geodir_listing_guest_count>=\"".$listing_guests["0"]."\"";
		} elseif ( $listing_guests["1"] == "20" ) {
			$guests_where = " AND geodir_listing_guest_count>=\"".$listing_guests["0"]."\"";
		} else {
			$guests_where = " AND (geodir_listing_guest_count>=\"".$listing_guests["0"]."\" && geodir_listing_guest_count<=\"".$listing_guests["1"]."\")";
		}
	}

	if ( $listing_bedrooms != "" && $listing_bedrooms != "undefined" ) {
		$listing_bedrooms = str_replace(" bedrooms", "", $listing_bedrooms);
		$listing_bedrooms = explode("-", $listing_bedrooms);

		if ( $listing_bedrooms["1"] == "10" ) {
			$bedrooms_where = " AND geodir_listing_bedroom_count>=\"".$listing_bedrooms["0"]."\"";
		} else {
			$bedrooms_where = " AND (geodir_listing_bedroom_count>=\"".$listing_bedrooms["0"]."\" && geodir_listing_bedroom_count<=\"".$listing_bedrooms["1"]."\")";
		}
	}

	if ( $listing_beds != "" && $listing_beds != "undefined" ) {
		$listing_beds = str_replace(" beds", "", $listing_beds);
		$listing_beds = explode("-", $listing_beds);

		if ( $listing_beds["1"] == "7" ) {
			$beds_where = " AND geodir_listing_bed_count>=\"".$listing_beds["0"]."\"";
		} else {
			$beds_where = " AND (geodir_listing_bed_count>=\"".$listing_beds["0"]."\" && geodir_listing_bed_count<=\"".$listing_beds["1"]."\")";
		}
	}

	if ( $search_keyword != "" && $search_keyword != "undefined" ) {
		if ( $post_type != 'gd_event' ) {
			$keyword_where = " AND ( post_title LIKE '%" . $search_keyword . "%' OR post_tags LIKE '%" . $search_keyword . "%' )";
		} else {
			$keyword_where = " AND ( details.post_title LIKE '%" . $search_keyword . "%' OR details.post_tags LIKE '%" . $search_keyword . "%' )";
		}
	}

	if ( ( $listing_search_cat != 'undefined' && $listing_search_cat != "" ) || ( $search_category != "" && $search_category != 'undefined' ) ) {
		if ( $listing_search_cat != "" ) {
			$searchable_category = $listing_search_cat;
		} else {
			$searchable_category = $search_category;
		}
		$searched_category = get_term_by('name', $searchable_category, $post_category);
		if ( $searched_category == false ) {
			$listing_search_category_where = " AND default_category LIKE '%-1%'";
		} else {
			$listing_search_category_where = " AND ".$post_category." LIKE '%," . $searched_category->term_id . ",%'";
		}
	}

	if ( $search_location != "" && $search_location != "undefined" ) {
		if ( $post_type != 'gd_event' ) {
			$location_where = " AND ( post_city LIKE '%" . $search_location . "%' OR post_region LIKE '%" . $search_location . "%' OR post_country LIKE '%" . $search_location . "%' OR post_address LIKE '%" . $search_location . "%' )";
		} else {
			$location_where = " AND ( details.post_city LIKE '%" . $search_location . "%' OR details.post_region LIKE '%" . $search_location . "%' OR details.post_country LIKE '%" . $search_location . "%' OR details.post_address LIKE '%" . $search_location . "%' )";
		}
	}

	if ( $listing_contract != "" && $listing_contract != "undefined" ) {
		$contract_where = " AND geodir_listing_type='".$listing_contract."'";
	}

	if ( geodir_location_page_id() == $post_id ) {
		$location_page_where = '';
		$location_data = geodir_get_current_location_terms();

		if ( isset($location_data['gd_country']) && $location_data['gd_country'] != '') {
			$location_page_where .= " AND post_locations LIKE '%,[".$location_data['gd_country']."]' ";
		}

		if ( isset($location_data['gd_city']) && $location_data['gd_city'] != '') {
			$location_page_where .= " AND post_locations LIKE '[".$location_data['gd_city']."],%' ";
		}

		if ( isset($location_data['gd_region']) && $location_data['gd_region'] != '') {
			$location_page_where .= " AND post_locations LIKE '%,[".$location_data['gd_region']."],%' ";
		}
	}

	if ( $listing_date != "" && $listing_date != "undefined" && $listing_date != "all" ) {
		if ( $post_type != 'gd_event' ) {
			if (strpos($listing_date,'~') !== false) {
				$date_array = explode('~', $listing_date);
				$starting_date = $date_array['0'];
				$ending_date = $date_array['1'];
			} else {
				$starting_date = $listing_date;
				$ending_date = $listing_date;
			}
			$date_where = " AND (UNIX_TIMESTAMP(geodir_listing_start_date)<=\"".strtotime($starting_date)."\" && UNIX_TIMESTAMP(geodir_listing_end_date)>=\"".strtotime($ending_date)."\")";
		} else {
			$event_date = date('Y-m-d', time()).' 00:00:00';
			if ( $listing_date == "today" ) {
				$date_where = " AND event.event_date = '".$event_date."' AND event.event_id = details.post_id";
			} elseif ( $listing_date == "upcoming" ) {
				$date_where = " AND UNIX_TIMESTAMP(event.event_date) >= '".strtotime($event_date)."' AND event.event_id = details.post_id";
			} elseif ( $listing_date == "past" ) {
				$date_where = " AND UNIX_TIMESTAMP(event.event_date) <= '".strtotime($event_date)."' AND event.event_id = details.post_id";
			}
		}
	}

	$geodir_dist = get_option('geodir_search_dist');
	if ( $geodir_dist != '' && ( $search_lat != '' && $search_lat != null && $search_lat != 'null' && $search_lat != 'undefined' ) && ( $search_long != '' && $search_long != null && $search_long != 'null' && $search_long != 'undefined' ) ) {
		$DistanceRadius = geodir_getDistanceRadius(get_option('geodir_search_dist_1'));
		if ( $post_type != 'gd_event' ) {
			$geodir_table = $wpdb->prefix .  "geodir_".$post_type."_detail";
		} else {
			$geodir_table = "details";
		}
		
		$distance_where .= " AND CONVERT((" . $DistanceRadius . " * 2 * ASIN(SQRT( POWER(SIN((ABS($search_lat) - ABS(" . $geodir_table . ".post_latitude)) * pi()/180 / 2), 2) +COS(ABS($search_lat) * pi()/180) * COS( ABS(" . $geodir_table . ".post_latitude) * pi()/180) *POWER(SIN(($search_long - " . $geodir_table . ".post_longitude) * pi()/180 / 2), 2) ))),DECIMAL(64,4)) <= " . $geodir_dist;
	}

	if ( !empty($exclude_cat) && isset($exclude_cat[$post_category]) ) {
		$category_where .= ' AND (';
		foreach ($exclude_cat[$post_category] as $key => $cat_value) {
			$category_where .= ' gd_placecategory NOT LIKE "%'.$cat_value.'%" AND';
		}
		$category_where = rtrim($category_where, 'AND');
		$category_where .= ')';
	}

	if ( $search_type == 'category' ) {
		$get_search_category = get_term_by('slug', $search_category, $post_category);
		$available_posts = '';

		$get_categories = $wpdb->prepare("SELECT object_id FROM " . $wpdb->prefix .  "term_relationships WHERE term_taxonomy_id=%s", $get_search_category->term_id);
		$get_categories_results = $wpdb->get_results($get_categories);

		foreach ($get_categories_results as $post_ids) {
			$available_posts .= $post_ids->object_id.',';
		}

		$available_posts = trim($available_posts, ',');

		$query = "SELECT post_id, post_title FROM " . $wpdb->prefix .  "geodir_".$post_type."_detail WHERE post_status = \"publish\" AND post_id IN(" . $available_posts . ")";
		$queryresults = $wpdb->get_results($query);
	} else {
		$search_values = $_POST;
		$search_query = '';

		foreach ($search_values as $search_key => $search_value) {
			// Only advanced search fields
			if ( strpos($search_key, 'geodir_') !== false || strpos($search_key, 'event') !== false ) {
				$search_value = urldecode($search_value);
				// Check for : for time inputs
				if ( strpos($search_value, ':') !== false ) {
					$search_query .= ' AND '.$search_key.' LIKE "'.sanitize_text_field($search_value).'%"';
				} elseif ( preg_match('/(?<!\d)\d{4}-\d{2}-\d{2}(?!\d)/', $search_value) ) {
					if ( strpos($search_value,'~') !== false ) {
						$date_array = explode('~', $search_value);
						$starting_date = $date_array['0'];
						$ending_date = $date_array['1'];
					} else {
						$starting_date = $search_value;
						$ending_date = $search_value;
					}

					if ( $post_type != 'gd_event' ) {
						$search_query .= " AND (".$search_key." >= \"".$starting_date."\" && ".$search_key." <= \"".$ending_date."\")";
					} else {
						$search_query .= " AND recurring_dates LIKE '%" . $starting_date . "%'";
					}
				} else {
					$search_query .= ' AND '.$search_key.' LIKE "%'.sanitize_text_field($search_value).'%"';
				}
			}
		}
		if ( $search_query != '' ) {
			$query = "SELECT post_id, post_title FROM " . $wpdb->prefix .  "geodir_".$post_type."_detail WHERE post_status = \"publish\"".$search_query.$price_where.$bedrooms_where.$beds_where.$guests_where.$distance_where.$location_page_where.$category_where;
		} elseif ( $post_type == 'gd_event' ) {
			$query = "SELECT details.post_id, details.post_title FROM " . $wpdb->prefix .  "geodir_".$post_type."_detail as details, " . $wpdb->prefix . "geodir_event_schedule as event WHERE details.post_status = \"publish\"".$date_where.$keyword_where.$location_where.$location_page_where.$distance_where.$category_where.' GROUP BY details.post_id';
		} elseif ( get_option("vh_theme_version") == "SkyDirectory" ) {
			$query = "SELECT post_id, post_title, is_featured, post_tags, post_city, post_country, geodir_contact, default_category FROM " . $wpdb->prefix .  "geodir_".$post_type."_detail WHERE post_status = \"publish\"".$price_where.$bedrooms_where.$beds_where.$guests_where.$keyword_where.$listing_search_category_where.$location_where.$location_page_where.$distance_where.$category_where;
		} elseif ( get_option("vh_theme_version") == "SkyVacation" ) {
			$query = "SELECT post_id, post_title, is_featured, post_tags FROM " . $wpdb->prefix .  "geodir_".$post_type."_detail WHERE post_status = \"publish\"".$price_where.$bedrooms_where.$beds_where.$guests_where.$location_where.$location_page_where.$date_where.$distance_where.$category_where;
		} else {
			$query = "SELECT post_id, post_title, is_featured, post_tags FROM " . $wpdb->prefix .  "geodir_".$post_type."_detail WHERE post_status = \"publish\"".$price_where.$bedrooms_where.$beds_where.$guests_where.$keyword_where.$listing_search_category_where.$location_where.$location_page_where.$contract_where.$distance_where.$category_where;
		}
		
		$queryresults = $wpdb->get_results($query);
	}

	$output .= '<ul id="geodir_category_list_view_id" class="geodir_category_list_view clearfix">';
	if ( !empty($queryresults) ) {
		foreach ( $queryresults as $geodir_listing ) {
			$image_count = 0;
			if ( vh_get_listing_price( $geodir_listing->post_id, get_post_type( $geodir_listing->post_id ) ) > $price_max ) {
				$price_max = vh_get_listing_price( $geodir_listing->post_id, get_post_type( $geodir_listing->post_id ) );
			}
			if ( vh_get_listing_price( $geodir_listing->post_id, get_post_type( $geodir_listing->post_id ) ) < $price_min ) {
				$price_min = vh_get_listing_price( $geodir_listing->post_id, get_post_type( $geodir_listing->post_id ) );
			}
			################################Jino edit price outside map left side#################################
			$values =  dynamic_convert(get_post_meta($geodir_listing->post_id, 'vh_resource_id', true), $_COOKIE['C_CURRENCY'],vh_get_listing_price( $geodir_listing->post_id, get_post_type( $geodir_listing->post_id ) ));
			
			$property_count++;
			$property_ids .= $geodir_listing->post_id.",";
			$output .= '
			<li  id="post-'.$geodir_listing->post_id.'" class="clearfix geodir-gridview gridview_onehalf gd-post-gd_place" data-position='.$values['money'].'>';
				if ( get_option("vh_theme_version") != "SkyDirectory" && $post_type != 'gd_event' ) {
				
					//$output .= '<div class="map-listing-price">'.get_option('vh_currency_symbol').vh_get_listing_price( $geodir_listing->post_id, get_post_type( $geodir_listing->post_id ) ).'</div>';
				
					$output .= '<div class="map-listing-price">'.$values['sign'].''.$values['money'].'</div>';
				} elseif ( get_option("vh_theme_version") != "SkyDirectory" && $post_type == 'gd_event' ) {
					$output .= '<div class="map-listing-price">'.get_geodir_event_date( $geodir_listing->post_id ).'</div>';
				}
				if ( get_option("vh_theme_version") == "SkyDirectory" ) {
					$output .= '
					<article class="geodir-category-listing">		
						<div class="geodir-post-img">';

						$featured_image = wp_get_attachment_url( get_post_thumbnail_id( $geodir_listing->post_id ) );

						if ( $featured_image != '' ) {
							$output .= "<img src=\"".vh_imgresize($featured_image, '120', '105', true)."\" />";
						} elseif ( get_option('geodir_listing_no_img', '') ) {
							$image = vh_imgresize(get_option( 'geodir_listing_no_img', '' ), 120, 105, true);
							$output .= "<img src=\"".$image."\" />";
						}

						$output .= '
						</div>
						<div class="clearfix"></div>
					</article>';
				}
				if ( $geodir_listing->is_featured == "1" || strpos(strtolower($geodir_listing->post_tags), 'featured') !== false ) {
					$output .= '<div class="map-listing-featured">'.__('Featured', 'vh').'</div>';
				}

				if ( get_option("vh_theme_version") == "SkyDirectory" ) {
					$output .= '<div class="geodir-listing-side"><a href="' . get_permalink( $geodir_listing->post_id ) . '">';
				}

				$output .= '
				<div class="map-listing-rating">'.vh_get_listing_rating( $geodir_listing->post_id, get_post_type( $geodir_listing->post_id ) ).'</div>';

				if ( get_option("vh_theme_version") != "SkyDirectory" ) {
					$output .= '<div class="map-listing-favorite">';
					ob_start();
					geodir_favourite_html(get_current_user_id(), $geodir_listing->post_id );
					$output .= ob_get_contents();
					ob_end_clean();
					$output .= '</div>';
				}

				$output .= '
				<div class="map-listing-title">'. $geodir_listing->post_title.'</div>';

				if ( get_option("vh_theme_version") != "SkyDirectory" ) {
					$output .= '
					<a href="'.get_permalink( $geodir_listing->post_id ).'" class="wpb_button wpb_btn-primary wpb_btn-small">'. __("View", "vh").'</a>';
				}

				if ( get_option("vh_theme_version") != "SkyDirectory" ) {
					$output .= '
					<article class="geodir-category-listing">		
						<div class="geodir-post-img">';
						$post_images = geodir_get_images( $geodir_listing->post_id, "map-sized-image", '' );
						$featured_image = wp_get_attachment_url( get_post_thumbnail_id( $geodir_listing->post_id ) );

						$output .= "<div class=\"map-listing-carousel-container\">
								<ul class=\"map-listing-carousel\">";
								if ( $featured_image != '' ) {
									if ( !empty($post_images) ) {
										foreach ($post_images as $image) {
											$output .= "
											<li class=\"map-listing-item\">
												<img src=\"".vh_imgresize($image->src, '343', '296', true)."\" />
											</li>";
											$image_count++;
										}
									} else {
										$image = vh_imgresize(get_option( 'geodir_listing_no_img', '' ), 343, 296, true);
										$output .= "
										<li class=\"map-listing-item\">
											<img src=\"".vh_imgresize($featured_image, '343', '296', true)."\" />
										</li>";
										$image_count++;
									}
								} elseif ( get_option('geodir_listing_no_img', '') ) {
									$image = vh_imgresize(get_option( 'geodir_listing_no_img', '' ), 343, 296, true);
									$output .= "
										<li class=\"map-listing-item\">
											<img src=\"".$image."\" />
										</li>";
									$image_count++;
								}

							
						$output .= "</ul></div>";

						$output .= '
						</div>
					</article>';
					if ( $image_count > 1 ) {
						$output .= '
						<a href="javascript:void(0)" class="map-listing-next icon-angle-right"></a>
						<a href="javascript:void(0)" class="map-listing-prev icon-angle-left"></a>';
					}
				}

				if ( get_option("vh_theme_version") == "SkyDirectory" ) {
					$listing_category = get_term( $geodir_listing->default_category, $post_category );
					$output .= '
					<div class="geodir-listing-side-lower">
						<span class="listing-location icon-location">' . $geodir_listing->post_city . ', ' . $geodir_listing->post_country . '</span>';
						if ( $geodir_listing->geodir_contact != null ) {
							$output .= '<span class="listing-contact icon-phone-1">' . $geodir_listing->geodir_contact . '</span>';
						}
						if ( $listing_category->name != null ) {
							$output .= '<span class="listing-categories icon-folder-open">' . $listing_category->name . '</span>';
						}
					$output .= '	
					</div></a>
				</div>';
				}

			$output .= '
			</li>';
		}
	} else {
		$output .= '
		<li id="post-none" class="clearfix geodir-gridview gridview_onehalf gd-post-gd_place" style="width: 90%; text-align: center;">
			<span>'.__("No listings found which match your selection.", "geodirectory").'</span>
		</li>';
	}

	$output .= '</ul>';
	$output .= '
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			jQuery(".geodir_category_list_view li.gridview_onehalf").hover(function() {
				var marker = jQuery(this)["0"].id.split("-");
				jQuery("div[id^=\'geo-marker-\']").removeClass("hovered-list");
				jQuery("#geo-marker-"+marker["1"]+"").addClass("hovered-list");
			}, function() {
				jQuery("div[id^=\'geo-marker-\']").removeClass("hovered-list");
			});
		});
		</script>';
	$output .= '<input type="hidden" id="geodir-search-results" value="'.$property_count.'" />';

	//$map_json = get_geodir_search_markers('return');

	//jino min max price
	// $output .= "<span id=\"geodir-visible_markers\">".$map_json."</span>";
	$output .= '<img id="geodir-search-loading" src="'.get_template_directory_uri() .'/images/loading.gif">';
	$output .= '<input type="hidden" id="geodir-price-min" value="' . $price_min. '" />';
	$output .= '<input type="hidden" id="geodir-price-max" value="' . $price_max . '" />';
	$output .= '<input type="hidden" id="geodir-search-post-type" value="' . $post_type . '" />';
	
	if ( !empty($queryresults) ) {
		if ( $post_type != 'gd_event' ) {
			foreach ($queryresults as $marker_value) {
				$post_marker = get_post( $marker_value->post_id );
				$all_markers[] = $marker_value->post_id;
				$post_marker_extra = vh_get_post_info( $marker_value->post_id, get_post_type( $marker_value->post_id ) );
				$icon = array();
				if ( wp_get_attachment_image_src( get_post_thumbnail_id($marker_value->post_id), "gallery-medium" ) ) {
					$icon = wp_get_attachment_image_src( get_post_thumbnail_id($marker_value->post_id), "gallery-medium" );
				} elseif ( get_option('geodir_listing_no_img', '') ) {
					$icon = vh_imgresize(get_option( 'geodir_listing_no_img', '' ), 125, 125, true);
				} else {
					$icon['0'] = '';
				}
				if ( $loop_count == 1 ) {
					$markers[] = array("totalcount"=>(string)count($queryresults),"id"=>$post_marker->ID,"t"=>$post_marker->post_title,"lt"=>$post_marker_extra["0"]->post_latitude,"ln"=>$post_marker_extra["0"]->post_longitude,"mk_id"=>$post_marker->ID.'_'.$post_marker_extra["0"]->default_category,"i"=>$icon["0"]);
				} else {
					$markers[] = array("id"=>$post_marker->ID,"t"=>$post_marker->post_title,"lt"=>$post_marker_extra["0"]->post_latitude,"ln"=>$post_marker_extra["0"]->post_longitude,"mk_id"=>$post_marker->ID.'_'.$post_marker_extra["0"]->default_category,"i"=>$icon["0"]);
				}
				$loop_count++;
			}
		} else {
			$available_events = array();
			foreach ($queryresults as $event_value) {
				$available_events[] = $event_value->post_id;
				$all_markers[] = $event_value->post_id;
			}
			$available_events = implode(',', $available_events);

			$event_date = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'geodir_gd_event_detail WHERE post_id IN ('.$available_events.')');
			foreach ($event_date as $event_date_key => $event_date_value) {
				$icon = array();
				if ( wp_get_attachment_image_src( get_post_thumbnail_id($event_date_value->post_id), "gallery-medium" ) ) {
					$icon = wp_get_attachment_image_src( get_post_thumbnail_id($event_date_value->post_id), "gallery-medium" );
				} elseif ( get_option('geodir_listing_no_img', '') ) {
					$image = vh_imgresize(get_option( 'geodir_listing_no_img', '' ), 125, 125, true);
					$icon['0'] = $image;
				}
				if ( $loop_count == 1 ) {
					$markers[] = array("totalcount"=>(string)count($event_date),"id"=>$event_date_value->post_id,"t"=>$event_date_value->post_title,"lt"=>$event_date_value->post_latitude,"ln"=>$event_date_value->post_longitude,"mk_id"=>$event_date_value->post_id.'_'.$event_date_value->default_category,"i"=>$icon["0"]);
				} else {
					$markers[] = array("id"=>$event_date_value->post_id,"t"=>$event_date_value->post_title,"lt"=>$event_date_value->post_latitude,"ln"=>$event_date_value->post_longitude,"mk_id"=>$event_date_value->post_id.'_'.$event_date_value->default_category,"i"=>$icon["0"]);
				}
				$loop_count++;
			}
		}
		$markers['all_markers'] = $output;
	} else {
		$markers[] = array("totalcount"=>"0");
		$markers['all_markers'] = $output;
	}
		

	if ( $return_data == null ) {
		echo json_encode($markers);
	} else {
	//	echo json_encode($query);
		return json_encode($markers);
	}
	
	die(1);
}

remove_filter('comment_text', 'geodir_wrap_comment_text',10,2);
// add_filter('comment_text', 'vh_geodir_wrap_comment_text',9,2);
// function vh_geodir_wrap_comment_text($content,$comment=''){
// 		if( !is_admin() ){
// 			return $content;
// 		} else {
// 			return 	$content;
// 		}
// }

remove_action("comments_template",'geodir_reviewrating_show_post_ratings',10);
if ( get_option('geodir_reviewrating_enable_sorting', '0') ) {
	add_action("vh_comment_sorting",'vh_geodir_reviewrating_show_post_ratings',10);
}
function vh_geodir_reviewrating_show_post_ratings(){
	global $post,$geodir_post_type;

	$all_postypes = geodir_get_posttypes();

	if(!in_array($geodir_post_type, $all_postypes))
		return false;

	if(isset($_REQUEST['comment_sorting'])){?>
	
			<script type="text/javascript">
				
				jQuery(document).ready(function(){
					
					jQuery('#gd-tabs dl dd').removeClass('geodir-tab-active');
					
					jQuery('#gd-tabs dl dd').find('a').each(function(){
					
						if(jQuery(this).attr('data-tab') == '#reviews')
							jQuery(this).closest('dd').addClass('geodir-tab-active');
						
					});
					
				});
				
			</script> <?php		
	}
	global $post;
	if(!isset($post->ID)){return;}
	$post_link = get_permalink($post->id);

	$comment_shorting_form_field_val = array(  'latest' => 'Latest',
											   'oldest' => 'Oldest', 
											   'low_rating' => 'Lowest Rating',
											   'high_rating' => 'Highest Rating'
											 );
	
	$comment_shorting_form_field_val = apply_filters( 'geodir_reviews_rating_comment_shorting', $comment_shorting_form_field_val );
   ?>
   <form name="comment_shorting_form" id="comment_sorting_form" method="get" action="<?php echo $post_link; ?>">
   <?php
	
	 	$query_variables = $_GET;
		
		$hidden_vars = '';
		if(!empty($query_variables)){
			
			foreach($query_variables as $key => $val){
				
				if( $key != 'comment_sorting')
					$hidden_vars .= '<input type="hidden" name="'.$key.'" value="'.$val.'" />';
			}
		}
		
		echo $hidden_vars;
	?>
	
	<select name="comment_sorting" class="comment_sorting" onchange="jQuery(this).closest('#comment_sorting_form').submit()">
    
    <?php
	if(isset($comment_shorting_form_field_val) && !empty($comment_shorting_form_field_val))
			foreach($comment_shorting_form_field_val as $key => $value) {
	?>
	<option <?php if(isset($_REQUEST['comment_sorting']) && $_REQUEST['comment_sorting'] ==  $key){echo 'selected="selected"';} ?> value="<?php echo $key; ?>"><?php _e($value, GEODIRREVIEWRATING_TEXTDOMAIN);?></option>
            <?php
	       }
	?>
	</select>
  </form>
    <?php
}

remove_filter('comment_text', 'geodir_reviewrating_wrap_comment_text',42,2);

if( get_option('geodir_reviewrating_enable_rating') ){
	add_filter('comment_text', 'vh_geodir_reviewrating_wrap_comment_text',12,3);
	function vh_geodir_reviewrating_wrap_comment_text($content,$comment=''){
		global $geodir_post_type;
		$all_postypes = geodir_get_posttypes();

		if(!in_array($geodir_post_type, $all_postypes))
			return $content;
			
		$like_unlike = '';
		
		if(!empty($comment) && !is_admin() && !$comment->comment_parent){
			if (get_option('geodir_reviewrating_enable_rating')) {
				if ( function_exists( 'geodir_reviewrating_get_comment_rating_by_id' ) ) {
					$comment_ratings = geodir_reviewrating_get_comment_rating_by_id($comment->comment_ID);
					$comment_rating_overall = isset($comment_ratings->overall_rating) ? $comment_ratings->overall_rating : '';
					$overall_html = geodir_reviewrating_draw_overall_rating($comment_rating_overall);
					$ratings = @unserialize($comment_ratings->ratings);
				}
			};
			
			if(!is_admin() && function_exists( 'geodir_reviewrating_draw_ratings' ) ) {
				$ratings_html = geodir_reviewrating_draw_ratings($ratings);
				$comment_images = geodir_reviewrating_get_comment_images($comment->comment_ID);
			}	
			
			$images_show_hide = '';
			$comment_images_display = '';
			
			if(get_option('geodir_reviewrating_enable_images')) {
						
				$total_images = 0;
				if(isset($comment_images->images) && $comment_images->images != '') {
					$total_images = explode(',',$comment_images->images);
				}
				// open lightbox on click
				$div_click = (int)get_option( 'geodir_disable_gb_modal' ) != 1 ? 'div.place-gallery' : 'div.overall-more-rating';
				$onclick = !empty($comment_images) && count($total_images)>0 ? 'onclick="javascript:jQuery(this).closest(\'.gdreview_section\').find(\''.$div_click.' a:first\').trigger(\'click\');"' : '';
				
				$images_show_hide = '<div class="showcommentimages" comment_id="'.$comment->comment_ID.'" '.$onclick.' ><i class="fa fa-camera"></i> <a href="javascript:void(0);">';

				if (empty($comment_images) || count($total_images) == 0) {
					$images_show_hide .= __('No Photo', 'vh');
				} elseif (count($total_images) == 1) {
					$images_show_hide .= sprintf(__('%d Photo', 'vh'), 1);
				} else {
					$images_show_hide .= sprintf(__('%d Photos', 'vh'), (int)count($total_images));
				}

				$images_show_hide .= '</a></div>';

				$comment_images_display = $images_show_hide;
				
			};
			
			if(get_option('geodir_reviewrating_enable_rating')) {
				// $overallrating_html = '<div class="comment_overall"><span>'.$overall_html.'</span></div>';
				$rating_html = $ratings_html;
			};
			
			if(get_option('geodir_reviewrating_enable_review') && !is_admin()) {
			
			};
			
			ob_start(); ?>

			<div class="gdreview_section">
				<?php if ( isset($comment_images->html) || $rating_html != '' ) {
					$extra_class1 = $extra_class2 = '';
					if ( $rating_html != '' ) {
						$extra_class1 = ' style="width: 35%; float: left;"';
						$extra_class2 = ' style="width: 65%; float: left;"';
					} ?>
				<div class="comment_more_ratings clearfix">
					<?php echo '<div '.$extra_class1.'>'.$rating_html.'</div>'; ?>
					<?php echo '<div '.$extra_class2.'>'.$comment_images->html.'</div>'; ?>
				</div>
				<?php } ?>
			</div>
			<div class="description"><?php echo get_comment_text( $comment->comment_ID );?></div>
			
			 
			<?php $content = ob_get_clean();
			
			return $content;
		} else {
			return 	$content;
		}
		
	}
}

if ( function_exists('geodir_get_default_package') ) {
	remove_action('geodir_before_detail_fields' , 'geodir_build_payment_list', 1);
	add_action('geodir_before_detail_fields' , 'vh_geodir_build_payment_list', 11);
	function vh_geodir_build_payment_list(){
		
		global $post, $package_id;
		
		$listing_type = isset($_REQUEST['listing_type']) ? $_REQUEST['listing_type'] : '';
		
		if(empty($listing_type)){
		
			$listing_type = $post->post_type;
		}
		
		if(isset($_REQUEST['package_id'])){
			
			$package_id = $_REQUEST['package_id'];
			
		}elseif(isset($post->package_id) && $post->package_id != ''){
			
			$listing_type = $post->post_type;
			$package_id = $post->package_id;
			
		}else{
			
			$default_package = geodir_get_default_package($listing_type);
			$package_id = $default_package->pid;
			
		}
		
		$package_info = geodir_get_package_info($package_id);
		
		$package_list_info = geodir_package_list_info($listing_type);
			
		
		$postlink = get_permalink( get_option('geodir_add_listing_page') );
		$postlink = geodir_getlink($postlink,array('listing_type'=>$listing_type),false);
		
		if(isset($_REQUEST['pid']) && $_REQUEST['pid'] != ''){
			$postlink = geodir_getlink($postlink,array('pid'=>$_REQUEST['pid']),false);
		}
		
		echo '<div class="geodir_price_package_row geodir_form_row clearfix ">';
		
		

		if(isset($_REQUEST['package_id']) || (!isset($_REQUEST['pid']) || $_REQUEST['pid'] == '' || isset($_REQUEST['upgrade'] ))){

		foreach($package_list_info as $pkg){ 
			
			$alive_days = 'unlimited';
			$post_pkg_link = '';
			if($pkg->days)
			$alive_days = $pkg->days;

			if ( defined('ICL_LANGUAGE_CODE') ) {
				$lang_code = ICL_LANGUAGE_CODE;
			} else {
				$lang_code = '';
			}

			$post_pkg_link = geodir_getlink($postlink,array('package_id'=>$pkg->pid, 'lang'=>$lang_code),false);
			
			?>

			<?php
			$price = explode('.', $pkg->amount);
			if ( $price['0'] == 0 ) {
				$extra_class = 'free';
			} else {
				$extra_class = 'money';
			}
			?>
				
			<div id="geodir_price_package_<?php echo $pkg->pid; ?>" class="geodir_form_row clear_both geodir_package <?php echo $extra_class; ?>" align="center" style="padding:2px;">
				<?php if ( $price['0'] != 0 ) { ?>
					<span class="add-listing-submit-price"><span><?php echo get_option('geodir_currencysym', '$'); ?></span><?php _e(stripslashes($price['0']), GEODIRPAYMENT_TEXTDOMAIN); ?></span>
				<?php } ?>
				<span class="addlisting-submit-line"></span>
				<span class="addlisting-submit-title"><?php _e(stripslashes($pkg->title), GEODIRPAYMENT_TEXTDOMAIN); ?></span>
				<span class="addlisting-submit-body"><span><?php _e(stripslashes($pkg->title_desc), GEODIRPAYMENT_TEXTDOMAIN); ?></span></span>
				<div class="button_container">
					<input type="button" value="<?php _e('Select', 'vh'); ?>" class="geodir_button wpb_button wpb_btn-warning wpb_btn-small geodir-select-package">
					<input name="package_id" type="radio" value="<?php echo $pkg->pid;?>"  <?php if($package_id == $pkg->pid) echo 'checked="checked"';?> onclick="window.location.href='<?php echo $post_pkg_link;?>'" style="display: none">
					<div class="clearfix"></div>
				</div>
			</div>
			
			<?php }
		}
		
		echo '</div>';	
	}

	remove_action('geodir_before_detail_fields' , 'geodir_build_coupon', 2);
	add_action('geodir_before_detail_fields' , 'vh_geodir_build_coupon', 23);
	function vh_geodir_build_coupon(){

		global $post;
		
		$listing_type = isset($_REQUEST['listing_type']) ? $_REQUEST['listing_type'] : '';
		
		if(empty($listing_type) && (isset($_REQUEST['pid']) || $_REQUEST['pid'] != '') )
			$listing_type = get_post_type( $_REQUEST['pid'] );
		
		
		if(get_option('geodir_allow_coupon_code') && geodir_is_valid_coupon($listing_type) && ((!isset($_REQUEST['pid']) || $_REQUEST['pid'] == '') || (isset($_REQUEST['package_id']) && $_REQUEST['package_id']!= ''))){
				
				$coupon_code = isset($post->coupon_code) ? $post->coupon_code : '';
				
			?>

			<div id="geodir_coupon_code_row" class="geodir_form_row clearfix" >
					 <input name="coupon_code" id="coupon_code" value="<?php echo esc_attr(stripslashes($coupon_code)); ?>" type="text" class="geodir_textfield" maxlength="100" placeholder="<?php echo PRO_ADD_COUPON_TEXT; ?>" />
					 <span class="geodir_message_note"><?php echo _e(COUPON_NOTE_TEXT,'geodirectory'); ?></span>
			</div><?php
		}

	}

	remove_action( 'geodir_publish_listing_form_before_msg', 'geodir_publish_payment_listing_form_before_msg', 1 );
	add_action( 'geodir_publish_listing_form_before_msg', 'vh_geodir_publish_payment_listing_form_before_msg', 2 );
	function vh_geodir_publish_payment_listing_form_before_msg() {
		global $post, $wpdb;
		
		$post_type = $post->listing_type;
		
		if( isset( $_REQUEST['package_id'] ) && $_REQUEST['package_id'] != '' ) {
			$package_price_info = geodir_get_post_package_info( $_REQUEST['package_id'] );
		} else {
			if( !empty( $post ) && isset( $post->package_id ) ) {
				$package_price_info = geodir_get_post_package_info( $post->package_id );
			}
		}
		
		$package_id = isset($package_price_info['pid']) ? $package_price_info['pid'] : '';
		$payable_amount = isset($package_price_info['amount']) ? $package_price_info['amount'] : 0;
		$alive_days = isset($package_price_info['days']) ? $package_price_info['days'] : 0;
		$type_title = isset($package_price_info['title']) ? $package_price_info['title'] : '';
		$sub_active = isset($package_price_info['sub_active']) ? $package_price_info['sub_active'] : '';
		
		if ( $sub_active ) {
			$sub_units_num_var = $package_price_info['sub_units_num'];
			$sub_units_var = $package_price_info['sub_units'];
			$alive_days = geodir_payment_get_units_to_days( $sub_units_num_var, $sub_units_var );
			
			// paypal free trial
			$sub_num_trial_days_var = $package_price_info['sub_num_trial_days'];
			$sub_num_trial_units_var = $package_price_info['sub_num_trial_units'];
			if ( $sub_num_trial_days_var > 0 ) {
				$alive_days = geodir_payment_get_units_to_days( $sub_num_trial_days_var, $sub_num_trial_units_var );
			}
		}
		
		$org_payable_amount = $payable_amount;
		
		/* -------- START LISTING FORM MESSAGE*/
		
		ob_start();
		if(isset($_REQUEST['coupon_code']) && $_REQUEST['coupon_code']!='') {
			if(geodir_is_valid_coupon($post_type, $_REQUEST['coupon_code'])) {
				$payable_amount = geodir_get_payable_amount_with_coupon($payable_amount,$_REQUEST['coupon_code']);
			} else {
				echo '<p class="error_msg_fix">'. WRONG_COUPON_MSG.'</p>';
			}
		}
		
			if($payable_amount > 0){ 
						
					if($alive_days==0){$alive_days = UNLIMITED;}
					echo
					'<div class="wpb_alert wpb_content_element vc_alert_square wpb_alert-info">
						<div class="messagebox_text">
							<p>';
								printf(GOING_TO_PAY_MSG, geodir_get_currency_sym().$payable_amount,$alive_days,$type_title);
							echo
							'</p>
						</div>
					</div>';
			
			}else{
				
					if($alive_days==0){$alive_days = UNLIMITED;}
					
					echo
					'<div class="wpb_alert wpb_content_element vc_alert_square wpb_alert-info">
						<div class="messagebox_text">
							<p>';
								if(!isset($_REQUEST['pid']) || $_REQUEST['pid']=='')
									printf(GOING_TO_FREE_MSG, $type_title,$alive_days);
								else
									printf(GOING_TO_UPDATE_MSG, geodir_get_currency_sym().$payable_amount,$alive_days,$type_title);
							echo
							'</p>
						</div>
					</div>';

			}
		
		 echo $form_message = ob_get_clean();
		 /* -------- END LISTING FORM MESSAGE*/
		 
		 
		 /* -------- START LISTING FORM PAYMENT OPTIONS*/
		 ob_start();
			
			?>
			<input type="hidden" name="price_select" value="<?php if(isset($package_id)){ echo $package_id;}?>" />
			<input type="hidden" name="coupon_code" value="<?php if(isset($_REQUEST['coupon_code'])){ echo $_REQUEST['coupon_code'];}?>" />
			<?php	
			
		 if($payable_amount > 0){
		
			if($sub_active){
				$sub_m_arr = apply_filters( 'geodir_subscription_methods', array('payment_method_paypal') );
				$method_names = implode(',', array_fill(0, count($sub_m_arr), '%s'));
				
				$paymentsql = $wpdb->prepare("select * from $wpdb->options where option_name in ($method_names) order by option_id", $sub_m_arr);
				
			} else {
				
				$paymentsql = $wpdb->prepare("select * from $wpdb->options where option_name like %s order by option_id", array('payment_method_%'));
			}
			
			$paymentinfo = $wpdb->get_results($paymentsql);
			
			if($paymentinfo){?>
				
				<h3 class="geodir_payment_head"> <?php echo SELECT_PAY_MEHTOD_TEXT; ?></h3>
				<ul class="geodir_payment_method">
				
				<?php
				
				$paymentOptionArray = array();
				$paymethodKeyarray = array();
				
				foreach($paymentinfo as $paymentinfoObj){
					
					$paymentInfo = unserialize($paymentinfoObj->option_value);
					if($paymentInfo['isactive']){
						$paymethodKeyarray[] = $paymentInfo['key'];
						$paymentOptionArray[$paymentInfo['display_order']][] = $paymentInfo;
					}
					
				}
				
				ksort($paymentOptionArray);
				
				if($paymentOptionArray){
					
					foreach($paymentOptionArray as $key=>$paymentInfoval){
					
						for($i=0;$i<count($paymentInfoval);$i++){
						
							$paymentInfo = $paymentInfoval[$i];
							$jsfunction = 'onclick="showoptions(this.value);"';
							
							$chked = '';
							$checkbox = '';
							if($key==1) {
								$chked = 'checked="checked"';
								$checkbox = 'checked';
							}
							
							?><li id="<?php echo $paymentInfo['key'];?>">
								<span class="radiobutton <?php echo $checkbox; ?>"></span>
								<input <?php echo $jsfunction;?>  type="radio" value="<?php echo $paymentInfo['key'];?>" id="<?php echo $paymentInfo['key'];?>_id" name="paymentmethod" <?php echo $chked;?> />
								<span class="input-side-text"> 
									<?php echo $paymentInfo['name']?>
								</span>
								<?php 
								 if(file_exists(GEODIR_PAYMENT_MANAGER_PATH.$paymentInfo['key'].'/'.$paymentInfo['key'].'.php')) 
										include_once(GEODIR_PAYMENT_MANAGER_PATH.$paymentInfo['key'].'/'.$paymentInfo['key'].'.php');	?> 
							</li><?php
						}
					}
					
					if(isset($paymethodKeyarray)){
			?>
				<script type="application/x-javascript">
				
				
				
				jQuery(document).ready(function($){
		
					var submit_button = $('#publish_listing .geodir_publish_button');
					submit_button.on('click', function(event){
			
					var payment_method 	= $('#publish_listing input[name="paymentmethod"]:checked').val();
					
					if( payment_method === 'authorizenet' ) {
						
						if($('#cardholder_name').val()==''){
							alert('<?php  _e('Please enter Cardholder name', GEODIRPAYMENT_TEXTDOMAIN);?>');return false;
						}
						
						if($('#cc_type').val()==''){
							alert('<?php  _e('Please select card type', GEODIRPAYMENT_TEXTDOMAIN);?>');return false;
						}
						
						if($('#cc_number').val()==''){
							alert('<?php  _e('Please enter card number', GEODIRPAYMENT_TEXTDOMAIN);?>');return false;
						}
						
						if($('#cc_month').val()=='' || $('#cc_year').val()=='' ){
							alert('<?php  _e('Please enter expire date', GEODIRPAYMENT_TEXTDOMAIN);?>');return false;
						}
						
						return true;
						
					}else { 
						return true;
					}
			
				  });
				});
										   
										   
								function showoptions(paymethod){
						<?php for($i=0;$i<count($paymethodKeyarray);$i++) { ?>
		
								showoptvar = '<?php echo $paymethodKeyarray[$i]?>options';
								if(eval(document.getElementById(showoptvar)))
								{
									document.getElementById(showoptvar).style.display = 'none';
									if(paymethod=='<?php echo $paymethodKeyarray[$i]?>')
									{ document.getElementById(showoptvar).style.display = ''; }
								}
							
							<?php }	?>
					}
					
								<?php for($i=0;$i<count($paymethodKeyarray);$i++) { ?>
							if(document.getElementById('<?php echo $paymethodKeyarray[$i];?>_id').checked)
							{ showoptions(document.getElementById('<?php echo $paymethodKeyarray[$i];?>_id').value);}
								<?php }	?>
				</script>  
				 
				<?php }
					
				}else{?><li><?php echo NO_PAYMENT_METHOD_MSG;?></li>
					<?php }?>
				</ul>
				<?php
			}
		
		}
		
		echo $html = ob_get_clean();
		
		/* -------- END LISTING FORM PAYMENT OPTIONS*/
		
		
		/* -------- START LISTING FORM BUTTON*/
		
		ob_start();
		
		if((!isset($_REQUEST['pid']) || $_REQUEST['pid']=='') && $payable_amount == 0){
			
			?> <input type="submit" name="Submit and Pay" value="<?php echo PRO_SUBMIT_BUTTON;?>" class="geodir_button geodir_publish_button" /><?php
			
		}elseif((isset($_REQUEST['pid']) && $_REQUEST['pid']!='') && $payable_amount == 0){
		
			?> <input type="submit" name="Submit and Pay" value="<?php echo PRO_UPDATE_BUTTON;?>" class="geodir_button geodir_publish_button" /><?php
			
		}elseif((isset($_REQUEST['package_id']) && $_REQUEST['package_id'] != '') && $payable_amount > 0 && (!isset($_REQUEST['pid']) || $_REQUEST['pid']=='')){
			
			?><input type="submit" name="Submit and Pay" value="<?php echo PRO_SUBMIT_PAY_BUTTON;?>" class=" geodir_button geodir_publish_button" /><?php
			
		}elseif(isset($_REQUEST['package_id']) && $_REQUEST['package_id'] != '' && $org_payable_amount > 0 && (isset($_REQUEST['pid']) || $_REQUEST['pid']!='')){
			
			$post_status = get_post_status( $_REQUEST['pid'] );
			
			if($post_status == 'draft'){
				?><input type="submit" name="Submit and Pay" value="<?php echo PRO_RENEW_BUTTON;?>" class="geodir_button geodir_publish_button" /><?php
			}else{
				?><input type="submit" name="Submit and Pay" value="<?php echo PRO_UPGRADE_BUTTON;?>" class="geodir_button geodir_publish_button" /><?php
			}
			
		}
		
		echo $listing_form_button = ob_get_clean();
		
		/* -------- END LISTING FORM BUTTON*/
		
		
		
		/* -------- START LISTING GO BACK LINK*/
		
		
		$post_id = '';
		if(isset($post->pid)){
			$post_id = $post->pid;
		}elseif(isset($_REQUEST['pid'])){
			$post_id = $_REQUEST['pid'];
		}
		
		$postlink = get_permalink( get_option('geodir_add_listing_page') );
		
		$postlink = geodir_getlink($postlink,array('pid'=>$post_id,'backandedit'=>'1','listing_type'=>$post_type),false);
		
		
		if(isset($_REQUEST['package_id']) && $_REQUEST['package_id'] != ''){
			
			$postlink = geodir_getlink($postlink,array('package_id'=>$_REQUEST['package_id']),false);
		}
		
		
		ob_start();	 
			?>
				<a href="<?php echo $postlink;?>" class="geodir_goback" ><?php echo PRO_BACK_AND_EDIT_TEXT;?></a>
				<input type="button" name="Cancel" value="<?php echo (PRO_CANCEL_BUTTON);?>" class="geodir_button cancle_button"  onclick="window.location.href='<?php echo geodir_get_ajax_url().'&geodir_ajax=add_listing&ajax_action=cancel&pid='.$post_id.'&listing_type='.$post_type;?>'" />
			<?php

		echo $listing_form_go_back = ob_get_clean();
		
		
		
		

	}
}

if ( function_exists('geodir_display_post_claim_link') ) {
	function vh_geodir_display_post_claim_link(){
		
		global $post, $preview;
		
		$geodir_post_type = array();

		if(get_option('geodir_post_types_claim_listing'))
			$geodir_post_type =	get_option('geodir_post_types_claim_listing');
		
		$post_id = $post->ID;
		$posttype = (isset($post->post_type)) ? $post->post_type : '';
		
		/*if(isset($_REQUEST['pid']) && $_REQUEST['pid'] != ''){
			$post_id = $_REQUEST['pid'];
			$posttype = get_post_type( $post_id );
		}*/

		if(in_array($posttype, $geodir_post_type) && !$preview)
		{
			$post_info = get_post($post_id);
			
			$author_id = $post_info->post_author;
			
			$post_type = $post_info->post_type;
			
			$user = new WP_User( $author_id );
			
			$author_role = $user->roles[0];
			
			$is_owned = geodir_get_post_meta($post_id,'claimed',true);
			
			if(get_option('geodir_claim_show_owner_varified')=='yes')
			{ 
				//if ($author_role =='author' && $is_owned != '0' )
				if ( $is_owned != '0' ) {
					echo '<span class="success_msg"><span class="icon-ok-circled"></span>'.__('Verified by owner', 'vh');
					if ( get_option('geodir_claim_show_author_link')=='yes' && !$preview ) {
						$author_link = get_author_posts_url( $author_id );
						$author_link = geodir_getlink($author_link,array('geodir_dashbord'=>'true','stype'=>$post_type ),false);
						
						echo ' <a href="'.$author_link.'">'.get_the_author_meta( 'display_name', $author_id ).'</a>';
					}
					echo '</span>';
				}
			}
			
			/*if(get_option('geodir_claim_enable')=='yes' && ($author_role =='administrator' || $is_owned=='0'))*/
			if(get_option('geodir_claim_enable')=='yes' && $is_owned=='0')
			{
				
				if ( is_user_logged_in())
				{
					echo '<input type="hidden" name="geodir_claim_popup_post_id" value="'.$post_id.'" /><div class="geodir_display_claim_popup_forms"></div><p class="edit-link"><a href="javascript:void(0);" class="vh_geodir_claim_enable">'.CLAIM_BUSINESS_OWNER.'</a></p>';
					
				}
				else
				{
					
					$site_login_url = get_option('siteurl').'?geodir_signup=true';
					echo '<p class="edit-link"><a href="'.$site_login_url.'" ><i class="fa fa-question-circle"></i> '.CLAIM_BUSINESS_OWNER.'</a></p>';
					
				}
				
				if(isset($_REQUEST['geodir_claim_request']) && $_REQUEST['geodir_claim_request']=='success')
				{
					// echo '<p class="sucess_msg">'.CLAIM_LISTING_SUCCESS.'</p>';
				}	
			}
		}
	}

	remove_action('geodir_before_main_form_fields' , 'geodir_add_claim_fields_before_main_form', 1);
	add_action('geodir_before_main_form_fields' , 'vh_geodir_add_claim_fields_before_main_form', 1);
	function vh_geodir_add_claim_fields_before_main_form() {
		
		global $post;
		$is_claimed = isset($post->claimed) ? $post->claimed : ''; ?>
		
		<div id="geodir_claimed_row" class="required_field geodir_form_row clearfix">
			<span><?php echo CLAIM_BUSINESS_OWNER_ASSOCIATE;?></span>
			<div class="radio_container">
				<span class="radiobutton"></span>
				<span class="input-side-text"><?php echo CLAIM_YES_TEXT;?></span>
				<input class="gd-radio" <?php if($is_claimed == '1'){echo 'checked="checked"';} ?> type="radio" name="claimed" value="1" field_type="radio" style="display: none">
				<div class="clearfix"></div>
			</div>
			<div class="radio_container">
				<span class="radiobutton"></span>
				<span class="input-side-text"><?php echo CLAIM_NO_TEXT;?></span>
				<input class="gd-radio" <?php if($is_claimed == '0'){echo 'checked="checked"';} ?> type="radio" name="claimed" value="0" field_type="radio" style="display: none">
				<div class="clearfix"></div>
			</div>

			<span class="geodir_message_error"><?php echo CLAIM_DECLARE_OWNER_ASSOCIATE;?></span>
		</div>
		<?php
	}
}

function get_geodir_show_listing_fields( $post_id, $type, $place='', $post_type='gd_place' ) {
	global $wpdb;
	$output = '';
	$field_count = 0;

	if ( function_exists('geodir_post_custom_fields') ) {
		$geodir_fields = geodir_post_custom_fields('','all',$post_type);
	} else {
		$geodir_fields = array();
	}

	if ( isset($_REQUEST['preview']) ) {
		$query = "SELECT is_active, show_on_listing, show_on_detail, field_icon, htmlvar_name, site_title FROM " . $wpdb->prefix .  "geodir_custom_fields";
		$geodir_fields = $wpdb->get_results($query, ARRAY_A);
	}

	if ( !empty($geodir_fields) ) {
		foreach ($geodir_fields as $extra_fields) {
			if ( $extra_fields["is_active"] == "1" && $extra_fields["show_on_listing"] == "1" ) {
				$field_count++;
			}
		}

		if ( $field_count == 0 ) {
			$field_count = 1;
		}

		if ( $type == 'vacation' ) {
			$icon_width = 100/$field_count;
		} else {
			$icon_width = 50;
		}
		
		$output .= '<div class="item-info ' . $type . '">';
		foreach ($geodir_fields as $extra_fields) {
			if ( isset($extra_fields['css_class']) ) {
				$css_class = $extra_fields['css_class'];
			} else {
				$css_class = '';
			}
			if ( $extra_fields["is_active"] == "1" && ( $extra_fields["show_on_listing"] == "1" && $place == '' ) || ( $extra_fields["show_on_detail"] == "1" && $place == 'single' && $extra_fields["field_icon"] != '' ) ) {
				$output .= '
				<div class="featured-' . $extra_fields['htmlvar_name'] . ' ' . $css_class . ' featured-item" style="width: ' . $icon_width . '%">';
					if($extra_fields["field_icon"]){//if (filter_var($extra_fields["field_icon"], FILTER_VALIDATE_URL) !== FALSE) {
					  
						 $img = $extra_fields["field_icon"];
						$output .= '<span class="listing-featured-image"><img src="' . $img . '" alt="' . __('Featured image', 'vh') . '" width="40" height="30"><div class="clearfix"></div></span>';
					} else {
						
						$output .= '<span class="featured-icon ' . $extra_fields["field_icon"] . '"></span>';
					}
					if ( isset($_REQUEST['preview']) ) {
						$field_name = $extra_fields["htmlvar_name"];
						$icon_text = $_REQUEST[$field_name];
					} else {
						$icon_text = geodir_get_post_meta($post_id,$extra_fields["htmlvar_name"],true);
					}
					$output .= '
					<span class="featured-text">' . $icon_text . ' ' . __($extra_fields["site_title"],'vh') . '</span>
				</div>';
			}
		}
		$output .= '<div class="clearfix"></div></div>';
	}

	return $output;
}

function get_geodir_event_date( $id ) {
	global $wpdb;

	$results = $wpdb->get_results('SELECT event_date FROM '.$wpdb->prefix.'geodir_event_schedule WHERE event_id = "'.$id.'" ORDER BY event_date LIMIT 1');

	if ( !empty($results) ) {
		if ( get_option('date_format') ) {
			$date_format = get_option('date_format');
		} else {
			$date_format = 'Y-m-d';
		}
		return date($date_format, strtotime($results['0']->event_date));
	} else {
		return __('None', 'vh');
	}
}