jQuery(function() {

    jQuery(document).on('click', '.campaign__hero-cta--sub', function(e) {
        e.preventDefault();
        var $this = jQuery(this);
        var campaign = $this.data('campaign');

        var post = {
            'campaign': campaign
        };

        var url =  '/wp-admin/admin-ajax.php?action=mailchimp_subscribe';

        jQuery.ajax({
            url: url,
            data: post,
            method: 'POST',
            success: function(response) {
                response = jQuery.parseJSON(response);
                if(response.status == 'success') {
                   
                } else {
                    
                }
            }
        });
        return false;
    });

});