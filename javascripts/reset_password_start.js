
$(document).ready(function() {
    $('.form-sendby').hide();
    $('.method_radio').click( function(e) {
        var method = e.target.value;
        $('.form-sendby').hide();
        if(e.target.checked === true && $('#'+method).css('display') != 'block') {
            $('#'+method).fadeIn(140);
        }
    });
 

    $( "#reset_password_do" ).click(function( event ) {
        if( $("#login_username").val().length == 0 )
            event.preventDefault();
    });




});
