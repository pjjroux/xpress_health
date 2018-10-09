/*
|--------------------------------------------------------------------------
| Javascript for search function
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-10-08
|
*/

$('#btn_search').on('click', function() {
    var search_term = $('#search_box').val();
    
    var processing = new Noty({
        layout: 'center',
        theme: 'bootstrap-v4',
        modal: true,
        text: '<div align="center"><img src="./assets/img/ajax-loader.gif"></div>',
        closeWith: [''],
    }).show();

    $.ajax({
        url: 'controller/product_controller.php',
        method: 'GET',
        dataType: 'json',
        async: false,
        data: { 
            action: 'searchProducts',
            search_term: search_term,
        },
        success: function (json) {
            processing.close();

            

            console.log($.param(search,JSON.stringify(json)));
        },
        error: function (jqxhr, textStatus, error) {
            processing.close();
            var err = textStatus + ", " + error;
                new Noty({
                    type: 'error',
                    layout: 'center',
                    theme: 'bootstrap-v4',
                    text: err,
                    modal: true,
                }).show();
        }
    });       



});