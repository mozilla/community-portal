jQuery(function(){


    jQuery('#nav-trigger').click(function(e) {
        var $this = jQuery(this);

        if($this.is(':checked')) {
            jQuery('.body').addClass('body--fixed');
        } else {
            jQuery('.body').removeClass('body--fixed');
        }

    });


});