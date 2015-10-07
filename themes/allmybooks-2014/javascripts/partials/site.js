

// Allows me to use '$' instead of 'jQuery'
// https://stackoverflow.com/a/24119140
var $              = jQuery.noConflict(),
    ajaxURL        = templateDirectory + '/php/save-list.php',
    futureReadList = $('#future-read-list'),
    showHideClicks = 0,
    bookFunctions  = {
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
      var data = {
        'list': 'future',
        'future_list': futureList.join(',')
      };
    }
    else {
      var data = {
        'list': list,
        'id': bookID
      };
    } 
    $.ajax({
      type: 'POST',
      url: ajaxURL,
      data: data,
      success: function(data) {
        if (data.length) {
          //console.log(data);
          if ($('.js-login-message').is(':hidden')) {
            $('.js-login-message').toggle();
          }
        }
      }
    });
  }
};


var toggleLightbox = function() {
  $('.lightbox').toggle();
  //$('body').toggleClass('no-scroll');
}

/**
 * Checks to see if the specified element is on or above the screen.
 * @param {String} elm - The specified element to check to see if it is on or above the screen.
 * @param {String} evalType - The type of check to perform - if empty, will default to visible
 * Source: https://stackoverflow.com/questions/5353934/check-if-element-is-visible-on-screen.
 */
var checkVisible = function(elm, evalType) {
  evalType = evalType || "visible";
  var vpH  = $(window).height(), // Viewport Height
      st   = $(window).scrollTop(), // Scroll Top
      y    = $(elm).offset().top,
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
      if (!$(this).hasClass('animate') && checkVisible($(this), 'above')) {
        // Rabndomly add the animate class.
        $(this).delay(Math.floor(Math.random() * 700)).queue(function() {
          $(this).addClass('animate').dequeue();
        });
      }
    });
  }
};

/* DOC READY START */
$(document).ready(function() {
  
  // Bring up the lightbox
  $('.js-lightbox-toggle').on('click', function() {
    toggleLightbox();
    if ($(this).hasClass('site-action')) {
      $('.js-lightbox-content').empty().append($(this).parent().find('form').clone());
    }
  });
  
  // Add a book
  $('.js-lightbox-content').on('click', '.button', function() {
    if ($(this).hasClass('add-book-form--button')) {
      var newBookForm   = $(this).closest('form'),
          newBookTitle  = newBookForm.find('#add-book-form--title').val(),
          newBookAuthor = newBookForm.find('#add-book-form--author').val();
      $.ajax({
        type: 'POST',
        url: ajaxURL,
        data: {
          'list': 'new',
          'title': newBookTitle,
          'author': newBookAuthor
        },
        success: function(data) {
          newBookForm.find('input').val('');
          toggleLightbox();
          // If ajax returns the post ID
          if (!isNaN(data)) {
            var bookTemplate = futureReadList.find('li').first().clone();
            bookTemplate
              .css('opacity', '.5')
              .attr('id', 'book-' + data)
              .attr('data-order', '0')
              .addClass('new-book')
              .find('.book--series, .book--tags, .book--want-date, .book--links, .book--meta')
                .remove()
              .end()
              .find('.book--title')
                .text(newBookTitle)
              .end()
              .find('.book--author span')
                .text(newBookAuthor)
              .end();
            futureReadList.prepend(bookTemplate);
            bookFunctions.updateList('future', bookTemplate.attr('ID').substring(5));
            bookTemplate.delay(250).slideDown(400, function() {
              $(this).css('opacity', '1');
            });
          }
          if (data.length) {
            // The ID of the newly created post.
            //console.log(data);
          }
        }
      });
    }
  });
  
  // Set up book--options' active options.
  // E.G. - Which book--option is highlighted.
  $('.book').each(function() {
    var list = $(this).closest('.book-list');
    $(this).find('.book--options a').each( function() {
      if (list.hasClass($(this).attr('class'))) {
        $(this).addClass('active');
      }
    });
  });
  
  // Toggle book--options' visibility.
  $('.book-list')
    .on('click', 'h1', function() {
      bookFunctions.toggleOptions($(this).closest('.book').attr('ID'));
    })
    // Moving books between lists
    .on('click', '.book--options a', function() {
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
    })
    .on('click', '.book--link', function() {
      var book = $(this).closest('.book')
      if ($(this).hasClass('delete-link')) {
        if (confirm('Are you sure you want to delete ' + book.find('h1').text() + '?')) {
          bookFunctions.updateList('delete', book.attr('ID').substring(5));
          book.css('opacity', '.5').slideUp(400);
        }
      }
    });
  
  // Toggles on the more/less button as well as the posts within it.
  $('#show-full-list-button').on('click', function() {
    futureReadList.toggleClass('expanded').toggleClass('collapsed')
    futureReadList.hasClass('expanded') ? $(this).text('less books') : $(this).text('more books');
    var stateChange = showHideClicks % 2 == 0 ? 'open' : 'close';
    $.ajax({
      type: 'POST',
      url: ajaxURL,
      data: {
        'list': 'bookList',
        'status': stateChange
      },
      beforeSend:function() {
        if (showHideClicks % 2 == 0) {
          $('.loader-inner').toggle();
        }
      },
      success: function(data) {
        stateChange == 'open' ? $('#future-read-list').append(data) : $('#future-read-list').html(data);
      },
      complete: function() {
        if (showHideClicks % 2 == 0) {
          $('.loader-inner').toggle();
        }
        showHideClicks++;
        visibleLooper();
      }
    });
  });
  
  // Turn off "logged out" message.
  $('.js-login-message').on('click', 'svg', function() {
    $(this).closest('.js-login-message').toggle();
  });
  
  // Look again while scrolling.
  $(window).scroll(function() {
    // Check to see if overflow books are on screen.
    visibleLooper();
  });
});
/* DOC READY STOP */
