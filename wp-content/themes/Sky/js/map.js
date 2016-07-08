
jQuery(window).load(function() {
	if ( typeof jQuery.goMap.map != 'undefined' ) {
		setTimeout(function() {
			google.maps.event.trigger(jQuery.goMap.map, 'resize');
	    /*jino map added to center*/
		var center = bounds.getCenter();
		jQuery.goMap.map.setCenter(center);
		}, 500);
	}
});

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

function initMap(map_options){ 
	//alert(map_options)
	map_options = eval(map_options) ;
	map_options.zoom = parseInt(map_options.zoom);
	
	options = map_options;
	


	var pscaleFactor;
	var pstartmin;
	var ajax_url =  options.ajax_url;
	var token  = options.token;
	var search_string = options.token;
	var mm = 0; // marker array
	var maptype = options.maptype;
	var zoom = options.zoom;
	var latitude = options.latitude;
	var longitude = options.longitude;
	var maxZoom = options.maxZoom;
	var etype = options.etype;
	var autozoom =  options.autozoom;
	var scrollwheel =  options.scrollwheel;
	var streetview =  options.streetViewControl;
	var bubble_size =  options.bubble_size;
	var map_canvas = options.map_canvas_name;
	var enable_map_direction = options.enable_map_direction;
	var enable_cat_filters = options.enable_cat_filters ;
	var enable_marker_cluster = options.enable_marker_cluster ;
	options.token = '68f48005e256696074e1da9bf9f67f06';
	options.navigationControlOptions = { position:	'TOP_RIGHT', style:'SMALL'};
	options.mapTypeControl = false;

	if ( jQuery("body").hasClass("geodir-main-search") || jQuery("body").hasClass("geodir-category-search") ) {
		options.width = (jQuery(window).width()-673)+'px';

		if ( jQuery("body").hasClass("admin-bar") ) {
			options.height = (jQuery(window).height()-102)+'px';
		} else {
			options.height = (jQuery(window).height()-70)+'px';
		};
		
	} else if ( jQuery("body").hasClass("page-template-template-frontpage") ) {
		options.width = jQuery(window).width()+'px';
		options.height = '747px';
	}

	// Create map
	jQuery("#" + map_canvas).goMap(options);
	// set max zoom
	
	var styles = [
			{
			featureType: "poi",
			stylers: [
				{ visibility: "off" }
			]
			},
			{
			featureType: "transit",
			stylers: [
				{ visibility: "off" }
			]
			},
	];
	
	if(!(typeof geodir_custom_map_style === 'undefined' ))
		styles = geodir_custom_map_style ;
		
	jQuery.goMap.map.setOptions({styles: styles});
	
	google.maps.event.addListenerOnce(jQuery.goMap.map, 'idle', function(){
		jQuery("#"  + map_canvas).goMap();
		for(var i in google.maps.MapTypeId){
			jQuery.goMap.map.mapTypes[google.maps.MapTypeId[i]].maxZoom = options.maxZoom;
		}
		google.maps.event.trigger(jQuery.goMap.map, 'resize');
	});

	google.maps.event.addListenerOnce(jQuery.goMap.map, 'tilesloaded', function(){
		google.maps.event.trigger(jQuery.goMap.map, 'resize');
		google.maps.event.trigger(jQuery.goMap.map, 'dragend');
	});

	google.maps.event.addListener(gd_infowindow,'domready',function(){
		if ( jQuery("#main_header_bg").length ) {
			jQuery(".gm-style-iw").parent().css('top', parseInt(jQuery(".gm-style-iw").parent().css('top').replace('px', ''))+70);
		};
	});

	var maxMap = document.getElementById(map_canvas + '_triggermap' );
	google.maps.event.addDomListener(maxMap, 'click', showAlert);
	function showAlert() {
	
		jQuery('#' + map_canvas).toggleClass('map-fullscreen');
		jQuery('.' + map_canvas +'_map_category').toggleClass('map_category_fullscreen');
		jQuery('#' + map_canvas +'_trigger').toggleClass('map_category_fullscreen');
		jQuery('body').toggleClass('body_fullscreen');
		jQuery('#' + map_canvas+'_loading_div').toggleClass('loading_div_fullscreen');
		jQuery('#' + map_canvas +'_map_nofound').toggleClass('nofound_fullscreen');
		jQuery('#' + map_canvas +'_triggermap').toggleClass('triggermap_fullscreen');
		jQuery('.trigger').toggleClass('triggermap_fullscreen');
		jQuery('.map-places-listing').toggleClass('triggermap_fullscreen');
		jQuery('.' + map_canvas +'_TopLeft').toggleClass('TopLeft_fullscreen');
		jQuery('#' + map_canvas +'_triggermap').closest('.geodir_map_container').toggleClass('geodir_map_container_fullscreen');
		
		window.setTimeout(function() { 
			var center = jQuery.goMap.map.getCenter(); 
			jQuery("#"+map_canvas).goMap();
			google.maps.event.trigger(jQuery.goMap.map, 'resize');
			jQuery.goMap.map.setCenter(center); 
			setGeodirMapSize(true);
		}, 100);
	}

	/* add option that allows enable/disable map dragging to phone devices */
	var hasControl = jQuery('#' + map_canvas).find('.gd-control-div').hasClass(map_canvas + '-control-div');
	if (geodir_all_js_msg.geodir_is_mobile && typeof geodir_all_js_msg.geodir_onoff_dragging != 'undefined' && geodir_all_js_msg.geodir_onoff_dragging && !hasControl) {
		var centerControlDiv = document.createElement('div');
		centerControlDiv.index = 1;

		jQuery(centerControlDiv).addClass('gd-control-div');
		jQuery(centerControlDiv).addClass(map_canvas + '-control-div');

		var centerControl = new gdCustomControl(centerControlDiv, options.enable_cat_filters, jQuery.goMap.map);
		var controlPosition = options.enable_cat_filters ? google.maps.ControlPosition.BOTTOM_LEFT : google.maps.ControlPosition.BOTTOM_RIGHT;

		jQuery.goMap.map.controls[controlPosition].push(centerControlDiv);
	}
}



