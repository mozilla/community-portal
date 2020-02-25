jQuery(function() {
	function handleUnsubscribe($btn) {
			const url =  '/wp-admin/admin-ajax.php?action=mailchimp_unsubscribe';
			$btn.click(function() {
			const campaignId = $btn.data('campaign');
			const nonce = $btn.data('nonce');
			const data = {
				campaignId, 
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