<?php
/* START GETTING THEME FUNCTIONALITY SET UP */
// Add theme support for Post Formats.
add_theme_support( 'post-formats', array( 'aside', 'gallery' ) );
// Add theme support for Post Thumbnails.
add_theme_support( 'post-thumbnails' );
// Add theme support for Automatic Feeds Links
add_theme_support( 'automatic-feed-links' );

// Define SITE_URL global variable for dev purposes.
define('SITE_URL', $_SERVER['HTTP_HOST']);

// Register Custom Menus
if ( function_exists( 'register_nav_menus' ) ) {
	register_nav_menus( );
}

// Custom mime types for uploading epubs
add_filter('upload_mimes', 'custom_upload_mimes');
function custom_upload_mimes ( $existing_mimes=array() ) {
	// add the file extension to the array
	$existing_mimes['epub'] = 'mime/type';
   // call the modified list of extensions
	return $existing_mimes;
}

/* END GETTING THEME FUNCTIONALITY SET UP */

/* START THEME FUNCTIONS */
function is_login_page() {
	return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

if ( ! is_admin() && ! is_login_page() ) {
	// Enqueue Styles
	if ( ! function_exists( 'site_styles' ) ) {
		function site_styles() {
			wp_register_style( 'main', get_bloginfo( 'template_directory' ) . '/_css/main.css', false, date('W.0') );
    	wp_enqueue_style( 'main' );
    	if ( 'localhost:8888' == SITE_URL ) {
				wp_register_style( 'dev', get_bloginfo( 'template_directory' ) . '/_css/dev.css', false, date('W.0') );
				wp_enqueue_style( 'dev' );
    	}
    }
  }
  add_action( 'init', 'site_styles' );

  // Enqueue Scripts
  if ( ! function_exists( 'site_scripts' ) ) {
	  function site_scripts() {
	  	wp_register_script( 'site-js', get_template_directory_uri() . '/_js/site.js', array('jquery'), date('W.0'), true );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'site-js' );
	  }
	}
  add_action( 'wp_enqueue_scripts', 'site_scripts' );
}

if ( ! function_exists( 'get_the_slug' ) ) {
	function get_the_slug( $phrase ) {
	    $result = strtolower($phrase);
	    $result = preg_replace("/[^a-z0-9\s-]/", "", $result);
	    $result = trim(preg_replace("/[\s-]+/", " ", $result));
	    $result = preg_replace("/\s/", "-", $result);
	    return $result;
	}
}

if ( ! function_exists( 'get_the_post_authors_string' ) ) {
	function get_the_post_authors_string( $post_id ) {
		$authors = wp_get_post_terms( $post_id, 'authors' );
		$i = 0;
		$authors_str = '';
		if ( ! empty( $authors ) ) {
			foreach ( $authors as $author ) {
				if ( 0 < $i ) {
					$authors_str .= ', ';
				}
				$authors_str .= $author->name;
				$i++;
			}
		}
		return $authors_str;
	}
}

if ( ! function_exists( 'get_series_list' ) ) {
	function get_series_list( $post_id ) {
		if ( have_rows( 'series_info', $post_id ) ) {
			$i = 0;
			while ( have_rows( 'series_info', $post_id ) ) {
				the_row();
				$series_id      = get_sub_field( 'series_name', $post_id );
				$series_object  = get_term( $series_id, 'series', $post_id );
				if ( $i > 0 ) {
					$series_string .= ', ';
				}
				$series_string .= $series_object->name . ' #' . get_sub_field( 'series_position', $post_id );
				$i++;
			}
			return $series_string;
		}
	}
}

