jQuery(function(){


    jQuery('.gdrp__cta, .gdrp__close').click(function(e){
        e.preventDefault();
        var domain = window.location.hostname;
        domain = domain.substring(domain.lastIndexOf(".", domain.lastIndexOf(".") - 1) + 1)
        document.cookie = "gdrp=true;path=/;domain=" + domain;

        jQuery('.gdrp').addClass('gdrp--hide');        


        return false;
    });



});