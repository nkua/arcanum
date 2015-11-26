var ARCANUM =  ARCANUM || {};

ARCANUM.renderUsersTable = function(data) {
    var h, i, loc;
    
    h = '<table class="table table-striped"><thead><tr>';

    for(var attr in summaryAttrs) {
        h = h + '<td>' + summaryAttrs[attr] + '</td>';
    }
    
    h = h+ '</tr></thead><tbody>';

    if(document.location.pathname.match('/admin_change_password')) {
        loc = 'admin_change_password.php?uid=';
    } else {
        loc = 'admin_show_user.php?uid=';
    }

    for(i=0; i < data.length; i++) {
if(data[i].cn==null) {data[i].cn="--"}

        h = h + '<tr><td>' + data[i].cn + '</td><td><a href="'+ loc + data[i].uid + '">' + data[i].uid + '</a></td>';
 for(var attr in summaryAttrs) {
            if(attr == 'cn' || attr == 'mail' || attr == 'uid') continue;
            h = h + '<td>' + data[i][attr] + '</td>';
        }
        h = h + '</tr>';
    }

    h = h + '</tbody></table>';

    $('#bodysearchresults').html(h);
}


ARCANUM.searchFor = function(q) {
    $.ajax({
        url: "ajax_handler.php?operation=ldap_search",
        type: 'POST',
        data: q,
        beforeSend: function ( xhr ) {
            $('#bodysearchresults').html('<img src="images/loading_searching_people.gif" alt="" style="margin: auto;" />');
        }

    }).done(function ( r ) {

        if(r.result == -2) return;
        $('#bodysearchresults').show();
        if(r.result == -1) {
            $('#bodysearchresults').html('<div class="alert alert-error">Σφάλμα LDAP Server ή λανθασμένο φίλτρο. Παρακαλούμε ξαναπροσπαθήστε.</div>');
            return;
        }

        if(r.result == 0) {
            $('#bodysearchresults').html('Δε βρέθηκαν χρήστες.');
            return;
        }

        $('#massivechanges').show();

        // Fill in the massive changes hidden form element, with either the filter or the
        // simple query:

        if($('#advancedsearchquery').val() != '') {
            $('#massivechangequery').val('');
            $('#massivechangefilter').val($('#advancedsearchquery').val());
        } else {
            $('#massivechangefilter').val('');
            $('#massivechangequery').val($('#bodysearchquery').val());
        }

        $('#numericresult_num').html(r.data.length);
        $('#numericresult').show();
        ARCANUM.renderUsersTable(r.data);
    });
}

ARCANUM.getURLParameter = function(name) {
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null;
}


$(document).ready(function(){
    $('#bodysearchform').submit( function(e) {
        e.preventDefault();
        var q = $('#bodysearchquery').val();
        ARCANUM.searchFor( { query: q } );
    });
    $('#advancedbodysearchform').submit( function(e) {
        e.preventDefault();
        var q = $('#advancedsearchquery').val();
        ARCANUM.searchFor( { filter: q });
    });

    $('#massivechanges').hide();
    $('#numericresult').hide();

    var clear = function() {
        $('#bodysearchresults').html('');
        $('#massivechanges').hide();
        $('#massivechangefilter').val('');
        $('#numericresult').hide();
    }

    var nq = ARCANUM.getURLParameter('navquery');
    if(nq != null) {
        $('#bodysearchquery').val(nq);
        ARCANUM.searchFor({query: nq});
    }


    if($('#advancedsearch')) {
        $('#advancedsearch').hide();
        $('#nav_simplesearch').addClass('active');
        $('#nav_simplesearch').click( function(e) {
            clear();
            $('#nav_simplesearch').addClass('active');
            $('#nav_advancedsearch').removeClass('active');
            $('#simplesearch').show();
            $('#advancedsearch').hide();
        });
        $('#nav_advancedsearch').click( function(e) {
            clear();
            $('#nav_simplesearch').removeClass('active');
            $('#nav_advancedsearch').addClass('active');
            $('#simplesearch').hide();
            $('#advancedsearch').show();
        });
    }

});

