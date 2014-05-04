<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/wordpress/wp-config.php');

$future_list = $_POST['future_list'];

global $wpdb;

if ( ! empty( $future_list ) && is_user_logged_in() ) {
	$books = explode( ',', $future_list );
	foreach ( $books as $b ) {
		$pieces = explode( ':', $b );
		$table_name = $wpdb->prefix . "reading_list";
		$rows_affected = $wpdb->update(
			$table_name,
			array( 'listorder' => $pieces[1] ),
			array( 'bid'       => $pieces[0]   )
		);
	}
}
