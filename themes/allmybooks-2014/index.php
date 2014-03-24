<?php
get_header();

/* FINISHED READING */
echo '<section id="finished-read" class="book-shelf"><div class="ribbon"></div><h1>I\'m Finished Reading</h1><ul id="finished-read-list" class="book-list"></ul><p class="instructions">Drag a book here to mark it as finished.</p></section>';

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
	echo '<section id="current-read" class="book-shelf"><div class="ribbon"></div><h1>I\'m Reading</h1><ul id="current-read-list" class="book-list">';
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
	echo '<section id="future-read" class="book-shelf"><div class="ribbon"></div><h1>I Want To Read</h1><ul id="future-read-list" class="book-list">';
	while ( $future_query->have_posts() ) {
		$future_query->the_post();
		the_book_builder( $post->ID );
	}
	echo '</ul></section>';
}
wp_reset_postdata();

get_footer();
