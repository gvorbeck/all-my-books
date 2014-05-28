<?php
require_once(__DIR__.'/../../../../wp-config.php');
$id = $_POST['id'];
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
