/*
|--------------------------------------------------------------------------
| Javascript for contact page 
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-10-22
|
*/
$(document).ready(function() {
    // If page loads with the sent parameter show noty and reload
    if (getUrlParameter('sent') == 1) {
        new Noty({
            type: 'success',
            layout: 'center',
            theme: 'bootstrap-v4',
            modal: true,
            timeout: 2000,
            text: 'Thank you for contacting us, we will get back to you shortly',
            callbacks: {
                onClose: function() {
                    window.location.href = 'contact.php';
                },
                
            }
        }).show();
    }
});

function loading() {
    var processing = new Noty({
        layout: 'center',
        theme: 'bootstrap-v4',
        modal: true,
        text: '<div align="center"><img src="./assets/img/ajax-loader.gif"></div>',
        closeWith: [''],
    }).show();
}

// Get value from url
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
};