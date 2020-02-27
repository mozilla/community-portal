jQuery(function() {

    jQuery(document).on('click', '.campaign__hero-cta--sub', function(e) {
        e.preventDefault();
        var $this = jQuery(this);
        var campaign = $this.data('campaign');
        var list = $this.data('list');

        var post = {
            'campaign': campaign,
            'list': list
        };

        var url =  '/wp-admin/admin-ajax.php?action=mailchimp_subscribe';

        jQuery.ajax({
            url: url,
            data: post,
            method: 'POST',
            success: function(response) {
                response = jQuery.parseJSON(response);
                if(response.status == 'OK') {
                    console.log($this.data('unsub-copy'));
                    $this.text($this.data('unsub-copy'));
                } else {
                    
                }
            }
        });
        return false;
    });

});