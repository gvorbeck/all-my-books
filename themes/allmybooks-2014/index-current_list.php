<?php /* CURRENTLY READING */ ?>
<?php 
$args = array(
  'posts_per_page' => -1,
  'meta_query'      => array(
    array(
      'key'   => 'reading_state',
      'value'  => 1,
    ),
  ),
);

$current_query = new WP_Query( $args );
$this_these = ( $current_query->found_posts > 1 ? 'These' : 'This');
if ( $current_query->have_posts() ) {
?>
  <section id="current-read" class="book-shelf sticky--cr">
    <h1><?php echo svg_bookmark(); ?>I'm Reading <?php echo $this_these; ?></h1>
    <ul id="current-read-list" class="book-list current">
<?php
      while ( $current_query->have_posts() ) {
        $current_query->the_post();
        the_book_builder( $post->ID, 0 );
      }
?>
    </ul>
  </section>
<?php
}
wp_reset_postdata();
