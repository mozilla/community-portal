jQuery(function() {

	Dropzone.autoDiscover =false;

	jQuery("#dropzone-photo-uploader").dropzone({
		url: '/wp-admin/admin-ajax.php?action=upload_group_image',
		acceptedFiles: 'image/*',
		createImageThumbnails: false,
		addRemoveLinks: true,
		init: function() {
			this.on("sending", function(file, xhr, formData){
				var nonce = jQuery('#my_nonce_field').val();
				formData.append('my_nonce_field', nonce);
				formData.append('profile_image', 'true');
			});
		},
		success: function (file, response) {
			var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
			'((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
			'((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
			'(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
			'(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
			'(\\#[-a-z\\d_]*)?$','i');
			
			response = response.replace(/\n/g, "");
			
			if(pattern.test(response.replace(/\s/g, ""))) {
				jQuery('#image-url').removeClass('profile__input--error');
				file.previewElement.classList.add("dz-success");
				file['attachment_id'] = response; // push the id for future reference
				jQuery('#image-url').val(response);
        jQuery('#dropzone-photo-uploader').css('background-image', 'url(' +  response + ')');
        if (jQuery('.event-creator__image-upload')) {
          jQuery('.event-creator__image-upload').css('background-size', 'contain')
        }
        if (jQuery('.profile__image-upload')) {
          jQuery('.profile__image-upload').css('background-size', 'cover');
        }
				jQuery('#dropzone-photo-uploader').addClass("profile__image-upload--complete");
				jQuery('.form__error--image').parent().removeClass('form__error-container--visible');
				jQuery('.dz-remove').removeClass('dz-remove--hide');
				jQuery('.dropzone__image-instructions').addClass('dropzone__image-instructions--hidden');
				if (jQuery('#image-delete')) {
					jQuery('#image-delete').removeClass('hidden');
				}
			} else {
				jQuery('.dz-preview').remove();
				jQuery('.dz-remove').removeClass('dz-remove--hide');
				jQuery('.dropzone__image-instructions').addClass('dropzone__image-instructions--hide');
				jQuery('.form__error--image').text(response);
				jQuery('.form__error--image').parent().addClass('form__error-container--visible');
			}
		},
		error: function (file, response) {
			file.previewElement.classList.add("dz-error");
		},
		sending: function(file, xhr, formData) {
		},
		removedfile: function(file) {
			return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;     
		}
	});

	const implicitSubmit = function(event, $form) {
		event.preventDefault();
		$form.submit();
	}

	const triggerDropzone = function(event) {
		event.preventDefault();
		jQuery('#dropzone-photo-uploader').click();
	}

	const handleForms = function(form) {
		
		const $form = jQuery(form);
		
		$form.keydown(function(e) {
			if (e.keyCode === 13) {
				implicitSubmit(e, $form);
			} 
		});
		
	}

	jQuery('.dropzone__image-instructions').on('click', function(e) {
		e.preventDefault();
	});

	jQuery('.dropzone__image-instructions').on('keydown', function(e) {
		if (e.keyCode === 32) {
			triggerDropzone(e);
		} 
	});

	function handleClearImage($deleteBtn) {
		const $photoUpload = jQuery("#dropzone-photo-uploader");
        const $imageInput = jQuery("#image-url");
		$photoUpload.css("background-image", "").css("background-size", "auto");
		jQuery(".dropzone__image-instructions").removeClass('dropzone__image-instructions--hidden');
		$photoUpload.css("background-position", "center");
		$imageInput.val("");
		jQuery('#dropzone-trigger').focus();
		$deleteBtn.hide();
	}

	function clearImage() {
		let $deleteBtn;
		if (jQuery("#image-delete").length) {
			$deleteBtn = jQuery("#image-delete");
		} else if (jQuery('.dz-remove').length) {
			$deleteBtn = jQuery('.dz-remove');
		} else {
			return;
		}
		$deleteBtn.on("click", function(e) {
			e.preventDefault();
			handleClearImage($deleteBtn);
		});

		$deleteBtn.on('keydown', function(e) {
			if (e.keyCode === 13) {
				e.preventDefault();
				implicitSubmit('event-form');
			}
			if (e.keyCode === 32) {
				e.preventDefault();
				handleClearImage($deleteBtn);
			}
		});	
	}

	clearImage();
	handleForms('#event-form');
	handleForms('#create-group-form');
	handleForms('#complete-profile-form');
})