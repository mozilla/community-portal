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

  function setHeightOfDivs(selector) {
    let t = 0;
    let t_elem;
    const element = ".events " + selector;
    const $cards = jQuery(element);
    $cards.each(function() {
      $this = jQuery(this);
      if ($this.outerHeight() > t) {
        t_elem = $this;
        t = $this.outerHeight();
      }
    });
    $cards.each(function() {
      jQuery(this).css("min-height", t_elem.outerHeight());
    });
  }

  function toggleVisibility(selector, value, hidden) {
    selector.val(value);
    if (hidden) {
      selector.parent().removeClass("event-creator__hidden");
      return;
    }
    selector.parent().addClass("event-creator__hidden");
  }

  function toggleLocationType() {
    const $locationTypeInput = jQuery("#location-type");
    const $locationName = jQuery("#location-name");
    const $locationNameLabel = jQuery("#location-address-label");
    $locationName.val("Online");
    $locationTypeInput.on("change", function() {
      $this = jQuery(this);
      if ($this.val() === "online") {
        toggleVisibility($locationName, "Online", false);
        $locationNameLabel.text("Online Meeting Link *");
        return;
      }
      toggleVisibility($locationName, "", true);
      $locationNameLabel.text("Address");
    });
  }

  function cpgAgreement() {
    const $cpgCheckbox = jQuery("#cpg");
    const $submitBtn = jQuery("#event-creator__submit-btn");
    $cpgCheckbox.on("change", function() {
      const $this = jQuery(this);
      if (this.checked) {
        $submitBtn.prop("disabled", false);
        return;
      }
      $submitBtn.prop("disabled", true);
    });
  }

  function checkInputs(inputs) {
    let $allClear = true;
    inputs.each(function() {
      const $this = jQuery(this);
      if (!$this.val()) {
        this.addClass("event-form__error");
        $allClear = false;
      }
    });
    return $allClear;
  }

  function validateForm() {
    const $eventForm = jQuery("#event-form");
    if ($eventForm) {
      const $requiredInputs = jQuery("input,textarea,select").filter(
        "[required]"
      );
      const $submitBtn = $eventForm.find("#event-creator__submit-btn");
      $submitBtn.on("click", function(e) {
        e.preventDefault();
        const allClear = checkInputs($requiredInputs);
        if (allClear) {
          $eventForm.submit();
        }
      });
    }
  }

  function init() {
    toggleMobileEventsNav(".events__nav__toggle", ".events__nav");
    toggleMobileEventsNav(".events__filter__toggle", ".events__filter");
    eventsMobileNav();
    applyFilters();
    window.addEventListener("resize", setHeightOfDivs);
    setHeightOfDivs(".events__tags");
    setHeightOfDivs(".card__description");
    toggleLocationType();
    cpgAgreement();
    // validateForm();
  }

  init();
});
