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
});