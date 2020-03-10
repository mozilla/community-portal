jQuery(function() {
	const handleAccordianToggle = function(accordian) {
		const $accordian = jQuery(accordian);
		const id = $accordian.attr('id');
		const $panel = jQuery(`.activity__accordion-content[aria-labelledby=${id}]`);
		if ($panel.hasClass('active')) {
			$panel.removeClass('active');
			$accordian.attr('aria-expanded', 'true');
			return;
		}
		$panel.addClass()
	};

	const toggleAccordians = function() {
		const $accordianContainers = jQuery('.activity__accordion-input');
		if ($accordianContainers.length > 0) {
			$accordianContainers.each((index, accordian) => {
				const $accordian = jQuery(accordian)
				$accordian.on('click', function() {
					const $this = jQuery(this);
					handleAccordianToggle($this);
				})
			});
		}
	}

	toggleAccordians();

});