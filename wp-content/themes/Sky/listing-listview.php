<?php do_action('geodir_before_listing_listview'); global $gridview_columns;
$grid_view_class = apply_filters('geodir_grid_view_widget_columns' ,$gridview_columns);
if(isset($_SESSION['gd_listing_view']) && $_SESSION['gd_listing_view']!='' && !isset($before_widget) && !isset($related_posts)){
	if($_SESSION['gd_listing_view']=='1'){$grid_view_class = '';}
	if($_SESSION['gd_listing_view']=='2'){$grid_view_class = 'gridview_onehalf';}
	if($_SESSION['gd_listing_view']=='3'){$grid_view_class = 'gridview_onethird ';}
	if($_SESSION['gd_listing_view']=='4'){$grid_view_class = 'gridview_onefourth';}
	if($_SESSION['gd_listing_view']=='5'){$grid_view_class = 'gridview_onefifth';}
}
$property_count = 0;
$price_min = $price_max = 0;
?>

<div class="geodir-map-listing-filters">
	<div class="geodir-filter-when">
		<span class="geodir-filter-when-text"><?php echo __("When", "vh") . ":"; ?></span>
		<span class="geodir-filter-when-value"></span>
	</div>
	<ul id="geodir-filter-list">
		<li class="add-filters"><?php _e('Add filters', 'vh'); ?></li>
		<?php
		if ( !empty($_GET["sgeo_adults"]) && !empty($_GET["sgeo_childrens"]) ) {
			echo '<li class="test">'.(intval($_GET["sgeo_adults"])+intval($_GET["sgeo_childrens"]))." ".__("guests", "vh").'</li>';
		}
		?>

	</ul>
</div>
<div class="geodir-filter-container">
	<div class="geodir-filter-inner">
		<div class="filter-field filter-per-night">
			<div class="filter-left">
				<span class="filter-text"><?php _e("Price", "vh"); ?></span>
				<span class="filter-second-text"><?php _e("Per night", "vh"); ?></span>
			</div>
			<div class="filter-right">
				<span class="range-slider-min">1</span>
				<span class="range-slider-max">1</span>
				<div id="slider-range-price"></div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="filter-field filter-guests">
			<div class="filter-left">
				<span class="filter-text"><?php _e("Guests", "vh"); ?></span>
			</div>
			<div class="filter-right">
				<span class="range-slider-min">1</span>
				<span class="range-slider-max">20+</span>
				<div id="slider-range-guests"></div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="filter-field filter-bedrooms">
			<div class="filter-left">
				<span class="filter-text"><?php _e("Bedrooms", "vh"); ?></span>
			</div>
			<div class="filter-right">
				<span class="range-slider-min">1</span>
				<span class="range-slider-max">10+</span>
				<div id="slider-range-bedrooms"></div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="filter-field filter-beds">
			<div class="filter-left">
				<span class="filter-text"><?php _e("Beds", "vh"); ?></span>
			</div>
			<div class="filter-right">
				<span class="range-slider-min">1</span>
				<span class="range-slider-max">7+</span>
				<div id="slider-range-beds"></div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="filter-field filter-checkboxes">
			<?php
				$custom_fields = geodir_post_custom_fields('','custom',$_GET['stype']);
				foreach ($custom_fields as $extra_fields) {
					if ( $extra_fields["type"] == "checkbox" && $extra_fields["cat_sort"] == "1" ) { ?>
						<div class="checkbox">
							<span class="checkbox_box"></span>
							<span class="checkbox_text"><?php echo $extra_fields["site_title"]; ?></span>
							<input class="main_list_selecter" type="checkbox" field_type="checkbox" name="geodir_accept_term_condition" id="geodir_accept_term_condition" value="1" style="display: none">
						</div>
				<?php }
				}
			?>
			<div class="clearfix"></div>
		</div>
	</div>

</div>

