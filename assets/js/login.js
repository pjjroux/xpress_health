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


/**
 * Validates form data on submit
 */
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
            console.log(json);
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







    return false;
}