function build_map_ajax_search_param(map_canvas_var,reload_cat_list)
{
	var child_collapse = jQuery('#'+map_canvas_var+'_child_collapse').val() ;
	
	var ptype  = new Array(), search_string   = '' , stype   = ''
	
	var gd_posttype = '';
	var gd_cat_posttype = '';
	
	if(jQuery('#'+map_canvas_var +'_posttype').val() != '' && jQuery('#'+map_canvas_var +'_posttype').val() != '0'){	
		gd_posttype = jQuery('#'+map_canvas_var +'_posttype').val();
		gd_cat_posttype = jQuery('#'+map_canvas_var +'_posttype').val();
		gd_posttype = '&gd_posttype='+gd_posttype;
	}
	
	
		
	if( jQuery('#'+map_canvas_var+'_jason_enabled').val()==1)
	{
	
		parse_marker_jason(eval(map_canvas_var+'_jason_args.' + map_canvas_var + '_jason' ), map_canvas_var)
		return false;	
	}
	
	if(reload_cat_list) // load the category listing in map canvas category list panel 
	{	
		jQuery.get(eval(map_canvas_var).ajax_url,{geodir_ajax:'map_ajax',ajax_action:'homemap_catlist',post_type:gd_cat_posttype, map_canvas:map_canvas_var, child_collapse:child_collapse},function(data){
			
			if(data){
				jQuery('#'+map_canvas_var+'_cat .toggle').html(data);
				//show_category_filter(map_canvas_var);
				geodir_show_sub_cat_collapse_button();
				build_map_ajax_search_param(map_canvas_var,false) ;
				return false;
			}
																																	   		});
		return false;
	}
	
	
	
	
	
	
	search_string = (jQuery('#'+map_canvas_var+'_search_string').val() != eval(map_canvas_var).inputText) ? jQuery('#'+map_canvas_var+'_search_string').val() : '';
	
	
	var location_string = '';
	var hood_string = '';
	
	if(jQuery('#'+map_canvas_var +'_country').val() != ''){
		var $gd_country = jQuery('#'+map_canvas_var +'_country').val();
		location_string = location_string+'&gd_country='+$gd_country;
	}
		
	if(jQuery('#'+map_canvas_var +'_region').val() != ''){
		var $gd_region = jQuery('#'+map_canvas_var +'_region').val();
		location_string = location_string+'&gd_region='+$gd_region;
	}
		
	if(jQuery('#'+map_canvas_var +'_city').val() != ''){	
		var $gd_city = jQuery('#'+map_canvas_var +'_city').val();	
		location_string = location_string+'&gd_city='+$gd_city;
	}
	
	if(jQuery('#'+map_canvas_var +'_neighbourhood').val() != ''){	
		var $gd_neighbourhood = jQuery('#'+map_canvas_var +'_neighbourhood').val();	
		//location_string = location_string+'&gd_neighbourhood='+$gd_neighbourhood;
		hood_string = location_string+'&gd_neighbourhood='+$gd_neighbourhood;
		
	}
	
	
	//loop through available categories
	mapcat = document.getElementsByName( map_canvas_var +"_cat[]");
	
	var checked = "";
	var none_checked = "";
	for(i = 0; i < mapcat.length; i++){
		if(mapcat[i].checked){
			checked += mapcat[i].value+",";
			
		}
		else{
			none_checked += mapcat[i].value+",";
		}
	}
	
	if(checked==""){checked = none_checked;}
	
	var strLen = checked.length;
	checked    = checked.slice(0,strLen-1);
	
	
	var search_query_string = '' ;
	search_query_string = '&geodir_ajax=map_ajax&ajax_action=cat&cat_id='+checked+"&search="+search_string+hood_string
	if(gd_posttype != '')
		search_query_string = search_query_string+gd_posttype;	
	
//	if(location_string != '')
	//	search_query_string = search_query_string+location_string;	
	
	
	map_ajax_search(map_canvas_var , search_query_string, ''); 
}

function geodir_show_sub_cat_collapse_button()
{
	jQuery('ul.main_list li').each(function(i){
		var sub_cat_list = jQuery(this).find('ul.sub_list')	;
		//alert((typeof sub_cat_list.attr('class') ==='undefined')) ;
		if(!(typeof sub_cat_list.attr('class') ==='undefined') )
		{
			
			if(sub_cat_list.is(':visible'))
			{
				jQuery(this).find('i').removeClass('fa-long-arrow-down');
				jQuery(this).find('i').addClass('fa-long-arrow-up');
			}
			else
			{
				jQuery(this).find('i').removeClass('fa-long-arrow-up');
				jQuery(this).find('i').addClass('fa-long-arrow-down');	
			}
			
				
			jQuery(this).find('i').show();/**/
		}
		else
			jQuery(this).find('i').hide();/**/
	})	
	geodir_activate_collapse_pan();
}

function geodir_activate_collapse_pan()
{
	jQuery('ul.main_list').find('i').click(function(){
		jQuery(this)
		.parent('li')
		.find('ul.sub_list')
		.toggle(200 , 
				function(){
								if(jQuery(this).is(':visible'))
								{
									jQuery(this).parent('li').find('i').removeClass('fa-long-arrow-down');
									jQuery(this).parent('li').find('i').addClass('fa-long-arrow-up');
								}
								else
								{
									jQuery(this).parent('li').find('i').removeClass('fa-long-arrow-up');
									jQuery(this).parent('li').find('i').addClass('fa-long-arrow-down');
								}
						   });	
		
	});	
}

function map_ajax_search(map_canvas_var, search_query_string, marker_jason)
{
	
	//document.getElementById( map_canvas_var+'_loading_div').style.display="block";
	jQuery( '#'+map_canvas_var+'_loading_div').show();
	
	if(marker_jason!='')
	{
		parse_marker_jason(marker_jason, map_canvas_var)	
		//document.getElementById( map_canvas+'_loading_div').style.display="none";
		jQuery( '#'+map_canvas+'_loading_div').hide();
		return;
	}
	
	var query_url = eval(map_canvas_var).ajax_url+search_query_string;
	
	jQuery.ajax({
		type: "GET",
		url: query_url,
		success: function(data){
		//	alert(map_canvas) ;
			//document.getElementById( map_canvas_var+'_loading_div').style.display="none";
			jQuery( '#'+map_canvas_var+'_loading_div').hide();
			parse_marker_jason( data, map_canvas_var );
		//	document.dispatchEvent(event_marker_reloaded);
		}
	});
	
	return;
} // End  map_ajax_search

