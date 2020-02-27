jQuery(function() {

	function handleUnsubscribe($btn) {
			const url =  '/wp-admin/admin-ajax.php?action=mailchimp_unsubscribe';
			$btn.click(function() {
			const campaign = $btn.data('campaign');
			const list = $btn.data('list');
			const data = {
				campaign,
				list 
			}
			jQuery.ajax({
				url, 
				data,
				method: 'POST',
				success: function(resp) {
					const response = jQuery.parseJSON(resp);
					if (response.status === 'success') {  

					} else {

					}
				}
			})
		})
	};
	
	const $unsubscribeBtn = jQuery('#unsubscribe');
	if ($unsubscribeBtn) {
		handleUnsubscribe($unsubscribeBtn);
    }


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