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

/* DOC READY START */
jQuery( document ).ready( function() {

	// Get browser window size. Will be 15px smaller than what Chrome reports.
	var pageWidth = jQuery( document ).width();
	jQuery( '#dev--window-width' ).text( pageWidth );
	jQuery( window ).resize( function(i) {
		pageWidth = jQuery( document ).width();
		jQuery( '#dev--window-width' ).text( jQuery( document ).width());
	});
	
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
			var clickedID = ui.item[0].id;
			// This makes sure the finished list never shows unless I am moving a book from the currently-read list.
			if ('current-read-list' == jQuery( '#' + clickedID ).parent().attr('id')){
				jQuery( "#finished-read" ).slideDown( "slow", function() {
				    // Animation complete.
				} );
			}
		},
		// Connect the two lists.
  	connectWith: ".book-list"
	} ).disableSelection();
	
	// Show navigation/login menu.
	jQuery( '#navigation--popup, .navigation--button').hover( function(i) {
		if ( pageWidth > 785 ) {
			jQuery( '#navigation--popup' ).toggle();
			jQuery( '.navigation--popup-arrow' ).toggle();
		}
	} );
	
} );
/* DOC READY STOP */