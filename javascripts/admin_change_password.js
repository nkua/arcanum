var UOA = UOA || {};

UOA.arcanum = {
    generatePass: function(uid) {
        $.post('ajax_handler.php?operation=password_generator&uid='+encodeURIComponent(uid),
            {},
            function(response) {
                var newpass = response;
                $('#newpass0').attr('value', newpass);
                $('#newpass1').attr('value', newpass);
                $('#newpassshow').html(newpass);
                $('#newpassnotice').show();
            }
        );
    },

};

