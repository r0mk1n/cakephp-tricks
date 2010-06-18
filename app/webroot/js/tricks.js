/**
 *
 *
 */
function __showAlertMesage( text, type ) {
    $.achtung({
        className: type == 'success' ? 'achtungSuccess' : 'achtungFail',
        disableClose: false,
        message: text
    });
}

/**
 * some autorun stuff
 */
$(function() {
    var progress = null;
    $( document ).ajaxStart( function() {
        progress = $.achtung({
            'className': 'achtungWait',
            'icon': 'wait-icon',
            'disableClose': true,
            'message': 'Loading data...please wait'
        });
    }).ajaxStop( function() {
        $( progress ).achtung('close');
    });

// attaching listener for elements with class=delete
    $('.delete').click(function( e ) {
        e.preventDefault();
        if ( confirm('Do you realy want to delete this record?') ) {
            document.location.href = $(this).attr('href');
        }
    });

// attaching autocomplete to elements with ID = EventLocation
    $('#EventLocation').autocomplete({
	    source: "/locations/find",
		minLength: 2,
		select: function( event, ui ) {
            $('#EventLocationId').val( ui.item.id );
		}
    });

});
