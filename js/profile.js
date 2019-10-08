jQuery(function(){
    jQuery('#complete-profile-form').one('submit', function(e){
        e.preventDefault();
        var error = false;

        jQuery(':input[required]').each(function(index, element){
            var $ele = jQuery(element);
            var $errorMsg = $ele.next('.form__error-container');

            if($ele.val() == "" || $ele.val() == "0" || ($ele.is(':checkbox') && $ele.prop("checked") === false)) {
                error = true;           
                $ele.addClass("profile__input--error");
                $errorMsg.addClass('form__error-container--visible');
            }

        });

        if(error || jQuery('.profile__input--error').length > 0) {
            jQuery('#complete-profile-form').find('.profile__input--error:first').focus();
            return false;
        } else {
            jQuery(this).submit();
            return true;
        }


    });

    jQuery('#profile-visibility').change(function(e) {
        var $this = jQuery(this);
        var value = parseInt($this.val());

        switch(value) {
            case 2:
                jQuery('#firstname-visibility').val(0);
                jQuery('#lastname-visibility').val(2);
                jQuery('#email-visibility').val(2);
                break;
            case 1:
                jQuery('#firstname-visibility').val(1);
                jQuery('#lastname-visibility').val(1);
                jQuery('#email-visibility').val(1);
                break;
            default:
                jQuery('#firstname-visibility').val(0);
                jQuery('#lastname-visibility').val(0);
                jQuery('#email-visibility').val(0);
        }
    });
});