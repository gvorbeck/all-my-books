// VAR bid = ID of book.
function updateFinishedList(bid) {
	jQuery( '#loading-container' ).toggle();
	jQuery.ajax( {
		type: 'POST',
		url: templateDirectory + '/_php/save-finished-list.php',
		data: 'id=' + bid,
		success: function(data) {
			jQuery( '#loading-container' ).toggle();
			//console.log(data);
		}
	} );
}

// VAR bid = ID of book.
function updateCurrentList(bid) {
	jQuery( '#loading-container' ).toggle();
	jQuery.ajax( {
		type: 'POST',
		url: templateDirectory + '/_php/save-current-list.php',
		data: 'id=' + bid,
		success: function(data) {
			jQuery( '#loading-container' ).toggle();
			//console.log(data);
		}
	} );
}

function updateFutureList() {
	
	// Get array of newly arranged LIs (updatedArray)
	var updatedArray = [];
	var expectedOrder = 1;
	jQuery( '#future-read-list li' ).each( function( index ) {
		var readingOrder = jQuery( this ).data( 'order' );
		if ( readingOrder != expectedOrder ) {
			updatedArray.push( this.id + ':' + expectedOrder );
		}
		expectedOrder++;
	} );
	
	// Turn updatedArray into comma seperated list (updatedList)
	var updatedList = updatedArray.join(",");
	
	// Send array to PHP
	jQuery( '#loading-container' ).toggle();
	jQuery.ajax( {
		'type': 'POST',
		'url': templateDirectory + '/_php/save-future-list.php',
		'data': 'future_list=' + updatedList,
		'success': function(data) {
			// data variable is anything echoed in above php file.
			jQuery( '#loading-container' ).toggle();
			//console.log(data);
		}
	} );
	
}

/* DOC READY START */
jQuery( document ).ready( function() {

	// Get browser window size. Will be 15px smaller than what Chrome reports.
	var pageWidth = jQuery( window).width()+15;
	jQuery( '#dev--window-width' ).text( pageWidth );
	jQuery( window ).resize( function(i) {
		pageWidth = jQuery( window ).width()+15;
		jQuery( '#dev--window-width' ).text( pageWidth );
	});
	
	// Hide the bloat of the wtr list.
	jQuery('#show-full-list-button').click(function() {
		jQuery('#future-read-list .overflow').toggle();
		if ( 'Expand' == jQuery(this).text() ) {
			jQuery(this).text('Collapse');
		} else {
			jQuery(this).text('Expand');
		};
	});
	
	// Set the two list's LIs as sortable.
	jQuery( "#current-read-list, #future-read-list, #finished-read-list" ).sortable( {
		update: function( event, ui ) {
			if (this === ui.item.parent()[0]) { // Usually update fires for every list linked. Anything in here will only fire once.
				//console.log(ui.item.attr('id')); // BOOK ID
				//console.log(ui.item.parent()[0].id); // LIST ID
				if ( 'future-read-list' == ui.item.parent()[0].id ) {
					updateFutureList();
				}
				if ( 'current-read-list' == ui.item.parent()[0].id ) {
					updateCurrentList(ui.item.attr('id'));
				}
				if ( 'finished-read-list' == ui.item.parent()[0].id ) {
					updateFinishedList(ui.item.attr('id'));
				}
			}
			//console.log(ui);
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
	jQuery( '.navigation--button, #navigation--popup').hover( function() {
		if ( pageWidth > (785) ) {
			jQuery('#navigation--popup').show();
		}
	}, function() {
		if ( pageWidth > (785) ) {
			jQuery('#navigation--popup').hide();
		}
	} );
	
} );
/* DOC READY STOP */