jQuery(function(){
    // Dropzone.autoDiscover = false;

    // jQuery("#group-photo-uploader").dropzone({
    //     url: '/wp-admin/admin-ajax.php?action=upload_group_image',
    //     acceptedFiles: 'image/*',
    //     maxFiles: null,
    //     createImageThumbnails: false,
    //     addRemoveLinks: false,
    //     init: function() {
    //         this.on("sending", function(file, xhr, formData){
    //             var nonce = jQuery('#my_nonce_field').val();
    //             formData.append('my_nonce_field', nonce);
    //             formData.append('group_image', 'true');
    //         });
    //     },
    //     success: function (file, response) {
    //         file.previewElement.classList.add("dz-success");
    //         file['attachment_id'] = response; // push the id for future reference
        
    //         var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
    //         '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
    //         '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
    //         '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
    //         '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
    //         '(\\#[-a-z\\d_]*)?$','i');
    //         response = response.replace(/\n/g, "");
    //         if(pattern.test(response.replace(/\s/g, ""))) {
                
    //             jQuery('.dz-preview').remove();
    //             jQuery('.dz-remove').removeClass('dz-remove--hide');
    //             jQuery('#image-url').val(response);
    //             jQuery('.create-group__image-upload').css('background-image', 'url(' +  response + ')');
    //             jQuery('.create-group__image-upload').css('background-size', 'cover');

    //             jQuery('.create-group__image-upload').removeClass('create-group__image-upload--uploading');
    //             jQuery('.create-group__image-upload').addClass('create-group__image-upload--done');
    //             jQuery('.create-group__image-instructions').addClass('create-group__image-instructions--hide');
    //             jQuery('.form__error--image').parent().removeClass('form__error-container--visible');
                
    //         } else {
    //             jQuery('.create-group__image-upload').css('background-image', "url('/wp-content/themes/community-portal/images/upload-image.svg')");
    //             jQuery('#image-url').val('');
    //             jQuery('.create-group__image-upload').addClass('create-group__image-upload--reset');
    //             jQuery('.form__error--image').text(response);
    //             jQuery('.dz-remove').addClass('dz-remove--hide');
    //             jQuery('.create-group__image-instructions--hide').removeClass('create-group__image-instructions--hide');
    //             jQuery('.form__error--image').parent().addClass('form__error-container--visible');
    //         } 
    //     },
    //     error: function (file, response) {
    //         file.previewElement.classList.add("dz-error");
    //     },
    //     sending: function(file, xhr, formData) {
            
    //         jQuery('.create-group__image-upload').removeClass('create-group__image-upload--done');
    //         jQuery('.create-group__image-upload').addClass('create-group__image-upload--uploading');
    //     },

	// });

    // jQuery('.create-group__image-instructions').click(function(e) {
	// 	e.preventDefault();
	// });
	


    jQuery('.create-group__input, .create-group__textarea, .create-group__select').on('change keyup input', function(e){
        var $this = jQuery(this);

        if($this.prop('required')) {
            if($this.val() != '' || $this.val() == '0') {
                $this.removeClass('create-group__input--error');
                $this.next('.form__error-container').removeClass('form__error-container--visible');
            } else {
                $this.addClass('create-group__input--error');
                $this.next('.form__error-container').addClass('form__error-container--visible');
            }
            e.stopPropagation();
        }

        
    });

    jQuery('.create-group__checkbox').on('change', function(e) {
		var $this = jQuery(this);
		var id = $this.prop('id');
		var $label = jQuery('label[for=' + id + ']');
		var tag = $this.data('value');
		var current = jQuery('#tags').val();

		if(!$label.hasClass('create-group__tag--active'))
		jQuery('#tags').val(current + ',' + tag);
		if($label.hasClass('create-group__tag--active'))
		jQuery('#tags').val(current.replace(',' + tag, ''));
		$label.toggleClass('create-group__tag--active');
		return false;
    });

    jQuery('.dz-remove').click(function(e){
        e.preventDefault();
        jQuery('.create-group__image-upload').css('background-image', "url('/wp-content/themes/community-portal/images/upload-image.svg')");
        jQuery('.create-group__image-upload').removeClass('create-group__image-upload--done');
        jQuery('.dz-preview').addClass('dz-hide');
        jQuery('.create-group__upload-image-svg').removeClass('.create-group__upload-image-svg--hide');
        jQuery('.create-group__image-instructions').removeClass('create-group__image-instructions--hide');
        jQuery('.dz-remove').addClass('dz-remove--hide');
        jQuery('#image-url').val('');
        jQuery('.create-group__image-upload').css('background-size', '75px 75px');
        return false;
    });

    jQuery('.create-group__cta').click(function(e){
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

        if(!jQuery('#group-admin-id').val()) {
            jQuery('#group-admin-id').prev('.form__error-container').addClass('form__error-container--visible');

            if(jQuery('#group-admin').length > 0) {
                var errMsg = jQuery('#group-admin').val().length <= 0 ? 'This field is required' : 'Invalid user';
    
                jQuery('#group-admin').addClass('create-group__input--error');
                jQuery('#group-admin-id').prev('.form__error-container').first('.form__error').text(errMsg);
                error = true;
            } else {
                error = false;
            }
            
        } else {
            jQuery('#group-admin-id').prev('.form__error-container').removeClass('form__error-container--visible');
            jQuery('#group-admin-id').prev('.form__error-container').first('.form__error').text('This field is required');
            jQuery('#group-admin-id').removeClass('create-group__input--error');
            jQuery('#group-admin').removeClass('create-group__input--error');
            error = false;
        }

        if(error || jQuery('.create-group__input--error').length > 0) {
            jQuery('#create-group-form').find('.create-group__input--error:first').focus();
            return false;
        } else {
            
            jQuery('#create-group-form').submit();
            return true;
        }  

        return false;
    });
  
    jQuery(document).on('click', '.group__join-cta', function(e) {
        e.preventDefault();
        var $this = jQuery(this);
        var group = $this.data('group');
        var post = { 
            'group': group
        };

        var url =  '/wp-admin/admin-ajax.php?action=join_group';

        jQuery.ajax({
            url: url,
            data: post,
            method: 'POST',
            success: function(response) {
                response = jQuery.parseJSON(response);

                if(response.status == 'success') {
                    var memberCount = parseInt(jQuery('.group__member-count').text());
                    
                    memberCount++;
                    $this.text('Leave Group');
                    jQuery('.group__member-count').text(memberCount);
                    $this.addClass('group__leave-cta');
                    $this.removeClass('group__join-cta');
                    location.reload();
                } else {
                    if(response.status === 'error' && response.msg === 'Not Logged In') {
                        window.location = '/login';
                    }
                }


            }
        });
        return false;

    });

    jQuery(document).on('click', '.group__leave-cta', function(e) {
        e.preventDefault();
        var $this = jQuery(this);
        var group = $this.data('group');
        var post = { 
            'group': group
        };

        var url = '/wp-admin/admin-ajax.php?action=leave_group';

        jQuery.ajax({
            url: url,
            data: post,
            method: 'POST',
            success: function(response) {
                response = jQuery.parseJSON(response);
                
                if(response.status == 'success') {
                    var memberCount = parseInt(jQuery('.group__member-count').text());
                    
                    memberCount--;
                    $this.text('Join Group');
                    
                    jQuery('.group__member-count').text(memberCount);

                    $this.addClass('group__join-cta');
                    $this.removeClass('group__leave-cta');
                    
                    location.reload();
                } else {
                    if(response.status === 'error' && response.msg === 'Not Logged In') {
                        window.location = '/login';
                    }
                }
            }
        });
        return false;

    });

    jQuery("#group-name").change(function(e) {
        var $this = jQuery(this);
        var name = $this.val();

        var $errorContainer = $this.next(".form__error-container");

        var get = { q: name };
        get["gid"] = jQuery("#current-group").length > 0 ? jQuery("#current-group").val() : false;

        jQuery.get("/wp-admin/admin-ajax.php?action=validate_group", get, function(response) {
            var resp = jQuery.parseJSON(response);

            // Show error
            if (resp !== true) {
                $this.addClass("create-group__input--error");
                $errorContainer.addClass("form__error-container--visible");
                $errorContainer.children(".form__error").text("This group name is already taken");
            } else {
                $this.removeClass("create-group__input--error");
                $errorContainer.removeClass("form__error-container--visible");
                $errorContainer.children(".form__error").text("This field is required");
            }
        });
    });

    jQuery('#group-admin').autoComplete({
        source: function(term, suggest) {
            jQuery.getJSON('/wp-admin/admin-ajax.php?action=get_users', { q: term }, function(data){
                var users = [];

                for(var x = 0; x < data.length; x++) {
                    users.push(data[x].data.ID+ ":" + data[x].data.user_nicename );
                }
                
                suggest(users);

            });
        },
        renderItem: function(item, search) {
            search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var data = item.split(':');
            var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
            if(data.length === 2) {
                return '<div class="autocomplete-suggestion" data-val="' + data[1] + '" data-id="' + data[0] + '">' + data[1].replace(re, "<b>$1</b>") + '</div>';    
            } else {
                return '<div class="autocomplete-suggestion" data-val="' + item + '">' + item.replace(re, "<b>$1</b>") + '</div>';
            }
        },
        onSelect: function(e, term, item) {
            e.preventDefault();
            jQuery('#group-admin-id').val(item.data('id'));

        }
    });


    jQuery('.group__nav-select').change(function(e) {
        var $this = jQuery(this);
        var value = $this.val();

        window.location = value;

    });

    jQuery('.groups__show-filter').click(function(e) {
        e.preventDefault();
        jQuery('.groups__filter-container').slideToggle({
                start: function() {
                    jQuery('.groups__filter-container').css('display','flex');
                    jQuery('.groups__filter-container').css('flex-direction','column');

                    if(jQuery('.groups__show-filter').text() == 'Hide Filters') {
                        jQuery('.groups__show-filter').text('Show Filters');
                    } else {
                        jQuery('.groups__show-filter').text('Hide Filters');
                    }
                }
          });

        return false;
    });


    jQuery('.groups__search-cta').click(function(e) {
        jQuery('input[name="tag"]').prop('disabled', true);
        jQuery('input[name="location"]').prop('disabled', true);
        jQuery('input[name="mygroups"]').prop('disabled', true);

        jQuery('#group-search-form').submit();
    });

    jQuery('.groups__tag-select').change(function(e){
        var tag = jQuery(this).val();
        jQuery('input[name="tag"]').val(tag);
        jQuery('#group-search-form').submit();
    });

    jQuery('.groups__location-select').change(function(e) {
        var location = jQuery(this).val();
        jQuery('input[name="location"]').val(location);

        if(jQuery('input[name="tag"]').val().length === 0) {
            jQuery('input[name="tag"]').prop('disabled', true);
        }

        if(jQuery('input[name="mygroups"]').val() == 'false') {
            jQuery('input[name="mygroups"]').prop('disabled', true);
        }

        jQuery('#group-search-form').submit();

    });

    jQuery('.groups__menu-link').click(function(e) {
        e.preventDefault();
        var $this = jQuery(this);
        
        if($this.data('nav') == 'mygroups') {
            jQuery('input[name="mygroups"]').val('true');
        } else {
            jQuery('input[name="mygroups"]').prop('disabled', true);
        }

        jQuery('#group-search-form').submit();


        return false;
    });

    jQuery('.groups__nav-select').change(function(e){
        var $this = jQuery(this);
        
        if($this.val() == 'mygroups') {
            jQuery('input[name="mygroups"]').val('true');
        } else {
            jQuery('input[name="mygroups"]').prop('disabled', true);
        }

        jQuery('#group-search-form').submit();
    });


    jQuery('.create-group__menu-link').click(function(e) {
        e.preventDefault();
        var $step = jQuery('input[name="step"]');

        if($step.val() == '2') {
            $step.val(0);
        }

        jQuery('#create-group-form').submit();
        return false;

    });

    jQuery('#create-group-mobile-nav').change(function(e){
        var $step = jQuery('input[name="step"]');

        if($step.val() == '2') {
            $step.val(0);
        }

        jQuery('#agree').removeAttr('required');
        jQuery('#create-group-form').submit();

    });

});