/**
 * Marks a book as finished.
 * @param {Integer} bid - Wordpress ID of the book to be marked as finished.
 * @param {String} templateDirectory - Absolute path to template directory on the server. Defined in header.php
 * @param {Mixed}  data - anything that is echoed in the called PHP file.
 */
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

/**
 * Marks a book as currently being read.
 * @param {Integer} bid - Wordpress ID of the book to be marked as being read.
 * @param {String} templateDirectory - Absolute path to template directory on the server. Defined in header.php
 * @param {Mixed}  data - anything that is echoed in the called PHP file.
 */
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

/**
 * Saves the order of books marked as wanting to be read.
 * @param {Array}   updatedArray - An array of book LIs that have been moved. Formatted as ID:ORDER#
 * @param {Integer} expectedOrder - Integer of order number.
 * @param {Integer} readingOrder - value of the data-order attribute in the LI.
 * @param {String}  templateDirectory - Absolute path to template directory on the server. Defined in header.php
 * @param {Mixed}   data - anything that is echoed in the called PHP file.
 */
function updateFutureList() {
	// Get array of newly arranged LIs (updatedArray)
	var updatedArray = [];
	var expectedOrder = 1;
	jQuery( '#future-read-list li' ).each( function() {
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
	jQuery('.navigation--button').click( function() {
		if ( jQuery( '#container' ).hasClass( 'open-login' ) ) {
			jQuery( '#container' ).removeClass( 'open-login' );
		} else {
			jQuery('#container').addClass('open-login');
		}
	});
	// Get browser window size. Will be 15px smaller than what Chrome reports.
	var pageWidth = jQuery( window).width();
	jQuery( '#dev--window-width' ).text( pageWidth );
	jQuery( window ).resize( function(i) {
		pageWidth = jQuery( window ).width();
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
	// Make lists sortable and connected to one another.
	// SOURCE: https://github.com/voidberg/html5sortable
	jQuery( '#finished-read-list, #current-read-list, #future-read-list' ).sortable( {
    connectWith: '.connected',
    forcePlaceholderSize: true,
    items: ':not(.disabled)'
	} ).bind( 'sortupdate', function(e, ui) {
		var bid = ui.item[0].id;
		switch ( jQuery( this ).attr( 'id' ) ) {
	    case 'future-read-list':
	    	updateFutureList();
	    	break;
	    case 'current-read-list':
	    	updateCurrentList(bid);
	    	break;
	    case 'finished-read-list':
	    	updateFinishedList(bid);
	    	break;
    }
	} );
	// Detect the drag of a book.
	var isDragging = false;
	jQuery( "#current-read-list .book" )
	.mousedown( function() {
    jQuery( window ).mousemove( function() {
      isDragging = true;
      jQuery( window ).unbind( "mousemove" );
			jQuery( "#finished-read" ).slideDown( "slow", function() {
				// Animation complete.
			} );
    } );
	} )
	.mouseup( function() {
    var wasDragging = isDragging;
    isDragging = false;
    jQuery( window ).unbind( "mousemove" );
    if ( !wasDragging ) {
    	//was being clicked.
    }
	} );
	// Show navigation/login menu.
	jQuery( '.navigation--button, #navigation--popup' ).hover( function() {
		if ( pageWidth > (785) ) {
			jQuery( '#navigation--popup' ).show();
		}
	}, function() {
		if ( pageWidth > (785) ) {
			jQuery( '#navigation--popup' ).hide();
		}
	} );
  // Check the initial Poistion of the Sticky Header
var stickyHeaderTop    = jQuery('#future-read h1').offset().top;
  var stickyHeaderHeight = jQuery('#future-read h1').outerHeight()+10;
  var stickyHeaderWidth  = jQuery('#content').width()-35;
  jQuery(window).resize( function() {
  	stickyHeaderWidth  = jQuery('#content').width()-35;
  	jQuery('#future-read h1').width(stickyHeaderWidth);
  } );
  
  // Immediately look to see if page has loaded below the banner.
	if( jQuery(window).scrollTop() >= stickyHeaderTop ) {
		jQuery('#future-read').addClass('sticky').children('h1').width(stickyHeaderWidth);
  	jQuery('#future-read-list').css('margin-top', stickyHeaderHeight);
  } else {
		jQuery('#future-read').removeClass('sticky');
  	jQuery('#future-read-list').css('margin-top', '0');
  }
  // Look again while scrolling.
  jQuery(window).scroll(function() {
  	if( jQuery(window).scrollTop() >= stickyHeaderTop ) {
  		jQuery('#future-read').addClass('sticky').children('h1').width(stickyHeaderWidth);
    	jQuery('#future-read-list').css('margin-top', stickyHeaderHeight);
    } else {
  		jQuery('#future-read').removeClass('sticky');
    	jQuery('#future-read-list').css('margin-top', '0');
    }
  } );
} );
/* DOC READY STOP */