if ( ! function_exists( 'get_action_links' ) ) {
	function get_action_links( $post_id ) {
		$ascii_title   = strtolower( urlencode( get_post( $post_id )->post_title ) );
		$ascii_authors = strtolower( urlencode( str_replace( ', ', ' ', get_the_post_authors_string( $post_id ) ) ) );
		if( ! get_field('book_file', $post_id) ) {
			$book_missing  = 'missing';
			$book_no_click = "onclick='return false'";
		}
		$book_links    = array();
		$book_links[]  = "<a id='amazon-action-link' class='action-link' href='http://www.amazon.com/s/field-keywords=$ascii_title+$ascii_authors' target='_blank' title='Search Amazon'>" . file_get_contents( locate_template( "_images/icons/amazon.svg" ) ) . "</a>";
		$book_links[]  = "<a id='wiki-action-link' class='action-link' href='http://en.wikipedia.org/wiki/Special:Search?search=$ascii_title+$ascii_authors' target='_blank' title='Search Wikipedia'>" . file_get_contents( locate_template( "_images/icons/wikipedia.svg" ) ) . "</a>";
		$book_links[]  = "<a id='luzme-action-link' class='action-link' href='http://luzme.com/search_all?keyword=$ascii_title+$ascii_authors' target='_blank' title='Shop Luzme'>" . file_get_contents( locate_template( "_images/icons/store.svg" ) ) . "</a>";
		if ( is_user_logged_in() ) {
			$book_links[] = "<a class='download-action-link action-link $book_missing' href='" . get_field('book_file', $post_id) . "' title='Download " . esc_attr( get_post( $post_id )->post_title ) . "' $book_no_click >" . file_get_contents( locate_template( "_images/icons/download.svg" ) ) . "</a>";
			$book_links[]  = '<a class="edit-action-link action-link" href="' . get_edit_post_link( $post_id ) . '" target="_blank" title="Edit ' . esc_attr( get_post( $post_id )->post_title ) . '">' . file_get_contents( locate_template( "_images/icons/edit.svg" ) ) . '</a>';
		}
		return $book_links;
	}
}

if ( ! function_exists( 'cat_class_builder' ) ) {
	function cat_class_builder( $post_id ) {
		// Set up category data (ommiting Uncategorized, of course).
		$cats = get_the_category( $post_id );
		$cat_class = 'genre-';
		if ( ! empty( $cats ) ) {
			foreach ( $cats as $cat ) {
				if ( 1 != $cat->cat_ID ) {
					$cat_class .= $cat->slug;
					break;
				}
			}
		}
		return $cat_class;
	}
}

if ( ! function_exists( 'the_book_builder' ) ) {
	function the_book_builder( $post_id, $list_order, $class = '', $time = '' ) {
		// Set up category data (ommiting Uncategorized, of course).
		$cats = get_the_category( $post_id );
		$cat_list = '';
		if ( ! empty( $cats ) ) {
			$i = 0;
			foreach ( $cats as $cat ) {
				if ( 1 != $cat->cat_ID ) {
					if ( $i > 0 ) {
						$cat_list .= ', ';
					}
					$i++;
					$cat_list .= trim( $cat->cat_name );
				}
			}
		}
		// Set up tag data.
		$tags = wp_get_post_terms( $post_id, 'post_tag' );
		$tag_list = '';
		if ( ! empty( $tags ) ) {
			$i = 0;
			foreach ( $tags as $tag ) {
				if ( $i > 0 ) {
					$tag_list .= ', ';
				}
				$i++;
				$tag_list .= trim( $tag->name );
			}
		}
		// Combine category and tag data.
		$cat_tag_string = '';
		if ( ! empty( $cat_list ) || ! empty( $tag_list ) ) {
			if ( ! empty( $cat_list ) ) {
				$cat_tag_string .= $cat_list;
			}
			if ( ! empty( $cat_list ) && ! empty( $tag_list ) ) {
				$cat_tag_string .= ', ';
			}
			if ( ! empty( $tag_list ) ) {
				$cat_tag_string .= $tag_list;
			}
		}
		echo "<li id='$post_id' class='book $class' data-order='$list_order'>";
			echo '<div class="book--action-links">' . implode( ' ', get_action_links( $post_id ) ) . '</div>';
			echo '<p>';
				echo '<span class="book--title">' . get_the_title( $post_id ) . '</span>';
				echo ' by <span class="book--author">' . get_the_post_authors_string( $post_id ) . '</span>';
				if ( '' != get_series_list( $post_id ) ) {
					echo ' <span class="book--series">[' . get_series_list( $post_id ) . ']</span>';
				}
				if ( '' != $cat_tag_string ) {
					echo ' <span class="book--tags">(' . $cat_tag_string . ')</span>';
				}
				if ( '' != $time ) {
					echo "<span class='book--want-date'>since $time</span>";
				}
			echo '</p>';
		echo '</li>';
	}
}
/* END THEME FUNCTIONS */

