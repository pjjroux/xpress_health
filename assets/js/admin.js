/*
|--------------------------------------------------------------------------
| Javascript for admin page
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-10-02
|
*/

$(document).ready(function () {
  $('#orders').DataTable({
    "pagingType": "simple", // "simple" option for 'Previous' and 'Next' buttons only
    "ordering": false,
  });
  $('.dataTables_length').addClass('bs-select');
});

/**
 * Confirm order
 * 
 * @param string inv_num Invoice number
 */
function confirmOrder(inv_num) {
  var n = new Noty({
    layout: 'center',
    theme: 'bootstrap-v4',
    modal: true,
    text: 'Are you sure you want to confirm this order as paid?',
    buttons: [
      Noty.button('YES', 'btn btn-success', function () {
        n.close();
        var processing = new Noty({
                layout: 'center',
                theme: 'bootstrap-v4',
                modal: true,
                text: '<div align="center"><img src="./assets/img/ajax-loader.gif"></div>',
                closeWith: [''],
            }).show();
        $.ajax({
            url: 'controller/order_controller.php',
            method: 'GET',
            dataType: 'json',
            async: true,
            data: { 
                action: 'confirmOrder',
                inv_num: inv_num
            },
            success: function (json) {
                processing.close();
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
                        timeout: 2000,
                        text: 'Payment confirmed, please check email for picking slip. Client will receive an update via email',
                        modal: true,
                        callbacks: {
                            onClose: function() {
                                window.location.href = 'admin.php';
                            },  
                        }
                    }).show();
               }
            },
            error: function (jqxhr, textStatus, error) {
                processing.close();
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
 * Cancel order
 * 
 * @param string inv_num Invoice number
 */
function cancelOrder(inv_num) {
  var n = new Noty({
    layout: 'center',
    theme: 'bootstrap-v4',
    modal: true,
    text: 'Are you sure you want to cancel this order?',
    buttons: [
      Noty.button('YES', 'btn btn-success', function () {
        n.close();
        var processing = new Noty({
                layout: 'center',
                theme: 'bootstrap-v4',
                modal: true,
                text: '<div align="center"><img src="./assets/img/ajax-loader.gif"></div>',
                closeWith: [''],
            }).show();
        $.ajax({
            url: 'controller/order_controller.php',
            method: 'GET',
            dataType: 'json',
            async: true,
            data: { 
                action: 'cancelOrder',
                inv_num: inv_num
            },
            success: function (json) {
                processing.close();
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
                        timeout: 2000,
                        text: 'Order cancelled and removed from system. Client will receive an update via email',
                        modal: true,
                        callbacks: {
                            onClose: function() {
                                window.location.href = 'admin.php';
                            },  
                        }
                    }).show();
               }
            },
            error: function (jqxhr, textStatus, error) {
                processing.close();
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