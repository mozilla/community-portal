jQuery(function() {
	//Finally, add autocomplete here
	//Autocomplete
	if( jQuery( "input#location-name__mozilla" ).length > 0 ){
			jQuery( "div.em-location-data input#location-name" ).autocomplete({
				source: EM.locationajaxurl,
				minLength: 2,
				focus: function( event, ui ){
					jQuery("input#location-id" ).val( ui.item.value );
					return false;
				},			 
				select: function( event, ui ){
					jQuery("input#location-id" ).val(ui.item.id).trigger('change');
					jQuery("input#location-name" ).val(ui.item.value);
					jQuery('input#location-address').val(ui.item.address);
					jQuery('input#location-town').val(ui.item.town);
					jQuery('input#location-state').val(ui.item.state);
					jQuery('input#location-region').val(ui.item.region);
					jQuery('input#location-postcode').val(ui.item.postcode);
					jQuery('input#location-latitude').val(ui.item.latitude);
					jQuery('input#location-longitude').val(ui.item.longitude);
					if( ui.item.country == '' ){
						jQuery('select#location-country option:selected').removeAttr('selected');
					}else{
						jQuery('select#location-country option[value="'+ui.item.country+'"]').attr('selected', 'selected');
					}
					jQuery('div.em-location-data input').css('background-color','#ccc').prop('readonly', true);
					jQuery('div.em-location-data select').css('background-color','#ccc').css('color', '#666666').prop('disabled', true);
					jQuery('#em-location-reset').show();
					jQuery('#em-location-search-tip').hide();
					jQuery(document).triggerHandler('em_locations_autocomplete_selected', [event, ui]);
					return false;
				}
			}).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
				html_val = "<a>" + em_esc_attr(item.label) + '<br><span style="font-size:11px"><em>'+ em_esc_attr(item.address) + ', ' + em_esc_attr(item.town)+"</em></span></a>";
				return jQuery( "<li></li>" ).data( "item.autocomplete", item ).append(html_val).appendTo( ul );
			};
			jQuery('#em-location-reset a').click( function(){
				jQuery('div.em-location-data input').css('background-color','#fff').val('').prop('readonly', false);
				jQuery('div.em-location-data select').css('background-color','#fff').css('color', 'auto').prop('disabled', false);
				jQuery('div.em-location-data option:selected').removeAttr('selected');
				jQuery('input#location-id').val('');
				jQuery('#em-location-reset').hide();
				jQuery('#em-location-search-tip').show();
				jQuery('#em-map').hide();
				jQuery('#em-map-404').show();
				if(typeof(marker) !== 'undefined'){
					marker.setPosition(new google.maps.LatLng(0, 0));
					infoWindow.close();
					marker.setDraggable(true);
				}
				return false;
			});
			if( jQuery('input#location-id').val() != '0' && jQuery('input#location-id').val() != '' ){
				jQuery('div.em-location-data input').css('background-color','#ccc').prop('readonly', true);
				jQuery('#em-location-reset').show();
				jQuery('#em-location-search-tip').hide();
			}
		}
		jQuery(document).triggerHandler('em_javascript_loaded');
});