/* CUSTOM FIELD CODE START */
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_book-data',
		'title' => 'Book Data',
		'fields' => array (
			array (
				'key' => 'field_533efbfd26ca0',
				'label' => 'Book File',
				'name' => 'book_file',
				'type' => 'file',
				'instructions' => 'Upload the e-book file here.',
				'save_format' => 'url',
				'library' => 'all',
			),
			array (
				'key' => 'field_5345e5255e78d',
				'label' => 'Series Info',
				'name' => 'series_info',
				'type' => 'repeater',
				'sub_fields' => array (
					array (
						'key' => 'field_5345f762c6e37',
						'label' => 'Series Name',
						'name' => 'series_name',
						'type' => 'taxonomy',
						'column_width' => '',
						'taxonomy' => 'series',
						'field_type' => 'select',
						'allow_null' => 0,
						'load_save_terms' => 0,
						'return_format' => 'id',
						'multiple' => 0,
					),
					array (
						'key' => 'field_5345f87655fe5',
						'label' => 'Series Position',
						'name' => 'series_position',
						'type' => 'number',
						'column_width' => '',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => '',
						'max' => '',
						'step' => 1,
					),
				),
				'row_min' => '',
				'row_limit' => '',
				'layout' => 'table',
				'button_label' => 'Add Series',
			),
			array (
				'key' => 'field_4fa81bf2a77dd',
				'label' => 'Read Records',
				'name' => 'read_records',
				'type' => 'repeater',
				'sub_fields' => array (
					array (
						'key' => 'field_4fa81bf2a8786',
						'label' => 'Read Year',
						'name' => 'read_year',
						'type' => 'text',
						'column_width' => '',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
				),
				'row_min' => '',
				'row_limit' => '',
				'layout' => 'table',
				'button_label' => 'Add Read Year',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'post',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
				0 => 'custom_fields',
				1 => 'discussion',
				2 => 'comments',
				3 => 'slug',
				4 => 'author',
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_reading-state-fields',
		'title' => 'Reading State Fields',
		'fields' => array (
			array (
				'key' => 'field_52d951d585923',
				'label' => 'Reading State',
				'name' => 'reading_state',
				'type' => 'radio',
				'choices' => array (
					0 => 'Read',
					1 => 'Currently Reading',
					2 => 'Want To Read',
				),
				'other_choice' => 0,
				'save_other_choice' => 0,
				'default_value' => 2,
				'layout' => 'vertical',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'post',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'side',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}
/* CUSTOM FIELD CODE END */
/* CUSTOM TAXONOMY CODE START */
// Series
add_action('init', 'cptui_register_my_taxes_series');
function cptui_register_my_taxes_series() {
register_taxonomy( 'series',array (
  0 => 'post',
),
array( 'hierarchical' => false,
	'label' => 'Series',
	'show_ui' => true,
	'query_var' => true,
	'show_admin_column' => false,
	'labels' => array (
  'search_items' => 'Series',
  'popular_items' => '',
  'all_items' => '',
  'parent_item' => '',
  'parent_item_colon' => '',
  'edit_item' => '',
  'update_item' => '',
  'add_new_item' => '',
  'new_item_name' => '',
  'separate_items_with_commas' => '',
  'add_or_remove_items' => '',
  'choose_from_most_used' => '',
)
) ); 
}
// Author
add_action('init', 'cptui_register_my_taxes_authors');
function cptui_register_my_taxes_authors() {
register_taxonomy( 'authors',array (
  0 => 'post',
),
array( 'hierarchical' => false,
	'label' => 'Authors',
	'show_ui' => true,
	'query_var' => true,
	'show_admin_column' => false,
	'labels' => array (
  'search_items' => 'Author',
  'popular_items' => '',
  'all_items' => '',
  'parent_item' => '',
  'parent_item_colon' => '',
  'edit_item' => '',
  'update_item' => '',
  'add_new_item' => '',
  'new_item_name' => '',
  'separate_items_with_commas' => '',
  'add_or_remove_items' => '',
  'choose_from_most_used' => '',
)
) ); 
}