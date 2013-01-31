var ARCANUM =  ARCANUM || {};

ARCANUM.showCurrentRows = function() {
    $.ajax({
        url: "ajax_handler.php?operation=setup_admins_show_current_rows",
        type: 'POST',
        data: {},

    }).done(function ( ret ) {
        $('#current_rows').html(ret);
    });
}

ARCANUM.createFormRow = function(newindex) {
    $.ajax({
        url: "ajax_handler.php?operation=setup_admins_create_form_row",
        type: 'POST',
        data: {newindex: newindex},

    }).done(function ( ret ) {
        $('#new_row').html(ret);
    });
}



$(document).ready(function(){
    ARCANUM.showCurrentRows();

    $('#create_form_row').click( function(e) {
        e.preventDefault();
        var newindex = $('.setup_admins_fieldset').length;
        ARCANUM.createFormRow( newindex );
        $('#create_form_row').hide();
        $('body').scrollTo('#setup_bottom');
    });

});


/**
 * Source: http://lions-mark.com/jquery/scrollTo/
 */

$.fn.scrollTo = function( target, options, callback ){
  if(typeof options == 'function' && arguments.length == 2){ callback = options; options = target; }
  var settings = $.extend({
    scrollTarget  : target,
    offsetTop     : 50,
    duration      : 500,
    easing        : 'swing'
  }, options);
  return this.each(function(){
    var scrollPane = $(this);
    var scrollTarget = (typeof settings.scrollTarget == "number") ? settings.scrollTarget : $(settings.scrollTarget);
    var scrollY = (typeof scrollTarget == "number") ? scrollTarget : scrollTarget.offset().top + scrollPane.scrollTop() - parseInt(settings.offsetTop);
    scrollPane.animate({scrollTop : scrollY }, parseInt(settings.duration), settings.easing, function(){
      if (typeof callback == 'function') { callback.call(this); }
    });
  });
}