jQuery(function() {

	function toggleStrings($label, className, online) {
		if (online) {
			$label.removeClass(`${className}--in-person`);
			$label.addClass(`${className}--online`);
			return;
		} 
		$label.addClass(`${className}--in-person`);
		$label.removeClass(`${className}--online`);
	}

    function getFilter(option) {
		const filter = option.dataset.filter;
        return filter;
    }

    function getUrl() {
        const url = new URL(location.href);
        return url;
    }

    function getParams(url) {
		const params = new URLSearchParams(url.search);
        return params;
    }

    function setUrlParams(url, key, value) {
		url.searchParams.set(key.toLowerCase(), value);
		return url;
	}
	
	function relocate(url) {
		window.location.href = url;
	} 

    function applyFilters() {
		const $filters = jQuery(".events__filter__option");
		const nonceValue = jQuery('#events-filter-nonce').val();
        if ($filters) {
            $filters.each((i, filter) => {
                jQuery(filter).on("change", function(e) {
					let value = encodeURI(e.target.value);
					const filterTitle = getFilter(e.target);
					let url = getUrl();
					const params = getParams(url);
					if (!params.has('nonce')) {
						setUrlParams(url, 'nonce', nonceValue);
					}
					if (params.has('pno')) {
						setUrlParams(url, 'pno', '1');
					}
					url = setUrlParams(url, filterTitle.toLowerCase(), value);
					relocate(url);
                });
            });
        }
    }

    function toggleMobileEventsNav(className, toggleTarget) {
        const $eventsNavToggle = jQuery(className);
        const $eventsNav = jQuery(toggleTarget);
        if ($eventsNavToggle && $eventsNav) {
            $eventsNavToggle.on("click", function(e) {
				e.preventDefault();
				const $this = jQuery(this);
				if ($this.hasClass('events__filter__toggle--hide')) {
					$this.removeClass('events__filter__toggle--hide');
					$this.addClass('events__filter__toggle--show');
				} else {
					$this.removeClass('events__filter__toggle--show');
					$this.addClass('events__filter__toggle--hide');
				}
				$eventsNav.slideToggle();
            });
        }
    }

    function eventsMobileNav() {
        const $viewOptions = jQuery(".events__nav--mobile select");
        if ($viewOptions) {
            $viewOptions.on("change", function(e) {
                const url = getUrl();
                const params = getParams(url);
                setUrlParams(url, params, "view", this.value);
            });
        }
    }

    function toggleVisibility($selector, value, hidden) {
        $selector.val(value);
        if (hidden) {
            $selector
                .parent()
                .parent()
                .removeClass("event-creator__hidden");
            return;
        }
        $selector
            .parent()
            .parent()
            .addClass("event-creator__hidden");
    }

    function toggleLocationType() {
        const $locationTypeInput = jQuery("#location-type");
        const $locationAddress = jQuery("#location-address");
        const $locationNameLabel = jQuery("#location-name-label");
        const $countryLabel = jQuery("#location-country-label");
        $locationTypeInput.on("change", function() {
            $this = jQuery(this);
            if ($this.val() === "online") {
                toggleVisibility($locationAddress, "Online", false);
				toggleStrings($locationNameLabel, 'event-creator__label', true);
				toggleStrings($countryLabel, 'event-creator__label', true);
                return;
            }
			toggleVisibility($locationAddress, "", true);
			toggleStrings($locationNameLabel, 'event-creator__label', false);
			toggleStrings($countryLabel, 'event-creator__label', false);
        });
    }

    function handleCityForOnline($country, $city) {
        if ($country.val() === 'OE') {
            $city.val('Online Event');
        } else if ($city.val() === 'Online Event') {
            $city.val('');
        }

    }

    function handleOnlineEvent() {
        const $locationCountry = jQuery('#location-country');
        const $locationCity = jQuery('#location-town');

        if ($locationCountry.length > 0) {
            $locationCountry.on('change', function(e) {
				const $this = jQuery(this);
                handleCityForOnline($this, $locationCity);
            });
        }
	}

    function clearErrors(input) {
        input.on("focus blur", function() {
			const $this = jQuery(this);
			if ($this.hasClass('event-creator__input--error')){
				toggleError($this, false);
			} 
		});
    }

    function toggleError(input, error = true) {
		if (error) {
			input.addClass('event-creator__input--error');
			input.next('.form__error-container').addClass('form__error-container--visible');
			return;
		}
		input.removeClass('event-creator__input--error');
		input.next('.form__error-container').removeClass('form__error-container--visible');
    }

    function checkInputs(inputs) {
        let $allClear = true;
        let $first = true;

        inputs.each(function() {
            const $this = jQuery(this);

            clearErrors($this);
            $allClear = validateCpg($allClear);
            const input_id = $this.attr("id");

            if(input_id == 'location-name') {
				if (jQuery('#location-type').val() === 'online') {
					var pattern = new RegExp( /[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)?/gi,'i');
					if(!pattern.test($this.val())) {
						toggleStrings($this.next('.form__error-container'), 'form__error', true);
						$this.addClass('event-creator__input--error');
						$allClear = false;
					} 
				} else if (!$this.val()) {
					toggleStrings($this.next('.form__error-container'), 'form__error', false);
				}
			} else if (!$this.val() || $this.val() === "00:00" || $this.val() === "0") {
                if ($first) {
                    jQuery("html, body").animate({
                            scrollTop: $this.parent().offset().top
                        },
                        1000
                    );
                    $this.focus();
                    $first = false;
                }
                toggleError($this);
                $allClear = false;
            } else {
				toggleError($this, false);
			}
        });

        var $communityGuideLines = jQuery('#cpg');
        if($communityGuideLines.length > 0 && !$communityGuideLines.is(':checked')) {
			$communityGuideLines.addClass('event-creator__input--error');
			$communityGuideLines.siblings('.form__error-container').eq('0').addClass('form__error-container--visible');
            $allClear = false;
        }

        return $allClear;
    }

    function validateCpg(allClear) {
        const $cpgCheck = jQuery("#cpg");
        if ($cpgCheck.length && !$cpgCheck.prop("checked")) {
            $cpgCheck.one("change", function() {
				$cpgCheck.removeClass("event-creator__input--error");
				$cpgCheck.siblings('.form__error-container').eq('0').removeClass('form__error-container--visible');
            });
            allClear = false;
        }
        return allClear;
    }

    function updateRedirect() {
        const $eventName = jQuery("#event-name");
        const $redirect = jQuery("input[name=redirect_to]");
        if (!$redirect.val() && $eventName.length) {
            $redirect.val(
                window.location.origin + "/events/" + $eventName.val().replace(" ", "-")
            );
        }
    }

    function validateForm() {
        const $eventForm = jQuery("#event-form")[0];

        if ($eventForm) {
            const $requiredInputs = jQuery("input, textarea, select").filter("[required]");

            const allClear = checkInputs($requiredInputs);

            if(allClear) {
                updateRedirect();
                $eventForm.submit();
            }
        }
    }

    function toggleInputAbility(input, typeValue) {
        if (input.prop("disabled") !== false) {
			input.attr("disabled", false);
			input.attr('tabindex', '0');
            if (typeValue) {
                input.val(typeValue);
            }
            return;
        }
        input.prop("disabled", true);
    }

    function toggleLocationContainer(container, location, country, typeValue) {
		const $locationAddress = jQuery("#location-address");
        container.toggleClass("event-creator__location-edit");
        toggleInputAbility(location, typeValue);
		toggleInputAbility(country);
		if (country.val() === "online") {
			toggleVisibility($locationAddress, "Online", false);
			return;
		}
		toggleVisibility($locationAddress, "", true);
    }

    function clearPrePopErrors(container, selector) {
        const $errors = container.find("." + selector);
        if ($errors.length) {
            $errors.each(function() {
                const $this = jQuery(this);
                $this.removeClass(selector);
            });
        }
    }

    function handleAutocomplete(container, location, country, typeValue) {
        jQuery("#location-name").on("autocompleteselect", function(e) {
			const $errors = container.find(".event-creator__input--error");
			if ($errors.length > 0) {
				$errors.each(function() {
					const $this = jQuery(this);
					toggleError($this);
				});	
			}
            clearPrePopErrors(container, "event-creator__error");
            clearPrePopErrors(container, "event-creator__error-text");
            toggleLocationContainer(container, location, country, typeValue);
            container.addClass("event-creator__location-edit");
        });
    }

    function editLocation() {
        const $editBtn = jQuery("#em-location-reset a");
        const $editContainer = jQuery(".event-creator__location");
        const $countryInput = jQuery("#location-country");
        const $locationType = jQuery("#location-type");
        const $locationTypeValue = $locationType.val();
        if ($editBtn) {
            handleAutocomplete(
                $editContainer,
                $countryInput,
                $locationType,
                $locationTypeValue
            );
            $editBtn.on("click", function() {
                toggleLocationContainer(
                    $editContainer,
                    $countryInput,
                    $locationType,
                    $locationTypeValue
                );
            });
        }
    }

    function handleSubmit() {
        const $form = jQuery("#event-form");
        if ($form) {
            $form.on("submit", function(e) {
                e.preventDefault();
                validateForm();
            });
        }
    }

    function trackLocationType() {
        const $locationTypeInput = jQuery('#location-type-placeholder');
        const $locationType = jQuery('#location-type');
        $locationType.change(function() {
        const $this = jQuery(this);
            $locationTypeInput.val($this.val());
        });
	}


    function init() {
        toggleMobileEventsNav(".events__nav__toggle", ".events__nav");
        toggleMobileEventsNav(".events__filter__toggle", ".events__filter");
        eventsMobileNav();
        applyFilters();

        toggleLocationType();
        handleSubmit();
        editLocation();
        trackLocationType();
        handleOnlineEvent();
    }


    jQuery('#events-show-debug-info').click(function(e){
        e.preventDefault();
        jQuery('.events-single__debug-info').toggleClass('events-single__debug-info--hidden');
        return false;
	});
	
	jQuery("#location-name").on('focus', function() {
		const $this = jQuery(this);
		const $errorContainer = $this.next('.form__error-container');
		if ($errorContainer.hasClass("form__error--online")) {
			$errorContainer.removeClass("form__error--online");
			return
		} 
		if ($errorContainer.hasClass("form__error--in-person")) {
			$errorContainer.removeClass('form__error--in-person');
		}
	});

	jQuery('#event-cancel').on('click', function(e) {
		const $this = jQuery(this);
		const confirmation = $this.data('confirmation');
		return confirm(confirmation);
	});

    init();
});