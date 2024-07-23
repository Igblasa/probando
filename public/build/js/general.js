//INICIO JQUERY
jQuery(document).ready(function ($) {

    $.extend(true, $.fn.dataTable.defaults, {
        dom: 'lBfrtip',
        iDisplayLength: 25,
        searching: true,
    });
    
    $(document).ready(function() {
        $('.sidebar-collapser').click(function() {
            $('.title_general, .nombre_user').toggle();
        });
    });
    
});

