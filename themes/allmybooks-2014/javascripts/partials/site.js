var bookFunctions = {
  toggleOptions: function(id) {
    if ($('#' + id).hasClass('show-options')) {
      $('#' + id).find('.book--options').slideUp();
      $('#' + id).removeClass('show-options');
    }
    else {
      $('#' + id).find('.book--options').slideDown();
      $('#' + id).addClass('show-options');
    }
  },
  updateList: function(list, bookID) {
    var futureList = [],
        readingOrder = 1;
    if (list == 'future') {
      $('#future-read-list .book').each( function() {
        futureList.push(this.id.substring(5) + ':' + readingOrder);
        readingOrder++;
      });
      futureList = futureList.join(',');
      var data = 'list=future&future_list=' + futureList;
    }
    else {
      var data = 'list=' + list + '&id=' + bookID;
    } 
    $.ajax({
      type: 'POST',
      url: templateDirectory + '/php/save-list.php',
      data: data,
      success: function(data) {
        if (data.length) {
          console.log(data);
          $('#logged-out-warning').addClass('animate-open').removeClass('animate-close').find( 'p' ).text( data );
        }
      }
    });
  }
};

/**
 * Checks to see if the specified element is on or above the screen.
 * @param {String} elm - The specified element to check to see if it is on or above the screen.
 * @param {String} evalType - The type of check to perform - if empty, will default to visible
 * Source: https://stackoverflow.com/questions/5353934/check-if-element-is-visible-on-screen.
 */
var checkVisible = function( elm, evalType ) {
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
var visibleLooper = function() {
  // Make sure the list is in 'expanded' mode
  if ($('#future-read-list').hasClass('expanded')) {
    // Only target 'overflow' items.
    $('#future-read-list').find('.overflow').each(function() {
      // And of those, only target those without the 'animate' class.
      if (!$(this).hasClass('animate')) {
        // Check to see if they are in the visible range.
        if (checkVisible($(this), 'above')) {
          var delay = Math.floor(Math.random() * 700);
          // Rabndomly add the animate class.
          $(this).delay(delay).queue(function() {
            $(this).addClass('animate').dequeue();
          });
        }
      }
    });
  }
}

// Allows me to use '$' instead of 'jQuery'
// https://stackoverflow.com/a/24119140
var $ = jQuery.noConflict();

/* DOC READY START */
$(document).ready(function() {
  
  // Set up book--options' active options.
  $('.book').each(function() {
    var list = $(this).closest('.book-list');
    $(this).find('.book--options a').each( function() {
      if (list.hasClass($(this).attr('class'))) {
        $(this).addClass('active');
      }
    });
  })  
  // Toggle book--options' visibility.
  .on('click', 'h1', function() {
    bookFunctions.toggleOptions($(this).closest('.book').attr('ID'));
  });
  
  // Moving books between lists
  $('.book--options').on('click', 'a', function() {
    var book = $(this).closest('.book'),
        bookClass = $(this).attr('class');
    // If this a link to move the book to a new list and isn't already active.
    if (!$(this).hasClass('active') && !$(this).hasClass('delete')) {
      $(this).closest('.book--options').children('a').removeClass('active');
      if ($(this).hasClass('finished') && !$(this).hasClass('active')) {
        $('#finished-read').slideDown();
      }
      $(this).addClass('active');
      book.css('opacity', '.5').slideUp(400, function() {
        $('.book-list.' + bookClass).prepend(book);
        bookFunctions.updateList(bookClass, book.attr('ID').substring(5));
        bookFunctions.toggleOptions(book.attr('ID'));
        $('#' + book.attr('ID')).delay(250).slideDown(400, function() {
          $(this).css('opacity', '1');
        });
      });
    }
    // If this is the delete link.
    else if ($(this).hasClass('delete')) {
      if (confirm('Are you sure you want to delete ' + book.find('h1').text() + '?')) {
        bookFunctions.updateList(bookClass, book.attr('ID').substring(5));
        book.css('opacity', '.5').slideUp(400);
      }
    }
  });
  
  // Toggles on the more/less button.
  $('#show-full-list-button').on('click', function() {
    var futureReadList = $('#future-read-list');
    futureReadList.toggleClass('expanded').toggleClass('collapsed')
    if (futureReadList.hasClass('expanded')) {
      $(this).text('less books');
      visibleLooper();
    }
    else {
      $(this).text('more books');
    }
  });
  // Look again while scrolling.
  $(window).scroll(function() {
    // Check to see if overflow books are on screen.
    visibleLooper();
  });
});
/* DOC READY STOP */
