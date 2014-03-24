<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/wordpress/wp-config.php');

$id             = $_POST['id'];
 
$finished_list  = $_POST['finishedlist'];
$current_list   = $_POST['currentlist'];
$future_list    = $_POST['futurelist'];

// Finished Reading List Update
if ( ! empty( $finished_list ) && is_user_logged_in() ) {
	$books = explode( ',', $finished_list );
	foreach ( $books as $b ) {
		update_post_meta( $b, 'reading_state', 0 );
	}
	if ( get_field( 'read_records', $id ) ) {
		$row_count  = count( get_field( 'read_records' ), $id );
		$read_count = $row_count - 1;
		$i			= 0;
		while ( the_repeater_field( 'read_records', $id ) ) {
			if ( $i == $read_count ) {
				update_post_meta( $id, 'read_records', $row_count + 1 );
				add_post_meta( $pid, '_read_records', 'field_4fa81bf2a77dd' );
				add_post_meta( $id, 'read_records_'.$row_count.'_read_year', date( 'Y' ) );
				add_post_meta( $pid, '_read_records_'.$row_count.'_read_year', 'field_4fa81bf2a8786' );
			}
			$i++;
		}
	} else {
		update_post_meta( $id, 'read_records', 1 );
		add_post_meta( $id, 'read_records_0_read_year', date( 'Y' ) );
	}
}

// Current Reading List Update
if ( ! empty( $current_list ) && is_user_logged_in() ) {
	$books = explode( ',', $current_list );
	foreach ( $books as $b ) {
		update_post_meta( $b, 'reading_state', 1 );
	}
}

// Future Reading List Update
if ( ! empty( $future_list ) && is_user_logged_in() ) {
	$books = explode( ',', $future_list );
	foreach ( $books as $b ) {
		update_post_meta( $b, 'reading_state', 2 );
	}
}