// read the data, create markers
function parse_marker_jason(data, map_canvas_var) {

	var jsonData = '';

	if(jQuery('#'+map_canvas_var).val()==''){// if map not loaded then load it
		initMap(map_canvas_var);
	}	

	jQuery("#"+ map_canvas_var).goMap();
	
	// get the bounds of the map
	bounds = new google.maps.LatLngBounds();
	
	// clear old markers
	jQuery.goMap.clearMarkers(); //deleteMarkers();
	
	//json evaluate returned data
	if ( jQuery("#geodir-main-search").length ) {
		
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

		if ( jQuery("body").hasClass("geodir-category-search") ) {
			
			jQuery.ajax({
				type: 'POST',
				url: my_ajax.ajaxurl,
				data: {"action": "geodir_search_markers", listing_date: jQuery("#geodir-search-date").val(), listing_price: listing_price_val, listing_guests: listing_guests_val, listing_bedrooms: listing_bedrooms_val, listing_beds: listing_beds_val, search_type: 'category', search_category: jQuery("#geodir-search-cateogry").val(), post_type: jQuery('#geodir-search-post-type').val() },
				success: function(response) {
					jsonData = jQuery.parseJSON(response);
					
					if (jsonData[0] == undefined || jsonData[0].totalcount <= 0) {
						document.getElementById( map_canvas_var+'_map_nofound').style.display = 'block';
						var mapcenter = new google.maps.LatLng(eval(map_canvas_var).latitude,eval(map_canvas_var).longitude);

						// Create markers
						list_markers(jsonData,map_canvas_var);

						// Display posts
						jQuery("#geodir-main-search").html(jsonData['all_markers']);
						jQuery("#geodir-main-search").removeClass("loading");

						jQuery.goMap.map.setCenter(eval(map_canvas_var).mapcenter);
						jQuery.goMap.map.setZoom(eval(map_canvas_var).zoom);
					} else {
						document.getElementById(map_canvas_var+'_map_nofound').style.display = 'none';
						var mapcenter = new google.maps.LatLng(eval(map_canvas_var).latitude,eval(map_canvas_var).longitude);

						// Create markers
						list_markers(jsonData,map_canvas_var);

						// Display posts
						jQuery("#geodir-main-search").html(jsonData['all_markers']);
						jQuery(".geodir_category_list_view").isotope({
							transformsEnabled: true,
							getSortData: {
								price: function ( elem ) {
									var element = jQuery(elem).find(".map-listing-price").html();
									if ( element != undefined ) {
										element = element.replace(my_ajax.currency_symbol, "");
									};
									return parseFloat( element );
								},
								rating: function ( elem ) {
									var element = jQuery(elem).find(".listing-item-star.text").html();
									return parseFloat( element );
								}
							},
							sortBy: 'price',
							sortAscending: true,
							animationOptions: {
								duration: 250,
								easing: 'swing',
								queue: true
							},
							animationEngine : "jquery"
						});
						jQuery(".map-listing-carousel-container").jcarousel();
						jQuery("#geodir-main-search").removeClass("loading");

						var center = bounds.getCenter();
						if ( jsonData[0].totalcount < 2 && jsonData[0].totalcount != 1 ) {
							geo_map_movement();
							var newcenter = new google.maps.LatLng(lng,lat);
							jQuery.goMap.map.setCenter(center);
						} else if ( jsonData[0].totalcount == 1 ) {
							jQuery.goMap.map.fitBounds(bounds);
							jQuery.goMap.map.setZoom(15);
						} else {
							jQuery.goMap.map.fitBounds(bounds);
						}

						if ( jQuery.goMap.map.getZoom() > eval(map_canvas_var).maxZoom ){jQuery.goMap.map.setZoom(eval(map_canvas_var).maxZoom);}

						if(eval(map_canvas_var).enable_marker_cluster) {
							if (typeof create_marker_cluster == 'function') { 
								create_marker_cluster(map_canvas_var)
							}
						}
					}
					return false;
				}
			});
		} else {
			var sPageURL = window.location.search.substring(1);
			var sURLVariables = sPageURL.split('&');
			var ajaxParams = '';
			for (var i = 0; i < sURLVariables.length; i++) {
				var sParameterName = sURLVariables[i].split('=');
				if ( ( sParameterName[0].indexOf('geodir_') >= 0 || sParameterName[0].indexOf('event') >= 0 ) && sParameterName[0] != 'geodir_search' ) {
					ajaxParams += ', "'+sParameterName[0]+'": "'+sParameterName[1]+'"';
				}
			}

			var updated_data = '{"action": "geodir_search_markers", "search_lat": "'+vh_getUrlParameter('sgeo_lat')+'", "search_long": "'+vh_getUrlParameter('sgeo_lon')+'", "listing_date": "'+jQuery("#geodir-search-date").val()+'", "listing_price": "'+listing_price_val+'", "listing_guests": "'+listing_guests_val+'", "listing_bedrooms": "'+listing_bedrooms_val+'", "listing_beds": "'+listing_beds_val+'", "search_keyword": "'+jQuery('#geodir-search-keyword').val()+'", "listing_search_cat": "'+jQuery('#geodir-listing-search-category').val()+'", "search_location": "'+jQuery('#geodir-listing-search-location').val()+'", "listing_contract": "'+jQuery('#geodir-search-contract').val()+'", "post_id": "'+jQuery('#geodir-current-page-id').val()+'", "post_type": "'+jQuery('#geodir-search-post-type').val()+'"'+ajaxParams+' }';

			ajaxData = jQuery.parseJSON(updated_data);
			
			jQuery.ajax({
				type: 'POST',
				url: my_ajax.ajaxurl,
				data: ajaxData,
				success: function(response) {
					jsonData = jQuery.parseJSON(response);
				
					if (jsonData[0] == undefined || jsonData[0].totalcount <= 0) {
						document.getElementById( map_canvas_var+'_map_nofound').style.display = 'block';
						var mapcenter = new google.maps.LatLng(eval(map_canvas_var).latitude,eval(map_canvas_var).longitude);
						var href_arr = location.href.split("&");
						var lng = href_arr["4"].split("=")["1"];
						var lat = href_arr["5"].split("=")["1"];

						// Create markers
						list_markers(jsonData,map_canvas_var);

						// Display posts
						jQuery("#geodir-main-search").html(jsonData['all_markers']);
						jQuery("#geodir-main-search").removeClass("loading");

						var newcenter = new google.maps.LatLng(lng,lat);
						jQuery.goMap.map.setCenter(newcenter);
						jQuery.goMap.map.setZoom(eval(map_canvas_var).zoom);
					} else {
						document.getElementById(map_canvas_var+'_map_nofound').style.display = 'none';
						var mapcenter = new google.maps.LatLng(eval(map_canvas_var).latitude,eval(map_canvas_var).longitude);

						if ( !my_ajax.is_location ) {
							var href_arr = location.href.split("&");
							var lng = href_arr["4"].split("=")["1"];
							var lat = href_arr["5"].split("=")["1"];
						};

						// Create markers
						list_markers(jsonData,map_canvas_var);

						// Display posts
						jQuery("#geodir-main-search").html(jsonData['all_markers']);
						jQuery(".geodir_category_list_view").isotope({
							transformsEnabled: true,
							getSortData: {
								price: function ( elem ) {
									var element = jQuery(elem).find(".map-listing-price").html();
									if ( element != undefined ) {
										element = element.replace(my_ajax.currency_symbol, "");
									};
									return parseFloat( element );
								},
								rating: function ( elem ) {
									var element = jQuery(elem).find(".listing-item-star.text").html();
									return parseFloat( element );
								}
							},
							sortBy: 'price',
							sortAscending: true,
							animationOptions: {
								duration: 250,
								easing: 'swing',
								queue: true
							},
							animationEngine : "jquery"
						});
						jQuery(".map-listing-carousel-container").jcarousel();
						if ( jQuery("body").hasClass("admin-bar") ) {
							var height = jQuery(window).height()-70-46-32;
	
							if ( jQuery('.geodir-map-listing-filters').length ) {
								height = height - jQuery('.geodir-map-listing-filters').height();
							}
	
							jQuery("body.geodir-main-search .geodir_category_list_view").css('height', height);
							jQuery("body.geodir-main-search #vh_wrappers").css('height', jQuery(window).height()-32);
						} else {
							var height = jQuery(window).height()-70-46;
	
							if ( jQuery('.geodir-map-listing-filters').length ) {
								height = height - jQuery('.geodir-map-listing-filters').height();
							}
	
							jQuery("body.geodir-main-search .geodir_category_list_view").css('height', height);
							jQuery("body.geodir-main-search #vh_wrappers").css('height', jQuery(window).height());
						};
						jQuery("#geodir-main-search").removeClass("loading");

						var center = bounds.getCenter();
	
						if ( jsonData[0].totalcount < 2 && jsonData[0].totalcount != 1 ) {
							geo_map_movement();
							var newcenter = new google.maps.LatLng(lng,lat);
							jQuery.goMap.map.setCenter(center);
						} else if ( jsonData[0].totalcount == 1 ) {
							jQuery.goMap.map.fitBounds(bounds);
							jQuery.goMap.map.setZoom(15);
							jQuery.goMap.map.setCenter(center);
						} else {
							jQuery.goMap.map.fitBounds(bounds);
						}
						
						// Check if markers fit bounds
						setTimeout(function() {
							jQuery.each(all_markers, function() {
								if ( jQuery.goMap.map.getBounds().contains(jQuery(this)['0'].getPosition()) == false ) {
									jQuery.goMap.map.fitBounds(bounds);
									return;
								};
							})
						}, 500);

						if(eval(map_canvas_var).enable_marker_cluster) {
							if (typeof create_marker_cluster == 'function') { 
								create_marker_cluster(map_canvas_var)
							}
						}
					}
					return false;
				}
			});
		}
	} else if ( jQuery("#main_header_bg").length ) {
		vh_get_header_map(map_canvas_var);
	} else if ( jQuery('.single-listing-sidebar').length ) {
		var post_info = vh_main.post_info;
		if ( !vh_main.is_preview ) {
			jQuery.ajax({
				type: 'POST',
				url: my_ajax.ajaxurl,
				data: {"action": "geodir_search_markers", listing_date: '', search_lat: post_info['0']['post_latitude'], search_long: post_info['0']['post_longitude'], listing_price: '', listing_guests: '', listing_bedrooms: '', listing_beds: '', search_keyword: '', listing_search_cat: '', search_location: post_info['0']['post_city'], listing_contract: '', post_type: vh_main.post_type },
				success: function(response) {
					jsonData = jQuery.parseJSON(response);
					if (jsonData[0] == undefined || jsonData[0].totalcount <= 0) {
						document.getElementById( map_canvas_var+'_map_nofound').style.display = 'block';
						var mapcenter = new google.maps.LatLng(eval(map_canvas_var).latitude,eval(map_canvas_var).longitude);
						list_markers(jsonData, map_canvas_var);
						var newcenter = new google.maps.LatLng(lng,lat);
						jQuery.goMap.map.setCenter(newcenter);
						jQuery.goMap.map.setZoom(eval(map_canvas_var).zoom);
					} else {
						document.getElementById(map_canvas_var+'_map_nofound').style.display = 'none';
						var mapcenter = new google.maps.LatLng(eval(map_canvas_var).latitude,eval(map_canvas_var).longitude);
						list_markers(jsonData,map_canvas_var);
						var center = bounds.getCenter();

						if ( jsonData[0].totalcount < 2 && jsonData[0].totalcount != 1 ) {
							var newcenter = new google.maps.LatLng(lng,lat);
							jQuery.goMap.map.setCenter(center);
							console.log('1');
						} else if ( jsonData[0].totalcount == 1 ) {
							jQuery.goMap.map.fitBounds(bounds);
							jQuery.goMap.map.setZoom(15);
							console.log('2');
						} else {
							jQuery.goMap.map.fitBounds(bounds);
							console.log('3');
						}

						if(eval(map_canvas_var).enable_marker_cluster) {
							if (typeof create_marker_cluster == 'function') { 
								create_marker_cluster(map_canvas_var)
							}
						}
					}
					return false;
				}
			});
		} else {
			google.maps.event.addListenerOnce(jQuery.goMap.map, 'idle', function(){
				document.getElementById( map_canvas_var+'_map_nofound').style.display = 'block';
				jQuery(document.getElementById( map_canvas_var+'_map_nofound')).html('<h3>'+vh_main.maptitle+'</h3><p>'+vh_main.mapcontent+'</p>');
			});
		}
	} else {
		jQuery.ajax({
			type: 'POST',
			url: my_ajax.ajaxurl,
			data: {"action": "geodir_search_markers", listing_date: '', listing_price: '', listing_guests: '', listing_bedrooms: '', listing_beds: '', search_keyword: '', listing_search_cat: '', search_location: '', listing_contract: '' },
			success: function(response) {
				jsonData = jQuery.parseJSON(response);
				if (jsonData[0] == undefined || jsonData[0].totalcount <= 0) {
					document.getElementById( map_canvas_var+'_map_nofound').style.display = 'block';
					var mapcenter = new google.maps.LatLng(eval(map_canvas_var).latitude,eval(map_canvas_var).longitude);
					list_markers(jsonData, map_canvas_var);
					jQuery.goMap.map.setCenter(mapcenter);
					jQuery.goMap.map.setZoom(eval(map_canvas_var).zoom);
				} else {
				
					document.getElementById(map_canvas_var+'_map_nofound').style.display = 'none';
					var mapcenter = new google.maps.LatLng(eval(map_canvas_var).latitude,eval(map_canvas_var).longitude);
					list_markers(jsonData,map_canvas_var);
					var center = bounds.getCenter();

					var geocoder = new google.maps.Geocoder();
					var address = '';
					if ( jQuery.cookie('vh_viewer_location') == null ) {
						address = my_ajax.def_city+', '+my_ajax.def_country;
					} else {
						var location = jQuery.cookie('vh_user_location');
						location = location.split('/');
						address = location['1']+', '+location['0'];
					}

					var geo_map_center = '';

					geocoder.geocode( { 'address': address}, function(results, status) {

						if (status == google.maps.GeocoderStatus.OK) {
							var latitude = results[0].geometry.location.lat();
							var longitude = results[0].geometry.location.lng();
							var latlngbounds = new google.maps.LatLngBounds(
								results[0].geometry.viewport.getSouthWest(), 
								results[0].geometry.viewport.getNorthEast()
							);
						}

						geo_map_center = new google.maps.LatLng(latitude,longitude);

						if ( my_ajax.home_map_autozoom == 'true' ) {
							setTimeout( function() {
								jQuery.goMap.map.fitBounds(latlngbounds);
							}, 200 );
							setTimeout( function() {
								jQuery('.stick_trigger_container').addClass('loading_off');
							}, 300 );
						} else {
							setTimeout( function() {
								jQuery.goMap.map.setZoom(parseInt(my_ajax.home_map_zoom));
								jQuery.goMap.map.setCenter(geo_map_center);
							}, 200 );
							setTimeout( function() {
								jQuery('.stick_trigger_container').addClass('loading_off');
							}, 300 );
						}
					});

					if(eval(map_canvas_var).enable_marker_cluster)
					{
						if (typeof create_marker_cluster == 'function') { 
							create_marker_cluster(map_canvas_var)
						}
					}
				}
				return false;
			}
		});
	}

	if(eval(map_canvas_var).enable_marker_cluster) {
		if (typeof remove_cluster_markers == 'function') { 
			remove_cluster_markers(map_canvas_var) 
		}
	}
	
	jQuery('#' + map_canvas_var + '_loading_div').hide();
}

