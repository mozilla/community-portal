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

    var $public_count = jQuery('li.public').find('.count');
    var $private_count = jQuery('li.private').find('.count');
    
    jQuery('li.public').find('a').insertBefore($public_count.insertBefore('Verified '));
    jQuery('li.private').find('a').insertBefore($private_count.insertBefore('Unverified'));

});