/**
 * show alert popup
 */
function __showAlertMesage( text, type ) {
    $.achtung({
        className: type == 'success' ? 'achtungSuccess' : 'achtungFail',
        disableClose: false,
        message: text
    });
}
/**
 * redirect function
 * @param url
 */

function __redirect( url ) {
    document.location.href = url;
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
            'message': 'Loading data ... please wait'
        });
    }).ajaxStop( function() {
        $( progress ).achtung('close');
    });

/**
 * attaching listener for elements with class=delete
 */

    $('.delete').click(function( e ) {
        e.preventDefault();
        if ( confirm('Do you realy want to delete this record?') ) {
            __redirect( $(this).attr('href') );
        }
    });

/**
 * attaching autocomplete to elements with ID = EventLocation
 */

    $('#LocationTitle').autocomplete({
	    source: "/locations/find",
		minLength: 2,
		select: function( event, ui ) {
            $('#EventLocationId').val( ui.item.id );
		}
    });

/**
 * management newLocation popup
 */
    $('#location_popup').dialog({
        title:'New location',
        modal:true,
        autoOpen:false,
        width:530,
        resizable:false,
        open: function() {
	        $("#location_popup").load(
	    	    '/locations/add',
	    	    null,
	    	    function (responseText, textStatus, XMLHttpRequest) {
                    __assignDialogListeners();
                    $('#location_popup').dialog( 'option', 'position', 'center' );
	    	    }
	    	);
	    }
    });

    function addLocationRemote() {
        $('#addLocationForm').ajaxForm( {
            target: '#location_popup',
            success: function( responseText, statusText, xhr, $form ) {
                __assignDialogListeners();
            }
        });
    }

    function __assignDialogListeners() {
        $('#cancelAddLocation').click( function(e) {
            e.preventDefault();
			$("#location_popup").dialog('close');
		});
        $('#processAddLocation').click( function(e) {
            addLocationRemote();
        });
    }

    $('#addLocationDialog').click(function(e) {
        $('#location_popup').dialog('open');
    });

/**
 * location info popup
 */
    $('#location_info_popup').dialog({
        title:'Location info',
        modal:true,
        autoOpen:false,
        width:400,
        resizable:false
    });

    $('.location_info').click(function(e) {
        e.preventDefault();
        $('#location_info_popup').load(
            $(this).attr( 'href' ),
            null,
            function (responseText, textStatus, XMLHttpRequest) {
                $('#location_info_popup').dialog( 'open' );
                $('#location_popup').dialog( 'option', 'position', 'center' );
            }
        );
    });

/**
 * event info popup
 */
    $('#event_info_popup').dialog({
        title:'Event info',
        modal:true,
        autoOpen:false,
        width:400,
        resizable:false
    });

    $('.event_info').click(function(e) {
        e.preventDefault();
        $('#event_info_popup').load(
            $(this).attr( 'href' ),
            null,
            function (responseText, textStatus, XMLHttpRequest) {
                $('#event_info_popup').dialog( 'open' );
                $('#event_popup').dialog( 'option', 'position', 'center' );
            }
        );
    });
/**
 * setcomplete handlers
 */

    $('.complete').change(function() {
        var id = $(this).val();
        $.ajax({
            url: '/events/setcomplete/' + id,
            success: function( data ) {
                if ( data > 0 ) {
                    $('#row_' + id).fadeOut( "slow" );
                } else {
                    __redirect( '/events' );
                }
            }
        });
    });

});
