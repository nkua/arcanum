
$(document).ready(function() {
    $('.form-sendby').hide();
    $('.method_radio').click( function(e) {
        var method = e.target.value;
        $('.form-sendby').hide();
        if(e.target.checked === true && $('#'+method).css('display') != 'block') {
            $('#'+method).fadeIn(140);
        }
    });

});
