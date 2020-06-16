jQuery(function() {

	const handleLanguageSwitcher = function($element) {
		$element.click(function(e) {
			const $this = jQuery(this);
			const $submenu = $this.find('.language-selector__select--submenu');
			$this.toggleClass('language-selector__select--active')
			$submenu.slideToggle();
		});
	}

	const init = function() {
		const $footerLanguageSwitcher = jQuery('#footer-language-selector');
		handleLanguageSwitcher($footerLanguageSwitcher);
	}

	init()
})