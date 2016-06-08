// On window load. This waits until images have loaded which is essential
/*global jQuery:false, my_ajax:false, on_resize:false */
/*jshint unused:false */
jQuery(window).load(function() {
	"use strict";

	jQuery('.recent-activities-container').isotope();

	function first_geodir_map_init() {
		var content_width = jQuery("#geodir-wrapper").outerWidth();
		var browser_width = jQuery(window).width();
		if (jQuery("body").hasClass("admin-bar")) {
			var browser_height = jQuery(window).height() - 102;
		}
		else {
			var browser_height = jQuery(window).height() - 70;
		}
		var sidebar_width = jQuery(".geodir-map-left").outerWidth();
		if (jQuery("body").hasClass("single-geodir-page")) {
			jQuery(".stick_trigger_container").css({
				"width": ((jQuery(window).width() - 1200) / 2) + jQuery('.single-listing-sidebar').width() + 57,
				"height": "425px"
			});
			jQuery(".geodir_marker_cluster").css({
				"width": ((jQuery(window).width() - 1200) / 2) + jQuery('.single-listing-sidebar').width() + 57,
				"height": "425px",
				"left": "0",
				"position": "relative"
			});
			jQuery(".geodir_map_container, #home_map_canvas_wrapper, #geodir_map_v3_home_map_100_wrapper, #geodir_map_v3_home_map_101_wrapper, #geodir_map_v3_home_map_102_wrapper, #home_map_canvas_loading_div").css({
				"width": browser_width - content_width,
				"height": "425px"
			});
		}
		else if (jQuery("body").hasClass("page-template-template-frontpage")) {
			if (jQuery(window).height() < 900 && jQuery('body').hasClass('header_search_map')) {
				if (jQuery("body").hasClass("admin-bar")) {
					var map_height = jQuery(window).height() - 162;
				}
				else {
					var map_height = jQuery(window).height() - 130;
				};
				jQuery('#main_header_bg, #main_header_image').height(map_height);
				jQuery('body.page-template-template-frontpage-php .wrapper .main .page-wrapper').css('margin-top', (jQuery(window).height() - 130) + 83 + 'px');

				jQuery(".stick_trigger_container").css({
					"width": jQuery(window).width(),
					"height": map_height
				});
				jQuery(".geodir_marker_cluster").css({
					"width": jQuery(window).width(),
					"height": map_height,
					"left": "0",
					"position": "relative"
				});
				jQuery(".geodir_map_container, #home_map_canvas_wrapper, #geodir_map_v3_home_map_100_wrapper, #geodir_map_v3_home_map_101_wrapper, #geodir_map_v3_home_map_102_wrapper, #home_map_canvas_loading_div").css({
					"width": jQuery(window).width(),
					"height": map_height
				});
			}
			else {
				jQuery(".stick_trigger_container").css({
					"width": jQuery(window).width(),
					"height": "747px"
				});
				jQuery(".geodir_marker_cluster").css({
					"width": jQuery(window).width(),
					"height": "747px",
					"left": "0",
					"position": "relative"
				});
				jQuery(".geodir_map_container, #home_map_canvas_wrapper, #geodir_map_v3_home_map_100_wrapper, #geodir_map_v3_home_map_101_wrapper, #geodir_map_v3_home_map_102_wrapper, #home_map_canvas_loading_div").css({
					"width": jQuery(window).width(),
					"height": "747px"
				});
			}
		}
		else {
			jQuery(".stick_trigger_container").css({
				"width": browser_width - sidebar_width + "px",
				"height": browser_height + "px"
			});
			jQuery(".geodir_marker_cluster").css({
				"width": browser_width - sidebar_width,
				"height": browser_height + "px",
				"left": "0",
				"position": "relative"
			});
			jQuery(".geodir_map_container, #home_map_canvas_wrapper, #geodir_map_v3_home_map_100_wrapper, #geodir_map_v3_home_map_101_wrapper, #geodir_map_v3_home_map_102_wrapper, #home_map_canvas_loading_div").css({
				"width": browser_width - sidebar_width,
				"height": browser_height + "px"
			});
		}

		if (typeof jQuery.goMap.map == Object) {
			// Resize map
			google.maps.event.trigger(jQuery.goMap.map, "resize");

			// Fit bounds
			// var bounds = new google.maps.LatLngBounds();
			// jQuery.goMap.map.fitBounds(bounds);
		};
	}

	if (jQuery("body").hasClass("search-results") || jQuery("body").hasClass("single-geodir-page") || jQuery("body").hasClass("search-no-results") || jQuery("body").hasClass("geodir-category-search") || jQuery("body").hasClass("page-template-template-frontpage") || jQuery("body").hasClass("geodir-main-search")) {
		first_geodir_map_init();
	}

	jQuery(".map-listing-carousel-container").jcarousel({
		wrap: "circular"
	});

	if (jQuery('.featured-properties-inner-carousel-container').length) {
		jQuery('.featured-properties-inner-carousel-container').jcarousel({
			wrap: "circular",
			animation: {
				duration: 0
			}
		});

		jQuery('.featured-carousel-controls .featured-carousel-next').click(function() {
			jQuery(this).parent().parent().find('.featured-properties-inner-carousel-container').jcarousel('scroll', '+=1');
			jQuery(this).parent().parent().find('.featured-properties-inner-carousel-container').css('opacity', '0').stop().animate({
				opacity: 1
			}, 700);
		});

		jQuery('.featured-carousel-controls .featured-carousel-prev').click(function() {
			jQuery(this).parent().parent().find('.featured-properties-inner-carousel-container').jcarousel('scroll', '-=1');
			jQuery(this).parent().parent().find('.featured-properties-inner-carousel-container').css('opacity', '0').stop().animate({
				opacity: 1
			}, 700);
		});
	};
});

/*
Plugin: jQuery Parallax
Version 1.1.3
Author: Ian Lunn
Twitter: @IanLunn
Author URL: http://www.ianlunn.co.uk/
Plugin URL: http://www.ianlunn.co.uk/plugins/jquery-parallax/

Dual licensed under the MIT and GPL licenses:
http://www.opensource.org/licenses/mit-license.php
http://www.gnu.org/licenses/gpl.html
*/

jQuery(document).ajaxStop(function() {
	if (jQuery(".blog-row").length) {
		if (jQuery(".blog-row").last().html().length == 71) {
			jQuery(".blog-row").last().hide();
		};
		if (jQuery("#blog_show_load_more").val() == 'false') {
			jQuery("#load_blog_posts").hide();
		};

		if (jQuery(window).width() > 1200) {
			jQuery(".featured-properties-row, .blog-carousel .blog-row").css({
				"max-width": '1170px',
				"min-width": '1170px',
				"width": '1170px'
			});
			jQuery('.featured-properties-row .featured-properties-inner-carousel img').css('width', 'auto');
		}
		else if (jQuery(window).width() < 1200) {
			jQuery(".featured-properties-row, .blog-carousel .blog-row").css({
				"max-width": jQuery(window).width() - 30,
				"min-width": jQuery(window).width() - 30,
				"width": jQuery(window).width() - 30
			});
			var image_width = jQuery('.featured-properties-row .featured-properties-inner-carousel-container').outerWidth();
			jQuery('.featured-properties-row .featured-properties-inner-carousel img').css('width', image_width);
			jQuery('.featured-properties-row .featured-properties-container .featured-properties-inner-carousel img').css('width', parseInt(image_width + (image_width * 0.25)));
			jQuery('.featured-properties-row .featured-properties-container.wide .featured-properties-inner-carousel img').css('width', parseInt(image_width + (image_width * 1.5)));
			jQuery('.featured-properties-inner-carousel-container').jcarousel('reload', {
				'animation': 'fast'
			});
			jQuery('.featured-properties-inner-carousel-container').jcarousel('scroll', '0');
		}
		else {
			jQuery(".featured-properties-row, .blog-carousel .blog-row").css({
				"max-width": "1170px",
				"min-width": "1170px",
				"width": jQuery(window).width() - 30
			});
		}
	};

	function vh_removeElements(text, selector) {
		var wrapped = jQuery("<div>" + text + "</div>");
		wrapped.find(selector).remove();
		return wrapped.html();
	}

	jQuery('.ac_results li').on('click', function() {
		var result = vh_removeElements(jQuery(this).html(), 'small');
		result = vh_removeElements(result, 'span');
		jQuery('#header-location').val(result);
	});
});

