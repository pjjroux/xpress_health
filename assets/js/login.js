/*
|--------------------------------------------------------------------------
| Javascript for Login page 
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-09-10
|
*/
$(document).ready(function() {
    // If page loads with the logged_in parameter redirect to home page
    if (getUrlParameter('logged_in') == 1) {
        window.location.href = 'index.php';
    }   
});

/**
 * Validates form data on submit
 */
var login_error;
function validateForm() {
    var client_email = $('#inputEmail').val();
    var client_password = $('#inputPassword').val();

    $.ajax({
        url: 'controller/login_controller.php',
        method: 'GET',
        dataType: 'json',
        async: false,
        data: { 
            action: 'validateUserLogin',
            client_email: client_email, 
            client_password: client_password
        },
        success: function (json) {
            if (json.error != '') {
                login_error = json.error;
            } else {
                // Login data validated set client_id in hidden field
                login_error = '';
                $('#client_id').val(json.client_id);
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

    if (login_error != '') {
        new Noty({
            type: 'error',
            layout: 'center',
            theme: 'bootstrap-v4',
            text: login_error
        }).show();
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