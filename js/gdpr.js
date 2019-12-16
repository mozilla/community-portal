jQuery(function(){


    jQuery('.gdpr__cta, .gdpr__close').click(function(e){
        e.preventDefault();
        var domain = window.location.hostname;
        domain = domain.substring(domain.lastIndexOf(".", domain.lastIndexOf(".") - 1) + 1)
        document.cookie = "gdpr=true;path=/;domain=" + domain;

        jQuery('.gdpr').addClass('gdpr--hide');        


        return false;
    });



});