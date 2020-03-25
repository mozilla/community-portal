jQuery(function(){
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
	
	const checkMatrixValue = function(value) {
    const username = new RegExp(/^[a-z0-9.\-_=/]+:/, 'gi');
    const domain = new RegExp(/(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$|(([a-zA-Z]|[a-zA-Z][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9])$|\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*/, 'gi');
		const validMatrixId = username.test(value) && domain.test(value);
		return validMatrixId;
	}

	const formErrorState = function(input) {
		const $this = jQuery(input);
		$this.addClass('create-group__input--error');
		$this.next('.form__error-container').addClass('form__error-container--visible');
	}

	const formClearError = function(input) {
		const $this = jQuery(input);
		$this.removeClass('create-group__input--error');
		$this.next('.form__error-container').removeClass('form__error-container--visible');
	}

	const handleMatrixInput = function() {
		const $this = jQuery(this);
		if ($this.val() !== '') {
			const validMatrixId = checkMatrixValue($this.val());
			if (!validMatrixId) {
				formErrorState(this);
				return
			} 
		}
		formClearError(this);
	}

	if (jQuery('#group-matrix')) {
		jQuery('#group-matrix').on('blur', handleMatrixInput);
	}

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
		
		const $matrixInput = jQuery('#group-matrix');
		if ($matrixInput.val() !== '') {
			const validMatrixId = checkMatrixValue($matrixInput.val());
			if (!validMatrixId) {
				formErrorState($matrixInput);
			}
		}

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
        jQuery('input[name="language"]').prop('disabled', true);
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

        if(jQuery('input[name="language"]').val().length === 0) {
            jQuery('input[name="language"]').prop('disabled', true);
        }

        if(jQuery('input[name="mygroups"]').val() == 'false') {
            jQuery('input[name="mygroups"]').prop('disabled', true);
        }

        jQuery('#group-search-form').submit();

    });

    jQuery('.groups__language-select').change(function(e) {
        var language = jQuery(this).val();
        jQuery('input[name="language"]').val(language);

        if(jQuery('input[name="location"]').val().length === 0) {
            jQuery('input[name="location"]').prop('disabled', true);
        }

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