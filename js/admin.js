jQuery(function(){

    var $public_input = jQuery('#bp-group-status-public');
    var $private_input = jQuery('#bp-group-status-private');

    jQuery('#bp-groups-settings-section-status').find('legend').text('Group Status');

    jQuery("label[for='bp-group-status-public']").html($public_input).append('Verified');
    jQuery("label[for='bp-group-status-private']").html($private_input).append('Unverified');



});