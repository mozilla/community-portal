jQuery(function(){

	const updateUserMeta = function() {
		const url = "/wp-admin/admin-ajax.php?action=newsletter_subscribe";
		jQuery.ajax({
			url,
			method: "POST",
			data: {
                subscribed: 1,
                newsletter_nonce: jQuery('#newsletter_nonce_field').val()
			},
			success: function(resp) {
				jQuery('#complete-profile-form').submit();
			}
		})
	}

	const checkMatrixValue = function(value) {
		const username = new RegExp(/^[a-z0-9.\-_=/]+:/, 'gi');
		const domain = new RegExp(/(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$|(([a-zA-Z]|[a-zA-Z][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9])$|\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*/, 'gi');
		const validMatrixId = username.test(value) && domain.test(value);
		return validMatrixId;
	}

	const formErrorState = function(input) {
		const $this = jQuery(input);
		$this.addClass('profile__input--error');
		$this.next('.form__error-container').addClass('form__error-container--visible');
	}

	const formClearError = function(input) {
		const $this = jQuery(input);
		$this.removeClass('profile__input--error');
		$this.next('.form__error-container').removeClass('form__error-container--visible');
	}

	const handleMatrixInput = function() {
		const $this = jQuery(this);
		if ($this.val() !== '') {
			const validMatrixId = checkMatrixValue($this.val());
			if (!validMatrixId) {
				formErrorState(this);
				return
			}
		}
		formClearError(this);
	}

	if (jQuery('#matrix')) {
		jQuery('#matrix').on('blur', handleMatrixInput);
	}

	const newsletterError = function(e) {
		const url = "/wp-admin/admin-ajax.php?action=newsletter_subscribe";
		jQuery.ajax({
			url,
			method: "POST",
			data: {
                subscribed: 2,
                newsletter_nonce: jQuery('#newsletter_nonce_field').val()
			},
			success: function(resp) {
				jQuery('#complete-profile-form').submit();
			}
		})
	}

	const handleSignUpSubmit = function(email, country, language) {
        let params = 'email=' + encodeURIComponent(email) +
					'&newsletters=about-mozilla' +
					'&privacy=true' +
					'&fmt=H'+
					'&source_url=' + encodeURIComponent(document.location.href);

		if (language){
			params += '&lang=' + language;
		}
		if (country) {
			params += '&country=' + country;
		}
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
			newsletterError();

        };

		let url = 'https://www.mozilla.org/en-US/newsletter/';
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


	const verifyEmail = function(input) {
		var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		const $this = jQuery(input);

		if (re.test($this.val())=== false) {
			$this.addClass('error');
			return false;
		}
		$this.removeClass('error');
		return true
	}

	jQuery('#newsletter-email').on('blur', function() {
		verifyEmail(this);
	});
	const handleSignUp = function() {
		const email = jQuery('#newsletter-email');
		const verified = verifyEmail(email);
		if (!verified) {
			return false;
		}
		const country = jQuery('#newsletter-country').val();
		const language = jQuery('#newsletter-language').val();
		return handleSignUpSubmit(email.val(), country, language);

	}

	const newsletterSignup = function() {
		const $newsletterCheck = jQuery('#newsletter');
		if (!$newsletterCheck || !$newsletterCheck.prop('checked')) {
			jQuery('#complete-profile-form').submit();
			return;
		}
		handleSignUp();
	}

    jQuery('.members__avatar--identicon').each(function(index, ele) {

        var $ele = jQuery(ele);
        var user = $ele.data('username');

        var avatar = new Identicon(btoa(user + 'mozilla-community-portal'), { format: 'svg' }).toString();
        $ele.css({'background-image': "url('data:image/svg+xml;base64," + avatar + "')"});

    });

    jQuery('#profile-languages-visibility').change(function() {
        var $this = jQuery(this);
        jQuery('#profile-languages-visibility-mobile').val($this.val());
        jQuery('input[name="profile_languages_visibility"]').val($this.val());
    });

    jQuery('#profile-languages-visibility-mobile').change(function() {
        var $this = jQuery(this);
        jQuery('#profile-languages-visibility').val($this.val());
        jQuery('input[name="profile_languages_visibility"]').val($this.val());
    });

    if(jQuery('.profile__avatar--empty').length > 0) {
        var user = jQuery('.profile__avatar--empty').data('user');
        var avatar = new Identicon(btoa(user + 'mozilla-community-portal'), { format: 'svg' }).toString();
        jQuery('.profile__avatar--empty').css({'background-image': "url('data:image/svg+xml;base64," + avatar + "')"});
    };

    jQuery('.profile__cta').click(function(e){
		e.preventDefault();
		var error = false;
		const $matrixInput = jQuery('#matrix');
		if ($matrixInput.val() !== '') {
			const validMatrixId = checkMatrixValue($matrixInput.val());
			if (!validMatrixId) {
				formErrorState($matrixInput);
			}
		}

        jQuery(':input[required]').each(function(index, element) {
            var $ele = jQuery(element);
            var $errorMsg = $ele.next('.form__error-container');

            if($ele.val() == "" || $ele.val() == "0" || ($ele.is(':checkbox') && $ele.prop("checked") === false)) {
                error = true;
                $ele.addClass("profile__input--error");
                $errorMsg.addClass('form__error-container--visible');
			}
			if($ele.attr('name') == 'email') {
				if ($errorMsg.hasClass('form__error-container--visible')) {
					error = true;
				}
            }
        });

        if(error || jQuery('.profile__input--error').length > 0) {
            var $errorEle = jQuery('#complete-profile-form').find('.profile__input--error:first');

            if($errorEle.attr('id') == 'image-url') {
                jQuery('#profile-image-visibility').focus();
            } else {
                $errorEle.focus();
            }
            return false;
        } else {
			newsletterSignup();
            return true;
        }

        return false;
    });

    jQuery('#profile-visibility').change(function(e) {
        var $this = jQuery(this);
        var value = parseInt($this.val());

        switch(value) {
            case 2:
                jQuery('#profile-image-visibility').val(2);
                jQuery('#firstname-visibility').val(0);
                jQuery('#lastname-visibility').val(2);
                jQuery('#email-visibility').val(2);
                jQuery('#profile-pronoun-visibility').val(2);
                jQuery('#profile-bio-visibility').val(2);
                jQuery('#profile-location-visibility').val(2);
                jQuery('#profile-phone-visibility').val(2);
                break;
            default:
                jQuery('#profile-image-visibility').val(value);
                jQuery('#firstname-visibility').val(value);
                jQuery('#lastname-visibility').val(value);
                jQuery('#email-visibility').val(value);
                jQuery('#profile-pronoun-visibility').val(value);
                jQuery('#profile-bio-visibility').val(value);
                jQuery('#profile-location-visibility').val(value);
                jQuery('#profile-phone-visibility').val(value);
        }
    });

    jQuery('#social-visibility').change(function(e) {
        var $this = jQuery(this);
        var value = parseInt($this.val());

        jQuery('#profile-discourse-visibility').val(value);
        jQuery('#profile-facebook-visibility').val(value);
        jQuery('#profile-twitter-visibility').val(value);
        jQuery('#profile-linkedin-visibility').val(value);
        jQuery('#profile-github-visibility').val(value);
        jQuery('#profile-telegram-visibility').val(value);
    });

    jQuery('#communication-visibility').change(function(e) {
        var $this = jQuery(this);
        var value = parseInt($this.val());

        jQuery('#profile-languages-visibility').val(value);
        jQuery('input[name="profile_languages_visibility"]').val(value);
        jQuery('#profile-tags-visibility').val(value);
    });


    jQuery('#portal-visibility').change(function(e) {
        var $this = jQuery(this);
        var value = parseInt($this.val());

        jQuery('#profile-groups-joined-visibility').val(value);
        jQuery('#profile-events-attended-visibility').val(value);
        jQuery('#profile-events-organized-visibility').val(value);
        jQuery('#profile-campaigns-visibility').val(value);
    });

    jQuery('.profile__add-language').click(function(e) {
        e.preventDefault();
        var $element = jQuery('.profile__form-field--tight:last');

        if($element.hasClass('profile__form-field--hidden')) {
            $element.removeClass('profile__form-field--hidden');
        } else {
            var $newLanguage = $element.clone(true);
            $newLanguage.addClass('profile__form-field--new');
            $newLanguage.insertBefore('.profile__add-language-container');
        }

        jQuery('.profile__form-field--new').find('.profile__select').val('');
        jQuery('.profile__form-field--new').removeClass('profile__form-field--new');
        $element.find(".profile__select--short:first").removeClass("profile__select--hide");

        return false;
    });

    jQuery('.profile__remove-language').click(function(e) {

        e.preventDefault();

        var $element = jQuery(this).parent().parent();
        jQuery(this).prev('.profile__select').addClass('profile__select--hide');

        if(jQuery('.profile__form-field--tight').length === 2) {
            $element.addClass('profile__form-field--hidden');
        } else {
            $element.remove();
        }

        jQuery(".profile__select--hide").val("");


        return false;
    });


    jQuery('#agree').click(function(e) {
        var $this = jQuery(this);

        if($this.is(':checked')) {
            $this.removeClass("profile__input--error");
            $this.next('.form__error-container--visible').removeClass('form__error-container--visible');
        }

	});

	const getCurrentTags = function(tagsInput) {
		let currentValue = tagsInput.val();
		currentValue = currentValue.length > 0 ? currentValue.split(/,\s?/) : [];
		return currentValue;
	}

	const updateValues = function($tagsInput, currentValue) {
		let newValues = currentValue;
		if (currentValue.length > 1) {
			newValues = currentValue.join(',');
		}
		$tagsInput.val(`${newValues}`);
	}

	const removeExistingTag = function($tagsInput, currentValue, tag) {
		if (currentValue.indexOf(tag) !== -1) {
			const index = currentValue.indexOf(tag);
			currentValue.splice(index, 1);
			updateValues($tagsInput, currentValue);
		}
	}

	const addNewTag = function($tagsInput, currentValue, tag) {
		if (currentValue.indexOf(tag) === -1) {
			currentValue.push(tag);
			updateValues($tagsInput, currentValue);
		}
	}

	const $tagsInput = jQuery('#tags');

    jQuery('.profile__checkbox').change(function(e) {
		var $this = jQuery(this);
		var id = $this.prop('id');
		var $label = jQuery('label[for=' + id + ']');
		var tag = $this.data('value');
		var currentValue = $tagsInput.val();

		currentValue = getCurrentTags($tagsInput);

		if(!$label.hasClass('profile__tag--active')) {
			addNewTag($tagsInput, currentValue, tag);
		}
		if($label.hasClass('profile__tag--active')){
			removeExistingTag($tagsInput, currentValue, tag);
		}
		$label.toggleClass('profile__tag--active');
		return false;
	});

	function clearMultipleErrorStates(input) {
		const $this = jQuery(input);
		var $errorContainer = $this.next('.form__error-container');
		$this.removeClass('profile__input--error');
		$errorContainer
			.removeClass('form__error-container--visible')
			.removeClass('form__error-container--secondary')
			.removeClass('form__error-container--tertiary');
	}

	function handleMultipleErrorStates(input, secondary = false, tertiary = false) {
		const $this = jQuery(input);
		var $errorContainer = $this.next('.form__error-container');
		$errorContainer
			.removeClass('form__error-container--secondary')
			.removeClass('form__error-container--tertiary');
		if (tertiary) {
			$this.addClass('profile__input--error');
			$errorContainer
				.addClass('form__error-container--visible')
				.addClass('form__error-container--tertiary');
			return;
		}
		if (secondary) {
			$this.addClass('profile__input--error');
			$errorContainer
				.addClass('form__error-container--visible')
				.addClass('form__error-container--secondary');
			return;
		}
		$this.addClass('profile__input--error');
		$errorContainer.addClass('form__error-container--visible')
	}

	function checkUserName(input) {
		const $this = jQuery(input);
		clearMultipleErrorStates($this);
		const value = $this.val();
		if (!value) {
			handleMultipleErrorStates($this);
			return;
		}
        const get = {};
        get.u = value;

        jQuery.ajax({
            url: '/wp-admin/admin-ajax.php?action=check_user',
            data: get,
            method: 'GET',
            success: function(data) {
                var response = jQuery.parseJSON(data);

                // User name is no good
                if(response == false) {
					handleMultipleErrorStates($this, true);
                } else {
                    clearMultipleErrorStates($this);
                }
            }
        })
	}

	function validateEmail(input) {
		const $this = jQuery(input);
		clearMultipleErrorStates($this);
		const value = $this.val();
		if (!value) {
			handleMultipleErrorStates($this);
			return;
		}
		// Validate email
		const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		if (re.test(value.toLowerCase()) === false) {
			handleMultipleErrorStates($this, false, true);
		}
		const get = {};
        get.u = value;

        jQuery.ajax({
            url: '/wp-admin/admin-ajax.php?action=validate_email',
            data: get,
            method: 'GET',
            success: function(data) {
                const response = jQuery.parseJSON(data.trim());
                if(response == false) {
					handleMultipleErrorStates($this, true);
					return;
                } else {
					clearMultipleErrorStates($this);
                }
            }
        });
	}
	let timeout = null;

    jQuery('#username').on('change keyup', function(e) {
		clearTimeout(timeout);
		const $this = jQuery(this);
		// Make a new timeout set to go off in 1000ms (1 second)
		timeout = setTimeout(function () {
			checkUserName($this)
		}, 1000);
    });


    jQuery('#email').on('change keyup', function(e) {
		clearTimeout(timeout);
		const $this = jQuery(this);
		// Make a new timeout set to go off in 1000ms (1 second)
		timeout = setTimeout(function () {
			validateEmail($this)
		}, 1000);
    });

    jQuery('.profile__input, .profile__textarea, .profile__select').on('change keyup', function(e){
        var $this = jQuery(this);
		if ($this.attr('id') === 'username' || $this.attr('id') === 'email') {
			return;
		}
        if($this.prop('required') ) {
            if($this.val() != '' || $this.val() == '0') {
                $this.removeClass('profile__input--error');
                $this.next('.form__error-container').removeClass('form__error-container--visible');
            } else {
                $this.addClass('profile__input--error');
                $this.next('.form__error-container').addClass('form__error-container--visible');
            }
        }
        e.stopPropagation();

        return false;
    });

    jQuery('#profile-delete-account').click(function(e) {
        e.preventDefault();
        jQuery('.profile__delete-account-error').addClass('profile__delete-account-error--hidden');
        var language = (jQuery('#string-translation').length > 0 ) ? jQuery('#string-translation').val() : 'en';

        if(confirm("Delete your profile?")) {
            jQuery.ajax({
                url: '/wp-admin/admin-ajax.php?action=delete_user&lang=' + language,
                method: 'POST',
                success: function(data) {
                    var response = jQuery.parseJSON(data);
                    if(response.status == 'success') {
                        if(response.translation) {
                            window.location = '/' + response.translation + '/people';
                        } else {
                            window.location = '/people';
                        }
                    } else {
                        jQuery('.profile__delete-account-error--hidden').removeClass('profile__delete-account-error--hidden');
                    }
                }
            });
        }

        return false;
    });


    jQuery('.members__location-select').change(function(e) {
        var location = jQuery(this).val();
        jQuery('input[name="country"]').val(location);

        if(jQuery('input[name="tag"]').val().length === 0) {
            jQuery('input[name="tag"]').prop('disabled', true);
        }

        jQuery('#members-search-form').submit();

    });

    jQuery('.members__language-select').change(function(e){
        var language = jQuery(this).val();
		jQuery('input[name="language"]').val(language);

		jQuery('#members-search-form').submit();

    });

    jQuery('.members__tag-select').change(function(e){
        var tag = jQuery(this).val();
        jQuery('input[name="tag"]').val(tag);
        jQuery('#members-search-form').submit();


    });

    jQuery('.members__toggle-filter').click(function(e) {
		const $this = jQuery(this);

        e.preventDefault();
        jQuery('.members__filter-container').slideToggle({
            start: function() {
				jQuery('.members__filter-container').css('display','flex');
				jQuery('.members__filter-container').css('flex-direction','column');
				if ($this.hasClass('members__toggle-filter--show')) {
					$this.removeClass('members__toggle-filter--show');
					$this.addClass('members__toggle-filter--hide');
				} else {
					$this.removeClass('members__toggle-filter--hide');
					$this.addClass('members__toggle-filter--show');
				}
            }
        });

        return false;
    });

    jQuery('.show-more').click(function(e) {
        const $this = jQuery(this);
        e.preventDefault();
        var items = $this.parent().find('a');
        jQuery(items).each(function( index ) {
          jQuery(this).removeClass('hidden');
          $this.addClass('hidden');
        });
        return false;
    });

});
