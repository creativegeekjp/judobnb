<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
	<head>
		<meta content="True" name="HandheldFriendly">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<meta name="viewport" content="width=device-width">
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>">
		<title><?php wp_title('&laquo;', true, 'right'); ?></title>
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">


		<?php
			global $vh_class,$wpdb,$map_canvas_arr;
			$logo_size_html = '';

			// Get theme logo
			$logo = get_option('vh_sitelogo');
			if($logo == false) {
				$logo = get_template_directory_uri() . '/images/logo.png';
				$logo_size_html = ' width="293" height="69"';
			}

			// Get favicon
			$favicon = get_option('vh_favicon');
			if ($favicon == false) {
				$favicon = get_template_directory_uri() . '/images/favicon.ico';
			}

			// Get header bg image
			$header_bg = get_option('vh_header_bg');
			if ($header_bg == false) {
				$header_bg = get_template_directory_uri() . '/images/header_bg.jpg';
			}

			$website_logo_retina_ready = filter_var(get_option('vh_website_logo_retina'), FILTER_VALIDATE_BOOLEAN);
			if ((bool)$website_logo_retina_ready != false) {
				$logo_size = getimagesize($logo);
				$logo_size_html = ' style="height: ' . ($logo_size[1] / 2) . 'px;" height="' . ($logo_size[1] / 2) . '"';
			}

			// Social icons
			$menu_header_twitter_url   = get_option( 'vh_header_twitter_url' );
			$menu_header_flickr_url    = get_option( 'vh_header_flickr_url' );
			$menu_header_facebook_url  = get_option( 'vh_header_facebook_url' );
			$menu_header_google_url    = get_option( 'vh_header_google_url' );
			$menu_header_pinterest_url = get_option( 'vh_header_pinterest_url' );

			// Set geodir cluster maps
			set_cluster_maps();

			$geodir_post_types = get_geodir_custom_post_types();

			if ( isset($_REQUEST["preview"]) && !empty($_REQUEST["geodir_video"]) ) {
				$geodir_video_url = $_REQUEST["geodir_video"];
			} elseif ( !isset($_REQUEST["preview"]) && strpos(get_post_type(get_the_ID()),'gd_') !== false ) {
				$geodir_video_url = geodir_get_video(get_the_ID());
			} else {
				$geodir_video_url = '';
			}

			if ( function_exists('geodir_is_page') && geodir_is_page('preview') ) {
				$post_type = $_REQUEST["listing_type"];
			} else {
				$post_type = get_post_type(get_the_ID());
			}
		?>
		<link rel="shortcut icon" href="<?php echo esc_attr($favicon); ?>" />
		<?php wp_head(); ?>
	</head>
	<script>
	  window.fbAsyncInit = function() {
	    FB.init({
	      appId      : '1674618099469989',
	      xfbml      : true,
	      version    : 'v2.5'
	    });
	  };
	
	  (function(d, s, id){
	     var js, fjs = d.getElementsByTagName(s)[0];
	     if (d.getElementById(id)) {return;}
	     js = d.createElement(s); js.id = id;
	     js.src = "//connect.facebook.net/en_US/sdk.js";
	     fjs.parentNode.insertBefore(js, fjs);
	   }(document, 'script', 'facebook-jssdk'));
	</script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"> </script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/slidesjs/3.0/jquery.slides.min.js"></script>
	<body <?php body_class($vh_class); ?>>
		<?php if ( $_SERVER['SERVER_NAME'] == 'cohhe.com' ) { ?>
			<a href="http://themeforest.net/item/sky-wordpress-listings-theme/10061836?ref=Cohhe" target="_blank" id="buy-now-ribbon"></a>
		<?php } ?>
		<div class="vh_wrapper" id="vh_wrappers">
		<div class="main-body-color"></div>
		<div class="overlay-hide"></div>
		<div class="pushy pushy-left">
			<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary-menu',
						'menu_class'     => 'responsive-menu',
						'depth'          => 2,
						'link_before'    => '',
						'link_after'     => ''
					)
				);
			?>
			
		</div>
		<div class="wrapper st-effect-3 w_display_none" id="container">
			<div class="main">
				<?php
					$website_logo_retina_ready = filter_var(get_option('vh_website_logo_retina'), FILTER_VALIDATE_BOOLEAN);
					if ((bool)$website_logo_retina_ready != false) {
						$logo_size = getimagesize($logo);
					}
					
					// if ( is_front_page() ) {
					// 	$logo = str_replace( '.png', '_white.png', $logo );
					// }
				?>
				
				<header class="header vc_row-fluid vc_col-sm-12">
					<div class="top-header vc_col-sm-12">
						<div class="logo shadows vc_col-sm-3">
							<a href="<?php echo home_url(); ?>"><img src="<?php echo site_url().$logo; ?>"<?php echo $logo_size_html ; ?> alt="<?php bloginfo('name'); ?>" /></a>
						</div>
						<div class="menu-btn icon-menu-1"></div>
						<?php if ( !is_front_page() && !isset($_GET['pay_action']) ) { ?>
						<div class="header_search"><?php get_search_form(); ?></div>
						<div class="header-search-body">
							<div class="header-search-container">
								<div class="header-search-form">
									<div class="header-form-container">
										<?php
											$listing_categories = get_terms('gd_placecategory');
											if ( !is_wp_error($listing_categories) ) {
												$listing_category = esc_attr($listing_categories['0']->name);
											} else {
												$listing_category = '';
											}
										?>

										<?php vh_get_header_search_field(); ?>

										<input type="hidden" id="header-post-type" value="<?php echo get_option('geodir_default_map_search_pt', 'gd_place'); ?>">
										<a href="javascript:void(0)" class="wpb_button wpb_btn-warning icon-search" id="header-submit2"></a>
										<div class="clearfix"></div>
									</div>
								</div>
							</div>
						</div>
						<?php } ?>
						
						<div class="top-menu-container <?php if ( get_option('vh_header_add_property') == 'false' ) echo 'button_disabled'; ?>">
							<?php
								wp_nav_menu(
									array(
										'theme_location'  => 'primary-menu',
										'menu_class'      => 'header-menu',
										'container'       => 'div',
										'container_class' => 'menu-style',
										'depth'           => 2,
										'link_before'     => '',
										'link_after'      => ''
									)
								);
							?>
						</div>
						<div class="clearfix"></div>
					</div>
					<?php 
					if ( ( in_array($post_type, $geodir_post_types) && !isset($_GET["stype"]) && !isset($_GET["gd_placecategory"]) && !is_search() ) || isset($_REQUEST["preview"]) ) { ?>
						<div class="gdplaces-header-container">
							<?php
								$i = 1;
								$picture_count = 0;
								$post_images = geodir_get_images( $post->ID, 'open-listing', get_option( 'geodir_listing_no_img' ) );
								if ( isset($_REQUEST["preview"]) ) {
									$post_images = explode(',', $_REQUEST["post_images"]);
								}
								if ( !empty($post_images) ) {
									foreach ($post_images as $image_value) {
										$picture_count++;
									}
								}
								echo "<div class=\"listing-carousel-container\">
								<ul class=\"listing-carousel\">";
								if ( $geodir_video_url != '' ) { ?>
									<li class="listing-item first">
									<?php
									$querystr = $wpdb->prepare("SELECT overall_rating,rating_count,post_city,post_country FROM " . $wpdb->prefix .  "geodir_" . $post_type . "_detail WHERE post_status = 'publish' AND post_id=%s", $post->ID);
									$queryresults = $wpdb->get_results($querystr);

									$video_link = $geodir_video_url;
									
									if ( strpos( $video_link, 'youtube' ) > 0 || strpos( $video_link, 'youtu.be' ) > 0 ) {
										if ( strpos( $video_link, 'youtube' ) > 0 ) {
											$imgstr = explode( 'v=', $video_link );
											$imgval = explode( '&', $imgstr[1] );
											$match  = $imgval[0];
										} else if ( strpos( $video_link, 'youtu.be' ) > 0 ) {
											$imgstr = explode( '/', $video_link );
											$match  = $imgstr[3];
										} ?>

										<div id="ytplayer"></div>
										<script type="text/javascript">
											// Load the IFrame Player API code asynchronously.
											var tag = document.createElement('script');
											tag.src = "https://www.youtube.com/player_api";
											var firstScriptTag = document.getElementsByTagName('script')[0];
											firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

											// Replace the 'ytplayer' element with an <iframe> and
											// YouTube player after the API code downloads.
											var player;
											function onYouTubePlayerAPIReady() {
												player = new YT.Player('ytplayer', {
													height: '611',
													width: '100%',
													videoId: '<?php echo $match; ?>',
													playerVars : {
														'autoplay' : 1,
														'rel' : 0,
														'showinfo' : 0,
														'egm' : 0,
														'showsearch' : 0,
														'controls' : 1,
														'modestbranding' : 1,
														'autohide' : 1
													},
													events: {
													'onReady': onPlayerReady
													}
												});
											};

											function onPlayerReady(event) {
												event.target.mute();

												jQuery(".listing-item-video").click(function() {
													event.target.playVideo();
													event.target.unMute();

												});

												jQuery(".listing-item-exitfullscreen").live( "click", function() {
													event.target.mute();
												});

												jQuery(".header-slider-next, .header-slider-prev").click(function() {
													event.target.pauseVideo();
												});
											}
										</script>
									<?php } else if ( strpos( $video_link, 'vimeo' ) > 0 ) {
										$split = explode( '/', $video_link );
										$match = $split['3'];
										?>

										<div class="vimeo-video-container"></div>

										<script type="text/javascript">
											if ( jQuery('.vimeo-video-container').length ) {
												var playVid = {
													getFrameHtml: function(){
														return '<iframe id="vimeo_player" src="https://player.vimeo.com/video/'+<?php echo $match; ?>+'?api=1&autoplay=1&loop=1&title=0&portrait=0&player_id=vimeo_player" width="100%" height="611" frameborder="0"></iframe>';
													},
													play: function(){
														jQuery(".vimeo-video-container").html(playVid.getFrameHtml());

														var iframe = jQuery('#vimeo_player')[0],
															player = $f(iframe);

														// When the player is ready, add listeners for pause, finish, and playProgress
														player.addEvent('ready', function(id) {
															// Set the API player
															player.api('setVolume', 0);
														});

														jQuery(".listing-item-video").click(function() {
															player.api('setVolume', 1);
															player.api('play');
														});

														jQuery(".listing-item-exitfullscreen").live( "click", function() {
															player.api('setVolume', 0);
														});

														jQuery(".header-slider-next, .header-slider-prev").click(function() {
															player.api('pause');
														});
													}
												}
												jQuery(document).ready(function($) {
													playVid.play();
												});
											}
										</script>
									<?php } ?>
										<div class="listing-item-rating">
											<?php
											if ( !isset($_REQUEST["preview"]) && $queryresults['0']->overall_rating != 0 ) {
												$overall_rating = $queryresults['0']->overall_rating;
											} else {
												$overall_rating = 0;
											}
											if ( isset($_REQUEST["preview"]) ) {
												$overall_rating = 0;
											}

											echo get_listing_rating_stars($overall_rating, true);

											?>
										</div>
										<div class="listing-item-title">
											<?php
											$post_title = get_the_title($post->ID);
											if ( isset($_REQUEST["preview"]) ) {
												$post_title = $_REQUEST["post_title"];
											}
											?>
											<span class="title" itemprop="name"><?php echo $post_title; ?></span>
											<?php echo geodir_favourite_html(get_current_user_id(), $post->ID); ?>
										</div>
										<div class="listing-item-location">
											<?php
											if ( isset($_REQUEST["preview"]) ) {
												echo $_REQUEST["post_address"];
											} else {
												echo $queryresults['0']->post_city . ", " . $queryresults['0']->post_country;
											}
											?>
										</div>
										<div class="listing-item-info">
											<?php if ( $geodir_video_url != '' && $picture_count >= 4 ) { ?>
												<div class="open-listing-two">
											<?php } else { ?>
												<div class="open-listing-one">
											<?php } ?>
											
												<?php if ( $geodir_video_url != '' ) { ?>
													<a href="javascript:void(0)" class="listing-item-video icon-youtube-play"><span class="listing-slider-hover video-hover"><?php _e("View video", "vh"); ?></span></a>
												<?php } ?>
												<?php if ( $picture_count >= 4 ) { ?>
													<a href="javascript:void(0)" class="listing-item-fullscreen icon-resize-full"><span class="listing-slider-hover gallery-hover"><?php _e("Maximize gallery", "vh"); ?></span></a>
												<?php } ?>
											</div>
											<?php if ( function_exists('geodir_display_post_claim_link') ) { ?>
												<div class="claim-busisness-container">
													<?php vh_geodir_display_post_claim_link(); ?>
												</div>
											<?php } ?>
										</div>
									</li>
								<?php }
								$loops = 1;
								if ( !empty($post_images) ) {
									foreach ($post_images as $image) {
											$querystr = $wpdb->prepare("SELECT overall_rating,rating_count,post_city,post_country FROM " . $wpdb->prefix .  "geodir_" . $post_type . "_detail WHERE post_status = 'publish' AND post_id=%s", $post->ID);
											$queryresults = $wpdb->get_results($querystr); ?>
											<li class="listing-item first">
												<?php if ( isset($_REQUEST["preview"]) ) {
													$image_source = $image;
												} else {
													$image_source = $image->src;
												} ?>
												<style type='text/css' media='all'>.carousel-image-<?php echo $loops; ?> { background: url(<?php echo $image_source; ?>); height: 611px; background-size: cover !important; background-position: 100% !important; }</style>
												<div class="carousel-image-<?php echo $loops; ?>"></div>
												<div class="listing-item-rating">
													<?php
													if ( !isset($_REQUEST["preview"]) && $queryresults['0']->overall_rating != 0 ) {
														$overall_rating = $queryresults['0']->overall_rating;
													} else {
														$overall_rating = 0;
													}
													if ( isset($_REQUEST["preview"]) ) {
														$overall_rating = 0;
													}

													echo get_listing_rating_stars($overall_rating, true);
													
													?>
												</div>
												<div class="listing-item-title">
												<?php
												$post_title = get_the_title($post->ID);
												if ( isset($_REQUEST["preview"]) ) {
													$post_title = $_REQUEST["post_title"];
												}
												?>
													<?php echo $post_title; ?>
													<?php echo geodir_favourite_html(get_current_user_id(), $post->ID); ?>
												</div>
												<div class="listing-item-location">
													<?php
													if ( isset($_REQUEST["preview"]) ) {
														echo $_REQUEST["post_address"];
													} else {
														echo $queryresults['0']->post_city . ", " . $queryresults['0']->post_country;
													}
													?>
												</div>
												<div class="listing-item-info">
													<?php if ( $geodir_video_url != '' && $picture_count >= 4 ) { ?>
														<div class="open-listing-two">
													<?php } else { ?>
														<div class="open-listing-one">
													<?php } ?>
														<?php if ( $geodir_video_url != '' ) { ?>
															<a href="javascript:void(0)" class="listing-item-video icon-youtube-play"><span class="listing-slider-hover"><?php _e("View video", "vh"); ?></span></a>
														<?php } ?>
														<?php if ( $picture_count >= 4 ) { ?>
															<a href="javascript:void(0)" class="listing-item-fullscreen icon-resize-full"><span class="listing-slider-hover"><?php _e("Maximize gallery", "vh"); ?></span></a>
														<?php } ?>
													</div>
													<?php if ( function_exists('geodir_display_post_claim_link') ) { ?>
														<div class="claim-busisness-container">
															<?php vh_geodir_display_post_claim_link(); ?>
														</div>
													<?php } ?>
												</div>
											</li>
										<?php
										$i++;
										$loops++;
									}
								}
								echo "</ul></div>";
								echo "<div class=\"listing-gallery-carousel-main\">
								<div class=\"listing-gallery-carousel-container\">
								<ul class=\"listing-gallery-carousel\">";
								$count = 0;
								foreach ($post_images as $image_slider) {
									if ( isset($_REQUEST["preview"]) ) {
										$image_source = $image_slider;
									} else {
										$image_source = $image_slider->src;
									}
									?>
									<li class="listing-gallery-item">
										<span class="image-container"><img src="<?php echo $image_source; ?>" alt=""/></span>
									</li>
									<?php $count++; ?>
								<?php }
								echo "</ul></div>
								<a href=\"javascript:void(0)\" class=\"header-gallery-slider-prev icon-angle-left\"></a>
								<a href=\"javascript:void(0)\" class=\"header-gallery-slider-next icon-angle-right\"></a>
								<a href=\"javascript:void(0)\" class=\"listing-item-exitfullscreen icon-resize-small\"></a>
								<div class=\"header-gallery-counter\">
									<span class=\"current-item\">2</span> ".__("of", "vh")." <span class=\"max-items\">".$count."</span>
								</div>
								</div>";
							?>
							<?php if ( $picture_count >= 2 || ( $picture_count >= 1 && $geodir_video_url != '' ) ) { ?>
								<a href="javascript:void(0)" class="header-slider-prev icon-angle-left"></a>
								<a href="javascript:void(0)" class="header-slider-next icon-angle-right"></a>
								<?php } ?>
							<?php if ( get_option("vh_theme_version") != "SkyDirectory" && $post_type != 'gd_event' ) { ?>
							<div class="geodir-top-selection">
								<div class="geodir-top-main">
									<?php if ( get_option("vh_theme_version") == "SkyVacation" ) {
										if ( isset($_COOKIE['vh_startrange']) ) {
											$star_date = $_COOKIE['vh_startrange'];
										} else {
											$star_date = date('Y-m-d', time());
										}
										if ( isset($_COOKIE['vh_endrange']) ) {
											$end_date = $_COOKIE['vh_endrange'];
										} else {
											$end_date = '';
										}
										?>
										<div class="single-listing-options when"><span class="listing-input-title"><?php _e('When:', 'vh'); ?></span><input type="text" id="listing-when"><input id="startrange" type="hidden" value="<?php echo esc_attr($star_date); ?>"><input id="endrange" type="hidden" value="<?php echo esc_attr($end_date); ?>"><?php do_action('vh_action_get_listing_when_options'); ?><div class="clearfix"></div></div>
										<div class="single-listing-options people"><span class="listing-input-title"><?php _e('People:', 'vh'); ?></span><input type="text" id="listing-people" readonly><?php vh_get_listing_people_options(); ?><div class="clearfix"></div></div>
									<?php } ?>
									<?php
										$reservation_url = get_permalink(get_option('vh_book_now'));
										$reservation_id = get_post_meta(get_the_ID(), 'vh_resource_id');
										if ( !empty( $reservation_id ) ) {
											$reservation_url .= '?resource=' . get_post_meta(get_the_ID(), 'vh_resource_id', true);
										}
									?>
									<form method="post" action="<?php echo $reservation_url; ?>" name="easy_listing_form" id="easy_listing_form">
										<input id="easy-listing-datepicker-from" type="hidden" name="from" value="" class="hasDatepicker">
										<input id="easy-listing-datepicker-to" type="hidden" name="to" value="" class="hasDatepicker">
									</form>
									<?php if ( get_option("vh_theme_version") == "SkyEstate" ) { ?>
										<input type="hidden" name="geodir_popup_post_id" value="<?php echo $post->ID?>">
										<div class="geodir_display_popup_forms"></div>
										<a href="javascript:void(0)" class="wpb_button wpb_btn-primary wpb_regularsize vh_b_send_inquiry single-listing-booknow"><?php _e("Send message", "vh"); ?></a>
									<?php } else { ?>
										<a href="javascript:void(0)" class="wpb_button wpb_btn-primary wpb_regularsize single-listing-booknow"><?php _e("Book now!", "vh"); ?></a>
									<?php } ?>
									<?php if ( get_option("vh_theme_version") == "SkyVacation" ) { ?>
										<div class="single-listing-info">
											<?php
											$postquery = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix .  "geodir_" . $post_type . "_detail WHERE post_status = 'publish' AND post_id=%s", $post->ID);
											$postqueryresults = $wpdb->get_results($postquery);
											if ( isset($postqueryresults['0']->geodir_listing_price) ) {
												$listing_price = $postqueryresults['0']->geodir_listing_price;
											} else {
												$listing_price = 0;
											}
											if ( isset($_REQUEST["preview"]) ) {
												$listing_price = $_REQUEST["geodir_listing_price"];
											}
											?>
											<span class="single-listing-text small"><?php echo get_option('vh_currency_symbol'); ?><span class="per-night" itemprop="priceRange"><?php echo $listing_price; ?> </span><?php _e("per night", "vh"); ?></span>
											<span class="single-listing-text"><?php echo get_option('vh_currency_symbol'); ?><span class="for-selected">0</span> <?php _e("for selected dates", "vh"); ?></span>
										</div>
									<?php } else { ?>
										<div class="single-listing-info">
											<?php
											$postquery = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix .  "geodir_" . $post_type . "_detail WHERE post_status = 'publish' AND post_id=%s", $post->ID);
											$postqueryresults = $wpdb->get_results($postquery);
											if ( isset($_REQUEST["preview"]) ) {
												$postqueryresults = $_REQUEST["geodir_listing_price"];
											}
											if ( isset($postqueryresults['0']->geodir_listing_price) ) {
												$listing_price = $postqueryresults['0']->geodir_listing_price;
											} else {
												$listing_price = 0;
											}
											?>
											<span class="single-listing-text normal"><?php echo get_option('vh_currency_symbol').$listing_price; ?></span>
										</div>
									<?php } ?>
								</div>
							</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if ( ( !is_page_template('template-frontpage.php' ) && !in_array($post_type, $geodir_post_types) && !isset($_REQUEST["preview"]) && !isset($_GET["geodir_search"]) && !isset($_GET["gd_placecategory"]) && !isset($_GET["geodir_dashbord"]) ) && ( function_exists('geodir_is_page') && !geodir_is_page('location') ) ) { ?>
						<div class="header-background-img"><img src="<?php echo $header_bg; ?>" alt="Header bg" /></div>
					<?php } ?>
					<?php if ( isset($_GET["geodir_dashbord"]) && !is_404() ) { ?>
						<div id="main_header_bg">
							<?php
							if ( isset($_COOKIE['vh_user_location']) ) {
								$user_location = explode('/', $_COOKIE['vh_user_location']);
								echo get_header_bg_image($user_location['0'], false, true);
							} else {
								$temp_country = get_option('vh_default_country');
								if ( $temp_country == '' ) {
									$temp_country = 'France';
								}
								echo get_header_bg_image($temp_country, false, true);
							}
							?>
						</div>
						<div id="main_header_image" class="author">
						
						</div>
						<div class="author-dash-image">
							<?php
							$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
							echo get_avatar($author->ID, 135); ?>
						</div>
						<div class="author-dash-info">
							<span class="author-name">
								<?php
									$author_id = get_query_var( 'author' );
									echo get_the_author_meta( 'display_name', $author_id );
								?>
							</span>
							<span class="author-listings">
								<?php
								$listing_count = 0;
									foreach ($geodir_post_types as $custom_post_value) {
										$listing_count = $listing_count+intval(vh_get_user_listings( $author_id, $custom_post_value));
									}
									echo $listing_count.' '.__('listings', 'vh');
								?>
							</span>
						</div>
					<?php } ?>
					<?php if ( is_page_template('template-frontpage.php') && !isset($_GET["geodir_signup"]) && function_exists('geodir_is_plugin_active') && !isset($_GET['pay_action']) ) { ?>
					<div class="header-search-container">
						<div id="main_header_bg">
							<?php
							if ( get_option('vh_header_search_map') == 'false' || get_option('vh_header_search_map') == false ) {
								if ( isset($_COOKIE['vh_user_location']) ) {
									$user_location = explode('/', $_COOKIE['vh_user_location']);
									echo get_header_bg_image($user_location['0'], false, true);
								} else {
									$temp_country = get_option('vh_default_country');
									if ( $temp_country == '' ) {
										$temp_country = 'France';
									}
									echo get_header_bg_image($temp_country, false, true);
								}
							} else {
								$width = empty($instance['width']) ? '0' : apply_filters('widget_width', $instance['width']);
								$height = empty($instance['heigh']) ? '0' : apply_filters('widget_heigh', $instance['heigh']);
								if ( get_option('vh_home_map_type') == false ) {
									$home_map_type = 'ROADMAP';
								} else {
									$home_map_type = get_option('vh_home_map_type');
								}
								$maptype = $home_map_type;
								$zoom = empty($instance['zoom']) ? '15' : apply_filters('widget_zoom', $instance['zoom']);
								$autozoom = empty($instance['autozoom']) ? '' : apply_filters('widget_autozoom', $instance['autozoom']);
								$child_collapse = empty($instance['child_collapse']) ? '0' : apply_filters('widget_child_collapse', $instance['child_collapse']);
								if ( get_option('vh_home_map_scrolling') == false || get_option('vh_home_map_scrolling') == 'false' ) {
									$home_map_scrolling = '0';
								} else {
									$home_map_scrolling = '1';
								}
								$scrollwheel = $home_map_scrolling;
								$map_args = array();

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
								$map_args['enable_location_filters'] = apply_filters('geodir_home_map_enable_location_filters', true);
								$map_args['enable_jason_on_load'] = false;
								$map_args['enable_marker_cluster'] = false;
								$map_args['enable_map_resize_button'] = true;
								$map_args['map_class_name'] = 'geodir-map-listing-page';
								$map_args['map_canvas_name'] = 'geodir_map_v3_home_map_102';
								
								$is_geodir_home_map_widget = true;
								$map_args['is_geodir_home_map_widget'] = $is_geodir_home_map_widget;
								geodir_draw_map($map_args);
							}
							?>
						</div>
						<div id="main_header_image" >
							<img class="slide-item" src="<?= get_template_directory_uri() ?>/images/No.0.jpeg" />
							<img class="slide-item" src="<?= get_template_directory_uri() ?>/images/No.1.jpeg" />
							<img class="slide-item" src="<?= get_template_directory_uri() ?>/images/No.2.jpeg" />
							<img class="slide-item slide-item-189px" src="<?= get_template_directory_uri() ?>/images/No.3.jpeg" />
							<img class="slide-item" src="<?= get_template_directory_uri() ?>/images/No.4.jpeg" />
							<img class="slide-item" src="<?= get_template_directory_uri() ?>/images/No.5.jpeg" />
							
														
						</div>
						<div class="welcome-container">
							<div id="welcome">
								<div class="va-middle">
									<h3 class="head">Welcome Home</h3>
									<p>Rent unique places to stay from local hosts in 190+ countries.</p>
								</div>
							</div>
						</div>
						<div class="header-search-form">
							<div class="header-form-container">
								<?php
									$listing_categories = get_terms('gd_placecategory');
									if ( !is_wp_error($listing_categories) ) {
										$listing_category = $listing_categories['0']->name;
									} else {
										$listing_category = '';
									}
								?>

								<?php vh_get_header_search_field(); ?>

								<a href="javascript:void(0)" class="wpb_button wpb_btn-warning icon-search" id="header-submit"></a>
								<?php if ( get_option("vh_theme_version") == "SkyEstate" ) { ?>
									<div class="clearfix"></div>
									<div id="header-more-options">
										<div class="header-more-left">
											<input id="header-more-max-price" type="hidden" value="<?php echo vh_get_geodir_max_price(); ?>">
											<div class="filter-field filter-price">
												<div class="filter-left">
													<span class="filter-text"><?php _e("Rent Price", "vh"); ?></span>
													<span class="filter-second-text"><?php _e("Per month", "vh"); ?></span>
												</div>
												<div class="filter-right">
													<span class="range-slider-min"><?php echo get_option('vh_currency_symbol'); ?>0</span>
													<span class="range-slider-max"><?php echo get_option('vh_currency_symbol').vh_get_geodir_max_price(); ?></span>
													<div id="header-range-price"></div>
												</div>
												<div class="clearfix"></div>
											</div>
											<div class="filter-field filter-area">
												<div class="filter-left">
													<span class="filter-text"><?php _e("Area", "vh"); ?></span>
													<span class="filter-second-text"><?php _e("Square meters", "vh"); ?></span>
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
													<span class="filter-text"><?php _e("Rooms", "vh"); ?></span>
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
													<span class="filter-text"><?php _e("Bathrooms", "vh"); ?></span>
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
													<span class="filter-text"><?php _e("Bedrooms", "vh"); ?></span>
												</div>
												<div class="filter-right">
													<span class="range-slider-min">1</span>
													<span class="range-slider-max">5+</span>
													<div id="header-range-bedrooms"></div>
												</div>
												<div class="clearfix"></div>
											</div>
										</div>
										<div class="header-more-right">
											<?php
												if ( function_exists('geodir_post_custom_fields') ) {
													$custom_fields = geodir_post_custom_fields('','custom','gd_place');
												} else {
													$custom_fields = array();
												}
												
												foreach ($custom_fields as $extra_fields) {
													if ( $extra_fields["type"] == "checkbox" && $extra_fields["cat_sort"] == "1" ) { ?>
														<div class="checkbox">
															<span class="checkbox_box"></span>
															<span class="checkbox_text"><?php echo $extra_fields["site_title"]; ?></span>
															<input class="more-options-checkbox" type="checkbox" value="<?php echo $extra_fields["htmlvar_name"]; ?>" style="display: none">
														</div>
												<?php }
												}
											?>
											<div class="clearfix"></div>
										</div>
										<div class="clearfix"></div>
									</div>
									<span class="header-more-button icon-angle-down">More options</span>
									<div class="clearfix"></div>
								<?php } ?>
								<input type="hidden" id="header-post-type" value="<?php echo get_option('geodir_default_map_search_pt', 'gd_place'); ?>">
								<span id="form-loading-effect"></span>
							</div>
						</div>
						<?php if ( function_exists('geodir_cp_create_default_fields') || defined('GDEVENTS_VERSION') ) { ?>
						<div class="header-custom-posts">
							<?php
							if ( function_exists('vh_get_custom_post_types') ) {
								vh_get_custom_post_types();
							}
							?>
						</div>
						<?php } ?>
					</div>
					<div id="header-current-location">
							<?php
							if ( isset($_COOKIE['vh_user_location']) ) {
								$user_location = explode('/', $_COOKIE['vh_user_location']);
								$country_listings = get_header_bg_info($user_location['1'], $user_location['0'], false, true);
							} else {
								$temp_country = get_option('vh_default_country');
								if ( $temp_country == '' ) {
									$temp_country = 'France';
								}
								$temp_city = get_option('vh_default_city');
								if ( $temp_city == '' ) {
									$temp_city = 'Paris';
								}
								$temp_location = $temp_country."/".$temp_city;
								$user_location = explode('/', $temp_location);
								$country_listings = get_header_bg_info($user_location['1'], $user_location['0'], false, true);
							}
							?>
						<span id="current_city"><?php echo $user_location['1']; ?></span>
						<span id="current_country"><?php echo $user_location['0']; ?></span>
						<div class="header-current-info">
							<span id="current_listing_count"><?php echo $country_listings; ?></span>
						</div>
					</div>
				<?php } ?>
				</header><!--end of header-->
				<div id="vh_loading_effect"></div>
				<div class="clearfix"></div>
				<?php
					wp_reset_postdata();
					$layout_type = get_post_meta(get_the_id(), 'layouts', true);

					if ( is_archive() || is_search() || is_404() || ( $post_type == 'tribe_events' && !is_single() ) ) {
						$layout_type = 'full';
					} else if ( is_home() ) {

						// Get the ID of your posts page
						$id = get_option('page_for_posts');

						$layout_type = get_post_meta($id, 'layouts', true) ? get_post_meta($id, 'layouts', true) : 'full';
					} elseif (empty($layout_type)) {
						$layout_type = get_option('vh_layout_style') ? get_option('vh_layout_style') : 'full';
					}

					switch ($layout_type) {
						case 'right':
							define('LAYOUT', 'sidebar-right');
							break;
						case 'full':
							define('LAYOUT', 'sidebar-no');
							break;
						case 'left':
							define('LAYOUT', 'sidebar-left');
							break;
					}