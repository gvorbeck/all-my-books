// VAR fid = ID of finished book.
function updateFinishedList(fid) {
	
	// Get array of newly arranged LIs (updatedArray)
	var updatedArray = [];
	jQuery( '#finished-read-list li' ).each( function( index ) {
		updatedArray.push( this.id );
	} );
	
	// Turn updatedArray into comma seperated list (updatedList)
	var updatedList = updatedArray.join(",");
	
	// Send array to PHP
	jQuery( '#loading-indicator' ).removeClass( 'hide' ).addClass( 'show' );
	jQuery.ajax( {
		type: 'POST',
		url: templateDirectory + '/_php/save-reading-list.php',
		data: 'id=' + fid + '&finishedlist=' + updatedList,
		success: function() {
			jQuery( '#loading-indicator' ).removeClass( 'show' ).addClass( 'hide' );
		}
	} );
	
}

function updateCurrentList() {
	
	// Get array of newly arranged LIs (updatedArray)
	var updatedArray = [];
	jQuery( '#current-read-list li' ).each( function( index ) {
		updatedArray.push( this.id );
	} );
	
	// Turn updatedArray into comma seperated list (updatedList)
	var updatedList = updatedArray.join(",");
	
	// Send array to PHP
	jQuery( '#loading-indicator' ).removeClass( 'hide' ).addClass( 'show' );
	jQuery.ajax( {
		type: 'POST',
		url: templateDirectory + '/_php/save-reading-list.php',
		data: 'currentlist=' + updatedList,
		success: function() {
			jQuery( '#loading-indicator' ).removeClass( 'show' ).addClass( 'hide' );
		}
	} );
	
}

function updateFutureList() {
	
	// Get array of newly arranged LIs (updatedArray)
	var updatedArray = [];
	jQuery( '#future-read-list li' ).each( function( index ) {
		updatedArray.push( this.id );
	} );
	
	// Turn updatedArray into comma seperated list (updatedList)
	var updatedList = updatedArray.join(",");
	
	// Send array to PHP
	jQuery( '#loading-indicator' ).removeClass( 'hide' ).addClass( 'show' );
	jQuery.ajax( {
		type: 'POST',
		url: templateDirectory + '/_php/save-reading-list.php',
		data: 'futurelist=' + updatedList,
		success: function() {
			jQuery( '#loading-indicator' ).removeClass( 'show' ).addClass( 'hide' );
		}
	} );
	
}

function showFinishedList() {
	var autoHeight = jQuery( '#finished-read' ).css( 'height', 'auto' );
	jQuery( '#finished-read' ).animate( {
		opacity: 1,
		height: autoHeight,
		padding: "1% 2%",
		margin: "20px 0 20px",
	}, 250, function() {
	} );
}

/* DOC READY START */
jQuery( document ).ready( function() {

	// Get browser window size. Will be 15px smaller than what Chrome reports.
	/*var browserSize = jQuery( window ).width();
	jQuery( window ).resize( function() {
		browserSize = jQuery( window ).width();
		if ( browserSize <= 720 ) {
			jQuery( '#header-navigation-popup' ).css( 'display', 'block' );
		}
		if ( browserSize > 720 ) {
			jQuery( '#header-navigation-popup' ).css( 'display', 'none' );
		}
	} );*/
	
	// Set the two list's LIs as sortable.
	jQuery( "#current-read-list, #future-read-list, #finished-read-list" ).sortable( {
		update: function( event, ui ) {
			if (this === ui.item.parent()[0]) { // Usually update fires for every list linked. Anything in here will only fire once.
				updateFinishedList(ui.item.attr('id')); // Send the ID of the element sent to the Finished List.
			}
			updateCurrentList();
			updateFutureList();
		},
		// Show Finished list.
		activate: function( event, ui ) {
			showFinishedList();
		},
		// Connect the two lists.
  	connectWith: ".book-list"
	} ).disableSelection();
	
	// Show navigation/login menu.
	/*jQuery( '#site-header h1 span, #navigation--popup').hover( function(i) {
		jQuery( '#navigation--popup' ).toggle();
	} );*/
	
	/*jQuery( '#header-navigation-button, #header-navigation' ).hover( function( index ) {
		if ( browserSize > 720 ) {
			jQuery( '#header-navigation-popup' ).css( 'display', 'block' );
		}
	}, function( index ) {
		if ( browserSize > 720 ) {
			jQuery( '#header-navigation-popup' ).css( 'display', 'none' );
		}
	} );*/
	
} );
/* DOC READY STOP */