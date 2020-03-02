jQuery(function() {
	const newsletterForm = jQuery('#newsletter_form');
	const $emailInput = jQuery('.newsletter__form input[name=email]');
	const $privacyCheckbox = jQuery('#privacy');

	const verifyEmail = function(input) {
		const $this = jQuery(input);
		if ($this[0].validity.valid === false) {
			$this.addClass('error');
			return false;
		} 
		$this.removeClass('error');
		return true
	}

	$emailInput.on('blur', function() {
		verifyEmail(this);
	});

	const newsletterError = function() {
		console.log(error);
	}
    // show sucess message
    function newsletterThanks() {
        var thanks = document.getElementById('newsletter_thanks');
        // show thanks message
        thanks.style.display = 'block';
    }

    // XHR subscribe; handle errors; display thanks message on success.
    newsletterForm.on('submit', function(e) { 
		e.preventDefault();
		e.stopPropagation();
		validEmail = verifyEmail($emailInput);
		const privacyCheck = $privacyCheckbox.prop('checked');
		const $cpgError = jQuery('.newsletter__cpg__error');

		if (!privacyCheck) {
			$cpgError.addClass('newsletter__cpg__error--active');
		}

		if (!validEmail || !privacyCheck) {
			return;
		}

        var skipXHR = newsletterForm.attr('data-skip-xhr');
        if (skipXHR) {
			return true;
        }

        var fmt = document.getElementById('fmt').value;
        var email = document.getElementById('email').value;
        var newsletter = document.getElementById('newsletters').value;
        var privacy = document.querySelector('input[name="privacy"]:checked') ? '&privacy=true' : '';
        var params = 'email=' + encodeURIComponent(email) +
					'&newsletters=' + newsletter +
					'&privacy=true' +
					'&fmt=' + fmt +
					'&source_url=' + encodeURIComponent(document.location.href);


        var xhr = new XMLHttpRequest();

        xhr.onload = function(r) {
            if (r.target.status >= 200 && r.target.status < 300) {
				// response is null if handled by service worker
                if(response === null) {
                    return;
				}
				var response = r.target.response;
				console.log(response);
                // if (response.success === true) {
				// 	console.log(JSON.parse(response));
                //     newsletterForm.style.display = 'none';
                //     newsletterThanks();
                // } else {
                //     if(response.errors) {
                //         for (var i = 0; i < response.errors.length; i++) {
                //             errorArray.push(response.errors[i]);
                //         }
                //     }
                //     newsletterError(new Error());
                // }
            } else {
                newsletterError(new Error());
            }
        };

        xhr.onerror = function(e) {
			console.log(e);
            newsletterError(e);
        };

        var url = newsletterForm.attr('action');

        xhr.open('POST', `https://cors-anywhere.herokuapp.com/${url}`, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
        xhr.timeout = 5000;
        xhr.ontimeout = newsletterError;
        xhr.responseType = 'json';
        xhr.send(params);

        return false;
	})

});