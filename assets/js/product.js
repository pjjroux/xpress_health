/*
|--------------------------------------------------------------------------
| Javascript for product listing
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-09-25
|
*/
$(document).ready(function() {
    $(".pagination").rPage();
});

/**
 * Add item to shopping cart
 * @param string supplement_id 
 */
function buy(supplement_id) {
    $.ajax({
        url: 'controller/cart_controller.php',
        method: 'GET',
        dataType: 'json',
        async: false,
        data: { 
            action: 'addToCart',
            supplement_id: supplement_id,
            supplement_qty: Number($('#'+supplement_id+'_qty').val())
        },
        success: function (json) {
            if (json.error != '') {
                new Noty({
                    type: 'error',
                    layout: 'center',
                    theme: 'bootstrap-v4',
                    modal: true,
                    text: json.error
                }).show();
            } else {
                new Noty({
                    type: 'success',
                    layout: 'center',
                    theme: 'bootstrap-v4',
                    timeout: 1500,
                    modal: true,
                    text: 'Added to shopping cart',
                    callbacks: {
                        onClose: function() {
                            window.location.reload();
                        },  
                    }
                }).show();
            }
        },
        error: function (jqxhr, textStatus, error) {
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
}


/**
 * Add 1 to the supplement qty
 * @param string supplement_id 
 */
function add_qty(supplement_id) {
    var new_qty = Number($('#'+supplement_id+'_qty').val()) + 1;
    $('#'+supplement_id+'_qty').val(new_qty);      
}

/**
 * Remove 1 from the supplement qty
 * @param string supplement_id 
 */
function remove_qty(supplement_id) {
    var qty =  Number($('#'+supplement_id+'_qty').val());

    // No negative quantities allowed
    if (qty > 1) {
        var new_qty = Number($('#'+supplement_id+'_qty').val()) - 1;
        $('#'+supplement_id+'_qty').val(new_qty);      
    }
    
}

function logout() {
    $.ajax({
        url: 'controller/login_controller.php?action=logout',
        success: function() {
          window.location.reload();
        }
      });
}