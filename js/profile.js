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

            // Validate email
            if($ele.attr('name') == 'email' && $ele.val()) {
                var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if(re.test(String($ele.val()).toLowerCase()) === false) {
                    error = true;
                    $ele.addClass("profile__input--error");
                    $errorMsg.addClass('form__error-container--visible');
                    $ele.next('.form__error-container').children('.form__error').text('Invalid Email');
                } else {
                    $ele.next('.form__error-container').children('.form__error').text('This field is required');
                }
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

    jQuery('#username').on('change keyup paste', function(e) {
        var $this = jQuery(this);
        var value = $this.val();
        var get = { };
        get.u = value;

        var $errorContainer = $this.next('.form__error-container');
        jQuery.ajax({
            url: '/wp-admin/admin-ajax.php?action=check_user',
            data: get,
            method: 'GET',
            success: function(data) {
                var response = jQuery.parseJSON(data);

                // User name is no good
                if(response == false) {
                    $this.addClass('profile__input--error');
                    $errorContainer.addClass('form__error-container--visible');
                    $errorContainer.children('.form__error').text('This username is already taken');
                } else {
                    $this.removeClass('profile__input--error');
                    $errorContainer.removeClass('form__error-container--visible');
                    $errorContainer.children('.form__error').text('This field is required');
                }
            }
        })
    });


    jQuery('#email').on('change keyup paste', function(e) {
        var $this = jQuery(this);
        var value = $this.val();
        var get = { };
        get.u = value;

        var $errorContainer = $this.next('.form__error-container');
        jQuery.ajax({
            url: '/wp-admin/admin-ajax.php?action=validate_email',
            data: get,
            method: 'GET',
            success: function(data) {
                var response = jQuery.parseJSON(data);
                if(response == false) {
                    $this.addClass('profile__input--error');
                    $errorContainer.addClass('form__error-container--visible');
                    $errorContainer.children('.form__error').text('This email is already in use');
                } else {
                    $this.removeClass('profile__input--error');
                    $errorContainer.removeClass('form__error-container--visible');
                    $errorContainer.children('.form__error').text('This field is required');
                }
            }
        })


    });


});