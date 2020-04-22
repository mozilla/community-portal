jQuery(function() {

	const verifyEmail = function(input) {
		const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		const $this = jQuery(input);

		if (re.test($this.val())=== false) {
			$this.next('.form__error-container').addClass('form__error-container--visible');
			$this.parent().addClass('form__error')
			return false;
		} 
		$this.next('.form__error-container').removeClass('form__error-container--visible');
		$this.parent().removeClass('form__error');
		return true
	}

	const verifyInputs = function(inputs) {
		let valid = true;
		inputs.forEach((input) => {
			if (input.val() === '') {
				input.next('.form__error-container').addClass('form__error-container--visible');
				input.parent().addClass('form__error')
				valid = false;
			} else {
				input.parent().removeClass('form__error')
				input.next('.form__error-container').removeClass('form__error-container--visible');
			}
		});
		return valid;
	}

	const verifyCheckbox = function(input) {
		if (!input.prop('checked')) {
			input.siblings('.form__error-container').addClass('form__error-container--visible');
			return false;
		}
		input.siblings('.form__error-container').removeClass('form__error-container--visible');
		return true;
	}

	const checkEmailInput = function(input) {
		input.on('blur', function() {
			verifyEmail(input);
		});
	}

	const checkTextError = function(input) {
		input.on('blur', function() {
			verifyInputs([input]);
		});
	}

	const checkTextInputs = function(inputs) {
		inputs.forEach((input) => {
			checkTextError(input);
		});
	}

	const checkPrivacyCheckbox = function(input) {
		input.on('change', function() {
			$error = input.siblings('.form__error-container');
			if ($error.hasClass('form__error-container--visible')) {
				$error.removeClass('form__error-container--visible');
			}
		});
	}
	
	jQuery(document).one('click', '.campaign__hero-cta--unsub', function(e) {
		var url =  '/wp-admin/admin-ajax.php?action=mailchimp_unsubscribe';
		
        $this = jQuery(this);

        e.stopImmediatePropagation();
        e.preventDefault();
        
        const campaign = $this.data('campaign');
		const list = $this.data('list');
		const nonce = $this.data('nonce');
        
        const data = {
            campaign,
			list,
			_ajax_nonce: nonce,
        };
        jQuery.ajax({
            url, 
            data,
            method: 'POST',
            success: function(resp) {
				console.log(resp);
                const response = jQuery.parseJSON(resp);

                if (response.status === 'OK') {  
                    $this.addClass('campaign__hero-cta--sub');
                    $this.removeClass('campaign__hero-cta--unsub');
                    $this.text($this.data('sub-copy'));
                } 
            }
        });

        return false;
    });

    jQuery(document).one('click', '.campaign__hero-cta--sub', function(e) {

        e.stopImmediatePropagation();
        e.preventDefault();

        var $this = jQuery(this);
        var campaign = $this.data('campaign');
		var list = $this.data('list');
		const nonce = $this.data('nonce');

        var post = {
            campaign,
			list,
			nonce,
        };

        var url =  '/wp-admin/admin-ajax.php?action=mailchimp_subscribe';

        jQuery.ajax({
            url: url,
            data: post,
            method: 'POST',
            success: function(response) {
				console.log(response);
                response = jQuery.parseJSON(response);
                if(response.status == 'OK') {
                    $this.removeClass('campaign__hero-cta--sub');
                    $this.addClass('campaign__hero-cta--unsub');
					$this.text($this.data('unsub-copy'));
                } 
			},
        });

        return false;
	});

	const submitForm = function(form, first_name, last_name, email) {
		const url =  '/wp-admin/admin-ajax.php?action=mailchimp_subscribe';
		const $this = jQuery(form);
        const campaign = $this.data('campaign');
		const list = $this.data('list');

        const post = {
            campaign,
			list,
			first_name,
			last_name,
			email
		};
		jQuery.ajax({
            url: url,
            data: post,
            method: 'POST',
            success: function(response) {
                response = jQuery.parseJSON(response);
				if (response.status === 'OK') {
					jQuery('.campaign-rsvp').addClass('success');
				} else {
					jQuery('.campaign-rsvp').addClass('failure');
				}
			},
			
        });
	}

	const validateForm = function(e, $firstName, $lastName, $emailInput, $privacyCheckbox) {
		e.preventDefault();
		const validInput = verifyInputs([$firstName, $lastName]);
		const validEmail = verifyEmail($emailInput);
		const validCheckbox = verifyCheckbox($privacyCheckbox);
		if (validInput && validEmail && validCheckbox) {
			submitForm(e.target, $firstName.val(), $lastName.val(), $emailInput.val());
		}
	}
	
	const checkAllInputs = function($firstName, $lastName, $emailInput, $privacyCheckbox) {
		checkTextInputs([$firstName, $lastName]);
		checkEmailInput($emailInput);
		checkPrivacyCheckbox($privacyCheckbox);
	}
	
	const campaignRsvp = function() {
		const $firstName = jQuery('#rsvp-first-name');
		const $lastName = jQuery('#rsvp-last-name');
		const $emailInput = jQuery('#rsvp-email');
		const $rsvpForm = jQuery('#campaign-rsvp-form');
		const $privacyCheckbox = jQuery('#privacy-policy');
		checkAllInputs($firstName, $lastName, $emailInput, $privacyCheckbox);
		$rsvpForm.on('submit', function(e) { 
			validateForm(e, $firstName, $lastName, $emailInput, $privacyCheckbox)
		});
	}

	campaignRsvp();

});