// jQuery(function() {

// 	function checkCookieName(name) 
//     {
// 		const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
// 		if (match) {
// 			return true;
// 		}
// 		else{
// 			return false;
// 		}
// 	}

// 	function wpmlRedirect() {
// 		const url = "/wp-admin/admin-ajax.php?action=update_mozilla_language";
// 		const requestUrl = window.location.href;
// 		console.log(url);
// 		jQuery.ajax({
// 			url,
// 			method: "POST",
// 			data: {
// 				requestUrl,
// 			},
// 			success: function(resp) {
// 				console.log(resp);
// 			}
// 		})
// 	}

// 	const languageSwitchListener = function() {
// 		const $option = jQuery('#wpml-language');
// 		$option.on('change', wpmlRedirect);
// 	}

// 	const init = function() {
// 		languageSwitchListener();
// 	}

// 	init();

// });