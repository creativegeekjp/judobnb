<?php

add_action('init', 'of_options');

if (!function_exists('of_options')) {

	function of_options() {
		global $of_options, $vh_fonts_default_options;
		$options = array();

		$themename = THEMENAME;

		$version_array = array("SkyEstate", "SkyVacation", "SkyDirectory");

		// Populate siteoptions option in array for use in theme
		$of_options               = get_option('of_options');
		$GLOBALS['template_path'] = VH_FUNCTIONS;

		$of_categories     = array();
		$of_categories_obj = get_categories('hide_empty=0');
		foreach ($of_categories_obj as $of_cat) {
			$of_categories[$of_cat->cat_ID] = $of_cat->cat_name;
		}
		$categories_tmp = array_unshift($of_categories, "Select a category:");

		// Access the WordPress Pages via an Array
		$of_pages = array();
		$of_pages_obj = get_pages('sort_column=post_parent,menu_order');
		foreach ($of_pages_obj as $of_page) {
			$of_pages[$of_page->ID] = $of_page->post_name;
		}
		$of_pages_tmp = array_unshift($of_pages, "Select the Blog page:");

		$of_categories = array();
		$of_categories_obj = get_categories('hide_empty=0');
		foreach ($of_categories_obj as $of_cat) {
			$of_categories[$of_cat->cat_ID] = $of_cat->cat_name;
		}
		$categories_tmp = array_unshift($of_categories, "Select a category:");

		// Footer Columns Array
		$footer_columns = array("1", "2", "3", "4");

		// Home map options
		$home_map_zoom = array('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19');
		$home_map_type = array('ROADMAP', 'SATELLITE', 'HYBRID', 'TERRAIN');

		// Paths for "type" => "images"
		$layout_style = VH_ADMIN_IMAGES . '/layout/';
		$framesurl    = VH_ADMIN_IMAGES . '/image-frames/';

		// Access the WordPress Categories via an Array
		$exclude_categories = array();
		$exclude_categories_obj = get_categories('hide_empty=0');
		foreach ($exclude_categories_obj as $exclude_cat) {
			$exclude_categories[$exclude_cat->cat_ID] = $exclude_cat->cat_name;
		}

		$footer_style = array('default', 'modern');

		$vh_fonts_default_options = array(
			'vh_primary_font_dark_font_face'   => 'Merriweather',
			'vh_primary_font_dark_weight'      => '300',
			'vh_primary_font_dark_font_size'   => 16,
			'vh_primary_font_dark_line_height' => 27,
			'vh_primary_font_dark_bg'          => '#fff',

			'vh_sidebar_font_dark_font_face'   => 'Merriweather',
			'vh_sidebar_font_dark_weight'      => '300',
			'vh_sidebar_font_dark_font_size'   => 16,
			'vh_sidebar_font_dark_line_height' => 27,
			'vh_sidebar_font_dark_bg'          => '#fff',

			'vh_headings_font_h1_font_face'   => 'Merriweather',
			'vh_headings_font_h1_weight'      => '300',
			'vh_headings_font_h1_font_size'   => 40,
			'vh_headings_font_h1_line_height' => 44,
			'vh_headings_font_h1_bg'          => '#fff',

			'vh_headings_font_h2_font_face'   => 'Merriweather',
			'vh_headings_font_h2_weight'      => '300',
			'vh_headings_font_h2_font_size'   => 32,
			'vh_headings_font_h2_line_height' => 40,
			'vh_headings_font_h2_bg'          => '#fff',

			'vh_headings_font_h3_font_face'   => 'Merriweather',
			'vh_headings_font_h3_weight'      => '300',
			'vh_headings_font_h3_font_size'   => 26,
			'vh_headings_font_h3_line_height' => 40,
			'vh_headings_font_h3_bg'          => '#fff',

			'vh_headings_font_h4_font_face'   => 'Merriweather',
			'vh_headings_font_h4_weight'      => '300',
			'vh_headings_font_h4_font_size'   => 24,
			'vh_headings_font_h4_line_height' => 40,
			'vh_headings_font_h4_bg'          => '#fff',

			'vh_headings_font_h5_font_face'   => 'Merriweather',
			'vh_headings_font_h5_weight'      => 'bold',
			'vh_headings_font_h5_font_size'   => 16,
			'vh_headings_font_h5_line_height' => 40,
			'vh_headings_font_h5_bg'          => '#fff',
			
			'vh_headings_font_h6_font_face'   => 'Merriweather',
			'vh_headings_font_h6_weight'      => 'bold',
			'vh_headings_font_h6_font_size'   => 12,
			'vh_headings_font_h6_line_height' => 40,
			'vh_headings_font_h6_bg'          => '#fff',
			
			'vh_links_font_font_face'   => 'Merriweather',
			'vh_links_font_weight'      => '300',
			'vh_links_font_font_size'   => 16,
			'vh_links_font_line_height' => 27,
			'vh_links_font_bg'          => '#fff',

			'vh_widget_font_face'   => 'Merriweather',
			'vh_widget_weight'      => '300',
			'vh_widget_font_size'   => 26,
			'vh_widget_line_height' => 40,
			'vh_widget_bg'          => '#fff',

			'vh_page_title_font_face'   => 'Merriweather',
			'vh_page_title_weight'      => '300',
			'vh_page_title_font_size'   => 40,
			'vh_page_title_line_height' => 44,
			'vh_page_title_bg'          => '#fff');

		// General options
		$options[] = array(
			"name"       => "General settings",
			"type"       => "heading",
			"menu_class" => "icon-cog-alt");

		$options[] = array(
			"name"  => "Website Logo",
			"desc"  => "Upload a custom logo for your Website.",
			"id"    => SHORTNAME . "_sitelogo",
			"order" => "",
			"type"  => "upload");

		$options[] = array(
			"name"  => "Resize larger logos",
			"desc"  => "If this checkbox is checked then larger logos are going to be resized to fit in the header.",
			"id"    => SHORTNAME . "_website_logo_resize",
			"order" => "false",
			"type"  => "checkbox");

		$options[] = array(
			"name"  => "Website logo is Retina ready",
			"desc"  => "You have to uplaod website logo which is 2x in dimensions. It will automatically scaled down for normal displays and prepared for High resolution displays.",
			"id"    => SHORTNAME . "_website_logo_retina",
			"order" => "false",
			"type"  => "checkbox");

		$options[] = array(
			"name"  => "Theme version",
			"desc"  => "Which theme version to use.",
			"id"    => SHORTNAME . "_theme_version",
			"order" => "",
			"type"    => "select",
			"options" => $version_array);

		$options[] = array(
			"name"  => "Book now",
			"desc"  => "Page ID to where the Book now button will redirect.",
			"id"    => SHORTNAME . "_book_now",
			"order" => "",
			"type"  => "text");

		$options[] = array(
			"name"  => "Currency symbol",
			"desc"  => "Symbol that's going to be next to prices.",
			"id"    => SHORTNAME . "_currency_symbol",
			"order" => "$",
			"type"  => "text");

		$options[] = array(
			"name"  => "Default Layout",
			"desc"  => "Select a layout style.<br />(full, left side sidebar, right side sidebar)",
			"id"    => SHORTNAME . "_layout_style",
			"order" => "full",
			"type"  => "font-icons",
 			"options" => array(
 				'left' => 'icon-th-list',
				'full'  => 'icon-menu',
				'right' => 'icon-th-list-right'));

		$options[] = array(
			"name"  => "Login Screen Logo",
			"desc"  => "Upload a custom logo.",
			"id"    => SHORTNAME . "_login_logo",
			"order" => "",
			"type"  => "upload");

		$options[] = array(
			"name"  => "Favicon",
			"desc"  => "Upload an image to use as your favicon",
			"id"    => SHORTNAME . "_favicon",
			"order" => "",
			"type"  => "upload");

		$options[] = array(
			"name"  => "Tracking Code or any other JavaScript",
			"desc"  => "Google Analytics tracking code or any other JavaScript",
			"id"    => SHORTNAME . "_tracking_code",
			"order" => "",
			"type"  => "textarea");

		// Close this group
		$options[] = array(
			"name" => "",
			"type" => "group_close");

		// Header options
		$options[] = array(
			"name"       => "Header settings",
			"type"       => "heading",
			"menu_class" => "icon-sitemap");

		$options[] = array(
			"name"  => "Header background",
			"desc"  => "Upload an image to use as your header background",
			"id"    => SHORTNAME . "_header_bg",
			"order" => "",
			"type"  => "upload");

		$options[] = array(
			"name"  => "Header video ID",
			"desc"  => "ID of the video to show instead of image.",
			"id"    => SHORTNAME . "_header_video",
			"order" => "",
			"type"  => "text");

		$options[] = array(
			"name"  => "Show \"Add property\" button at header",
			"desc"  => "Choose if you want to show this button at header.",
			"id"    => SHORTNAME . "_header_add_property",
			"order" => "true",
			"type"  => "checkbox");

		$options[] = array(
			"name"  => "Show login button at header",
			"desc"  => "Choose if you want to show login button at header.",
			"id"    => SHORTNAME . "_header_login",
			"order" => "true",
			"type"  => "checkbox");

		// Close this group
		$options[] = array(
			"name" => "",
			"type" => "group_close");

		// Breadcrumb settings
		$options[] = array(
			"name"       => "Breadcrumb settings",
			"type"       => "heading",
			"menu_class" => "icon-menu");

		$options[] = array(
			"name"  => "Disable Breadcrumbs",
			"desc"  => "Breadcrumbs are shown by default",
			"id"    => SHORTNAME . "_breadcrumb",
			"order" => "false",
			"type"  => "checkbox");

		$options[] = array(
			"name"  => "Breadcrumb Delimiter",
			"desc"  => "This is the symbol that will appear in between your breadcrumbs",
			"id"    => SHORTNAME . "_breadcrumb_delimiter",
			"order" => "",
			"type"  => "text");

		// Close this group
		$options[] = array(
			"name" => "",
			"type" => "group_close");

		// Twitter widget options
		$options[] = array(
			"name"       => "Twitter widget settings",
			"type"       => "heading",
			"menu_class" => "icon-twitter");

		$options[] = array(
			"name"  => "Consumer key",
			"desc"  => "Please enter your Twitter API consumer key",
			"id"    => SHORTNAME . "_twitter_consumer_key",
			"order" => "",
			"type"  => "text");

		$options[] = array(
			"name"  => "Consumer secret",
			"desc"  => "Please enter your Twitter API consumer secret",
			"id"    => SHORTNAME . "_twitter_consumer_secret",
			"order" => "",
			"type"  => "text");

		$options[] = array(
			"name"  => "User token",
			"desc"  => "Please enter your Twitter API User token",
			"id"    => SHORTNAME . "_twitter_user_token",
			"order" => "",
			"type"  => "text");

		$options[] = array(
			"name"  => "User secret",
			"desc"  => "Please enter your Twitter API User secret",
			"id"    => SHORTNAME . "_twitter_user_secret",
			"order" => "",
			"type"  => "text");

		// Close this group
		$options[] = array(
			"name" => "",
			"type" => "group_close");

		// CSS options
		$options[] = array(
			"name"       => "CSS settings",
			"type"       => "heading",
			"menu_class" => "icon-css");

		$options[] = array(
			"name"  => "Custom CSS",
			"desc"  => "Custom CSS to your website",
			"id"    => SHORTNAME . "_custom_css",
			"order" => "",
			"type"  => "textarea");

		// Close this group
		$options[] = array(
			"name" => "",
			"type" => "group_close");

		// Javascript options
		$options[] = array(
			"name"       => "JavaScript settings",
			"type"       => "heading",
			"menu_class" => "icon-code-1");

		$options[] = array(
			"name"  => "Google Maps API Key (v3)",
			"desc"  => "Enter your Google Maps API Key (v3)",
			"id"    => SHORTNAME . "_google_maps_api_key",
			"order" => "",
			"type"  => "text");

		// Close this group
		$options[] = array(
			"name" => "",
			"type" => "group_close");

		// Typography options
		$options[] = array(
			"name"       => "Typography",
			"type"       => "heading",
			"menu_class" => "icon-font");

		$options[] = array(
			"name"  => "Load cyrillic font subset",
			"desc"  => "Check if you want to load this font subset.",
			"id"    => SHORTNAME . "_subset_cyrillic",
			"order" => "false",
			"type"  => "checkbox");

		$options[] = array(
			"name"  => "Load greek font subset",
			"desc"  => "Check if you want to load this font subset.",
			"id"    => SHORTNAME . "_subset_greek",
			"order" => "false",
			"type"  => "checkbox");

		$options[] = array(
			"name"  => "Load latin font subset",
			"desc"  => "Check if you want to load this font subset.",
			"id"    => SHORTNAME . "_subset_latin",
			"order" => "true",
			"type"  => "checkbox");

		$options[] = array(
			"name"  => "Load latin-extended font subset",
			"desc"  => "Check if you want to load this font subset.",
			"id"    => SHORTNAME . "_subset_latin_ext",
			"order" => "false",
			"type"  => "checkbox");

		$options[] = array(
			"name"   => "Primary font",
			"desc"   => "Primary font dark style",
			"id"     => SHORTNAME . "_primary_font_dark",
			"order"  => "",
			"type"   => "font",
			"min_px" => "8",
			"max_px" => "25",
			"min_ln" => "8",
			"max_ln" => "50",
			"color"  => "333333");

		$options[] = array(
			"name"   => "Sidebar font",
			"desc"   => "Sidebar font dark style",
			"id"     => SHORTNAME . "_sidebar_font_dark",
			"order"  => "",
			"type"   => "font",
			"min_px" => "8",
			"max_px" => "25",
			"min_ln" => "8",
			"max_ln" => "50",
			"color"  => "333333");

		$options[] = array(
			"name"   => "Headings font (H1)",
			"desc"   => "Headings font style",
			"id"     => SHORTNAME . "_headings_font_h1",
			"order"  => "",
			"type"   => "font",
			"min_px" => "8",
			"max_px" => "70",
			"min_ln" => "8",
			"max_ln" => "80",
			"color"  => "339933");

		$options[] = array(
			"name"   => "Headings font (H2)",
			"desc"   => "Headings font style",
			"id"     => SHORTNAME . "_headings_font_h2",
			"order"  => "",
			"type"   => "font",
			"min_px" => "8",
			"max_px" => "50",
			"min_ln" => "8",
			"max_ln" => "70",
			"color"  => "339933");

		$options[] = array(
			"name"   => "Headings font (H3)",
			"desc"   => "Headings font style",
			"id"     => SHORTNAME . "_headings_font_h3",
			"order"  => "",
			"type"   => "font",
			"min_px" => "8",
			"max_px" => "35",
			"min_ln" => "8",
			"max_ln" => "50",
			"color"  => "0099cc");

		$options[] = array(
			"name"   => "Headings font (H4)",
			"desc"   => "Headings font style",
			"id"     => SHORTNAME . "_headings_font_h4",
			"order"  => "",
			"type"   => "font",
			"min_px" => "8",
			"max_px" => "35",
			"min_ln" => "8",
			"max_ln" => "50",
			"color"  => "000000");

		$options[] = array(
			"name"   => "Headings font (H5)",
			"desc"   => "Headings font style",
			"id"     => SHORTNAME . "_headings_font_h5",
			"order"  => "",
			"type"   => "font",
			"min_px" => "8",
			"max_px" => "35",
			"min_ln" => "8",
			"max_ln" => "50",
			"color"  => "000000");

		$options[] = array(
			"name"   => "Headings font (H6)",
			"desc"   => "Headings font style",
			"id"     => SHORTNAME . "_headings_font_h6",
			"order"  => "",
			"type"   => "font",
			"min_px" => "8",
			"max_px" => "35",
			"min_ln" => "8",
			"max_ln" => "50",
			"color"  => "000000");

		$options[] = array(
			"name"   => "Normal links font",
			"desc"   => "Normal links font style",
			"id"     => SHORTNAME . "_links_font",
			"order"  => "",
			"type"   => "font",
			"min_px" => "8",
			"max_px" => "35",
			"min_ln" => "8",
			"max_ln" => "50",
			"color"  => "0099cc");

		$options[] = array(
			"name"   => "Widget title font",
			"desc"   => "Widget title font style",
			"id"     => SHORTNAME . "_widget",
			"order"  => "",
			"type"   => "font",
			"min_px" => "8",
			"max_px" => "35",
			"min_ln" => "8",
			"max_ln" => "50",
			"color"  => "339933");

		$options[] = array(
			"name"   => "Page title font",
			"desc"   => "Page title font style",
			"id"     => SHORTNAME . "_page_title",
			"order"  => "",
			"type"   => "font",
			"min_px" => "8",
			"max_px" => "85",
			"min_ln" => "8",
			"max_ln" => "125",
			"color"  => "339933");

		// Close this group
		$options[] = array(
			"name" => "",
			"type" => "group_close");

		// Footer options
		$options[] = array(
			"name"       => "Footer settings",
			"type"       => "heading",
			"menu_class" => "icon-sitemap");

		$options[] = array(
			"name"  => "Footer Logo",
			"desc"  => "Upload a custom logo for your footer.",
			"id"    => SHORTNAME . "_site_footer_logo",
			"order" => "",
			"type"  => "upload");

		$options[] = array(
			"name"  => "Footer logo is Retina ready",
			"desc"  => "You have to uplaod footer logo which is 2x in dimensions. It will automatically scaled down for normal displays and prepared for High resolution displays.",
			"id"    => SHORTNAME . "_site_footer_retina",
			"order" => "false",
			"type"  => "checkbox");

		$options[] = array(
			"name"    => "Footer Columns",
			"desc"    => "Enter number of columns you would like to display in the footer",
			"id"      => SHORTNAME . "_footer_columns",
			"order"   => "4",
			"type"    => "select",
			"options" => $footer_columns,
			"type"  => "select");

		$options[] = array(
			"name"  => "Footer copyright",
			"desc"  => "Add copyright text which you would like to display in the footer",
			"id"    => SHORTNAME . "_footer_copyright",
			"order" => "&copy; [year], Sky by <a href='http://cohhe.com' target='_blank'>Cohhe</a>",
			"type"  => "text");

		$options[] = array(
			"name"  => "Scroll to top",
			"desc"  => "Show scroll to top image?",
			"id"    => SHORTNAME . "_scroll_to_top",
			"order" => "",
			"type"  => "checkbox");

		// Close this group
		$options[] = array(
			"name" => "",
			"type" => "group_close");

		// 404 page settings
		$options[] = array(
			"name"       => "404 page",
			"type"       => "heading",
			"menu_class" => "icon-attention-1");

		$options[] = array(
			"name"   => "404 Page Title",
			"desc"   => "Set the page title that is displayed on the 404 Error Page",
			"id"     => SHORTNAME . "_404_title",
			"order"  => "This is somewhat embarrassing, isn't it?",
			"type"   => "text",
			"slider" => "no");

		$options[] = array(
			"name"  => "404 Message",
			"desc"  => "Set the message that is displayed on the 404 Error Page",
			"id"    => SHORTNAME . "_404_message",
			"order" => "It seems we can't find what you're looking for. Perhaps searching, or one of the links below, can help.",
			"type"  => "textarea");

		// Close this group
		$options[] = array(
			"name" => "",
			"type" => "group_close");

		// GeoLocate page settings
		$options[] = array(
			"name"       => "Geolocation",
			"type"       => "heading",
			"menu_class" => "icon-location");

		$options[] = array(
			"name"  => "Is geolocation on?",
			"desc"  => "If checkbox is checked, users are prompted to share their location.",
			"id"    => SHORTNAME . "_geolocation_state",
			"order" => "true",
			"type"  => "checkbox");

		$options[] = array(
			"name"   => "Default country",
			"desc"   => "Set default country to show at your homepage.",
			"id"     => SHORTNAME . "_default_country",
			"order"  => "France",
			"type"   => "text",
			"slider" => "no");

		$options[] = array(
			"name"   => "Default city",
			"desc"   => "Set default city to show at your homepage.",
			"id"     => SHORTNAME . "_default_city",
			"order"  => "Paris",
			"type"   => "text",
			"slider" => "no");

		// Close this group
		$options[] = array(
			"name" => "",
			"type" => "group_close");

		// Home map page settings
		$options[] = array(
			"name"       => "Home map",
			"type"       => "heading",
			"menu_class" => "icon-picture");

		$options[] = array(
			"name"  => "Use map as header search background",
			"desc"  => "Images are replaced with a map, full of active listings.",
			"id"    => SHORTNAME . "_header_search_map",
			"order" => "false",
			"type"  => "checkbox");

		$options[] = array(
			"name"    => "Map type",
			"desc"    => "Select you home map type",
			"id"      => SHORTNAME . "_home_map_type",
			"order"   => "ROADMAP",
			"options" => $home_map_type,
			"type"  => "select");

		$options[] = array(
			"name"    => "Map zoom level",
			"desc"    => "Select you home map zoom level",
			"id"      => SHORTNAME . "_home_map_zoom",
			"order"   => "12",
			"options" => $home_map_zoom,
			"type"  => "select");

		$options[] = array(
			"name"  => "Auto zoom map?",
			"desc"  => "Overrides zoom level and auto fits map around city bounds.",
			"id"    => SHORTNAME . "_home_map_autozoom",
			"order" => "false",
			"type"  => "checkbox");

		$options[] = array(
			"name"  => "Enable mouse scroll zoom?",
			"desc"  => "Enables scrolling with mouse wheel.",
			"id"    => SHORTNAME . "_home_map_scrolling",
			"order" => "false",
			"type"  => "checkbox");

		// Close this group
		$options[] = array(
			"name" => "",
			"type" => "group_close");

		// Home map page settings
		$options[] = array(
			"name"       => "Map markers",
			"type"       => "heading",
			"menu_class" => "icon-location");

		$options[] = array(
			"name"  => "Use default markers",
			"desc"  => "Use unstyled, default markers for maps.",
			"id"    => SHORTNAME . "_map_markers",
			"order" => "false",
			"type"  => "checkbox");

		update_option('of_template', $options);
		update_option('of_themename', $themename);
		update_option('of_shortname', SHORTNAME);
	}
}