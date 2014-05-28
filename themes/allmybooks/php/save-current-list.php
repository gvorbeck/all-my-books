<?php
if ( '::1' == $_SERVER["REMOTE_ADDR"] ) {
	// IF THIS IS A LOCALHOST ENVIRONMENT
	// IF YOU GET 500 INTERNAL SERVER ERRORS ON LOCAL HOST, EDIT THIS
	require_once($_SERVER['DOCUMENT_ROOT'].'/allmybooks/wordpress/wp-config.php');
} else {
	require_once($_SERVER['DOCUMENT_ROOT'].'/wordpress/wp-config.php');
}
$id = $_POST['id'];
if ( ! empty( $id ) && is_user_logged_in() ) {
	update_post_meta( $id, 'reading_state', 1 );
}
