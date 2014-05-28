<?php
require_once(__DIR__.'/../../../../wp-config.php');
$id = $_POST['id'];
if ( ! empty( $id ) && is_user_logged_in() ) {
	update_post_meta( $id, 'reading_state', 1 );
}
