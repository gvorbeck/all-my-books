<?php
require_once( __DIR__ . '/../../../../wp-config.php' );
global $wpdb;

$list = $_POST['list'];
$id   = $_POST['id'];
$reading_list_table  = $wpdb->prefix . 'reading_list';

/*
  @function   amb_sanitize_reading_list
  @actions    Refreshes the order of the reading_list table.
*/
function amb_sanitize_reading_list() {
  global $wpdb;
  $reading_list_table  = $wpdb->prefix . 'reading_list';
  // Get all the reading_list table data
  $reading_list = $wpdb->get_results('SELECT * FROM ' . $reading_list_table . ' ORDER BY listorder');
  $i = 1;
  // Update reading_list listorder
  foreach ($reading_list as $r) {
    $wpdb->update( $reading_list_table, array('listorder' => $i), array('bid' => $r->bid));
    $i++;
  }
}
  
if (is_user_logged_in()) {
  switch ($list) {
    case "bookList":
      $status = $_POST['status'];
      $class = '';
      if ($status == 'open') {
        $book_list = $wpdb->get_results('SELECT * FROM ' . $reading_list_table . ' WHERE listorder > 10 ORDER BY listorder');
        $class = 'overflow';
      } else {
        $book_list = $wpdb->get_results('SELECT * FROM ' . $reading_list_table . ' ORDER BY listorder LIMIT 10');
        $class = 'shown';
      }
      foreach ($book_list as $b) {
        the_book_builder($b->bid, $b->listorder, $class, $b->time);
      }
      break;
    case "finished":
      if (!empty($id)) {
        $row_count = count(get_field('read_records', $id));
        update_post_meta($id, 'reading_state', 0);
        if ($row_count) {
          update_post_meta($id, 'read_records', $row_count + 1);
          add_post_meta($id, 'read_records_' . $row_count . '_read_year', date('Y'));
          add_post_meta($id, '_read_records_' . $row_count . '_read_year', 'field_4fa81bf2a8786');
        } else {
          update_post_meta($id, 'read_records', 1);
          add_post_meta($id, 'read_records_0_read_year', date('Y'));
          add_post_meta($id, '_read_records_0_read_year', 'field_4fa81bf2a8786');
        }
        $wpdb->delete($reading_list_table, array('bid' => $id));
        amb_sanitize_reading_list();
      }
      break;
    case "current":
      if (!empty($id)) {
        // Set radio button
        update_post_meta($id, 'reading_state', 1);
        // Delete this book from the reading_list table
        $wpdb->delete($reading_list_table, array('bid' => $id));
        amb_sanitize_reading_list();
      }
      break;
    case "future":
      $future_list = $_POST['future_list'];
      if (!empty( $future_list)) {
        $books = explode(',', $future_list);
        foreach ($books as $b) {
          $pieces = explode(':', $b);
          update_post_meta($pieces[0], 'reading_state', 2);
          $results = $wpdb->get_results('SELECT * FROM ' . $reading_list_table . ' WHERE bid = ' . $pieces[0], ARRAY_N);
          if (count($results) > 0) {
            $rows_affected = $wpdb->update(
              $reading_list_table,
              array('listorder' => $pieces[1]),
              array('bid'       => $pieces[0])
            );
          } else {
            $rows_affected = $wpdb->insert(
              $reading_list_table,
              array(
                'time' => current_time('mysql'),
                'bid' => $pieces[0],
                'listorder' => $pieces[1]
              )
            );
          }
        }
      }
      break;
    case "fut_admin":
      $book = $_POST['id'];
      $orig = $_POST['orig'];
      $place = $_POST['place'];
      $wtr_ids = $wpdb->get_col( 'SELECT bid FROM ' . $reading_list_table );
      if ( in_array($book, $wtr_ids) ) {
        $wtr_ids = $wpdb->get_results( 'SELECT bid, listorder FROM ' . $reading_list_table . ' ORDER BY listorder' );
        if ( $orig < $place ) {
          $o = $orig + 1;
          foreach ( $wtr_ids as $w ) {
            if ( $w->listorder >= $o && $w->listorder <= $place ) {
              $wpdb->update( $reading_list_table, array( 'listorder' => ($w->listorder - 1) ), array( 'bid' => $w->bid ) );
            }
          }
        } else {
          $o = $orig - 1;
          foreach ( $wtr_ids as $w ) {
            if ( $w->listorder >= $place && $w->listorder <= $o ) {
              $wpdb->update( $reading_list_table, array( 'listorder' => ($w->listorder + 1) ), array( 'bid' => $w->bid ) );
            } 
          }
        }
        $wpdb->update( $reading_list_table, array( 'listorder' => $place ), array( 'bid' => $book ) );
      } else {
        update_post_meta( $book, 'reading_state', 2 );
        // ADD TO DB TABLE
        $wtr_ids = $wpdb->get_results( 'SELECT bid, listorder FROM ' . $reading_list_table . ' ORDER BY listorder' );
        $length = count($wtr_ids);
        foreach ( $wtr_ids as $w ) {
          if ( $w->listorder >= $place ) {
            $wpdb->update( $reading_list_table, array( 'listorder' => ($w->listorder + 1) ), array( 'bid' => $w->bid ) );
          }
        }
        $wpdb->insert($reading_list_table, array( 'time' => current_time('mysql'), 'bid' => $book, 'listorder' => $place ) );
      }
      break;
    case "delete":
      wp_delete_post($_POST['id']);
      $wpdb->delete($reading_list_table, array('bid' => $id));
      amb_sanitize_reading_list();
      break; 
    case "new":
      $new_post = array(
        'post_content' => '',
        'post_title' => wp_strip_all_tags($_POST['title']),
        'post_status' => 'publish',
        'tax_input' => array(
          'authors' => $_POST['author']
        )
      );
      echo wp_insert_post($new_post, true);
      break;
  }
} else {
  echo 'You are not currently logged in.';
}