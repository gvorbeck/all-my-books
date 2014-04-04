<?php
/* START GETTING THEME FUNCTIONALITY SET UP */
// Add theme support for Post Formats.
add_theme_support( 'post-formats', array( 'aside', 'gallery' ) );
// Add theme support for Post Thumbnails.
add_theme_support( 'post-thumbnails' );
// Add theme support for Automatic Feeds Links
add_theme_support( 'automatic-feed-links' );

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
		$series = wp_get_post_terms( $post_id, 'series_id' );
		$series_string = '';
		if ( ! empty( $series ) ) {
			$i = 0;
			foreach ( $series as $s ) {
				$series_subject = $s->name;
				preg_match( '/\d+$/', $series_subject, $series_no );
				$series_slug = str_replace( '--'.$series_no[0], '', $series_subject );
				$series_title = get_term_by( 'slug', $series_slug, 'series' );
				if ( $i > 0 ){
					$series_string .= ', ';
				}
				$i++;
				$series_string .= trim( $series_title->name.' #'.$series_no[0] );
			}
		}
		return $series_string;
	}
}

if ( ! function_exists( 'get_wiki_amazon_links' ) ) {
	function get_wiki_amazon_links( $post_id ) {
		$ascii_title   = strtolower( urlencode( get_post( $post_id )->post_title ) );
		$ascii_authors = strtolower( urlencode( str_replace( ', ', ' ', get_the_post_authors_string( $post_id ) ) ) );
		
		$book_links    = array();
		$book_links[]  = "<a class='amazon-info-link info-link' href='http://www.amazon.com/s/field-keywords=$ascii_title+$ascii_authors' target='_blank' title='Search Amazon'><span class='icon--font'></span></a>";
		$book_links[]  = "<a class='wiki-info-link info-link' href='http://en.wikipedia.org/wiki/Special:Search?search=$ascii_title+$ascii_authors' target='_blank' title='Search Wikipedia'><span class='icon--font'></span></a>";
		
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
	function the_book_builder( $post_id ) {
	
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

		echo '<li id="' . $post_id . '" class="book">';
			echo '<div class="book--info-links">' . implode( ' ', get_wiki_amazon_links( $post_id ) ) . '</div>';
			echo '<p>';
				echo '<span class="book--title">' . get_the_title( $post_id ) . '</span>';
				echo ' by <span class="book--author">' . get_the_post_authors_string( $post_id ) . '</span>';
				if ( '' != $cat_tag_string ) {
					echo ' <span class="book--tags">(' . $cat_tag_string . ')</span>';
				}
				if ( '' != get_series_list( $post_id ) ) {
					echo ' <span class="book--series">[' . get_series_list( $post_id ) . ']</span>';
				}
			echo '</p>';
		echo '</li>';
	}
}
/* END THEME FUNCTIONS */
