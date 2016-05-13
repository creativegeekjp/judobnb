<?php
/**
 * Sky functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 */

// Define file directories
define('VH_HOME', get_template_directory());
define('VH_FUNCTIONS', get_template_directory() . '/functions');
define('VH_GLOBAL', get_template_directory() . '/functions/global');
define('VH_WIDGETS', get_template_directory() . '/functions/widgets');
define('VH_CUSTOM_PLUGINS', get_template_directory() . '/functions/plugins');
define('VH_ADMIN', get_template_directory() . '/functions/admin');
define('VH_ADMIN_IMAGES', get_template_directory_uri() . '/functions/admin/images');
define('VH_METABOXES', get_template_directory() . '/functions/admin/metaboxes');
define('VH_SIDEBARS', get_template_directory() . '/functions/admin/sidebars');

// Define theme URI
define('VH_URI', get_template_directory_uri() .'/');
define('VH_GLOBAL_URI', VH_URI . 'functions/global');

define('THEMENAME', 'Sky');
define('SHORTNAME', 'VH');
define('VH_HOME_TITLE', 'Front page');
define('VH_DEVELOPER_NAME_DISPLAY', 'Cohhe themes');
define('VH_DEVELOPER_URL', 'http://cohhe.com');

define('TESTENVIRONMENT', FALSE);

add_action('after_setup_theme', 'vh_setup');
add_filter('widget_text', 'do_shortcode');

// Set max content width
if (!isset($content_width)) {
	$content_width = 900;
}

if (!function_exists('vh_setup')) {

	function vh_setup() {

		// Load Admin elements
		require_once(VH_ADMIN . '/theme-options.php');
		require_once(VH_ADMIN . '/admin-interface.php');
		require_once(VH_ADMIN . '/menu-custom-field.php');
		require_once(VH_FUNCTIONS . '/get-the-image.php');
		require_once(VH_METABOXES . '/layouts.php');
		require_once(VH_METABOXES . '/contact_map.php');
		require_once(VH_METABOXES . '/donations.php');
		require_once(VH_SIDEBARS . '/multiple_sidebars.php');
		require_once(VH_FUNCTIONS . '/installer/importer/widgets-importer.php');
		require_once(VH_FUNCTIONS . '/installer/functions-themeinstall.php');

		// Widgets list
		$widgets = array (
			VH_WIDGETS . '/contactform.php',
			VH_WIDGETS . '/googlemap.php',
			VH_WIDGETS . '/social_links.php',
			VH_WIDGETS . '/advertisement.php',
			VH_WIDGETS . '/recent-posts-plus.php',
			VH_WIDGETS . '/fast-flickr-widget.php',
		);

		// Load Widgets
		load_files($widgets);

		// Load global elements
		require_once(VH_GLOBAL . '/wp_pagenavi/wp-pagenavi.php');

		// if (file_exists(VH_CUSTOM_PLUGINS . '/landing-pages/landing-pages.php')) {
		// 	require_once(VH_CUSTOM_PLUGINS . '/landing-pages/landing-pages.php');
		// }

		// TGM plugins activation
		require_once(VH_FUNCTIONS . '/tgm-activation/class-tgm-plugin-activation.php');

		// Extend Visual Composer
		if (defined('WPB_VC_VERSION')) {
			require_once(VH_FUNCTIONS . '/visual_composer_extended.php');
		}

		// Shortcodes list
		$shortcodes = array (
			//VH_SHORTCODES . '/test.php'
		);

		// Load shortcodes
		load_files($shortcodes);

		// This theme styles the visual editor with editor-style.css to match the theme style.
		add_editor_style();

		// Add default posts and comments RSS feed links to <head>.
		add_theme_support('automatic-feed-links');

		// If theme is activated them send to options page
		// if (is_admin() && isset($_GET['activated'])) {
		// 	wp_redirect(admin_url('admin.php?page=themeoptions'));
		// }
	}
}

function vh_localization() {
	$lang = get_template_directory() . '/languages';
	load_theme_textdomain('vh', $lang);
}
add_action('after_setup_theme', 'vh_localization');

function vh_register_widgets () {
	register_sidebar( array(
		'name'          => __( 'Normal', 'vh' ),
		'id'            => 'sidebar-5',
		'class'         => 'normal',
		'before_widget' => '<div class="widget">',
		'after_widget'  => '<div class="clearfix"></div></div>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>'
	) );

	register_sidebar( array(
		'name'          => __( 'Listings', 'vh' ),
		'id'            => 'sidebar-6',
		'class'         => 'normal',
		'before_widget' => '<div class="widget">',
		'after_widget'  => '<div class="clearfix"></div></div>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>'
	) );
	
	register_sidebar( array(
		'name'          => __( 'Footer Full Width', 'vh' ),
		'id'            => 'sidebar-7',
		'class'         => 'normal',
		'before_widget' => '<div class="widget">',
		'after_widget'  => '<div class="clearfix"></div></div>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>'
	) );
}
add_action( 'widgets_init', 'vh_register_widgets' );

function vh_unregister_geodir_widgets() {
	unregister_widget('geodir_listing_slider_widget');
	unregister_widget('geodir_popular_post_category');
	unregister_widget('geodir_popular_postview');
	unregister_widget('geodir_related_listing_postview');
	unregister_widget('geodir_recent_reviews_widget');
	unregister_widget('geodir_homepage_map');
	unregister_widget('geodir_map_listingpage');
	unregister_widget('geodir_loginwidget');
	unregister_widget('geodir_social_like_widget');
	unregister_widget('geodirsubscribeWidget');
	unregister_widget('geodiradvtwidget');
	unregister_widget('GeodirFlickrWidget');
	unregister_widget('geodir_twitter');
	unregister_widget('geodir_advance_search_widget');
	unregister_widget('geodir_popular_location');
	unregister_widget('geodir_location_neighbourhood');
	unregister_widget('geodir_neighbourhood_posts');
	unregister_widget('geodir_location_description');
	unregister_widget('geodir_near_me_button_widget');
}
add_action('widgets_init', 'vh_unregister_geodir_widgets');

function vh_unregister_geodir_sidebars() {
	unregister_sidebar('geodir_home_top');
	unregister_sidebar('geodir_home_content');
	unregister_sidebar('geodir_home_right');
	unregister_sidebar('geodir_home_left');
	unregister_sidebar('geodir_home_bottom');
	unregister_sidebar('geodir_listing_top');
	unregister_sidebar('geodir_listing_left_sidebar');
	unregister_sidebar('geodir_listing_right_sidebar');
	unregister_sidebar('geodir_listing_bottom');
	unregister_sidebar('geodir_search_top');
	unregister_sidebar('geodir_search_left_sidebar');
	unregister_sidebar('geodir_search_right_sidebar');
	unregister_sidebar('geodir_search_bottom');
	unregister_sidebar('geodir_detail_top');
	unregister_sidebar('geodir_detail_sidebar');
	unregister_sidebar('geodir_detail_bottom');
	unregister_sidebar('geodir_author_top');
	unregister_sidebar('geodir_author_left_sidebar');
	unregister_sidebar('geodir_author_right_sidebar');
	unregister_sidebar('geodir_author_bottom');
	unregister_sidebar('geodir_add_listing_sidebar');
}
add_action('widgets_init', 'vh_unregister_geodir_sidebars');

// Force Visual Composer to initialize as "built into the theme". This will hide certain tabs under the Settings->Visual Composer page
if(function_exists('vc_set_as_theme')) vc_set_as_theme();

// Add quote post format support
add_theme_support( 'post-formats', array( 'quote' ) );

// Load Widgets
function load_files ($files) {
	foreach ($files as $file) {
		require_once($file);
	}
}

if (function_exists('add_theme_support')) {
	add_theme_support('post-thumbnails');

	// Default Post Thumbnail dimensions
	set_post_thumbnail_size(150, 150);
}

function the_excerpt_max_charlength($charlength) {
	$excerpt = get_the_excerpt();
	$charlength++;

	if ( mb_strlen( $excerpt ) > $charlength ) {
		$subex = mb_substr( $excerpt, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			echo mb_substr( $subex, 0, $excut );
		} else {
			echo $subex;
		}
		echo '...';
	} else {
		echo $excerpt;
	}
}

function vh_comment_count( $count ) {
	if ( ! is_admin() ) {
		global $id;

		$comments = get_comments('status=approve&post_id=' . $id);
		$separate_comments = separate_comments($comments);

		$comments_by_type = &$separate_comments;
		return count($comments_by_type['comment']);
	} else {
		return $count;
	}
}
add_filter('get_comments_number', 'vh_comment_count', 0);

function tgm_cpt_search( $query ) {
	if ( $query->is_search && ( get_post_type() != 'forum' && get_post_type() != 'topic' ) ) {
		if ( isset($_GET["geodir_search"]) ) {
			$query->set( 'post_type', get_geodir_custom_post_types() );
		} elseif ( !is_admin() ) {
			$post_types = array_merge( array( 'post' ), get_geodir_custom_post_types() );
			$query->set( 'post_type', $post_types );
		}
	}
		
	return $query;
}
add_filter( 'pre_get_posts', 'tgm_cpt_search' );

// Add new image sizes
if ( function_exists('add_image_size')) {
	add_image_size('featured-property-thin', 293, 492, true); // large-image image size
	add_image_size('featured-property-square', 291, 246, true); // large-image image size
	add_image_size('featured-property-wide', 584, 246, true); // large-image image size
	add_image_size('popular-destinations-square', 586, 492, true); // large-image image size
	add_image_size('open-listing', 1200, 611, true); // large-image image size
	add_image_size('main-header-image', 1400, 747, true); // large-image image size

	# Gallery image Cropped sizes
	add_image_size('gallery-large', 270, 270, true); // gallery-large gallery size
	add_image_size('gallery-medium', 125, 125, true); // gallery-medium gallery size
}

// Public JS scripts
if (!function_exists('vh_scripts_method')) {
	function vh_scripts_method() {
		wp_register_script( 'prettyphoto', plugins_url() . '/js_composer/assets/lib/prettyphoto/js/jquery.prettyPhoto.js', array( 'jquery' ), '', true);

		wp_enqueue_script('jquery');
		wp_enqueue_script('prettyphoto', array('jquery'), '', TRUE);
		wp_enqueue_script('master', get_template_directory_uri() . '/js/master.js', array('jquery', 'prettyphoto'), '', TRUE);
		wp_enqueue_script('isotope', get_template_directory_uri() . '/js/jquery.isotope.min.js', array('jquery', 'master'), '', TRUE);
		// wp_enqueue_script('jquery-ui-tabs');

		wp_enqueue_script('jquery.date', get_template_directory_uri() . '/js/date.js', array('jquery'), '', TRUE);

		// wp_deregister_script('geodirectory-googlemap-script');
		wp_enqueue_script('geodirectory-googlemap-script', '//maps.googleapis.com/maps/api/js?sensor=false', array(), '3', false);

		// wp_deregister_style('geodirectory-font-awesome');
		
		// wp_enqueue_script('geoplugin', '//www.geoplugin.net/javascript.gp', array(), '3', TRUE);
		
		wp_enqueue_script('jquery.pushy', get_template_directory_uri() . '/js/nav/pushy.js', array('jquery'), '', TRUE);
		wp_enqueue_script('jquery.mousewheel', get_template_directory_uri() . '/js/jquery.mousewheel.min.js', array('jquery'), '', TRUE);
		wp_enqueue_script('jquery.jcarousel', get_template_directory_uri() . '/js/jquery.jcarousel.pack.js', array('jquery'), '', TRUE);

		wp_enqueue_script('jquery.infobox', get_template_directory_uri() . '/js/infobox.js', array('jquery'), '', TRUE);

		wp_enqueue_script( 'jquery-ui-datepicker' );
		
		if ( function_exists('geodir_is_plugin_active') ) {
			wp_enqueue_script( 'jquery-ui-dialog' );
		}

		$geodir_post_types = get_geodir_custom_post_types();
		if ( in_array(get_post_type(), $geodir_post_types) ) {
			wp_enqueue_script('froogaloop', '//f.vimeocdn.com/js/froogaloop2.min.js', array('master'), '', TRUE);
		}
		
		// if ( !wp_script_is('geodir-autocompleter-js') ) {
		// 	wp_enqueue_script( 'jquery-ui-autocomplete' );
		// 	$geo_advanced_search = 'false';
		// } else {
		// 	$geo_advanced_search = 'true';
		// }

		if ( wp_script_is('geodir-autocompleter-js') ) {
			wp_dequeue_script( 'geodir-autocompleter-js' );
			$geo_advanced_search = 'false';
		} else {
			$geo_advanced_search = 'true';
		}

		if ( function_exists('bp_is_blog_page') ) {
			if ( bp_is_blog_page() ) {
				wp_enqueue_script( 'jquery-ui-autocomplete' );
			}
		} else {
			wp_enqueue_script( 'jquery-ui-autocomplete' );
		}

		if ( wp_script_is('geodir-autocompleter-js') ) {
			wp_enqueue_script('geodirectory-lightbox-jquery');
		}

		wp_enqueue_script('jquery.tags', get_template_directory_uri() . '/js/tag-it.min.js', array('jquery'), '', TRUE);

		wp_enqueue_script('jquery.cookie', get_template_directory_uri() . '/js/jquery.cookie.js', array('jquery'), '', TRUE);
		wp_enqueue_script('jquery.debouncedresize', get_template_directory_uri() . '/js/jquery.debouncedresize.js', array('jquery'), '', TRUE);

		wp_enqueue_script('jquery.modernizr', get_template_directory_uri() . '/js/modernizr.custom.js', array('jquery'), '', FALSE);

		wp_enqueue_script("jquery-effects-core");

		wp_enqueue_script('jquery.timepicker', get_template_directory_uri() . '/js/jquery.timepicker.min.js', array('jquery-ui-datepicker', 'jquery-ui-slider'), '', FALSE);

		if ( function_exists('geodir_is_plugin_active') ) {
			wp_dequeue_script( 'geodir-map-widget' );
			wp_enqueue_script( 'geodir-map-widget', get_template_directory_uri() . '/js/map.js', array('jquery'), '', TRUE);
			wp_dequeue_script( 'geodirectory-listing-validation-script' );
			wp_enqueue_script( 'geodirectory-listing-validation-scripts', get_template_directory_uri() . '/js/listing_validation.js', array('jquery'), '', TRUE);
			wp_enqueue_script( 'richmarker', get_template_directory_uri() . '/js/richmarker.js', array('jquery'), '', TRUE);
			wp_deregister_script( 'geodirectory-goMap-script' );
			wp_enqueue_script( 'geodirectory-goMap-script', get_template_directory_uri() . '/js/goMap.js', array(), '', TRUE);
		}

		if ( is_singular() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
		wp_localize_script( 'master', 'ajax_login_object', array( 
			'ajaxurl'        => admin_url( 'admin-ajax.php' ),
			'redirecturl'    => home_url(),
			'loadingmessage' => __('Sending user info, please wait...', "vh" ),
			'registermessage' => __('A password will be emailed to you for future use', "vh" )
		));

		if ( function_exists('geodir_is_page') ) {
			$geo_is_location = geodir_is_page('location');
			$geo_is_preview = geodir_is_page('preview');
		} else {
			$geo_is_location = false;
			$geo_is_preview = false;
		}

		wp_localize_script( 'master', 'my_ajax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'blog_url' => get_site_url(),
			'currency_symbol' => get_option('vh_currency_symbol'),
			'def_country' => get_option('vh_default_country'),
			'def_city' => get_option('vh_default_city'),
			'geo_advanced_search' => $geo_advanced_search,
			'home_map_zoom' => get_option('vh_home_map_zoom', '12'),
			'home_map_autozoom' => get_option('vh_home_map_autozoom', 'false'),
			'map_markers' => get_option('vh_map_markers', 'false'),
			'marker_icon' => get_geodir_category_icons(),
			'marker_def_icon' => get_option('geodir_default_marker_icon'),
			'is_location' => $geo_is_location,
			'default_search' => get_option('geodir_default_map_search_pt', 'gd_place'),
			'header_map' => get_option('VH_header_search_map', 'false'),
			'autocomplete' => get_option('geodir_enable_autocompleter', '1'),
			'autosubmit' => get_option('geodir_autocompleter_autosubmit', '0'),
			'disable_geoloc' => get_option('geodir_autolocate_disable', '0')
		));

		wp_localize_script( 'geodir-map-widget', 'vh_main', array(
			'post_id' => get_the_ID(),
			'post_info' => vh_get_post_info( get_the_ID(), get_post_type() ),
			'post_type' => get_post_type( get_the_ID() ),
			'maptitle' => __('Preview', 'vh'),
			'mapcontent' => __('Once you publish your listing, markers will be visible here.', 'vh'),
			'is_preview' => $geo_is_preview
		));
	}
}
add_action('wp_enqueue_scripts', 'vh_scripts_method');

// Admin JS scripts

function vh_admin_enqueue() {
	wp_enqueue_script('jquery.tags', get_template_directory_uri() . '/js/tag-it.min.js', array('jquery', 'jquery-ui-autocomplete'), '', TRUE);
}
add_action( 'admin_enqueue_scripts', 'vh_admin_enqueue' );

// Public CSS files
if (!function_exists('vh_style_method')) {
	function vh_style_method() {
		wp_enqueue_style('master-css', get_template_directory_uri() . '/style.css');
		wp_enqueue_style('vh-normalize', get_template_directory_uri() . '/css/normalize.css');
		wp_enqueue_style('js_composer_front');
		wp_enqueue_style('prettyphoto');
		wp_enqueue_style('vh-responsive', get_template_directory_uri() . '/css/responsive.css');
		wp_enqueue_style('pushy', get_template_directory_uri() . '/css/nav/pushy.css');
		wp_enqueue_style('component', get_template_directory_uri() . '/css/component.css');
		wp_enqueue_style('slide', get_template_directory_uri() . '/css/slide.css');
		

		// Load google fonts
		if (file_exists(TEMPLATEPATH . '/css/gfonts.css')) {
			wp_enqueue_style('front-gfonts', get_template_directory_uri() . '/css/gfonts.css');
		}

		/* Color scheme css */
		wp_enqueue_style('color-schemes-green', get_template_directory_uri() . '/css/color-schemes/green.css');
		wp_enqueue_style('color-schemes-red', get_template_directory_uri() . '/css/color-schemes/red.css');

		if ( get_option('vh_website_logo_resize') == 'true' ) {
			wp_add_inline_style('master-css', '.top-header .logo a img { max-height: 70px; }');
		}
	}
}
add_action('wp_enqueue_scripts', 'vh_style_method');

// Admin CSS
function vh_admin_css() {
	wp_enqueue_style( 'vh-admin-css', get_template_directory_uri() . '/functions/admin/css/wp-admin.css' );
	wp_enqueue_style('jquery.tags', get_template_directory_uri() . '/css/jquery.tagit.css');
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
}
add_action('admin_head','vh_admin_css');

function ajax_login() {

	// First check the nonce, if it fails the function will break
	check_ajax_referer( 'ajax-login-nonce', 'security' );

	// Nonce is checked, get the POST data and sign user on

	if ( $_POST['rememberme'] == "1" ) {
		$remember = true;
	} else {
		$remember = false;
	}

	$info                  = array();
	$info['user_login']    = sanitize_user($_POST['username']);
	$info['user_password'] = $_POST['password'];
	$info['remember']      = $remember;

	$user_signon = wp_signon( $info, false );
	if ( is_wp_error($user_signon) ) {
		$error = $user_signon->get_error_codes();
		if ( in_array('invalid_username', $error) ) {
			echo json_encode(array('loggedin' => false, 'message' => __('Invalid username', "vh" ), 'for_input' => 'username'));
		} elseif ( in_array('incorrect_password', $error) ) {
			echo json_encode(array('loggedin' => false, 'message' => __('Invalid password', "vh" ), 'for_input' => 'password'));
		} elseif ( in_array('empty_password', $error) ) {
			echo json_encode(array('loggedin' => false, 'message' => __('Enter password', "vh" ), 'for_input' => 'password'));
		} elseif ( in_array('empty_username', $error) ) {
			echo json_encode(array('loggedin' => false, 'message' => __('Enter username', "vh" ), 'for_input' => 'username'));
		} else {
			echo json_encode(array('loggedin' => false, 'message' => $error));
		}
	} else {
		echo json_encode(array('loggedin' => true, 'message' => __('Login successful, redirecting...', "vh" )));
	}

	die();
}