jQuery(document).ready(function($) {
	"use strict";

	if (jQuery('#buy-now-ribbon').length && window.self === window.top) {
		jQuery('#buy-now-ribbon').show();
	};

	if (jQuery("body").hasClass("single-geodir-page")) {
		var content_width = jQuery("#geodir-wrapper").outerWidth();
		var browser_width = jQuery(window).width();
		if (jQuery("body").hasClass("admin-bar")) {
			var browser_height = jQuery(window).height() - 102;
		}
		else {
			var browser_height = jQuery(window).height() - 70;
		}
		jQuery(".stick_trigger_container").css({
			"width": ((jQuery(window).width() - 1200) / 2) + jQuery('.single-listing-sidebar').width() + 57,
			"height": "425px"
		});
		jQuery(".geodir_marker_cluster").css({
			"width": ((jQuery(window).width() - 1200) / 2) + jQuery('.single-listing-sidebar').width() + 57,
			"height": "425px",
			"left": "0",
			"position": "relative"
		});
		jQuery(".geodir_map_container, #home_map_canvas_wrapper, #geodir_map_v3_home_map_100_wrapper, #geodir_map_v3_home_map_101_wrapper, #geodir_map_v3_home_map_102_wrapper, #home_map_canvas_loading_div").css({
			"width": browser_width - content_width,
			"height": "425px"
		});
	}

	var $select_file_button = '';

	jQuery('body').on('click', function() {
		if ($select_file_button != '') {
			if (jQuery('#post_images').val() != '') {
				jQuery('#post_imagesdropbox .input-required').css('background-color', 'rgb(51, 153, 51)');
			}
			else {
				jQuery('#post_imagesdropbox .input-required').css('background-color', 'rgb(255, 102, 0)');
			}
		};
	});

	jQuery('.chosen-results li.active-result').live('click', function() {
		if (jQuery(this).parent().parent().parent().parent().parent().hasClass('geodir_taxonomy_field')) {
			jQuery(this).parent().parent().parent().parent().parent().parent().find('.input-required').css('background-color', 'rgb(51, 153, 51)');
		}
		else {
			jQuery(this).parent().parent().parent().parent().find('.input-required').css('background-color', 'rgb(51, 153, 51)');
		}
	});

	// Perform AJAX login on form submit
	jQuery('form#login').on('submit', function(e) {
		jQuery('form#login p.status').show().text(ajax_login_object.loadingmessage);
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_login_object.ajaxurl,
			data: {
				'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
				'username': jQuery('form#login #username').val(),
				'password': jQuery('form#login #password').val(),
				'rememberme': jQuery('form#login #rememberme').val(),
				'security': jQuery('form#login #security').val()
			},
			success: function(data) {
				if (data.for_input == "username") {
					jQuery('form#login .username-error').show().text(data.message);
					jQuery('form#login .header-form-input').removeClass("error");
					jQuery('form#login .username-error').parent().addClass("error");
				}
				else if (data.for_input == "password") {
					jQuery('form#login .password-error').show().text(data.message);
					jQuery('form#login .header-form-input').removeClass("error");
					jQuery('form#login .password-error').parent().addClass("error");
				}

				if (data.loggedin == true) {
					document.location.href = ajax_login_object.redirecturl;
				}
			}
		});
		e.preventDefault();
	});

	// Perform AJAX register on form submit
	jQuery('form#register').on('submit', function(e) {
		jQuery('form#register .status').show().text(ajax_login_object.loadingmessage);
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_login_object.ajaxurl,
			data: {
				'action': 'ajax_register',
				'email': jQuery('form#register #user_email').val(),
				'fullname': jQuery('form#register #fullname').val(),
				'regsecurity': jQuery('form#register #regsecurity').val()
			},
			success: function(data) {
				if (data.for_input == "username") {
					jQuery('form#register .fullname-error').show().text(data.message);
					jQuery('form#register .status').text(ajax_login_object.registermessage);
					jQuery('form#register .email-error').hide();
					jQuery('form#register .header-form-input').removeClass("error");
					jQuery('form#register .fullname-error').parent().addClass("error");
				}
				else if (data.for_input == "user_email") {
					jQuery('form#register .email-error').show().text(data.message);
					jQuery('form#register .status').text(ajax_login_object.registermessage);
					jQuery('form#register .fullname-error').hide();
					jQuery('form#register .header-form-input').removeClass("error");
					jQuery('form#register .email-error').parent().addClass("error");
				}
				else if (data.for_input == "main") {
					jQuery('form#register .status').show().text(data.message);
					jQuery('form#register .email-error').hide();
					jQuery('form#register .fullname-error').hide();
					jQuery('.header-form-input input[type="text"]').val('');
				}
				// if (data.loggedin == true) {
				// 	document.location.href = ajax_login_object.redirecturl;
				// }
			}
		});
		e.preventDefault();
	});

	jQuery(".header-login-button:not(.dashboard)").click(function() {
		jQuery(".header-login-main").fadeIn(300);
		jQuery(".header-login-button").fadeOut(150);
	});

	jQuery("#rememberme_check, .rememberme_checkbox").click(function() {
		if (jQuery(".rememberme_checkbox").hasClass("checked")) {
			jQuery("#rememberme").val("0");
		}
		else {
			jQuery("#rememberme").val("1");
		}
		jQuery(".rememberme_checkbox").toggleClass("checked icon-ok");

	});

	var $isotope_container = jQuery(".blog .wpb_thumbnails");

	$isotope_container.isotope({
		straightAcross: true
	});

	// update columnWidth on window resize
	jQuery(window).bind("debouncedresize", function() {
		$isotope_container.isotope({

			// update columnWidth to a percentage of container width
			masonry: {
				columnWidth: $isotope_container.width() / 2
			}
		});
	});

	jQuery(".scroll-to-top").click(function() {
		jQuery("html, body").animate({
			scrollTop: 0
		}, "slow");
		return false;
	});

	jQuery('.package_button').click(function(e) {
		e.preventDefault();
		jQuery(this).parent().parent().find('input:radio').prop('checked', true);
		jQuery('#job_package_selection').submit();
	});

	function geodir_get_popup_forms(e, ele, clk_class, popup_id) {
		var ajax_url = geodir_var.geodir_ajax_url;
		var append_class = ele.parent();

		var post_id = append_class.find('input[name="geodir_popup_post_id"]').val();

		jQuery.gdmodal('<div id="basic-modal-content" class="clearfix simplemodal-data" style="display: block;"><div class="geodir-modal-loading"><i class="fa fa-refresh fa-spin "></i></div></div>'); // show popup right away
		jQuery.post(ajax_url, {
				popuptype: clk_class,
				post_id: post_id
			})
			.done(function(data) {

				append_class.find('.geodir_display_popup_forms').append(data);
				e.preventDefault();
				jQuery.gdmodal.close(); // close popup and show new one with new data, will be so fast user will not see it
				jQuery('#' + popup_id).gdmodal({
					persist: true,
					//overlayClose:true,
					onClose: function() {
						jQuery.gdmodal.close({
							overlayClose: true
						});
						append_class.find('.geodir_display_popup_forms').html('');
					},
				});
			});
	}

	jQuery('a.vh_b_send_inquiry').click(function(e) {
		geodir_get_popup_forms(e, jQuery(this), 'b_send_inquiry', 'basic-modal-content2');
	});

	jQuery('a.vh_b_sendtofriend').click(function(e) {
		geodir_get_popup_forms(e, jQuery(this), 'b_sendtofriend', 'basic-modal-content');
	});

	function geodir_get_claim_popup_forms(e, clk_class, popup_id) {

		var ajax_url = my_ajax.ajaxurl + '?action=vh_geodir_claim_ajax_action';
		var post_id = jQuery('input[name="geodir_claim_popup_post_id"]').val()

		var append_class = jQuery('.vh_' + clk_class).closest('.claim-busisness-container');

		jQuery.gdmodal('<div id="basic-modal-content" class="clearfix simplemodal-data" style="display: block;"><div class="geodir-modal-loading"><i class="fa fa-refresh fa-spin "></div></div>'); // show popup right away
		jQuery.post(ajax_url, {
				popuptype: clk_class,
				listing_id: post_id
			})
			.done(function(data) {
				append_class.find('.geodir_display_claim_popup_forms').append(data);
				e.preventDefault();
				jQuery.gdmodal.close(); // close popup and show new one with new data, will be so fast user will not see it
				jQuery('#' + popup_id).gdmodal({
					persist: true,
					onClose: function() {
						jQuery.gdmodal.close({
							overlayClose: true
						});
						append_class.find('.geodir_display_claim_popup_forms').html('');
					},
				});

			});

	}

	if (jQuery('.chosen_select').length && !check_browser_agent()) {
		jQuery('.chosen_select').addClass('chosen-mobile');
	};

	function check_browser_agent() {
		if (/iP(od|hone)/i.test(window.navigator.userAgent)) {
			return false;
		}
		if (/Android/i.test(window.navigator.userAgent)) {
			if (/Mobile/i.test(window.navigator.userAgent)) {
				return false;
			}
		}
		return true;
	}

	jQuery('a.vh_geodir_claim_enable').click(function(e) {
		geodir_get_claim_popup_forms(e, 'geodir_claim_enable', 'gd-basic-modal-content4');
	});

	jQuery(".gmmodal-close-dialog").live("click", function() {
		jQuery.gdmodal.close({
			overlayClose: true
		});
	});

	jQuery(".gmmodal-close-dialog").live("click", function() {
		jQuery(".gm-style-iw").parent().find("div").first().find("div:nth-child(4)").hide();
	});

	jQuery('.header_search .sb-icon-search').click(function() {
		jQuery(".header-search-body .header-search-container").fadeIn(300);
	})

	if (jQuery('body.geodir-multi-ratings span.gd-rank').length) {
		jQuery('body.geodir-multi-ratings span.gd-rank').each(function() {
			jQuery(this).html('');
		});
	};

	jQuery('.gd-rate-category ul.rate-area-list li, .gd-rate-area ul.rate-area-list li').hover(function() {
		var hovered_index = jQuery(this).index();
		var item_width = jQuery(this).width();
		var left_position = hovered_index * item_width;
		jQuery(this).parent().parent().find('.gd-rank').stop().css('left', left_position + 'px');
		jQuery(this).parent().parent().find('.gd-rank').stop().addClass('active');
	}, function() {
		var hovered_index = jQuery(this).index();
		var item_width = jQuery(this).width();
		var left_position = hovered_index * item_width;
		jQuery(this).parent().parent().find('.gd-rank').stop().css('left', left_position + 'px');
		jQuery(this).parent().parent().find('.gd-rank').stop().removeClass('active');
	});

	jQuery('.gd-rate-category ul.rate-area-list, .gd-rate-area ul.rate-area-list').hover(function() {
		jQuery(this).addClass('active');
	}, function() {
		jQuery(this).removeClass('active');
	});

	// jQuery(".geodir_category_list_view li.gridview_onehalf").live("hover", function() {
	// 	jQuery(this).addClass("animation");
	// 	jQuery(this).removeClass("reverse-animation");
	// },function() {
	// 	jQuery(this).removeClass("animation");
	// 	jQuery(this).addClass("reverse-animation");
	// });

	jQuery('#your-profile .submit .profile-update-submit').click(function() {
		jQuery(this).next().click();
	})

	jQuery(".geodir_category_list_view li.gridview_onehalf").live({
		mouseenter: function() {
			jQuery(this).addClass("animation");
			jQuery(this).removeClass("reverse-animation");
		},
		mouseleave: function() {
			jQuery(this).removeClass("animation");
			jQuery(this).addClass("reverse-animation");
		}
	});

	jQuery(".map-listing-next, .map-listing-prev").live({
		mouseenter: function() {
			jQuery(this).parent().find("a.wpb_button").hide();
		},
		mouseleave: function() {
			jQuery(this).parent().find("a.wpb_button").show();
		}
	});

	jQuery('.map-listing-next').live("click", function() {
		jQuery(this).parent().find(".map-listing-carousel-container").jcarousel('scroll', '+=1');
	});

	jQuery('.map-listing-prev').live("click", function() {
		jQuery(this).parent().find(".map-listing-carousel-container").jcarousel('scroll', '-=1');
	});

	jQuery('#dd1 > span').live('click', function() {
		if (jQuery("#dd1").hasClass("active")) {
			jQuery(this).parent().find("ul").hide();
		}
		else {
			jQuery(this).parent().find("ul").show();
		}
		jQuery("#dd1").toggleClass("active");
	});

	jQuery(".geodir_category_list_view").isotope({
		transformsEnabled: true,
		getSortData: {
			price: function(elem) {
				var element = jQuery(elem).find(".map-listing-price").html();
				if (element != undefined) {
					element = element.replace(my_ajax.currency_symbol, "");
				};
				return parseFloat(element);
			},
			rating: function(elem) {
				if (element != undefined) {
					var element = jQuery(elem).find(".listing-item-star.text").html();
				}
				return parseFloat(element);
			}
		},
		sortBy: 'price',
		sortAscending: true,
		animationOptions: {
			duration: 250,
			easing: 'swing',
			queue: true
		},
		animationEngine: "jquery"
	});

	jQuery("#dd1 a").live('click', function() {
		jQuery("#dd1 a").removeClass("active");
		jQuery(this).addClass("active");
		jQuery("#dd1 .sort-by").html(jQuery(this).find("span").html());
		jQuery("#dd1 ul").hide();
		jQuery("#dd1").removeClass("active");

		var sortValue = jQuery(this).attr('data-sort-value');

		if (sortValue == 'price') {
			jQuery(".geodir_category_list_view").isotope({
				transformsEnabled: true,
				getSortData: {
					price: function(elem) {
						var element = jQuery(elem).find(".map-listing-price").html();
						if (element != undefined) {
							element = element.replace(my_ajax.currency_symbol, "");
						};
						return parseFloat(element);
					},
					rating: function(elem) {
						if (element != undefined) {
							var element = jQuery(elem).find(".listing-item-star.text").html();
						}
						return parseFloat(element);
					}
				},
				sortBy: sortValue,
				sortAscending: true,
				animationOptions: {
					duration: 250,
					easing: 'swing',
					queue: true
				},
				animationEngine: "jquery"
			});
		}
		else {
			jQuery(".geodir_category_list_view").isotope({
				transformsEnabled: true,
				getSortData: {
					price: function(elem) {
						var element = jQuery(elem).find(".map-listing-price").html();
						if (element != undefined) {
							element = element.replace(my_ajax.currency_symbol, "");
						};
						return parseFloat(element);
					},
					rating: function(elem) {
						if (element != undefined) {
							var element = jQuery(elem).find(".listing-item-star.text").html();
						}
						return parseFloat(element);
					}
				},
				sortBy: sortValue,
				sortAscending: false,
				animationOptions: {
					duration: 250,
					easing: 'swing',
					queue: true
				},
				animationEngine: "jquery"
			});
		}

		if (jQuery("body").hasClass("admin-bar")) {
			var height = jQuery(window).height() - 70 - 46 - 32;

			if (jQuery('.geodir-map-listing-filters').length) {
				height = height - jQuery('.geodir-map-listing-filters').height();
			}

			jQuery("body.geodir-main-search .geodir_category_list_view").css('height', height);
		}
		else {
			var height = jQuery(window).height() - 70 - 46;

			if (jQuery('.geodir-map-listing-filters').length) {
				height = height - jQuery('.geodir-map-listing-filters').height();
			}

			jQuery("body.geodir-main-search .geodir_category_list_view").css('height', height);
		};
	});

	jQuery(window).bind("debouncedresize", function() {
		if (jQuery('body').hasClass('geodir-main-search')) {
			if (jQuery("body").hasClass("admin-bar")) {
				var height = jQuery(window).height() - 70 - 46 - 32;

				if (jQuery('.geodir-map-listing-filters').length) {
					height = height - jQuery('.geodir-map-listing-filters').height();
				}

				jQuery("body.geodir-main-search .geodir_category_list_view").css('height', height);
			}
			else {
				var height = jQuery(window).height() - 70 - 46;

				if (jQuery('.geodir-map-listing-filters').length) {
					height = height - jQuery('.geodir-map-listing-filters').height();
				}

				jQuery("body.geodir-main-search .geodir_category_list_view").css('height', height);
			};
		};
	});

	jQuery(".property-count").html(jQuery("#geodir-search-results").val());

	// jQuery( "#slider-range-price, #slider-range-guests, #slider-range-bedrooms, #slider-range-beds" ).slider({
	// 	range: true,
	// 	min: 0,
	// 	max: 500,
	// 	values: [ 75, 300 ],
	// 	slide: function( event, ui ) {
	// 		// $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
	// 	}
	// });

	jQuery("#geodir-filter-list li.add-filters").click(function(e) {
		if (jQuery(".geodir-filter-container").hasClass("active")) {
			jQuery(".geodir-filter-container").hide();
		}
		else {
			jQuery(".geodir-filter-container").show();
			jQuery(".geodir-filter-container").focus();
			jQuery(".geodir-filter-container").css('left', '675px');
		}
		jQuery(".geodir-filter-container").toggleClass("active");
	});

	jQuery("body").click(function(e) {
		if (jQuery('body').hasClass("search-results")) {
			if (!jQuery(e.srcElement).parent().hasClass("add-filters") && jQuery(e.srcElement.offsetParent).attr("class") !== 'geodir-filter-inner') {
				jQuery(".geodir-filter-container").hide();
				jQuery(".geodir-filter-container").removeClass("active");
			};

			if (!jQuery(e.target).hasClass("sort-by") && !jQuery(e.target).hasClass("geodir-sortby-item")) {
				jQuery("#dd1 > ul").hide();
			};
		};
	});

	jQuery(".map-view-default").click(function() {
		jQuery('#post_mapview').prop('checked', true);
		jQuery(".map-view-default").addClass("active");
		jQuery(".map-view-satellite").removeClass("active");
		jQuery(".map-view-hybrid").removeClass("active");
	});

	jQuery(".map-view-satellite").click(function() {
		jQuery('#map_view1').prop('checked', true);
		jQuery(".map-view-satellite").addClass("active");
		jQuery(".map-view-default").removeClass("active");
		jQuery(".map-view-hybrid").removeClass("active");
	});

	jQuery(".map-view-hybrid").click(function() {
		jQuery('#map_view2').prop('checked', true);
		jQuery(".map-view-hybrid").addClass("active");
		jQuery(".map-view-default").removeClass("active");
		jQuery(".map-view-satellite").removeClass("active");
	});

	jQuery(".addlisting-upload-button").click(function() {
		$select_file_button = 'clicked';
		jQuery("#post_imagesplupload-browse-button").click(); //post_imagesplupload-thumbs
	});

	jQuery(".checkbox_box").live('click', function() {
		if (jQuery(this).hasClass("checked")) {
			jQuery(this).parent().removeClass("checked");
			jQuery(this).parent().find("input[type=checkbox]").prop('checked', false);
		}
		else {
			jQuery(this).parent().addClass("checked");
			jQuery(this).parent().find("input[type=checkbox]").prop('checked', true);
		}
		jQuery(this).toggleClass("checked icon-ok");

	});

	jQuery(".radiobutton").live("click", function() {
		if (jQuery(this).hasClass("checked")) {
			jQuery(this).parent().find("input[type=radio]").prop('checked', false);
		}
		else {
			jQuery(this).parent().find("input[type=radio]").prop('checked', true).click();
		}
		jQuery(this).parent().parent().find('.radiobutton').removeClass("checked");
		jQuery(this).toggleClass("checked");

	});

	if ((jQuery("#header-location").length || jQuery("#header-top-location").length) && my_ajax.autocomplete == '1') {

		jQuery(document).on('keypress', '#header-location, #header-top-location', function() {
			jQuery(this).autocomplete({
				source: my_ajax.ajaxurl + "?action=vh_get_location_search_terms&vh_post_type=" + jQuery('#header-post-type').val(),
				minLength: 2, //search after two characters
				select: function(event, ui) {
					jQuery.cookie('vh_viewer_location', ui.item.value, {
						path: '/'
					});
					if (my_ajax.autosubmit == '1') {
						jQuery("#header-submit, #header-submit2").click();
					};
				},
				html: true,
				open: function() {
					jQuery(".ui-autocomplete:visible").css({
						left: "-=6"
					});
				}
			}).data("ui-autocomplete")._renderItem = function(ul, item) {
				return $("<li></li>")
					.data("item.autocomplete", item)
					.append("<a>" + item.label + "</a>")
					.appendTo(ul);
			};

		});
	};

	if ((jQuery("#header-type").length || jQuery("#header-top-type").length || jQuery("#header-category").length || jQuery("#header-top-category").length)) {
		jQuery("#header-type, #header-top-type, #header-category, #header-top-category").autocomplete({
			source: my_ajax.ajaxurl + "?action=vh_get_listing_category&vh_post_type=" + jQuery('#header-post-type').val(),
			minLength: 2, //search after two characters
			select: function(event, ui) {
				jQuery(this).parent().removeClass('triangle');
			},
			html: true,
			open: function() {
				jQuery(this).parent().find('.search-contract-container').removeClass('active').hide();
				jQuery(this).parent().addClass('triangle');
				jQuery(".ui-autocomplete:visible").css({
					left: "-=7",
					top: "+=4"
				});
			}
		}).data("ui-autocomplete")._renderItem = function(ul, item) {
			return $("<li></li>")
				.data("item.autocomplete", item)
				.append("<a>" + item.label + "</a>")
				.appendTo(ul);
		};
	};

	jQuery("#header-type, #header-top-type, #header-category, #header-top-category").live('focus', function() {
		jQuery('body').addClass('category-field-focused');
		jQuery(this).parent().addClass('focused');
	});

	jQuery("#header-type, #header-top-type, #header-category, #header-top-category").live('blur', function() {
		jQuery('body').removeClass('category-field-focused');
		jQuery(this).parent().removeClass('focused');
	});

	// if ( jQuery("body").hasClass("admin-bar") ) {
	// 	var height = jQuery(window).height()-70-77-46-32;
	// 	jQuery("body.geodir-main-search .geodir_category_list_view").height(height);
	// 	jQuery("body.geodir-main-search #vh_wrappers").css('height', jQuery(window).height()-32);
	// } else {
	// 	jQuery("body.geodir-main-search .geodir_category_list_view").css('height', jQuery(window).height()-70-77-46);
	// 	jQuery("body.geodir-main-search #vh_wrappers").css('height', jQuery(window).height());
	// };

	function hideDatePicker(element) {
		if (typeof element === 'undefined') {
			var element = jQuery(this).parent().find(".search-calendar-options");
		}
		else {
			element = element.find(".search-calendar-options");
		}

		element.animate({
			opacity: 0
		}, 0, function() {
			jQuery(".search-calendar-options").hide();
			jQuery(".search-calendar-container").hide();
		});
	}

	function showDatePicker(element) {
		if (typeof element === 'undefined') {
			var element = jQuery(this).parent().find(".search-calendar-options");
		}
		else {
			element = element.find(".search-calendar-options");
		}

		jQuery(".search-calendar-options").stop().show();
		jQuery(".search-calendar-container").show();
		jQuery(".search-calendar-container").width(jQuery(".search-calendar-options").outerWidth() + jQuery("#ui-datepicker-div").outerWidth() - 12);
		jQuery(".search-calendar-container").height(jQuery("#ui-datepicker-div").outerHeight() - 4);
		jQuery(".search-calendar-options").height(jQuery("#ui-datepicker-div").outerHeight() - 4);
		jQuery(".search-calendar-options").animate({
			opacity: 1
		}, 65, function() {
			// Animation complete.
		});
	}

	var startDate, endDate, dateRange = [];
	var $tmp_start_date, $tmp_end_date, $date_selected = '';

	if (jQuery("#header-when, #header-top-when, #listing-when").length) {
		jQuery("#header-when, #header-top-when, #listing-when").datepicker({
			dateFormat: 'yy-mm-dd',
			onClose: function(dateText, inst) {
				jQuery(".ui-datepicker").stop(false, true);
			},
			dayNamesMin: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'],
			showAnim: 'fadeIn',
			minDate: 0,
			onChangeMonthYear: function() {
				setTimeout(function() {
					jQuery(".search-calendar-container").width(jQuery(".search-calendar-options").outerWidth() + jQuery("#ui-datepicker-div").outerWidth() - 12);
					if (jQuery(window).width() < 767) {
						jQuery(".search-calendar-container").height(jQuery("#ui-datepicker-div").outerHeight() - 4 + 272);
					}
					else {
						jQuery(".search-calendar-container").height(jQuery("#ui-datepicker-div").outerHeight() - 4);
					}
					jQuery(".search-calendar-options").height(jQuery("#ui-datepicker-div").outerHeight() - 4);
				}, 50);
			},
			onSelect: function(dateText, inst) {

				// Page has been reloaded, if user tries to select new date then start all over again
				if (!$date_selected) {
					jQuery("#startrange").val('');
					jQuery("#startrange").val('');
				}

				var date1 = jQuery.datepicker.parseDate('yy-mm-dd', jQuery("#startrange").val());
				var date2 = jQuery.datepicker.parseDate('yy-mm-dd', jQuery("#endrange").val());
				if (!date1 || date2) {
					if (jQuery(".single-listing-info").length) {
						jQuery(".single-listing-info .single-listing-text .for-selected").html(jQuery(".single-listing-info .single-listing-text.small .per-night").html());
					}

					jQuery("#startrange").val(dateText);
					jQuery("#endrange").val("");


					// Start date is selected. Set min date to selected start date.
					jQuery(this).datepicker("option", "minDate", dateText);

					// Get date from datepicker
					startDate = dateText;

					// Set input field value
					jQuery("#header-when, #listing-when").val(startDate);

					// Set cookie
					jQuery.cookie('vh_selected_date', startDate, {
						path: '/'
					});

					// Set date range cookies
					jQuery.cookie('vh_startrange', dateText, {
						path: '/'
					});
					jQuery.cookie('vh_endrange', '', {
						path: '/'
					});

					// Keep open datepicker
					showDatePicker();
					jQuery(".ui-datepicker").css({
						'display': 'block'
					});
				}
				else {
					jQuery("#endrange").val(dateText);
					// jQuery("#header-when, #listing-when").datepicker('hide');

					// End date is selected. Set min date to "today"
					jQuery(this).datepicker("option", "minDate", 0);

					// Get date from datepicker
					endDate = dateText;

					// Format date
					var startformatted = $.datepicker.formatDate('d', new Date(startDate));
					var endformatted = $.datepicker.formatDate('d M', new Date(endDate));
					var year = endDate.split("-");

					// Set input field value
					jQuery("#header-when, #header-top-when, #listing-when").val(startformatted + " - " + endformatted + " " + year["0"]);
					dateRange = [];
					for (var d = new Date(startDate); d <= new Date(endDate); d.setDate(d.getDate() + 1)) {
						dateRange.push($.datepicker.formatDate('yy-mm-dd', d));
					}

					// Set cookie (Format: 30 - 32 Dec 2014)
					jQuery.cookie('vh_selected_date', startformatted + " - " + endformatted + " " + year["0"], {
						path: '/'
					});

					// Set date range cookies
					jQuery.cookie('vh_endrange', dateText, {
						path: '/'
					});

					// Reset daterange
					dateRange = [];

					// Remove click event
					jQuery('.vh_wrapper').off('click.calendar');

					var a_date = new Date(Date.parse(jQuery.cookie('vh_startrange')).toString('yyyy'), parseInt(Date.parse(jQuery.cookie('vh_startrange')).toString('M')) - 1, Date.parse(jQuery.cookie('vh_startrange')).toString('d'), 0, 0, 0, 0);
					var b_date = new Date(Date.parse(jQuery.cookie('vh_endrange')).toString('yyyy'), parseInt(Date.parse(jQuery.cookie('vh_endrange')).toString('M')) - 1, Date.parse(jQuery.cookie('vh_endrange')).toString('d'), 0, 0, 0, 0);

					if (jQuery(".single-listing-info").length) {
						///////////////////////jino "for selected dates"
						//jQuery(".single-listing-info .single-listing-text .for-selected").html(jQuery(".single-listing-info .single-listing-text.small .per-night").html()*(Math.round((b_date-a_date)/1000/60/60/24)+1));
						var amnt = jQuery(".single-listing-info .single-listing-text.small .per-night").html() * (Math.round((b_date - a_date) / 1000 / 60 / 60 / 24));


						if (amnt == 0) {

						}
						else {
							jQuery(".single-listing-info .single-listing-text .for-selected").html(amnt);
						}

					}

					// Hide datepicker
					hideDatePicker(jQuery(this).parent());
					jQuery(".ui-datepicker").css({
						'display': 'none'
					});

					$date_selected = false;
					$tmp_start_date = '';
					$tmp_end_date = '';
				}
				$date_selected = true;

				jQuery("td.highlighted").removeClass("highlighted");
			},
			beforeShowDay: function(date) {
				var date1, date2 = '';

				if ($tmp_start_date) {
					date1 = jQuery.datepicker.parseDate('yy-mm-dd', $tmp_start_date);
					date2 = jQuery.datepicker.parseDate('yy-mm-dd', $tmp_end_date);
				}
				else {
					date1 = jQuery.datepicker.parseDate('yy-mm-dd', jQuery("#startrange").val());
					date2 = jQuery.datepicker.parseDate('yy-mm-dd', jQuery("#endrange").val());
				}

				return [true, date1 && ((date.getTime() == date1.getTime()) || (date2 && date >= date1 && date <= date2)) ? "highlighted" : ""];
			},
			beforeShow: function(input) {

				// Add click event with delay
				setTimeout(function() {

					// When click anywhere check if it's not calendar.
					jQuery('.vh_wrapper').on("click.calendar", function(e) {

						// Remove click event
						hideDatePicker(jQuery('body'));
						jQuery(".ui-datepicker").css({
							'display': 'none'
						});
						$date_selected = false;
						$tmp_start_date = '';
						$tmp_end_date = '';
						jQuery('.vh_wrapper').off('click.calendar');
					});
				}, 200);

				// Set min date to "today"
				jQuery(this).datepicker("option", "minDate", 0);
				jQuery("#header-when, #header-top-when").datepicker("refresh");
			}
		});
	}

	if (jQuery.cookie('vh_selected_date') == null) {
		var d = new Date();

		var month = d.getMonth() + 1;
		var day = d.getDate();

		var output = d.getFullYear() + '-' +
			(('' + month).length < 2 ? '0' : '') + month + '-' +
			(('' + day).length < 2 ? '0' : '') + day;

		jQuery.cookie('vh_selected_date', output, {
			path: '/'
		});
	};

	if (jQuery("#header-when, #header-top-when, #listing-when").length) {
		if (jQuery.cookie('vh_selected_date') == "This Weekend") {
			jQuery("#header-when, #header-top-when, #listing-when").val(jQuery.cookie('vh_selected_date'));
			jQuery(".single-listing-info .single-listing-text .for-selected").html(parseInt(jQuery(".single-listing-info .single-listing-text.small .per-night").html()) * 2);
		}
		else if (jQuery.cookie('vh_selected_date') == "Next week") {
			jQuery("#header-when, #header-top-when, #listing-when").val(jQuery.cookie('vh_selected_date'));
			jQuery(".single-listing-info .single-listing-text .for-selected").html(parseInt(jQuery(".single-listing-info .single-listing-text.small .per-night").html()) * 7);
		}
		else if (jQuery.cookie('vh_selected_date').indexOf(" - ") > -1) {
			var a_date = new Date(Date.parse(jQuery.cookie('vh_startrange')).toString('yyyy'), parseInt(Date.parse(jQuery.cookie('vh_startrange')).toString('M')) - 1, Date.parse(jQuery.cookie('vh_startrange')).toString('d'), 0, 0, 0, 0);
			var b_date = new Date(Date.parse(jQuery.cookie('vh_endrange')).toString('yyyy'), parseInt(Date.parse(jQuery.cookie('vh_endrange')).toString('M')) - 1, Date.parse(jQuery.cookie('vh_endrange')).toString('d'), 0, 0, 0, 0);

			jQuery("#header-when, #header-top-when, #listing-when").val(jQuery.cookie('vh_selected_date'));
			jQuery(".single-listing-info .single-listing-text .for-selected").html(jQuery(".single-listing-info .single-listing-text.small .per-night").html() * (Math.round((b_date - a_date) / 1000 / 60 / 60 / 24) + 1));
		}
		else {
			jQuery("#header-when, #header-top-when, #listing-when").datepicker("setDate", new Date(jQuery.cookie('vh_selected_date')));
			jQuery(".single-listing-info .single-listing-text .for-selected").html(jQuery(".single-listing-info .single-listing-text.small .per-night").html());
		}
	}

	if (jQuery.cookie('vh_selected_people') == null) {
		jQuery.cookie('vh_selected_people', '1 Adult/No Children', {
			path: '/'
		});
		jQuery("#header-people, #listing-people").val(jQuery.cookie('vh_selected_people'));

	}
	else {
		console.log(">>>>>>>>>>>>>>>>>>>>");
		jQuery("#header-people, #listing-people").val(jQuery.cookie('vh_selected_people'));
		//	jQuery("#header-people, #listing-people").val('vh_selected_people');

	}

	jQuery(".calendar-search-item").live({
		mouseenter: function() {
			if (jQuery(this).hasClass("today")) {
				$tmp_start_date = Date.parse('today').toString('yyyy-MM-dd');
				$tmp_end_date = '';
			}
			else if (jQuery(this).hasClass("tomorrow")) {
				$tmp_start_date = Date.parse('tomorrow').toString('yyyy-MM-dd');
				$tmp_end_date = '';
			}
			else if (jQuery(this).hasClass("this_weekend")) {
				$tmp_start_date = Date.parse('saturday').toString('yyyy-MM-dd');
				$tmp_end_date = Date.parse('next sunday').toString('yyyy-MM-dd');
			}
			else if (jQuery(this).hasClass("next_week")) {
				$tmp_start_date = Date.parse('next monday').toString('yyyy-MM-dd');
				$tmp_end_date = Date.parse('next monday').next().sunday().toString('yyyy-MM-dd');
			}

			// Temporary reset selected date
			jQuery("#header-when, #header-top-when, #listing-when").datepicker("setDate", null);

			// sky_datepicker.datepicker("setDate", $tmp_start_date);
			jQuery("#header-when, #header-top-when, #listing-when").datepicker("refresh");
		},
		mouseleave: function() {
			$tmp_start_date = jQuery("#startrange").val();
			$tmp_end_date = jQuery("#endrange").val();

			jQuery("#header-when, #header-top-when, #listing-when").datepicker("refresh");
		}
	});

	jQuery(".calendar-search-item").live('click', function() {
		if (jQuery(this).hasClass("today")) {
			jQuery("#header-when, #header-top-when, #listing-when").val("Today");
			var date = jQuery(this).find(".date-value").val();

			jQuery("#startrange").val(Date.parse('today').toString('yyyy-MM-dd'));
			jQuery("#endrange").val('');

			// Set date range cookies
			jQuery.cookie('vh_startrange', Date.parse('today').toString('yyyy-MM-dd'), {
				path: '/'
			});
			jQuery.cookie('vh_endrange', '', {
				path: '/'
			});
			jQuery.cookie('vh_selected_date', date, {
				path: '/'
			});

			if (jQuery(".single-listing-info").length) {
				jQuery(".single-listing-info .single-listing-text .for-selected").html(jQuery(".single-listing-info .single-listing-text.small .per-night").html());
			}
		}
		else if (jQuery(this).hasClass("tomorrow")) {
			jQuery("#header-when, #header-top-when, #listing-when").val("Tomorrow");
			var date = jQuery(this).find(".date-value").val();

			jQuery("#startrange").val(Date.parse('tomorrow').toString('yyyy-MM-dd'));
			jQuery("#endrange").val('');

			// Set date range cookies
			jQuery.cookie('vh_startrange', Date.parse('tomorrow').toString('yyyy-MM-dd'), {
				path: '/'
			});
			jQuery.cookie('vh_endrange', '', {
				path: '/'
			});
			jQuery.cookie('vh_selected_date', date, {
				path: '/'
			});

			if (jQuery(".single-listing-info").length) {
				jQuery(".single-listing-info .single-listing-text .for-selected").html(jQuery(".single-listing-info .single-listing-text.small .per-night").html());
			}
		}
		else if (jQuery(this).hasClass("this_weekend")) {
			jQuery("#header-when, #header-top-when, #listing-when").val("This Weekend");

			jQuery("#startrange").val(Date.parse('saturday').toString('yyyy-MM-dd'));
			jQuery("#endrange").val(Date.parse('next sunday').toString('yyyy-MM-dd'));

			// Set date range cookies
			jQuery.cookie('vh_startrange', Date.parse('saturday').toString('yyyy-MM-dd'), {
				path: '/'
			});
			jQuery.cookie('vh_endrange', Date.parse('next sunday').toString('yyyy-MM-dd'), {
				path: '/'
			});
			jQuery.cookie('vh_selected_date', 'This Weekend', {
				path: '/'
			});

			if (jQuery(".single-listing-info").length) {
				jQuery(".single-listing-info .single-listing-text .for-selected").html(jQuery(".single-listing-info .single-listing-text.small .per-night").html() * 2);
			}
		}
		else if (jQuery(this).hasClass("next_week")) {
			jQuery("#header-when, #header-top-when, #listing-when").val("Next week");

			jQuery("#startrange").val(Date.parse('next monday').toString('yyyy-MM-dd'));
			jQuery("#endrange").val(Date.parse('next monday').next().sunday().toString('yyyy-MM-dd'));

			// Set date range cookies
			jQuery.cookie('vh_startrange', Date.parse('next monday').toString('yyyy-MM-dd'), {
				path: '/'
			});
			jQuery.cookie('vh_endrange', Date.parse('next monday').next().sunday().toString('yyyy-MM-dd'), {
				path: '/'
			});
			jQuery.cookie('vh_selected_date', 'Next week', {
				path: '/'
			});

			if (jQuery(".single-listing-info").length) {
				jQuery(".single-listing-info .single-listing-text .for-selected").html(jQuery(".single-listing-info .single-listing-text.small .per-night").html() * 7);
			}
		}

		// Hide datepicker
		hideDatePicker(jQuery(this).parent().parent().parent());
		jQuery(".header-when.ui-datepicker").css({
			'display': 'none'
		});

		$tmp_start_date = '';
		$tmp_end_date = '';
	});

	jQuery(".geodir-addtofav-icon").hover(function() {
		jQuery(this).find(".fa-heart").addClass("icon-heart-1");
		jQuery(this).parent().append("<span class=\"listing-slider-hover\">Add to favorites</span>");
		jQuery(this).parent().find("span").show();
	}, function() {
		jQuery(this).find(".fa-heart").removeClass("icon-heart-1");
		jQuery(this).parent().find("span").remove();
	});

	jQuery(".geodir-removetofav-icon").hover(function() {
		jQuery(this).find(".fa-heart").addClass("icon-heart-1");
		jQuery(this).parent().append("<span class=\"listing-slider-hover\">Remove from favorites</span>");
		jQuery(this).parent().find("span").show();
	}, function() {
		jQuery(this).find(".fa-heart").removeClass("icon-heart-1");
		jQuery(this).parent().find("span").remove();
	});

	jQuery(".header-slider-prev, .header-slider-next").hover(function() {
		jQuery(".listing-item-rating, .listing-item-title, .listing-item-location, .listing-item-info").fadeOut(300);
		jQuery(".listing-item.first").removeClass("first");
	}, function() {
		jQuery(".listing-item-rating, .listing-item-title, .listing-item-location, .listing-item-info").fadeIn(300);
		jQuery(".listing-carousel li").addClass("first");
	});

	jQuery(".listing-item-video").click(function() {
		jQuery(".listing-item").find("iframe").css({
			"position": "relative",
			"z-index": "22"
		});
		jQuery(".listing-item").find("iframe").parent().append("<a href=\"javascript:void(0)\" class=\"listing-item-exitfullscreen icon-resize-small\"></a>");
	});

	jQuery(".listing-item-exitfullscreen").live("click", function() {
		jQuery(".listing-item").find("iframe").css({
			"position": "initial",
			"z-index": "0"
		});
		jQuery(".listing-item").find("iframe").parent().find(".listing-item-exitfullscreen").remove();
	});

	function get_datepicker_highlights() {
		var today, tomorrow, this_weekend, next_week = "";
		jQuery(".date-value").each(function(i, val) {
			if (i == "0") {
				today = '{"value_type":"today","value_date":"' + jQuery(this).val() + '"},';
			}
			else if (i == 1) {
				tomorrow = '{"value_type":"tomorrow","value_date":"' + jQuery(this).val() + '"},';
			}
			else if (i == 2) {
				this_weekend = '{"value_type":"this_weekend","value_date":"' + jQuery(this).val() + '"},';
			}
			else if (i == 3) {
				next_week = '{"value_type":"next_week","value_date":"' + jQuery(this).val() + '"}';
			}
		});
		return "[" + today + tomorrow + this_weekend + next_week + "]";
	}

	setTimeout(function() {
		jQuery(".listing-carousel-container").jcarousel({
			wrap: "circular"
		});
	}, 500);

	jQuery(".listing-carousel li").each(function() {
		jQuery(this).css("width", jQuery(window).width());
	});

	jQuery(window).bind("debouncedresize", function() {
		jQuery(".listing-carousel li").each(function() {
			jQuery(this).css("width", jQuery(window).width());
			jQuery('.listing-carousel-container').jcarousel();
		});

		if (jQuery('body').hasClass('single-geodir-page')) {
			if (jQuery(window).width() > 1200) {
				jQuery(".stick_trigger_container").css({
					"width": ((jQuery(window).width() - 1200) / 2) + jQuery('.single-listing-sidebar').width() + 57
				});
				jQuery(".geodir_marker_cluster").css({
					"width": ((jQuery(window).width() - 1200) / 2) + jQuery('.single-listing-sidebar').width() + 57
				});
				jQuery(".geodir_map_container, #home_map_canvas_wrapper, #geodir_map_v3_home_map_100_wrapper, #geodir_map_v3_home_map_101_wrapper, #geodir_map_v3_home_map_102_wrapper").css({
					"width": ((jQuery(window).width() - 1200) / 2) + jQuery('.single-listing-sidebar').width() + 57
				});
			}
			else {
				jQuery(".stick_trigger_container").css({
					"width": "100%"
				});
				jQuery(".geodir_marker_cluster").css({
					"width": "100%"
				});
				jQuery(".geodir_map_container, #home_map_canvas_wrapper, #geodir_map_v3_home_map_100_wrapper, #geodir_map_v3_home_map_101_wrapper, #geodir_map_v3_home_map_102_wrapper").css({
					"width": "100%"
				});
			}

			var position = jQuery.goMap.map.getCenter();
			google.maps.event.trigger(jQuery.goMap.map, "resize");

			jQuery.goMap.map.setCenter(position);
		}
		else {
			var sidebar_width = jQuery(".geodir-map-left").outerWidth();
			jQuery(".stick_trigger_container").css("width", jQuery(window).width() - sidebar_width + "px");
			jQuery(".geodir_marker_cluster").css("width", jQuery(window).width() - sidebar_width + "px");
			jQuery(".geodir_map_container, #home_map_canvas_wrapper, #geodir_map_v3_home_map_100_wrapper, #geodir_map_v3_home_map_101_wrapper, #geodir_map_v3_home_map_102_wrapper").css("width", jQuery(window).width() - sidebar_width + "px");
		}

		if (jQuery("body").hasClass("skydirectory")) {
			jQuery('body.single-geodir-page #geodir-wrapper, body.geodir-listing-preview #geodir-wrapper').css('margin-top', jQuery('.listing-carousel-container').height() + 70);
		}
		else {
			jQuery('body.single-geodir-page #geodir-wrapper, body.geodir-listing-preview #geodir-wrapper').css('margin-top', jQuery('.listing-carousel-container').height() + 140);
		}
	});

	jQuery('.header-slider-next').click(function() {
		jQuery(".listing-carousel-container").jcarousel('scroll', '+=1');
	});

	jQuery('.header-slider-prev').click(function() {
		jQuery(".listing-carousel-container").jcarousel('scroll', '-=1');
	});

	jQuery('.listing-item-fullscreen').click(function() {
		jQuery(".listing-carousel-container").css("opacity", "0");
		jQuery(".listing-gallery-carousel-main").fadeIn(150);
		jQuery(".gdplaces-header-container").addClass("gallery");
		jQuery("body").addClass("header-gallery");
		window.scrollTo(0, 70);
		if (jQuery("body").hasClass("skydirectory")) {
			var geodir_height = Number(jQuery(window).height()) + 70;
		}
		else {
			var geodir_height = Number(jQuery(window).height()) + 140;
		}
		var picture_max_height = Number(jQuery(window).height()) - 325;
		if (jQuery("body").hasClass("admin-bar")) {
			var container_height = jQuery(window).height() - 32;
		}
		else {
			var container_height = jQuery(window).height();
		}

		console.log(jQuery(window).height());

		jQuery(".listing-gallery-carousel-main").append("<style>.gdplaces-header-container.gallery .listing-carousel-container, .gdplaces-header-container.gallery .listing-gallery-carousel-main, .gdplaces-header-container.gallery .listing-gallery-carousel-container { height: " + container_height + "px; } body.single-geodir-page.header-gallery #geodir-wrapper { margin-top: " + geodir_height + "px; } .listing-gallery-item.active-right img, .listing-gallery-item.active-left img { max-height: " + picture_max_height + "px; } .listing-gallery-carousel li { height: " + container_height + "px; }</style>");

		// jQuery('html, body').animate({scrollTop: '+=70px'}, 300);
	});

	jQuery('.text-field-placeholder').each(function() {
		if (jQuery(this).val() == 'Captcha') {
			jQuery(this).next().find('input[type=text]').attr("placeholder", jQuery(this).val());
		}
		else if (jQuery(this).next().attr('type') == 'text' || jQuery(this).next().is('textarea')) {
			jQuery(this).next().attr("placeholder", jQuery(this).val());

		};
	})

	jQuery('.easy-button').addClass('wpb_button wpb_btn-primary wpb_regularsize');

	if (!jQuery('body').hasClass('skyestate')) {
		jQuery('.single-listing-booknow').click(function() {
			var startdate = $.datepicker.formatDate('dd.mm.yy', new Date(jQuery('#startrange').val()));
			var enddate = $.datepicker.formatDate('dd.mm.yy', new Date(jQuery('#endrange').val()));
			if (enddate == 'NaN.NaN.NaN') {
				enddate = startdate;
			};
			if (jQuery('#startrange').val() != '') {
				jQuery('#easy-listing-datepicker-from').val(startdate);
			};
			if (jQuery('#endrange').val() == '') {
				jQuery('#easy-listing-datepicker-to').val(startdate);
			}
			else {
				jQuery('#easy-listing-datepicker-to').val(enddate);
			}
			jQuery('#easy_listing_form').submit();
		})
	};

	jQuery(".listing-item-exitfullscreen").live("click", function() {
		if (jQuery(this).hasClass("googlemap") && !jQuery(this).hasClass("icon-resize-small") && jQuery("body").hasClass("geodir-main-search")) {
			jQuery(".geodir-map-listing-filters").hide();
			jQuery(".geodir-map-listing-top").hide();
			jQuery("body.geodir-main-search .geodir_category_list_view").hide();
			jQuery("body.geodir-main-search .stick_trigger_container").css({
				'top': '0',
				'float': 'left'
			});
			jQuery("body").addClass("geodir-fullmap");

			var content_width = jQuery("#geodir-wrapper").outerWidth();
			var browser_width = jQuery(window).width();
			if (jQuery("body").hasClass("admin-bar")) {
				var browser_height = jQuery(window).height() - 102;
			}
			else {
				var browser_height = jQuery(window).height() - 70;
			}

			jQuery(".stick_trigger_container").css({
				"width": browser_width + "px",
				"height": browser_height + "px"
			});
			jQuery(".geodir_marker_cluster").css({
				"width": browser_width,
				"height": browser_height + "px",
				"left": "0",
				"position": "relative"
			});
			jQuery(".geodir_map_container, #home_map_canvas_wrapper, #geodir_map_v3_home_map_100_wrapper, #geodir_map_v3_home_map_101_wrapper, #geodir_map_v3_home_map_102_wrapper").css({
				"width": browser_width,
				"height": browser_height + "px"
			});

			var extra_html = '<a href="javascript:void(0)" class="geodir-map-filters googlemap"></a>'
			jQuery(this).parent().append('<a href="javascript:void(0)" class="listing-item-exitfullscreen icon-resize-small googlemap"></a><div class="clearfix"></div>' + extra_html);
			jQuery(this).remove();

			var position = jQuery.goMap.map.getCenter();
			google.maps.event.trigger(jQuery.goMap.map, "resize");

			jQuery.goMap.map.setCenter(position);
		}
		else if (jQuery(this).hasClass("googlemap") && jQuery(this).hasClass("icon-resize-small") && jQuery("body").hasClass("geodir-main-search")) {
			jQuery(".geodir-map-listing-filters").show();
			jQuery(".geodir-map-listing-top").show();
			jQuery("body.geodir-main-search .geodir_category_list_view").show();
			jQuery(".geodir-filter-container").hide();
			jQuery("body.geodir-main-search .stick_trigger_container").css({
				'top': '0',
				'float': 'left'
			});
			jQuery("body").removeClass("geodir-fullmap");

			var content_width = jQuery("#geodir-wrapper").outerWidth();
			var browser_width = jQuery(window).width();
			if (jQuery("body").hasClass("admin-bar")) {
				var browser_height = jQuery(window).height() - 102;
			}
			else {
				var browser_height = jQuery(window).height() - 70;
			}

			jQuery(".stick_trigger_container").css({
				"width": browser_width - 673 + "px",
				"height": browser_height + "px"
			});
			jQuery(".geodir_marker_cluster").css({
				"width": browser_width - 673,
				"height": browser_height + "px",
				"left": "0",
				"position": "relative"
			});
			jQuery(".geodir_map_container, #home_map_canvas_wrapper, #geodir_map_v3_home_map_100_wrapper, #geodir_map_v3_home_map_101_wrapper, #geodir_map_v3_home_map_102_wrapper").css({
				"width": browser_width - 673,
				"height": browser_height + "px"
			});

			jQuery(this).parent().append('<a href="javascript:void(0)" class="listing-item-exitfullscreen icon-resize-full googlemap"></a>');
			jQuery(".geodir-map-filters.googlemap").remove();
			jQuery(this).remove();

			var position = jQuery.goMap.map.getCenter();
			google.maps.event.trigger(jQuery.goMap.map, "resize");

			jQuery.goMap.map.setCenter(position);

			var listing_price = '';
			var listing_price_val = '';
			var listing_guests = '';
			var listing_guests_val = '';
			var listing_bedrooms = '';
			var listing_bedrooms_val = '';
			var listing_beds = '';
			var listing_beds_val = '';

			jQuery("#geodir-filter-list li").each(function() {
				if (jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("per night") >= 0) {
					listing_price = listing_price_val = jQuery(this).find(".tagit-label").html();
				}
				else if (jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("guests") >= 0) {
					listing_guests = listing_guests_val = jQuery(this).find(".tagit-label").html();
				}
				else if (jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("bedrooms") >= 0) {
					listing_bedrooms = listing_bedrooms_val = jQuery(this).find(".tagit-label").html();
				}
				else if (jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("beds") >= 0) {
					listing_beds = listing_beds_val = jQuery(this).find(".tagit-label").html();
				}
			});

			jQuery.ajax({
				type: 'POST',
				url: my_ajax.ajaxurl,
				data: {
					"action": "geodir_search_markers",
					listing_date: jQuery(".geodir-filter-when-value").html(),
					listing_price: listing_price_val,
					listing_guests: listing_guests_val,
					listing_bedrooms: listing_bedrooms_val,
					listing_beds: listing_beds_val
				},
				success: function(response) {
					var jsonData = jQuery.parseJSON(response);

					return false;
				}
			});

			get_geodir_search_posts(jQuery(".geodir-filter-when-value").html(), listing_price, listing_guests, listing_bedrooms, listing_beds, my_ajax.ajaxurl);

		}
		else if (jQuery(this).hasClass("googlemap") && jQuery(this).hasClass("icon-resize-small")) {
			var listing_price = '';
			var listing_guests = '';
			var listing_bedrooms = '';
			var listing_beds = '';

			jQuery.ajax({
				type: 'POST',
				url: my_ajax.ajaxurl,
				data: {
					"action": "geodir_search_sidemap"
				},
				success: function(response) {
					jQuery("#geodir-main-search").html(jQuery("#geodir-main-search").html() + response);
					// jQuery("body.geodir-main-search .stick_trigger_container").css({'top': '0', 'float': 'left'});
					// var extra_html = '<a href="javascript:void(0)" class="geodir-map-filters googlemap"></a>'
					// jQuery(".geodir_map_container").append('<a href="javascript:void(0)" class="listing-item-exitfullscreen icon-resize-small googlemap"></a><div class="clearfix"></div>'+extra_html);
					return false;
				}
			});

			jQuery("#geodir-filter-list li").each(function() {
				if (jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("per night") >= 0) {
					listing_price = jQuery(this).find(".tagit-label").html();
				}
				else if (jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("guests") >= 0) {
					listing_guests = jQuery(this).find(".tagit-label").html();
				}
				else if (jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("bedrooms") >= 0) {
					listing_bedrooms = jQuery(this).find(".tagit-label").html();
				}
				else if (jQuery(this).find(".tagit-label").html() != undefined && jQuery(this).find(".tagit-label").html().indexOf("beds") >= 0) {
					listing_beds = jQuery(this).find(".tagit-label").html();
				}
			});

			get_geodir_search_posts(jQuery(".geodir-filter-when-value").html(), listing_price, listing_guests, listing_bedrooms, listing_beds, my_ajax.ajaxurl);

		}
		else {
			jQuery(".listing-carousel-container").css("opacity", "1");
			jQuery(".listing-gallery-carousel-main").fadeOut(150);
			jQuery(".gdplaces-header-container").removeClass("gallery");
			jQuery("body").removeClass("header-gallery");
		}
	});

	function get_geodir_search_posts(date, price, guests, bedrooms, beds, ajaxurl) {
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				"action": "geodir_search",
				listing_date: date,
				listing_price: price,
				listing_guests: guests,
				listing_bedrooms: bedrooms,
				listing_beds: beds
			},
			success: function(response) {
				jQuery("#geodir-main-search").html(response);
				// jQuery(".geodir_category_list_view").isotope();
				// jQuery(".map-listing-carousel-container").jcarousel({wrap: "circular"});
				jQuery(".property-count").html(jQuery("#geodir-search-results").val());
				jQuery("#geodir-main-search").removeClass("loading");

				jQuery(".geodir-map-listing-filters").show();
				jQuery(".geodir-map-listing-top").show();
				jQuery("body.geodir-main-search .geodir_category_list_view").show();
				jQuery(".geodir-filter-container").hide();

				return false;
			}
		});
	}

	function get_geodir_fullmap(ajaxurl) {
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				"action": "geodir_search_fullmap"
			},
			success: function(response) {
				jQuery("#geodir-main-search").html(response);
				jQuery("body.geodir-main-search .stick_trigger_container").css({
					'top': '0',
					'float': 'left'
				});
				var extra_html = '<a href="javascript:void(0)" class="geodir-map-filters googlemap"></a>'
				jQuery(".geodir_map_container").append('<a href="javascript:void(0)" class="listing-item-exitfullscreen icon-resize-small googlemap"></a>' + extra_html);
				return false;
			}
		});
	}

	// jQuery(".geodir_map_container").append('<a href="javascript:void(0)" class="listing-item-exitfullscreen icon-resize-small googlemap"></a>'+extra_html);

	jQuery(document).on('click', '.header-custom-posts a:not(.active)', function() {
		jQuery('#form-loading-effect').fadeIn();
		jQuery('.header-custom-posts a').removeClass('active');
		jQuery(this).addClass('active');
		jQuery.ajax({
			type: 'POST',
			url: my_ajax.ajaxurl,
			data: {
				"action": "geodir_custom_posts",
				post_type: jQuery(this).text()
			},
			success: function(response) {
				jQuery('.header-search-form').html(response);

				if (jQuery("#header-when, #header-top-when").length) {
					jQuery("#header-when, #header-top-when, #listing-when").datepicker({
						dateFormat: 'yy-mm-dd',
						onClose: function(dateText, inst) {
							jQuery(".ui-datepicker").stop(false, true);
						},
						dayNamesMin: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'],
						showAnim: 'fadeIn',
						minDate: 0,
						onChangeMonthYear: function() {
							setTimeout(function() {
								jQuery(".search-calendar-container").width(jQuery(".search-calendar-options").outerWidth() + jQuery("#ui-datepicker-div").outerWidth() - 12);
								if (jQuery(window).width() < 767) {
									jQuery(".search-calendar-container").height(jQuery("#ui-datepicker-div").outerHeight() - 4 + 272);
								}
								else {
									jQuery(".search-calendar-container").height(jQuery("#ui-datepicker-div").outerHeight() - 4);
								}
								jQuery(".search-calendar-options").height(jQuery("#ui-datepicker-div").outerHeight() - 4);
							}, 50);
						},
						onSelect: function(dateText, inst) {

							// Page has been reloaded, if user tries to select new date then start all over again
							if (!$date_selected) {
								jQuery("#startrange").val('');
								jQuery("#startrange").val('');
							}

							var date1 = jQuery.datepicker.parseDate('yy-mm-dd', jQuery("#startrange").val());
							var date2 = jQuery.datepicker.parseDate('yy-mm-dd', jQuery("#endrange").val());
							if (!date1 || date2) {
								if (jQuery(".single-listing-info").length) {
									jQuery(".single-listing-info .single-listing-text .for-selected").html(jQuery(".single-listing-info .single-listing-text.small .per-night").html());
								}

								jQuery("#startrange").val(dateText);
								jQuery("#endrange").val("");


								// Start date is selected. Set min date to selected start date.
								jQuery(this).datepicker("option", "minDate", dateText);

								// Get date from datepicker
								startDate = dateText;

								// Set input field value
								jQuery("#header-when, #listing-when").val(startDate);

								// Set cookie
								jQuery.cookie('vh_selected_date', startDate, {
									path: '/'
								});

								// Set date range cookies
								jQuery.cookie('vh_startrange', dateText, {
									path: '/'
								});
								jQuery.cookie('vh_endrange', '', {
									path: '/'
								});

								// Keep open datepicker
								showDatePicker();
								jQuery(".ui-datepicker").css({
									'display': 'block'
								});
							}
							else {
								jQuery("#endrange").val(dateText);
								// jQuery("#header-when, #listing-when").datepicker('hide');

								// End date is selected. Set min date to "today"
								jQuery(this).datepicker("option", "minDate", 0);

								// Get date from datepicker
								endDate = dateText;

								// Format date
								var startformatted = $.datepicker.formatDate('d', new Date(startDate));
								var endformatted = $.datepicker.formatDate('d M', new Date(endDate));
								var year = endDate.split("-");

								// Set input field value
								jQuery("#header-when, #header-top-when, #listing-when").val(startformatted + " - " + endformatted + " " + year["0"]);
								dateRange = [];
								for (var d = new Date(startDate); d <= new Date(endDate); d.setDate(d.getDate() + 1)) {
									dateRange.push($.datepicker.formatDate('yy-mm-dd', d));
								}

								// Set cookie (Format: 30 - 32 Dec 2014)
								jQuery.cookie('vh_selected_date', startformatted + " - " + endformatted + " " + year["0"], {
									path: '/'
								});

								// Set date range cookies
								jQuery.cookie('vh_endrange', dateText, {
									path: '/'
								});

								// Reset daterange
								dateRange = [];

								// Remove click event
								jQuery('.vh_wrapper').off('click.calendar');

								var a_date = new Date(Date.parse(jQuery.cookie('vh_startrange')).toString('yyyy'), parseInt(Date.parse(jQuery.cookie('vh_startrange')).toString('M')) - 1, Date.parse(jQuery.cookie('vh_startrange')).toString('d'), 0, 0, 0, 0);
								var b_date = new Date(Date.parse(jQuery.cookie('vh_endrange')).toString('yyyy'), parseInt(Date.parse(jQuery.cookie('vh_endrange')).toString('M')) - 1, Date.parse(jQuery.cookie('vh_endrange')).toString('d'), 0, 0, 0, 0);

								if (jQuery(".single-listing-info").length) {
									jQuery(".single-listing-info .single-listing-text .for-selected").html(jQuery(".single-listing-info .single-listing-text.small .per-night").html() * (Math.round((b_date - a_date) / 1000 / 60 / 60 / 24) + 1));
								}

								// Hide datepicker
								hideDatePicker(jQuery(this).parent());
								jQuery(".ui-datepicker").css({
									'display': 'none'
								});

								$date_selected = false;
								$tmp_start_date = '';
								$tmp_end_date = '';
							}
							$date_selected = true;

							jQuery("td.highlighted").removeClass("highlighted");
						},
						beforeShowDay: function(date) {
							var date1, date2 = '';

							if ($tmp_start_date) {
								date1 = jQuery.datepicker.parseDate('yy-mm-dd', $tmp_start_date);
								date2 = jQuery.datepicker.parseDate('yy-mm-dd', $tmp_end_date);
							}
							else {
								date1 = jQuery.datepicker.parseDate('yy-mm-dd', jQuery("#startrange").val());
								date2 = jQuery.datepicker.parseDate('yy-mm-dd', jQuery("#endrange").val());
							}

							return [true, date1 && ((date.getTime() == date1.getTime()) || (date2 && date >= date1 && date <= date2)) ? "highlighted" : ""];
						},
						beforeShow: function(input) {

							// Add click event with delay
							setTimeout(function() {

								// When click anywhere check if it's not calendar.
								jQuery('.vh_wrapper').on("click.calendar", function(e) {

									// Remove click event
									hideDatePicker(jQuery('body'));
									jQuery(".ui-datepicker").css({
										'display': 'none'
									});
									$date_selected = false;
									$tmp_start_date = '';
									$tmp_end_date = '';
									jQuery('.vh_wrapper').off('click.calendar');
								});
							}, 200);

							// Set min date to "today"
							jQuery(this).datepicker("option", "minDate", 0);
							jQuery("#header-when, #header-top-when").datepicker("refresh");
						}
					});
				}

				if ((jQuery("#header-type").length || jQuery("#header-top-type").length || jQuery("#header-category").length || jQuery("#header-top-category").length) && my_ajax.geo_advanced_search == 'false') {
					jQuery("#header-type, #header-top-type, #header-category, #header-top-category").autocomplete({
						source: my_ajax.ajaxurl + "?action=vh_get_listing_category&vh_post_type=" + jQuery('#header-post-type').val(),
						minLength: 2, //search after two characters
						select: function(event, ui) {
							jQuery(this).parent().removeClass('triangle');
						},
						html: true,
						open: function() {
							jQuery(this).parent().find('.search-contract-container').removeClass('active').hide();
							jQuery(this).parent().addClass('triangle');
							jQuery(".ui-autocomplete").css({
								left: "-=7",
								top: "+=4"
							});
						}
					}).data("ui-autocomplete")._renderItem = function(ul, item) {
						return $("<li></li>")
							.data("item.autocomplete", item)
							.append("<a>" + item.label + "</a>")
							.appendTo(ul);
					};
				};

				if ((jQuery("#header-location").length || jQuery("#header-top-location").length) && my_ajax.geo_advanced_search == 'false' && my_ajax.autocomplete == '1') {
					jQuery("#header-location, #header-top-location").autocomplete({
						source: my_ajax.ajaxurl + "?action=vh_get_location_search_terms&vh_post_type=" + jQuery('#header-post-type').val(),
						minLength: 2, //search after two characters
						select: function(event, ui) {
							jQuery.cookie('vh_viewer_location', ui.item.value, {
								path: '/'
							});
							if (my_ajax.autosubmit == '1') {
								jQuery("#header-submit, #header-submit2").click();
							};
						},
						html: true,
						open: function() {
							jQuery(".ui-autocomplete:visible").css({
								left: "-=6"
							});
						}
					}).data("ui-autocomplete")._renderItem = function(ul, item) {
						return $("<li></li>")
							.data("item.autocomplete", item)
							.append("<a>" + item.label + "</a>")
							.appendTo(ul);
					};
				};

				if (jQuery.cookie('vh_selected_people') == null) {
					/*jino*/
					jQuery.cookie('vh_selected_people', '1 Adult/No Children', {
						path: '/'
					});
					jQuery("#header-people, #listing-people").val(jQuery.cookie('vh_selected_people'));
				}
				else {
					jQuery("#header-people, #listing-people").val(jQuery.cookie('vh_selected_people'));
				}

				if (jQuery.cookie('vh_selected_date') != null) {
					jQuery('#header-when').val(jQuery.cookie('vh_selected_date'));
				};

				if (jQuery('.header-input-container input[name=event]').length) {
					var d = new Date();
					var month = d.getMonth() + 1;
					var day = d.getDate();

					var date = d.getFullYear() + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;

					jQuery('.header-input-container input[name=event]').attr('placeholder', date);
				};

				return false;
			}
		});


	});

	jQuery('#geodir_accept_term_condition_row .geodir_button').click(function() {
		e.preventDefault();

		console.log('clicked');
	});

	jQuery(".geodir-map-filters.googlemap").live("click", function() {
		if (jQuery(this).hasClass("active")) {
			jQuery(".geodir-filter-container").hide();
		}
		else {
			jQuery(".geodir-filter-container").show();
		}
		jQuery(this).toggleClass("active");
		jQuery(".geodir-filter-container").css('left', '525px');
	})

	if (jQuery('.listing-gallery-carousel li').length >= 4) {
		jQuery(".listing-gallery-carousel-container").jcarousel({
			wrap: "circular"
		}).on('jcarousel:fullyvisiblein', 'li', function(event, carousel) {
			jQuery(".listing-gallery-carousel li").removeClass("active-left");
			jQuery(".listing-gallery-carousel li").removeClass("active");
			jQuery(".listing-gallery-carousel li").removeClass("active-right");
			carousel._target.addClass("active-left");
			carousel._target.next().addClass("active");
			carousel._target.next().next().addClass("active-right");
		});
	};

	jQuery(".listing-gallery-carousel li:nth-child(1)").addClass("active-left");
	jQuery(".listing-gallery-carousel li:nth-child(2)").addClass("active");
	jQuery(".listing-gallery-carousel li:nth-child(3)").addClass("active-right");

	jQuery(".listing-gallery-carousel li").each(function() {
		jQuery(this).css("width", jQuery(window).width() / 3);
	});

	jQuery(window).bind("debouncedresize", function() {
		jQuery(".listing-gallery-carousel li").each(function() {
			jQuery(this).css("width", jQuery(window).width() / 3);
		});

		jQuery(".featured-properties-row").each(function() {
			jQuery(this).css("width", jQuery(window).width() / 3);
		});
	});

	jQuery('.header-gallery-slider-next').click(function() {
		jQuery(".listing-gallery-carousel-container").jcarousel('scroll', '+=1');
		if (parseInt(jQuery(".header-gallery-counter").find(".max-items").html()) == parseInt(jQuery(".header-gallery-counter").find(".current-item").html())) {
			jQuery(".header-gallery-counter").find(".current-item").html(0);
		};
		jQuery(".header-gallery-counter").find(".current-item").html(parseInt(jQuery(".header-gallery-counter").find(".current-item").html()) + 1);
	});

	jQuery('.header-gallery-slider-prev').click(function() {
		jQuery(".listing-gallery-carousel-container").jcarousel('scroll', '-=1');
		if (parseInt(jQuery(".header-gallery-counter").find(".current-item").html()) == 1) {
			jQuery(".header-gallery-counter").find(".current-item").html(parseInt(jQuery(".header-gallery-counter").find(".max-items").html()) + 1);
		};
		jQuery(".header-gallery-counter").find(".current-item").html(parseInt(jQuery(".header-gallery-counter").find(".current-item").html()) - 1);
	});

	jQuery(".stick_trigger_container .listing-item-exitfullscreen:not(.streetview)").live('click', function() {
		if (!jQuery("body").hasClass("geodir-main-search")) {
			if (!jQuery(this).hasClass("active")) {
				var content_width = jQuery("#geodir-wrapper").outerWidth();
				var browser_width = jQuery(window).width();
				if (jQuery("body").hasClass("admin-bar")) {
					var browser_height = jQuery(window).height() - 32;
				}
				else {
					var browser_height = jQuery(window).height();
				}
				if (jQuery("body").hasClass("skyestate") || jQuery("body").hasClass("skyvacation")) {
					var scroll_to = 70 + 611 + 70;
				}
				else {
					var scroll_to = 70 + 611;
				}
				window.scrollTo(0, scroll_to);

				jQuery(".stick_trigger_container").css({
					"width": browser_width + "px",
					"height": browser_height + "px",
					"left": "-" + parseInt(jQuery(".single-listing-sidebar").offset().left) + "px",
					"position": "relative"
				});
				jQuery(".geodir_marker_cluster").css({
					"width": browser_width,
					"height": browser_height + "px",
					"left": "0",
					"position": "relative"
				});
				jQuery(".geodir_map_container, #home_map_canvas_wrapper, #geodir_map_v3_home_map_100_wrapper, #geodir_map_v3_home_map_101_wrapper, #geodir_map_v3_home_map_102_wrapper").css({
					"width": browser_width,
					"height": browser_height + "px"
				});
				jQuery(".single-listing-by").css({
					"margin-top": browser_height + "px"
				});
				jQuery(".single-listing-sidebar > .google-map-main").hide();
				jQuery(this).addClass('icon-resize-small')

				var position = jQuery.goMap.map.getCenter();
				google.maps.event.trigger(jQuery.goMap.map, "resize");

				jQuery.goMap.map.setCenter(position);

				jQuery(this).addClass("active");
				jQuery(this).next().addClass('icon-resize-small');
			}
			else {
				var content_width = jQuery("#geodir-wrapper").outerWidth();
				var browser_width = jQuery(window).width();
				if (jQuery("body").hasClass("admin-bar")) {
					var browser_height = jQuery(window).height() - 32;
				}
				else {
					var browser_height = jQuery(window).height();
				}
				if (jQuery("body").hasClass("skyestate")) {
					var scroll_to = 70 + 611 + 70;
				};
				window.scrollTo(0, scroll_to);

				jQuery(".stick_trigger_container").css({
					"width": (browser_width - jQuery(".single-listing-sidebar").offset().left) + "px",
					"height": "425px",
					"left": "0",
					"position": "relative"
				});
				jQuery(".geodir_marker_cluster").css({
					"width": (browser_width - jQuery(".single-listing-sidebar").offset().left) + "px",
					"height": "425px",
					"left": "0",
					"position": "relative"
				});
				jQuery(".geodir_map_container, #home_map_canvas_wrapper, #geodir_map_v3_home_map_100_wrapper, #geodir_map_v3_home_map_101_wrapper, #geodir_map_v3_home_map_102_wrapper").css({
					"width": (browser_width - jQuery(".single-listing-sidebar").offset().left) + "px",
					"height": "425px"
				});
				jQuery(".single-listing-by").css({
					"margin": "0"
				});
				jQuery(".single-listing-sidebar > .google-map-main").show();
				jQuery(this).removeClass('icon-resize-small');

				var position = jQuery.goMap.map.getCenter();
				google.maps.event.trigger(jQuery.goMap.map, "resize");

				jQuery.goMap.map.setCenter(position);

				jQuery(this).removeClass("active");
				jQuery(this).next().removeClass('icon-resize-small');
			}
			jQuery(this).toggleClass("active");
		};
	});

	jQuery(".stick_trigger_container .listing-item-exitfullscreen").live('click', function() {
		if (!jQuery("body").hasClass("geodir-main-search")) {
			if (!jQuery(this).hasClass("active")) {

				if (jQuery(this).hasClass('streetview')) {
					var position = new google.maps.LatLng(vh_main.post_info['0']['post_latitude'], vh_main.post_info['0']['post_longitude']);
					var panorama = jQuery.goMap.map.getStreetView();
					panorama.setPosition(position);
					panorama.setPov({
						heading: 45,
						pitch: -10
					});
					panorama.setVisible(true);

					// jQuery.goMap.setMap({addressControlOptions: {position: 'TOP_RIGHT'}});
				};

			}
			else {

				if (jQuery(this).hasClass('streetview')) {
					var position = jQuery.goMap.map.getCenter();
					var panorama = jQuery.goMap.map.getStreetView();
					panorama.setVisible(false);

					// jQuery.goMap.setMap({streetViewControl: false});
				};

			}
			jQuery(this).toggleClass("active");
		};
	});

	if (jQuery('body').hasClass('single-geodir-page')) {
		jQuery('.stick_trigger_container').append('<a href="javascript:void(0)" class="listing-item-exitfullscreen icon-resize-full googlemap"></a>');
		jQuery('.stick_trigger_container').append('<a href="javascript:void(0)" class="listing-item-exitfullscreen icon-resize-full googlemap streetview"></a>');
	};

	jQuery('.popular-destinations-container.half-right, .popular-destinations-container.half-left').hover(function() {
		jQuery(this).find('img').first().stop().animate({
			'margin-left': '0'
		}, 200, function() {});
	}, function() {
		jQuery(this).find('img').first().animate({
			'margin-left': '-29px'
		}, 200, function() {});
	});

	jQuery('.featured-properties-container.animation').hover(function() {
		jQuery(this).find('img').first().stop().animate({
			'margin-left': '0'
		}, 200, function() {});
	}, function() {
		jQuery(this).find('img').first().animate({
			'margin-left': '-29px'
		}, 200, function() {});
	});

	jQuery(".reply-count").click(function() {
		if (jQuery(this).parent().parent().parent().hasClass("active")) {
			jQuery(this).parent().parent().parent().find("ul.children").first().fadeOut(300, function() {
				jQuery(this).parent().removeClass("active");
			});
		}
		else {
			jQuery(this).parent().parent().parent().addClass("active");
			jQuery(this).parent().parent().parent().find("ul.children").first().fadeIn(300);
		}
	});

	jQuery(".add-review-container a, .comment-reply-link").click(function(e) {
		e.preventDefault();
		var comment_title = jQuery("#respond #reply-title").html().split("<small>");
		jQuery("#respond").dialog({
			modal: true,
			width: 678,
			resizable: false,
			dialogClass: "main-dialog",
			title: comment_title["0"],
			position: {
				my: "center center",
				at: "center center"
			},
			close: function() {
				jQuery(this).dialog('destroy');
			},
			create: function() {
				jQuery(".ui-dialog-title").append(" <span>" + jQuery("#listing-name").val() + "</span>");

			}
		});

		jQuery(".form-submit #submit").addClass("wpb_button wpb_btn-primary wpb_btn-small");
		if (jQuery(".form-submit").find(".close-dialog").length == 0) {
			jQuery(".form-submit").append("<a href=\"javascript:void(0)\" class=\"close-dialog wpb_button wpb_btn-inverse wpb_btn-small\">Cancel</a>");
		};
	});

	jQuery('.comment-reply-link').click(function() {
		jQuery('.review-container').hide();
		jQuery(".ui-dialog-title").html('Leave a reply on' + " <span>" + jQuery("#listing-name").val() + "</span>");
	});

	jQuery('.add-review-container > a').click(function() {
		jQuery('.review-container').show();
	});

	jQuery(".close-dialog").live("click", function() {
		jQuery("#respond").dialog('destroy');
	})

	jQuery(".google-map-container").hover(function() {
		var marker = jQuery(this)["0"].id.split("-");
		jQuery("div[id^='geo-marker-']").removeClass("hovered-list");
		jQuery("#geo-marker-" + marker["3"] + "").addClass("hovered-list");
	}, function() {
		jQuery("div[id^='geo-marker-']").removeClass("hovered-list");
	});

	jQuery("div[id^='geo-marker-']").live("mouseover", function() {
		var marker = jQuery(this)["0"].id.split("-");
		jQuery("#list-geo-marker-" + marker["2"] + "").addClass("map-hover");
	});

	jQuery("div[id^='geo-marker-']").live("mouseout", function() {
		var marker = jQuery(this)["0"].id.split("-");
		jQuery(".google-map-container").removeClass("map-hover");
	});

	jQuery(window).bind("debouncedresize", function() {
		vh_resize_carousel_images();
		vh_resize_header_video();

		jQuery(".google-map-main").width(((jQuery(window).width() - 1200) / 2) + jQuery('.single-listing-sidebar').width() + 57);
	});

	vh_resize_carousel_images();

	jQuery(".google-map-main").width(((jQuery(window).width() - 1200) / 2) + jQuery('.single-listing-sidebar').width() + 57);

	function vh_resize_carousel_images() {
		if (jQuery(window).width() > 1200) {
			jQuery(".featured-properties-row, .blog-carousel .blog-row").css({
				"max-width": '1170px',
				"min-width": '1170px',
				"width": '1170px'
			});
			jQuery('.featured-properties-row .featured-properties-inner-carousel img').css('width', 'auto');
		}
		else if (jQuery(window).width() < 1200) {
			jQuery(".featured-properties-row, .blog-carousel .blog-row").css({
				"max-width": jQuery(window).width() - 30,
				"min-width": jQuery(window).width() - 30,
				"width": jQuery(window).width() - 30
			});
			var image_width = jQuery('.featured-properties-row .featured-properties-inner-carousel-container').outerWidth();
			jQuery('.featured-properties-row .featured-properties-inner-carousel img').css('width', image_width);
			jQuery('.featured-properties-row .featured-properties-container .featured-properties-inner-carousel img').css('width', parseInt(image_width + (image_width * 0.25)));
			jQuery('.featured-properties-row .featured-properties-container.wide .featured-properties-inner-carousel img').css('width', parseInt(image_width + (image_width * 1.5)));
			if (jQuery('.featured-properties-inner-carousel-container').length) {
				jQuery('.featured-properties-inner-carousel-container').jcarousel();
				jQuery('.featured-properties-inner-carousel-container').jcarousel('reload', {
					'animation': 'fast'
				});
				jQuery('.featured-properties-inner-carousel-container').jcarousel('scroll', '0');
			};
		}
		else {
			jQuery(".featured-properties-row, .blog-carousel .blog-row").css({
				"max-width": "1170px",
				"min-width": "1170px",
				"width": jQuery(window).width() - 30
			});
		}
	}

	if (jQuery('#blog_post_container').length) {
		get_blog_posts(jQuery('#blog_posts_categories').val(), jQuery('#blog_posts_limit').val(), '1', my_ajax.ajaxurl);
	}

	jQuery('.geodir_package .geodir-select-package').click(function(e) {
		e.preventDefault();
		jQuery(this).next().prop("checked", true).click();
	});

	function vh_resize_header_video() {
		var browser_width = jQuery(window).width();
		var video_height = jQuery('#main_header_bg video').outerHeight();

		jQuery('#main_header_bg video').css({
			'width': browser_width + 'px',
			'height': 'auto'
		});
		if (video_height < '747') {
			jQuery('#main_header_bg video').css('display', 'none');
		}
		else {
			jQuery('#main_header_bg video').css('display', 'block');
		}
	}

	function vh_getUrlParameter(sParam) {
		var sPageURL = window.location.search.substring(1);
		var sURLVariables = sPageURL.split('&');
		for (var i = 0; i < sURLVariables.length; i++) {
			var sParameterName = sURLVariables[i].split('=');
			if (sParameterName[0] == sParam) {
				return sParameterName[1];
			}
		}
	}

	if (jQuery('body').hasClass('geodirectory-payment-manager')) {

		if (vh_getUrlParameter('upgrade') != null && vh_getUrlParameter('pid') != null) {
			jQuery('.geodir_add_listing_fields, #geodir_coupon_code_row, #NotRecurring_sh, #propertyform h5, #gdfi_import_url, #gd_facebook_import, .gd-start-edate, .gd-event-end-div, .gd-all-day, .gd-event-time-row').hide();
			jQuery('#gd_recurring_span').parent().parent().hide();
			jQuery('.geodir_price_package_row').show();
		}
		else if (vh_getUrlParameter('pid') != null) {
			jQuery('.geodir_add_listing_fields, #geodir_coupon_code_row, #NotRecurring_sh, #propertyform h5, #gdfi_import_url, #gd_facebook_import, .gd-start-edate, .gd-event-end-div, .gd-all-day, .gd-event-time-row').show();
			jQuery('#gd_recurring_span').parent().parent().show();
			jQuery('.geodir_price_package_row').hide();
		}
		else if (vh_getUrlParameter('package_id') != null) {
			jQuery('.geodir_add_listing_fields, #geodir_coupon_code_row, #NotRecurring_sh, #propertyform h5, #gdfi_import_url, #gd_facebook_import, .gd-start-edate, .gd-event-end-div, .gd-all-day, .gd-event-time-row').show();
			jQuery('#gd_recurring_span').parent().parent().show();
			jQuery('.geodir_price_package_row').hide();
		}
		else {
			jQuery('.geodir_add_listing_fields, #geodir_coupon_code_row, #NotRecurring_sh, #propertyform h5, #gdfi_import_url, #gd_facebook_import, .gd-start-edate, .gd-event-end-div, .gd-all-day, .gd-event-time-row').hide();
			jQuery('#gd_recurring_span').parent().parent().hide();
			jQuery('.geodir_price_package_row').show();
		}
	}
	else {
		jQuery('.geodir_price_package_row').hide();
	}

	jQuery('.featured-carousel-controls .delete-listing').click(function() {
		if (confirm('Are you sure you want to permanently deleted this listing?')) {
			return true;
		}
		else {
			return false;
		}
	});

	function get_blog_posts(cat, limit, posts, ajaxurl) {
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				"action": "blog_posts",
				categories: cat,
				post_limit: limit,
				p_count: posts
			},
			success: function(response) {
				jQuery("#blog_post_container").addClass('loaded');
				jQuery("#blog_post_container").html(response);
				jQuery("#load_blog_posts").removeClass("disabled");
				jQuery("#load_blog_posts").parent().removeClass("loading");
				return false;
			}
		});
	}

	jQuery('.geodir_add_listing_fields .geodir_button').click(function() {
		jQuery(this).parent().parent().submit();
	});

	jQuery("#load_blog_posts").click(function() {
		if (!jQuery(this).hasClass('disabled')) {
			jQuery("#posts_to_load").val(parseInt(jQuery("#posts_to_load").val()) + 1);
			get_blog_posts(jQuery('#blog_posts_categories').val(), jQuery('#blog_posts_limit').val(), jQuery("#posts_to_load").val(), my_ajax.ajaxurl);
			jQuery(this).parent().addClass('loading');
		};
		jQuery(this).addClass("disabled");

	})

	jQuery(".popular-destinations-container.city").hover(function() {
		jQuery(this).find(".city-text").show().animate({
			opacity: 1
		}, 150, function() {
			// Animation complete
		});
	}, function() {
		jQuery(this).find(".city-text").animate({
			opacity: 0
		}, 150, function() {
			jQuery(this).hide();
		});
	});

	if (jQuery("body").hasClass("page-template-template-frontpage-php") && !jQuery('body').hasClass('geodir_disabled')) {
		if (!jQuery.cookie('vh_user_location') && my_ajax.disable_geoloc == '0') {
			if (jQuery('body').hasClass('geolocation_on')) {
				vh_geolocate_user();
			}
			else {
				vh_get_country_picture();
			}
		}
		else if (!jQuery.cookie('vh_user_location') && my_ajax.disable_geoloc == '1') {
			jQuery.cookie('vh_user_location', my_ajax.def_country + "/" + my_ajax.def_city, {
				path: '/'
			});
		}
		else {
			if (jQuery.cookie('vh_user_tracking') == 'denied') {
				vh_geolocate_user();
			};
		}
	};

	function vh_geolocate_user() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {

				var geocoder = new google.maps.Geocoder();
				var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

				geocoder.geocode({
					'latLng': latlng
				}, function(results, status) {
					// if user successfully tracked
					if (status == google.maps.GeocoderStatus.OK) {
						var country_val = '';
						var city_val = '';
						jQuery.each(results, function(key, value) {
							if (jQuery.inArray("country", value.types) != '-1') {
								country_val = value.formatted_address;
								jQuery("#current_country").html(country_val);
							}
							else if (jQuery.inArray("locality", value.types) != '-1') {
								city_val = value.address_components['0']['short_name'];
								jQuery("#current_city").html(city_val);
							};
						});

						jQuery.cookie('vh_user_location', country_val + "/" + city_val, {
							path: '/'
						});
						jQuery.cookie('vh_user_tracking', 'allowed', {
							path: '/'
						});

						if (!jQuery('body').hasClass('header_search_map') || my_ajax.header_map == 'false') {
							jQuery.ajax({
								type: 'POST',
								url: my_ajax.ajaxurl,
								data: {
									"action": "get_header_bg",
									country: country_val
								},
								success: function(response) {
									jQuery("#main_header_bg").html(response);
									return false;
								}
							});

							jQuery.ajax({
								type: 'POST',
								url: my_ajax.ajaxurl,
								data: {
									"action": "get_header_bg-information",
									c_country: country_val,
									c_city: city_val
								},
								success: function(response) {
									jQuery("#current_listing_count").html(response);
									jQuery("#current_listing_count").show();
									return false;
								}
							});
						};

					}
				});
			}, function(error) {
				// if user denies tracking
				if (error.code == '1') {
					jQuery.cookie('vh_user_location', my_ajax.def_country + "/" + my_ajax.def_city, {
						path: '/'
					});
					jQuery.cookie('vh_user_tracking', 'denied', {
						path: '/'
					});
				};
			});
		}
		else {
			console.log("Geolocation services are not supported by your browser.");
		}
	}

	function vh_get_country_picture() {
		var def_country = my_ajax.def_country;
		if (def_country == '') {
			def_country = 'France';
		};
		var def_city = my_ajax.def_city;
		if (def_city == '') {
			def_city = 'Paris';
		};

		jQuery.cookie('vh_user_location', def_country + "/" + def_city, {
			path: '/'
		});

		if (my_ajax.header_map == 'false') {
			jQuery.ajax({
				type: 'POST',
				url: my_ajax.ajaxurl,
				data: {
					"action": "get_header_bg",
					country: def_country
				},
				success: function(response) {
					jQuery("#main_header_bg").html(response);
					return false;
				}
			});

			jQuery.ajax({
				type: 'POST',
				url: my_ajax.ajaxurl,
				data: {
					"action": "get_header_bg-information",
					c_country: def_country,
					c_city: def_city
				},
				success: function(response) {
					jQuery("#current_listing_count").html(response);
					jQuery("#current_listing_count").show();
					return false;
				}
			});
		};
	}

	if (jQuery("#header-range-price").length) {
		jQuery("#header-range-price").slider({
			range: true,
			min: 0,
			max: jQuery("#header-more-max-price").val(),
			values: [0, jQuery("#header-more-max-price").val()],
			slide: function(event, ui) {
				jQuery(this).parent().find(".range-slider-min").html(my_ajax.currency_symbol + ui.values[0]);
				jQuery(this).parent().find(".range-slider-max").html(my_ajax.currency_symbol + ui.values[1]);
			},
			start: function(event, ui) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").addClass("ui-active");
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").addClass("ui-changed");
			},
			stop: function(event, ui) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").removeClass("ui-active");
			}
		});
	};

	if (jQuery("#header-range-area").length) {
		jQuery("#header-range-area").slider({
			range: true,
			min: 1,
			max: 500,
			values: [1, 500],
			slide: function(event, ui) {
				jQuery(this).parent().find(".range-slider-min").html(ui.values[0]);
				jQuery(this).parent().find(".range-slider-max").html(ui.values[1]);
			},
			start: function(event, ui) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").addClass("ui-active");
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").addClass("ui-changed");
			},
			stop: function(event, ui) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").removeClass("ui-active");
			}
		});
	};

	if (jQuery("#header-range-rooms").length) {
		jQuery("#header-range-rooms").slider({
			range: true,
			min: 1,
			max: 10,
			values: [1, 10],
			slide: function(event, ui) {
				jQuery(this).parent().find(".range-slider-min").html(ui.values[0]);
				jQuery(this).parent().find(".range-slider-max").html(ui.values[1]);
			},
			start: function(event, ui) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").addClass("ui-active");
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").addClass("ui-changed");
			},
			stop: function(event, ui) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").removeClass("ui-active");
			}
		});
	};

	if (jQuery("#header-range-bathrooms").length) {
		jQuery("#header-range-bathrooms").slider({
			range: true,
			min: 1,
			max: 3,
			values: [1, 3],
			slide: function(event, ui) {
				jQuery(this).parent().find(".range-slider-min").html(ui.values[0]);
				jQuery(this).parent().find(".range-slider-max").html(ui.values[1]);
			},
			start: function(event, ui) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").addClass("ui-active");
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").addClass("ui-changed");
			},
			stop: function(event, ui) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").removeClass("ui-active");
			}
		});
	};

	if (jQuery("#header-range-bedrooms").length) {
		jQuery("#header-range-bedrooms").slider({
			range: true,
			min: 1,
			max: 5,
			values: [1, 5],
			slide: function(event, ui) {
				jQuery(this).parent().find(".range-slider-min").html(ui.values[0]);
				jQuery(this).parent().find(".range-slider-max").html(ui.values[1]);
			},
			start: function(event, ui) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").addClass("ui-active");
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").addClass("ui-changed");
			},
			stop: function(event, ui) {
				jQuery(this).parent().find(".ui-slider-range.ui-widget-header").removeClass("ui-active");
			}
		});
	};

	jQuery(".header-more-button").live('click', function() {
		if (jQuery(this).hasClass("active")) {
			jQuery("#header-more-options").hide().css('opacity', '0');
			jQuery(this).html('More options');
			jQuery(this).addClass('icon-angle-down');
			jQuery(this).removeClass('icon-angle-up');
		}
		else {
			jQuery("#header-more-options").show().css('opacity', '1');
			jQuery(this).html('Close options');
			jQuery(this).removeClass('icon-angle-down');
			jQuery(this).addClass('icon-angle-up');
		}
		jQuery(this).toggleClass('active');
	})

	// jQuery("body.single-post .comment-form-email input, body.single-post .comment-form-comment textarea, .wpcf7-form-control-wrap input").focus(function() {
	// 	jQuery(this).parent().addClass("focused");
	// })

	// jQuery("body.single-post .comment-form-email input, body.single-post .comment-form-comment textarea, .wpcf7-form-control-wrap input").focusout(function() {
	// 	jQuery(this).parent().removeClass("focused");
	// })

	// jQuery("body.single-post .comment-form-email input, body.single-post .comment-form-comment textarea, .wpcf7-form-control-wrap input").hover(function() {
	// 	jQuery(this).parent().addClass("hovered");
	// }, function() {
	// 	jQuery(this).parent().removeClass("hovered");
	// })

	jQuery(".review-container .star-icon").hover(function() {
		if (jQuery(this)["0"].id == "gd_star1") {
			jQuery("#gd_star1, #gd_star2, #gd_star3, #gd_star4, #gd_star5").removeClass("active icon-star");
			jQuery("#gd_star1").addClass("active icon-star");
		}
		else if (jQuery(this)["0"].id == "gd_star2") {
			jQuery("#gd_star1, #gd_star2, #gd_star3, #gd_star4, #gd_star5").removeClass("active icon-star");
			jQuery("#gd_star1, #gd_star2").addClass("active icon-star");
		}
		else if (jQuery(this)["0"].id == "gd_star3") {
			jQuery("#gd_star1, #gd_star2, #gd_star3, #gd_star4, #gd_star5").removeClass("active icon-star");
			jQuery("#gd_star1, #gd_star2, #gd_star3").addClass("active icon-star");
		}
		else if (jQuery(this)["0"].id == "gd_star4") {
			jQuery("#gd_star1, #gd_star2, #gd_star3, #gd_star4, #gd_star5").removeClass("active icon-star");
			jQuery("#gd_star1, #gd_star2, #gd_star3, #gd_star4").addClass("active icon-star");
		}
		else if (jQuery(this)["0"].id == "gd_star5") {
			jQuery("#gd_star1, #gd_star2, #gd_star3, #gd_star4, #gd_star5").removeClass("active icon-star");
			jQuery("#gd_star1, #gd_star2, #gd_star3, #gd_star4, #gd_star5").addClass("active icon-star");
		}
	}, function() {
		jQuery("#gd_star1, #gd_star2, #gd_star3, #gd_star4, #gd_star5").removeClass("active icon-star");
	});

	jQuery(".review-container .star-icon").click(function() {
		if (jQuery(this)["0"].id == "gd_star1") {
			jQuery("#gd_star1, #gd_star2, #gd_star3, #gd_star4, #gd_star5").removeClass("clicked");
			jQuery("#gd_star1").addClass("clicked");
			jQuery(this).parent().parent().find("#geodir_overallrating").val(1);
			jQuery(this).parent().find("#geodir_overallrating").val(1);
		}
		else if (jQuery(this)["0"].id == "gd_star2") {
			jQuery("#gd_star1, #gd_star2, #gd_star3, #gd_star4, #gd_star5").removeClass("clicked");
			jQuery("#gd_star1, #gd_star2").addClass("clicked");
			jQuery(this).parent().parent().find("#geodir_overallrating").val(2);
			jQuery(this).parent().find("#geodir_overallrating").val(2);
		}
		else if (jQuery(this)["0"].id == "gd_star3") {
			jQuery("#gd_star1, #gd_star2, #gd_star3, #gd_star4, #gd_star5").removeClass("clicked");
			jQuery("#gd_star1, #gd_star2, #gd_star3").addClass("clicked");
			jQuery(this).parent().parent().find("#geodir_overallrating").val(3);
			jQuery(this).parent().find("#geodir_overallrating").val(3);
		}
		else if (jQuery(this)["0"].id == "gd_star4") {
			jQuery("#gd_star1, #gd_star2, #gd_star3, #gd_star4, #gd_star5").removeClass("clicked");
			jQuery("#gd_star1, #gd_star2, #gd_star3, #gd_star4").addClass("clicked");
			jQuery(this).parent().parent().find("#geodir_overallrating").val(4);
			jQuery(this).parent().find("#geodir_overallrating").val(4);
		}
		else if (jQuery(this)["0"].id == "gd_star5") {
			jQuery("#gd_star1, #gd_star2, #gd_star3, #gd_star4, #gd_star5").removeClass("clicked");
			jQuery("#gd_star1, #gd_star2, #gd_star3, #gd_star4, #gd_star5").addClass("clicked");
			jQuery(this).parent().parent().find("#geodir_overallrating").val(5);
			jQuery(this).parent().find("#geodir_overallrating").val(5);
		}
	})

	if (jQuery.cookie('vh_viewer_location') == null && my_ajax.disable_geoloc == '0') {
		if (jQuery('body').hasClass('geolocation_on')) {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(function(position) {

					var geocoder = new google.maps.Geocoder();
					var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

					geocoder.geocode({
						'latLng': latlng
					}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							jQuery.cookie('vh_viewer_location', results[1].address_components[1].short_name, {
								path: '/'
							});
							jQuery("#header-location, #header-top-location").val(jQuery.cookie('vh_viewer_location'));
						}
					});
				});
			}
			else {
				console.log("Geolocation services are not supported by your browser.");
			}
		}
		else {
			jQuery.cookie('vh_viewer_location', my_ajax.def_city, {
				path: '/'
			});
			jQuery("#header-location, #header-top-location").val(my_ajax.def_city);
		}
	}
	else {
		jQuery("#header-location, #header-top-location").val(jQuery.cookie('vh_viewer_location'));
	}

	jQuery("#header-when, #header-top-when, #listing-when").live('click', function() {
		jQuery("#ui-datepicker-div").removeClass("header-when header-top-when listing-when");
		jQuery("#ui-datepicker-div").addClass(jQuery(this)["0"]["id"]);

		jQuery(this).parent().find(".search-calendar-container").find(".search-calendar-options").show();
		jQuery(this).parent().find(".search-calendar-container").show();
		jQuery(this).parent().find(".search-calendar-container").width(jQuery(".search-calendar-options").outerWidth() + jQuery("#ui-datepicker-div").outerWidth() - 12);
		if (jQuery(window).width() < 767) {
			jQuery(this).parent().find(".search-calendar-container").height(jQuery("#ui-datepicker-div").outerHeight() - 4 + 272);
		}
		else {
			jQuery(this).parent().find(".search-calendar-container").height(jQuery("#ui-datepicker-div").outerHeight() - 4);
		}
		jQuery(this).parent().find(".search-calendar-container").find(".search-calendar-options").height(jQuery("#ui-datepicker-div").outerHeight() - 4);
		jQuery(this).parent().find(".search-calendar-container").find(".search-calendar-options").animate({
			opacity: 1
		}, 65, function() {
			// Animation complete
		});
	});

	jQuery("#header-people, #listing-people").live('click', function() {
		jQuery(this).parent().find(".search-people-container").show();
		jQuery(this).parent().find(".search-people-container").animate({
			opacity: 1
		}, 150, function() {
			jQuery(this).parent().find(".search-people-container").addClass("active");
		});

	});

	jQuery("#header-contract, #header-top-contract").live('click', function() {
		jQuery(this).parent().find(".search-contract-container").show();
		jQuery(this).parent().find(".search-contract-container").animate({
			opacity: 1
		}, 150, function() {
			jQuery(this).parent().find(".search-contract-container").addClass("active");
		});
	});

	jQuery("#header-type, #header-top-type, #header-category, #header-top-category").live('click', function() {
		jQuery(this).parent().find(".search-contract-container").show();
		jQuery(this).parent().find(".search-contract-container").animate({
			opacity: 1
		}, 150, function() {
			jQuery(this).parent().find(".search-contract-container").addClass("active");
		});
	});

	jQuery(document).mouseup(function(e) {
		var container = jQuery(".search-people-container.active");

		if (!container.is(e.target) && container.has(e.target).length === 0) {
			container.hide();
			container.removeClass("active");
		}
	});

	jQuery(document).mouseup(function(e) {
		var container = jQuery(".header-login-main");

		if (!container.is(e.target) && container.has(e.target).length === 0) {
			container.fadeOut(150);
			jQuery(".header-login-button").fadeIn(150);
		}
	});

	jQuery(".calendar-people-item").live('click', function() {
		var input = jQuery("#header-people, #listing-people").val();
		var input_value = input.split("/");

		if (input == "") {
			if (jQuery(this).parent().is(".search-people-adults")) {
				jQuery("#header-people, #listing-people").val(jQuery(this).find("span").html() + "/");
			}
			else {
				jQuery("#header-people, #listing-people").val("/" + jQuery(this).find("span").html());
			}
		}
		else if (jQuery(this).parent().is(".search-people-adults")) {
			if (input_value["0"] == "") {
				jQuery("#header-people, #listing-people").val(jQuery(this).find("span").html() + input);
			}
			else {
				jQuery.cookie('vh_selected_people', jQuery(this).find("span").html() + "/" + input_value["1"], {
					path: '/'
				});
				jQuery("#header-people, #listing-people").val(jQuery(this).find("span").html() + "/" + input_value["1"]);

			}
		}
		else {
			if (input_value["1"] == "") {
				jQuery("#header-people, #listing-people").val(input + jQuery(this).find("span").html());
			}
			else {
				jQuery.cookie('vh_selected_people', input_value["0"] + "/" + jQuery(this).find("span").html(), {
					path: '/'
				});
				jQuery("#header-people, #listing-people").val(input_value["0"] + "/" + jQuery(this).find("span").html());
			}
		}

		var current_input_value = jQuery("#header-people, #listing-people").val().split("/");

		if (current_input_value["0"] != "" && current_input_value["1"] != "") {
			jQuery(".search-people-container").animate({
				opacity: 0
			}, 150, function() {
				jQuery(".search-people-container").hide();
				jQuery(".search-people-container").removeClass("active");
			});
		};
	});

	jQuery(".calendar-contract-item").live('click', function() {
		if (jQuery(this).parent().parent().hasClass('category')) {
			jQuery("#header-type, #header-top-type, #header-category, #header-top-category").val(jQuery(this).find("span").html());
		}
		else {
			jQuery("#header-contract, #header-top-contract").val(jQuery(this).find("span").html())
		}

		jQuery(".search-contract-container").animate({
			opacity: 0
		}, 150, function() {
			jQuery(".search-contract-container").hide();
			jQuery(".search-contract-container").removeClass("active");
		});
	});

	if (jQuery('.header-input-container input[name=event]').length) {
		var d = new Date();
		var month = d.getMonth() + 1;
		var day = d.getDate();

		var date = d.getFullYear() + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;

		jQuery('.header-input-container input[name=event]').attr('placeholder', date);
	};


	jQuery("#header-submit, #header-submit2").live('click', function(e) {

		var geocoder = new google.maps.Geocoder();
		var address = jQuery("#header-location, #header-top-location").val();
		// var when = jQuery("#header-when, #header-top-when").val();
		var people = jQuery("#header-people").val();
		var keyword = jQuery("#header-keyword, #header-top-keyword").val();
		var category = jQuery("#header-category, #header-top-category").val();
		var type = jQuery("#header-type, #header-top-type").val();
		var contract = jQuery("#header-contract, #header-top-contract").val();
		var post_type = jQuery("#header-post-type").val();

		if (jQuery("body").hasClass("skyvacation") && jQuery("#header-people").length) {

			var people_array = people.split("/");
			var adults = people_array["0"].split(" ");
			var childrens = people_array["1"].split(" ");
		};
		var height = 0;
		if (jQuery('#endrange').val() == '') {
			if (jQuery('#startrange').val() != '') {
				var when = jQuery('#startrange').val();
			}
			else {
				var when = jQuery('#header-when').val();
			}
		}
		else {
			var when = jQuery('#startrange').val() + "~" + jQuery('#endrange').val();

		}

		if (address != undefined) {
			jQuery.cookie('vh_viewer_location', address, {
				path: '/'
			});
		};

		if (post_type == '') {
			post_type = 'gd_place';

		};

		if (address == undefined) {
			address = jQuery.cookie('vh_viewer_location');
		};

		geocoder.geocode({
			'address': address
		}, function(results, status) {

			if (status == google.maps.GeocoderStatus.OK) {
				var latitude = results[0].geometry.location.lat();
				var longitude = results[0].geometry.location.lng();
			}

			if (jQuery('.header-input-container.event').length) {
				var location = "?geodir_search=1&stype=" + post_type + "&s=+&snear=" + address + "&sgeo_lat=" + latitude + "&sgeo_lon=" + longitude + "&sgeo_keyword=" + keyword;

				window.location.href = my_ajax.blog_url + location;
			}
			else if (jQuery('.header-input-container.advanced').length) {
				var advanced_location = '';
				jQuery('.header-input-container.advanced').each(function() {
					if (jQuery(this).find('input').val() === undefined) {
						// Non-inputs
						if (jQuery(this).find('textarea').length) {
							if (jQuery(this).find('textarea').val() != '') {
								advanced_location += '&' + jQuery(this).find('textarea').attr('name') + '=' + encodeURIComponent(jQuery(this).find('textarea').val());
							};
						};
						// Select
						if (jQuery(this).find('select').length) {
							advanced_location += '&' + jQuery(this).find('select').attr('name') + '=' + encodeURIComponent(jQuery(this).find('select').val());
						};
					}
					else {
						// Inputs
						var input_type = jQuery(this).find('input').attr('type');
						switch (input_type) {
							case 'text':
								// Datepicker
								if (jQuery(this).find('input').hasClass('hasDatepicker')) {
									if (jQuery(this).find('#endrange').val() == '') {
										var date_selected = jQuery(this).find('#startrange').val();
									}
									else {
										var date_selected = jQuery(this).find('#startrange').val() + '~' + jQuery(this).find('#endrange').val();
									}

									if (date_selected != '') {
										advanced_location += '&' + jQuery(this).find('input').attr('name') + '=' + date_selected;
									};
								}
								else if (jQuery(this).find('input').parent().hasClass('chosen-search')) {
									// single-select
									if (jQuery(this).find('select option:selected').val() != '') {
										if (jQuery(this).find('select').attr('name') != 'gd_placecategory') {
											advanced_location += '&' + jQuery(this).find('select').attr('name') + '=' + jQuery(this).find('select option:selected').val();
										}
										else {
											advanced_location += '&sgeo_category=' + jQuery(this).find('select option:selected').val();
										}
									}
								}
								else if (jQuery(this).find('input').parent().hasClass('search-field')) {
									// multi-select
									if (jQuery(this).find('select').val() != null) {
										advanced_location += '&' + jQuery(this).find('select').attr('name') + '=' + jQuery(this).find('select').val();
									};
								}
								else {
									// text
									if (jQuery(this).find('input').val() != '') {
										advanced_location += '&' + jQuery(this).find('input').attr('name') + '=' + jQuery(this).find('input').val();
									};
								}
								break;
							case 'checkbox':
								if (jQuery(this).find('input').is(":checked")) {
									var checkbox_checked = '1';
								}
								else {
									var checkbox_checked = '0';
								}
								advanced_location += '&' + jQuery(this).find('input').attr('name') + '=' + checkbox_checked;
								break;
							case 'tel':
								if (jQuery(this).find('input').val() != '') {
									advanced_location += '&' + jQuery(this).find('input').attr('name') + '=' + jQuery(this).find('input').val();
								};
								break;
							case 'email':
								if (jQuery(this).find('input').val() != '') {
									advanced_location += '&' + jQuery(this).find('input').attr('name') + '=' + jQuery(this).find('input').val();
								};
								break;
							case 'time':
								if (jQuery(this).find('input').val() != '') {
									advanced_location += '&' + jQuery(this).find('input').attr('name') + '=' + jQuery(this).find('input').val();
								};
								break;
							case 'radio':
								if (jQuery(this).find('input:checked').attr('value') !== undefined) {
									advanced_location += '&' + jQuery(this).find('input').attr('name') + '=' + jQuery(this).find('input:checked').attr('value');
								};
								break;
							case 'address':
								if (jQuery(this).find('input').val() != '') {
									address = jQuery(this).find('input').val();
								};
								break;

							default:
								console.log('Undefined input type: ' + input_type);
						}
					}
				})

				var location = "?geodir_search=1&stype=" + post_type + "&s=+&snear=" + address + "&sgeo_lat=" + latitude + "&sgeo_lon=" + longitude + advanced_location;
				window.location.href = my_ajax.blog_url + location;

			}
			else if (jQuery("body").hasClass("skyvacation")) {
				var location = "?geodir_search=1&stype=" + post_type + "&s=+&snear=" + address + "&sgeo_lat=" + latitude + "&sgeo_lon=" + longitude + "&sgeo_when=" + when + "&sgeo_adults=" + adults["0"] + "&sgeo_childrens=" + childrens["0"];

				//Codes to add url extension for search +"/ja/ in ?geodir_search="
				var docURL = document.URL;
				if (docURL.indexOf("ja") == -1) {
					//string not found
					window.location.href = my_ajax.blog_url + location;
				}
				else {
					//string found
					window.location.href = my_ajax.blog_url + "/ja/" + location;

				}

			}
			else if (jQuery("body").hasClass("skydirectory")) {
				var location = "?geodir_search=1&stype=" + post_type + "&s=+&snear=" + address + "&sgeo_lat=" + latitude + "&sgeo_lon=" + longitude + "&sgeo_keyword=" + keyword + "&sgeo_category=" + category;
				window.location.href = my_ajax.blog_url + location;

			}
			else {
				// More options checkboxes

				var checkboxes = '';
				var sliders = '';
				if (jQuery('.header-more-right .checkbox').length) {
					jQuery('.header-more-right .more-options-checkbox').each(function() {
						if (jQuery(this).is(":checked")) {
							checkboxes += '&' + jQuery(this).val() + '=1';
						};
					});
				};

				// More options sliders
				jQuery(".header-more-left .filter-field").each(function() {
					if (typeof jQuery(this).find('.ui-slider-range.ui-changed').parent().attr('id') != 'undefined') {
						var slider_id = jQuery(this).find('.ui-slider-range.ui-changed').parent().attr('id');
						var slider_name = slider_id.split('-');
						var slider_min = jQuery(this).find('.ui-slider-range.ui-changed').parent().parent().find('.range-slider-min').html();
						var slider_max = jQuery(this).find('.ui-slider-range.ui-changed').parent().parent().find('.range-slider-max').html();

						sliders += '&' + slider_name['2'] + '=' + slider_min + '-' + slider_max;
					};
				});

				var location = "?geodir_search=1&stype=" + post_type + "&s=+&snear=" + address + "&sgeo_lat=" + latitude + "&sgeo_lon=" + longitude + "&sgeo_type=" + type + "&sgeo_contract=" + contract + checkboxes + sliders;
				window.location.href = my_ajax.blog_url + location;

			}
		});
	});

	jQuery(".popular-destinations-image .city-text").live('click', function(e) {
		var geocoder = new google.maps.Geocoder();
		var address = jQuery(this).parent().find(".city-country").html();
		var post_type = jQuery("#header-post-type").val();
		var height = 0;

		if (post_type == '') {
			post_type = 'gd_place';
		};

		geocoder.geocode({
			'address': address
		}, function(results, status) {

			if (status == google.maps.GeocoderStatus.OK) {
				var latitude = results[0].geometry.location.lat();
				var longitude = results[0].geometry.location.lng();
			}

			if (jQuery("body").hasClass("skydirectory")) {
				var location = "?geodir_search=1&stype=" + post_type + "&s=+&snear=" + address + "&sgeo_lat=" + latitude + "&sgeo_lon=" + longitude + "&sgeo_when=&sgeo_adults=1&sgeo_childrens=No";
				window.location.href = my_ajax.blog_url + location;
			}
			else if (jQuery("body").hasClass("skyvacation")) {
				var location = "?geodir_search=1&stype=" + post_type + "&s=+&snear=" + address + "&sgeo_lat=" + latitude + "&sgeo_lon=" + longitude;
				window.location.href = my_ajax.blog_url + location;
			}
			else {
				var location = "?geodir_search=1&stype=" + post_type + "&s=+&snear=" + address + "&sgeo_lat=" + latitude + "&sgeo_lon=" + longitude;
				window.location.href = my_ajax.blog_url + location;
			}
		});
	})

	jQuery(window).bind("debouncedresize", function() {
		jQuery('.wpb_thumbnails').isotope();
	});

	if (jQuery(window).width() >= 767) {
		jQuery("a.menu-trigger").click(function() {
			jQuery(".mp-menu").css({
				top: jQuery(document).scrollTop()
			});

			return false;
		});
	}

	jQuery(".fixed_menu .social-container").css({
		'top': (jQuery(window).height()) - (jQuery(".fixed_menu .social-container").height() + 60)
	});

	jQuery(".gallery-icon a").attr('rel', 'prettyphoto');

	jQuery("a[rel^='prettyPhoto']").prettyPhoto();

	// Opacity hover effect
	jQuery(".opacity_hover").mouseenter(function() {
		var social = this;
		jQuery(social).animate({
			opacity: "0.8"
		}, 80, function() {
			jQuery(social).animate({
				opacity: "1.0"
			}, 80);
		});
	});

	var $window = $(window);
	var windowHeight = $window.height();

	$window.resize(function() {
		windowHeight = $window.height();
		jQuery(".fixed_menu .social-container").css({
			'top': (jQuery(window).height()) - (jQuery(".fixed_menu .social-container").height() + 60)
		});
	});

	/**
	 * jQuery.LocalScroll - Animated scrolling navigation, using anchors.
	 * Copyright (c) 2007-2009 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
	 * Dual licensed under MIT and GPL.
	 * Date: 3/11/2009
	 * @author Ariel Flesler
	 * @version 1.2.7
	 **/
	;
	(function($) {
		var l = location.href.replace(/#.*/, '');
		var g = $.localScroll = function(a) {
			$('body').localScroll(a)
		};
		g.defaults = {
			duration: 1e3,
			axis: 'y',
			event: 'click',
			stop: true,
			target: window,
			reset: true
		};
		g.hash = function(a) {
			if (location.hash) {
				a = $.extend({}, g.defaults, a);
				a.hash = false;
				if (a.reset) {
					var e = a.duration;
					delete a.duration;
					$(a.target).scrollTo(0, a);
					a.duration = e
				}
				i(0, location, a)
			}
		};
		$.fn.localScroll = function(b) {
			b = $.extend({}, g.defaults, b);
			return b.lazy ? this.bind(b.event, function(a) {
				var e = $([a.target, a.target.parentNode]).filter(d)[0];
				if (e) i(a, e, b)
			}) : this.find('a,area').filter(d).bind(b.event, function(a) {
				i(a, this, b)
			}).end().end();

			function d() {
				return !!this.href && !!this.hash && this.href.replace(this.hash, '') == l && (!b.filter || $(this).is(b.filter))
			}
		};

		function i(a, e, b) {
			var d = e.hash.slice(1),
				f = document.getElementById(d) || document.getElementsByName(d)[0];
			if (!f) return;
			if (a) a.preventDefault();
			var h = $(b.target);
			if (b.lock && h.is(':animated') || b.onBefore && b.onBefore.call(b, a, f, h) === false) return;
			if (b.stop) h.stop(true);
			if (b.hash) {
				var j = f.id == d ? 'id' : 'name',
					k = $('<a> </a>').attr(j, d).css({
						position: 'absolute',
						top: $(window).scrollTop(),
						left: $(window).scrollLeft()
					});
				f[j] = '';
				$('body').prepend(k);
				location = e.hash;
				k.remove();
				f[j] = d
			}
			h.scrollTo(f, b).trigger('notify.serialScroll', [f])
		}
	})(jQuery);

	// Hide loading effect
	jQuery('.overlay-hide').hide();

	jQuery('#vh_loading_effect').addClass('hide').delay(500).queue(function(next) {
		jQuery(this).hide();
		next();
	});
});

