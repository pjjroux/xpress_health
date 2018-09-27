/*
|--------------------------------------------------------------------------
| Javascript for shopping cart and checkout
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-09-20
|
*/

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

    $.ajax({
        url: 'controller/cart_controller.php',
        method: 'GET',
        dataType: 'json',
        async: false,
        data: { 
            action: 'add_qty',
            supplement_id: supplement_id, 
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
                window.location.reload();
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
 * Remove 1 from the supplement qty
 * @param string supplement_id 
 */
function remove_qty(supplement_id) {
    var qty =  Number($('#'+supplement_id+'_qty').val());

    // No negative quantities allowed
    if (qty > 1) {
        var new_qty = Number($('#'+supplement_id+'_qty').val()) - 1;
        $('#'+supplement_id+'_qty').val(new_qty);

        $.ajax({
            url: 'controller/cart_controller.php',
            method: 'GET',
            dataType: 'json',
            async: false,
            data: { 
                action: 'remove_qty',
                supplement_id: supplement_id, 
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
                    window.location.reload();
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
    
}


/**
 * Remove 1 from the supplement qty
 * @param string supplement_id 
 */
function remove_from_cart(supplement_id) {
    var n = new Noty({
        layout: 'center',
        theme: 'bootstrap-v4',
        modal: true,
        text: 'Do you want to remove the item/s from your cart?',
        buttons: [
          Noty.button('YES', 'btn btn-success', function () {
            $.ajax({
                url: 'controller/cart_controller.php',
                method: 'GET',
                dataType: 'json',
                async: false,
                data: { 
                    action: 'removeFromCart',
                    supplement_id: supplement_id, 
                },
                success: function (json) {
                    n.close();
                    if (json.error != '') {
                        new Noty({
                            type: 'error',
                            layout: 'center',
                            theme: 'bootstrap-v4',
                            text: json.error,
                            modal: true,
                        }).show();
                    } else {
                        new Noty({
                            type: 'success',
                            layout: 'center',
                            theme: 'bootstrap-v4',
                            timeout: 1500,
                            text: 'Removed from shopping cart',
                            modal: true,
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
                            modal: true,
                            text: err
                        }).show();
                }
            }); 
          }, {id: 'button1', 'data-status': 'ok'}),
      
          Noty.button('NO', 'btn btn-danger', function () {
              n.close();
          })
        ]
      });
      n.show();
}

/**
 * Checkout shopping cart and place order
 */
function checkout() {
    var n = new Noty({
        layout: 'center',
        theme: 'bootstrap-v4',
        modal: true,
        text: 'Are you sure you want to place this order?',
        buttons: [
          Noty.button('YES', 'btn btn-success', function () {
            $.ajax({
                url: 'controller/cart_controller.php',
                method: 'GET',
                dataType: 'json',
                async: false,
                data: { 
                    action: 'checkout',
                },
                success: function (json) {
                    n.close();
                    if (json.error != '') {
                        new Noty({
                            type: 'error',
                            layout: 'center',
                            theme: 'bootstrap-v4',
                            text: json.error,
                            modal: true,
                        }).show();
                    } else {
                        new Noty({
                            type: 'success',
                            layout: 'center',
                            theme: 'bootstrap-v4',
                            timeout: 1500,
                            text: 'Order placed, you will receive a confirmation email shortly',
                            modal: true,
                            callbacks: {
                                onClose: function() {
                                    window.location.href = 'index.php';
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
                            modal: true,
                            text: err
                        }).show();
                }
            }); 
          }, {id: 'button1', 'data-status': 'ok'}),
      
          Noty.button('NO', 'btn btn-danger', function () {
              n.close();
          })
        ]
      });
      n.show();
}
