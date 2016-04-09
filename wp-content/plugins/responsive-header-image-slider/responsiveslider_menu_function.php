<?php
add_action('admin_menu', 'register_responsive_slider_submenu_page');
function register_responsive_slider_submenu_page() {
	add_submenu_page( 'edit.php?post_type=sp_responsiveslider', 'Slider Designs', 'Slider Designs', 'manage_options', 'responsive_slider-submenu-page', 'register_responsive_slider_page_callback' );

}
function register_responsive_slider_page_callback() {

	$result ='<div class="wrap"><div id="icon-tools" class="icon32"></div><h2 style="padding:15px 0">Slider Designs</h2></div>
	<div class="medium-12 wpcolumns"><h1>Buy Pro Designs of WP Slick Slider and Carousel</h1>
	<em><strong>WP Responsive header image slider is replaced with  WP Slick Slider and Carousel</strong> </em>
				<p><a href="http://wponlinesupport.com/wp-plugin/sp-responsive-header-image-slider/" target="_blank"><img  src="'.plugin_dir_url( __FILE__ ).'images/slick-slider.png"></a></p></div>
				<div class="medium-12 wpcolumns"><h3>Free Designs</h3></div>
				<div class="medium-6 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/design-1.jpg"><p><code>[sp_responsiveslider design="design-1"]</code></p></div></div>
				<div class="medium-6 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/design-2.jpg"><p><code>[sp_responsiveslider design="design-2"]</code></p></div></div>
				<div class="medium-6 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/design-3.jpg"><p><code>[sp_responsiveslider design="design-3"]</code></p></div></div>
				<div class="medium-6 wpcolumns"><div class="postdesigns"><strong> Complete shortcode is: </strong><p><code>[sp_responsiveslider design="design-1" cat_id="2" width="1024" first_slide="1" height="300" effect="fade" pagination="true" navigation="true" speed="3000" autoplay="true" autoplay_interval="3000"]</code></p></div></div>
				
				<div class="medium-12 wpcolumns"><h1>Buy Pro Designs of WP Slick Slider and Carousel</h1>
				<em><strong>WP Responsive header image slider is replaced with  WP Slick Slider and Carousel</strong> </em>
				<p><a href="http://wponlinesupport.com/wp-plugin/sp-responsive-header-image-slider/" target="_blank"><img  src="'.plugin_dir_url( __FILE__ ).'images/slick-slider.png"></a></p></div>
				<div class="medium-12 wpcolumns"><h3>Complete shortcode for Slider:</h3><p><code>[slick-slider  design="prodesign-1" category="8" show_content="true" limit="5"
 dots="true" arrows="true" autoplay="true" sliderheight="400" autoplay_interval="5000" speed="1000" effect="false" loop="true"]</code></p></div>
				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-1.jpg"><p><code>[slick-slider design="prodesign-1"]</code></p></div></div>
				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-2.jpg"><p><code>[slick-slider design="prodesign-2"]</code></p></div></div>
				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-3.jpg"><p><code>[slick-slider design="prodesign-3"]</code></p></div></div>
				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-4.jpg"><p><code>[slick-slider design="prodesign-4"]</code></p></div></div>
				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-5.jpg"><p><code>[slick-slider design="prodesign-5"]</code></p></div></div>
				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-6.jpg"><p><code>[slick-slider design="prodesign-6"]</code></p></div></div>
				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-7.jpg"><p><code>[slick-slider design="prodesign-7"]</code></p></div></div>
				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-8.jpg"><p><code>[slick-slider design="prodesign-8"]</code></p></div></div>
				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-9.jpg"><p><code>[slick-slider design="prodesign-9"]</code></p></div></div>
				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-10.jpg"><p><code>[slick-slider design="prodesign-10"]</code></p></div></div>
				<div class="medium-12 wpcolumns">
				<h3>Complete shortcode for Carousel Slider:</h3><p><code>[slick-carousel-slider  design="prodesign-11" category="8" limit="5"
 slidestoshow="4" slidestoscroll="1" dots="true" image_size="large" show_content="true" arrows="true" autoplay="true"  autoplay_interval="5000" speed="1000" centermode="true" variablewidth="true" loop="true"]</code></p></div>

				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-11.jpg"><p><code>[slick-carousel-slider design="prodesign-11"]</code></p></div></div>
				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-12.jpg"><p><code>[slick-carousel-slider design="prodesign-12"]</code></p></div></div>
				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-13.jpg"><p><code>[slick-carousel-slider design="prodesign-13"]</code></p></div></div>
				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-14.jpg"><p><code>[slick-carousel-slider design="prodesign-14"]</code></p></div></div>
				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-15.jpg"><p><code>[slick-carousel-slider design="prodesign-15"]</code></p></div></div>
				<div class="medium-4 wpcolumns"><div class="postdesigns"><img  src="'.plugin_dir_url( __FILE__ ).'images/prodesign-16.jpg"><p><code>[slick-carousel-slider design="prodesign-16"]</code></p></div></div>
				<div class="medium-12 wpcolumns"><h2>Check the demo</h2>
				<p><strong>Check Demo Link</strong> <a href="http://demo.wponlinesupport.com/prodemo/pro-wp-slick-slider-and-carousel-demo/" target="_blank">Pro WP Slick Slider and Image Carousel</a></div>';

	echo $result;

}

function register_responsive_slider_admin_style(){
	?>
	<style type="text/css">
	.postdesigns{-moz-box-shadow: 0 0 5px #ddd;-webkit-box-shadow: 0 0 5px#ddd;box-shadow: 0 0 5px #ddd; background:#fff; padding:10px;  margin-bottom:15px;}
	.wpcolumn, .wpcolumns {-webkit-box-sizing: border-box;-moz-box-sizing: border-box;  box-sizing: border-box;}
.postdesigns img{width:100%; height:auto;}
@media only screen and (min-width: 40.0625em) {  
  .wpcolumn,
  .wpcolumns {position: relative;padding-left:10px;padding-right:10px;float: left; }
  .medium-1 {    width: 8.33333%; }
  .medium-2 {    width: 16.66667%; }
  .medium-3 {    width: 25%; }
  .medium-4 {    width: 33.33333%; }
  .medium-5 {    width: 41.66667%; }
  .medium-6 {    width: 50%; }
  .medium-7 {    width: 58.33333%; }
  .medium-8 {    width: 66.66667%; }
  .medium-9 {    width: 75%; }
  .medium-10 {    width: 83.33333%; }
  .medium-11 {    width: 91.66667%; }
  .medium-12 {    width: 100%; } 
   }
	</style>

<?php }

add_action('admin_head', 'register_responsive_slider_admin_style');

