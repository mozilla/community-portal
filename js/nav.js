jQuery(function(){


    jQuery('#nav-trigger').click(function(e) {
        var $this = jQuery(this);

        if($this.is(':checked')) {
            jQuery('.body').addClass('body--fixed');
        } else {
            jQuery('.body').removeClass('body--fixed');
        }

    });


    if(jQuery('.nav__avatar--empty').length > 0) {
        var user = jQuery('.nav__avatar--empty').data('user');
        var avatar = new Identicon(btoa(user + 'mozilla'), { format: 'svg' }).toString();
        jQuery('.nav__avatar--empty').css({'background-image': "url('data:image/svg+xml;base64," + avatar + "')"});
    }

});