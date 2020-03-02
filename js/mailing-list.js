jQuery(function() {
	const newsletterForm = jQuery('.campaigns__subscribe-form');
	
		var errorArray = [];
    var newsletterErrors = document.getElementById('newsletter_errors');
    function newsletterError(e) {
        var errorList = document.createElement('ul');

        if(errorArray.length) {
            for (var i = 0; i < errorArray.length; i++) {
                var item = document.createElement('li');
                item.appendChild(document.createTextNode(errorArray[i]));
                errorList.appendChild(item);
            }
        } else {
            // no error messages, forward to server for better troubleshooting
            newsletterForm.attr('data-skip-xhr', true);
            newsletterForm.submit();
        }

        newsletterErrors.appendChild(errorList);
        newsletterErrors.style.display = 'block';
    }

    // show sucess message
    function newsletterThanks() {
        var thanks = document.getElementById('newsletter_thanks');
        // show thanks message
        thanks.style.display = 'block';
    }

    // XHR subscribe; handle errors; display thanks message on success.
    newsletterForm.on('submit', function(e) { {
		e.preventDefault();
        e.stopPropagation();
        var skipXHR = newsletterForm.attr('data-skip-xhr');
        if (skipXHR) {
            return true;
        }
        

        // new submission, clear old errors
        errorArray = [];
        newsletterErrors.style.display = 'none';
        while (newsletterErrors.firstChild) {
            newsletterErrors.removeChild(newsletterErrors.firstChild);
        }

        var fmt = document.getElementById('fmt').value;
        var email = document.getElementById('email').value;
        var newsletter = document.getElementById('newsletters').value;
        var privacy = document.querySelector('input[name="privacy"]:checked') ? '&privacy=true' : '';
        var params = 'email=' + encodeURIComponent(email) +
					'&newsletters=' + newsletter +
					privacy +
					'&fmt=' + fmt +
					'&source_url=' + encodeURIComponent(document.location.href);

        var xhr = new XMLHttpRequest();

        xhr.onload = function(r) {
            if (r.target.status >= 200 && r.target.status < 300) {
				// response is null if handled by service worker
				console.log(r);
                // if(response === null) {
                //     newsletterError(new Error());
                //     return;
                // }
                var response = r.target.response;
                // if (response.success === true) {
				// 	console.log(JSON.parse(response));
                //     newsletterForm.style.display = 'none';
                //     newsletterThanks();
                // } else {
                //     if(response.errors) {
                //         for (var i = 0; i < response.errors.length; i++) {
                //             errorArray.push(response.errors[i]);
                //         }
                //     }
                //     newsletterError(new Error());
                // }
            } else {
                newsletterError(new Error());
            }
        };

        xhr.onerror = function(e) {
            newsletterError(e);
        };

        var url = newsletterForm.attr('action');

        xhr.open('POST', `https://cors-anywhere.herokuapp.com/${url}`, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
        xhr.timeout = 5000;
        xhr.ontimeout = newsletterError;
        xhr.responseType = 'json';
        xhr.send(params);

        return false;
    }
	})
});