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
    url: templateDirectory + '/php/save-list.php',
    data: 'list=fin&id=' + bid,
    success: function(data) {
      jQuery( '#loading-container' ).toggle();
      if (data.length) {
        jQuery( '#logged-out-warning' ).addClass('animate-open').removeClass('animate-close').children( 'p' ).text( data );
      }
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
    url: templateDirectory + '/php/save-list.php',
    data: 'list=cur&id=' + bid,
    success: function(data) {
      jQuery( '#loading-container' ).toggle();
      if (data.length) {
        jQuery( '#logged-out-warning' ).addClass('animate-open').removeClass('animate-close').children( 'p' ).text( data );
      }
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
    'url': templateDirectory + '/php/save-list.php',
    'data': 'list=fut&future_list=' + updatedList,
    'success': function(data) {
      // data variable is anything echoed in above php file.
      jQuery( '#loading-container' ).toggle();
      if (data.length) {
        jQuery( '#logged-out-warning' ).addClass('animate-open').removeClass('animate-close').children( 'p' ).text( data );
      }
    }
  } );
}

/**
 * Checks to see if the specified element is on or above the screen.
 * @param {String} elm - The specified element to check to see if it is on or above the screen.
 * @param {String} evalType - The type of check to perform - if empty, will default to visible
 * Source: https://stackoverflow.com/questions/5353934/check-if-element-is-visible-on-screen.
 */
function checkVisible( elm, evalType ) {
    evalType = evalType || "visible";

    var vpH = $(window).height(), // Viewport Height
        st = $(window).scrollTop(), // Scroll Top
        y = $(elm).offset().top,
        elementHeight = $(elm).height();

    if (evalType === "visible") return ((y < (vpH + st)) && (y > (st - elementHeight)));
    if (evalType === "above") return ((y < (vpH + st)));
}

/**
 * Goes through the expanded wtr list and appropriately calls the checkVisible function
 */
function visibleLooper() {
  // Make sure the list is in 'expanded' mode
  if ( jQuery('#future-read-list.expanded').length ) {
    // Only target 'overflow' items.
    jQuery('#future-read-list .overflow').each( function() {
      // And of those, only target those without the 'animate' class.
      if ( ! jQuery(this).hasClass('animate') ) {
        // Check to see if they are in the visible range.
        if (checkVisible( jQuery(this), 'above' )) {
          var delay = Math.floor(Math.random() * 1000);
          // Rabndomly add the animate class.
          jQuery(this).delay(delay).queue( function() {
            jQuery(this).addClass('animate').dequeue();
          } );
        }
      }
    } );
  }
}
/* DOC READY START */
jQuery( document ).ready( function() {
  // Hide the bloat of the wtr list.
  jQuery('#show-full-list-button').click(function() {
    if ( jQuery('#future-read-list').hasClass('collapsed') ) {
      jQuery('#future-read-list').addClass('expanded').removeClass('collapsed');
      jQuery(this).text('Less Books');
      visibleLooper();
    } else {
      jQuery('#future-read-list').addClass('collapsed').removeClass('expanded');
      jQuery(this).text('More Books');
      jQuery('#future-read-list .overflow').each( function() {
        jQuery(this).removeClass('animate');
      } );
    }
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
  } );
  // Close the logged out warning popup.
  jQuery( '#logged-out-warning a' ).click( function() {
    jQuery( '#logged-out-warning' ).addClass('animate-close').removeClass('animate-open');
  } );
  // Sticky Headers.
  jQuery('.sticker').width(jQuery('.sticker').width());
  jQuery(".sticker").sticky({topSpacing:0});
  // Look again while scrolling.
  jQuery(window).scroll(function() {
    // Check to see if overflow books are on screen.
    visibleLooper();
    /*if( jQuery(window).scrollTop() >= stickyHeaderTop ) {
      jQuery('#future-read').addClass('sticky');
      jQuery('#future-read h1').css('width', stickyWidth);
      jQuery('#future-read-list').css('margin-top', stickyHeight);
    } else {
      jQuery('#future-read').removeClass('sticky');
      jQuery('#future-read h1').css('width', stickyWidth);
      jQuery('#future-read-list').css('margin-top', '0');
    }*/
  } );
} );
/* DOC READY STOP */