// Enable the user with no privileges to run ajax_login() in AJAX
add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );

function ajax_register() {

	// First check the nonce, if it fails the function will break
	check_ajax_referer( 'ajax-register-nonce', 'regsecurity' );
		
	$username = sanitize_user($_POST['fullname']);
	$email = sanitize_email( $_POST['email']);
	
	// Register the user
	$user_register = register_new_user( $username, $email );
	if ( is_wp_error($user_register) ) {
		$error  = $user_register->get_error_codes();
		if( in_array('empty_user_login', $error) )
			echo json_encode(array('loggedin'=>false, 'message'=>__('Enter your username', 'vh'), 'for_input' => 'username'));
		elseif(in_array('username_exists',$error))
			echo json_encode(array('loggedin'=>false, 'message'=>__('Username already exists.', 'vh'), 'for_input' => 'username'));
		elseif(in_array('email_exists',$error))
			echo json_encode(array('loggedin'=>false, 'message'=>__('Email already exists.', 'vh'), 'for_input' => 'email'));
		elseif(in_array('empty_email',$error))
			echo json_encode(array('loggedin'=>false, 'message'=>__('Enter email.', 'vh'), 'for_input' => 'email'));
		elseif(in_array('empty_username',$error))
			echo json_encode(array('loggedin'=>false, 'message'=>__('Enter username.', 'vh'), 'for_input' => 'username'));
		else
			echo json_encode(array('loggedin'=>false, 'message'=>$error, 'for_input' => 'main'));
	} else {
		echo json_encode(array('loggedin'=>false, 'message'=>__('An email with your password was sent.', 'vh'), 'for_input' => 'main'));      
	}

	die();
}
add_action( 'wp_ajax_nopriv_ajax_register', 'ajax_register' );

if ( !function_exists('get_geodir_category_icons') ) {
	function get_geodir_category_icons() {
		$geo_categories = get_terms( 'gd_placecategory' );
		$categories = array();

		if ( !is_wp_error($geo_categories) ) {
			foreach ( $geo_categories as $category_value ) {
				if ( function_exists('get_tax_meta') ) {
					$cat_icon = get_tax_meta( $category_value->term_id, 'ct_cat_icon', false, 'gd_place' );
				} else {
					$cat_icon = array();
				}
				if ( isset($cat_icon['src']) ) {
					$categories[$category_value->term_id] = $cat_icon['src'];
				} else {
					$categories[$category_value->term_id] = get_option('geodir_default_marker_icon');
				}
			}
		}
		
		return $categories;
	}
}

if ( !function_exists('get_geodir_custom_post_types') ) {
	function get_geodir_custom_post_types() {
		$post_types = get_option('geodir_post_types');
		$custom_post_types = array();

		if ( !empty($post_types) ) {
			foreach ($post_types as $post_types_key => $post_types_value) {
				$custom_post_types[] = $post_types_key;
			}
		}

		return $custom_post_types;
	}
}

/* Filter categories */
function filter_categories($list) {

	$find    = '(';
	$replace = '[';
	$list    = str_replace( $find, $replace, $list );
	$find    = ')';
	$replace = ']';
	$list    = str_replace( $find, $replace, $list );

	return $list;
}
add_filter('wp_list_categories', 'filter_categories');

function c_parent_comment_counter($id) {
	global $wpdb;
	$query = $wpdb->prepare("SELECT COUNT(comment_post_id) AS count FROM $wpdb->comments WHERE `comment_approved` = 1 AND `comment_parent` = %s", $id);
	$parents = $wpdb->get_row($query);
	return $parents->count;
}

function vh_get_geodir_max_price( $post_type = 'gd_place' ) {
	global $wpdb;
	$query = "SELECT max(geodir_listing_price) FROM " . $wpdb->prefix .  "geodir_" . $post_type . "_detail WHERE post_status=\"publish\"";
	$parents = $wpdb->get_var($query);

	if ( $parents == null ) {
		$parents = 1;
	}

	return $parents;
}

add_filter('comment_reply_link', 'vh_geodir_comment_replaylink', 20 );
function vh_geodir_comment_replaylink($link){
	
	$link = '<div class="gd_comment_replaylink icon-plus-circled">'.$link.'</div>';
	
	return $link;
}

function vh_update_geodir_reviews() {
	global $post;
	$post_types = get_geodir_custom_post_types();
	$args = array(
		'post_type' => $post_types,
		'posts_per_page' => '-1'
	);

	$the_query = new WP_Query( $args );

	if( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			geodir_update_postrating(get_the_ID(), get_post_type());
		}
	}

	wp_reset_query();
	wp_reset_postdata();
}

function vh_update_geodir_ids() {
	global $wpdb;

	// Fix listing IDs
	$current_listings = $wpdb->get_results('SELECT ID, post_title FROM '.$wpdb->prefix.'posts WHERE post_type="gd_place"');
	foreach ($current_listings as $key => $value) {
		$wpdb->query('UPDATE '.$wpdb->prefix.'geodir_gd_place_detail SET post_id="'.$value->ID.'" WHERE post_title="'.$value->post_title.'"');
	}
}

function set_cluster_maps() {
	$def = get_option('widget_geodir_map_v3_home_map');
	if ( !isset($def['100']) ) {
		$def['100'] = array('autozoom' => 1, 'width' => '0', 'heigh' => '0');
	}
	if ( !isset($def['101']) ) {
		$def['101'] = array('autozoom' => 1, 'width' => '0', 'heigh' => '0');
	}
	if ( !isset($def['102']) ) {
		$def['102'] = array('autozoom' => 1, 'width' => '0', 'heigh' => '0');
	}
	update_option('widget_geodir_map_v3_home_map', $def);
}

// Custom Login Logo
function vh_login_logo() {
	$login_logo = get_option('vh_login_logo');

	if ($login_logo != false) {
		echo '
	<style type="text/css">
		#login h1 a { background-image: url("' . $login_logo . '") !important; }
	</style>';
	}
}
add_action('login_head', 'vh_login_logo');

function vh_ldc_like_counter_p( $text="Likes: ",$post_id=NULL ) {
	global $post;
	$ldc_return = '';

	if( empty($post_id) ) {
		$post_id = $post->ID;
	}

	if ( function_exists('get_post_ul_meta') ) {
		$ldc_return = "<span class='ldc-ul_cont_likes icon-heart' onclick=\"alter_ul_post_values(this,'$post_id','like')\" >".$text."<span>".get_post_ul_meta($post_id,"like")."</span></span>";
	}

	return $ldc_return;
}

if ( get_option('vh_currency_symbol') == false ) {
	update_option('vh_currency_symbol', '$');
}

function get_preview_data( $data ) {
	global $wpdb;
	$preview_data = array();
	$excluded_keys = array('geodir_fileimage_limit', 'geodir_filetotImg', 'geodir_accept_term_condition', 'geodir_spamblocker', 'geodir_filled_by_spam_bot');
	$valid_keys = array();

	$query = "SELECT htmlvar_name, field_type, site_title FROM " . $wpdb->prefix .  "geodir_custom_fields WHERE show_on_detail = '1' AND is_active = '1' AND field_icon = ''";
	$queryresults = $wpdb->get_results($query);



	foreach ($queryresults as $detail_value) {
		if ( $detail_value->htmlvar_name == 'post' ) {
			$valid_keys['post_address'] = array('type' => $detail_value->field_type, 'title' => $detail_value->site_title);
		} elseif ( $detail_value->htmlvar_name == 'gd_placecategory' ) {
			$valid_keys['post_category'] = array('type' => $detail_value->field_type, 'title' => $detail_value->site_title);
		} else {
			$valid_keys[$detail_value->htmlvar_name] = array('type' => $detail_value->field_type, 'title' => $detail_value->site_title);
		}
	}

	foreach ($data as $preview_key => $preview_value) {
		if ( array_key_exists($preview_key, $valid_keys) ) {
			$preview_data[$preview_key] = array('value' => $preview_value, 'type' => $valid_keys[$preview_key]['type'], 'title' => $valid_keys[$preview_key]['title']);
		}
	}

	return $preview_data;
}

function vh_show_new_badge( $post_id ) {
	$output = '';
	$listing_data = get_post( $post_id );
	$geodir_days_new = (int)get_option('geodir_listing_new_days');
	if (round(abs(strtotime($listing_data->post_date) - strtotime(date('Y-m-d'))) / 86400) < $geodir_days_new) {
		$output .= '<span class="new-listing-badge">'.__('New', '').'</span>';
	}
	wp_reset_query();
	wp_reset_postdata();
	return $output;
}

function vh_limit_text($text, $limit) {
	if (str_word_count($text, 0) > $limit) {
		$words = str_word_count($text, 2);
		$pos = array_keys($words);
		$text = substr($text, 0, $pos[$limit]) . '...';
	}
	return $text;
}

// Sets the post excerpt length to 40 words.
function vh_excerpt_length($length) {
	return 39;
}
add_filter('excerpt_length', 'vh_excerpt_length');

// Returns a "Continue Reading" link for excerpts
function vh_continue_reading_link() {
	return ' </p><p><a href="' . esc_url(get_permalink()) . '" class="read_more_link">' . __('Read more', 'vh') . '</a>';
}

if ( function_exists( 'gd_add_listing_bottom_code' ) ) {
	add_action( 'geodir_before_detail_fields', 'gd_add_listing_bottom_code', 10 );
}

add_action( 'wp_ajax_nopriv_blog_posts', 'vh_get_posts' );
add_action( 'wp_ajax_blog_posts', 'vh_get_posts' );
function vh_get_posts() {
	$categories = sanitize_text_field($_POST['categories']);
	$limit = sanitize_text_field($_POST['post_limit']);
	$post_count = sanitize_text_field($_POST['p_count']);
	$output = '';
	$count = $loop_count = $row_count = 1;
	global $post;

	if ( $limit == '' ) {
		$limit = -1;
	} else {
		$limit = sanitize_text_field($_POST['post_limit']);
	}

	$args = array(
		'post_type' => 'post',
		'category_name' => $categories,
		'posts_per_page' => $limit
	);
	
	$the_query = new WP_Query( $args );

	$output .= '<div class="blog-carousel-container">';
	$output .= '<div class="blog-carousel posts">';
	$output .= '<div class="blog-row">';
	while( $the_query->have_posts() && $post_count >= $row_count ) {
		$the_query->the_post();

		if ( $count > 10 ) {
			$count = 1;
		}

		$class = 'wide';

		if ( $count == 1 || $count == 5 || $count == 6 || $count == 9 ) {
			$class = 'wide';
		} elseif ( $count == 2 ||  $count == 7 || $count == 8 ) {
			$class = 'half-left';
		} else {
			$class = 'half-right';
		}

		if ( $count == 3 ) {
			$class .= ' full-right';
		} elseif ( $count == 5 || $count == 9 ) {
			$class .= ' half';
		} elseif ( $count == 7  ) {
			$class .= ' top';
		}

		$output .= '<div class="blog-inner-container ' . $class . '">';
		$output .= '<div class="blog-image">';
		if ( $count == 1 || $count == 5 || $count == 6 || $count == 9 ) {
			$output .= get_the_post_thumbnail( $post->ID, 'popular-destinations-square' );
			$output .= '<div class="blog-picture-time">' . human_time_diff(get_the_time('U',$post->id),current_time('timestamp')) . ' ' . __('ago', 'vh') . '</div>';
			$output .= '<div class="blog-picture-title"><a href="' . get_permalink( $post->ID ) . '">' . get_the_title() . '</a></div>';
			$output .= '<a href="' . get_permalink( $post->ID ) . '" class="blog-picture-read">' . __('Read article', 'vh') . '</a>';
			$output .= '<div class="blog-author image">';
			$output .= '<div class="blog-author-image">';
			$output .= get_avatar(get_userdata( get_post_field( 'post_author', $post->ID ) )->ID, 70);
			$output .= '</div>';
			$output .= '<div class="blog-author-info">';
				$output .= '<div class="blog-category icon-folder-open">';
					$output .= get_the_category_list( __( ', ', 'vh' ) );
				$output .= '</div>';
				$output .= '<div class="blog-comments icon-comment-1">';
					$tc = wp_count_comments($post->ID);
					$output .= $tc->approved;
				$output .= '</div>';
				$output .= '<div class="blog-author-inner">';
					$output .= '<span class="author-text">' . __('Author:', 'vh') . '</span>';
					$output .= '<span class="author-name"><a href="'. get_author_posts_url( get_post_field( 'post_author', $post->ID ) ) . '">' . get_userdata( get_post_field( 'post_author', $post->ID ) )->display_name . '</a></span>';
				$output .= '</div>';
				$output .= '<div class="clearfix"></div>';
			$output .= '</div>';
			$output .= '<div class="clearfix"></div>';
		$output .= '</div>';
		} else {
			$output .= get_the_post_thumbnail( $post->ID, 'featured-property-square' );
			$output .= '<div class="blog-picture-title"><a href="' . get_permalink( $post->ID ) . '">' . __('Read article', 'vh') . '</a></div>';
		}
		$output .= '</div>';
		$output .= '<div class="blog-inner-side">';
		$output .= '<div class="blog-date">' . human_time_diff(get_the_time('U',$post->id),current_time('timestamp')) . ' ' . __('ago', 'vh') . '</div>';
		$output .= '<div class="blog-title"><a href="' . get_permalink( $post->ID ) . '">' . get_the_title() . '</a></div>';
		$output .= '<div class="blog-excerpt">';
		if ( strlen($post->post_content) > 80 ) {
			$post->post_content = substr($post->post_content, 0, 80) . '..';
		} else {
			$post->post_content;
		}
		$output .= $post->post_content;
		$output .= '</div>';
		$output .= '<div class="blog-author">';
			$output .= '<div class="blog-author-image">';
			$output .= get_avatar(get_userdata( get_post_field( 'post_author', $post->ID ) )->ID, 70);
			$output .= '</div>';
			$output .= '<div class="blog-author-info">';
				$output .= '<div class="blog-category icon-folder-open">';
					$output .= get_the_category_list( __( ', ', 'vh' ) );
				$output .= '</div>';
				$output .= '<div class="blog-comments icon-comment-1">';
					$tc = wp_count_comments($post->ID);
					$output .= $tc->approved;
				$output .= '</div>';
				$output .= '<div class="blog-author-inner">';
					$output .= '<span class="author-text">' . __('Author:', 'vh') . '</span>';
					$output .= '<span class="author-name"><a href="'. get_author_posts_url( get_post_field( 'post_author', $post->ID ) ) . '">' . get_userdata( get_post_field( 'post_author', $post->ID ) )->display_name . '</a></span>';
				$output .= '</div>';
				$output .= '<div class="clearfix"></div>';
			$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		if ( $count % 10 == 0 && count($the_query->posts) != $loop_count ) {
			$posts_from = $row_count*10+1;
			$posts_to = ($row_count+1)*10;
			$separator = '<div class="clearfix"></div><span class="blog-separator">'.$posts_from." - ".$posts_to.'</span>';
			$output .= '<div class="clearfix"></div></div><div class="blog-row">';
			if ( $post_count != 1 ) {
				$output .= $separator;
			}
			$row_count++;
		}

		$count++;
		$loop_count++;

	}
	$output .= '</div>';

	if ( count($the_query->posts) == $loop_count-1 ) {
		$output .= '<input type="hidden" id="blog_show_load_more" value="false">';
	} else {
		$output .= '<input type="hidden" id="blog_show_load_more" value="true">';
	}
	$output .= '</div>';

	echo $output;
	
	wp_reset_query();
	wp_reset_postdata();
	die(1);
}

function vh_get_image_id($image_url) {
	global $wpdb;
	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url )); 
		return $attachment[0]; 
}

add_action( 'wp_ajax_nopriv_geodir_search_sidemap', 'get_geodir_search_sidemap' );
add_action( 'wp_ajax_geodir_search_sidemap', 'get_geodir_search_sidemap' );
function get_geodir_search_sidemap() {
$width = empty($instance['width']) ? '1247' : apply_filters('widget_width', $instance['width']);
	$height         = empty($instance['heigh']) ? '973' : apply_filters('widget_heigh', $instance['heigh']);
	$maptype        = empty($instance['maptype']) ? 'ROADMAP' : apply_filters('widget_maptype', $instance['maptype']);
	$zoom           = empty($instance['zoom']) ? '13' : apply_filters('widget_zoom', $instance['zoom']);
	$autozoom       = empty($instance['autozoom']) ? '' : apply_filters('widget_autozoom', $instance['autozoom']);
	$child_collapse = empty($instance['child_collapse']) ? '0' : apply_filters('widget_child_collapse', $instance['child_collapse']);
	$scrollwheel    = empty($instance['scrollwheel']) ? '0' : apply_filters('widget_scrollwheel', $instance['scrollwheel']);
	$map_args       = array();

	$map_args['width']                    = $width;
	$map_args['height']                   = $height;
	$map_args['maptype']                  = $maptype;
	$map_args['scrollwheel']              = $scrollwheel;
	$map_args['zoom']                     = $zoom ;
	$map_args['autozoom']                 = $autozoom;
	$map_args['child_collapse']           = $child_collapse;
	$map_args['enable_cat_filters']       = true;
	$map_args['enable_text_search']       = true;
	$map_args['enable_post_type_filters'] = true;
	$map_args['enable_location_filters']  = apply_filters('geodir_home_map_enable_location_filters', true);
	$map_args['enable_jason_on_load']     = false;
	$map_args['enable_marker_cluster']    = false;
	$map_args['enable_map_resize_button'] = true;
	$map_args['map_class_name']           = 'geodir-map-home-page';
	
	$is_geodir_home_map_widget = true;
	$map_args['is_geodir_home_map_widget'] = $is_geodir_home_map_widget;


	geodir_draw_map($map_args);


	echo $output;
	
	wp_reset_query();
	wp_reset_postdata();
	die(1);
}

function vh_get_post_info( $post_id, $post_type = 'gd_place' ) {
	global $wpdb;

	if (strpos($post_type,'gd_') === false) {
		$post_type = 'gd_place';
	}

	if ( !isset($_REQUEST['preview']) ) {
		if ( function_exists('geodir_is_plugin_active') ) {
			$query = $wpdb->prepare("SELECT post_latitude, post_longitude, default_category, post_city FROM " . $wpdb->prefix .  "geodir_" . $post_type . "_detail WHERE post_status = \"publish\" AND post_id=%s", $post_id);
			$queryresults = $wpdb->get_results($query);
		} else {
			$queryresults = '';
		}
	} else {
		$queryresults = array('0' => (object)array('post_latitude' => $_REQUEST['post_latitude'], 'post_longitude' => $_REQUEST['post_longitude'], 'default_category' => $_REQUEST['post_default_category'], 'post_city' => '' ) );
	}

	return $queryresults;
}

function get_header_video() {
	$video_id = get_option('vh_header_video');
	$output = '';
	if ( $video_id != '' ) {
		$attachment_info = wp_get_attachment_metadata( $video_id );
		if ( $attachment_info['fileformat'] == 'mp4' || $attachment_info['fileformat'] == 'webm' || $attachment_info['fileformat'] == 'ogg' ) {
			$output .= '
			<video autoplay muted loop>';
			$output .= '
				<source src="'.wp_get_attachment_url( $video_id ).'" type="video/'.$attachment_info['fileformat'].'">
				Your browser does not support the video tag.
			</video>';

		}
	}

	return $output;

}

