<?php
if ( '::1' == $_SERVER["REMOTE_ADDR"] ) {
	// IF THIS IS A LOCALHOST ENVIRONMENT
	// IF YOU GET 500 INTERNAL SERVER ERRORS ON LOCAL HOST, EDIT THIS
	require_once($_SERVER['DOCUMENT_ROOT'].'/allmybooks/wordpress/wp-config.php');
} else {
	require_once($_SERVER['DOCUMENT_ROOT'].'/wordpress/wp-config.php');
}
$future_list = $_POST['future_list'];
global $wpdb;
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
