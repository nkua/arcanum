ARCANUM = {
    result: 0
};

$(document).ready(function() {
    var t;

    // TODO: i18n
    ARCANUM.check = function() {
        $.ajax({
            url: 'ajax_handler.php?operation=check_sms_received',
            dataType: 'json'

        }).done(function(response) { 
            if(response.result == 0) {
                // error
                $('#smsstatus').html('<div class="alert alert-error">Error</div>');

            } else if(response.result == 1) {
                t = setTimeout('ARCANUM.check();', 5000);
                // no response yet

            } else if(response.result == 2) {
                // received an invalid code
                t = setTimeout('ARCANUM.check();', 10000);
                // 'Λάβαμε λανθασμένο κωδικό. Παρακαλούμε ξαναστείλτε το SMS.'
                $('#smsstatus').html('We just received an invalid code. Please resend the SMS.');

            } else if(response.result == 3) {
                // received correct code
                clearTimeout(t);
                $('#smsstatus').html('OK');
                $('#sendinstructions').fadeOut(180, function() {
                    $('#tokenform').fadeIn(140);
                });
                ARCANUM.result = 1;
                return true;
            }
        });
    };

    t = setTimeout('ARCANUM.check();', 10000);

});
