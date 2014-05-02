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
if ( $future_query->have_posts() ) {
	while ( $future_query->have_posts() ) {
		$future_query->the_post();
		global $wpdb;
		$results = $wpdb->get_results( 'SELECT * FROM wp_reading_list WHERE bid = ' . $post->ID, ARRAY_N );
		
		echo '<h4>the count for ' . $post->ID . ' is ' . $results . '</h4>'; //REMOVE
		var_dump($results); //REMOVE
		
		if ( count( $results ) > 1 ) {
			// THERE ARE DUPLICATES OF THIS BOOK IN THE TABLE!!!
			echo 'there are multiples'; //REMOVE
			$i = 0;
			foreach ($results as &$r) {
				if ( $i > 0 ) {
					$wpdb->delete( 'wp_reading_list', array( 'id' => $r[0] ) );
				}
				$i++;
			}
		}
		if ( count( $results ) > 0 ) {
			// THIS BOOK IS ALREADY PRESENT IN THE TABLE
			echo 'there is something here';
		}
	}
}
/*if ( $future_query->have_posts() ) {
	echo '<section id="future-read" class="book-shelf"><div class="ribbon"></div><h1>I Want To Read These</h1><ul id="future-read-list" class="book-list">';
	while ( $future_query->have_posts() ) {
		$future_query->the_post();
		the_book_builder( $post->ID );
	}
	echo '</ul></section>';
}
wp_reset_postdata();*/

get_footer();