function header_size() {

	jQuery(window).on('touchmove', function(event) {
		set_height();
	});
	var win = jQuery(window),
		header = jQuery('.header .top-header'),
		logo = jQuery('.header .top-header .logo img'),
		elements = jQuery('.header, .top-header .header-social-icons div a, .top-header .logo, .top-header .header_search, .header_search .search .gray-form .footer_search_input, .top-header .menu-btn.icon-menu-1'),
		el_height = jQuery(elements).filter(':first').height(),
		isMobile = 'ontouchstart' in document.documentElement,
		set_height = function() {
			var st = win.scrollTop(),
				newH = 0;

			if (st < el_height / 2) {
				newH = el_height - st;
				header.removeClass('header-small');
			}
			else {
				newH = el_height / 2;
				header.addClass('header-small');
			}

			elements.css({
				'height': newH + 'px',
				'line-height': newH + 'px'
			});
			logo.css({
				'max-height': newH + 'px'
			});
		}

	if (!header.length) {
		return false;
	}

	win.scroll(set_height);
	set_height();
}

// debulked onresize handler

function on_resize(c, t) {
	"use strict";

	var onresize = function() {
		clearTimeout(t);
		t = setTimeout(c, 100);
	};
	return c;
}


function clearInput(input, inputValue) {
	"use strict";

	if (input.value === inputValue) {
		input.value = '';
	}
}

