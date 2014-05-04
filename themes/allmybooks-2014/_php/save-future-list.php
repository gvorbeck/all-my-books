<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/wordpress/wp-config.php');

$future_list    = $_POST;

global $wpdb;
foreach ( $future_list as $key=>$value ) {
	echo "key is $key and value is $value";
	$table_name = $wpdb->prefix . "reading_list";
	$rows_affected = $wpdb->update(
		$table_name,
		array( 'listorder' => $value ),
		array( 'bid'       => $key   )
	);
}
