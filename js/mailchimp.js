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
});