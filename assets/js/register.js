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
    // Get reference values for reference select box
    $.getJSON("controller/client_controller.php", {action: "getReferences"})
        .done(function(json) {
            $.each(json, function(key, value) {
            $('#inputRef').append($('<option>').text(value['ref_description']).attr('value', value['ref_id']));
            });
        })
        .fail(function(jqxhr, textStatus, error) {
            var err = textStatus + ", " + error;
            $('.msg-text').text(err);
            $('.alert').addClass("show");
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
                $('.msg-text').text(err);
                $('.alert').addClass("show");
            });
    } 
});


/**
 * Validates form data on submit
 */
function validateForm() {
    // Password and confirm password must match
    if ($('#inputPassword').val() !== $('#inputRePassword').val()) {
        $('.msg-text').text("Passwords must match");
        $('.alert').addClass("show");
        $('#inputPassword').focus();
        return false;
    }

    // Test if user with email not already registered
    var inputID = $("#inputID").val();
    var inputEmail = $("#inputEmail").val();
    
    $.getJSON("controller/client_controller.php", {action: "isAlreadyRegistered", client_id: inputID, client_email: inputEmail})
        .done(function(json) {
            if (json.client_id) {
                $('.msg-text').text("Client already registered");
                $('.alert').addClass("show");
                $('#inputID').focus();
                return false;
            } else if (json.client_email) {
                $('.msg-text').text("Email address already in use");
                $('.alert').addClass("show");
                $('#inputEmail').focus();
                return false;
            } else {
                $('#registration_form').submit(); 
            }
        })
        .fail(function(jqxhr, textStatus, error) {
            var err = textStatus + ", " + error;
            $('.msg-text').text(err);
            $('.alert').addClass("show");
        });
}
