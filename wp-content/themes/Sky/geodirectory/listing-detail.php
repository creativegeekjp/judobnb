<style type="text/css">
	.modalDialog {
    position: fixed;
    font-family: Arial, Helvetica, sans-serif;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: rgba(0, 0, 0, 0.8);
    z-index: 99999;
    opacity:0;
    -webkit-transition: opacity 400ms ease-in;
    -moz-transition: opacity 400ms ease-in;
    transition: opacity 400ms ease-in;
    pointer-events: none;
}
.modalDialog:target {
    opacity:1;
    pointer-events: auto;
}
.modalDialog > div {
    width: 400px;
    position: relative;
    margin: 10% auto;
    padding: 5px 20px 13px 20px;
    border-radius: 10px;
    background: #fff;
    background: -moz-linear-gradient(#fff, #999);
    background: -webkit-linear-gradient(#fff, #999);
    background: -o-linear-gradient(#fff, #999);
}
.close {
    background: #606061;
    color: #FFFFFF;
    line-height: 25px;
    position: absolute;
    right: -12px;
    text-align: center;
    top: -10px;
    width: 24px;
    text-decoration: none;
    font-weight: bold;
    -webkit-border-radius: 12px;
    -moz-border-radius: 12px;
    border-radius: 12px;
    -moz-box-shadow: 1px 1px 3px #000;
    -webkit-box-shadow: 1px 1px 3px #000;
    box-shadow: 1px 1px 3px #000;
}
.close:hover {
    background: #00d9ff;
}
</style>
<?php 
header("X-XSS-Protection: 0"); // IE requirement
// call header
get_header();

###### WRAPPER OPEN ######
// this adds the opening html tags to the primary div, this required the closing tag below :: ($type='',$id='',$class='')
do_action( 'geodir_wrapper_open', 'details-page', 'geodir-wrapper','');
	
	###### TOP CONTENT ######
	// action called before the main content and the page specific content
	do_action('geodir_top_content', 'details-page');
	// action called before the main content
	do_action('geodir_before_main_content', 'details-page');

	
			###### MAIN CONTENT WRAPPERS OPEN ######
			// this adds the opening html tags to the content div, this required the closing tag below :: ($type='',$id='',$class='')
			do_action( 'geodir_wrapper_content_open', 'details-page', 'geodir-wrapper-content','');
			// this adds the opening html tags to the <article>, this required the closing tag below :: ($type='',$id='',$class='',$itemtype='')
			do_action( 'geodir_article_open', 'details-page', 'post-'.get_the_ID(),get_post_class(), 'http://schema.org/LocalBusiness');
			
					###### MAIN CONTENT ######
					// this call the main page content
					if ( have_posts() ) { 					
					the_post(); 
					global $post, $post_images, $wpdb, $preview;

					if ( geodir_is_page('preview') ) {
						$post_type = $_REQUEST["listing_type"];
						$listing_rating = '0';
						$listing_rating_count = '0';
					} else {
						$post_type = get_post_type(get_the_ID());
						$listing_rating = get_listing_rating_count( $post->ID, true, $post_type );
						$listing_rating_count = get_listing_rating_count( $post->ID, false, $post_type );
					}

					$listing_price = get_geodir_listing_price( $post->ID, true, $post_type );
					?>
						<span itemprop="name" style="display: none"><?php echo get_the_title( $post->ID ); ?></span>
						<span itemprop="pricerange" style="display: none"><?php echo $listing_price; ?></span>
						<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
							<span class="listing-item-star text" itemprop="ratingValue" style="display: none"><?php echo $listing_rating; ?></span>
							<span itemprop="reviewCount" style="display: none"><?php echo $listing_rating_count; ?></span>
						</div>
						<div class="single-listing-by vc_col-sm-9">
							<?php if ( get_option("vh_theme_version") != "SkyDirectory" ) { ?>
								<div class="single-listing-by-main">
									<div class="single-listing-by-image">
										<?php echo get_avatar(get_userdata( get_post_field( 'post_author', $post->ID ) )->ID, 135); ?>
									</div>
									<div class="single-listing-by-container">
										<div class="single-listing-by-info">
											<span class="author-text"><?php _e('Listing by:', 'vh'); ?></span>
											<?php
												if ( get_option('permalink_structure') ) {
													$dash_symbol = '?';
												} else {
													$dash_symbol = '&';
												}
											?>
											<span class="author-name"><a href="<?php echo get_author_posts_url( get_post_field( 'post_author', $post->ID ) ).$dash_symbol ?>geodir_dashbord=true&stype=<?php echo $post_type; ?>"><?php echo get_userdata( get_post_field( 'post_author', $post->ID ) )->display_name; ?></a></span>
										</div>
										<div class="single-listing-social">
											<?php if ( !get_option('geodir_disable_tfg_buttons_section', '0') ) { ?>
												<div id="fb-root"></div>
												<script>(function(d, s, id) {
												  var js, fjs = d.getElementsByTagName(s)[0];
												  if (d.getElementById(id)) return;
												  js = d.createElement(s); js.id = id;
												  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.0";
												  fjs.parentNode.insertBefore(js, fjs);
												}(document, 'script', 'facebook-jssdk'));
												</script>
												<div class="fb-like" data-href="<?php echo get_permalink($post->ID); ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
												<a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
												<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
												<!-- Place this tag in your head or just before your close body tag. -->
												<script src="https://apis.google.com/js/platform.js" async defer></script>

												<!-- Place this tag where you want the share button to render. -->
												<div class="g-plus" data-action="share" data-annotation="bubble"></div>
											<?php } ?>
											<?php if ( !get_option('geodir_disable_sharethis_button_section', '0') ) { ?>
												<a href="javascript:void(0)" id="st_sharethis" class="single-listing-share icon-share"></a>
												<div class="addthis_toolbox addthis_default_style">
													<script type="text/javascript">var switchTo5x=false;</script>
													<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
													<script type="text/javascript">stLight.options({publisher: "2bee0c38-7c7d-4ce7-9d9a-05e920d509b4", doNotHash: false, doNotCopy: false, hashAddressBar: false});
													stWidget.addEntry({
													"service":"sharethis",
													"element":document.getElementById('st_sharethis'),
													"url":"<?php echo geodir_curPageURL();?>",
													"title":"<?php echo $post->post_title;?>",
													"type":"chicklet",
													"text":""    
													});</script>
												</div>
											<?php } ?>
											<a href="javascript:void(0)" class="single-listing-mail icon-mail-alt vh_b_sendtofriend"></a>
										
											<input type="hidden" name="geodir_popup_post_id" value="<?php echo $post->ID?>">
											<div class="geodir_display_popup_forms"></div>
										</div>
									</div>
							
								<?php

									$querystr = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix .  "geodir_" . $post_type . "_detail WHERE post_status = 'publish' AND post_id=%s", $post->ID);
									$queryresults = $wpdb->get_results($querystr);

								?>
								<?php if ( get_option("vh_theme_version") != "SkyEstate" ) { ?>
									<input type="hidden" name="geodir_popup_post_id" value="<?php echo $post->ID?>">
									<div class="geodir_display_popup_forms"></div>
									
										<?php 
										
											$current_user = wp_get_current_user();
											
											if ( !is_user_logged_in() ) // && $current_user->ID == 1 || current_user_can( 'administrator' ) 
											{
												?>
												<a href="/wp-login.php"  name='basic' class="wpb_button single-listing-contact-author simplemodal-login">Please Login</a>
											   <?php
											  
											} 
											else 
											{
											
											    ?> 
													<a href="<?php bp_send_private_message_link('&unames='.get_userdata( get_post_field( 'post_author', $post->ID ) )->user_login); ?>" class="wpb_button single-listing-contact-author">Send Message</a>
												<?php
											}
									?>
									
								<?php } ?>
								<?php if ( get_option("vh_theme_version") != "SkyDirectory" ) {
									echo get_geodir_show_listing_fields( $post->ID, 'vacation', 'single', $post_type );
								} ?>
								<div class="clearfix"></div>
								<?php if ( get_option('geodir_payment_expire_date_on_detail', '0') && $queryresults['0']->expire_date != 'Never' && strtotime($queryresults['0']->expire_date) > time() ) {
									echo '<span class="open-listing-expires">'.__('Expires', 'vh'). ': '.$queryresults['0']->expire_date.'</span>';
								} ?>
							</div>
							<?php } ?>
							<div class="single-listing-main-content">
								<?php
								if ($preview) {
									echo "<p>".$_REQUEST["post_desc"]."</p>";
									// Preview table
									$preview_values = get_preview_data($_REQUEST);

									if ( !empty($preview_values) ) { ?>
										<div class="custom-table-container"> <?php
										foreach ($preview_values as $extra_fields) {
											  ?>
									 			<div class="custom-table-item <?php echo $extra_fields['type']; ?>">
													<?php if ( $extra_fields['type'] == 'fieldset' ) { ?>
														<span class="custom-table-title"><?php echo $extra_fields["title"]; ?></span>
													<?php } else { ?>
														<span class="custom-table-title"><?php echo $extra_fields["title"]; ?>:</span>
													<?php } ?>
													<span class="custom-table-description">
														<?php
															if (filter_var($extra_fields["value"], FILTER_VALIDATE_URL) !== FALSE) {
																echo '<a href="'.$extra_fields["value"].'">'.$extra_fields["value"].'</a>';
															} elseif (filter_var($extra_fields["value"], FILTER_VALIDATE_EMAIL) !== FALSE) {
																echo '<a href="mailto:'.$extra_fields["value"].'">'.$extra_fields["value"].'</a>';
															} else {
																if ( isset($extra_fields['value']['gd_placecategory']) ) {
																	$categories = explode(',', $extra_fields['value']['gd_placecategory']);
																	$cat_arr = array();
																	foreach ($categories as $cat_value) {
																		if ( $cat_value != '' ) {
																			$current_cat = get_term_by('id', $cat_value, $post_type.'category');
																			$cat_arr[] = $current_cat->name;
																		}
																	}
																	echo implode(', ', $cat_arr);
																} else {
																	echo $extra_fields["value"];
																}
															}
														?>
													</span>
												</div>
										<?php 
										}
										?> <div class="clearfix"></div></div> <?php
									}

									// template specific, this can add the sidebar top section and breadcrums
									do_action('geodir_detail_before_main_content');
								} else {
									the_content();

									if ( function_exists('geodir_post_custom_fields') ) {
										$geodir_fields = geodir_post_custom_fields('','all', $post_type);
									} else {
										$geodir_fields = array();
									}

									if ( !empty($geodir_fields) ) { ?>
										<div class="custom-table-container"> <?php
										foreach ($geodir_fields as $extra_fields) {
											if ( $extra_fields["is_active"] == "1" && $extra_fields["show_on_detail"] == "1" && $extra_fields["field_icon"] == '' ) { ?>
												<div class="custom-table-item <?php echo $extra_fields['type']; ?>">
													<?php if ( $extra_fields['type'] == 'fieldset' ) { ?>
														<span class="custom-table-title"><?php echo $extra_fields["site_title"]; ?></span>
													<?php } else { ?>
														<span class="custom-table-title"><?php echo $extra_fields["site_title"]; ?>:</span>
													<?php } ?>
													<span class="custom-table-description">
														<?php
															if (filter_var(geodir_get_post_meta($post->ID,$extra_fields["htmlvar_name"],true), FILTER_VALIDATE_URL) !== FALSE) {
																echo '<a href="'.geodir_get_post_meta($post->ID,$extra_fields["htmlvar_name"],true).'">'.geodir_get_post_meta($post->ID,$extra_fields["htmlvar_name"],true).'</a>';
															} elseif (filter_var(geodir_get_post_meta($post->ID,$extra_fields["htmlvar_name"],true), FILTER_VALIDATE_EMAIL) !== FALSE) {
																echo '<a href="mailto:'.geodir_get_post_meta($post->ID,$extra_fields["htmlvar_name"],true).'">'.geodir_get_post_meta($post->ID,$extra_fields["htmlvar_name"],true).'</a>';
															} else {
																if ( $extra_fields['type'] == 'address' && $extra_fields['htmlvar_name'] == 'post' ) {
																	echo geodir_get_post_meta($post->ID,'post_address',true);
																} elseif ( $extra_fields['htmlvar_name'] == $post_type.'category' ) {
																	$categories = explode(',', geodir_get_post_meta($post->ID,$extra_fields["htmlvar_name"],true));
																	$cat_arr = array();
																	foreach ($categories as $cat_value) {
																		if ( $cat_value != '' ) {
																			$current_cat = get_term_by('id', $cat_value, $post_type.'category');
																			$cat_arr[] = $current_cat->name;
																		}
																	}
																	echo implode(', ', $cat_arr);
																} elseif ( $extra_fields['type'] == 'checkbox' ) {
																	$checkbox_value = geodir_get_post_meta($post->ID,$extra_fields["htmlvar_name"],true);
																	if ( $checkbox_value == '1' ) {
																		_e('Yes', 'vh');
																	} else {
																		_e('No', 'vh');
																	}
																} else {
																	echo geodir_get_post_meta($post->ID,$extra_fields["htmlvar_name"],true);
																}
															}
														?>
													</span>
												</div>
										<?php }
										}
										if ( $post_type == 'gd_event' ) {
											$event_dates = $wpdb->get_results('SELECT recurring_dates FROM '.$wpdb->prefix.'geodir_gd_event_detail WHERE post_id="'.get_the_ID().'"');
											$event_dates = unserialize($event_dates['0']->recurring_dates);

											if ( !empty($event_dates) ) {
												if ( $event_dates['is_recurring'] ) {
													$event_date_arr = explode(',', $event_dates['event_recurring_dates']);
												} else {
													if ( $event_dates['event_start'] == $event_dates['event_end'] ) {
														$event_date_arr = array($event_dates['event_start']);
													} else {
														$event_date_arr = vh_date_range($event_dates['event_start'], $event_dates['event_end'], '+1 day', 'Y-m-d');
													}
												}
												if ( get_option('geodir_event_hide_past_dates', '0') ) {
													foreach ( $event_date_arr as $event_key => $event_value ) {
														if ( strtotime($event_value) <= time() ) {
															unset($event_date_arr[$event_key]);
														}
													}
												}
												?>
												<div class="clearfix"></div>
												<div class="custom-table-item event-dates">
													<span class="custom-table-title"><?php _e('Event dates', 'vh'); ?>:</span>
													<span class="custom-table-description">
														<?php foreach ( $event_date_arr as $event_key => $event_value ) {
															echo $event_value.'<br>';
														} ?>
													</span>
												</div>
												<div class="custom-table-item event-dates">
													<span class="custom-table-title"><?php _e('Event times', 'vh'); ?>:</span>
													<span class="custom-table-description">
														<?php foreach ( $event_date_arr as $event_key => $event_value ) {
															if ( $event_dates['different_times'] == '1' ) {
																echo $event_dates['starttimes'][$event_key].' - '.$event_dates['endtimes'][$event_key].'<br>';
															} else {
																if ( $event_dates['all_day'] ) {
																	echo __('All day', 'vh').'<br>';
																} else {
																	echo $event_dates['starttime'].' - '.$event_dates['endtime'].'<br>';
																}
															}
														} ?>
													</span>
												</div>
						
												<?php
											}
										}
										?> <div class="clearfix"></div></div> <?php
									}

									if ( get_option("vh_theme_version") == "SkyDirectory" ) { ?>
										<div class="single-listing-by-main skydirectory">
											<div class="single-listing-by-container">
													<input type="hidden" name="geodir_popup_post_id" value="<?php echo $post->ID?>">
													<div class="geodir_display_popup_forms"></div>
													<a href="javascript:void(0)" class="wpb_button single-listing-contact-author vh_b_send_inquiry"><?php _e("Send message", "vh"); ?></a>
													<a href="javascript:void(0)" class="single-listing-mail icon-mail-alt vh_b_sendtofriend"></a>
													<div class="single-listing-social">
														<?php if ( !get_option('geodir_disable_tfg_buttons_section', '0') ) { ?>
															<div id="fb-root"></div>
															<script>(function(d, s, id) {
															  var js, fjs = d.getElementsByTagName(s)[0];
															  if (d.getElementById(id)) return;
															  js = d.createElement(s); js.id = id;
															  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.0";
															  fjs.parentNode.insertBefore(js, fjs);
															}(document, 'script', 'facebook-jssdk'));
															</script>
															<div class="fb-like" data-href="<?php echo get_permalink($post->ID); ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
															<a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
															<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
															<!-- Place this tag in your head or just before your close body tag. -->
															<script src="https://apis.google.com/js/platform.js" async defer></script>

															<!-- Place this tag where you want the share button to render. -->
															<div class="g-plus" data-action="share" data-annotation="bubble"></div>
														<?php } ?>
														<?php if ( !get_option('geodir_disable_sharethis_button_section', '0') ) { ?>
															<a href="javascript:void(0)" id="st_sharethis" class="single-listing-share icon-share"></a>
															<div class="addthis_toolbox addthis_default_style">
																<script type="text/javascript">var switchTo5x=false;</script>
																<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
																<script type="text/javascript">stLight.options({publisher: "2bee0c38-7c7d-4ce7-9d9a-05e920d509b4", doNotHash: false, doNotCopy: false, hashAddressBar: false});
																	stWidget.addEntry({
																	"service":"sharethis",
																	"element":document.getElementById('st_sharethis'),
																	"url":"<?php echo geodir_curPageURL();?>",
																	"title":"<?php echo $post->post_title;?>",
																	"type":"chicklet",
																	"text":""    
																	});</script>
																</div>
																<input type="hidden" name="geodir_popup_post_id" value="<?php echo $post->ID?>">
																<div class="geodir_display_popup_forms"></div>
															</div>
														<?php } ?>
														<span class="share-location"><?php _e('Share:', 'vh'); ?></span>
													</div>
											<div class="clearfix"></div>
										</div>
									<?php }
								}
								comments_template( "/geodir-comments.php", true );
								?>
							</div>
						</div><?php
					 }elseif($preview){
						do_action( 'geodir_action_geodir_set_preview_post'); // set the $post to the preview values
						//print_r($post);
						do_action( 'geodir_details_main_content', $post);
					}
			
	###### SIDEBAR ######
	// do_action('geodir_detail_sidebar');
	$width = empty($instance['width']) ? '700' : apply_filters('widget_width', $instance['width']);
	$height = empty($instance['heigh']) ? '425' : apply_filters('widget_heigh', $instance['heigh']);
	$maptype = empty($instance['maptype']) ? 'ROADMAP' : apply_filters('widget_maptype', $instance['maptype']);
	$zoom = empty($instance['zoom']) ? '13' : apply_filters('widget_zoom', $instance['zoom']);
	$autozoom = empty($instance['autozoom']) ? '' : apply_filters('widget_autozoom', $instance['autozoom']);
	$child_collapse = empty($instance['child_collapse']) ? '0' : apply_filters('widget_child_collapse', $instance['child_collapse']);
	$scrollwheel = empty($instance['scrollwheel']) ? '0' : apply_filters('widget_scrollwheel', $instance['scrollwheel']);

	$map_args = array();
	// $map_args['map_canvas_name'] = str_replace('-' , '_' , $args['widget_id']); //'home_map_canvas'.$str ;
	$map_args['width'] = $width;
	$map_args['height'] = $height;
	$map_args['maptype'] = $maptype;
	$map_args['scrollwheel'] = $scrollwheel;
	$map_args['zoom'] = $zoom ;
	$map_args['autozoom'] = $autozoom;
	$map_args['child_collapse'] = $child_collapse;
	$map_args['enable_cat_filters'] = true;
	$map_args['enable_text_search'] = true;
	$map_args['enable_post_type_filters'] = true;
	$map_args['enable_location_filters'] = apply_filters('geodir_home_map_enable_location_filters', false);
	$map_args['enable_jason_on_load'] = false;
	$map_args['enable_marker_cluster'] = false;
	$map_args['enable_map_resize_button'] = true;
	$map_args['map_class_name'] = 'geodir-map-home-page';
	$map_args['map_canvas_name'] = 'geodir_map_v3_home_map_101';
	
	$is_geodir_home_map_widget = true;
	$map_args['is_geodir_home_map_widget'] = $is_geodir_home_map_widget;
	
	
	?>

	<div class="single-listing-sidebar sidebar-inner vc_col-sm-3">
	<?php
		geodir_draw_map($map_args);
		if ( !geodir_is_page('preview') ) {
			echo get_gmap_listings();
		}
		global $vh_is_in_sidebar;
		$vh_is_in_sidebar = true;
		generated_dynamic_sidebar();
	?>
	</div>
	
	<?php

	###### MAIN CONTENT WRAPPERS CLOSE ######
	// this adds the closing html tags to the </article> :: ($type='')
	do_action( 'geodir_article_close', 'details-page');
	// action called after the main content
	do_action('geodir_after_main_content');
	// this adds the closing html tags to the wrapper_content div :: ($type='')
	do_action( 'geodir_wrapper_content_close', 'details-page');

	###### BOTTOM SECTION WIDGET AREA ######
	// adds the details bottom section widget area, you can add more classes via ''
	do_action( 'geodir_sidebar_detail_bottom_section', '' );

###### WRAPPER CLOSE ######	
// this adds the closing html tags to the wrapper div :: ($type='')
do_action( 'geodir_wrapper_close', 'details-page');


get_footer();      