jQuery(document).on('click', '.header-custom-posts a:not(.active)', function() {
	var map_canvas = jQuery('#main_header_bg div:first-child').attr('id').replace('catcher_', '');
	var post_type = 'gd_'+jQuery(this).text();
	vh_setAllMap(null);
	vh_get_header_map(map_canvas, post_type);
	
});

function gdCustomControl(controlDiv, cat_filters, gdMap) {
	// Set CSS for the control border
	var controlUI = document.createElement('div');
	jQuery(controlUI).addClass('gd-dragg-ui');
	if (cat_filters) {
		jQuery(controlUI).addClass('gd-dragg-with-cat');
	}
	gdMap.setOptions({draggable: false});
	jQuery(controlUI).addClass('gd-drag-inactive');
	controlUI.style.backgroundColor = '#fff';
	controlUI.style.borderRadius = '2px';
	controlUI.style.boxShadow = '0 1px 4px -1px rgba(0, 0, 0, 0.3)';
	controlUI.style.cursor = 'pointer';
	if (cat_filters) {
		controlUI.style.marginBottom = '40px';
	} else {
		controlUI.style.marginBottom = '5px';
	}
	controlUI.style.marginTop = '5px';
	controlUI.style.textAlign = 'center';
	controlDiv.appendChild(controlUI);

	// Set CSS for the control interior
	var controlText = document.createElement('div');
	jQuery(controlText).addClass('gd-dragg-action');
	controlUI.style.border = '1px solid rgba(0, 0, 0, 0.15)';
	controlText.style.color = '#333';
	controlText.style.fontSize = '11px';
	controlText.style.lineHeight = '1.5';
	controlText.style.paddingLeft = '6px';
	controlText.style.paddingTop = '1px';
	controlText.style.paddingBottom = '1px';
	controlText.style.paddingRight = '6px';
	controlText.innerHTML = geodir_all_js_msg.geodir_on_dragging_text;
	controlUI.appendChild(controlText);

	// Setup the click event listeners: simply set the map to
	//
	google.maps.event.addDomListener(controlUI, 'click', function () {
		if (jQuery(this).hasClass('gd-drag-active')) {
			jQuery(this).removeClass('gd-drag-active').addClass('gd-drag-inactive').find('.gd-dragg-action').text(geodir_all_js_msg.geodir_on_dragging_text);
			gdMap.setOptions({draggable: false});
		} else {
			jQuery(this).removeClass('gd-drag-inactive').addClass('gd-drag-active').find('.gd-dragg-action').text(geodir_all_js_msg.geodir_off_dragging_text);
			gdMap.setOptions({draggable: true});
		}
	});
}

