/*
|--------------------------------------------------------------------------
| Javascript for Registration page 
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-08-29
|
*/
$(document).ready(function() {
    // If page loads with the registered parameter show noty and redirect to login page within 5 seconds
    if (getUrlParameter('registered') == 1) {
        new Noty({
            type: 'success',
            layout: 'center',
            theme: 'bootstrap-v4',
            text: 'Thank you for registering, you will be redirected to the login page shortly'
        }).show();

        window.setTimeout(function() {
            window.location.href = 'login.html';
        }, 5000);
    }



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


        
});



// If ID is entered check if user data does exist and populate rest of the fields with existing data
$("#inputID").on("input", function() {
    var inputID = $("#inputID").val();

    // Strip mask formatting from value and only call if length is 13
    if (inputID.replace(/[_-]/g, '').length == 13) {
        $.getJSON("controller/client_controller.php", {action: "getClientData", client_id: inputID})
            .done(function(json) {
                if (typeof json.client_name != "undefined") {
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
});


/**
 * Validates form data on submit
 */
var client_id_exists = '';
var client_email_exists = '';
function validateForm() {
    // Password and confirm password must match
    if ($('#inputPassword').val() !== $('#inputRePassword').val()) {
        new Noty({
            type: 'error',
            layout: 'center',
            theme: 'bootstrap-v4',
            text: 'Passwords must match'
        }).show();
        $('#inputPassword').focus();
        return false;
    }
    
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
            action: 'isAlreadyRegistered',
            client_id: inputID, 
            client_email: inputEmail
        },
        success: function (json) {
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
            text: 'Client already registered'
        }).show();
        $('#inputID').focus();
        return false;
    }

    if (client_email_exists) {
        new Noty({
            type: 'error',
            layout: 'center',
            theme: 'bootstrap-v4',
            text: 'Email address already in use'
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