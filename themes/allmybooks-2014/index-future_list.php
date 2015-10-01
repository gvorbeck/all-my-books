<?php /* FUTURE READING LIST */ ?>
<section id="future-read" class="book-shelf">
  <h1><?php echo svg_bookmark(); ?>Want</h1>
  <ul id="future-read-list" class="book-list future collapsed">
    <?php 
    $book_list = $wpdb->get_results('SELECT * FROM wp_reading_list ORDER BY listorder LIMIT 10');
      foreach ($book_list as $b) {
        the_book_builder($b->bid, $b->listorder, '', $b->time);
      }
    ?>
  </ul>
  <a href="javascript:;" id="show-full-list-button" class="button">more books</a>
</section>
