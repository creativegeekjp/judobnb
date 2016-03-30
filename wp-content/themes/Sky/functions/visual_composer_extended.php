<?php
/*
* Extended Visual Composer plugin
*/

// Remove sidebar element
vc_remove_element("vc_widget_sidebar");
vc_remove_element("vc_images_carousel");
vc_remove_element("vc_toggle");
vc_remove_element("vc_tour");
vc_remove_element("vc_carousel");
vc_remove_element("vc_cta_button");

// Remove default WordPress widgets
vc_remove_element("vc_wp_search");
vc_remove_element("vc_wp_meta");
vc_remove_element("vc_wp_recentcomments");
vc_remove_element("vc_wp_calendar");
vc_remove_element("vc_wp_pages");
vc_remove_element("vc_wp_tagcloud");
vc_remove_element("vc_wp_custommenu");
vc_remove_element("vc_wp_text");
vc_remove_element("vc_wp_posts");
vc_remove_element("vc_wp_links");
vc_remove_element("vc_wp_categories");
vc_remove_element("vc_wp_archives");
vc_remove_element("vc_wp_rss");

vc_add_param("vc_row", array(
	"type"        => "dropdown",
	"class"       => "",
	"heading"     => __("Row style", "vh"),
	"admin_label" => true,
	"param_name"  => "type",
	"value"       => array(
		__( "Default", "vh" )           => "0",
		__( "Full Width - White", "vh" ) => "1"
	),
	"description" => ""
));

// Gap
vc_map( array(
	"name"     => __( "Gap", "vh" ),
	"base"     => "vh_gap",
	"icon"     => "icon-wpb-ui-gap-content",
	"class"    => "vh_vc_sc_gap",
	"category" => __( "by Sky", "vh" ),
	"params"   => array(
		array(
			"type"        => "textfield",
			"class"       => "",
			"heading"     => __( "Gap height", "vh" ),
			"admin_label" => true,
			"param_name"  => "height",
			"value"       => "10",
			"description" => __( "In pixels", "vh" )
		)
	)
) );

$colors_arr = array(
	__("Red", "vh")    => "red",
	__("Blue", "vh")   => "blue",
	__("Yellow", "vh") => "yellow",
	__("Green", "vh")  => "green"
);

// Pricing table
vc_map( array(
		"name"      => __( "Pricing Table", "vh" ),
		"base"      => "vh_pricing_table",
		"class"     => "vh-pricing-tables-class",
		"icon"      => "icon-wpb-ui-pricing_table-content",
		"category"  => __( "by Sky", "vh" ),
		"params"    => array(
			array(
				"type"        => "dropdown",
				"heading"     => __("Color", "vh"),
				"param_name"  => "pricing_block_color",
				"value"       => $colors_arr,
				"description" => __("Pricing block color.", "vh")
			),
			array(
				"type"        => "textfield",
				"heading"     => __( "Title", "vh" ),
				"param_name"  => "pricing_title",
				"value"       => "",
				"description" => __( "Please add offer title.", "vh" )
			),
			array(
				"type"        => "textarea",
				"heading"     => __( "Description text 1", "vh" ),
				"param_name"  => "content1",
				"value"       => "",
				"description" => __( "Please add first part of your offer text.", "vh" )
			),
			array(
				"type"        => "textarea_html",
				"heading"     => __( "Description text 2", "vh" ),
				"param_name"  => "content2",
				"value"       => "",
				"description" => __( "Please add second part of your offer text.", "vh" )
			),
			array(
				"type"        => "textfield",
				"heading"     => __( "Price", "vh" ),
				"param_name"  => "price",
				"value"       => "",
				"description" => __( "Please add offer price.", "vh" )
			),
			array(
				"type"        => "vc_link",
				"heading"     => __( "", "vh" ),
				"param_name"  => "button_link",
				"value"       => "",
				"description" => __( "Please add offer button link.", "vh" )
			),
			array(
				"type"        => "textfield",
				"heading"     => __( "Extra class name", "vh" ),
				"param_name"  => "el_class",
				"value"       => "",
				"description" => __( "If you wish to style particular content element differently, then use this field to add a class name.", "vh" )
			)
		)
	)
);

// Update Buttons map
$colors_arr = array(__("Transparent", "vh") => "btn-transparent", __("Blue", "vh") => "btn-primary", __("Light Blue", "vh") => "btn-info", __("Green", "vh") => "btn-success", __("Yellow", "vh") => "btn-warning", __("Red", "vh") => "btn-danger", __("Inverse", "vh") => "btn-inverse");

