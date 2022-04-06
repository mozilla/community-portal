jQuery(function() {

	const getFilter = function(option) {
		const filter = option.dataset.filter;
		return filter;
	}

	const getUrl = function() {
		const url = new URL(location.href);
		return url;
	}

	const getParams = function(url) {
		const params = new URLSearchParams(url.search);
		return params;
	}

	const setUrlParams = function(url, key, value) {
		url.searchParams.set(key.toLowerCase(), value);
		return url;
	}

	const relocate = function(url) {
		window.location.href = url;
	}

	const applyFilters = function() {
		const $filters = jQuery(".events__filter__option");
		if ($filters) {
			$filters.each((i, filter) => {
				jQuery(filter).on("change", function(e) {
					let value = encodeURI(e.target.value);
					const filterTitle = getFilter(e.target);
					let url = getUrl();
					const params = getParams(url);
					if (params.has('pno')) {
						setUrlParams(url, 'pno', '1');
					}
					url = setUrlParams(url, filterTitle.toLowerCase(), value);
					relocate(url);
				});
			});
		}
	}

	const toggleMobileEventsNav = function(className, toggleTarget) {
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

	const eventsMobileNav = function() {
		const $viewOptions = jQuery(".events__nav--mobile select");
		if ($viewOptions) {
			$viewOptions.on("change", function(e) {
				const url = getUrl();
				const params = getParams(url);
				setUrlParams(url, params, "view", this.value);
			});
		}
	}


	// FORM VALIDATION
	const toggleVisibility = function($selector, value, hidden) {
		$selector.val(value);
		if (hidden) {
			$selector
				.parent()
				.parent()
				.addClass("event-creator__hidden");
			return;
		}
		$selector
			.parent()
			.parent()
			.removeClass("event-creator__hidden");
		}

	const toggleStrings = function($label, className, online) {
		if (online) {
			$label.removeClass(`${className}--in-person`);
			$label.addClass(`${className}--online`);
			return;
		}
		$label.addClass(`${className}--in-person`);
		$label.removeClass(`${className}--online`);
	}

	const toggleError = function(input, error = true) {
		if (error) {
			input.addClass('event-creator__input--error');
			input.next('.form__error-container').addClass('form__error-container--visible');
			return;
		}
		input.removeClass('event-creator__input--error');
		input.next('.form__error-container').removeClass('form__error-container--visible');
	}

	const clearErrors = function(input) {
		input.on("focus blur", function() {
			const $this = jQuery(this);
			if ($this.hasClass('event-creator__input--error')){
				toggleError($this, false);
			}
		});
	}

	const validateCpg = function(allClear) {
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

	const checkInputs = function(inputs) {
		let $allClear = true;
		let $first = true;

		inputs.each(function() {
			const $this = jQuery(this);

			clearErrors($this);
			$allClear = validateCpg($allClear);
			const input_id = $this.attr("id");

			if(input_id == 'location-name-mozilla') {
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

	const updateRedirect = function() {
		const $eventName = jQuery("#event-name");
		const $redirect = jQuery("input[name=redirect_to]");
		if (!$redirect.val() && $eventName.length) {
			$redirect.val(
				window.location.origin + "/events/" + $eventName.val().replace(" ", "-")
			);
		}
	}

	const validateForm = function() {
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

	const handleSubmit = function() {
		const $form = jQuery("#event-form");
		if ($form) {
			$form.on("submit", function(e) {
				e.preventDefault();
				validateForm();
			});
		}
	}

	const clearLocationNameErrors = function() {
		jQuery("#location-name-mozilla").on('focus', function() {
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
	}

	const handleEventCancellation = function() {
		jQuery('#event-cancel').on('click', function(e) {
			const $this = jQuery(this);
			const confirmation = $this.data('confirmation');
			return confirm(confirmation);
		});
	}

	const showDebugInformation = function() {
		jQuery('#events-show-debug-info').click(function(e){
			e.preventDefault();
			jQuery('.events-single__debug-info').toggleClass('events-single__debug-info--hidden');
			return false;
		});
	}

	// LOCATION HANDLER
	const editContainer = function() {
		const $container = jQuery(".event-creator__location");
		return $container.hasClass('event-creator__location-edit');
	}

	const checkContainerClass = function() {
		const editMode = editContainer();
		if (editMode) {
			const fields = [ 'name-mozilla', 'id', 'address'];
			handleLocationEdit(fields);
			clearLocationFields(fields);
		}
	}

	const clearPrePopErrors = function(selector) {
		const $errors = jQuery(`.event-creator__location .${selector}`);
		if ($errors.length) {
				$errors.each(function() {
				const $this = jQuery(this);
				$this.removeClass(selector);
				});
		}
	}

	const handleCityForOnline = function($country, $city) {
		checkContainerClass();
		if ($country.val() === 'OE') {
			string = $city.data('string');
			$city.val(string);
			clearPrePopErrors('form__error-container--visible');
			clearPrePopErrors('event-creator__input--error');
			return;
		}
		$city.val('');
	}

	const toggleSelectAbility = function(selects, enabled) {
		selects.forEach((select) => {
			let $el = jQuery(`#location-${select}`);
			if (enabled) {
				$el.prop('disabled', false);
				$el.attr('tabindex', '0');
				return;
			}
			$el.prop('disabled', true);
			$el.prop('tabindex', '-1');
		})
	}

	const toggleInputAbility = function(inputs, enabled) {
		inputs.forEach((input) => {
			let $el = jQuery(`#location-${input}`);
			if (enabled) {
				$el.prop('readonly', false);
				$el.attr('tabindex', '0');
				return;
			}
			$el.prop('readonly', true);
			$el.prop('tabindex', '-1');
		})
	}

	const toggleLocationContainer = function(display = false) {
		const $container = jQuery(".event-creator__location");
		if (display) {
			$container.removeClass("event-creator__location-edit");
			return;
		}
		$container.addClass("event-creator__location-edit");
	}

	const iterateThroughErrors = function($errors) {
		$errors.each(function() {
			const $this = jQuery(this);
			toggleError($this);
		});
	}

	const displayReset = function(display) {
		const resetBtn = jQuery('#em-location-reset');
		if (display) {
			resetBtn.show();
			return;
		}
		resetBtn.hide();
	}

	const handleAutocomplete = function() {
		displayReset(true);
		const $errors = jQuery(".event-creator__location .event-creator__input--error");
		const inputs = ['address', 'name-mozilla', 'town'];
		const selects = ['type', 'country'];
		if ($errors.length > 0) {
			iterateThroughErrors($errors);
		}
		clearPrePopErrors("event-creator__input--error");
		clearPrePopErrors("form__error-container--visible");
		toggleInputAbility(inputs, false);
		toggleSelectAbility(selects, false);
		toggleLocationContainer();
	}

	const clearLocationFields = function(fields) {
		fields.forEach((field) => {
			let $el = jQuery(`#location-${field}`);
			if (field === 'country') {
				$el.val('0');
				return;
			}
			$el.val('');
		});

	}

	const handleLocationEdit = function(fields) {
		const inputs = ['name-mozilla', 'town', 'address'];
		const selects = ['type', 'country'];
		toggleLocationContainer(true);
		toggleInputAbility(inputs, true);
		toggleSelectAbility(selects, true);
		displayReset(false);
	}

	const editLocation = function() {
		const $editBtn = jQuery("#em-location-reset")
		if ($editBtn) {
		$editBtn.on("click", function(e) {
				e.preventDefault();
				const fields = [ 'name-mozilla', 'country--hidden', 'id', 'country', 'town', 'address'];
				handleLocationEdit(fields);
			});
		}
	}

	const toggleLocationType = function (online, address = "") {
		const $locationAddress = jQuery("#location-address");
		const $locationNameLabel = jQuery("#location-name-label");
		const $countryLabel = jQuery("#location-country-label");
		if (online) {
			toggleVisibility($locationAddress, 'online', true);
			toggleStrings($locationNameLabel, 'event-creator__label', true);
			toggleStrings($countryLabel, 'event-creator__label', true);
			return;
		}
		toggleVisibility($locationAddress, address, false);
		toggleStrings($locationNameLabel, 'event-creator__label', false);
		toggleStrings($countryLabel, 'event-creator__label', false);
	}

	const handleOnlineEvent = function() {
		const $locationCountry = jQuery('#location-country');
		const $locationCity = jQuery('#location-town');

		if ($locationCountry.length > 0) {
				$locationCountry.on('change', function(e) {
				const $this = jQuery(this);
				handleCityForOnline($this, $locationCity);
				});
		}
	}

	const handleAutocompleteSelection = function(data) {
		data.type = data.address === 'Online' ? 'online' : 'address';
		for (key in data) {
			let $el = jQuery(`#location-${key}`);
			$el.val(data[key]);
			let $el_hidden = jQuery(`#location-${key}--hidden`);
			if ($el_hidden.length > 0) {
				$el_hidden.val(data[key]);
			}
		}
		const $location_name = jQuery('#location-name-mozilla');
		$location_name.val(data.name);
		$location_type = data.type === 'online' ? true : false;
		toggleLocationType($location_type, data.address);
		handleAutocomplete();
	}


	jQuery('#location-name-mozilla').autoComplete({
		source: function(term, suggest) {
				jQuery.getJSON('/wp-admin/admin-ajax.php?action=get_locations', { q: term }, function(data){

				const locations = data.map((location) => {
					return {
						name: location.location_name,
						id: location.location_id,
						country: location.location_country,
						town: location.location_town,
						address: location.location_address,
						type: location.location_type,
					};
				});
				suggest(locations);
				});
		},
		renderItem: function(item, search) {
			return `<div class="autocomplete-suggestion" data-name="${item.name}" data-id=${item.id} data-country="${item.country}" data-type="${item.type}" data-town="${item.town}" data-address="${item.address}">${item.name}</div>`
		},
		onSelect: function(e, term, item) {
			e.preventDefault();
			handleAutocompleteSelection(item.data());
		}
	});

	const handleLocationSelectChange = function(id, value) {
		const $hiddenInput = jQuery(`#${id}--hidden`);
		$hiddenInput.val(value);
	}

	const handleLocationTypeChange = function() {
		const $locationTypeInput = jQuery("#location-type");
		$locationTypeInput.on("change", function() {
			$this = jQuery(this);
			handleLocationSelectChange('location-type', $this.val());
			if ($this.val() === 'online') {
				toggleLocationType(true);
				return;
			}
			toggleLocationType(false);
		});
	}

	const trackSelectValues = function() {
		const ids = ['location-country']
		ids.forEach((id) => {
			const $select = jQuery(`#${id}`);
			$select.on('change', function() {
				const $this = jQuery('this');
				handleLocationSelectChange(id, $this.val());
			})
		});
	}

	const timezoneInEvent = function() {
		const data = jQuery('.timezone span');
		if ( data.lenght != 0 ) {
			var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
			var locale = Intl.DateTimeFormat().resolvedOptions().locale;
			var date_start = new Date(data.data('start-date') + "T" + data.data('start-time') + data.data('timezone-offset'));
			console.log(data.data('start-date') + " " + data.data('start-time') + " " + data.data('timezone-offset'))
			var date_end = new Date(data.data('end-date') + "T" + data.data('end-time') + data.data('timezone-offset'));
			data.html(date_start.toLocaleString(locale, { timeZone: tz, month: 'numeric', day: 'numeric', hour: '2-digit', minute: '2-digit' }) + ' - ' + date_end.toLocaleString(locale, { timeZone: tz, month: 'numeric', day: 'numeric', hour: '2-digit', minute: '2-digit' }))
		}
	}

	const init = function() {
		toggleMobileEventsNav(".events__nav__toggle", ".events__nav");
		toggleMobileEventsNav(".events__filter__toggle", ".events__filter");
		eventsMobileNav();
		applyFilters();

		editLocation();

		// LOCATION HANDLER
		handleLocationTypeChange();
		trackSelectValues();

		// FORM VALIDATION
		handleSubmit();

		// Event handlers
		clearLocationNameErrors();
		handleEventCancellation();
		showDebugInformation();
		handleOnlineEvent();

		timezoneInEvent();
	}

	init();
});