function vh_get_header_search_field() {
	global $wpdb;
	$form_output = '';
	$default_post_type = get_option('geodir_default_map_search_pt', 'gd_place');

	if ( function_exists('geodir_advance_search_filter') ) {
		$advanced_fields = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'geodir_custom_advance_search_fields WHERE post_type="'.$default_post_type.'" ORDER BY sort_order');
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
					$options = $wpdb->get_results('SELECT option_values FROM '.$wpdb->prefix.'geodir_custom_fields WHERE htmlvar_name="'.$advanced_value->site_htmlvar_name.'" AND post_type="'.$advanced_value->post_type.'"');
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
					$options = $wpdb->get_results('SELECT option_values FROM '.$wpdb->prefix.'geodir_custom_fields WHERE htmlvar_name="'.$advanced_value->site_htmlvar_name.'" AND post_type="'.$advanced_value->post_type.'"');
					$option_values = explode(',', $options['0']->option_values);

					$form_output .= '
					<div class="header-input-container advanced" style="width: '.$field_width.'px;">
						<span class="header-input-title">'.$advanced_value->front_search_title.'</span>
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
					$options = $wpdb->get_results('SELECT option_values FROM '.$wpdb->prefix.'geodir_custom_fields WHERE htmlvar_name="'.$advanced_value->site_htmlvar_name.'" AND post_type="'.$advanced_value->post_type.'"');
					$option_values = explode(',', $options['0']->option_values);

					$form_output .= '
					<div class="header-input-container advanced" style="width: '.$field_width.'px;">
						<span class="header-input-title">'.$advanced_value->front_search_title.'</span>
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
						<span class="header-input-title">'.$advanced_value->front_search_title.'</span>
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
	} elseif ( $default_post_type == 'gd_event' ) {
		$form_output .= '
		<div class="header-input-container event" style="width: 339px;"><span class="header-input-title">'.__('Keyword:', 'vh').'</span><input type="text" id="header-keyword" name="s" style="width: 100%;"><div class="clearfix"></div></div>
		<div class="header-input-container event" style="width: 339px;"><span class="header-input-title">'.__('Location:', 'vh').'</span><input type="text" id="header-location" style="width: 100%;"><div class="clearfix"></div></div>';
	} else {
		$post_taxonomy = 'gd_placecategory';

		$listing_categories = get_terms('gd_placecategory');
		if ( !is_wp_error($listing_categories) ) {
			$listing_category = esc_attr($listing_categories['0']->name);
		} else {
			$listing_category = '';
		}

		if ( get_option("vh_theme_version") == "SkyVacation" ) {
			if ( !isset($_COOKIE['vh_startrange']) ? $start_range = '' : $start_range = esc_attr($_COOKIE['vh_startrange']) );
			if ( !isset($_COOKIE['vh_endrange']) ? $end_range = '' : $end_range = esc_attr($_COOKIE['vh_endrange']) );
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
	}

	echo $form_output;
}

add_action( 'wp_ajax_nopriv_get_header_bg', 'get_header_bg_image' );
add_action( 'wp_ajax_get_header_bg', 'get_header_bg_image' );
function get_header_bg_image( $user_country = 'Default', $die = true, $return = false ) {
	if ( !empty($_POST['country']) ) {
		$curr_country = sanitize_text_field($_POST['country']);
	} else {
		$curr_country = $user_country;
	}
	$countries = get_option('vh_countries_options');
	$info = trim($countries['pu_textbox'],',');
	$info_arr = json_decode('['.$info.']',true);
	$image_id = '';

	foreach ($info_arr as $country) {
		$arr_country = explode(':', $country['country']);
		if ( $arr_country['0'] == $curr_country ) {
			$image_id = $arr_country['1'];
		}
	}

	if ( $image_id == '' ) {
		$output = '
		
	
		<script type="text/javascript">
		

			jQuery(
			function(){
				jQuery("#main_header_image").slidesjs({
					play: {
						effect:"fade",
						interval: 10000,
						auto: true
					}
				});
			}
			);

		</script>		
		';
	} else {
		echo get_header_video();
		$bg_url = wp_get_attachment_image_src($image_id, 'main-header-image');
		$output = '<style type="text/css" media="all">#main_header_image { background: url(' . $bg_url['0'] . '); height: 747px; background-size: cover !important; background-position: 100% !important; }</style>';
	}

	if ( $return ) {
		return $output;
	} else {
		echo $output;
	}
	
	if ( $die ) {
		die();
	}
}

add_action( 'wp_ajax_nopriv_get_header_bg-information', 'get_header_bg_info' );
add_action( 'wp_ajax_get_header_bg-information', 'get_header_bg_info' );
function get_header_bg_info( $user_city = 'Paris', $user_country = 'France', $die = true, $return = false ) {
	if ( !empty($_POST['c_country']) ) {
		$curr_country = sanitize_text_field($_POST['c_country']);
	} else {
		$curr_country = $user_country;
	}
	
	if ( !empty($_POST['c_country']) ) {
		$curr_city = sanitize_text_field($_POST['c_city']);
	} else {
		$curr_city = $user_city;
	}

	global $wpdb;
	$queryresults = 0;

	$geodir_post_types = get_geodir_custom_post_types();
	foreach ($geodir_post_types as $custom_post_value) {
		$query = "SELECT count(*) FROM " . $wpdb->prefix .  "geodir_" . $custom_post_value . "_detail WHERE post_status = 'publish' && post_city LIKE \"".$curr_city."%\" AND post_country LIKE \"".$curr_country."%\"";
		$queryresults += $wpdb->get_var($query);
	}

	if ( empty($queryresults) ) {
		$queryresults = '0';
	}

	if ( $return ) {
		return $queryresults.__(' listings', 'vh');
	} else {
		echo $queryresults.__(' listings', 'vh');
	}

	if ( $die ) {
		die();
	}
}

function vh_geodir_comment_template( $comment_template ) {
	// global $post;

	if ( function_exists('geodir_get_posttypes') ) {
		$post_types = geodir_get_posttypes();
	} else {
		$post_types = array();
	}
	

	// if ( !( is_singular() && ( have_comments() || (isset($post>comment_status) && 'open' == $post>comment_status) ) ) ) {
	// 	return;
	// }

	// if(in_array($post>post_type, $post_types)){ // assuming there is a post type called business
	// 	return get_template_directory().'/geodirectory/gd_placecomments.php.php';
	// }
}
add_filter( "comments_template", "vh_geodir_comment_template" );

function vh_get_listing_price( $id, $post_type ) {
	global $wpdb;

	$query = $wpdb->prepare("SELECT geodir_listing_price as Price FROM " . $wpdb->prefix .  "geodir_" . $post_type . "_detail WHERE post_status = \"publish\" AND post_id=%s", $id);
	$queryresults = $wpdb->get_var($query);

	return $queryresults;
}

function vh_get_listing_rating( $id, $post_type ) {
	global $wpdb;
	$output = '';

	$query = $wpdb->prepare("SELECT overall_rating,rating_count FROM " . $wpdb->prefix .  "geodir_" . $post_type . "_detail WHERE post_status = \"publish\" AND post_id=%s", $id);
	$queryresults = $wpdb->get_results($query);

	if ( $queryresults['0']->overall_rating != 0 ) {
		$overall_rating = $queryresults['0']->overall_rating;
	} else {
		$overall_rating = 0;
	}

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
		$output .= "<span class=\"listing-item-star text\">".round($overall_rating, 0)."</span>";
	$output .= '
	</div>';

	return $output;
}

function my_widget_class($params) {

	// its your widget so you add  your classes
	$classe_to_add = (strtolower(str_replace(array(' '), array(''), $params[0]['widget_name']))); // make sure you leave a space at the end
	$classe_to_add = 'class=" '.$classe_to_add . ' ';
	$params[0]['before_widget'] = str_replace('class="', $classe_to_add, $params[0]['before_widget']);

	return $params;
}
add_filter('dynamic_sidebar_params', 'my_widget_class');

function vh_get_listing_people_options() {
	$adults = array(__("1 Adult", "vh"), __("2 Adults", "vh"), __("3 Adults", "vh"), __("4 Adults", "vh"), __("5 Adults", "vh"), __("6 Adults", "vh"), __("7 Adults", "vh"), __("8 Adults", "vh"), __("9 Adults", "vh"), __("10 Adults", "vh"));
	$childrens = array(__("No Children", "vh"), __("1 Child", "vh"), __("2 Children", "vh"), __("3 Children", "vh"), __("4 Children", "vh"), __("5 Children", "vh"), __("6 Children", "vh"), __("7 Children", "vh"), __("8 Children", "vh"), __("9 Children", "vh"), __("10 Children", "vh"));

	echo "
	<div class=\"search-people-container\" tabindex=\"-1\">";
		echo "
		<div class=\"search-people-adults\">";
		foreach ($adults as $value) {
			echo "
			<div class=\"calendar-people-item\">
				<span class=\"people-item\">" . $value . "</span>
			</div>";
		}
		echo "</div>";

		echo "
		<div class=\"search-people-children\">";
		foreach ($childrens as $value) {
			echo "
			<div class=\"calendar-people-item\">
				<span class=\"people-item\">" . $value . "</span>
			</div>";
		}
		echo "</div>";
	echo "</div>";
}

function vh_get_search_contract() {
	echo "
	<div class=\"search-contract-container\" tabindex=\"-1\">";
			echo "
			<div class=\"calendar-contract-item\">
				<span class=\"contract-item\">".__('For Sale', 'vh')."</span>
			</div>";
			echo "
			<div class=\"calendar-contract-item\">
				<span class=\"contract-item\">".__('For Rent', 'vh')."</span>
			</div>";
	echo "</div>";
}

function vh_get_search_category( $category = 'gd_placecategory' ) {

	$listing_categories = get_terms( $category );

	echo "
	<div class=\"search-contract-container category\" tabindex=\"-1\">";
	echo "<div class='inner-category'>";
	foreach ($listing_categories as $cat_val) {
		echo "
		<div class=\"calendar-contract-item\">
			<span class=\"contract-item\">".$cat_val->name."</span>
		</div>";
		
	}
	echo "</div>";
	echo "</div>";
}

function vh_startsWith($haystack,$needle,$case=true) {
	if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
	return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
}

function vh_get_search_type() {
	global $wpdb;
	$term = $_REQUEST['term'];

	$listing_categories = get_terms('gd_placecategory');

	$terms_json = array();


	foreach ($listing_categories as $value) {
		if ( strpos(strtolower($value->name), strtolower($term)) !== false ) {
			$terms_arr["value"] = $value->name;
			$terms_arr["label"] = $value->name;
			
			$terms_json[] = $terms_arr;
		}
	}

	if ( empty($terms_json) ) {
		$terms_arr["value"] = $term;
		$terms_arr["label"] = __("Sorry, no matches were found.", "vh");
		$terms_json[] = $terms_arr;
	}

	$json = json_encode($terms_json);
	echo $json;
	exit();
}
add_action( 'wp_ajax_vh_get_search_type', 'vh_get_search_type' );
add_action( 'wp_ajax_nopriv_vh_get_search_type', 'vh_get_search_type' );

if(isset($_REQUEST['popuptype']) && $_REQUEST['popuptype'] != '' && isset($_REQUEST['post_id']) && $_REQUEST['post_id'] != ''){
	
	if($_REQUEST['popuptype'] == 'b_send_inquiry' || $_REQUEST['popuptype'] == 'b_sendtofriend')
		require_once (get_template_directory().'/geodirectory/popup-forms.php');
	
	exit;
}

function get_listing_rating_stars( $rating, $text=true ) {
	$count = 0;
	$output = "
	<div class=\"listing-rating-stars\">";
		for ($i=0; $i < 5; $i++) {
			if ( $count < $rating ) {
				$output .= "<span class=\"listing-rating-star icon-star\"></span>";
			} else {
				$output .= "<span class=\"listing-rating-star icon-star-empty\"></span>";
			}
			
			$count++;
		}

		if ( $text == true ) {
			$output .= "<span class=\"listing-rating-star text\">".ceil($rating)."</span>";
		}
	$output .= "
	</div>";

	return $output;
}

function get_listing_rating_count( $post_id, $overall = true, $post_type ) {
	global $wpdb;

	$querystr = $wpdb->prepare("SELECT overall_rating,rating_count FROM " . $wpdb->prefix .  "geodir_" . $post_type . "_detail WHERE post_status = 'publish' AND post_id=%s", $post_id);
	$queryresults = $wpdb->get_results($querystr);

	if ( $queryresults['0']->overall_rating != 0 ) {
		$overall = $queryresults['0']->overall_rating;
	} else {
		$overall = 0;
	}
	
	if ( $overall ) {
		return $overall;
	} else {
		return $queryresults['0']->rating_count;
	}
	
}

function get_geodir_listing_price( $post_id, $price = true, $post_type ) {
	global $wpdb;
	$postquery = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix .  "geodir_" . $post_type . "_detail WHERE post_status = 'publish' AND post_id=%s", $post_id);
	$postqueryresults = $wpdb->get_results($postquery);

	if ( isset($postqueryresults['0']->geodir_listing_price) ) {
		$listing_price = $postqueryresults['0']->geodir_listing_price;
	}

	if ( $price && isset($postqueryresults['0']->geodir_listing_price) ) {
		return get_option('vh_currency_symbol').$listing_price;
	}
	
}

function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

function vh_convertToHoursMins($time, $format = '%d:%d') {
	settype($time, 'integer');
	if ($time < 1) {
		return;
	}
	$hours = floor($time / 60);
	$minutes = ($time % 60);
	return sprintf($format, $hours, $minutes);
}

function vh_wp_tag_cloud_filter($return, $args) {
	return '<div class="tag_cloud_' . $args['taxonomy'] . '">' . $return . '</div>';
}
add_filter('wp_tag_cloud', 'vh_wp_tag_cloud_filter', 10, 2);

add_filter( 'tribe_events_the_previous_month_link', 'vh_tribe_previous_month_filter' );

// Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
function vh_page_menu_args($args) {
	$args['show_home'] = true;
	return $args;
}
add_filter('wp_page_menu_args', 'vh_page_menu_args');

// Register menus
function register_vh_menus () {
	register_nav_menus(
		array (
			'primary-menu' => __('Primary Menu', 'vh')
		)
	);
}
add_action('init', 'register_vh_menus');

// Check if current user is a host
function user_is_host( ) {

    $user = wp_get_current_user();

    if ( ! empty( $user ) ) {
        return in_array('host', (array) $user->roles );
    }
}

// Short code for current user name
add_shortcode( 'current-username' , 'ss_get_current_username' );
function ss_get_current_username(){
    $user = wp_get_current_user();
    return $user->display_name;
}

function my_wp_nav_menu_args( $args = '' ) {
	if ( $args['theme_location'] == 'primary-menu' ) {
		if( is_user_logged_in() ) {
			$args['menu'] = 'logged-in';
			if(user_is_host()){
				$args['menu'] = 'logged-in-host';
			}
		} else { 
			$args['menu'] = 'logged-out';
		} 
	}
	return $args;
}
add_filter( 'wp_nav_menu_args', 'my_wp_nav_menu_args' );

// Adds classes to the array of body classes.
function vh_body_classes($classes) {
	global $post, $wp_version;
	$geodir_post_types = get_geodir_custom_post_types();

	if (is_singular() && !is_home()) {
		$classes[] = 'singular';
	}

	if ( !is_front_page() ) {
		$classes[] = 'not_front_page';
	}

	if (is_search()) {
		$search_key = array_search('search', $classes);
		if ($search_key !== false) {
			unset($classes[$search_key]);
		}
	}

	if ( !empty($_GET["listing_type"]) && in_array($_GET["listing_type"], $geodir_post_types) ) {
		$classes[] = "geodir-listing";
	}

	if ( !empty($_REQUEST["preview"]) && in_array($_REQUEST["preview"], $geodir_post_types) ) {
		$classes[] = "geodir-listing-preview";
	}

	if ( (!empty($_GET["geodir_search"]) && $_GET["geodir_search"] == "1") || (!empty($_GET["gd_placecategory"]) && $_GET["gd_placecategory"] != "") || ( function_exists('geodir_is_page') && geodir_is_page('location') ) ) {
		$classes[] = "geodir-main-search";
	}

	if ( !empty($_GET["gd_placecategory"]) && $_GET["gd_placecategory"] != "" ) {
		$classes[] = "geodir-category-search";
	}

	if ( !empty($_GET["stype"]) && $_GET["stype"] == "gd_event" ) {
		$classes[] = "geodir-event-search";
	}

	if ( get_option("vh_theme_version") == "SkyDirectory" ) {
		$classes[] = "skydirectory";
	} elseif ( get_option("vh_theme_version") == "SkyEstate" ) {
		$classes[] = "skyestate";
	} else {
		$classes[] = "skyvacation";
	}

	if ( function_exists('geodir_payment_activation') ) {
		$classes[] = "geodirectory-payment-manager";
	}

	$book_now_id = get_option('vh_book_now');

	if ( !empty($book_now_id) && $book_now_id == $post->ID ) {
		$classes[] = "easyreservation";
	}

	if ( !function_exists('geodir_is_plugin_active') ) {
		$classes[] = "geodir_disabled";
	}

	if ( wp_script_is('geodir-autocompleter-js') ) {
		$geo_advanced_search = get_option('geodir_autolocate_disable');
	} else {
		$geo_advanced_search = '0';
	}
	
	$geolocation_state = get_option('vh_geolocation_state');
	if ( ( $geolocation_state == false || $geolocation_state == 'true' ) && $geo_advanced_search == '0' ) {
		$classes[] = "geolocation_on";
	} else {
		$classes[] = "geolocation_off";
	}

	if ( !empty($_GET["geodir_dashbord"]) && $_GET["geodir_dashbord"] == "true" ) {
		$classes[] = 'geodir_dashbord';
	}

	
	if ( in_array(get_post_type(), $geodir_post_types) && is_single() ) {
		$classes[] = 'single-geodir-page';
	}

	// Color scheme class
	$vh_color_scheme = get_theme_mod( 'vh_color_scheme');

	if ( !empty($vh_color_scheme) ) {
		$classes[] = $vh_color_scheme;
	}

	if ( function_exists('geodir_reviewrating_wrap_comment_text') && get_option('geodir_reviewrating_enable_rating') ) {
		$classes[] = 'geodir-multi-ratings';
	}

	// If blog shortcode
	global $post;
	if (isset($post->post_content) && false !== stripos($post->post_content, '[blog')) {
		$classes[] = 'page-template-blog';
	}
	
	// $fixed_menu = get_option('vh_fixed_menu') ? get_option('vh_fixed_menu') : 'true';
	// if ( $fixed_menu == 'true' ) {
	// 	$classes[] = 'fixed_menu';
	// }

	// Breadcrumbs class
	$disable_breadcrumb = get_option('vh_breadcrumb') ? get_option('vh_breadcrumb') : 'false';
	if (!is_home() && !is_front_page() && $disable_breadcrumb == 'false') {
		$classes[] = 'has_breadcrumb';
	}

	if ( get_option('vh_header_search_map') != 'false' && get_option('vh_header_search_map') != false ) {
		$classes[] = 'header_search_map';
	}

	if ( version_compare($wp_version, '4.4', '>=') ) {
		$classes[] = 'wp-post-4-4';
	}

	return $classes;
}
add_filter('body_class', 'vh_body_classes');