$icons_arr = array(
	__("None", "vh")                     => "none",
	__("Address book icon", "vh")        => "wpb_address_book",
	__("Alarm clock icon", "vh")         => "wpb_alarm_clock",
	__("Anchor icon", "vh")              => "wpb_anchor",
	__("Application Image icon", "vh")   => "wpb_application_image",
	__("Arrow icon", "vh")               => "wpb_arrow",
	__("Asterisk icon", "vh")            => "wpb_asterisk",
	__("Hammer icon", "vh")              => "wpb_hammer",
	__("Balloon icon", "vh")             => "wpb_balloon",
	__("Balloon Buzz icon", "vh")        => "wpb_balloon_buzz",
	__("Balloon Facebook icon", "vh")    => "wpb_balloon_facebook",
	__("Balloon Twitter icon", "vh")     => "wpb_balloon_twitter",
	__("Battery icon", "vh")             => "wpb_battery",
	__("Binocular icon", "vh")           => "wpb_binocular",
	__("Document Excel icon", "vh")      => "wpb_document_excel",
	__("Document Image icon", "vh")      => "wpb_document_image",
	__("Document Music icon", "vh")      => "wpb_document_music",
	__("Document Office icon", "vh")     => "wpb_document_office",
	__("Document PDF icon", "vh")        => "wpb_document_pdf",
	__("Document Powerpoint icon", "vh") => "wpb_document_powerpoint",
	__("Document Word icon", "vh")       => "wpb_document_word",
	__("Bookmark icon", "vh")            => "wpb_bookmark",
	__("Camcorder icon", "vh")           => "wpb_camcorder",
	__("Camera icon", "vh")              => "wpb_camera",
	__("Chart icon", "vh")               => "wpb_chart",
	__("Chart pie icon", "vh")           => "wpb_chart_pie",
	__("Clock icon", "vh")               => "wpb_clock",
	__("Fire icon", "vh")                => "wpb_fire",
	__("Heart icon", "vh")               => "wpb_heart",
	__("Mail icon", "vh")                => "wpb_mail",
	__("Play icon", "vh")                => "wpb_play",
	__("Shield icon", "vh")              => "wpb_shield",
	__("Video icon", "vh")               => "wpb_video"
);

$target_arr = array(__("Same window", "vh") => "_self", __("New window", "vh") => "_blank");
$size_arr = array(__("Regular size", "vh") => "wpb_regularsize", __("Large", "vh") => "btn-large", __("Small", "vh") => "btn-small", __("Mini", "vh") => "btn-mini");

vc_map( array(
  "name" => __("Button", "vh"),
  "base" => "vc_button",
  "icon" => "icon-wpb-ui-button",
  "category" => __('Content', 'vh'),
  "params" => array(
	array(
	  "type" => "textfield",
	  "heading" => __("Text on the button", "vh"),
	  "holder" => "button",
	  "class" => "wpb_button",
	  "param_name" => "title",
	  "value" => __("Text on the button", "vh"),
	  "description" => __("Text on the button.", "vh")
	),
	array(
	  "type" => "textfield",
	  "heading" => __("URL (Link)", "vh"),
	  "param_name" => "href",
	  "description" => __("Button link.", "vh")
	),
	array(
	  "type" => "dropdown",
	  "heading" => __("Target", "vh"),
	  "param_name" => "target",
	  "value" => $target_arr,
	  "dependency" => Array('element' => "href", 'not_empty' => true)
	),
	array(
	  "type" => "dropdown",
	  "heading" => __("Color", "vh"),
	  "param_name" => "color",
	  "value" => $colors_arr,
	  "description" => __("Button color.", "vh")
	),
	array(
	  "type" => "dropdown",
	  "heading" => __("Icon", "vh"),
	  "param_name" => "icon",
	  "value" => $icons_arr,
	  "description" => __("Button icon.", "vh")
	),
	array(
	  "type" => "dropdown",
	  "heading" => __("Size", "vh"),
	  "param_name" => "size",
	  "value" => $size_arr,
	  "description" => __("Button size.", "vh")
	),
	array(
	  "type" => "textfield",
	  "heading" => __("Extra class name", "vh"),
	  "param_name" => "el_class",
	  "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "vh")
	)
  ),
  "js_view" => 'VcButtonView'
) );