function vh_setAllMap(map) {
	var map_markers = window.all_markers;
	for (var i = 0; i < map_markers.length; i++) {
			map_markers[i].setMap(map);
	}
}

function vh_get_header_map(map_canvas_var, m_post_type) {

	m_post_type = typeof m_post_type !== 'undefined' ? m_post_type : my_ajax.default_search;

	jQuery.ajax({
		type: 'POST',
		url: my_ajax.ajaxurl,
		data: {"action": "geodir_search_markers", listing_date: '', listing_price: '', listing_guests: '', listing_bedrooms: '', listing_beds: '', search_keyword: '', listing_search_cat: '', search_location: '', listing_contract: '', post_type: m_post_type },
		success: function(response) {
			jsonData = jQuery.parseJSON(response);
			if (jsonData[0] == undefined || jsonData[0].totalcount <= 0) {
				document.getElementById( map_canvas_var+'_map_nofound').style.display = 'block';
				var mapcenter = new google.maps.LatLng(eval(map_canvas_var).latitude,eval(map_canvas_var).longitude);
				list_markers(jsonData, map_canvas_var);
				jQuery.goMap.map.setCenter(mapcenter);
				jQuery.goMap.map.setZoom(eval(map_canvas_var).zoom);
			} else {
				document.getElementById(map_canvas_var+'_map_nofound').style.display = 'none';
				var mapcenter = new google.maps.LatLng(eval(map_canvas_var).latitude,eval(map_canvas_var).longitude);
				list_markers(jsonData,map_canvas_var);
				var center = bounds.getCenter();

				var geocoder = new google.maps.Geocoder();
				var address = '';

				if ( jQuery.cookie('vh_viewer_location') == null ) {
					address = my_ajax.def_city+', '+my_ajax.def_country;
				} else {
					var location = jQuery.cookie('vh_user_location');
					location = location.split('/');
					address = location['1']+', '+location['0'];
					// address = 'Rome, Italy';
				}

				var geo_map_center = '';

				geocoder.geocode( { 'address': address}, function(results, status) {

					if (status == google.maps.GeocoderStatus.OK) {
						var latitude = results[0].geometry.location.lat();
						var longitude = results[0].geometry.location.lng();
						var latlngbounds = new google.maps.LatLngBounds(
							results[0].geometry.viewport.getSouthWest(), 
							results[0].geometry.viewport.getNorthEast()
						);
					}

					geo_map_center = new google.maps.LatLng(latitude,longitude);

					if ( my_ajax.home_map_autozoom == 'true' ) {
						setTimeout( function() {
							jQuery.goMap.map.fitBounds(latlngbounds);
						}, 1500);
						setTimeout( function() {
							jQuery('.stick_trigger_container').addClass('loading_off');
						}, 1500 );
					} else {
						setTimeout( function() {
							jQuery.goMap.map.setZoom(parseInt(my_ajax.home_map_zoom));
							jQuery.goMap.map.setCenter(geo_map_center);
						}, 1500);
						setTimeout( function() {
							jQuery('.stick_trigger_container').addClass('loading_off');
						}, 1500 );
					}
				});

				if(eval(map_canvas_var).enable_marker_cluster) {
					if (typeof create_marker_cluster == 'function') {
						create_marker_cluster(map_canvas_var)
					}
				}
			}
			return false;
		}
	});
}

