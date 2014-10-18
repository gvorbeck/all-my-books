<?php /* FUTURE READING LIST */ ?>
<?php
global $wpdb;

// 1]] GET BOOKS FROM DB TABLE.
// Books marked to-be-read within the wp_reading_list table.
$reading_list_table = $wpdb->get_results( "SELECT * FROM wp_reading_list" );
$reading_list_table_ids = array();
$i = 0;
foreach ($reading_list_table as $book) {
  $i++;
  $reading_list_table_ids[$i] = $book->bid;
}

// 2]] GET BOOKS FROM CMS RADIO BUTTON.
// Books marked to-be-read with the radio button in the Wordpress backend.
$args = array(
  'posts_per_page'  => -1,
  'meta_query'      => array(
    array(
      'key'         => 'reading_state',
      'value'       => 2,
    ),
  ),
);
$reading_list_cms   = new WP_Query( $args );
$reading_list_cms_ids = array();
$i = 0;
foreach ( $reading_list_cms->posts as $c ) {
  $i++;
  $reading_list_cms_ids[$i] = $c->ID;
}

// 3]] CHECK RADIO BUTTONS AGAINST DB TABLE.
// Make sure the database table has everything that is marked with the radio button.
foreach ($reading_list_cms_ids as $cms_id) {
  if ( ! in_array($cms_id, $reading_list_table_ids) ) {
    $new_book_id = count($reading_list_table_ids) + 1;
    $reading_list_table_ids[$new_book_id] = $cms_id;
    $new_book = $wpdb->insert('wp_reading_list', array( 'time' => current_time('mysql'), 'bid' => $cms_id, 'listorder' => $new_book_id ) );
  }
}
// 4]] CHECK DB TABLE AGAINST RADIO BUTTONS.
// Make sure the database table doesn't have anything outdated.
foreach ($reading_list_table_ids as $table_id) {
  if ( 0 == $table_id ) {
    $empty_book = $wpdb->delete( 'wp_reading_list', array( 'bid'=>$table_id ) );
  }
  if ( ! in_array($table_id, $reading_list_cms_ids) ) {
    $old_book = $wpdb->delete( 'wp_reading_list', array( 'bid' => $table_id ) );
  }
}
// 5]] NOW PRINT IT OUT.
$wtr_final = $wpdb->get_results("SELECT * FROM wp_reading_list ORDER BY listorder");
$this_these = ( count($future_list) > 1 ? 'These' : 'This');
echo '<section id="future-read" class="book-shelf">';
  echo '<h1 class="sticky--wtr">' . svg_bookmark() . 'I Want To Read ' . $this_these . '</h1>';
  if ( $wtr_final ) {
    $i = 1;
    $class = '';
    echo '<ul id="future-read-list" class="book-list [connected sortable] collapsed">';
    foreach ($wtr_final as $wtr) {
      $listorder_stamper = $wpdb->update( 'wp_reading_list', array( 'listorder' => $i ),  array( 'bid' => $wtr->bid ) );
      if ( 10 >= $i ? $class = 'shown' : $class = 'overflow' );
      $time = '<span class="month">' . date( 'M', strtotime( $wtr->time ) ) . '</span><span class="year">' . date( '\'y', strtotime( $wtr->time ) ) . '</span>';
      the_book_builder( $wtr->bid, $wtr->listorder, $class, $time );
      $i++;
    }
    echo '</ul>';
  } else {
  echo '<p class="instructions">Error: Make sure that the Reading List plugin is activated <a href="' . get_admin_url() . 'plugins.php">here</a>.</p>';
}
?>
<a href="javascript:;" id="show-full-list-button" class="button">More Books</a>
</section>
