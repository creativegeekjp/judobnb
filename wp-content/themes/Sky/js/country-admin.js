jQuery(window).load(function() {
	jQuery("#myTags").tagit({
		afterTagRemoved: function(event, ui) {
			var tag = ui.tagLabel;
			var tag_to_replace = '{"country":"'+tag+'"},';
			var date_old = jQuery('#pu_textbox').val();
		
			jQuery('#pu_textbox').val(date_old.replace(tag_to_replace,''));
		}
	});
	jQuery('#country_data_set').click(function() {
		var country = jQuery('#countries_input').val();
		var info = '{"country":"'+country+'"},';
		jQuery('#myTags').tagit('createTag', country);
		jQuery('#pu_textbox').val(jQuery('#pu_textbox').val()+info);
		jQuery('#countries_input').val('');
	});
})