// function moveOffset() {
// 	if( jQuery(".full-width").length ) {
// 		var offset = jQuery(".full-width").position().left;
// 		jQuery(".full-width").css({
// 			width: jQuery('.main').width(),
// 			marginLeft: -offset
// 		});
// 	};
// };

jQuery(document).ready(function() {
	"use strict";

	// Top menu
	if (jQuery(".header .sf-menu").length) {
		var menuOptions = {
				speed: 'fast',
				speedOut: 'fast',
				hoverClass: 'sfHover',
			}
			// initialise plugin
		var menu = jQuery('.header .sf-menu').superfish(menuOptions);
	}
	// !Top menu

	// Search widget
	jQuery('.search.widget .sb-icon-search').click(function(el) {
		el.preventDefault();
		jQuery('.search.widget form').submit();
	});
	// !Seaarch widget

	// Search widget
	jQuery('.search-no-results .main-inner .sb-icon-search').click(function(el) {
		el.preventDefault();
		jQuery('.search-no-results .main-inner .search form').submit();
	});
	// !Seaarch widget


	// Social icons hover effect
	jQuery(".social_links li a").mouseenter(function() {
		var social = this;
		jQuery(social).animate({
			opacity: "0.5"
		}, 250, function() {
			jQuery(social).animate({
				opacity: "1.0"
			}, 100);
		});
	});
	// !Social icons hover effect

	// Widget contact form - send
	jQuery("#contact_form").submit(function() {
		jQuery("#contact_form").parent().find("#error, #success").hide();
		var str = jQuery(this).serialize();
		jQuery.ajax({
			type: "POST",
			url: my_ajax.ajaxurl,
			data: 'action=contact_form&' + str,
			success: function(msg) {
				if (msg === 'sent') {
					jQuery("#contact_form").parent().find("#success").fadeIn("slow");
				}
				else {
					jQuery("#contact_form").parent().find("#error").fadeIn("slow");
				}
			}
		});
		return false;
	});
	// !Widget contact form - send

	/* Merge gallery */
	jQuery('.merge-gallery div').mouseenter(function() {
		jQuery(this).find('.gallery-caption').animate({
			bottom: jQuery(this).find('img').height()
		}, 250);
	}).mouseleave(function() {
		jQuery(this).find('.gallery-caption').animate({
			bottom: jQuery(this).find('img').height() + 150
		}, 250);
	});




});

function isNumber(evt) {



	var e = jQuery("#geodir_listing_price");
	var n = e.val();
	var result = parseInt(n);
	var pattern = /^\d+$/;


	if (!pattern.test(n)) {

		alert('Invalid listing price');
		jQuery("#geodir_listing_price").val("");
	}

	return true;
}