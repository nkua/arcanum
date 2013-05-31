var Arcanum = Arcanum || {};

$(document).ready(function() {

    Arcanum.isAdequate = 0;
    
    $('#cp_newpass').delayedObserver(function() {
            var pass, pass2;
            pass = $('#cp_newpass').attr('value');
            pass2 = $('#cp_verify').attr('value');

            // remove any highlighted password
            $('.highlighted_password').removeClass('highlighted_password');

            if(pass.length > 3) {
                $.post('ajax_handler.php?operation=strength_check&password='+encodeURIComponent(pass),
                    {},
                    function(response) {
                        var responseType, succeeded, total, failedTestsMsgs, allTests, failedTests;
                        responseType = response[0];
                        
                        if(responseType == 'validation') {
                            // TODO - FIXME - also reset all existing strength messages
                            // Arcanum.resetAll();
                            return;
                        }
                        if(responseType == 'strength') {
                            succeeded = response[1];
                            total = response[2];
                            failedTestsMsgs = response[3];
                            allTests = response[4];
                            failedTests = response[5];
                        }

                        if(succeeded == total) {
                            // We have an adequate passphrase.
                            Arcanum.hidePopover();
                            // Check if the verified password is the same.
                            if(pass == pass2) {
                                Arcanum.isAdequate = 1;
                            } else {
                                Arcanum.isAdequate = 1;
                            }

                        } else {
                            // handle the case where we have to notify user
                            Arcanum.hidePopover();
                            Arcanum.showPopover(failedTestsMsgs);
                            Arcanum.isAdequate = 0;
                        }
                        return true;
                    }
                );

            } else {
                // Arcanum.hidePopover();
                Arcanum.isAdequate = 0;
            }
    }, 0.5);
   

    // Observer for password verification
    $('#cp_verify').on('blur', function() {
        
        var pass = $('#cp_newpass').attr('value'),
            pass2 = $('#cp_verify').attr('value');

        if(pass2.length > 3 && Arcanum.isAdequate == 1) {
            // Check if the verified password is the same.
            if(pass == pass2) {
                Arcanum.hidePopoverVerify();
                Arcanum.enableSubmit();
            } else {
                Arcanum.showPopoverVerify();
                $('#cp_verify').focus();
            }
        }
    });

    $('#cp_verify').delayedObserver(function() {
        if($('#cp_verify').attr('value') == '') {
            Arcanum.hidePopoverVerify();
        }
    }, 0.5);


    // Fill in password suggestions
    var fillInPasswordSuggestions = function() {

        $.post('ajax_handler.php?operation=password_suggestions', function(response) {
            $('#password_suggestions').append('<table>');
        
            for(var i = 0; i<4; i++) {

                $('#password_suggestions').append(
                    _.template(
                        "<tr><% _.each(passwords, function(p) { %> <td class='suggested_password'><%= p %></td> <% }); %></tr>",
                        {passwords : response.slice(i, i+6) }
                    )

                );
            }
            $('#password_suggestions').append('</table>');
        });

    };

    fillInPasswordSuggestions();

    $('#password_suggestions').click( function(e) {
        var newpass = e.target.innerHTML;
        
        $('.highlighted_password').removeClass('highlighted_password');
        $(e.target).addClass('highlighted_password');
        $('#cp_newpass').val(newpass);
        $('#cp_verify').val(newpass);
        Arcanum.hidePopover();
    });



    $('#get_other_suggestions').click( function() {
        $('#password_suggestions').html('');
        fillInPasswordSuggestions();
    });


});

Arcanum = {
    isAdequate: 0,

    allTestNodes: new Array(), 

    allTests: new Array(), 

    showPopover: function(failedTests) {
        $('#popover_mark_cp_newpass').popover({
            html: true,
            trigger: 'manual',
            placement: 'left',
            title: '<span style="color: red">Ο κωδικός δεν είναι αρκετά ισχυρός.</span>',
            content: this.buildPopoverContent(failedTests)
        });

        $('#popover_mark_cp_newpass').popover('show');
        
        
    },

    showPopoverVerify: function(failedTests) {
        $('#popover_mark_cp_verify').popover({
            html: true,
            trigger: 'manual',
            placement: 'left',
            title: '<span style="color: red">Ελέγξτε την επαλήθευση του κωδικού</span>',
            content: 'Οι δύο κωδικοί δεν είναι οι ίδιοι.'
        });

        $('#popover_mark_cp_verify').popover('show');
    },

    hidePopover: function() {
        $('#popover_mark_cp_newpass').popover('hide');
        $('#popover_mark_cp_newpass').popover('destroy');
        this.hidePopoverVerify();
    },

    hidePopoverVerify: function() {
        $('#popover_mark_cp_verify').popover('hide');
        $('#popover_mark_cp_verify').popover('destroy');
    },

    buildPopoverContent: function(failedTests) {
        var i, text = '<ul>';
        for(i=0; i<failedTests.length; i++) {
            text = text + '<li>' + failedTests[i] + '</li>';
        }
        text = text + '</ul>';
        return text;
    },
    
    enableSubmit: function() {
        $('#changepass_do').removeAttr('disabled').removeClass('disabled').addClass('warning');
    },
    
    disableSubmit: function() {
        $('#changepass_do').attr('disabled', 'disabled').addClass('disabled');
    },
    
  
}

