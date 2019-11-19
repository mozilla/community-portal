jQuery(function(){

    var $public_input = jQuery('#bp-group-status-public');
    var $private_input = jQuery('#bp-group-status-private');

    jQuery('#bp-groups-settings-section-status').find('legend').text('Group Status');

    jQuery("label[for='bp-group-status-public']").html($public_input).append('Verified');
    jQuery("label[for='bp-group-status-private']").html($private_input).append('Unverified');


    jQuery("td.column-status").each(function(index, ele) {
        var $ele = jQuery(ele);

        if($ele.data('colname') == 'Status') {
            if($ele.text() === 'Private') {
                $ele.text('Unverified');
            }
            if($ele.text() === 'Public') {
                $ele.text('Verified');
            }
        }
    });
    
    jQuery('li.public').find('a').html(jQuery('li.public').find('a').html().replace("Public", "Verified"));
    jQuery('li.private').find('a').html(jQuery('li.private').find('a').html().replace("Private", "Unverified"));

});