var marker_clicked = false;
var visible_markers = [];
var all_markers = [];
var southWest = northEast = map_bounds = '';

function geo_map_movement() {
	total_idle = 0;

	var listing_price = listing_guests = listing_bedrooms = listing_beds = '';
	var currently_visible = '';
	

	jQuery.each( visible_markers, function( i, val ) {
		if ( my_ajax.map_markers == 'true' ) {
			var marker = val.split('_');
			currently_visible = currently_visible+marker["0"]+",";
		} else {
			var marker = val.split('"');
			currently_visible = currently_visible+marker["1"].split("-")["2"]+",";
		}

	});

	var all_posts = currently_visible.slice(0, -1).split(',');

	// Show only visible posts
	jQuery('.geodir_category_list_view li').each(function() {
		if ( typeof jQuery(this).attr('id') == 'string' ) {
			var post_id = jQuery(this).attr('id').split('-');
			post_id = post_id['1'];
			if ( currently_visible.indexOf(post_id) > -1 ) {
				jQuery(this).show();
			}
			/*jino map remove to show*/
			else 
			{
				jQuery(this).hide();
			}
			
		};
	});

	// Reorder items
	jQuery(".geodir_category_list_view").isotope('layout');

	// Update propery count
	jQuery('.geodir-map-listing-top .property-count').html(all_posts.length);

	jQuery.cookie('vh_currently_visible', currently_visible, { path: '/' });

	visible_markers = [];
	total_idle++;
}
	
function list_markers(input, map_canvas_var) {

	var totalcount = input[0].totalcount;
	var all_markers_value = input['all_markers'];

	if ( typeof all_markers_value == 'string' ) {
		var marker_count = Object.keys(input).length-1;
	} else {
		var marker_count = input.length;
	}
	
	if(totalcount > 0){
		for (var i = 0; i < marker_count; i++) {
			var marker = create_marker(input[i],map_canvas_var);
		}

		google.maps.event.addListener(jQuery.goMap.map, 'click', function() {
			if ( !marker_clicked ) {
				gd_infowindow.close(jQuery.goMap.map, marker);
			}
			marker_clicked = false;
		});

		var total_idle;

		google.maps.event.addListener(jQuery.goMap.map, 'dragend', function() {
			if ( !jQuery("body").hasClass("geodir-fullmap") && jQuery('body').hasClass('geodir-main-search') ) {
				geo_map_movement();
			};
		});

		google.maps.event.addListener(jQuery.goMap.map, 'zoom_changed', function() {
			if ( !jQuery("body").hasClass("geodir-fullmap") && jQuery('body').hasClass('geodir-main-search') ) {
				geo_map_movement();
			};
		});
	}

}	

function geodir_htmlEscape(str) {
	return String(str)
			.replace(/&prime;/g,"'")
			.replace(/&frasl;/g, '/')
			.replace(/&ndash;/g, '-')
			.replace(/&ldquo;/g, '"')
			.replace(/&gt;/g, '>');
}
gd_single_marker_lat = '';	
gd_single_marker_lon = '';	
// create the marker and set up the event window
function create_marker(input,map_canvas_var ) 
{
	gd_single_marker_lat = input.lt;
	gd_single_marker_lon = input.ln;
	jQuery("#"+ map_canvas_var).goMap();
	var extra_class = "";
	var marker_click = false;
	
	if(input.lt && input.ln){
		var coord = new google.maps.LatLng(input.lt,input.ln);
		var marker_id = 0 ;
		if(eval(map_canvas_var).enable_cat_filters)
			marker_id = input.mk_id
		else
			marker_id = input.id
			
			
		var title = geodir_htmlEscape(input.t);
		
		//if(!input.i){return;}
		if(!input.i){input.i = geodir_all_js_msg.geodir_default_marker_icon;}

		if ( input.id == vh_main.post_id && jQuery("#geodir-main-search").length != 1 ) {
			extra_class = " class=\"active\"";
		};

		if ( my_ajax.map_markers == 'true' ) {
			var marker_cat_id = input.mk_id.split('_');
			var marker_icon = my_ajax.marker_icon[marker_cat_id['1']];
			if ( typeof(marker_icon) === 'undefined' ) {
				marker_icon = my_ajax.marker_def_icon;
			};
			var marker  = jQuery.goMap.createMarker({
					id: marker_id,
					title: title,
					position: coord,
					visible: true,
					clickable: true,
					icon: marker_icon
				});
		} else {
			var marker = new RichMarker({
					position: coord,
					map: jQuery.goMap.map,
					draggable: false,
					flat: true,
					content: '<div id="geo-marker-'+input.id+'"'+extra_class+'><img src="'+input.i+'"/></div>'
				});
		}

		all_markers.push(marker);

		bounds.extend(coord);

		// Adding a click event to the marker
		google.maps.event.addListener(marker, 'click', function() {
			
			jQuery("#"+ map_canvas_var).goMap();											
			
			var preview_query_str = '';
			marker_click = true;
			marker_clicked = false;
			if(input.post_preview){
				preview_query_str = '&post_preview='+input.post_preview;
			}
			
			if(eval(map_canvas_var).bubble_size){
				var marker_url = eval(map_canvas_var).ajax_url+"&geodir_ajax=map_ajax&ajax_action=info&m_id="+input.id+"&small=1"+preview_query_str; 								            
			}else{
				var marker_url = eval(map_canvas_var).ajax_url+"&geodir_ajax=map_ajax&ajax_action=info&m_id="+input.id+preview_query_str;
			}
			
			var  loading = '<div id="map_loading"></div>';
			gd_infowindow.open(jQuery.goMap.map, marker);
			gd_infowindow.setContent(loading);

			google.maps.event.addListener(gd_infowindow,'domready',function(){
				// if ( jQuery("#main_header_bg").length ) {
				// 	jQuery(".gm-style-iw").parent().css('top', parseInt(jQuery(".gm-style-iw").parent().css('top').replace('px', ''))+70);
				// };

				jQuery(".gm-style-iw").parent().find("div").first().find("div:nth-child(1)").addClass("bubble-div-1");
				jQuery(".gm-style-iw").parent().find("div").first().find("div:nth-child(2)").addClass("bubble-div-2");
				jQuery(".gm-style-iw").parent().find("div").first().find("div:nth-child(3)").addClass("bubble-div-3");
				jQuery(".gm-style-iw").parent().find("div").first().find("div:nth-child(4)").addClass("bubble-div-4");
				jQuery(".gm-style-iw").next().addClass("bubble-div-close");
				jQuery(".geodir-bubble_desc").hover(function() {
					jQuery(this).addClass("animation");
					jQuery(this).removeClass("reverse-animation");
				},function() {
					jQuery(this).removeClass("animation");
					jQuery(this).addClass("reverse-animation");
				});

				jQuery(".map-listing-next, .map-listing-prev").hover(function() {
					jQuery(".geodir-bubble_desc > a.wpb_button").hide();
				},function() {
					jQuery(".geodir-bubble_desc > a.wpb_button").show();
				});

				jQuery(".geodir-addtofav").hover(function() {
					jQuery(this).find(".fa-heart").addClass("icon-heart-1");
				}, function() {
					jQuery(this).find(".fa-heart").removeClass("icon-heart-1");
				});

				jQuery(".geodir-removetofav-icon").hover(function() {
					jQuery(this).find(".fa-heart").addClass("remove icon-heart-1");
				}, function() {
					jQuery(this).find(".fa-heart").removeClass("remove icon-heart-1");
				});

				if ( jQuery(".geodir-bubble_image .map-listing-carousel-container").length ) {
					jQuery(".map-listing-carousel-container").jcarousel({wrap: "circular"});
				};
			});
		
			jQuery.ajax({
				type: "GET",
				url: marker_url,
				cache: false,
				dataType: "html",
				error: function(xhr, error){
						alert(error);
				},
				success: function(response){
					jQuery("#"+ map_canvas_var).goMap();
					gd_infowindow.setContent(response);
					gd_infowindow.open(jQuery.goMap.map, marker);
				}
			});

			if ( marker_click ) {
				// gd_infowindow.close(jQuery.goMap.map, marker);
				marker_clicked = true;
			}
			marker_click = false;
			
			return;
			
		});

		// Adding a visible_changed event to the marker
		google.maps.event.addListener(marker, 'visible_changed', function(){
			gd_infowindow.close(jQuery.goMap.map, marker);
		});

		google.maps.event.addListener(jQuery.goMap.map, 'dragend', function() {
			var map = jQuery.goMap.map;
			if( jQuery.goMap.map.getBounds().contains(marker.getPosition() ) ){
				if ( my_ajax.map_markers == 'true' ) {
					visible_markers.push(marker['id']);
				} else {
					visible_markers.push(marker['content']);
				}
			}
		});

		google.maps.event.addListener(jQuery.goMap.map, 'zoom_changed', function() {
			var map = jQuery.goMap.map;
			if( jQuery.goMap.map.getBounds().contains(marker.getPosition() ) ){
				if ( my_ajax.map_markers == 'true' ) {
					visible_markers.push(marker['id']);
				} else {
					visible_markers.push(marker['content']);
				}
			}
		});
	
		return true;
	}else{
		//no lat & long, return no marker
		return false;
	}
}

