jQuery(function() {

    function toggleLightboxVisibility(lightbox) {
        lightbox.toggleClass("lightbox-show");
	}

    function toggleLightbox(lightboxID, openBtnID, closeBtnID, firstSelector) {
		var $lightbox = jQuery(lightboxID);
		$lightbox.on('click', function(e) {
			closeByClickingOff(e.target);
		});
        if ($lightbox.length > 0) {
            var $openBtn = jQuery(openBtnID);
            var $closeBtn = jQuery(closeBtnID);
            var $firstBtn = $lightbox.find(firstSelector).first();

            $openBtn.on("click", function(e) {
                e.preventDefault();
                toggleLightboxVisibility($lightbox);
                $firstBtn.focus();
                jQuery("body").addClass("noscroll");
            });

            $closeBtn.on("click", function() {
                $openBtn.focus();
                toggleLightboxVisibility($lightbox);
                jQuery("body").removeClass("noscroll");
            });

            var $lastBtn = jQuery(`${lightboxID} ${firstSelector}`).last();
            trapFocus($closeBtn, $lastBtn);
            closeByKeyboard($lightbox);
        }
    }

    function closeByKeyboard($lightbox) {
        $lightbox.on("keyup", e => {
            e.preventDefault();
            if (e.keyCode === 27) {
                toggleLightboxVisibility($lightbox);
                jQuery("body").removeClass("noscroll");
            }
        });
	}
	
	function closeByClickingOff(target) {
		const $target = jQuery(target);
		if ($target.hasClass('lightbox')) {
			toggleLightboxVisibility($target);
		}
	}

    function trapFocus($closeBtn, $lastBtn) {
        $closeBtn.on("keydown", e => {
            if (e.keyCode === 9 && e.shiftKey) {
                e.preventDefault();
                $lastBtn.focus();
            }
        });

        $lastBtn.on("keydown", function(e) {
            if (e.keyCode === 9 && !e.shiftKey) {
                e.preventDefault();
                $closeBtn.focus();
            }
        });
    }

    function handleCopyToClipboardClick() {
        var $copyTrigger = jQuery("#copy-share-link");
        $copyTrigger.on("click", function(e) {
            e.preventDefault();
            
            copyToClipboard();
            $copyTrigger.text("Link Copied");
        });
    }

    function copyToClipboard() {
        var el = document.createElement("textarea");
        el.value = location.href;
        el.setAttribute("readonly", "");
        el.style.position = "absolute";
        el.style.left = "-9999px";
        document.body.appendChild(el);
        el.select();
        document.execCommand("copy");
        document.body.removeChild(el);
    }

    function initLightbox() {
        toggleLightbox(
            "#attendees-lightbox",
            "#open-attendees-lightbox",
            "#close-attendees-lightbox",
            ".events-single__member-card a"
        );

        toggleLightbox(
            "#events-share-lightbox",
            "#open-events-share-lightbox",
            "#close-share-lightbox",
            "a"
        );

        toggleLightbox(
            "#groups-share-lightbox",
            ".group__share-cta",
            "#close-share-lightbox",
            "a"
		);
		
		toggleLightbox(
			"#campaign-rsvp-lightbox",
			".campaign__hero-cta--no-account",
			"#close-rsvp-lightbox",
			"a"
		)


        toggleLightbox('#activity-share-lightbox', '.activity__cta--share', "#close-share-lightbox", 'a');
        toggleLightbox('#campaign-share-lightbox', '.campaign__share-cta', "#close-share-lightbox", 'a');

        handleCopyToClipboardClick();
    }

    initLightbox();

})