jQuery(function() {

	const verifyEmail = function(input) {
		const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		const $this = jQuery(input);

		if (re.test($this.val())=== false) {
			$this.addClass('error');
			return false;
		} 
		$this.removeClass('error');
		return true
	}

	const clearErrors = function(input) {
		input.on('blur', function() {
			verifyEmail(this);
		});
	}

	const updateUserMeta = function() {
		const url = "/wp-admin/admin-ajax.php?action=newsletter_subscribe";
		jQuery.ajax({
			url,
			method: "POST",
			data: {
				subscribed: '1',
			},
			success: function(resp) {
				newsletterThanks()
			}
		})
	}

	const newsletterFailure = function() {
		const $newsletterForm = jQuery('.newsletter__form');
		$newsletterForm.css('display', 'none');
		const $failure = jQuery('.newsletter__failure');
		$failure.css('display', 'block');
	}

	const newsletterError = function(error) {
		const url = "/wp-admin/admin-ajax.php?action=newsletter_subscribe";
		jQuery.ajax({
			url,
			method: "POST",
			data: {
				subscribed: '2',
			},
			success: function(resp) {
				newsletterFailure()
			}
		})
	}

    // show sucess message
    function newsletterThanks() {
		const $newsletterForm = jQuery('.newsletter__form');
		$newsletterForm.css('display', 'none');
		const $success = jQuery('.newsletter__success');
		$success.css('display', 'block');
    }

    // XHR subscribe; handle errors; display thanks message on success.
    const handleSubmit = function(e) {
		e.preventDefault();
		const $this = jQuery(this);
		const skipXHR = $this.attr('data-skip-xhr');
		const $emailInput = jQuery('.newsletter__form input[name=email]');
		validEmail = verifyEmail($emailInput);
		clearErrors($emailInput);
		const $privacyCheckbox = jQuery('#privacy');
		const privacyCheck = $privacyCheckbox.prop('checked');
		const $cpgError = jQuery('.newsletter__cpg__error');

		if (!privacyCheck) {
			$cpgError.addClass('newsletter__cpg__error--active');
		}

		if (!validEmail || !privacyCheck) {
			return;
		}

        if (skipXHR) {
			return true;
        }

		const email = $emailInput.val();
        var params = 'email=' + encodeURIComponent(email) +
					'&newsletters=about-mozilla' +
					'&privacy=true' +
					'&fmt=H'+
					'&source_url=' + encodeURIComponent(document.location.href);


        var xhr = new XMLHttpRequest();

        xhr.onload = function(r) {
            if (r.target.status >= 200 && r.target.status < 300) {
				// response is null if handled by service worker
                if(response === null) {
                    return;
				}
				var response = r.target.response;
				if (response.success) {
					updateUserMeta();
					return;
				}
				newsletterError();
            } else {
                newsletterError();
            }
        };

        xhr.onerror = function(e) {
            newsletterError(e);
        };

		let url = $this.attr('action');
		if (location.protocol === 'http') {
			url = `https://cors-anywhere.herokuapp.com/${url}`;
		}
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
        xhr.timeout = 5000;
        xhr.ontimeout = newsletterError;
        xhr.responseType = 'json';
        xhr.send(params);
        return false;
	}

	
	const initNewsletter = function(selector) {
		const newsletterForms = jQuery(selector);
		if (!newsletterForms.length > 0) {
			return;
		}
		const $emailInput = jQuery('.newsletter__form input[name=email]');
		clearErrors($emailInput);
		
		newsletterForms.each((i, form) => {
			jQuery(form).on('submit', handleSubmit);
		})
	}

	initNewsletter('.newsletter__form');
});
