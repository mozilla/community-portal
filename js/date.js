jQuery(function(){
	jQuery('.custom_date').datepicker({
        dateFormat : 'yy-mm-dd',
        defaultDate: new Date()
    });
    

    jQuery(".custom_date").datepicker("setDate", "-0d");


});