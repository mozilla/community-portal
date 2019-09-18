jQuery(function(){

    Dropzone.autoDiscover = false;
    
    jQuery("#group-photo-uploader").dropzone({
        url: '/wp-admin/admin-ajax.php?action=upload_group_image',
        acceptedFiles: 'image/*',
        maxFiles: 1,
        createImageThumbnails: false,
        addRemoveLinks: true,
        init: function() {
            this.on("sending", function(file, xhr, formData){
                var nonce = jQuery('#my_nonce_field').val();
                formData.append('my_nonce_field', nonce);
            });
        },
        success: function (file, response) {
        
            file.previewElement.classList.add("dz-success");
            file['attachment_id'] = response; // push the id for future reference
            
            jQuery('#image-url').val(response);
            jQuery('.dz-image').css('background-image', 'url(' +  response + ')');

            jQuery('.create-group__image-upload').removeClass('create-group__image-upload--uploading');
            jQuery('.create-group__image-upload').addClass('create-group__image-upload--done');
            jQuery('.create-group__image-instructions').addClass('create-group__image-instructions--hide');
        },
        error: function (file, response) {
            file.previewElement.classList.add("dz-error");
        },
        sending: function(file, xhr, formData) {
            jQuery('.create-group__image-upload').removeClass('create-group__image-upload--done');
            jQuery('.create-group__image-upload').addClass('create-group__image-upload--uploading');
        },
        removedfile: function(file) {
            jQuery('.create-group__image-upload').removeClass('create-group__image-upload--done');   
            jQuery('.create-group__image-instructions--hide').removeClass('create-group__image-instructions--hide');
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;     
        }
    });


    jQuery('.create-group__tag').click(function(e) {
        e.preventDefault();
        var $this = jQuery(this);
        var tag = $this.data('value');
        var current = jQuery('#tags').val();

        if(!$this.hasClass('create-group__tag--active'))
            jQuery('#tags').val(current + ',' + tag);
        
        if($this.hasClass('create-group__tag--active'))
            jQuery('#tags').val(current.replace(',' + tag, ''));

        $this.toggleClass('create-group__tag--active');

        return false;
    });


    var cleave = new Cleave('#group-meet-date', {
        date: true, 
        datePattern: ['m', 'd', 'Y'],
        dateMin: new Date().toISOString().slice(0,10)
    });


    jQuery('.create-group__input, .create-group__textarea, .create-group__select').on('change keyup paste', function(){
        var $this = jQuery(this);
        if($this.val() != '' || $this.val() == '0') {
            $this.removeClass('create-group__input--error');
            $this.next('.form__error-container').removeClass('form__error-container--visible');
        } else {
            $this.addClass('create-group__input--error');
            $this.next('.form__error-container').addClass('form__error-container--visible');
        }

    });

    jQuery('#create-group-form').submit(function(e){
        e.preventDefault();
        var error = false;
        jQuery(':input[required]').each(function(index, element){
            var $ele = jQuery(element);
            var $errorMsg = $ele.next('.form__error-container');

            if($ele.val() == "" || $ele.val() == "0" || ($ele.is(':checkbox') && $ele.prop("checked") === false)) {
                error = true;

                if($ele.is(':checkbox')) {
                    var checkboxes = $ele.siblings('.create-group__check');
                    if(checkboxes.length === 1) {
                        jQuery(checkboxes[0]).addClass('create-group__check--error');
                    }
                } else {
                    $ele.addClass("create-group__input--error");
                }
                $errorMsg.addClass('form__error-container--visible');
            }
        });

        if(error) {
            jQuery('#create-group-form').find('.create-group__input--error:first').focus();
            return false;
        } else {
            console.log("We are good");
            jQuery(this).submit();
            return true;
        }
        
    });



});