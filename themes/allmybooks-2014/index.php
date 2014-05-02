<?php
get_header();

/* FINISHED READING */
echo '<section id="finished-read" class="book-shelf"><div class="ribbon"></div><h1>I\'m Finished Reading These</h1><ul id="finished-read-list" class="book-list"></ul><p class="instructions">Drag a book here to mark it as finished.</p></section>';

/* CURRENTLY READING */
$args = array(
	'posts_per_page' => -1,
	'meta_query' 	   => array(
		array(
			'key' 	=> 'reading_state',
			'value'	=> 1,
		),
	),
);

$current_query = new WP_Query( $args );

if ( $current_query->have_posts() ) {
	echo '<section id="current-read" class="book-shelf"><div class="ribbon"></div><h1>I\'m Reading These</h1><ul id="current-read-list" class="book-list">';
	while ( $current_query->have_posts() ) {
		$current_query->the_post();
		the_book_builder( $post->ID );
	}
	echo '</ul></section>';
}
wp_reset_postdata();

/* FUTURE READING */
// 1. GET ALL POSTS MARKED AS 'WANT TO READ'.
$args = array(
	'posts_per_page' => -1,
	'meta_query'     => array(
		array(
			'key'   => 'reading_state',
			'value' => 2,
		),
	),
);
$future_query = new WP_Query( $args );
// 2. GO THROUGH THEME AND FIND OUT IF THE 'READING LIST' TABLE HAS ANY PROBLEMS WITH THIS INFORMATION.
if ( $future_query->have_posts() ) {
	while ( $future_query->have_posts() ) {
		$future_query->the_post();
		$results = $wpdb->get_results( 'SELECT * FROM wp_reading_list WHERE bid = ' . $post->ID, ARRAY_N );
		
		if ( count( $results ) > 1 ) {
			// THERE ARE DUPLICATES OF THIS BOOK IN THE TABLE!!!
			$i = 0;
			foreach ($results as &$r) {
				if ( $i > 0 ) {
					// ...SO REMOVE THE DUPLICATES (BUT NOT THE ORIGINAL)
					$wpdb->delete( 'wp_reading_list', array( 'id' => $r[0] ) );
				}
				$i++;
			}
		}
		if ( count( $results ) > 0 ) {
			// THIS BOOK IS ALREADY PRESENT IN THE TABLE ...SO DO NOTHING.
		} else {
			// THIS BOOK IS NOT IN THE TABLE ...SO PUT IT IN THERE.
			global $wpdb;
			$bid           = $post->ID;
			$table_name    = $wpdb->prefix . "reading_list";
			$rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'bid' => $bid ) );
		}
	}
}
wp_reset_postdata();
// AT THIS POINT YOU HAVE CLEANED UP YOUR TABLE, NOW START LAYING IT  OUT IN CODE.
$future_list = $wpdb->get_results( 'SELECT * FROM wp_reading_list', ARRAY_N );
echo '<section id="future-read" class="book-shelf"><div class="ribbon"></div><h1>I Want To Read These</h1><ul id="future-read-list" class="book-list">';
foreach ( $future_list as &$f ) {
	the_book_builder( $f[2] );
}

get_footer();
