jQuery(function() {
	function handleUnsubscribe($btn) {
			const url =  '/wp-admin/admin-ajax.php?action=mailchimp_unsubscribe';
			$btn.click(function() {
			const campaign = $btn.data('campaign');
			const nonce = $btn.data('nonce');
			const data = {
				campaign, 
				nonce
			}
			jQuery.ajax({
				url, 
				data,
				method: 'POST',
				success: function(resp) {
					console.log(resp);
				}
			})
		})
	};
	
	const $unsubscribeBtn = jQuery('#unsubscribe');
	if ($unsubscribeBtn) {
		handleUnsubscribe($unsubscribeBtn);
    }
});