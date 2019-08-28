jQuery(function(){

    Dropzone.autoDiscover = false;
    jQuery("#group-photo-uploader").dropzone({
        url: '/wp-admin/admin-ajax.php?action=upload_group_image',
        acceptedFiles: 'image/*',
        maxFiles: 1,
        createImageThumbnails: false,
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
        },
        error: function (file, response) {
            file.previewElement.classList.add("dz-error");
        },
        addRemoveLinks: true,
        removedfile: function(file) {
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
        }
    });


});