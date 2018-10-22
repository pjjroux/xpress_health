/*
|--------------------------------------------------------------------------
| Javascript for Account page 
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-09-23
|
*/
$(document).ready(function() {
    // If page loads with the registered parameter show noty and redirect to login page within 5 seconds
    if (getUrlParameter('updated') == 1) {
        new Noty({
            type: 'success',
            layout: 'center',
            theme: 'bootstrap-v4',
            timeout: 2000,
            modal: true,
            text: 'Account information updated successfully'
        }).show();
    }

    if (action == 'edit') {
        // Get reference values for reference select box
        $.getJSON("controller/client_controller.php", {action: "getReferences"})
            .done(function(json) {
                $.each(json, function(key, value) {
                    $('#inputRef').append($('<option>').text(value['ref_description']).attr('value', value['ref_id']));
                });
            })
            .fail(function(jqxhr, textStatus, error) {
                var err = textStatus + ", " + error;
                new Noty({
                    type: 'error',
                    layout: 'center',
                    theme: 'bootstrap-v4',
                    text: err
                }).show();
            });

        // Input masks for form validation
        $("#inputID").inputmask("9999999999999",{
            greedy: false,
            showMaskOnFocus: false,
            showMaskOnHover: false
        });
        $('#inputTelHome, #inputTelWork, #inputCell').inputmask('(999)-(999)-(9999)',{
            greedy: false,
            showMaskOnFocus: false,
            showMaskOnHover: false
        });
        $("#inputPostal").inputmask("9{0,4}",{
            greedy: false,
            showMaskOnFocus: false,
            showMaskOnHover: false
        });

        // Get user data
        $.getJSON("controller/client_controller.php", {action: "getClientData"})
            .done(function(json) {
                if (typeof json.client_name != "undefined") {
                    $("#inputID").val(json.client_id);
                    $("#inputEmail").val(json.client_email);
                    $("#inputName").val(json.client_name);
                    $("#inputSurname").val(json.client_surname);
                    $("#inputAddress").val(json.client_address);
                    $("#inputPostal").val(json.client_postalcode);
                    $("#inputTelHome").val(json.client_tel_home);
                    $("#inputTelWork").val(json.client_tel_work);
                    $("#inputCell").val(json.client_tel_cell);
                    $("#inputRef").val(json.ref_id);
                }
            })
            .fail(function(jqxhr, textStatus, error) {
                var err = textStatus + ", " + error;
                new Noty({
                    type: 'error',
                    layout: 'center',
                    theme: 'bootstrap-v4',
                    text: err
                }).show();
            });
    }

    if (action == 'orders') {
        $('#orders').DataTable({
            "pagingType": "simple", // "simple" option for 'Previous' and 'Next' buttons only
        });
        $('.dataTables_length').addClass('bs-select');
    } 
});

/**
 * Validates form data on submit
 */
var client_id_exists = '';
var client_email_exists = '';
function validateForm() {
    // Test if user with email not already registered
    var inputID = $("#inputID").val();
    var inputEmail = $("#inputEmail").val();

    client_id_exists = false;
    client_email_exists = false;
    $.ajax({
        url: 'controller/client_controller.php',
        method: 'GET',
        dataType: 'json',
        async: false,
        data: { 
            action: 'isAlreadyRegisteredUpdate',
            client_id: inputID, 
            client_email: inputEmail
        },
        success: function (json) {
            console.log(json);
            if (json.client_id) {
                client_id_exists = true;
            }
           
            if (json.client_email) {
                client_email_exists = true;
            }
        },
        error: function (jqxhr, textStatus, error) {
            var err = textStatus + ", " + error;
                new Noty({
                    type: 'error',
                    layout: 'center',
                    theme: 'bootstrap-v4',
                    text: err
                }).show();
        }
    });


    if (client_id_exists) {
        new Noty({
            type: 'error',
            layout: 'center',
            theme: 'bootstrap-v4',
            text: 'A user with this ID is already registered'
        }).show();
        $('#inputID').focus();
        return false;
    }

    if (client_email_exists) {
        new Noty({
            type: 'error',
            layout: 'center',
            theme: 'bootstrap-v4',
            text: 'Email address already in use by another user'
        }).show();
        $('#inputEmail').focus();
        return false;
    }
    
    return true;
}

// Get value from url
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
};

function logout() {
    $.ajax({
        url: 'controller/login_controller.php?action=logout',
        success: function() {
            window.location.href = 'index.php';
        }
      });
}


// Email invoice to client
function email_invoice(inv_num) {
    var n = new Noty({
        layout: 'center',
        theme: 'bootstrap-v4',
        modal: true,
        text: 'Are you sure you want to request invoice by email for this order?',
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
                    action: 'requestInvoiceMail',
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
                            text: 'Request sent you will receive an invoice by email shortly',
                            modal: true,
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