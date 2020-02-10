jQuery(function() {

    function getFilter(option) {
        const filter = option.dataset.filter;
        return filter;
    }

    function getUrl() {
        const url = new URL(location.href);
        return url;
    }

    function getParams(url) {
        const params = new URLSearchParams(url.search.slice(1));
        return params;
    }

    function setUrlParams(url, params, key, value) {
        url.searchParams.set(key.toLowerCase(), value);
        window.location.href = url;
    }

    function applyFilters() {
        const $filters = jQuery(".events__filter__option");
        if ($filters) {
            $filters.each((i, filter) => {
                jQuery(filter).on("change", function(e) {
                    const value = encodeURI(e.target.value);
                    const filterTitle = getFilter(e.target);
                    const url = getUrl();
                    const params = getParams(url);
                    setUrlParams(url, params, filterTitle.toLowerCase(), value);
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
                $eventsNav.slideToggle();
                if (/show/gi.test($eventsNavToggle[0].innerText)) {
                    $eventsNavToggle[0].innerText = "Hide Filters";
                } else if (/hide/gi.test($eventsNavToggle[0].innerText)) {
                    $eventsNavToggle[0].innerText = "Show Filters";
                }
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

    function toggleVisibility(selector, value, hidden) {
        jQuery(selector).val(value);
        if (hidden) {
            selector
                .parent()
                .parent()
                .removeClass("event-creator__hidden");
            return;
        }
        selector
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
                $locationNameLabel.text("Online Meeting Link");
                $countryLabel.text("Where will this event be held?");
                return;
            }
            toggleVisibility($locationAddress, "", true);
            $locationNameLabel.text("Location Name");
            $countryLabel.text("Country");
        });
    }

    function handleCityForOnline($country, $city) {
        if ($country.val() === 'OE') {
            $city.val('Online Event');
            $city.prev().text("URL *");
        } else if ($city.val() === 'Online Event') {
            $city.val('');
            $city.prev().text("City *");
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
	
	function handleFocusClear($this) {
		const input_id = $this.attr("id");
		const $label = jQuery(`label[for=${input_id}]`);
		$this.removeClass("event-creator__error");
		$label.removeClass("event-creator__error-text");
		const $parent = $label.parent();
		toggleError($parent);
	}

    function clearErrors(input) {
        input.on("focus", function() {
			const $this = jQuery(this);
			if ($this.hasClass('event-creator__error')){
				handleFocusClear($this);
			}
		});
		input.on("blur", function() {
			const $this = jQuery(this);
			if ($this.hasClass('event-creator__error')){
				handleFocusClear($this);
			}
		});
    }

    function toggleError(parent, errMsg = 'This field is required') {
        const $errorPresent = parent.find("> .event-creator__error-field");
        if (!$errorPresent.length > 0) {
            
            const $errorText = jQuery(
                '<p class="event-creator__error-field"> '+ errMsg +' </p>'
            );
            parent.append($errorText);
            return;
        }
        $errorPresent.each(function() {
            $this = jQuery(this);
            $this.remove();
        });
    }

    function checkInputs(inputs) {
        let $allClear = true;
        let $first = true;

        inputs.each(function() {
            const $this = jQuery(this);

            clearErrors($this);
            $allClear = validateCpg($allClear);
            const input_id = $this.attr("id");

            if(input_id == 'location-name' && jQuery('#location-type').val() == 'online') {
                var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
                                            '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
                                            '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
                                            '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
                                            '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
                                            '(\\#[-a-z\\d_]*)?$','i');
            

                if(!pattern.test($this.val())) {
                    const $label = jQuery(`label[for=${input_id}]`);
                    const $parent = $label.parent();

                    toggleError($parent, 'Invalid URL provided');
                    $this.addClass("event-creator__error");
                    $allClear = false;
                }
            } 

            if (!$this.val() || $this.val() === "00:00" || $this.val() === "0") {
                if ($first) {
                    jQuery("html, body").animate({
                            scrollTop: $this.parent().offset().top
                        },
                        1000
                    );
                    $this.focus();
                    $first = false;
                }

                const $label = jQuery(`label[for=${input_id}]`);
                const $parent = $label.parent();
                toggleError($parent);
                $this.addClass("event-creator__error");
                $allClear = false;
            }

        });

        var $communityGuideLines = jQuery('#cpg');
        if($communityGuideLines.length > 0 && !$communityGuideLines.is(':checked')) {
            var $parent = $communityGuideLines.parent();
            toggleError($parent, 'Please agree to the community guidelines');
            $allClear = false;
        }

        return $allClear;
    }

    function validateCpg(allClear) {
        const $cpgCheck = jQuery("#cpg");
        if ($cpgCheck.length && !$cpgCheck.prop("checked")) {
            const $label = jQuery("label[for=cpg]");
            $cpgCheck.one("change", function() {
                $label.removeClass("event-creator__error-text");
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
        container.toggleClass("event-creator__location-edit");
        toggleInputAbility(location, typeValue);
        toggleInputAbility(country);
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
            const $errors = container.find(".event-creator__error-field");
            $errors.each(function() {
                const $this = jQuery(this);
                toggleError($this.parent());
            });
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

    init();
});