function openMarker(map_canvas ,id){
	jQuery("#"+ map_canvas).goMap();
	//for (var i = 0, l = jQuery.goMap.markers.length; i < l; i++) {
	//	alert(jQuery.goMap.markers[i])
	//}
	google.maps.event.trigger(jQuery.goMap.mapId.data(id), 'click'); 
}

function animate_marker(map_canvas ,id){
		jQuery("#"+ map_canvas).goMap();
		//alert(jQuery.goMap.mapId.data(id))
		jQuery.goMap.mapId.data(id).setAnimation(google.maps.Animation.BOUNCE);
	}
	
function stop_marker_animation(map_canvas ,id){
	jQuery("#"+ map_canvas).goMap();
	
	if (jQuery.goMap.mapId.data(id).getAnimation() != null) {
		jQuery.goMap.mapId.data(id).setAnimation(null);
	}
}

// Listing map sticky script //

function getCookie(cname)
{
var name = cname + "=";
var ca = document.cookie.split(';');
for(var i=0; i<ca.length; i++)
  {
	 var c = ca[i].trim();
	if (c.indexOf(name)==0) return c.substring(name.length,c.length);
  }
return "";
} 

function setCookie(cname,cvalue,exdays)
{
	var d = new Date();
	d.setTime(d.getTime()+(exdays*24*60*60*1000));
	var expires = "expires="+d.toGMTString();
	document.cookie = cname + "=" + cvalue + "; " + expires+";Path=/";
} 

	
function map_sticky(map_options) {
	var optionsname = map_options;
	map_options = eval(map_options) ;

	if(map_options.sticky && jQuery(window).width()>1250){
	
		jQuery.fn.scrollBottom = function() { 
			return this.scrollTop() + this.height(); 
		};			
	
		//var content = jQuery("#geodir_wrapper").closest('div').scrollBottom();
		//var content = jQuery("#geodir-main-content").closest('div').scrollBottom();
		var content = jQuery(".geodir-sidebar-wrap").scrollBottom();
		
		var stickymap = jQuery("#sticky_map_"+optionsname+"").scrollBottom();
		var catcher = jQuery('#catcher_'+optionsname+'');
		var sticky = jQuery('#sticky_map_'+optionsname+'');		
		var sticky_show_hide_trigger = sticky.closest('.stick_trigger_container').find('.trigger_sticky');
		var mapheight = jQuery("#sticky_map_"+optionsname+"").height();
		//alert(mapheight)
		jQuery(window).scroll(function() {
			
			jQuery("#"+optionsname+"").goMap();
			
			// get the bounds of the map
			bounds = new google.maps.LatLngBounds();
			
			var wheight = jQuery(window).height();
			
			
				//alert(catcher.offset().top);
			//sticky.css({'min-width':'300px'});
			//alert(content);
			//alert(stickymap);
			//if(content > stickymap ) {alert(1);}
			//if(jQuery(window).scrollTop() >= catcher.offset().top ) {alert(2);}
				
			if(jQuery(window).scrollTop() >= catcher.offset().top && content > stickymap ) {
				if(!sticky.hasClass('stickymap')){ 	
					sticky.addClass('stickymap');
					sticky.hide();
					sticky.css({'position':'fixed','right':'0','border':'1px solid red'});
					//sticky.css({'top':'25%','width':'25%'});
					sticky.css({'top':'25%'});
					catcher.css({'height' :mapheight }) ;
					var cstatus = getCookie('geodir_stickystatus');
					if(cstatus!='shide'){
						sticky.show('slow');
						
						sticky_show_hide_trigger.removeClass('triggeron_sticky');
						sticky_show_hide_trigger.addClass('triggeroff_sticky');
					}else{
						sticky_show_hide_trigger.removeClass('triggeroff_sticky');
						sticky_show_hide_trigger.addClass('triggeron_sticky');
					}
					
					
					
				}	
				
				sticky_show_hide_trigger.css({'top':'25%','width':'1%','padding-right':'3px','padding-left':'0px'});
				sticky_show_hide_trigger.css({'position':'fixed','right':'0'});
				
				sticky_show_hide_trigger.show();
				
			}
			
			if(jQuery(window).scrollTop() < catcher.offset().top){	
				if(sticky.hasClass('stickymap')){ 	
					sticky.hide();
					sticky.removeClass('stickymap');
					sticky.css({'position':'relative','border':'none'});
					sticky.css({'top':'0','width':'auto'});
					sticky.fadeIn('slow');
					catcher.css({'height' :'0' }) ;
					sticky_show_hide_trigger.removeClass('triggeroff_sticky');
					sticky_show_hide_trigger.addClass('triggeron_sticky');
				}
				
				
				sticky_show_hide_trigger.hide();
			}
			
		});
	
		jQuery(window).resize(function(){jQuery(window).scroll();});

	} // sticky if end
	
	// bind a click event on listing_map_show_hide_all_markers 'Click here to see all other markers.'
	// first check if this div exists or not.
	/*if (jQuery('#listing_map_show_hide_all_markers') != null ) 
	{
		jQuery('#listing_map_show_hide_all_markers a').hide();
		jQuery('#listing_map_show_hide_all_markers a').click(function() {
			show_all_markers();
			jQuery(this).hide();
		});
	   // alert('Found with Not Null');
	}*/
	
}



		var rendererOptions = {draggable: true };
		var directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
		var directionsService = new google.maps.DirectionsService();
