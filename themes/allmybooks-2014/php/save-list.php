<?php
require_once( __DIR__ . '/../../../../wp-config.php' );
$list = $_POST['list'];
$id   = $_POST['id'];
switch ( $list ) {
  case "fin":
    if ( ! empty( $id ) && is_user_logged_in() ) {
      $row_count = count(get_field('read_records', $id));
      update_post_meta( $id, 'reading_state', 0 );
      if ( $row_count ) {
        update_post_meta( $id, 'read_records', $row_count + 1 );
        add_post_meta( $id, 'read_records_'.$row_count.'_read_year', date( 'Y' ) );
        add_post_meta( $id, '_read_records_'.$row_count.'_read_year', 'field_4fa81bf2a8786' );
      } else {
        update_post_meta( $id, 'read_records', 1 );
        add_post_meta( $id, 'read_records_0_read_year', date( 'Y' ) );
        add_post_meta( $id, '_read_records_0_read_year', 'field_4fa81bf2a8786' );
      }
    }
    break;
  case "cur":
    if ( ! empty( $id ) && is_user_logged_in() ) {
      update_post_meta( $id, 'reading_state', 1 );
    }
    break;
  case "fut":
    $future_list = $_POST['future_list'];
    if ( ! empty( $future_list ) && is_user_logged_in() ) {
      $books = explode( ',', $future_list );
      foreach ( $books as $b ) {
        $pieces = explode( ':', $b );
        update_post_meta( $pieces[0], 'reading_state', 2 );
        $table_name = $wpdb->prefix . "reading_list";
        $results     = $wpdb->get_results( 'SELECT * FROM wp_reading_list WHERE bid = ' . $pieces[0], ARRAY_N );
        if ( count( $results ) > 0 ) {
          $rows_affected = $wpdb->update(
            $table_name,
            array( 'listorder' => $pieces[1] ),
            array( 'bid'       => $pieces[0] )
          );
        } else {
          $rows_affected = $wpdb->insert(
            $table_name,
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
}