if ( function_exists('geodir_cp_create_default_fields') ) {
	$post_types = array(
		"type"        => "textfield",
		"heading"     => __( "Post type", "vh" ),
		"param_name"  => "listing_post_type",
		"value"       => "",
		"description" => __( "From which post type to query the posts? Default gd_place", "vh" )
	);
	$post_taxonomy = array(
		"type"        => "textfield",
		"heading"     => __( "Post taxonomy", "vh" ),
		"param_name"  => "listing_post_taxonomy",
		"value"       => "",
		"description" => __( "From which custom post taxonomy to query the posts? Default gd_placecategory", "vh" )
	);
} else {
	$post_types = $post_taxonomy = array();
}

// Featured properties
vc_map( array(
	"name"      => __( "Featured properties", "vh" ),
	"base"      => "vh_featured_properties",
	"class"     => "",
	"icon"      => "icon-wpb-ui-pricing_table-content",
	"category"  => __( "by Sky", "vh" ),
	"params"    => array(
		array(
			"type"        => "textfield",
			"heading"     => __("Primary title", "vh"),
			"param_name"  => "listing_ptitle",
			"value"       => '',
			"description" => __("Primary title for this module.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Secondary title", "vh"),
			"param_name"  => "listing_stitle",
			"value"       => '',
			"description" => __("Secondary title for this module.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __( "Tags", "vh" ),
			"param_name"  => "listing_tags",
			"value"       => "",
			"description" => __( "Module will display listings with these tags.", "vh" )
		),
		array(
			"type"        => "textfield",
			"heading"     => __( "Limit", "vh" ),
			"param_name"  => "listing_limit",
			"value"       => "",
			"description" => __( "How much listings to display.", "vh" )
		),
		$post_types
	)
));


// Popular destinations
vc_map( array(
	"name"      => __( "Popular destinations", "vh" ),
	"base"      => "vh_popular_destinations",
	"class"     => "",
	"icon"      => "icon-wpb-ui-pricing_table-content",
	"category"  => __( "by Sky", "vh" ),
	"params"    => array(
		array(
			"type"        => "textfield",
			"heading"     => __("Primary title", "vh"),
			"param_name"  => "popular_dest_ptitle",
			"value"       => '',
			"description" => __("Primary title for this module.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Secondary title", "vh"),
			"param_name"  => "popular_dest_stitle",
			"value"       => '',
			"description" => __("Secondary title for this module.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __( "Tags", "vh" ),
			"param_name"  => "popular_dest_tags",
			"value"       => "",
			"description" => __( "Module will display listings with these tags.", "vh" )
		),
		array(
			"type"        => "textfield",
			"heading"     => __( "Countries", "vh" ),
			"param_name"  => "popular_dest_countries",
			"value"       => "",
			"description" => __( "Module will display these country pictures. Example - Italy:Rome,Spain:Barcelona,France:Paris", "vh" )
		),
		array(
			"type"        => "textfield",
			"heading"     => __( "Limit", "vh" ),
			"param_name"  => "popular_dest_limit",
			"value"       => "",
			"description" => __( "How much listings to display.", "vh" )
		),
		$post_types
	)
));

// Recent activities
vc_map( array(
	"name"      => __( "Recent activities", "vh" ),
	"base"      => "vh_recent_activities",
	"class"     => "",
	"icon"      => "icon-wpb-ui-pricing_table-content",
	"category"  => __( "by Sky", "vh" ),
	"params"    => array(
		array(
			"type"        => "textfield",
			"heading"     => __("Primary title", "vh"),
			"param_name"  => "recent_activities_ptitle",
			"value"       => '',
			"description" => __("Primary title for this module.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Secondary title", "vh"),
			"param_name"  => "recent_activities_stitle",
			"value"       => '',
			"description" => __("Secondary title for this module.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Limit", "vh"),
			"param_name"  => "recent_activities_limit",
			"value"       => '',
			"description" => __("Limit of the entries that are going to be shown.", "vh")
		),
		$post_types
	)
));

// Blog carousel
vc_map( array(
	"name"      => __( "Blog carousel", "vh" ),
	"base"      => "vh_blog_carousel",
	"class"     => "",
	"icon"      => "icon-wpb-ui-pricing_table-content",
	"category"  => __( "by Sky", "vh" ),
	"params"    => array(
		array(
			"type"        => "textfield",
			"heading"     => __("Primary title", "vh"),
			"param_name"  => "blog_carousel_ptitle",
			"value"       => '',
			"description" => __("Primary title for this module.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Secondary title", "vh"),
			"param_name"  => "blog_carousel_stitle",
			"value"       => '',
			"description" => __("Secondary title for this module.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Categories", "vh"),
			"param_name"  => "blog_carousel_categories",
			"value"       => '',
			"description" => __("From which category to display entries.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Limit", "vh"),
			"param_name"  => "blog_carousel_limit",
			"value"       => '',
			"description" => __("Limit of the entries that are going to be shown.", "vh")
		)
	)
));

// Blog posts
vc_map( array(
	"name"      => __( "Blog posts", "vh" ),
	"base"      => "vh_blog_posts",
	"class"     => "",
	"icon"      => "icon-wpb-ui-pricing_table-content",
	"category"  => __( "by Sky", "vh" ),
	"params"    => array(
		array(
			"type"        => "textfield",
			"heading"     => __("Title", "vh"),
			"param_name"  => "blog_posts_title",
			"value"       => '',
			"description" => __("Title for this module.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Categories", "vh"),
			"param_name"  => "blog_posts_categories",
			"value"       => '',
			"description" => __("From which category to display entries.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Limit", "vh"),
			"param_name"  => "blog_posts_limit",
			"value"       => '',
			"description" => __("Limit of the entries that are going to be shown.", "vh")
		)
	)
));

// Social links
vc_map( array(
	"name"      => __( "Social links", "vh" ),
	"base"      => "vh_social_links",
	"class"     => "",
	"icon"      => "icon-wpb-ui-pricing_table-content",
	"category"  => __( "by Sky", "vh" ),
	"params"    => array(
		array(
			"type"        => "textfield",
			"heading"     => __("Facebook text", "vh"),
			"param_name"  => "social_links_fb_text",
			"value"       => '',
			"description" => __("Your facebook link text.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Facebook link", "vh"),
			"param_name"  => "social_links_fb",
			"value"       => '',
			"description" => __("Your facebook link.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Twitter text", "vh"),
			"param_name"  => "social_links_tw_text",
			"value"       => '',
			"description" => __("Your twitter link text.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Twitter", "vh"),
			"param_name"  => "social_links_tw",
			"value"       => '',
			"description" => __("Your twitter link.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Youtube text", "vh"),
			"param_name"  => "social_links_yt_text",
			"value"       => '',
			"description" => __("Your youtube link text.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Youtube", "vh"),
			"param_name"  => "social_links_yt",
			"value"       => '',
			"description" => __("Your youtube link.", "vh")
		)
	)
));

// Categories module
vc_map( array(
	"name"      => __( "Listing categories", "vh" ),
	"base"      => "vh_listing_categories",
	"class"     => "",
	"icon"      => "icon-wpb-ui-pricing_table-content",
	"category"  => __( "by Sky", "vh" ),
	"params"    => array(
		array(
			"type"        => "textfield",
			"heading"     => __("Primary title", "vh"),
			"param_name"  => "categories_main_title",
			"value"       => '',
			"description" => __("Primary module title.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Secondary title", "vh"),
			"param_name"  => "categories_secondary_title",
			"value"       => '',
			"description" => __("Secondary module title.", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Categories", "vh"),
			"param_name"  => "categories_selected_cat",
			"value"       => '',
			"description" => __("Category parents of which you want to display your categories.", "vh")
		),
		$post_taxonomy,
		$post_types
	)
));

// gd_homepage_map module
vc_map( array(
	"name"      => __( "GD Homepage map", "vh" ),
	"base"      => "gd_homepage_map",
	"class"     => "",
	"icon"      => "icon-wpb-ui-pricing_table-content",
	"category"  => __( "by GeoDirectory", "vh" ),
	"params"    => array(
		array(
			"type"        => "textfield",
			"heading"     => __("Width", "vh"),
			"param_name"  => "width",
			"value"       => '',
			"description" => __("A number of pixels or percent (default = 960px)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Height", "vh"),
			"param_name"  => "height",
			"value"       => '',
			"description" => __("A number of pixels or percent (default = 425px)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Maptype", "vh"),
			"param_name"  => "maptype",
			"value"       => '',
			"description" => __("One of HYBRID, SATELLITE or ROADMAP (default = ROADMAP) – not case sensitive", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Zoom", "vh"),
			"param_name"  => "zoom",
			"value"       => '',
			"description" => __("A number between 1 (narrowest) or 19 (widest) (default = 13)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Autozoom", "vh"),
			"param_name"  => "autozoom",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Child_collapse", "vh"),
			"param_name"  => "child_collapse",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Scrollwheel", "vh"),
			"param_name"  => "scrollwheel",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		)
	)
));

// gd_listing_map module
vc_map( array(
	"name"      => __( "GD Listing map", "vh" ),
	"base"      => "gd_listing_map",
	"class"     => "",
	"icon"      => "icon-wpb-ui-pricing_table-content",
	"category"  => __( "by GeoDirectory", "vh" ),
	"params"    => array(
		array(
			"type"        => "textfield",
			"heading"     => __("Width", "vh"),
			"param_name"  => "width",
			"value"       => '',
			"description" => __("A number of pixels or percent (default = 960px)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Height", "vh"),
			"param_name"  => "height",
			"value"       => '',
			"description" => __("A number of pixels or percent (default = 425px)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Maptype", "vh"),
			"param_name"  => "maptype",
			"value"       => '',
			"description" => __("One of HYBRID, SATELLITE or ROADMAP (default = ROADMAP) – not case sensitive", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Zoom", "vh"),
			"param_name"  => "zoom",
			"value"       => '',
			"description" => __("A number between 1 (narrowest) or 19 (widest) (default = 13)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Autozoom", "vh"),
			"param_name"  => "autozoom",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Child_collapse", "vh"),
			"param_name"  => "child_collapse",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Scrollwheel", "vh"),
			"param_name"  => "scrollwheel",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Sticky", "vh"),
			"param_name"  => "sticky",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		)
	)
));

// gd_listing_slider module
vc_map( array(
	"name"      => __( "GD Listing slider", "vh" ),
	"base"      => "gd_listing_slider",
	"class"     => "",
	"icon"      => "icon-wpb-ui-pricing_table-content",
	"category"  => __( "by GeoDirectory", "vh" ),
	"params"    => array(
		array(
			"type"        => "textfield",
			"heading"     => __("Post type", "vh"),
			"param_name"  => "post_type",
			"value"       => '',
			"description" => __("The slug for the post_type (default = gd_place)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Category", "vh"),
			"param_name"  => "category",
			"value"       => '',
			"description" => __("ID number of the category to show (default = 0 for all)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Post number", "vh"),
			"param_name"  => "post_number",
			"value"       => '',
			"description" => __("Number of posts to show (default = 5)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Slideshow", "vh"),
			"param_name"  => "slideshow",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Animation loop", "vh"),
			"param_name"  => "animation_loop",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Direction nav", "vh"),
			"param_name"  => "direction_nav",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Slideshow speed", "vh"),
			"param_name"  => "slideshow_speed",
			"value"       => '',
			"description" => __("A positive number of milliseconds (default = 5000)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Animation speed", "vh"),
			"param_name"  => "animation_speed",
			"value"       => '',
			"description" => __("A positive number of milliseconds (default = 600)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Animation", "vh"),
			"param_name"  => "animation",
			"value"       => '',
			"description" => __("Either slide or fade (default = slide)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Order by", "vh"),
			"param_name"  => "order_by",
			"value"       => '',
			"description" => __("One of az, latest, featured, high_review, high_rating, random (default = latest)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Show title", "vh"),
			"param_name"  => "show_title",
			"value"       => '',
			"description" => __("True or False (default = false)(this is the post title over image)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Show featured only", "vh"),
			"param_name"  => "show_featured_only",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Title", "vh"),
			"param_name"  => "title",
			"value"       => '',
			"description" => __("the title for the title of the slider", "vh")
		)
	)
));

// gd_login_box module
vc_map( array(
	"name"      => __( "GD Login box", "vh" ),
	"base"      => "gd_login_box",
	"class"     => "",
	"icon"      => "icon-wpb-ui-pricing_table-content",
	"category"  => __( "by GeoDirectory", "vh" )
));

// gd_popular_post_category module
vc_map( array(
	"name"      => __( "GD Post category", "vh" ),
	"base"      => "gd_popular_post_category",
	"class"     => "",
	"icon"      => "icon-wpb-ui-pricing_table-content",
	"category"  => __( "by GeoDirectory", "vh" ),
	"params"    => array(
		array(
			"type"        => "textfield",
			"heading"     => __("Category limit", "vh"),
			"param_name"  => "category_limit",
			"value"       => '',
			"description" => __("Number of categories to show (default = 15)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Title", "vh"),
			"param_name"  => "title",
			"value"       => '',
			"description" => __("(default = ‘Popular Categories’)", "vh")
		)
	)
));

// gd_popular_post_view module
vc_map( array(
	"name"      => __( "GD Post view", "vh" ),
	"base"      => "gd_popular_post_view",
	"class"     => "",
	"icon"      => "icon-wpb-ui-pricing_table-content",
	"category"  => __( "by GeoDirectory", "vh" ),
	"params"    => array(
		array(
			"type"        => "textfield",
			"heading"     => __("Post type", "vh"),
			"param_name"  => "post_type",
			"value"       => '',
			"description" => __("The slug for the post_type (default = gd_place)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Category", "vh"),
			"param_name"  => "category",
			"value"       => '',
			"description" => __("ID number of the category to show (default = 0 for all)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Post number", "vh"),
			"param_name"  => "post_number",
			"value"       => '',
			"description" => __("Number of posts to show (default = 5)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Layout", "vh"),
			"param_name"  => "layout",
			"value"       => '',
			"description" => __("Number of columns to show (default = 2)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Add location filter", "vh"),
			"param_name"  => "add_location_filter",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("List sort", "vh"),
			"param_name"  => "list_sort",
			"value"       => '',
			"description" => __("One of az, latest, featured, high_review, high_rating, random (default = latest)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Use viewing post type", "vh"),
			"param_name"  => "use_viewing_post_type",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Character count", "vh"),
			"param_name"  => "character_count",
			"value"       => '',
			"description" => __("Number of characters to show from the Excerpt (Min/default = 20)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Listing width", "vh"),
			"param_name"  => "listing_width",
			"value"       => '',
			"description" => __("A percent between 20 and 100 (default = ‘’)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Show featured only", "vh"),
			"param_name"  => "show_featured_only",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Show special only", "vh"),
			"param_name"  => "show_special_only",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("With pics only", "vh"),
			"param_name"  => "with_pics_only",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("With videos only", "vh"),
			"param_name"  => "with_videos_only",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		)
	)
));

// gd_recent_reviews module
vc_map( array(
	"name"      => __( "GD Recent reviews", "vh" ),
	"base"      => "gd_recent_reviews",
	"class"     => "",
	"icon"      => "icon-wpb-ui-pricing_table-content",
	"category"  => __( "by GeoDirectory", "vh" ),
	"params"    => array(
		array(
			"type"        => "textfield",
			"heading"     => __("Count", "vh"),
			"param_name"  => "count",
			"value"       => '',
			"description" => __("Number of posts to show (default = 5)", "vh")
		)
	)
));

// gd_related_listings module
vc_map( array(
	"name"      => __( "GD Related listings", "vh" ),
	"base"      => "gd_related_listings",
	"class"     => "",
	"icon"      => "icon-wpb-ui-pricing_table-content",
	"category"  => __( "by GeoDirectory", "vh" ),
	"params"    => array(
		array(
			"type"        => "textfield",
			"heading"     => __("Post number", "vh"),
			"param_name"  => "post_number",
			"value"       => '',
			"description" => __("Number of posts to show (default = 5)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Relate to", "vh"),
			"param_name"  => "relate_to",
			"value"       => '',
			"description" => __("Either category or tags (default=category)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Layout", "vh"),
			"param_name"  => "layout",
			"value"       => '',
			"description" => __("Number of columns to show (default = 2)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Add location filter", "vh"),
			"param_name"  => "add_location_filter",
			"value"       => '',
			"description" => __("True or False (default = false)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Listing width", "vh"),
			"param_name"  => "listing_width",
			"value"       => '',
			"description" => __("A percent between 20 and 100 (default = ‘’)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("List sort", "vh"),
			"param_name"  => "list_sort",
			"value"       => '',
			"description" => __("One of az, latest, featured, high_review, high_rating, random (default = latest)", "vh")
		),
		array(
			"type"        => "textfield",
			"heading"     => __("Character count", "vh"),
			"param_name"  => "character_count",
			"value"       => '',
			"description" => __("Number of characters to show from the Excerpt (Min/default = 20)", "vh")
		)
	)
));

?>