function calcRoute(map_canvas){
		 
		initMap(map_canvas);
		 
		var optionsname = map_canvas;
		var map_options = eval(optionsname);
		
		// Direction map
		
		directionsDisplay.setMap(jQuery.goMap.map);
		directionsDisplay.setPanel(document.getElementById(map_canvas+"_directionsPanel"));	
		
		google.maps.event.addListener(directionsDisplay, 'directions_changed', function() {
			computeTotalDistance(directionsDisplay.directions, map_canvas);
		});
		
		jQuery('#directions-options').show();
		
		var from_address = document.getElementById(map_canvas+'_fromAddress').value;
		
		var request = {
			origin: from_address,
			destination: gd_single_marker_lat +','+ gd_single_marker_lon,
			travelMode: gdGetTravelMode(),
			unitSystem: gdGetTravelUnits()
		};

		directionsService.route(request, function(response, status) {
			if (status == google.maps.DirectionsStatus.OK) {
				directionsDisplay.setDirections(response);
				
				//map = new google.maps.Map(document.getElementById(map_canvas), map_options);
				//directionsDisplay.setMap(map);
				
			}else {alert(geodir_all_js_msg.address_not_found_on_map_msg + from_address);}
		});
	
}




function gdGetTravelMode(){
  var mode = jQuery('#travel-mode').val();
	if (mode=='driving'){return google.maps.DirectionsTravelMode.DRIVING;}
	else if (mode=='walking') {return google.maps.DirectionsTravelMode.WALKING;}
	else if(mode=='bicycling') {return google.maps.DirectionsTravelMode.BICYCLING;} 
	else if (mode=='transit') {return google.maps.DirectionsTravelMode.TRANSIT;}
	else{return google.maps.DirectionsTravelMode.DRIVING;}
  }
  
  function gdGetTravelUnits(){
  var mode = jQuery('#travel-units').val();
	if (mode=='kilometers'){return google.maps.DirectionsUnitSystem.METRIC;}
	else{return google.maps.DirectionsUnitSystem.IMPERIAL;}
  }

function computeTotalDistance(result, map_canvas) {
		var total = 0;
		var myroute = result.routes[0];
		for (i = 0; i < myroute.legs.length; i++) {
			total += myroute.legs[i].distance.value;
		}
		totalk = total / 1000
		totalk_round = Math.round(totalk * 100)/100
		totalm = total / 1609.344
		totalm_round = Math.round(totalm * 100)/100
		//document.getElementById(map_canvas+"_directionsPanel").innerHTML = "<p>Total Distance: <span id='totalk'>" + totalk_round + " km</span></p><p>Total Distance: <span id='totalm'>" + totalm_round + " miles</span></p>";
	} 

jQuery(function($){
	setGeodirMapSize(false);
	$(window).resize(function() {
		setGeodirMapSize(true);
	});
})
function setGeodirMapSize(resize) {
	var isAndroid = navigator.userAgent.toLowerCase().indexOf("android")>-1 ? true : false;
	var dW = parseInt(jQuery(window).width());
	var dH = parseInt(jQuery(window).height());
	if(GeodirIsiPhone() || ( isAndroid && (((dW>dH && dW==640 && dH==360) || (dH>dW && dW==360 && dH==640)) || ((dW>dH && dW==533 && dH==320) || (dH>dW && dW==320 && dH==533)) || ((dW>dH && dW==960 && dH==540) || (dH>dW && dW==540 && dH==960))))) {
		jQuery(document).find('.geodir_map_container').each(function(){
			jQuery(this).addClass('geodir-map-iphone');
		});
	}
	else {
		jQuery(document).find('.geodir_map_container').each(function(){
			var $this = this;
			var gmcW = parseInt(jQuery($this).width());
			var gmcH = parseInt(jQuery($this).height());
			if (gmcW>=400 && gmcH>=350) {
				jQuery($this).removeClass('geodir-map-small').addClass('geodir-map-full');
			} else {
				jQuery($this).removeClass('geodir-map-full').addClass('geodir-map-small');
			}
		});
		if (resize) {
			jQuery(document).find('.geodir_map_container_fullscreen').each(function(){
				var $this = this;
				var gmcW = parseInt(jQuery(this).find('.gm-style').width());
				var gmcH = parseInt(jQuery(this).find('.gm-style').height());
				if (gmcW>=400 && gmcH>=370) {
					jQuery($this).removeClass('geodir-map-small').addClass('geodir-map-full');
				} else {
					jQuery($this).removeClass('geodir-map-full').addClass('geodir-map-small');
				}
			});
		}
	}
}

function GeodirIsiPhone(){
	if ((navigator.userAgent.toLowerCase().indexOf("iphone")>-1) || (navigator.userAgent.toLowerCase().indexOf("ipod")>-1) || (navigator.userAgent.toLowerCase().indexOf("ipad")>-1)) {
		return true;
	} else {
		return false;
	}
}