function vh_css_settings() {

	// Vars
	$css        = array();
	$custom_css = get_option('vh_custom_css');

	// Custom CSS
	if(!empty($custom_css)) {
		array_push($css, $custom_css);
	}

	echo "
		<!-- Custom CSS -->
		<style type='text/css'>\n";

	if(!empty($css)) {
		foreach($css as $css_item) {
			echo $css_item . "\n";
		}
	}

	$fonts[SHORTNAME . "_primary_font_dark"] = ' html .main-inner p, .ac-device .description, .pricing-table .pricing-content .pricing-desc-1, body .vc_progress_bar .vc_single_bar .vc_label, .page-wrapper .member-desc, .page-wrapper .member-position, .page-wrapper .main-inner ul:not(.ui-tabs-nav) li, .page-wrapper .bg-style-2 p, .wrapper a, .header-search-form input[type="text"], .header-input-container .header-input-title, .item-location, .popular-destinations-image .city-country, .blog-image .blog-picture-time, .blog-picture-read, .blog-carousel .blog-inner-container .blog-date, .blog-carousel .blog-inner-container .blog-excerpt, .blog-author .author-text, .listing-category-side .category-parent, .listing-category-side .category-child, .listing-category-image .listing-count, .useful-links-title, .phone-info-container .phone-text, .phone-info-container .phone-number, .single-listing-by-info .author-text, .wrapper p, h2.wpb_gallery_heading, #comments > span, .wrapper .comment-content .description, .wrapper .comment-content p, .reviewer-time, .add-review-container span, .google-map-location, .listing-item-title, .listing-item-location, .page-title .blog-post-date, .comment-form-comment textarea, .prev-post-text, body.single-post #commentform .form-submit #submit, .google-map-image .listing-price, #propertyform input[type=text], .wrapper .geodir_message_error, .wrapper .geodir_message_note, .addlisting-upload-text, .wrapper .addlisting-upload-button, #propertyform #upload-msg, .checkbox_text, .addlisting-submit-title, .addlisting-submit-body, .geodir-filter-when-text, .geodir-filter-when-value, #geodir-filter-list li, .geodir-map-listing-top, .map-listing-title, .map-listing-price, .geodir-filter-inner .filter-left .filter-text, .geodir-filter-inner .filter-left .filter-second-text, .geodir-filter-inner .filter-right > span, body #simplemodal-container .geodir_popup_field input[type=text], body #simplemodal-container .geodir_popup_field textarea, .gdmodal-title, .wpb_button.wpb_btn-small, #commentform .comment_auth_email input[type=text], .ui-dialog.main-dialog .ui-dialog-titlebar span.ui-dialog-title, .next-post-text, .contact-phone-container, .contact-email-container, wpcf7-response-output, .single-listing-item-info, .breadcrumb .current, .header-login-main, .footer-inner, .footer-content, .featured-properties-row, .popular-destinations-main, .search-calendar-container, #ui-datepicker-div, li.geodir-gridview > span, .single-listing-info, .blog-carousel-container, .open-blog-infobox, body.single-post #commentform input[type=text], body.single-post #commentform input[type=email], .header-gallery-counter, #geodir-wrapper, .search-people-container, #header-more-options, .page_info, .wrapper, .wrapper .easyFrontendFormular, .wrapper #easyFrontendFormular .easy-button, .wrapper #easyFormInnerlay input[type="button"], body .simplemodal-data .gmmodal-dialog-lower input, body #simplemodal-container #gd-basic-modal-content4 .button, body.geodir-multi-ratings .gd-rate-category span.lable, body.geodir-multi-ratings .gd-rate-area .gd-ratehead, .ui-menu .ui-menu-item a';
	$fonts[SHORTNAME . "_sidebar_font_dark"] = ' .sidebar-inner, .sky-contactform.widget input:not(.btn), .sky-recentpostsplus.widget .news-item p, .wrapper .text.widget p, .sky-fastflickrwidget.widget, .widget li, .wrapper .search.widget .sb-search-input, .widget .content-form .textarea.input-block-level, .text.widget .textwidget, .newsletter-email';
	$fonts[SHORTNAME . "_headings_font_h1"]  = ' .wrapper h1, body .wrapper .page_info .page-title h1';
	$fonts[SHORTNAME . "_headings_font_h2"]  = ' .page-wrapper h2, h2, .content .entry-title, .teaser_grid_container .post-title';
	$fonts[SHORTNAME . "_headings_font_h3"]  = ' .wrapper h3';
	$fonts[SHORTNAME . "_headings_font_h4"]  = ' .wrapper h4';
	$fonts[SHORTNAME . "_headings_font_h5"]  = ' .wrapper h5';
	$fonts[SHORTNAME . "_headings_font_h6"]  = ' .wrapper h6';
	$fonts[SHORTNAME . "_links_font"]        = ' .wpb_wrapper a, #author-link a, .sky-usefullinks.widget a';
	$fonts[SHORTNAME . "_widget"]            = ' .wrapper .sidebar-inner .item-title-bg h4, .wrapper .sidebar-inner .widget-title, .wrapper h3.widget-title a';
	$fonts[SHORTNAME . "_page_title"]        = ' body .wrapper .page_info .page-title h1';

	// Custom fonts styling
	foreach ($fonts as $key => $font) {
		$output                 = '';
		$current['font-family'] = get_option($key . '_font_face');
		$current['font-size']   = get_option($key . '_font_size');
		$current['line-height'] = get_option($key . '_line_height');
		$current['color']       = get_option($key . '_font_color');
		$current['font-weight'] = get_option($key . '_weight');

		foreach ($current as $kkey => $item) {

			if ( $key == SHORTNAME . '_widget' ) {
				if (!empty($item)) {

					if ($kkey == 'font-size' || $kkey == 'line-height') {
						$ending = 'px';
					} else if ($kkey == 'color') {
						$before = '#';
					} else if ($kkey == 'font-family') {
						$before = "'";
						$ending = "'";
						$item   = str_replace("+", " ", $item);
					} else if ($kkey == 'font-weight' && $item == 'italic') {
						$kkey = 'font-style';
					} else if ($kkey == 'font-weight' && $item == 'bold_italic') {
						$kkey = 'font-style';
						$item = 'italic; font-weight: bold';
					}


					$output .= " " . $kkey . ": " . $before . $item . $ending . ";";
				}

			}

			$ending = '';
			$before = '';
			if (!empty($item) && $key != SHORTNAME . '_widget') {

				if ($kkey == 'font-size' || $kkey == 'line-height') {
					$ending = 'px';
				} else if ($kkey == 'color') {
					$before = '#';
				} else if ($kkey == 'font-family') {
					$before = "'";
					$ending = "'";
					$item   = str_replace("+", " ", $item);
				} else if ($kkey == 'font-weight' && $item == 'italic') {
					$kkey = 'font-style';
				} else if ($kkey == 'font-weight' && $item == 'bold_italic') {
					$kkey = 'font-style';
					$item = 'italic; font-weight: bold';
				}


				$output .= " " . $kkey . ": " . $before . $item . $ending . ";";
			}
		}


		if ( !empty($output) && !empty($font) && $key != SHORTNAME . '_widget' ) {
			echo $font . ' { ' . $output . ' }';
		}
		if ( !empty($output) && !empty($font) && $key == SHORTNAME . '_widget' ) {
			echo '@media (min-width: 1200px) { ' . $font . ' { ' . $output . ' } } ';
		}
	}

	echo "</style>\n";

}
add_action('wp_head','vh_css_settings', 99);

if (!function_exists('vh_posted_on')) {

	// Prints HTML with meta information for the current post.
	function vh_posted_on() {
		printf(__('<span>Posted: </span><a href="%1$s" title="%2$s" rel="bookmark">%4$s</a><span class="by-author"> by <a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span>', 'vh'),
			esc_url(get_permalink()),
			esc_attr(get_the_time()),
			esc_attr(get_the_date('c')),
			esc_html(get_the_date()),
			esc_url(get_author_posts_url(get_the_author_meta('ID'))),
			sprintf(esc_attr__('View all posts by %s', 'vh'), get_the_author()),
			esc_html(get_the_author())
		);
	}
}

function clear_nav_menu_item_id($id, $item, $args) {
	return "";
}
add_filter('nav_menu_item_id', 'clear_nav_menu_item_id', 10, 3);

function add_nofollow_cat( $text ) {
	$text = str_replace('rel="category"', "", $text);
	return $text;
}
add_filter( 'the_category', 'add_nofollow_cat' );

function ajax_contact() {
	if(!empty($_POST)) {
		$sitename = get_bloginfo('name');
		$siteurl  = home_url();
		$to       = isset($_POST['contact_to'])? sanitize_email(trim($_POST['contact_to'])) : '';
		$name     = isset($_POST['contact_name'])? sanitize_text_field(trim($_POST['contact_name'])) : '';
		$email    = isset($_POST['contact_email'])? sanitize_email(trim($_POST['contact_email'])) : '';
		$content  = isset($_POST['contact_content'])? sanitize_text_field(trim($_POST['contact_content'])) : '';

		$error = false;
		$error = ($to === '' || $email === '' || $content === '' || $name === '') ||
				 (!preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $email)) ||
				 (!preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $to));

		if($error == false) {
			$subject = "$sitename message from $name";
			$body    = "Site: $sitename ($siteurl) \n\nName: $name \n\nEmail: $email \n\nMessage: $content";
			$headers = "From: $name ($sitename) <$email>\r\nReply-To: $email\r\n";
			$sent    = wp_mail($to, $subject, $body, $headers);

			// If sent
			if ($sent) {
				echo 'sent';
				die();
			} else {
				echo 'error';
				die();
			}
		} else {
			echo _e('Please fill all fields!', 'vh');
			die();
		}
	}
}
add_action('wp_ajax_nopriv_contact_form', 'ajax_contact');
add_action('wp_ajax_contact_form', 'ajax_contact');

if ( function_exists('geodir_admin_claim_listing_init') ) {
	function vh_geodir_claim_popup_form($post_id){
		global $post;
		
		?>
		<div id="gd-basic-modal-content4" class="clearfix">
		
			<?php do_action( 'geodir_before_claim_form' ); ?>
			<form name="geodir_claim_form" id="geodir_claim_form" action="<?php echo admin_url();?>admin-ajax.php?action=geodir_claim_ajax_action" method="post" >
			<?php
				$nonce = wp_create_nonce( 'add_claim_nonce'.$post_id );
			?>
				<input type="hidden" name="add_claim_nonce_field" value="<?php echo $nonce;?>" />	
				<input type="hidden" id="claim_form_pid" name="geodir_pid" value="<?php echo $post_id;?>" />
				<input type="hidden" name="geodir_sendact" value="add_claim" />

				<div class="gdmodal-title"><?php echo CLAIM_LISTING_TEXT;?></div>
				
				<p id="reply_send_success2" class="sucess_msg" style="display:none;"></p>
				
				<?php do_action('geodir_before_claim_form_field', 'geodir_full_name') ;?>
				<div class="row clearfix" >
					<div class="geodir_popup_field">	
						<input class="is_required" field_type="text" name="geodir_full_name" id="geodir_full_name" type="text" placeholder="<?php echo CLAIM_FULLNAME;?>" />
						<span class="message_error2" id="geodir_full_nameInfo" ></span>
					</div>
				</div>
				<?php do_action('geodir_after_claim_form_field', 'geodir_full_name') ;?>
				
				<?php do_action('geodir_before_claim_form_field', 'geodir_user_comments') ;?>
				<div class="row  clearfix" >
					<div class="geodir_popup_field">	
						<input class="is_required" field_type="text" name="geodir_user_number" id="geodir_user_number" type="text" placeholder="<?php echo CLAIM_CONTACT_NUMBER;?>" />
						<span class="message_error2" id="geodir_user_numberInfo" ></span>
					</div>
				</div>
				<?php do_action('geodir_after_claim_form_field', 'geodir_user_number') ;?>
				
				<?php do_action('geodir_before_claim_form_field', 'geodir_user_comments') ;?>
				<div class="row  clearfix" >
					<div class="geodir_popup_field">	
						<input class="is_required" field_type="text" name="geodir_user_position" id="geodir_user_position" type="text" placeholder="<?php echo CLAIM_POS_IN_BUSINESS;?>" />
						<span class="message_error2" id="geodir_user_positionInfo"></span>
					</div>
				</div>
				<?php do_action('geodir_after_claim_form_field', 'geodir_user_number') ;?>
				
				<?php do_action('geodir_before_claim_form_field', 'geodir_user_comments') ;?>
				<div class="row  clearfix" >
					<div class="geodir_popup_field">	
						<textarea class="is_required" field_type="textarea" name="geodir_user_comments" id="geodir_user_comments" cols="" rows="" placeholder="<?php echo CLAIM_COMMENT_TEXT;?>" ><?php echo CLAIM_LISTING_SAMPLE_CONTENT;?></textarea>
						<span class="message_error2" id="geodir_user_commentsInfo"></span>
					</div>
				</div>
				
				<?php do_action( 'geodir_after_claim_form_field', 'geodir_user_comments' ) ;?>
				<div class="gmmodal-dialog-lower">
					<input name="geodir_Send" type="submit" value="<?php echo CLAIM_SEND_TEXT; ?> " class="button wpb_button wpb_btn-primary wpb_btn-small" />
					<a href="javascript:void(0)" class="gmmodal-close-dialog wpb_button wpb_btn-inverse wpb_btn-small">Cancel</a>
				</div>
			</form>
			<?php do_action('geodir_after_claim_form'); ?>
		</div>
		<?php

	}

	add_action('wp_ajax_vh_geodir_claim_ajax_action', "vh_geodir_claim_ajax_action");
	add_action('wp_ajax_nopriv_vh_geodir_claim_ajax_action', 'vh_geodir_claim_ajax_action');
	function vh_geodir_claim_ajax_action() {
		// echo 'expression';
		// die();

		if(isset($_POST['geodir_sendact']) && $_POST['geodir_sendact'] == 'add_claim')
		{	
			geodir_user_add_claim();
		}
		
		if(isset($_POST['claimact']) && $_POST['claimact'] == 'addclaim')
		{
			geodir_claim_add_comment();
		}
		
		if(isset($_POST['subtab']) && $_POST['subtab'] == 'geodir_claim_options')
		{
			
			geodir_update_options(geodir_claim_default_options());
			
			$msg = CLAIM_LISTING_OPTIONS_SAVE;
			
			$msg = urlencode($msg);
			
			$location = admin_url()."admin.php?page=geodirectory&tab=claimlisting_fields&subtab=geodir_claim_options&claim_success=".$msg;
			
			wp_redirect($location);
			die();
			
		}
		
		if(isset($_POST['manage_action']) && $_POST['manage_action']=='true')
		{
			geodir_manage_claim_listing_actions();
		}
		
		if(isset($_POST['subtab']) && $_POST['subtab'] == 'geodir_claim_notification')
		{
			
			geodir_update_options(geodir_claim_notifications());
			
			$msg = CLAIM_NOTIFY_SAVE_SUCCESS;
			
			$msg = urlencode($msg);
			
			$location = admin_url()."admin.php?page=geodirectory&tab=claimlisting_fields&subtab=geodir_claim_notification&claim_success=".$msg;
		
			wp_redirect($location);die();
			
		}
		
		if(isset($_POST['popuptype']) && $_POST['popuptype'] != '' && isset($_POST['listing_id']) && $_POST['listing_id'] != ''){

			if($_POST['popuptype'] == 'geodir_claim_enable')
				vh_geodir_claim_popup_form($_POST['listing_id']);
			
			die();
		}
		
	}
}

function addhttp($url) {
	if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
		$url = "http://" . $url;
	}
	return $url;
}

function checkShortcode($string) {
	global $post;
	if (isset($post->post_content) && false !== stripos($post->post_content, $string)) {
		return true;
	} else {
		return false;
	}
}

// custom comment fields
function vh_custom_comment_fields($fields) {
	global $post, $commenter;

	$fields['author'] = '<div class="comment_auth_email"><p class="comment-form-author">
							<input id="author" name="author" type="text" class="span4" placeholder="' . __( 'Name', 'vh' ) . '" value="' . esc_attr( $commenter['comment_author'] ) . '" aria-required="true" size="30" />
						 </p>';

	$fields['email'] = '<p class="comment-form-email">
							<input id="email" name="email" type="text" class="span4" placeholder="' . __( 'Email', 'vh' ) . '" value="' . esc_attr( $commenter['comment_author_email'] ) . '" aria-required="true" size="30" />
						</p></div>';

	$fields['url'] = '<p class="comment-form-url">
						<input id="url" name="url" type="text" class="span4" placeholder="' . __( 'Website', 'vh' ) . '" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />
						</p>';

	$fields = array( $fields['author'], $fields['email'] );
	return $fields;
}
add_filter( 'comment_form_default_fields', 'vh_custom_comment_fields' );

function vh_date_range($first, $last, $step = '+1 day', $output_format = 'd/m/Y' ) {

    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);

    while( $current <= $last ) {

        $dates[] = date($output_format, $current);
        $current = strtotime($step, $current);
    }

    return $dates;
}

if ( ! function_exists( 'vh_comment' ) ) {
	/**
	 * Template for comments and pingbacks.
	 *
	 * To override this walker in a child theme without modifying the comments template
	 * simply create your own ac_comment(), and that function will be used instead.
	 *
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 *
	 */
	function vh_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class('geodir-comment'); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:','vh' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)','vh' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class('geodir-comment'); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<div class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 100 );
				?>
				<div class="clearfix"></div>
			</div><!-- .comment-meta -->

			<div class="comment-content comment">
				<?php
					$comment_time = get_comment_time("g/i/s/n/j/Y");
					$comment_time_exploded = explode("/", $comment_time);
					$comment_timestamp = mktime($comment_time_exploded["0"], $comment_time_exploded["1"], $comment_time_exploded["2"], $comment_time_exploded["3"], $comment_time_exploded["4"], $comment_time_exploded["5"]);
					$comment_time_full = human_time_diff($comment_timestamp, current_time('timestamp')) . " " . __("ago", "vh") . ", " .get_comment_time("g:i");
					$comment_id = get_comment_ID();
					$comment_data = get_comment( $comment_id );

					if ( $comment_data->user_id != '0' ) {
						if ( get_option('permalink_structure') ) {
							$dash_symbol = '?';
						} else {
							$dash_symbol = '&';
						}
						$author_link = "<a href='". get_author_posts_url( $comment_data->user_id ).$dash_symbol."geodir_dashbord=true&stype=" . get_post_type( $comment_data->comment_post_ID ) . "' class='item-author' itemprop='author'>". get_userdata( $comment_data->user_id )->display_name . "</a>";
					} else {
						$author_link = '<span class="guest-comment" itemprop="author">'.$comment_data->comment_author.'</span>';
					}

					printf( '%1$s<span class="reviewer-time">%2$s</span>', $author_link, $comment_time_full );
					
					comment_text();

					?>
			</div><!-- .comment-content -->

			<div class="reply comment-controls">
				<?php edit_comment_link( __( 'Edit','vh' ), '<div class="edit-link icon-pencil-squared">', '</div>' ); ?>
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply','vh' ), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				<?php
				if ( c_parent_comment_counter(get_comment_ID()) != 0 ) {
					echo "<a href=\"javascript:void(0)\" class=\"reply-count icon-chat\">".c_parent_comment_counter(get_comment_ID())." ".__("replies", "vh")."</a>";
				}
				?>
			</div><!-- .reply -->
			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.','vh' ); ?></p>
			<?php endif; ?>
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
}