<div class="geodir-map-listing-top">
	<span class="property-count">0</span><span class="property-count-text"><?php echo __("properties", "vh") . ":"; ?></span>
	<div id="dd1" class="wrapper-dropdown">
		<span class="sort-by-text"><?php echo __("Sort by", "vh") . ":"; ?></span>
		<span class="sort-by icon-angle-down"><?php _e("Best price", "vh"); ?></span>
		<ul class="dropdown">
			<li><a href="javascript:void(0)" class="active" data-sort-value="price"><span><?php _e("Best price", "vh"); ?></span></a><input type="hidden" value="price"></li>
			<li><a href="javascript:void(0)" data-sort-value="rating"><span><?php _e("Rating", "vh"); ?></span></a><input type="hidden" value="Rating"></li>
		</ul>
	</div>
	<!-- <span class="property-sort">Sort By: Best price</span> -->
</div>

<div id="geodir-main-search">
	<img id="geodir-search-loading" src="<?php echo get_template_directory_uri();?>/images/loading.gif">
	<input type="hidden" id="geodir-search-results" value="<?php echo esc_attr($property_count); ?>" />
</div>

<input type="hidden" id="geodir-price-min" value="0" />
<input type="hidden" id="geodir-price-max" value="9999" />
<?php if ( $_GET["gd_placecategory"] != "" ) { ?>
	<input type="hidden" id="geodir-search-cateogry" value="<?php echo esc_attr($_GET["gd_placecategory"]); ?>" />
<?php } ?>


<?php
	$width = empty($instance['width']) ? '555' : apply_filters('widget_width', $instance['width']);
	$height = empty($instance['heigh']) ? '555' : apply_filters('widget_heigh', $instance['heigh']);
	$maptype = empty($instance['maptype']) ? 'ROADMAP' : apply_filters('widget_maptype', $instance['maptype']);
	$zoom = empty($instance['zoom']) ? '13' : apply_filters('widget_zoom', $instance['zoom']);
	$autozoom = empty($instance['autozoom']) ? '' : apply_filters('widget_autozoom', $instance['autozoom']);
	$child_collapse = empty($instance['child_collapse']) ? '0' : apply_filters('widget_child_collapse', $instance['child_collapse']);
	$scrollwheel = empty($instance['scrollwheel']) ? '0' : apply_filters('widget_scrollwheel', $instance['scrollwheel']);
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
	
	$is_geodir_home_map_widget = true;
	$map_args['is_geodir_home_map_widget'] = $is_geodir_home_map_widget;
	geodir_draw_map($map_args);
