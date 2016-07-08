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
if ( isset($_GET['stype']) ) {
	$curr_post_type = esc_attr( $_GET['stype'] );
} else {
	$curr_post_type = 'gd_place';
}
?>

<div class="geodir-map-left">
	<?php if ( get_option("vh_theme_version") != "SkyDirectory" &&  ( isset($_GET['stype']) && $_GET['stype'] != 'gd_event' ) ) { ?>
	<div class="geodir-map-listing-filters">
		<?php if ( get_option("vh_theme_version") != "SkyEstate" && isset($_GET["sgeo_when"]) ) { ?>
		<div class="geodir-filter-when">
			<span class="geodir-filter-when-text"><?php echo __("When", "vh") . ":"; ?></span>
			<span class="geodir-filter-when-value"></span>
			<input id="geodir-search-date" type="hidden" value="<?php echo esc_attr($_GET["sgeo_when"]); ?>">
		</div>
		<?php } ?>
		<ul id="geodir-filter-list">
			<li class="add-filters"><?php _e('Add filters', 'vh'); ?></li>
			<?php
			if ( !empty($_GET["sgeo_adults"]) && !empty($_GET["sgeo_childrens"]) ) {
				if ( $_GET["sgeo_childrens"] == "No" ) {
					$childrens = 0;
				} else {
					$childrens = $_GET["sgeo_childrens"];
				}
				
			//	echo '<li class="test">'.(intval($_GET["sgeo_adults"])+intval($childrens))." ".__("guests", "vh").'</li>';
			}
			?>
		</ul>
	</div>
	<?php } ?>
	<?php if ( get_option("vh_theme_version") != "SkyDirectory" ) { ?>
		<div class="geodir-filter-container">
			<div class="geodir-filter-inner">
				<?php
				global $wpdb;
				$price_result = $wpdb->get_results('SHOW COLUMNS FROM '.$wpdb->prefix.'geodir_gd_place_detail LIKE "geodir_listing_price"');
				$guests_result = $wpdb->get_results('SHOW COLUMNS FROM '.$wpdb->prefix.'geodir_gd_place_detail LIKE "geodir_listing_guest_count"');
				$bedrooms_result = $wpdb->get_results('SHOW COLUMNS FROM '.$wpdb->prefix.'geodir_gd_place_detail LIKE "geodir_listing_bedroom_count"');
				$beds_result = $wpdb->get_results('SHOW COLUMNS FROM '.$wpdb->prefix.'geodir_gd_place_detail LIKE "geodir_listing_bed_count"');
				?>
				<?php if ( !empty($price_result) ) { ?>
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
				<?php } ?>
				<?php if ( !empty($guests_result) ) { ?>
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
				<?php } ?>
				<?php if ( !empty($bedrooms_result) ) { ?>
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
				<?php } ?>
				<?php if ( !empty($beds_result) ) { ?>
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
				<?php } ?>
				<div class="filter-field filter-checkboxes">
					<?php
						if ( isset($_GET['stype']) ) {
							$stype = $_GET['stype'];
						} else {
							$stype = 'gd_place';
						}
						$custom_fields = geodir_post_custom_fields('','custom',$stype);
						foreach ($custom_fields as $extra_fields) {
							if ( $extra_fields["type"] == "checkbox" && $extra_fields["cat_sort"] == "1" ) { ?>
								<div class="checkbox">
									<span class="checkbox_box"></span>
									<span class="checkbox_text"><?php echo $extra_fields["site_title"]; ?></span>
									<input class="main_list_selecter" type="checkbox" name="<?php echo $extra_fields['name'];?>" style="display: none">
								</div>
						<?php }
						}
					?>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php if ( ( ( isset( $_GET['stype'] ) && $_GET['stype'] != 'gd_event' ) || isset($_GET['gd_placecategory']) ) || geodir_is_page('location') ) { ?>
	<div class="geodir-map-listing-top">
		<span class="property-count">0</span><span class="property-count-text"><?php echo __("properties", "vh") . ":"; ?></span>
		<div id="dd1" class="wrapper-dropdown">
			<span class="sort-by-text"><?php echo __("Sort by", "vh") . ":"; ?></span>
			<span class="sort-by icon-angle-down"><?php _e("Best price", "vh"); ?></span>
			<ul class="dropdown">
				<li><a href="javascript:void(0)" class="active" data-sort-value="price"><span class="geodir-sortby-item"><?php _e("Best price", "vh"); ?></span></a><input type="hidden" value="price"></li>
				<li><a href="javascript:void(0)" data-sort-value="rating"><span class="geodir-sortby-item"><?php _e("Rating", "vh"); ?></span></a><input type="hidden" value="Rating"></li>
			</ul>
		</div>
	</div>
	<?php } else { ?>
	<div class="geodir-map-listing-top events">
		<span class="property-count">0</span><span class="property-count-text"><?php echo __("events", "vh") . ":"; ?></span>
		<div id="dd1" class="wrapper-dropdown">
			<span class="sort-by-text"><?php echo __("Sort by", "vh") . ":"; ?></span>
			<span class="sort-by icon-angle-down"><?php _e("All events", "vh"); ?></span>
			<ul class="dropdown">
				<li><a href="javascript:void(0)" class="active"><span class="geodir-sortby-item"><?php _e("All events", "vh"); ?></span></a><input type="hidden" value="all"></li>
				<li><a href="javascript:void(0)"><span class="geodir-sortby-item"><?php _e("Today", "vh"); ?></span></a><input type="hidden" value="today"></li>
				<li><a href="javascript:void(0)"><span class="geodir-sortby-item"><?php _e("Upcoming", "vh"); ?></span></a><input type="hidden" value="upcoming"></li>
				<li><a href="javascript:void(0)"><span class="geodir-sortby-item"><?php _e("Past", "vh"); ?></span></a><input type="hidden" value="past"></li>
			</ul>
		</div>
	</div>
	<?php } ?>

	<div id="geodir-main-search">
		<img id="geodir-search-loading" src="<?php echo get_template_directory_uri();?>/images/loading.gifs">
		<input type="hidden" id="geodir-search-results" value="<?php echo esc_attr($property_count); ?>" />
		<input type="hidden" id="geodir-price-min" value="0" />
		<input type="hidden" id="geodir-price-max" value="<?php echo vh_get_geodir_max_price( $curr_post_type ); ?>" />
		<input type="hidden" id="geodir-search-post-type" value="<?php echo $curr_post_type; ?>" />
		<input type="hidden" id="geodir-current-page-id" value="<?php echo get_the_ID(); ?>" />
	</div>
</div>

<?php if ( isset($_GET["gd_placecategory"]) ) { ?>
	<input type="hidden" id="geodir-search-cateogry" value="<?php echo esc_attr($_GET["gd_placecategory"]); ?>" />
<?php } ?>
<?php if ( isset($_GET["sgeo_keyword"]) ) { ?>
	<input type="hidden" id="geodir-search-keyword" value="<?php echo esc_attr($_GET["sgeo_keyword"]); ?>" />
<?php } ?>
<?php if ( isset($_GET["sgeo_category"]) || isset($_GET["sgeo_type"]) ) { 
	if ( isset($_GET["sgeo_category"]) ) {
		$search_cat = $_GET["sgeo_category"];
	} else {
		$search_cat =$_GET["sgeo_type"];
	} ?>
	<input type="hidden" id="geodir-listing-search-category" value="<?php echo esc_attr($search_cat); ?>" />
<?php } ?>
<?php if ( isset($_GET["snear"]) ) { ?>
	<input type="hidden" id="geodir-listing-search-location" value="<?php echo esc_attr($_GET["snear"]); ?>" />
<?php } ?>
<?php if ( isset($_GET["sgeo_contract"]) ) { ?>
	<input type="hidden" id="geodir-search-contract" value="<?php echo esc_attr($_GET["sgeo_contract"]); ?>" />
<?php } ?>

<?php
	$width = empty($instance['width']) ? '0' : apply_filters('widget_width', $instance['width']);
	$height = empty($instance['heigh']) ? '0' : apply_filters('widget_heigh', $instance['heigh']);
	$maptype = empty($instance['maptype']) ? 'ROADMAP' : apply_filters('widget_maptype', $instance['maptype']);
	$zoom = empty($instance['zoom']) ? '15' : apply_filters('widget_zoom', $instance['zoom']);
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
	$map_args['map_canvas_name'] = 'geodir_map_v3_home_map_100';
	
	$is_geodir_home_map_widget = true;
	$map_args['is_geodir_home_map_widget'] = $is_geodir_home_map_widget;
	geodir_draw_map($map_args);
?>

<script type="text/javascript">
	jQuery( document ).ajaxStop(function() {
		// jQuery(".map-listing-carousel-container").jcarousel('reload').jcarousel('scroll', 0, false);
	});

	jQuery(document).ready(function($) {

		// Take date from URL
		var href_arr = location.href.split("&");
		if ( href_arr.length > 6 ) {
			var href_date = href_arr["6"].split("=")["1"].split('~');

			if ( href_date['1'] != undefined ) {
				// Format date
				var startformatted = $.datepicker.formatDate('d', new Date(href_date['0']));
				var endformatted   = $.datepicker.formatDate('d M', new Date(href_date['1']));
				var year           = $.datepicker.formatDate('yy', new Date(href_date['1']));

				// Set text field value
				jQuery(".geodir-filter-when-value").html(startformatted+" - "+endformatted+ " "+year);
			} else {
				// Set text field value
				jQuery(".geodir-filter-when-value").html(href_date['0']);
			}
		} else {
			jQuery('.geodir-filter-when').hide();
		}

		var jsonData = '';

		function get_geomap_markers() {
			var listing_price_val = listing_guests_val = listing_bedrooms_val = listing_beds_val = '';

			jQuery("#geodir-filter-list li").each(function() {
			
				if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("per night") >= 0 ) {
					listing_price_val = jQuery(this).find(".tagit-label").html();
					//jQuery("div").find(".map-listing-price").html();
						console.log(">>>>>>>Price"+  listing_price_val);
				} else if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("guests") >= 0 ) {
					listing_guests_val = jQuery(this).find(".tagit-label").html();
						console.log(">>>>>>>Guset"+  listing_guests_val);
				} else if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("bedrooms") >= 0 ) {
					listing_bedrooms_val = jQuery(this).find(".tagit-label").html();
						console.log(">>>>>>>Bedroom"+  listing_bedrooms_val);
				} else if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("beds") >= 0 ) {
					listing_beds_val = jQuery(this).find(".tagit-label").html();
						console.log(">>>>>>>Beds"+  listing_beds_val);
				} 
			});

			<?php
				if ( !isset($_GET["sgeo_category"]) ) {
					$sgeo_category = '';
				} else {
					$sgeo_category = esc_js($_GET["sgeo_category"]);
				}
				if ( !isset($_GET["sgeo_type"]) ) {
					$sgeo_type = '';
				} else {
					$sgeo_type = esc_js($_GET["sgeo_type"]);
				}
			?>

			if ( '<?php echo $sgeo_category; ?>' == '' ) {
				var listing_category = '<?php echo $sgeo_type; ?>';
			} else {
				var listing_category = '<?php echo $sgeo_category; ?>';
			}

			var sPageURL = window.location.search.substring(1);
			var sURLVariables = sPageURL.split('&');
			var ajaxParams = '';

			for (var i = 0; i < sURLVariables.length; i++) {
				var sParameterName = sURLVariables[i].split('=');
				if ( ( sParameterName[0].indexOf('geodir_') >= 0 || sParameterName[0].indexOf('snear') >= 0 || sParameterName[0].indexOf('event') >= 0 || sParameterName[0].indexOf('gd_') >= 0 ) && sParameterName[0] != 'geodir_search' ) {
					if ( sParameterName[0].indexOf('snear') >= 0 ) {
						ajaxParams += ', "search_location": "'+sParameterName[1]+'"';
					} else if ( sParameterName[0].indexOf('gd_') >= 0 ) {
						ajaxParams += ', "listing_search_cat": "'+sParameterName[1]+'"';
					} else {
						ajaxParams += ', "'+sParameterName[0]+'": "'+sParameterName[1]+'"';
					}
				}
			}

			jQuery('.filter-checkboxes > .checkbox').each(function() {
				if ( jQuery(this).find('input[type=checkbox]').is(':checked') ) {
					ajaxParams += ', "'+jQuery(this).find('input[type=checkbox]').attr('name')+'": "1"';
				};
			});

			<?php
				if ( !isset($_GET["sgeo_when"]) ) {
					$sgeo_when = '';
				} else {
					$sgeo_when = esc_js($_GET["sgeo_when"]);
				}
				if ( !isset($_GET["sgeo_keyword"]) ) {
					$sgeo_keyword = '';
				} else {
					$sgeo_keyword = esc_js($_GET["sgeo_keyword"]);
				}
				if ( !isset($_GET["sgeo_contract"]) ) {
					$sgeo_contract = '';
				} else {
					$sgeo_contract = esc_js($_GET["sgeo_contract"]);
				}
			?>
			
			/*
			jQuery(".map-listing-price").each(function() {
			console.log(jQuery(this).text());
			});
			*/
			var updated_data = '{"action": "geodir_search_markers", "search_lat": "'+vh_getUrlParameter('sgeo_lat')+'", "search_long": "'+vh_getUrlParameter('sgeo_lon')+'", "listing_date": "<?php echo $sgeo_when; ?>", "listing_price": "'+listing_price_val+'", "listing_guests": "'+listing_guests_val+'", "listing_bedrooms": "'+listing_bedrooms_val+'", "listing_beds": "'+listing_beds_val+'", "search_keyword": "<?php echo $sgeo_keyword; ?>", "listing_search_cat": "'+listing_category+'", "listing_contract": "<?php echo $sgeo_contract; ?>"'+ajaxParams+' }';

			ajaxData = jQuery.parseJSON(updated_data);

			jQuery.ajax({
				type: 'POST',
				url: my_ajax.ajaxurl,
				data: ajaxData,
				success: function(response) {
					jsonData = jQuery.parseJSON(response);
					vh_setAllMap(null);
					if ( jsonData['0'].totalcount != '0' ) {
						list_markers(jsonData, 'geodir_map_v3_home_map_100');
						jQuery("#geodir-main-search").html(jsonData['all_markers']);
						jQuery("#geodir-main-search").removeClass("loading");

						jQuery('.advmap_nofound').hide();
					} else {
						jQuery("#geodir-main-search").html(jsonData['all_markers']);
						jQuery("#geodir-main-search").removeClass("loading");
						jQuery('.advmap_nofound').show();
					}

					jQuery('.geodir-map-listing-top .property-count').html(jsonData['0'].totalcount);
					
					return false;
				}
			});
		}

		function vh_getUrlParameter(sParam) {
			var sPageURL = window.location.search.substring(1);
			var sURLVariables = sPageURL.split('&');
			for (var i = 0; i < sURLVariables.length; i++)
			{
				var sParameterName = sURLVariables[i].split('=');
				if (sParameterName[0] == sParam)
				{
					return sParameterName[1];
				}
			}
		}

		<?php if ( get_option("vh_theme_version") != "SkyDirectory" ) { ?>

			if ( typeof vh_getUrlParameter('price') != 'undefined' ) {
				var price = vh_getUrlParameter('price');
				price = price.split('-');
				var min_price = price['0'];
				var max_price = price['1'];
				min_price = min_price.replace(my_ajax.currency_symbol, '');
				max_price = max_price.replace(my_ajax.currency_symbol, '');
			} else {
				var min_price = 0;
				var max_price = <?php echo vh_get_geodir_max_price( $curr_post_type ); ?>;
			}

			jQuery( "#slider-range-price" ).slider({
				range: true,
				min: 0,
				max: <?php echo vh_get_geodir_max_price( $curr_post_type ); ?>,

				values: [ min_price, max_price ],
				create: function( event, ui ) {
					var current_values = jQuery(this).slider("values");
					jQuery(this).parent().find(".range-slider-min").html(my_ajax.curr_sign + current_values[ 0 ]);///////jino
					jQuery(this).parent().find(".range-slider-max").html(my_ajax.curr_sign + current_values[ 1 ]);/////jino
				},
				slide: function( event, ui ) {
					jQuery(this).parent().find(".range-slider-min").html(my_ajax.curr_sign + ui.values[ 0 ]);/////jino
					jQuery(this).parent().find(".range-slider-max").html(my_ajax.curr_sign + ui.values[ 1 ]);/////jino
				},
				start: function( event, ui ) {
					jQuery(this).parent().find(".ui-slider-range.ui-widget-header").addClass("ui-active");
				},
				stop: function( event, ui ) {
					jQuery(this).parent().find(".ui-slider-range.ui-widget-header").removeClass("ui-active");

					var tag = '' + ui.values[ 0 ] + "-" + '' + ui.values[ 1 ] + " <?php echo _e('per night','vh'); ?>";/////jino
					jQuery("#geodir-filter-list li").each(function() {
						if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("per night") >= 0 ) {
							jQuery(this).remove();
						};
					});
					jQuery("#geodir-filter-list").tagit("createTag", tag);

					get_geomap_markers();

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

					var tag = ui.values[ 0 ] + "-" + ui.values[ 1 ] + " <?php echo _e('guests','geodirectory'); ?>";
					jQuery("#geodir-filter-list li").each(function() {
						if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("guests") >= 0 ) {
							jQuery(this).remove();
						};
					});
					jQuery("#geodir-filter-list").tagit("createTag", tag);

					get_geomap_markers();

					jQuery("#geodir-main-search").addClass("loading");
				}
			});

			if ( typeof vh_getUrlParameter('bedrooms') != 'undefined' ) {
				var bedrooms = vh_getUrlParameter('bedrooms');
				bedrooms = bedrooms.split('-');
				var min_bedrooms = bedrooms['0'];
				var max_bedrooms = bedrooms['1'];
			} else {
				var min_bedrooms = 0;
				var max_bedrooms = 10;
			}

			jQuery( "#slider-range-bedrooms" ).slider({
				range: true,
				min: 1,
				max: 10,
				values: [ min_bedrooms, max_bedrooms ],
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

					var tag = ui.values[ 0 ] + "-" + ui.values[ 1 ] + " <?php echo _e('bedrooms','vh'); ?>";
					jQuery("#geodir-filter-list li").each(function() {
						if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("bedrooms") >= 0 ) {
							jQuery(this).remove();
						};
					});
					jQuery("#geodir-filter-list").tagit("createTag", tag);

					get_geomap_markers();

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

					var tag = ui.values[ 0 ] + "-" + ui.values[ 1 ] + " <?php echo _e('beds','vh'); ?>";
					jQuery("#geodir-filter-list li").each(function() {
						if ( jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("beds") >= 0 ) {
							jQuery(this).remove();
						};
					});
					jQuery("#geodir-filter-list").tagit("createTag", tag);

					get_geomap_markers();

					jQuery("#geodir-main-search").addClass("loading");
				}
			});
		<?php } ?>

		jQuery('.filter-checkboxes .main_list_selecter').each(function() {
			if ( vh_getUrlParameter(jQuery(this).attr('name')) == '1' ) {
				jQuery(this).parent().addClass('checked');
				jQuery(this).parent().find('.checkbox_box').addClass('checked icon-ok')
				jQuery(this).prop('checked', true);
			};
		});

		jQuery('div.checkbox').live('click', function() {
			if ( jQuery(".geodir-map-filters.googlemap").length ) {
				get_geomap_markers();
			} else {
				var checkboxval = jQuery(this).find('input').attr('name');
				var search_params = location.search;
				var search_values = search_params.split('&');
				var new_search = '';

				if ( typeof vh_getUrlParameter(checkboxval) == 'undefined' ) {
					new_search = search_params+'&'+checkboxval+'=1&';
				} else {
					for (var i = 0; i < search_values.length; i++) {
						var sParameterName = search_values[i].split('=');
						if ( sParameterName[0] != checkboxval ) {
							new_search += search_values[i]+'&';
						}
					}
				}

				history.pushState({}, '', new_search.slice(0, -1) );
				get_geomap_markers();
			}
			jQuery("#geodir-main-search").addClass("loading");
		});

		jQuery("#geodir-filter-list").tagit();

		if ( typeof vh_getUrlParameter('price') != 'undefined' ) {
			var tag = vh_getUrlParameter('price')+" per night";
			jQuery("#geodir-filter-list").tagit("createTag", tag);
		};

		jQuery('.geodir-map-listing-top.events ul li a').live('click', function() {

			jQuery('#geodir-main-search').addClass('loading');
			var event_type = jQuery(this).parent().find('input').val();

			var updated_data = '{"action": "geodir_search_markers", "post_type": "gd_event", "search_location": "'+vh_getUrlParameter('snear')+'", "search_lat": "'+vh_getUrlParameter('sgeo_lat')+'", "search_long": "'+vh_getUrlParameter('sgeo_lon')+'", "listing_date": "'+event_type+'", "search_keyword": "'+vh_getUrlParameter('sgeo_keyword')+'" }';

			ajaxData = jQuery.parseJSON(updated_data);

			jQuery.ajax({
				type: 'POST',
				url: my_ajax.ajaxurl,
				data: ajaxData,
				success: function(response) {
					jsonData = jQuery.parseJSON(response);
					vh_setAllMap(null);
					if ( jsonData['0'].totalcount != '0' ) {
						list_markers(jsonData, 'geodir_map_v3_home_map_100');
						jQuery("#geodir-main-search").html(jsonData['all_markers']);
						jQuery("#geodir-main-search").removeClass("loading");
						jQuery('.advmap_nofound').hide();
					} else {
						jQuery("#geodir-main-search").html(jsonData['all_markers']);
						jQuery("#geodir-main-search").removeClass("loading");
						jQuery('.advmap_nofound').show();
					}
					return false;
				}
			});
		});
	});
</script>

<div class="clearfix"></div>
<?php do_action('geodir_after_listing_listview');   