if ( ! function_exists( 'vh_geodir_comment' ) ) {
	/**
	 * Template for comments and pingbacks.
	 *
	 * To override this walker in a child theme without modifying the comments template
	 * simply create your own ac_comment(), and that function will be used instead.
	 *
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 *
	 */
	function vh_geodir_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class('geodir-comment'); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:','vh' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)','vh' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class('geodir-comment'); ?> id="li-comment-<?php comment_ID(); ?>" itemprop="review" itemscope="" itemtype="http://schema.org/Review">
		<article id="comment-<?php comment_ID(); ?>" class="comment review">
			<div class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 100 );
				?>
				<div class="clearfix"></div>
			</div><!-- .comment-meta -->

			<div class="comment-content comment">
				<?php
				if ( function_exists('geodir_get_commentoverall') ) {
					$rating = geodir_get_commentoverall( get_comment_ID() );
				}
				?>
				<?php
					$comment_time = get_comment_time("g/i/s/n/j/Y");
					$comment_time_exploded = explode("/", $comment_time);
					$comment_timestamp = mktime($comment_time_exploded["0"], $comment_time_exploded["1"], $comment_time_exploded["2"], $comment_time_exploded["3"], $comment_time_exploded["4"], $comment_time_exploded["5"]);
					$comment_time_full = human_time_diff($comment_timestamp, current_time('timestamp')) . " " . __("ago", "vh") . ", " .get_comment_time("g:i");
					$comment_id = get_comment_ID();
					$comment_data = get_comment( $comment_id );
					$geodir_post_types = get_geodir_custom_post_types();

					if ( $comment_data->user_id != '0' ) {
						if ( get_option('permalink_structure') ) {
							$dash_symbol = '?';
						} else {
							$dash_symbol = '&';
						}
						$author_link = "<a href='". get_author_posts_url( $comment_data->user_id ).$dash_symbol."geodir_dashbord=true&stype=" . get_post_type( $comment_data->comment_post_ID ) . "' class='item-author' itemprop='author'>". get_userdata( $comment_data->user_id )->display_name . "</a>";
					} else {
						$author_link = '<span class="guest-comment" itemprop="author">'.$comment_data->comment_author.'</span>';
					}

					if ( in_array(get_post_type( $comment_data->comment_post_ID ), $geodir_post_types) ) {
						$comment_like = '';
						
						printf( '%1$s<div class="reviewer-rating">%2$s</div>%3$s<span class="reviewer-time">%4$s</span>', $author_link, get_listing_rating_stars( $rating, false ), $comment_like, $comment_time_full );
						?>
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
						<?php
						echo '
						<div itemprop="reviewBody" class="comment-text">
							<span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
					    		<span itemprop="ratingValue" style="display: none">'.$rating.'</span>
					    	</span><div id="translate" class="btn-translate" onClick="composetranslate()">translate</div>
					    ';
					} else {
						printf( '%1$s<span class="reviewer-time">%2$s</span>', $author_link, $comment_time_full );
					}
					
					?>
					<?php
					
					comment_text();

					//echo '<div class="clearfix"></div>';

					if ( get_option('geodir_reviewrating_enable_rating') && get_option('geodir_reviewrating_enable_review') && function_exists('geodir_reviewrating_wrap_comment_text') ) {
						$comment_like = geodir_reviewrating_comments_like_unlike($comment->comment_ID, false);
						echo $comment_like.'<div class="clearfix"></div>';
					}

					if ( in_array(get_post_type( $comment_data->comment_post_ID ), $geodir_post_types) ) {
						echo '</div>';
					}
					?>
					
					<div class="clearfix"></div>
			</div><!-- .comment-content -->

			<div class="reply comment-controls">
				<?php edit_comment_link( __( 'Edit','vh' ), '<div class="edit-link icon-pencil-squared">', '</div>' ); ?>
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply','vh' ), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				<?php
				if ( c_parent_comment_counter(get_comment_ID()) != 0 ) {
					echo "<a href=\"javascript:void(0)\" class=\"reply-count icon-chat\">".c_parent_comment_counter(get_comment_ID())." ".__("replies", "vh")."</a>";
				}
				?>
				<?php
				if( !empty($comment) && !is_admin() && !$comment->comment_parent && function_exists('geodir_reviewrating_get_comment_rating_by_id') ) {
					$comment_ratings = geodir_reviewrating_get_comment_rating_by_id($comment->comment_ID);
					$comment_rating_overall = isset($comment_ratings->overall_rating) ? $comment_ratings->overall_rating : '';
					$overall_html = geodir_reviewrating_draw_overall_rating($comment_rating_overall);
					$ratings = @unserialize($comment_ratings->ratings);
					$rating_html = geodir_reviewrating_draw_ratings($ratings);
					$comment_images = geodir_reviewrating_get_comment_images($comment->comment_ID);

					if( get_option('geodir_reviewrating_enable_images') && $comment_images != null ) {
						$total_images = 0;
						if(isset($comment_images->images) && $comment_images->images != '') {
							$total_images = explode(',',$comment_images->images);
						}

						// open lightbox on click
						$div_click = (int)get_option( 'geodir_disable_gb_modal' ) != 1 ? 'div.place-gallery' : 'div.overall-more-rating';
						$onclick = !empty($comment_images) && count($total_images)>0 ? 'onclick="javascript:jQuery(this).parent().parent().find(\'.gdreview_section\').find(\''.$div_click.' a:first\').trigger(\'click\');"' : '';
						
						$images_show_hide = '<div class="showcommentimages" comment_id="'.$comment->comment_ID.'" '.$onclick.' ><i class="fa fa-camera"></i> <a href="javascript:void(0);">';

						if (empty($comment_images) || count($total_images) == 0) {
							$images_show_hide .= __('No Photo', 'vh');
						} elseif (count($total_images) == 1) {
							$images_show_hide .= sprintf(__('%d Photo', 'vh'), 1);
						} else {
							$images_show_hide .= sprintf(__('%d Photos', 'vh'), (int)count($total_images));
						}

						$images_show_hide .= '</a></div>';

						echo $images_show_hide;
						
					};

					if ( $comment_images != null || $rating_html != '' ) {
						echo '<div class="overall-more-rating"><a href="javascript:void(0)" onclick="jQuery(this).parent().parent().parent().find(\'.gdreview_section\').find(\'.comment_more_ratings\').slideToggle()">'.__('More', 'vh').'</a></div>';
					}
				}
				?>
			</div><!-- .reply -->
			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.','vh' ); ?></p>
			<?php endif; ?>
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
}

function vh_breadcrumbs() {

	$disable_breadcrumb = get_option('vh_breadcrumb') ? get_option('vh_breadcrumb') : 'false';
	$delimiter          = get_option('vh_breadcrumb_delimiter') ? get_option('vh_breadcrumb_delimiter') : '<span class="delimiter icon-angle-circled-right"></span>';

	$home   = __('Home', 'vh'); // text for the 'Home' link
	$before = '<span class="current">'; // tag before the current crumb
	$after  = '</span>'; // tag after the current crumb

	if (!is_home() && !is_front_page() && $disable_breadcrumb == 'false') {
		global $post;
		$homeLink = home_url();

		$output = '<div class="breadcrumb">';
		$output .= '<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';

		if (is_category()) {
			global $wp_query;
			$cat_obj   = $wp_query->get_queried_object();
			$thisCat   = $cat_obj->term_id;
			$thisCat   = get_category($thisCat);
			$parentCat = get_category($thisCat->parent);
			if ($thisCat->parent != 0)
				$output .= get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' ');
			$output .= $before . 'Archive by category "' . single_cat_title('', false) . '"' . $after;
		} elseif (is_day()) {
			$output .= '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
			$output .= '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
			$output .= $before . get_the_time('d') . $after;
		} elseif (is_month()) {
			$output .= '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
			$output .= $before . get_the_time('F') . $after;
		} elseif (is_year()) {
			$output .= $before . get_the_time('Y') . $after;
		} elseif (is_single() && !is_attachment()) {
			if (get_post_type() != 'post') {
				$post_type = get_post_type_object(get_post_type());
				$slug = $post_type->rewrite;
				$output .= '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
				$output .= $before . get_the_title() . $after;
			} else {
				$cat = get_the_category();
				$cat = $cat[0];
				$output .= get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
				$output .= $before . get_the_title() . $after;
			}
		} elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
			$post_type = get_post_type_object(get_post_type());
			if ( isset($post_type) ) {
				$output .= $before . $post_type->labels->singular_name . $after;
			}
		} elseif (is_attachment()) {
			$parent = get_post($post->post_parent);
			$cat    = get_the_category($parent->ID);
			if ( isset($cat[0]) ) {
				$cat = $cat[0];
			}

			//$output .= get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
			$output .= '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
			$output .= $before . get_the_title() . $after;
		} elseif (is_page() && !$post->post_parent) {
			$output .= $before . get_the_title() . $after;
		} elseif (is_page() && $post->post_parent) {
			$parent_id   = $post->post_parent;
			$breadcrumbs = array();
			while ($parent_id) {
				$page          = get_page($parent_id);
				$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
				$parent_id     = $page->post_parent;
			}
			$breadcrumbs = array_reverse($breadcrumbs);
			foreach ($breadcrumbs as $crumb) {
				$output .= $crumb . ' ' . $delimiter . ' ';
			}
			$output .= $before . get_the_title() . $after;
		} elseif (is_search()) {
			$output .= $before . 'Search results for "' . get_search_query() . '"' . $after;
		} elseif (is_tag()) {
			$output .= $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;
		} elseif (is_author()) {
			global $vh_author;
			$userdata = get_userdata($vh_author);
			$output .= $before . 'Articles posted by ' . get_the_author() . $after;
		} elseif (is_404()) {
			$output .= $before . 'Error 404' . $after;
		}

		if (get_query_var('paged')) {
			if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
				$output .= ' (';
			$output .= __('Page', 'vh') . ' ' . get_query_var('paged');
			if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
				$output .= ')';
		}

		$output .= '</div>';

		return $output;
	}
}

/*
 * This theme supports custom background color and image, and here
 * we also set up the default background color.
 */
add_theme_support( 'custom-background', array(
	'default-color' => 'f4f4f4'
) );

function vh_sanitize_color( $input ) {
	return (string) $input;
}

/**
 * Add postMessage support for the Theme Customizer.
 */
function vh_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	$wp_customize->add_section( 'color_scheme_section', array(
		'title'    => __( 'Color Scheme', 'vh' ),
		'priority' => 35
	) );

	$wp_customize->add_setting( 'vh_color_scheme', array(
		'default'   => 'default-color-scheme',
		'transport' => 'postMessage',
		'sanitize_callback' => 'vh_sanitize_color'
	) );

	$wp_customize->add_control( new Customize_Scheme_Control( $wp_customize, 'vh_color_scheme', array(
		'label'    => 'Choose color scheme',
		'section'  => 'color_scheme_section',
		'settings' => 'vh_color_scheme'
	) ) );
}
add_action( 'customize_register', 'vh_customize_register' );

/**
 * Binds CSS and JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function vh_customize_preview_js_css() {
	wp_enqueue_script( 'vh-customizer-js', get_template_directory_uri() . '/functions/admin/js/theme-customizer.js', array( 'jquery', 'customize-preview' ), '', true );
}
add_action( 'customize_preview_init', 'vh_customize_preview_js_css' );

if (class_exists('WP_Customize_Control')) {
	class Customize_Scheme_Control extends WP_Customize_Control {
		public $type = 'radio';

		public function render_content() {
		?>
			<style>

				/* Customizer */
				.input_hidden {
					position: absolute;
					left: -9999px;
				}

				.radio-images img {
					margin-right: 4px;
					border: 2px solid #fff;
				}

				.radio-images img.selected {
					border: 2px solid #888;
					border-radius: 5px;
				}

				.radio-images label {
					display: inline-block;
					cursor: pointer;
				}
			</style>
			<script type="text/javascript">
				jQuery('.radio-images input:radio').addClass('input_hidden');
				jQuery('.radio-images img').live('click', function() {
					jQuery('.radio-images img').removeClass('selected');
					jQuery(this).addClass('selected');
				});
			</script>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div class="radio-images">
				<input type="radio" class="input_hidden" name="vh_color_scheme" <?php $this->link(); ?> id="default-color-scheme" value="default-color-scheme" />
				<label for="default-color-scheme">
					<img src="<?php echo esc_attr(get_template_directory_uri()) . '/functions/admin/images/schemes/color-scheme-default.png'; ?>"<?php echo ( $this->value() == 'default-color-scheme' ) ? ' checked="checked" class="selected"' : ''; ?> style="width: 50px; height: 50px;" alt="Default Color Scheme" />
				</label>
				<input type="radio" class="input_hidden" name="vh_color_scheme" <?php $this->link(); ?> id="green-color-scheme" value="green-color-scheme" />
				<label for="green-color-scheme">
					<img src="<?php echo esc_attr(get_template_directory_uri()) . '/functions/admin/images/schemes/color-scheme-green.png'; ?>"<?php echo ( $this->value() == 'green-color-scheme' ) ? ' checked="checked" class="selected"' : ''; ?> style="width: 50px; height: 50px;" alt="Green Color Scheme" />
				</label>
				<input type="radio" class="input_hidden" name="vh_color_scheme" <?php $this->link(); ?> id="red-color-scheme" value="red-color-scheme" />
				<label for="red-color-scheme">
					<img src="<?php echo esc_attr(get_template_directory_uri()) . '/functions/admin/images/schemes/color-scheme-red.png'; ?>"<?php echo ( $this->value() == 'red-color-scheme' ) ? ' checked="checked" class="selected"' : ''; ?> style="width: 50px; height: 50px;" alt="Red Color Scheme" />
				</label>
			</div>
		<?php
		}
	}
}

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function vh_register_required_plugins() {

	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(
		array(
			'name'     				=> 'Sky Functionality', // The plugin name
			'slug'     				=> 'sky-plugin', // The plugin slug (typically the folder name)
			'source'   				=> get_template_directory() . '/functions/tgm-activation/plugins/sky-plugin.zip', // The plugin source
			'required' 				=> true, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '2.9.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'                  => 'WPBakery Visual Composer', // The plugin name
			'slug'                  => 'js_composer', // The plugin slug (typically the folder name)
			'source'                => get_template_directory() . '/functions/tgm-activation/plugins/js_composer.zip', // The plugin source
			'required'              => true, // If false, the plugin is only 'recommended' instead of required
			'version'               => '4.8.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation'      => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url'          => '', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'Contact Form 7', // The plugin name
			'slug'     				=> 'contact-form-7', // The plugin slug (typically the folder name)
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '4.3', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'GeoDirectory', // The plugin name
			'slug'     				=> 'geodirectory', // The plugin slug (typically the folder name)
			'source'   				=> get_template_directory() . '/functions/tgm-activation/plugins/geodirectory.zip', // The plugin source
			'required' 				=> true, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '1.5.3', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'Profile Editor', // The plugin name
			'slug'     				=> 'profile-editor', // The plugin slug (typically the folder name)
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '1.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'easyReservations', // The plugin name
			'slug'     				=> 'easyreservations', // The plugin slug (typically the folder name)
			'source'   				=> get_template_directory() . '/functions/tgm-activation/plugins/easyreservations.zip', // The plugin source
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '3.5.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
		)
	);

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'domain'       		=> 'vh',         	// Text domain - likely want to be the same as your theme.
		'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
		'parent_menu_slug' 	=> 'themes.php', 				// Default parent menu slug
		'parent_url_slug' 	=> 'themes.php', 				// Default parent URL slug
		'menu'         		=> 'install-required-plugins', 	// Menu slug
		'has_notices'      	=> true,                       	// Show admin notices or not
		'is_automatic'    	=> true,					   	// Automatically activate plugins after installation or not
		'message' 			=> '',							// Message to output right before the plugins table
		'strings'      		=> array(
			'page_title'                       			=> __( 'Install Required Plugins', 'vh' ),
			'menu_title'                       			=> __( 'Install Plugins', 'vh' ),
			'installing'                       			=> __( 'Installing Plugin: %s', 'vh' ), // %1$s = plugin name
			'oops'                             			=> __( 'Something went wrong with the plugin API.', 'vh' ),
			'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
			'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
			'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
			'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
			'return'                           			=> __( 'Return to Required Plugins Installer', 'vh' ),
			'plugin_activated'                 			=> __( 'Plugin activated successfully.', 'vh' ),
			'complete' 									=> __( 'All plugins installed and activated successfully. %s', 'vh' ), // %1$s = dashboard link
			'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
		)
	);

	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'vh_register_required_plugins' );

// Enable the use of shortcodes within widgets.
add_filter( 'widget_text', 'do_shortcode' ); 

// Assign the tag for our shortcode and identify the function that will run. 
add_shortcode( 'template_directory_uri', 'wpse61170_template_directory_uri' );

// Define function 
function wpse61170_template_directory_uri() {
    return get_template_directory_uri();
}