?>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		var map_markers = window.all_markers;
		function get_geodir_search_posts(date,price,guests,bedrooms,beds,search_type,search_cat,ajaxurl) {
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "geodir_search", listing_date: date, listing_price: price, listing_guests: guests, listing_bedrooms: bedrooms, listing_beds: beds, search: search_type, search_category: search_cat},
				success: function(response) {
					jQuery("#geodir-main-search").html(response);
					jQuery(".geodir_category_list_view").isotope();
					// jQuery(".map-listing-carousel-container").jcarousel();
					jQuery(".property-count").html(jQuery("#geodir-search-results").val());
					jQuery("#geodir-main-search").removeClass("loading");

					// jsonData = jQuery.parseJSON(jQuery("#geodir-visible_markers").html());
						
					// setAllMap(null);
					// map_markers = [];
					// for (var i = 0; i < jsonData.length; i++) {
					// 	var coord = new google.maps.LatLng(jsonData[i].lt, jsonData[i].ln);
					// 	var marker = new RichMarker({
					// 			position: coord,
					// 			map: jQuery.goMap.map,
					// 			draggable: false,
					// 			flat: true,
					// 			content: '<div id="geo-marker-'+jsonData[i].id+'"><img src="'+jsonData[i].i+'"/></div>'
					// 		});
					// 	map_markers.push(marker);
					// }

					return false;
				}
			});
		}

		function setAllMap(map) {
			for (var i = 0; i < map_markers.length; i++) {
				map_markers[i].setMap(map);
			}
		}

		if ( jQuery("body").hasClass("geodir-category-search") ) {
			get_geodir_search_posts(jQuery(".geodir-filter-when-value").html(),'','','','', 'category', jQuery("#geodir-search-cateogry").val(), my_ajax.ajaxurl);
		} else {
			get_geodir_search_posts(jQuery.cookie('vh_selected_date'),'',<?php echo (intval($_GET["sgeo_adults"])+intval($_GET["sgeo_childrens"])); ?>,'','','','',my_ajax.ajaxurl);
		}

		function get_geomap_markers() {
			var listing_price_val = listing_guests_val = listing_bedrooms_val = listing_beds_val = '';

			jQuery("#geodir-filter-list li").each(function() {
				if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("per night") >= 0 ) {
					listing_price_val = jQuery(this).find(".tagit-label").html();
				} else if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("guests") >= 0 ) {
					listing_guests_val = jQuery(this).find(".tagit-label").html();
				} else if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("bedrooms") >= 0 ) {
					listing_bedrooms_val = jQuery(this).find(".tagit-label").html();
				} else if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("beds") >= 0 ) {
					listing_beds_val = jQuery(this).find(".tagit-label").html();
				}
			});

			jQuery.ajax({
				type: 'POST',
				url: my_ajax.ajaxurl,
				data: {"action": "geodir_search_markers", listing_date: jQuery(".geodir-filter-when-value").html(), listing_price: listing_price_val, listing_guests: listing_guests_val, listing_bedrooms: listing_bedrooms_val, listing_beds: listing_beds_val},
				success: function(response) {
					jsonData = jQuery.parseJSON(response);
					
					setAllMap(null);
					map_markers = [];
					for (var i = 0; i < jsonData.length; i++) {
						var coord = new google.maps.LatLng(jsonData[i].lt, jsonData[i].ln);
						var marker = new RichMarker({
								position: coord,
								map: jQuery.goMap.map,
								draggable: false,
								flat: true,
								content: '<div id="geo-marker-'+jsonData[i].id+'"><img src="'+jsonData[i].i+'"/></div>'
							});
						map_markers.push(marker);
					}
					
					return false;
				}
			});
		}
		
		jQuery( "#slider-range-price" ).slider({
			range: true,
			min: jQuery("#geodir-price-min").val(),
			max: jQuery("#geodir-price-max").val(),
			values: [ jQuery("#geodir-price-min").val(), jQuery("#geodir-price-max").val() ],
			slide: function( event, ui ) {
				jQuery(this).parent().find(".range-slider-min").html("$" + ui.values[ 0 ]);
				jQuery(this).parent().find(".range-slider-max").html("$" + ui.values[ 1 ]);
			},
			start: function( event, ui ) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").addClass("ui-active");
			},
			stop: function( event, ui ) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").removeClass("ui-active");

				var tag = "$" + ui.values[ 0 ] + "-" + "$" + ui.values[ 1 ] + " per night";
				var listing_price = listing_guests = listing_bedrooms = listing_beds = '';
				
				jQuery("#geodir-filter-list li").each(function() {
					if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("per night") >= 0 ) {
						jQuery(this).remove();
					};
				});
				jQuery("#geodir-filter-list").tagit("createTag", tag);

				jQuery("#geodir-filter-list li").each(function() {
					if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("per night") >= 0 ) {
						listing_price = listing_price_val = jQuery(this).find(".tagit-label").html();
					} else if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("guests") >= 0 ) {
						listing_guests = listing_guests_val = jQuery(this).find(".tagit-label").html();
					} else if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("bedrooms") >= 0 ) {
						listing_bedrooms = listing_bedrooms_val = jQuery(this).find(".tagit-label").html();
					} else if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("beds") >= 0 ) {
						listing_beds = listing_beds_val = jQuery(this).find(".tagit-label").html();
					}
				});

				if ( jQuery(".geodir-map-filters.googlemap").length ) {
					get_geomap_markers();
				} else {
					if ( jQuery("body").hasClass("geodir-category-search") ) {
						get_geodir_search_posts(jQuery(".geodir-filter-when-value").html(), listing_price, listing_guests, listing_bedrooms, listing_beds, 'category', jQuery("#geodir-search-cateogry").val(), my_ajax.ajaxurl);
					} else {
						get_geomap_markers();
						get_geodir_search_posts(jQuery(".geodir-filter-when-value").html(), listing_price, listing_guests, listing_bedrooms, listing_beds, '', '', my_ajax.ajaxurl);
					}
				}
				jQuery("#geodir-main-search").addClass("loading");

			}
		});

		jQuery( "#slider-range-guests" ).slider({
			range: true,
			min: 1,
			max: 20,
			values: [ 1, 20 ],
			slide: function( event, ui ) {
				jQuery(this).parent().find(".range-slider-min").html(ui.values[ 0 ]);
				if ( ui.values[ 1 ] == jQuery( "#slider-range-guests" ).slider("option", "max") ) {
					jQuery(this).parent().find(".range-slider-max").html(ui.values[ 1 ]+"+");
				} else {
					jQuery(this).parent().find(".range-slider-max").html(ui.values[ 1 ]);
				}
			},
			start: function( event, ui ) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").addClass("ui-active");
			},
			stop: function( event, ui ) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").removeClass("ui-active");

				var tag = ui.values[ 0 ] + "-" + ui.values[ 1 ] + " guests";
				
				jQuery("#geodir-filter-list li").each(function() {
					if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("guests") >= 0 ) {
						jQuery(this).remove();
					};
				});
				jQuery("#geodir-filter-list").tagit("createTag", tag);
			}
		});

		jQuery( "#slider-range-bedrooms" ).slider({
			range: true,
			min: 1,
			max: 10,
			values: [ 1, 10 ],
			slide: function( event, ui ) {
				jQuery(this).parent().find(".range-slider-min").html(ui.values[ 0 ]);
				if ( ui.values[ 1 ] == jQuery( "#slider-range-bedrooms" ).slider("option", "max") ) {
					jQuery(this).parent().find(".range-slider-max").html(ui.values[ 1 ]+"+");
				} else {
					jQuery(this).parent().find(".range-slider-max").html(ui.values[ 1 ]);
				}
			},
			start: function( event, ui ) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").addClass("ui-active");
			},
			stop: function( event, ui ) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").removeClass("ui-active");

				var tag = ui.values[ 0 ] + "-" + ui.values[ 1 ] + " bedrooms";
				var listing_price = listing_guests = listing_bedrooms = listing_beds = '';
				
				jQuery("#geodir-filter-list li").each(function() {
					if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("bedrooms") >= 0 ) {
						jQuery(this).remove();
					};
				});
				jQuery("#geodir-filter-list").tagit("createTag", tag);

				jQuery("#geodir-filter-list li").each(function() {
					if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("per night") >= 0 ) {
						listing_price = jQuery(this).find(".tagit-label").html();
					} else if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("guests") >= 0 ) {
						listing_guests = jQuery(this).find(".tagit-label").html();
					} else if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("bedrooms") >= 0 ) {
						listing_bedrooms = jQuery(this).find(".tagit-label").html();
					} else if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("beds") >= 0 ) {
						listing_beds = jQuery(this).find(".tagit-label").html();
					}
				});

				if ( jQuery(".geodir-map-filters.googlemap").length ) {
					jQuery.ajax({
						type: 'POST',
						url: my_ajax.ajaxurl,
						data: {"action": "geodir_search_markers", listing_date: jQuery(".geodir-filter-when-value").html(), listing_price: listing_price_val, listing_guests: listing_guests_val, listing_bedrooms: listing_bedrooms_val, listing_beds: listing_beds_val},
						success: function(response) {
							jsonData = jQuery.parseJSON(response);
							
							setAllMap(null);
							map_markers = [];
							for (var i = 0; i < jsonData.length; i++) {
								var coord = new google.maps.LatLng(jsonData[i].lt, jsonData[i].ln);
								var marker = new RichMarker({
										position: coord,
										map: jQuery.goMap.map,
										draggable: false,
										flat: true,
										content: '<div id="geo-marker-'+jsonData[i].id+'"><img src="'+jsonData[i].i+'"/></div>'
									});
								map_markers.push(marker);
							}
							
							return false;
						}
					});
				} else {
					get_geodir_search_posts(jQuery(".geodir-filter-when-value").html(), listing_price, listing_guests, listing_bedrooms, listing_beds, my_ajax.ajaxurl);
				}

				jQuery("#geodir-main-search").addClass("loading");
			}
		});

		jQuery( "#slider-range-beds" ).slider({
			range: true,
			min: 1,
			max: 7,
			values: [ 1, 7 ],
			slide: function( event, ui ) {
				jQuery(this).parent().find(".range-slider-min").html(ui.values[ 0 ]);
				if ( ui.values[ 1 ] == jQuery( "#slider-range-beds" ).slider("option", "max") ) {
					jQuery(this).parent().find(".range-slider-max").html(ui.values[ 1 ]+"+");
				} else {
					jQuery(this).parent().find(".range-slider-max").html(ui.values[ 1 ]);
				}
			},
			start: function( event, ui ) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").addClass("ui-active");
			},
			stop: function( event, ui ) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").removeClass("ui-active");

				var tag = ui.values[ 0 ] + "-" + ui.values[ 1 ] + " beds";
				var listing_price = listing_guests = listing_bedrooms = listing_beds = '';
				
				jQuery("#geodir-filter-list li").each(function() {
					if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("beds") >= 0 ) {
						jQuery(this).remove();
					};
				});
				jQuery("#geodir-filter-list").tagit("createTag", tag);

				jQuery("#geodir-filter-list li").each(function() {
					if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("per night") >= 0 ) {
						listing_price = jQuery(this).find(".tagit-label").html();
					} else if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("guests") >= 0 ) {
						listing_guests = jQuery(this).find(".tagit-label").html();
					} else if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("bedrooms") >= 0 ) {
						listing_bedrooms = jQuery(this).find(".tagit-label").html();
					} else if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("beds") >= 0 ) {
						listing_beds = jQuery(this).find(".tagit-label").html();
					}
				});

				if ( jQuery(".geodir-map-filters.googlemap").length ) {
					jQuery.ajax({
						type: 'POST',
						url: my_ajax.ajaxurl,
						data: {"action": "geodir_search_markers", listing_date: jQuery(".geodir-filter-when-value").html(), listing_price: listing_price_val, listing_guests: listing_guests_val, listing_bedrooms: listing_bedrooms_val, listing_beds: listing_beds_val},
						success: function(response) {
							jsonData = jQuery.parseJSON(response);
							
							setAllMap(null);
							map_markers = [];
							for (var i = 0; i < jsonData.length; i++) {
								var coord = new google.maps.LatLng(jsonData[i].lt, jsonData[i].ln);
								var marker = new RichMarker({
										position: coord,
										map: jQuery.goMap.map,
										draggable: false,
										flat: true,
										content: '<div id="geo-marker-'+jsonData[i].id+'"><img src="'+jsonData[i].i+'"/></div>'
									});
								map_markers.push(marker);
							}
							
							return false;
						}
					});
				} else {
					get_geodir_search_posts(jQuery(".geodir-filter-when-value").html(), listing_price, listing_guests, listing_bedrooms, listing_beds, my_ajax.ajaxurl);
				}
				
				jQuery("#geodir-main-search").addClass("loading");
			}
		});

		jQuery(".filter-field.filter-per-night .range-slider-min").html("<?php echo '$'.$price_min; ?>");
		jQuery(".filter-field.filter-per-night .range-slider-max").html("<?php echo '$'.$price_max; ?>");

		jQuery("#geodir-filter-list").tagit();
	});
</script>

<div class="clear"></div>
<?php do_action('geodir_after_listing_listview');   
