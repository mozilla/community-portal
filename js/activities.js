jQuery(function() {
	const handleAccordionToggle = function(accordion) {
		const $accordion = jQuery(accordion);
		const id = $accordion.attr('id');
		const $panel = jQuery(`.activity__accordion-content[aria-labelledby=${id}]`);
		if ($panel.hasClass('active')) {
			$panel.removeClass('active');
			$accordion.attr('aria-expanded', 'false');
			return;
		}
    $panel.addClass('active');
    $accordion.attr('aria-expanded', 'true');
	};

	const toggleAccordions = function() {
		const $accordionContainers = jQuery('.activity__accordion-input');
		if ($accordionContainers.length > 0) {
			$accordionContainers.each((index, accordion) => {
				const $accordion = jQuery(accordion)
				$accordion.on('click', function() {
					const $this = jQuery(this);
					handleAccordionToggle($this);
				})
			});
		}
	}

	toggleAccordions();

});