function vh_import_listings() {
	global $wpdb;

	$custom_fields_table  = $wpdb->prefix . 'geodir_custom_fields';
	$place_detail_table   = $wpdb->prefix . 'geodir_gd_place_detail';
	$post_locations_table = $wpdb->prefix . 'geodir_post_locations';
	$post_review_table = $wpdb->prefix . 'geodir_post_review';

	// Clear custom fields table of its content
	$wpdb->query("TRUNCATE TABLE ".$custom_fields_table);

	// Insert custom geodirectory fields
	$wpdb->query("INSERT INTO " . $custom_fields_table . " (`post_type`, `data_type`, `field_type`, `admin_title`, `admin_desc`, `site_title`, `htmlvar_name`, `default_value`, `sort_order`, `option_values`, `clabels`, `is_active`, `is_default`, `is_admin`, `is_required`, `required_msg`, `show_on_listing`, `show_on_detail`, `show_as_tab`, `for_admin_use`, `packages`, `cat_sort`, `cat_filter`, `extra_fields`, `field_icon`, `css_class`, `decimal_point`) VALUES
	('gd_place', 'VARCHAR', 'taxonomy', 'Category', 'Select listing category from here. Select at least one category', 'Category', 'gd_placecategory', '', 0, '', 'Category', '1', '1', '1', '1', '', '', '', '', '', ',1,2', '', '', '', '', '', ''),
	('gd_place', '', 'address', 'Address', 'Please enter listing address. eg. : <b>230 Vine Street</b>', 'Address', 'post', '', 1, '', 'Address', '1', '1', '1', '1', 'Address fields are required', '', '', '', '', ',1,2', '', '', 'a:15:{s:9:\"show_city\";i:1;s:10:\"city_lable\";s:4:\"City\";s:11:\"show_region\";i:1;s:12:\"region_lable\";s:6:\"Region\";s:12:\"show_country\";i:1;s:13:\"country_lable\";s:7:\"Country\";s:8:\"show_zip\";i:1;s:9:\"zip_lable\";s:13:\"Zip/Post Code\";s:8:\"show_map\";i:1;s:9:\"map_lable\";s:18:\"Set Address On Map\";s:12:\"show_mapview\";i:1;s:13:\"mapview_lable\";s:15:\"Select Map View\";s:12:\"show_mapzoom\";i:1;s:13:\"mapzoom_lable\";s:6:\"hidden\";s:11:\"show_latlng\";i:1;}', '', '', ''),
	('gd_place', 'VARCHAR', 'text', 'Time', 'Enter Business or Listing Timing Information.<br/>eg. : 10.00 am to 6 pm every day', 'Time', 'geodir_timing', '', 2, '', 'Time', '1', '1', '1', '0', '', '', '', '', '', '1,2,', '', '', '', '', '', ''),
	('gd_place', 'VARCHAR', 'phone', 'Phone', 'You can enter phone number,cell phone number etc.', 'Phone', 'geodir_contact', '', 3, '', 'Phone', '1', '1', '1', '0', '', '', '', '', '', '1,2,', '', '', '', '', '', ''),
	('gd_place', 'VARCHAR', 'email', 'Email', 'You can enter your business or listing email.', 'Email', 'geodir_email', '', 4, '', 'Email', '1', '1', '1', '0', '', '', '', '', '', '1,2,', '', '', '', '', '', ''),
	('gd_place', 'VARCHAR', 'url', 'Website', 'You can enter your business or listing website.', 'Website', 'geodir_website', '', 5, '', 'Website', '1', '1', '1', '0', '', '', '', '', '', '1,2,', '', '', '', '', '', ''),
	('gd_place', 'VARCHAR', 'url', 'Twitter', 'You can enter your business or listing twitter url.', 'Twitter', 'geodir_twitter', '', 6, '', 'Twitter', '1', '1', '1', '0', '', '', '', '', '', '1,2,', '', '', '', '', '', ''),
	('gd_place', 'VARCHAR', 'url', 'Facebook', 'You can enter your business or listing facebook url.', 'Facebook', 'geodir_facebook', '', 7, '', 'Facebook', '1', '1', '1', '0', '', '', '', '', '', '1,2,', '', '', '', '', '', ''),
	('gd_place', 'TEXT', 'textarea', 'Video', 'Add video code here, YouTube etc.', 'Video', 'geodir_video', '', 8, '', 'Video', '1', '0', '1', '0', '', '', '', '', '', '1,2,', '', '', '', '', '', ''),
	('gd_place', 'TEXT', 'textarea', 'Special Offers', 'Note: List out any special offers (optional)', 'Special Offers', 'geodir_special_offers', '', 9, '', 'Special Offers', '1', '0', '1', '0', '', '', '', '', '', '1,2,', '', '', '', '', '', ''),
	('gd_place', 'INT', 'text', 'Guest count', '', 'guests', 'geodir_listing_guest_count', '', 10, '', '', '1', '0', '0', '0', '', '1', '1', '0', '0', '1,2', '0', '', '', 'http://cohhe.com/demo/sky/sky-vacation/wp-content/themes/sky-directory/images/guest-new.png', '', ''),
	('gd_place', 'INT', 'text', 'Bedroom count', '', 'bedrooms', 'geodir_listing_bedroom_count', '', 11, '', '', '1', '0', '0', '0', '', '1', '1', '0', '0', '1,2', '0', '', '', 'http://cohhe.com/demo/sky/sky-vacation/wp-content/themes/sky-directory/images/door-new.png', '', ''),
	('gd_place', 'INT', 'text', 'Bed count', '', 'beds', 'geodir_listing_bed_count', '', 12, '', '', '1', '0', '0', '0', '', '1', '1', '0', '0', '1,2', '0', '', '', 'http://cohhe.com/demo/sky/sky-vacation/wp-content/themes/sky-directory/images/bed-new.png', '', ''),
	('gd_place', 'INT', 'text', 'Listing price', '', 'Listing price', 'geodir_listing_price', '', 13, '', '', '1', '0', '0', '0', '', '0', '0', '0', '0', '1,2,', '', '', '', '', '', ''),
	('gd_place', 'DATE', 'datepicker', 'Listing start date', '', 'Listing start date', 'geodir_listing_start_date', '', 14, '', '', '1', '0', '0', '0', '', '0', '0', '0', '0', '1,2,', '', '', 'a:1:{s:11:\"date_format\";s:0:\"\";}', '', '', ''),
	('gd_place', 'DATE', 'datepicker', 'Listing end date', '', 'Listing end date', 'geodir_listing_end_date', '', 15, '', '', '1', '0', '0', '0', '', '0', '0', '0', '0', '1,2,', '', '', 'a:1:{s:11:\"date_format\";s:0:\"\";}', '', '', ''),
	('gd_place', 'INT', 'text', 'Adult count', '', 'Adult count', 'geodir_adult_count', '', 16, '', '', '1', '0', '0', '0', '', '0', '0', '0', '0', '1,2,', '', '', '', '', '', ''),
	('gd_place', 'INT', 'text', 'Children count', '', 'Children count', 'geodir_children_count', '', 17, '', '', '1', '0', '0', '0', '', '0', '0', '0', '0', '1,2,', '', '', '', '', '', ''),
	('gd_place', '', 'select', 'Listing type', '', 'Listing type', 'geodir_listing_type', '', 18, 'Sale,Rent', '', '1', '0', '0', '0', '', '0', '0', '0', '0', '1,2,', '', '', '', '', '', ''),
	('gd_place', 'INT', 'text', 'Bathroom count', '', 'Bathroom count', 'geodir_bathroom_count', '', 19, '', '', '1', '0', '0', '0', '', '0', '0', '0', '0', '1,2,', '', '', '', '', '', ''),
	('gd_place', 'INT', 'text', 'Listing square feet', '', 'Listing square feet', 'geodir_square_feet', '', 20, '', '', '0', '0', '0', '1', '', '0', '0', '0', '0', '1', '', '', '', '', '', '');");

	// Drop existing table and create new one
	$wpdb->query("DROP TABLE IF EXISTS " . $place_detail_table);
	$wpdb->query("CREATE TABLE " . $place_detail_table . " (
	  `post_id` int(11) NOT NULL AUTO_INCREMENT,
	  `post_title` text,
	  `post_status` varchar(20) DEFAULT NULL,
	  `default_category` int(11) DEFAULT NULL,
	  `post_tags` varchar(254) DEFAULT NULL,
	  `post_location_id` int(11) NOT NULL,
	  `marker_json` text,
	  `claimed` enum('1','0') DEFAULT '0',
	  `businesses` enum('1','0') DEFAULT '0',
	  `is_featured` enum('1','0') DEFAULT '0',
	  `featured_image` varchar(254) DEFAULT NULL,
	  `paid_amount` double NOT NULL DEFAULT '0',
	  `package_id` int(11) NOT NULL DEFAULT '0',
	  `alive_days` int(11) NOT NULL DEFAULT '0',
	  `paymentmethod` varchar(30) DEFAULT NULL,
	  `expire_date` varchar(25) DEFAULT NULL,
	  `submit_time` varchar(15) DEFAULT NULL,
	  `submit_ip` varchar(20) DEFAULT NULL,
	  `overall_rating` int(11) DEFAULT '0',
	  `rating_count` int(11) DEFAULT '0',
	  `post_locations` varchar(254) DEFAULT NULL,
	  `post_dummy` enum('1','0') DEFAULT '0',
	  `gd_placecategory` varchar(254) DEFAULT NULL,
	  `post_address` varchar(254) DEFAULT NULL,
	  `post_city` varchar(30) DEFAULT NULL,
	  `post_region` varchar(30) DEFAULT NULL,
	  `post_country` varchar(30) DEFAULT NULL,
	  `post_zip` varchar(15) DEFAULT NULL,
	  `post_latitude` varchar(20) DEFAULT NULL,
	  `post_longitude` varchar(20) DEFAULT NULL,
	  `post_mapview` varchar(15) DEFAULT NULL,
	  `post_mapzoom` varchar(3) DEFAULT NULL,
	  `post_latlng` varchar(3) DEFAULT '1',
	  `geodir_timing` varchar(254) DEFAULT NULL,
	  `geodir_contact` varchar(254) DEFAULT NULL,
	  `geodir_email` varchar(254) DEFAULT NULL,
	  `geodir_website` varchar(254) DEFAULT NULL,
	  `geodir_twitter` varchar(254) DEFAULT NULL,
	  `geodir_facebook` varchar(254) DEFAULT NULL,
	  `geodir_video` text,
	  `geodir_special_offers` text,
	  `post_neighbourhood` varchar(30) DEFAULT NULL,
	  `geodir_listing_guest_count` int(11) DEFAULT NULL,
	  `geodir_listing_bedroom_count` int(11) DEFAULT NULL,
	  `geodir_listing_bed_count` int(11) DEFAULT NULL,
	  `geodir_listing_price` int(11) DEFAULT NULL,
	  `geodir_listing_start_date` date DEFAULT NULL,
	  `geodir_listing_end_date` date DEFAULT NULL,
	  `geodir_adult_count` int(11) DEFAULT NULL,
	  `geodir_children_count` int(11) DEFAULT NULL,
	  `geodir_listing_type` varchar(254) DEFAULT NULL,
	  `geodir_bathroom_count` int(11) DEFAULT NULL,
	  `geodir_square_feet` int(11) DEFAULT NULL,
	  `expire_notification` enum('false','true') NOT NULL,
	  PRIMARY KEY (`post_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

	// Insert listings data
	$wpdb->query("INSERT INTO " . $place_detail_table . " (`post_id`, `post_title`, `post_status`, `default_category`, `post_tags`, `post_location_id`, `marker_json`, `claimed`, `businesses`, `is_featured`, `featured_image`, `paid_amount`, `package_id`, `alive_days`, `paymentmethod`, `expire_date`, `submit_time`, `submit_ip`, `overall_rating`, `rating_count`, `post_locations`, `post_dummy`, `gd_placecategory`, `post_address`, `post_city`, `post_region`, `post_country`, `post_zip`, `post_latitude`, `post_longitude`, `post_mapview`, `post_mapzoom`, `post_latlng`, `geodir_timing`, `geodir_contact`, `geodir_email`, `geodir_website`, `geodir_twitter`, `geodir_facebook`, `geodir_video`, `geodir_special_offers`, `post_neighbourhood`, `geodir_listing_guest_count`, `geodir_listing_bedroom_count`, `geodir_listing_bed_count`, `geodir_listing_price`, `geodir_listing_start_date`, `geodir_listing_end_date`, `geodir_adult_count`, `geodir_children_count`, `geodir_listing_type`, `geodir_bathroom_count`, `geodir_square_feet`, `expire_notification`) VALUES
	(".vh_get_listing_id('Loire Valley').", 'Franklin Square', 'publish', 2, 'Popular,Sample Tags,Tags', 8, '{\"id\":\"17\",\"lat_pos\": \"41.9028379\",\"long_pos\": \"12.494770499999959\",\"marker_id\":\"17_2\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Attractions1.png\",\"group\":\"catgroup2\"}', '', '', '0', '/2015/01/17_photo-1417021423914-070979c8eb34.jpg', 0, 1, 0, NULL, 'Never', '1421251482', '141.101.88.112', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '1', ',2,8,', 'Via Torino, 97', 'Rome', 'Lazio', 'Italy', '00185', '41.9028379', '12.494770499999959', 'ROADMAP', '12', '1', 'Open today until 1 p.m., Sunday 10 am to 9 pm', '(111) 677-4444', 'info@franklinsq.com', 'http://franklinsquare.com', 'http://twitter.com/franklinsquare', 'http://facebook.com/franklinsquare', 'http://vimeo.com/106787023', '', NULL, 1, 4, 3, 120, '2014-12-12', '2015-12-12', 3, 3, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Please Touch Museum').", 'Please Touch Museum', 'publish', 2, 'Popular,Sample Tags,Tags', 8, '{\"id\":\"19\",\"lat_pos\": \"41.8808089\",\"long_pos\": \"12.48997989999998\",\"marker_id\":\"19_2\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Attractions1.png\",\"group\":\"catgroup2\"}', '', '', '0', '/2015/01/19_5fcb0a55.jpg', 0, 1, 0, NULL, 'Never', '1421251480', '141.101.88.112', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '1', ',2,8,', 'Piazza di Santa Balbina', 'Rome', 'Lazio', 'Italy', '00153', '41.8808089', '12.48997989999998', 'ROADMAP', '14', '1', 'Open today until 1 p.m., Sunday 10 am to 9 pm', '(222) 777-1111', 'info@pleasetouchmuseum.com', 'http://pleasetouchmuseum.com', 'http://twitter.com/pleasetouchmuseum', 'http://facebook.com/pleasetouchmuseum', 'http://vimeo.com/106787023', '', NULL, 2, 4, 1, 120, '2014-12-12', '2015-12-12', 4, 3, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Longwood Gardens').", 'Longwood Gardens', 'publish', 2, 'garden,Popular,wood', 8, '{\"id\":\"21\",\"lat_pos\": \"41.873902312963935\",\"long_pos\": \"12.539280844238306\",\"marker_id\":\"21_2\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Attractions1.png\",\"group\":\"catgroup2\"}', '', '', '0', '/2015/01/21_photo-1413897255566-39fda18f373f.jpg', 0, 1, 0, NULL, 'Never', '1421251477', '141.101.88.112', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '1', ',2,', 'Vicolo di Villa Berta', 'Rome', 'Lazio', 'Italy', '00181', '41.873902312963935', '12.539280844238306', 'ROADMAP', '11', '1', 'Open today until 1 p.m., Sunday 10 am to 9 pm', '(111) 888-1111', 'info@longwoodgardens.com', 'http://longwoodgardens.com', 'http://twitter.com/longwoodgardens', 'http://facebook.com/longwoodgardens', 'http://vimeo.com/106787023', '', NULL, 3, 2, 2, 90, '2014-12-12', '2015-12-12', 4, 4, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('The Philadelphia Zoo').", 'The Philadelphia Zoo', 'publish', 2, 'garden,Popular,wood', 8, '{\"id\":\"23\",\"lat_pos\": \"41.9294928\",\"long_pos\": \"12.508820499999956\",\"marker_id\":\"23_2\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Attractions1.png\",\"group\":\"catgroup2\"}', '', '', '0', '/2015/01/23_photo-1415033523948-6c31d010530d.jpg', 0, 1, 0, NULL, 'Never', '1421251474', '141.101.88.112', 4, 1, '[line-lexington],[pennsylvania],[united-states]', '1', ',2,', 'Via di Priscilla, 53', 'Rome', 'Lazio', 'Italy', '00199', '41.9294928', '12.508820499999956', 'ROADMAP', NULL, '1', 'Open today until 11.30 a.m., Sunday 11 am to 7 pm', '(211) 143-1900', 'info@philadelphiazoo.com', 'http://philadelphiazoo.com', 'http://twitter.com/philadelphiazoo', 'http://facebook.com/philadelphiazoo', 'http://vimeo.com/106787023', '', NULL, 4, 5, 3, 60, '2014-12-12', '2015-12-12', 4, 3, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('National Constitution Center').", 'National Constitution Center', 'publish', 2, 'Center,Popular,Tag', 8, '{\"id\":\"25\",\"lat_pos\": \"41.9271737\",\"long_pos\": \"12.477041800000052\",\"marker_id\":\"25_2\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Attractions1.png\",\"group\":\"catgroup2\"}', '', '', '0', '/2015/01/25_photo-1417870839255-a23faa90c6b0.jpg', 0, 1, 0, NULL, 'Never', '1421251470', '141.101.88.112', 4, 1, '[line-lexington],[pennsylvania],[united-states]', '1', ',2,8,', 'Via Barnaba Tortolini, 19', 'Rome', 'Lazio', 'Italy', '00197', '41.9271737', '12.477041800000052', 'ROADMAP', NULL, '1', 'Open today until 9.30 a.m., Sunday 11 am to 7 pm', '(111) 111-1111', 'info@ncc.com', 'http://ncc.com', 'http://twitter.com/ncc', 'http://facebook.com/ncc', 'http://vimeo.com/106787023', '', NULL, 5, 2, 1, 80, '2014-12-12', '2015-12-12', 3, 2, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Sadsbury Woods Preserve').", 'Sadsbury Woods Preserve', 'publish', 2, 'Popular,sample,Tags', 3, '{\"id\":\"27\",\"lat_pos\": \"41.3985292\",\"long_pos\": \"2.1371030999999903\",\"marker_id\":\"27_2\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Attractions1.png\",\"group\":\"catgroup2\"}', '', '', '0', '/2015/01/27_photo-1417436026361-a033044d901f.jpg', 0, 1, 0, NULL, 'Never', '1421251467', '141.101.88.112', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '1', ',2,', 'Carrer de Raset, 28', 'Barcelona', 'Catalunya', 'Spain', '08021', '41.3985292', '2.1371030999999903', 'ROADMAP', NULL, '1', 'Open today until 12.30 p.m., Sunday 12 pm to 7 pm', '(222) 999-9999', 'info@swp.com', 'http://swp.com', 'http://twitter.com/swp', 'http://facebook.com/swp', 'http://vimeo.com/106787023', '', NULL, 2, 1, 1, 70, '2014-12-12', '2015-12-12', 4, 3, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Museum Without Walls').", 'Museum Without Walls', 'publish', 2, 'Museum,Popular', 4, '{\"id\":\"29\",\"lat_pos\": \"41.4189914\",\"long_pos\": \"2.169573600000035\",\"marker_id\":\"29_2\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Attractions1.png\",\"group\":\"catgroup2\"}', '', '', '0', '/2015/01/29_photo-1417434743061-9873e0beaed6.jpg', 0, 1, 0, NULL, 'Never', '1421251221', '141.101.88.112', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '1', ',2,', 'Carrer de Florència, 25', 'Barcelona', 'Barcelona', 'Spain', '08041', '41.4189914', '2.169573600000035', 'ROADMAP', '13', '1', 'Open today until 10.30 a.m., Sunday 10 am to 7 pm', '(222) 999-9999', 'info@mwwalls.com', 'http://museumwithoutwallsaudio.org/', 'http://twitter.com/mwwalls', 'http://facebook.com/mwwalls', 'http://vimeo.com/106787023', '', NULL, 3, 1, 1, 70, '2014-12-12', '2015-12-12', 5, 3, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Audacious Freedom').", 'Audacious Freedom', 'publish', 2, 'Popular,Tag1', 3, '{\"id\":\"31\",\"lat_pos\": \"41.3610154\",\"long_pos\": \"2.1593815999999606\",\"marker_id\":\"31_2\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Attractions1.png\",\"group\":\"catgroup2\"}', '', '', '0', '/2015/01/31_photo-1414589491349-2acf6131176e.jpg', 0, 1, 0, NULL, 'Never', '1421943762', '141.101.88.133', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '1', ',2,', 'Passeig del Migdia, 176', 'Barcelona', 'Catalunya', 'Spain', '08038', '41.3610154', '2.1593815999999606', 'ROADMAP', '12', '1', 'Open today until 11.30 a.m., Sunday 1 pm to 7 pm', '(777) 777-7777', 'info@aampmuseum.com', 'http://www.aampmuseum.org/', 'http://twitter.com/Cohhe_Themes', 'http://facebook.com/cohhethemes', 'http://vimeo.com/106787023', '', NULL, 2, 4, 1, 80, '2014-12-12', '2015-12-12', 4, 1, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('The Liberty Bell Center').", 'The Liberty Bell Center', 'publish', 2, 'Popular', 3, '{\"id\":\"33\",\"lat_pos\": \"41.3832306\",\"long_pos\": \"2.1067299999999705\",\"marker_id\":\"33_2\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Attractions1.png\",\"group\":\"catgroup2\"}', '', '', '0', '/2015/01/33_moss-2.jpg', 0, 1, 0, NULL, 'Never', '1421943743', '141.101.88.133', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '1', ',2,8,', 'Avinguda Diagonal, 716', 'Barcelona', 'Catalunya', 'Spain', '08034', '41.3832306', '2.1067299999999705', 'ROADMAP', '11', '1', 'The center is open year round, 9 a.m. – 5 p.m., with extended hours in the summer.', '(777) 666-6666', 'info@nps.com', 'http://www.nps.gov/inde', 'http://twitter.com/Cohhe_Themes', 'http://facebook.com/cohhethemes', 'http://vimeo.com/106787023', '', NULL, 4, 4, 4, 90, '2014-12-12', '2015-12-12', 0, 1, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Rittenhouse Square').", 'Rittenhouse Square', 'publish', 2, 'Museum,Popular', 3, '{\"id\":\"35\",\"lat_pos\": \"41.3895964\",\"long_pos\": \"2.1854107999999997\",\"marker_id\":\"35_2\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Attractions1.png\",\"group\":\"catgroup2\"}', '', '', '0', '/2015/01/35_landscape-2.jpg', 0, 1, 0, NULL, 'Never', '1421251207', '141.101.88.112', 4, 2, '[line-lexington],[pennsylvania],[united-states]', '1', ',2,', 'Avinguda de Joaquim Renart', 'Barcelona', 'Catalunya', 'Spain', '08003', '41.3895964', '2.1854107999999997', 'ROADMAP', '13', '1', 'The center is open year round, 9 a.m. – 5 p.m., with extended hours in the summer.', '(777) 666-6666', 'info@fairmountpark.com', 'http://www.fairmountpark.org/rittenhousesquare.asp', 'http://twitter.com/fairmountpark', 'http://facebook.com/fairmountpark', 'http://vimeo.com/106787023', '', NULL, 3, 1, 4, 70, '2014-12-12', '2015-12-12', 5, 3, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Loews Philadelphia Hotel').", 'Loews Philadelphia Hotel', 'publish', 3, '', 2, '{\"id\":\"37\",\"lat_pos\": \"40.71922881098176\",\"long_pos\": \"-73.98842828564449\",\"marker_id\":\"37_3\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Hotels1.png\",\"group\":\"catgroup3\"}', '', '', '0', '/2015/01/37_field-2.jpg', 0, 1, 0, NULL, 'Never', '1421251202', '141.101.88.112', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '1', ',3,8,', '110 Ludlow Street', 'New York', 'New York', 'United States', '10002', '40.71922881098176', '-73.98842828564449', 'ROADMAP', '13', '1', 'Daily, 6:30 am – 12:00 pm', '(111) 111-0000', 'info@loewshotels.com', 'http://www.loewshotels.com/en/hotels/philadelphia-hotel/overview.aspx', 'http://twitter.com/loewshotels', 'http://facebook.com/loewshotels', 'http://vimeo.com/106787023', '', NULL, 5, 3, 1, 65, '2014-12-12', '2015-12-12', 4, 1, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Embassy Suites Philadelphia').", 'Embassy Suites Philadelphia', 'publish', 3, '', 2, '{\"id\":\"39\",\"lat_pos\": \"40.7099328\",\"long_pos\": \"-74.00911489999999\",\"marker_id\":\"39_3\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Hotels1.png\",\"group\":\"catgroup3\"}', '', '', '0', '/2015/01/39_canal-2.jpg', 0, 1, 0, NULL, 'Never', '1421251198', '141.101.88.112', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '1', ',3,', '10 John Street', 'New York', 'New York', 'United States', '10038', '40.7099328', '-74.00911489999999', 'ROADMAP', '14', '1', 'Daily, 10:30 am – 10 pm', '(111) 111-0000', 'info@embassysuites1.com', 'http://embassysuites1.hilton.com/en_US/es/hotel/PHLDTES-Embassy-Suites-Philadelphia-Center-City-Pennsylvania/index.do', 'http://twitter.com/embassysuites1', 'http://facebook.com/embassysuites1', 'http://vimeo.com/106787023', '', NULL, 3, 1, 5, 160, '2014-12-12', '2015-12-12', 3, 1, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Doubletree Hotel Philadelphia').", 'Doubletree Hotel Philadelphia', 'publish', 3, '', 2, '{\"id\":\"41\",\"lat_pos\": \"40.720603\",\"long_pos\": \"-73.99604320000003\",\"marker_id\":\"41_3\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Hotels1.png\",\"group\":\"catgroup3\"}', '', '', '0', '/2015/01/41_building-2.jpg', 0, 1, 0, NULL, 'Never', '1421251196', '141.101.88.112', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '1', ',3,', '179 Mott Street', 'New York', 'New York', 'United States', '10012', '40.720603', '-73.99604320000003', 'ROADMAP', '14', '1', 'Daily, 10:30 am – 10 pm', '(111) 111-0000', 'info@doubletree1.com', 'http://doubletree1.hilton.com/en_US/dt/hotel/PHLBLDT-Doubletree-Hotel-Philadelphia-Pennsylvania/index.do', 'http://twitter.com/doubletree1', 'http://facebook.com/doubletree1', 'http://vimeo.com/106787023', '', NULL, 3, 3, 2, 95, '2014-12-12', '2015-12-12', 1, 1, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Philadelphia Marriott Downtown').", 'Philadelphia Marriott Downtown', 'publish', 3, '', 2, '{\"id\":\"43\",\"lat_pos\": \"40.759299360221185\",\"long_pos\": \"-73.98314507764894\",\"marker_id\":\"43_3\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Hotels1.png\",\"group\":\"catgroup3\"}', '', '', '0', '/2015/01/43_42dcc5ea.jpg', 0, 1, 0, NULL, 'Never', '1421251190', '141.101.88.112', 4, 1, '[line-lexington],[pennsylvania],[united-states]', '1', ',3,8,', '148 West 48th Street', 'New York', 'New York', 'United States', '10036', '40.759299360221185', '-73.98314507764894', 'ROADMAP', '14', '1', '24 Hours', '(123) 111-2222', 'info@marriott.com', 'http://www.marriott.com/hotels/travel/phldt-philadelphia-marriott-downtown/', 'http://twitter.com/marriott', 'http://facebook.com/marriott', 'http://vimeo.com/106787023', '', NULL, 3, 1, 1, 45, '2014-12-12', '2015-12-12', 4, 5, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Hilton Inn at Penn').", 'Hilton Inn at Penn', 'publish', 3, '', 2, '{\"id\":\"45\",\"lat_pos\": \"40.7560681\",\"long_pos\": \"-73.99012249999998\",\"marker_id\":\"45_3\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Hotels1.png\",\"group\":\"catgroup3\"}', '', '', '0', '/2015/01/45_0c4125681.jpg', 0, 1, 0, NULL, 'Never', '1421251186', '141.101.88.112', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '1', ',3,5,', '242 West 41st Street', 'New York', 'New York', 'United States', '10036', '40.7560681', '-73.99012249999998', 'ROADMAP', '14', '1', 'Daily : 11 am to 11 pm', '(888) 888-8888', 'info@theinnatpenn.com', 'http://www.theinnatpenn.com/', 'http://twitter.com/theinnatpenn', 'http://facebook.com/theinnatpenn', 'http://vimeo.com/106787023', '', NULL, 4, 1, 1, 80, '2014-12-12', '2015-12-12', 1, 5, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Red Fort').", 'Red Fort', 'publish', 2, '', 7, '{\"id\":\"184\",\"lat_pos\": \"28.646707674948644\",\"long_pos\": \"77.1616722863281\",\"marker_id\":\"184_2\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Attractions1.png\",\"group\":\"catgroup2\"}', '', '', '0', '/2015/01/184_kKvwBPaT9ucgomTWSQx9_switzerland-photo.jpg', 0, 1, 0, NULL, 'Never', '1421251157', '141.101.88.112', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '0', ',2,', 'Shadipur Main Bazar Road', 'New Delhi', 'Delhi', 'India', '110008', '28.646707674948644', '77.1616722863281', 'ROADMAP', NULL, '1', '', '', '', '', '', '', 'http://vimeo.com/106787023', '', NULL, 3, 5, 3, 50, '2014-12-12', '2015-12-12', 1, 4, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Marseille').", 'Marseille', 'publish', 5, 'Featured,Popular', 6, '{\"id\":\"168\",\"lat_pos\": \"48.8765302\",\"long_pos\": \"2.2911222999999836\",\"marker_id\":\"168_5\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Food_Nightlife1.png\",\"group\":\"catgroup5\"}', '', '', '0', '/2015/01/168_6108b580.jpg', 0, 1, 0, NULL, 'Never', '1422458531', '141.101.88.133', 4, 3, '[line-lexington],[pennsylvania],[united-states]', '0', ',5,', '20 Rue des Acacias', 'Paris', 'Île-de-France', 'France', '75017', '48.8765302', '2.2911222999999836', 'ROADMAP', NULL, '1', '', '', '', '', '', '', 'http://vimeo.com/106787023', '', NULL, 1, 3, 3, 90, '2014-12-12', '2015-12-12', 5, 1, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Lyon').", 'Lyon', 'publish', 4, 'Featured,Popular', 6, '{\"id\":\"177\",\"lat_pos\": \"48.88539610000001\",\"long_pos\": \"2.3540193000000045\",\"marker_id\":\"177_4\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Restaurants1.png\",\"group\":\"catgroup4\"}', '', '', '0', '/2015/01/177_eda0fb7c.jpg', 0, 1, 0, NULL, 'Never', '1421251176', '141.101.88.112', 5, 3, '[line-lexington],[pennsylvania],[united-states]', '0', ',4,', '7 Rue Polonceau', 'Paris', 'Île-de-France', 'France', '75018', '48.88539610000001', '2.3540193000000045', 'ROADMAP', NULL, '1', '', '', '', '', '', '', 'http://vimeo.com/106787023', '', NULL, 1, 1, 1, 170, '2014-12-12', '2015-12-12', 2, 3, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Strasbourg').", 'Strasbourg', 'publish', 6, 'Featured,Popular', 6, '{\"id\":\"176\",\"lat_pos\": \"48.8404602\",\"long_pos\": \"2.3974878999999873\",\"marker_id\":\"176_6\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Festival1.png\",\"group\":\"catgroup6\"}', '', '', '0', '/2015/01/176_JaI1BywIT5Or8Jfmci1E_zakopane.jpg', 0, 1, 0, NULL, 'Never', '1421251171', '141.101.88.112', 4, 2, '[line-lexington],[pennsylvania],[united-states]', '0', ',6,', '15 Rue Lamblardie', 'Paris', 'Île-de-France', 'France', '75012', '48.8404602', '2.3974878999999873', 'ROADMAP', NULL, '1', '', '', '', '', '', '', 'http://vimeo.com/106787023', '', NULL, 3, 3, 2, 115, '2014-12-12', '2015-12-12', 1, 3, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Arles').", 'Arles', 'publish', 2, 'Featured,Popular', 6, '{\"id\":\"175\",\"lat_pos\": \"48.83180489999999\",\"long_pos\": \"2.299938999999995\",\"marker_id\":\"175_2\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Attractions1.png\",\"group\":\"catgroup2\"}', '', '', '0', '/2015/01/175_home-decor-555096_1280.jpg', 0, 1, 0, NULL, 'Never', '1421251100', '141.101.88.112', 4, 2, '[line-lexington],[pennsylvania],[united-states]', '0', ',2,', '104 Rue Brancion', 'Paris', 'Île-de-France', 'France', '75015', '48.83180489999999', '2.299938999999995', 'ROADMAP', NULL, '1', '', '', '', '', '', '', 'http://vimeo.com/106787023', '', NULL, 1, 4, 4, 150, '2014-12-12', '2015-12-12', 4, 1, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Biarritz').", 'Biarritz', 'publish', 8, 'Featured,Popular', 6, '{\"id\":\"174\",\"lat_pos\": \"48.8486383\",\"long_pos\": \"2.258570700000064\",\"marker_id\":\"174_8\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Feature1.png\",\"group\":\"catgroup8\"}', '', '', '0', '/2015/01/174_room-416043_1280.jpg', 0, 1, 0, NULL, 'Never', '1421251167', '141.101.88.112', 5, 2, '[line-lexington],[pennsylvania],[united-states]', '0', ',8,', '2 Place de la Porte d''Auteuil', 'Paris', 'Île-de-France', 'France', '75016', '48.8486383', '2.258570700000064', 'ROADMAP', '15', '1', '', '', '', '', '', '', 'http://vimeo.com/106787023', '', NULL, 3, 2, 5, 85, '2014-12-12', '2015-12-12', 3, 5, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Loire Valley').", 'Loire Valley', 'publish', 5, 'Featured,Popular', 6, '{\"id\":\"173\",\"lat_pos\": \"48.859341\",\"long_pos\": \"2.3198198000000048\",\"marker_id\":\"173_5\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Food_Nightlife1.png\",\"group\":\"catgroup5\"}', '', '', '0', '/2015/01/173_the-interior-of-the-428657_1280.jpg', 0, 1, 0, NULL, 'Never', '1421251162', '141.101.88.112', 4, 2, '[line-lexington],[pennsylvania],[united-states]', '0', ',5,', '12 Rue Saint-Dominique', 'Paris', 'Île-de-France', 'France', '75007', '48.859341', '2.3198198000000048', 'ROADMAP', '12', '1', '', '', '', '', '', '', 'http://vimeo.com/106787023', '', NULL, 3, 5, 1, 60, '2014-12-12', '2015-12-12', 2, 3, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Jama Masjid').", 'Jama Masjid', 'publish', 8, '', 7, '{\"id\":\"169\",\"lat_pos\": \"28.654507\",\"long_pos\": \"77.2202092\",\"marker_id\":\"169_8\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Feature1.png\",\"group\":\"catgroup8\"}', '', '', '0', '/2015/01/169_photo-1415226161018-3ec581fa733d.jpg', 0, 1, 0, NULL, 'Never', '1421251152', '141.101.88.112', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '0', ',8,', 'Gali Samosan', 'New Delhi', 'Delhi', 'India', '110006', '28.654507', '77.2202092', 'ROADMAP', '13', '1', '', '', '', '', '', '', 'http://vimeo.com/106787023', '', NULL, 5, 4, 2, 70, '2014-12-12', '2015-12-12', 1, 3, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Chandni Chowk').", 'Chandni Chowk', 'publish', 6, '', 7, '{\"id\":\"170\",\"lat_pos\": \"28.6345336\",\"long_pos\": \"77.27822850000007\",\"marker_id\":\"170_6\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Festival1.png\",\"group\":\"catgroup6\"}', '', '', '0', '/2015/01/170_photo-1415226481302-c40f24f4d45e.jpg', 0, 1, 0, NULL, 'Never', '1422285707', '141.101.88.133', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '0', ',6,', 'Block K', 'New Delhi', 'Delhi', 'India', '110031', '28.6345336', '77.27822850000007', 'ROADMAP', '12', '1', '', '', '', '', '', '', 'http://vimeo.com/106787023', '', NULL, 2, 2, 1, 120, '2014-12-12', '2015-12-12', 2, 4, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Swaminarayan Akshardham').", 'Swaminarayan Akshardham', 'publish', 5, '', 7, '{\"id\":\"171\",\"lat_pos\": \"28.5759905\",\"long_pos\": \"77.24459950000005\",\"marker_id\":\"171_5\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Food_Nightlife1.png\",\"group\":\"catgroup5\"}', '', '', '0', '/2015/01/171_rVvIisyfQwOhZv35PPhh_unsplash.jpg', 0, 1, 0, NULL, 'Never', '1421251066', '141.101.88.112', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '0', ',5,', 'C-31/G,  Shiv Mandir Marg', 'New Delhi', 'Delhi', 'India', '110024', '28.5759905', '77.24459950000005', 'ROADMAP', NULL, '1', '', '', '', '', '', '', 'http://vimeo.com/106787023', '', NULL, 2, 4, 1, 90, '2014-12-12', '2015-12-12', 1, 2, 'Sale', 0, NULL, 'false'),
	(".vh_get_listing_id('Humayun\'s Tomb').", 'Humayun''s Tomb', 'publish', 3, '', 7, '{\"id\":\"172\",\"lat_pos\": \"28.5917507\",\"long_pos\": \"77.16072120000001\",\"marker_id\":\"172_3\",\"icon\":\"http://cohhe.com/demo/sky/sky-vacation/wp-content/uploads/2015/01/Hotels1.png\",\"group\":\"catgroup3\"}', '', '', '0', '/2015/01/172_unsplash_52a1c2d7d6f4f_1.jpg', 0, 1, 0, NULL, 'Never', '1421251106', '141.101.88.112', 0, 0, '[line-lexington],[pennsylvania],[united-states]', '0', ',3,', 'National Highway 8', 'New Delhi', 'Delhi', 'India', '110021', '28.5917507', '77.16072120000001', 'ROADMAP', NULL, '1', '', '', '', '', '', '', 'http://vimeo.com/106787023', '', NULL, 1, 1, 3, 170, '2014-12-12', '2015-12-12', 2, 1, 'Sale', 0, NULL, 'false');");

	// Clear locations table of its content
	$wpdb->query("TRUNCATE TABLE ".$post_locations_table);

	// Insert geodirectory post locations
	$wpdb->query("INSERT INTO " . $post_locations_table . " (`location_id`, `country`, `region`, `city`, `country_slug`, `region_slug`, `city_slug`, `city_latitude`, `city_longitude`, `is_default`, `city_meta`, `city_desc`) VALUES
	(1, 'United States', 'New York', 'Auburn', 'united-states', 'new-york', 'auburn', '42.917052', '-76.65585899999996', '1', '', ''),
	(2, 'United States', 'New York', 'New York', 'united-states', 'new-york', 'new-york', '40.7560681', '-73.99012249999998', '', '', ''),
	(3, 'Spain', 'Catalunya', 'Barcelona', 'spain', 'catalunya', 'barcelona', '41.3895964', '2.1854107999999997', '', '', ''),
	(4, 'Spain', 'Barcelona', 'Barcelona', 'spain', 'barcelona', 'barcelona', '41.4189914', '2.169573600000035', '', '', ''),
	(5, 'Italy', 'Lazio', 'Roma', 'italy', 'lazio', 'roma', '41.9271737', '12.477041800000052', '', '', ''),
	(6, 'France', 'Île-de-France', 'Paris', 'france', 'ile-de-france', 'paris', '48.8765302', '2.2911222999999836', '', '', ''),
	(7, 'India', 'Delhi', 'New Delhi', 'india', 'delhi', 'new-delhi', '28.646707674948644', '77.1616722863281', '', '', ''),
	(8, 'Italy', 'Lazio', 'Rome', 'italy', 'lazio', 'rome', '41.8808089', '12.48997989999998', '', '', ''),
	(9, 'France', 'Provence-Alpes-Côte-Azur', 'Aix-en-Provence', 'france', 'provence-alpes-cote-dazur', 'aix-en-provence', '43.52717511282925', '5.45056026675411', '', '', ''),
	(10, 'Malaysia', 'Selangor', 'Puchong', 'malaysia', 'selangor', 'puchong', '3.0162657', '101.60807499999999', '', '', ''),
	(11, 'Switzerland', 'Ticino', 'Bioggio', 'switzerland', 'ticino', 'bioggio', '46.01445630304938', '8.925233834472692', '', '', ''),
	(12, 'Portugal', 'Faro', 'Faro', 'portugal', 'faro', 'faro', '37.03104168136069', '-7.926784998687708', '', '', ''),
	(13, 'Germany', 'Berlin', 'Berlin', 'germany', 'berlin', 'berlin', '52.53456', '13.347489999999993', '', '', ''),
	(14, 'United States', 'New York', 'Suffern', 'united-states', 'new-york', 'suffern', '41.1411152', '-74.13574069999999', '', '', ''),
	(15, 'Ecuador', 'Islas Galápagos', 'Puerto Ayora', 'ecuador', 'islas-galapagos', 'puerto-ayora', '-0.7419677366764459', '-90.31376180000001', '', '', ''),
	(16, 'Croatia', 'lika-senj', 'novalja', 'croatia', 'lika-senj', 'novalja', '44.5235202', '14.978710999999976', '', '', ''),
	(17, 'Cote Ivoire', 'Lagunes', 'Abidjan', 'cote-divoire', 'lagunes', 'abidjan', '5.316667', '-4.0333329999999705', '', '', ''),
	(18, 'South Africa', 'kwa zulu natal', 'southbroom', 'south-africa', 'kwa-zulu-natal', 'southbroom', '-30.9454864', '30.299310699999978', '', '', ''),
	(19, 'United States', 'virginia', 'appomattox', 'united-states', 'virginia', 'appomattox', '42.917052', '-76.65585899999996', '', '', ''),
	(20, 'Philippines', 'Cebu', 'Mandaue', 'philippines', 'cebu', 'mandaue', '10.3457258', '123.9264564', '', '', '');");

	// Clear reviews table of its content
	$wpdb->query("TRUNCATE TABLE ".$post_review_table);

	// Insert geodirectory post reviews
	$wpdb->query("INSERT INTO " . $post_review_table . " (`post_id`, `post_title`, `post_type`, `user_id`, `comment_id`, `rating_ip`, `ratings`, `overall_rating`, `comment_images`, `wasthis_review`, `status`, `post_status`, `post_date`, `post_city`, `post_region`, `post_country`, `post_latitude`, `post_longitude`, `comment_content`) VALUES
	(".vh_get_listing_id('Loire Valley').", 'Loire Valley', 'gd_place', 0, 5, '141.101.89.130', NULL, 4, NULL, 0, '1', 1, '2015-01-07 05:24:01', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Loire Valley').", 'Loire Valley', 'gd_place', 0, 6, '141.101.89.130', NULL, 5, NULL, 0, '1', 1, '2015-01-07 05:26:56', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Arles').", 'Arles', 'gd_place', 0, 8, '141.101.89.130', NULL, 3, NULL, 0, '1', 1, '2015-01-07 05:30:07', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Arles').", 'Arles', 'gd_place', 0, 9, '141.101.89.130', NULL, 1, NULL, 0, '1', 1, '2015-01-07 05:30:24', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Biarritz').", 'Biarritz', 'gd_place', 0, 10, '141.101.89.130', NULL, 5, NULL, 0, '1', 1, '2015-01-07 05:30:50', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Biarritz').", 'Biarritz', 'gd_place', 0, 11, '141.101.89.130', NULL, 5, NULL, 0, '1', 1, '2015-01-07 05:31:08', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Strasbourg').", 'Strasbourg', 'gd_place', 0, 13, '141.101.89.130', NULL, 3, NULL, 0, '1', 1, '2015-01-07 05:31:53', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Strasbourg').", 'Strasbourg', 'gd_place', 0, 14, '141.101.89.130', NULL, 4, NULL, 0, '1', 1, '2015-01-07 05:32:14', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Lyon').", 'Lyon', 'gd_place', 0, 15, '141.101.89.130', NULL, 5, NULL, 0, '1', 1, '2015-01-07 05:32:36', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Lyon').", 'Lyon', 'gd_place', 0, 16, '141.101.89.130', NULL, 5, NULL, 0, '1', 1, '2015-01-07 05:32:54', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Lyon').", 'Lyon', 'gd_place', 0, 17, '141.101.89.130', NULL, 5, NULL, 0, '1', 1, '2015-01-07 05:33:11', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Marseille').", 'Marseille', 'gd_place', 0, 18, '141.101.89.130', NULL, 1, NULL, 0, '1', 1, '2015-01-07 05:33:32', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Marseille').", 'Marseille', 'gd_place', 0, 19, '141.101.89.130', NULL, 2, NULL, 0, '1', 1, '2015-01-07 05:33:49', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Marseille').", 'Marseille', 'gd_place', 0, 20, '141.101.89.130', NULL, 1, NULL, 0, '1', 1, '2015-01-07 05:34:11', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Rittenhouse Square').", 'Rittenhouse Square', 'gd_place', 0, 21, '141.101.89.130', NULL, 3, NULL, 0, '1', 1, '2015-01-07 05:37:16', 'Barcelona', 'Catalunya', 'Spain', NULL, NULL, NULL),
	(".vh_get_listing_id('Rittenhouse Square').", 'Rittenhouse Square', 'gd_place', 0, 23, '141.101.89.130', NULL, 4, NULL, 0, '1', 1, '2015-01-12 04:48:03', 'Barcelona', 'Catalunya', 'Spain', NULL, NULL, NULL),
	(".vh_get_listing_id('The Philadelphia Zoo').", 'The Philadelphia Zoo', 'gd_place', 0, 24, '141.101.89.130', NULL, 4, NULL, 0, '1', 1, '2015-01-12 04:49:18', 'Rome', 'Lazio', 'Italy', NULL, NULL, NULL),
	(".vh_get_listing_id('Philadelphia Marriott Downtown').", 'Philadelphia Marriott Downtown', 'gd_place', 28, 26, '141.101.89.124', NULL, 4, NULL, 0, '1', 1, '2015-01-13 19:54:03', 'New York', 'New York', 'United States', NULL, NULL, NULL),
	(".vh_get_listing_id('National Constitution Center').", 'National Constitution Center', 'gd_place', 0, 27, '141.101.89.130', NULL, 4, NULL, 0, '1', 1, '2015-01-14 18:41:59', 'Rome', 'Lazio', 'Italy', NULL, NULL, NULL),
	(".vh_get_listing_id('Rittenhouse Square').", 'Rittenhouse Square', 'gd_place', 0, 29, '173.245.62.116', NULL, 4, NULL, 0, '0', 1, '2015-01-31 03:29:35', 'Barcelona', 'Catalunya', 'Spain', NULL, NULL, NULL),
	(".vh_get_listing_id('Biarritz').", 'Biarritz', 'gd_place', 58, 30, '108.162.208.26', NULL, 5, NULL, 0, '0', 1, '2015-02-02 17:58:27', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Lyon').", 'Lyon', 'gd_place', 93, 31, '108.162.212.66', NULL, 4, NULL, 0, '0', 1, '2015-02-12 16:28:32', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL),
	(".vh_get_listing_id('Sadsbury Woods Preserve').", 'Sadsbury Woods Preserve', 'gd_place', 0, 32, '108.162.241.193', NULL, 4, NULL, 0, '0', 1, '2015-02-15 23:03:09', 'Barcelona', 'Catalunya', 'Spain', NULL, NULL, NULL),
	(".vh_get_listing_id('Franklin Square').", 'Franklin Square', 'gd_place', 104, 33, '141.101.99.40', NULL, 5, NULL, 0, '0', 1, '2015-02-17 03:48:54', 'Rome', 'Lazio', 'Italy', NULL, NULL, NULL),
	(".vh_get_listing_id('Marseille').", 'Marseille', 'gd_place', 0, 34, '108.162.242.139', NULL, 5, NULL, 0, '0', 1, '2015-02-28 01:18:27', 'Paris', 'Île-de-France', 'France', NULL, NULL, NULL);
	");

	// Fix listing IDs
	$current_listings = $wpdb->get_results('SELECT ID, post_title FROM '.$wpdb->prefix.'posts WHERE post_type="gd_place"');
	foreach ($current_listings as $key => $value) {
		$category = vh_get_categories();
		$wpdb->query('UPDATE '.$wpdb->prefix.'geodir_gd_place_detail SET post_id="'.$value->ID.'", default_category="'.$category->term_taxonomy_id.'", gd_placecategory=",'.$category->term_taxonomy_id.'," WHERE post_title="'.$value->post_title.'"');
		$wpdb->query('UPDATE '.$wpdb->prefix.'term_relationships SET term_taxonomy_id="'.$category->term_taxonomy_id.'" WHERE object_id="'.$value->ID.'"');
		update_post_meta($value->ID, 'post_categories', array('gd_placecategory' => $category->term_taxonomy_id.",y,d:"));
	}

	vh_fix_geodir_images();

	flush_rewrite_rules();
}

function vh_fix_geodir_images() {
	global $wpdb;
	$current_places = $wpdb->get_results('SELECT post_id, featured_image FROM '.$wpdb->prefix.'geodir_gd_place_detail');
	$image_query = "INSERT INTO " . $wpdb->prefix . "geodir_attachments (`post_id`, `user_id`, `title`, `caption`, `content`, `file`, `mime_type`, `menu_order`, `is_featured`, `is_approved`, `metadata`) VALUES ";
	foreach ($current_places as $place_key => $place_value) {
		$image_path = explode('/', $place_value->featured_image);
		$image_query .= "(".$place_value->post_id.", NULL, '".str_replace('.jpg', '', $image_path[count($image_path)-1])."', NULL, NULL, '".$place_value->featured_image."', 'image/jpeg', '1', '0', '1', NULL),";
	}
	$image_query = rtrim($image_query, ',').';';
	$wpdb->query($image_query);
}

function vh_get_categories() {
	$category = get_terms('gd_placecategory');
	$random_category = array_rand( get_terms('gd_placecategory') );
	return $category[$random_category];
}

function vh_get_listing_id( $title ) {
	return get_page_by_title($title, 'OBJECT', 'gd_place')->ID;
}

function vh_vcSetAsTheme() {
	vc_set_as_theme( true );
}
add_action( 'vc_before_init', 'vh_vcSetAsTheme' );

remove_action('geodir_author_page_title', 'geodir_action_author_page_title', 10);
remove_action('geodir_author_before_main_content', 'geodir_breadcrumb', 20);
remove_filter('comment_text', 'geodir_wrap_comment_text', 40, 2);

function vh_profile_editor_activation() {
	global $wpdb;
	$skype = $wpdb->get_results('SELECT NAME FROM ' . $wpdb->prefix . 'profile_editor_fields WHERE NAME = "skype"');
	$twitter = $wpdb->get_results('SELECT NAME FROM ' . $wpdb->prefix . 'profile_editor_fields WHERE NAME = "twitter"');
	$yahoo = $wpdb->get_results('SELECT NAME FROM ' . $wpdb->prefix . 'profile_editor_fields WHERE NAME = "yahoo"');
	$aim = $wpdb->get_results('SELECT NAME FROM ' . $wpdb->prefix . 'profile_editor_fields WHERE NAME = "aim"');

	if ( empty($skype) ) {
		$wpdb->query('INSERT INTO ' . $wpdb->prefix . 'profile_editor_fields (NAME,TYPE,LABEL,PLACEHOLDER,RULES,DESCRIPTION) VALUES ("skype","text","Skype","Skype name","{\"field_empty\":\"off\",\"field_syntax\":\"off\",\"field_min\":\"\",\"field_max\":\"\",\"field_registration\":\"on\"}","Your skype name")');
		$wpdb->query('UPDATE ' . $wpdb->prefix . 'usermeta SET meta_key="pe_skype" WHERE meta_key="skype"');
	}

	if ( empty($twitter) ) {
		$wpdb->query('INSERT INTO ' . $wpdb->prefix . 'profile_editor_fields (NAME,TYPE,LABEL,PLACEHOLDER,RULES,DESCRIPTION) VALUES ("twitter","text","Twitter","Twitter name","{\"field_empty\":\"off\",\"field_syntax\":\"off\",\"field_min\":\"\",\"field_max\":\"\",\"field_registration\":\"on\"}","Your twitter name")');
		$wpdb->query('UPDATE ' . $wpdb->prefix . 'usermeta SET meta_key="pe_twitter" WHERE meta_key="twitter"');
	}

	if ( empty($yahoo) ) {
		$wpdb->query('INSERT INTO ' . $wpdb->prefix . 'profile_editor_fields (NAME,TYPE,LABEL,PLACEHOLDER,RULES,DESCRIPTION) VALUES ("yahoo","text","Yahoo","Yahoo name","{\"field_empty\":\"off\",\"field_syntax\":\"off\",\"field_min\":\"\",\"field_max\":\"\",\"field_registration\":\"on\"}","Your yahoo name")');
		$wpdb->query('UPDATE ' . $wpdb->prefix . 'usermeta SET meta_key="pe_yahoo" WHERE meta_key="yahoo"');
	}

	if ( empty($aim) ) {
		$wpdb->query('INSERT INTO ' . $wpdb->prefix . 'profile_editor_fields (NAME,TYPE,LABEL,PLACEHOLDER,RULES,DESCRIPTION) VALUES ("aim","text","Aim","Aim name","{\"field_empty\":\"off\",\"field_syntax\":\"off\",\"field_min\":\"\",\"field_max\":\"\",\"field_registration\":\"on\"}","Your aim name")');
		$wpdb->query('UPDATE ' . $wpdb->prefix . 'usermeta SET meta_key="pe_aim" WHERE meta_key="aim"');
	}
}
register_activation_hook( WP_PLUGIN_DIR.'/profile-editor/profile-editor.php', 'vh_profile_editor_activation' );


function vh_metabox_setup() {
	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'vh_add_metabox' );

	/* Save post meta on the 'save_post' hook. */
	add_action( 'save_post', 'vh_save_metabox', 10, 2 );
}
add_action( 'load-post.php', 'vh_metabox_setup' );
add_action( 'load-post-new.php', 'vh_metabox_setup' );

function vh_save_metabox( $post_id, $post ) {
	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['vh_nonce'] ) || !wp_verify_nonce( $_POST['vh_nonce'], basename( __FILE__ ) ) )
	return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
	return $post_id;

	$meta_values = array(
		'vh_resource_id'
		);

	foreach ($meta_values as $a_meta_value) {
		/* Get the posted data and sanitize it for use as an HTML class. */
		$new_meta_value   = ( isset( $_POST[$a_meta_value] ) ? sanitize_text_field( $_POST[$a_meta_value] ) : '' );

		/* Get the meta key. */
		$meta_key   = $a_meta_value;

		/* Get the meta value of the custom field key. */
		$meta_value   = get_post_meta( $post_id, $meta_key, true );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
		update_post_meta( $post_id, $meta_key, $new_meta_value );

		/* If there is no new meta value but an old value exists, delete it. */
		elseif ( '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, $meta_key, $meta_value );
	}
}

function vh_add_metabox() {
	$gd_post_types = array();
	$gd_custom_post_types = get_geodir_custom_post_types();
	if ( !empty( $gd_custom_post_types ) ) {
		foreach ($gd_custom_post_types as $type_value) {
			$gd_post_types[] = $type_value;
		}
	}

	add_meta_box(
		'vh_reservation_metabox',                       // Unique ID
		esc_html__( 'Reservation settings', 'vh' ),     // Title
		'vh_reservation_metabox',                       // Callback function
		$gd_post_types,                                 // Admin page (or post type)
		'normal',                                       // Context
		'high'                                          // Priority
	);
}

function vh_reservation_metabox( $object, $box ) { ?>

	<?php wp_nonce_field( basename( __FILE__ ), 'vh_nonce' ); ?>

	<p>
		<label for="vh_resource_id"><?php _e( "Resource ID", 'vh' ); ?></label>
		<br />
		<input class="widefat" type="text" name="vh_resource_id" id="vh_resource_id" value="<?php echo esc_attr( get_post_meta( $object->ID, 'vh_resource_id', true ) ); ?>" size="30" />
	</p>

<?php }

function vh_filter_reservations( $content ) {
	if ( isset( $_GET['resource'] ) ) {
		$resource_id = $_GET['resource'];
		$content = preg_replace( "/resource=\"\d{0,100}\"/", "resource=\"" . $resource_id . "\"", $content);
	}
    return $content;
}
add_filter( 'the_content', 'vh_filter_reservations' );

add_role(
	'host',
	'Host' ,
	array(
		'read' => true,
        'edit_posts' => true,
        'delete_posts' => true,
        'publish_posts' => true,
        'upload_files' => true,
	)
);

add_role(
	'guest',
	'Guest' ,
	array(
		'read' => true, // true allows this capability
	)
);


class Easy_Social_Media extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		parent::__construct(
			'easy_social_media',
			__('Easy Social Media', 'social_media'),
			array( 'description' => __( 'An easy social media display. This widget requires FontAwesome.', 'social_media' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$sm_tt = $instance['title'];
		$sm_fb = $instance['fb'];
		$sm_gp = $instance['gp'];
		$sm_tw = $instance['tw'];
		$sm_li = $instance['li'];
		$sm_pt = $instance['pt'];
		$sm_yt = $instance['yt'];
		$sm_ig = $instance['ig'];

		$output = '<h4>' . $sm_tt . '</h4>';
		$output .= '
			<ul class="inline-list social">';
				if ($sm_fb != '' ) : $output .= '<li><a class="fb" href="'.$sm_fb.'" target="_blank"><i class="fa fa-facebook"></i></a></li>'; endif;
				if ($sm_gp != '' ) : $output .= '<li><a class="gp" href="'.$sm_gp.'" target="_blank"><i class="fa fa-google-plus"></i></a></li>'; endif;
				if ($sm_tw != '' ) : $output .= '<li><a class="tw" href="'.$sm_tw.'" target="_blank"><i class="fa fa-twitter"></i></a></li>'; endif;
				if ($sm_li != '' ) : $output .= '<li><a class="li" href="'.$sm_li.'" target="_blank"><i class="fa fa-linkedin"></i></a></li>'; endif;
				if ($sm_pt != '' ) : $output .= '<li><a class="pt" href="'.$sm_pt.'" target="_blank"><i class="fa fa-pinterest"></i></a></li>'; endif;
				if ($sm_yt != '' ) : $output .= '<li><a class="yt" href="'.$sm_yt.'" target="_blank"><i class="fa fa-youtube-play"></i></a></li>'; endif;
				if ($sm_ig != '' ) : $output .= '<li><a class="ig" href="'.$sm_yt.'" target="_blank"><i class="fa fa-instagram"></i></a></li>'; endif;
		$output .= '</ul>';

		echo $args['before_widget'];
		echo __( $output, 'social_media' );
		echo $args['after_widget'];
	}

	/**
	 * Ouputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$sm_tt = (isset( $instance[ 'title' ] ))? $instance[ 'title' ]:__( 'Social Media', 'social_media' );
		$sm_fb = (isset( $instance[ 'fb' ] ))? $instance[ 'fb' ]:__( 'https://www.facebook.com', 'social_media' );
		$sm_gp = (isset( $instance[ 'gp' ] ))? $instance[ 'gp' ]:__( 'https://plus.google.com/', 'social_media' );
		$sm_tw = (isset( $instance[ 'tw' ] ))? $instance[ 'tw' ]:__( 'https://twitter.com', 'social_media' );
		$sm_li = (isset( $instance[ 'li' ] ))? $instance[ 'li' ]:__( 'https://www.linkedin.com', 'social_media' );
		$sm_pt = (isset( $instance[ 'pt' ] ))? $instance[ 'pt' ]:__( 'https://www.pinterest.com/', 'social_media' );
		$sm_yt = (isset( $instance[ 'yt' ] ))? $instance[ 'yt' ]:__( 'http://www.youtube.com', 'social_media' );
		$sm_ig = (isset( $instance[ 'ig' ] ))? $instance[ 'ig' ]:__( 'https://www.instagram.com', 'social_media' );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wp_widget_plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $sm_tt ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'fb' ); ?>"><?php _e( 'Facebook:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'fb' ); ?>" name="<?php echo $this->get_field_name( 'fb' ); ?>" type="text" value="<?php echo esc_attr( $sm_fb ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'gp' ); ?>"><?php _e( 'Google+:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'gp' ); ?>" name="<?php echo $this->get_field_name( 'gp' ); ?>" type="text" value="<?php echo esc_attr( $sm_gp ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'tw' ); ?>"><?php _e( 'Twitter:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'tw' ); ?>" name="<?php echo $this->get_field_name( 'tw' ); ?>" type="text" value="<?php echo esc_attr( $sm_tw ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'li' ); ?>"><?php _e( 'LinkedIn:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'li' ); ?>" name="<?php echo $this->get_field_name( 'li' ); ?>" type="text" value="<?php echo esc_attr( $sm_li ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'pt' ); ?>"><?php _e( 'Pinterest:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'pt' ); ?>" name="<?php echo $this->get_field_name( 'pt' ); ?>" type="text" value="<?php echo esc_attr( $sm_pt ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'yt' ); ?>"><?php _e( 'YouTube:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'yt' ); ?>" name="<?php echo $this->get_field_name( 'yt' ); ?>" type="text" value="<?php echo esc_attr( $sm_yt ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'ig' ); ?>"><?php _e( 'Instagram:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'ig' ); ?>" name="<?php echo $this->get_field_name( 'ig' ); ?>" type="text" value="<?php echo esc_attr( $sm_ig ); ?>">
		</p>
	<?php
	}

	/**
	 * Processing widget options on save $
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags($new_instance['title']) : '';
		$instance['fb'] = ( ! empty( $new_instance['fb'] ) ) ? strip_tags( $new_instance['fb'] ) : '';
		$instance['gp'] = ( ! empty( $new_instance['gp'] ) ) ? strip_tags( $new_instance['gp'] ) : '';
		$instance['tw'] = ( ! empty( $new_instance['tw'] ) ) ? strip_tags( $new_instance['tw'] ) : '';
		$instance['li'] = ( ! empty( $new_instance['li'] ) ) ? strip_tags( $new_instance['li'] ) : '';
		$instance['pt'] = ( ! empty( $new_instance['pt'] ) ) ? strip_tags( $new_instance['pt'] ) : '';
		$instance['yt'] = ( ! empty( $new_instance['yt'] ) ) ? strip_tags( $new_instance['yt'] ) : '';
		$instance['ig'] = ( ! empty( $new_instance['ig'] ) ) ? strip_tags( $new_instance['ig'] ) : '';
		
		return $instance;
	}
}
add_action('widgets_init',create_function('', 'return register_widget("Easy_Social_Media");'));

function posts_for_current_author($query) {

	if($query->is_admin) {
		
		global $user_ID;
		if($user_ID != 1)
			$query->set('author',  $user_ID);
	}
	return $query;
}
add_filter('pre_get_posts', 'posts_for_current_author');

// Excerpt for pages
function my_add_excerpts_to_pages() {
     add_post_type_support( 'page', 'excerpt' );
}
add_action( 'init', 'my_add_excerpts_to_pages' );

function my_login_logo() { ?>
    <style type="text/css">
        html, body.login {
        	background: #333;
        }
		body.login div#login h1 a {
			background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/logo_v2_white.png);
            height: 70px;
		    width: 260px;
		    background-size: contain;
		}
		body.login div#login form#loginform {
			padding: 26px 26px 36px;
		}
		body.login div#login form#loginform p.forgetmenot {
			float: none;
			margin: 15px 0;
		}
		body.login div#login form#loginform p.submit input#wp-submit {
			width: 100%;
		    text-transform: uppercase;
		    font-size: 16px;
		    padding: 8px 16px;
		    height: auto;
		}
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo_url() {
  return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
  return get_bloginfo( 'name' );
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

