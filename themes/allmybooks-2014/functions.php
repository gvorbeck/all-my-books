<?php
/* START GETTING THEME FUNCTIONALITY SET UP */
// Add theme support for Post Formats.
add_theme_support('post-formats', array('aside', 'gallery'));
// Add theme support for Post Thumbnails.
add_theme_support('post-thumbnails');
// Add theme support for Automatic Feeds Links
add_theme_support('automatic-feed-links');

// Define SITE_URL global variable for dev purposes.
define('SITE_URL', $_SERVER['HTTP_HOST']);

// Register Custom Menus
if (function_exists('register_nav_menus')) {
  register_nav_menus();
}

// Custom mime types for uploading epubs
add_filter('upload_mimes', 'custom_upload_mimes');
function custom_upload_mimes ($existing_mimes=array()) {
  // add the file extension to the array
  $existing_mimes['epub'] = 'mime/type';
  $existing_mimes['mobi'] = 'mime/type';
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
      wp_register_style('loaders', content_url() . '/node_modules/loaders.css/loaders.min.css', false, false);
      wp_register_style('main', get_bloginfo('template_directory') . '/style.css', false, date('W.0'));
      wp_enqueue_style('loaders');
      wp_enqueue_style('main');
    }
  }
  add_action( 'init', 'site_styles' );

  // Enqueue Scripts
  if (!function_exists('site_scripts')) {
    function site_scripts() {
      // Remove weird auto-added emoji script from site 
      remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
      remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
      remove_action( 'wp_print_styles', 'print_emoji_styles' );
      remove_action( 'admin_print_styles', 'print_emoji_styles' );
      // Site js
      wp_register_script('site-js', get_template_directory_uri() . '/javascripts/site.min.js', array('jquery'), date('W.0'), true);
      // loaders.css
      wp_register_script('loaders', content_url() . '/node_modules/loaders.css/loaders.css.js', '', false, true);
      // jQuery
      wp_deregister_script('jquery');
      wp_register_script('jquery', ('https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js'), false, '2.1.4', true);
      wp_enqueue_script('jquery');
      wp_enqueue_script('site-js');
      wp_enqueue_script('loaders');
    }
  }
  add_action('wp_enqueue_scripts', 'site_scripts');
}

if ( ! function_exists( 'amb_admin_script' ) ) {
  function amb_admin_script() {
    wp_register_script( 'admin-js', get_template_directory_uri() . '/javascripts/admin.min.js', array('jquery'), date('W.0'), true );
    wp_enqueue_script( 'admin-js' );
    wp_register_style( 'admin', get_bloginfo( 'template_directory' ) . '/admin.css', false, date('W.0') );
    wp_enqueue_style( 'admin' );
    $translation_array = array( 'templateUrl' => get_stylesheet_directory_uri() );
    //after wp_enqueue_script
    wp_localize_script( 'admin-js', 'amb_theme_directory', $translation_array );
  }
}
add_action( 'admin_enqueue_scripts', 'amb_admin_script' );

if (!function_exists('get_the_slug')) {
  function get_the_slug($phrase) {
    $result = strtolower($phrase);
    $result = preg_replace("/[^a-z0-9\s-]/", "", $result);
    $result = trim(preg_replace("/[\s-]+/", " ", $result));
    $result = preg_replace("/\s/", "-", $result);
    return $result;
  }
}

/*
  
*/
if (!function_exists('amb_reading_list_sanity_check')) {
  function amb_reading_list_sanity_check() {
    // Get all the content from the reading_list table
    $reading_list = $wpdb->get_results('SELECT * FROM wp_reading_list ORDER BY listorder');
    $i = 1;
    foreach ($reading_list as $r) {
      $wpdb->update( 'wp_reading_list', array('listorder' => $i), array('bid' => $r->bid));
      $i++;
    }
  }
}

/*
  @function   get_the_post_authors_string
  @parameters $post_id - The ID of the post whose authors are sought.
  @returns    A comma-delimited list of authors associated with the post.
*/
if (!function_exists('get_the_post_authors_string')) {
  function get_the_post_authors_string($post_id) {
    return implode(', ', wp_get_post_terms($post_id, 'authors', array('fields' => 'names')));
  }
}

/*
  @function   get_series_list
  @parameters $post_id - The ID of the post whose series data is sought.
  @returns    A comma-delimited list of series data associated with the post.
*/
if (!function_exists( 'get_series_list')) {
  function get_series_list($post_id) {
    if (have_rows('series_info', $post_id)) {
      $full_series_array = [];
      while (have_rows('series_info', $post_id)) {
        the_row();
        $full_series_array[] = get_sub_field('series_name', $post_id)->name . ' #' . get_sub_field('series_position', $post_id);
      }
      return implode(', ', $full_series_array);
    }
  }
}

/*
  @function   get_book_links
  @parameters $post_id - The ID of the post to build links around.
  @returns    A <UL> element filled with link <LI> elements.
*/
if (!function_exists('get_book_links')) {
  function get_book_links($post_id) {
    $ascii_title   = strtolower(urlencode(get_post($post_id)->post_title));
    $ascii_authors = strtolower(urlencode(str_replace(', ', ' ', get_the_post_authors_string($post_id))));
    $book_links_html = "<ul class='book--links'><li><a class='amazon-link book--link' href='http://www.amazon.com/s/field-keywords=$ascii_title+$ascii_authors' target='_blank' title='Search Amazon'>" . svg_amazon() . "</a></li><li><a class='wiki-link book--link' href='http://en.wikipedia.org/wiki/Special:Search?search=$ascii_title+$ascii_authors' target='_blank' title='Search Wikipedia'>" . svg_wikipedia() . "</a></li><li><a class='luzme-link book--link' href='http://luzme.com/search_all?keyword=$ascii_title+$ascii_authors' target='_blank' title='Shop Luzme'>" . svg_store() . "</a></li>";
    if (is_user_logged_in()) {
      if (get_field('book_file', $post_id)) {
        $book_links_html .= "<li><a class='download-link book--link' href='" . get_field('book_file', $post_id) . "' title='Download " . esc_attr(get_post($post_id)->post_title) . "'>" . svg_download() . "</a></li>";
      }
      $book_links_html .= '<li><a class="edit-link book--link" href="' . get_edit_post_link($post_id) . '" target="_blank" title="Edit ' . esc_attr(get_post($post_id)->post_title) . '">' . svg_edit() . '</a></li><li><a class="delete-link book--link" href="javascript:;" title="Delete ' . esc_attr(get_post($post_id)->post_title) . '">' . svg_trash() . '</a></li>';
    }
    return $book_links_html . '</ul>';
  }
}

/*
  @function   amb_get_cats_tags
  @parameters $post_id - The ID of the post to capture category and tag data from.
  @returns    A comma-delimited list of categoryt and/or tag data associated with the post.
*/
if (!function_exists('amb_get_cats_tags')) {
  function amb_get_cats_tags($post_id) {
    
    // Categories
    $categories = get_the_category($post_id);
    foreach ($categories as $key=>$value) {
      $categories[$key] = $value->name;
      if ($categories[$key] == 'Uncategorized') {
        array_splice($categories, $key, 1);
      }
    }
    $categories = implode(', ', $categories);
    
    // Tags
    $tags = implode(', ', wp_get_post_terms($post_id, 'post_tag', array('fields' => 'names')));
    
    // Put it all together.
    if ($categories && $tags) {
      return $categories . ', ' . $tags;
    } else {
      return $categories . $tags;
    }
  }
}

if (!function_exists('the_book_builder')) {
  function the_book_builder($post_id, $list_order, $class = '', $time = '') {
    // Last read data.
    $rows          = get_field('read_records', $post_id);
    $last_row      = is_array($rows) ? end($rows) : '';
    $last_row_year = is_array($rows) ? $last_row['read_year'] : '';
    ?>
    <li id="book-<?php echo $post_id; ?>" class="book <?php echo $class; ?>" data-order="<?php echo $list_order; ?>">
      <article>
        <header>
          <h1 class="book--title"><?php echo get_the_title($post_id); ?></h1>
          <div class="book--options">
            <a class="finished" href="javascript:;">Finished</a>
            <a class="current" href="javascript:;">Reading</a>
            <a class="future" href="javascript:;">Want</a>
          </div>
        </header>
        <div class="book--content">
          <?php if ($last_row_year) { ?>
            <div class='book--last-date book--meta'>
              <div>
                <span><?php echo $last_row_year; ?></span>
              </div>
              <span class='book--meta-label'>Last Read</span>
            </div>
          <?php } if ($time) { ?>
            <div class='book--want-date book--meta'>
              <div><?php echo date('M \'y', strtotime($time)); ?></div>
              <span class='book--meta-label'>Added</span>
            </div>
          <?php } ?>
          <div class="book--details">
            <?php if (get_the_post_authors_string($post_id)) { ?>
              <span class="book--author book--detail">
                <?php //The second span is for js ?>
                <?php echo svg_author() . '<span>' . get_the_post_authors_string($post_id); ?></span>
              </span>
            <?php } if (get_series_list($post_id)) { ?>
              <span class="book--series book--detail">
                <?php echo svg_series() . get_series_list($post_id); ?>
              </span>
            <?php } if (amb_get_cats_tags($post_id)) { ?>
              <span class="book--tags book--detail">
                <?php echo svg_tag() . strtolower(amb_get_cats_tags($post_id)); ?>
              </span>
            <?php } ?>
          </div>
        </div>
        <footer>
          <?php echo get_book_links($post_id); ?>
        </footer>
      </article>
    </li>
    <?php
  }
}

function amb_add_meta_box() {
  $screens = array( 'post' );
  foreach ( $screens as $screen ) {
    add_meta_box(
      'amb_sectionid',
      __( 'Reading List Position', 'amb_textdomain' ),
      'amb_meta_box_callback',
      $screen
    );
  }
}
add_action( 'add_meta_boxes', 'amb_add_meta_box' );

function amb_meta_box_callback( $post ) {
  // Add an nonce field so we can check for it later.
  wp_nonce_field( 'amb_meta_box', 'amb_meta_box_nonce' );
  global $wpdb;
  $current_place = $wpdb->get_results( "SELECT * FROM wp_reading_list WHERE bid = $post->ID" );
  $dropdown = $wpdb->get_results( "SELECT listorder FROM wp_reading_list ORDER BY listorder" );
  if ( 'auto-draft' == $post->post_status ) {
    // Is a draft. Not in WTR.
  } else {
    echo "<p>The book is # ";
      echo "<select id='wp-reading-order-dropdown'>";
        if ( empty( $current_place) ) {
          echo "<option value='NULL' selected='selected'>-</option>";
        }
        foreach ( $dropdown as $d ) {
          if ( $current_place[0]->listorder == $d->listorder ) {
            echo "<option value='$d->listorder' selected='selected'>$d->listorder</option>";
          } else {
            echo "<option value='$d->listorder'>$d->listorder</option>";
          }
        }
      echo "</select>";
    echo "</p>";
  }
}
function amb_save_meta_box_data($post_id) {
  // Check if our nonce is set.
  if (!isset( $_POST['amb_meta_box_nonce'])) {
    return;
  }
  // Verify that the nonce is valid.
  if (!wp_verify_nonce($_POST['amb_meta_box_nonce'], 'amb_meta_box')) {
    return;
  }
  // If this is an autosave, our form has not been submitted, so we don't want to do anything.
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
  // Check the user's permissions.
  if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
    if ( ! current_user_can( 'edit_page', $post_id ) ) {
      return;
    }
  } else {
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
      return;
    }
  }
  // Make sure that it is set.
  if ( ! isset( $_POST['amb_new_field'] ) ) {
    return;
  }
  // Sanitize user input.
  $my_data = sanitize_text_field( $_POST['amb_new_field'] );
  // Update the meta field in the database.
  update_post_meta( $post_id, '_my_meta_value_key', $my_data );
  //$wpdb->update( 'wp_reading_list',  );
}
add_action( 'save_post', 'amb_save_meta_box_data' );
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
            'return_format' => 'object',
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
  register_taxonomy('authors',
    array(0 => 'post'),
    array(
      'hierarchical' => false,
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
        'choose_from_most_used' => ''
      )
    )
  );
}
/* CUSTOM TAXONOMY CODE STOP */
/* SVG CODE START */
function svg_bookmark() {
  return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="234.604px" height="525.882px" viewBox="188.698 134.5 234.604 525.882" enable-background="new 188.698 134.5 234.604 525.882" xml:space="preserve"><path d="M409.703 124.664H202.296c-7.515 0-13.599 6.089-13.599 13.599v509.349c0 5.4 3.1 10 7.7 12.3 c3.042 1.5 7.98-0.446 10.532-2.662L306 571.111l99.083 86.09c2.552 2.2 7.5 4.1 10.5 2.7 c4.547-2.203 7.687-6.86 7.687-12.252V138.269C423.309 130.8 417.2 124.7 409.7 124.664z" class="style0"></path></svg>';
}
function svg_amazon() {
  return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="32" height="32" viewBox="0 0 32 32"><g/><path d="M28.416 22.048c-3.927 1.667-8.197 2.47-12.079 2.47-5.756 0-11.329-1.578-15.836-4.2-0.394-0.231-0.688 0.173-0.36 0.5 4.2 3.8 9.7 6 15.8 6 4.4 0 9.452-1.378 12.956-3.961 0.58-0.431 0.084-1.071-0.509-0.819z" class="style0"/><path d="M31.878 20.061c-0.383-0.472-3.669-0.879-5.672 0.53-0.31 0.215-0.257 0.5 0.1 0.5 1.129-0.134 3.643-0.436 4.1 0.1 0.5 0.575-0.499 2.94-0.921 3.995-0.129 0.3 0.1 0.5 0.4 0.2 1.882-1.572 2.365-4.866 1.982-5.342z" class="style0"/><path d="M16.859 10.903c-1.527 0.174-3.525 0.285-4.956 0.915-1.65 0.712-2.81 2.169-2.81 4.3 0 2.7 1.7 4.1 3.9 4.1 1.9 0 2.899-0.441 4.345-1.917 0.5 0.7 0.6 1 1.5 1.8 0.2 0.1 0.5 0.1 0.623-0.063l0.007 0.007c0.526-0.467 1.483-1.301 2.020-1.75 0.215-0.178 0.178-0.463 0.007-0.701-0.482-0.667-0.993-1.208-0.993-2.443v-4.107c0-1.739 0.122-3.336-1.16-4.534-1.012-0.971-2.687-1.312-3.97-1.312-2.506 0-5.305 0.934-5.894 4.033-0.059 0.3 0.2 0.5 0.4 0.552l2.558 0.274c0.237-0.011 0.411-0.245 0.456-0.482 0.219-1.068 1.116-1.583 2.12-1.583 0.5 0 1.2 0.2 1.5 0.7 0.4 0.5 0.3 1.3 0.3 1.909l0 0.341zM16.366 16.382c-0.419 0.741-1.083 1.197-1.824 1.197-1.012 0-1.601-0.771-1.601-1.909 0-2.246 2.013-2.654 3.918-2.654v0.571c0 1 0 1.883-0.493 2.795z" class="style0"/></svg>';
}
function svg_wikipedia() {
  return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="33.567px" height="21.624px" viewBox="3.639 4.675 33.567 21.624" enable-background="new 3.639 4.675 33.567 21.624" xml:space="preserve"><path d="M25.409 26.295l-4.3-10.125c-1.702 3.342-3.589 6.814-5.206 10.12c-0.009 0.017-0.78 0.007-0.781-0.003 c-2.468-5.76-5.027-11.482-7.507-17.237C7.041 7.6 5 5.4 3.6 5.393C3.648 5.2 3.6 4.9 3.6 4.644l8.497-0.004 L12.13 5.379c-0.998 0.046-2.722 0.683-2.276 1.78c1.2 2.6 5.4 12.6 6.6 15.149c0.8-1.564 3.033-5.738 3.952-7.501 c-0.721-1.479-3.103-7.001-3.817-8.393c-0.539-0.907-1.89-1.018-2.931-1.033c0-0.233 0.013-0.411 0.008-0.729l7.47 0.023v0.678 c-1.012 0.028-1.969 0.404-1.536 1.37c1 2.1 1.6 3.6 2.5 5.5c0.295-0.565 1.805-3.662 2.524-5.298 c0.435-1.086-0.215-1.494-2.035-1.543c0.024-0.179 0.009-0.538 0.022-0.709c2.324-0.009 5.83-0.017 6.451-0.026l0.004 0.7 c-1.187 0.046-2.412 0.678-3.053 1.658l-3.106 6.443c0.341 0.8 3.3 7.5 3.6 8.223l6.424-14.823 c-0.457-1.2-1.916-1.468-2.486-1.482c0.004-0.191 0.004-0.482 0.007-0.725l6.704 0.05l0.011 0.034l-0.011 0.6 c-1.471 0.044-2.381 0.831-2.924 2.12c-1.336 3.018-5.42 12.577-8.146 18.807c-0.004 0.003-0.715-0.001-0.716-0.002V26.295z"/></svg>';
}
function svg_store() {
  return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="44.16px" height="41.804px" viewBox="9.92 12.503 44.16 41.804" enable-background="new 9.92 12.503 44.16 41.804" xml:space="preserve"><path d="M54.08 17.924H21.512l-0.932-5.421h-8.252c-1.332 0-2.408 1.078-2.408 2.409v2.408h6.602l4.248 24.7 c0.094 0.5 0.6 1 1.2 0.984h28.527c1.332 0 2.41-1.076 2.41-2.41v-2.406H24.998l-0.587-3.414h22.042 c2.66 0 5.172-2.128 5.611-4.75L54.08 17.924z"/><circle cx="29.4" cy="50.1" r="4.2"/><path d="M39.682 50.141c0 2.3 1.9 4.2 4.2 4.164c2.301 0 4.168-1.865 4.168-4.164s-1.867-4.166-4.168-4.166 C41.545 46 39.7 47.8 39.7 50.141z"/></svg>';
}
function svg_download() {
  return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="612px" height="605.88px" viewBox="0 96.12 612 605.88" enable-background="new 0 96.12 612 605.88" xml:space="preserve"><path d="M191.556 255.852c-2.448-3.672-2.652-7.14-0.612-10.404c2.04-4.08 5.1-6.12 9.18-6.12h49.572V105.912 c0-2.856 1.02-5.202 3.06-7.038s4.488-2.754 7.344-2.754h91.8c6.936 0 10.4 3.3 10.4 9.792v133.416h49.572 c4.08 0 7.1 1.8 9.2 5.508c2.04 4.1 1.8 7.752-0.609 11.016L314.568 404.568c-2.04 2.854-4.692 4.281-7.956 4.3 c-3.672 0-6.528-1.428-8.568-4.281L191.556 255.852L191.556 255.852z M574.056 407.628c10.608 0 19.6 3.8 26.9 11.3 c7.344 7.5 11 16.6 11 27.231v217.873c0 10.608-3.672 19.584-11.016 26.931C593.64 698.3 584.7 702 574.1 702 H37.944c-10.608 0-19.584-3.672-26.928-11.016C3.672 683.6 0 674.7 0 664.056V446.184c0-10.605 3.672-19.686 11.016-27.231 c7.344-7.548 16.32-11.322 26.928-11.322h135.864c8.568 0 16.2 2.4 22.9 7.344c6.732 4.9 11.3 11.4 13.8 19.6 c6.936 20.8 19.1 37.6 36.4 50.49c17.34 12.9 37 19.3 59.1 19.278s41.82-6.426 59.364-19.278 c17.544-12.852 29.784-29.682 36.72-50.49c2.448-8.157 7.038-14.688 13.77-19.584c6.732-4.896 14.181-7.344 22.341-7.344 L574.056 407.628L574.056 407.628z"/></svg>';
}
function svg_edit() {
  return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="77.999px" height="78px" viewBox="0 0 77.999 78" xml:space="preserve"><g><path d="M71.807 6.191c-7.215-7.216-12.629-6.133-12.629-6.133l-25.26 25.259L5.049 54.185L0 78l23.812-5.051l28.869-28.869 l25.26-25.257C77.941 18.8 79 13.4 71.8 6.191z M22.395 70.086l-8.117 1.748c-0.785-1.467-1.727-2.932-3.455-4.659 c-1.727-1.727-3.193-2.669-4.658-3.456l1.75-8.116l2.346-2.348c0 0 4.4 0.1 9.4 5.078c4.988 5 5.1 9.4 5.1 9.4 L22.395 70.086z"/></g></svg>';
}
function svg_github() {
  return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="512px" height="512px" viewBox="0 0 512 512" enable-background="new 0 0 512 512" xml:space="preserve"><path d="M256 0C114.615 0 0 114.6 0 256s114.615 256 256 256s256-114.615 256-256S397.385 0 256 0z M408.028 408 c-19.76 19.758-42.756 35.266-68.354 46.093c-6.503 2.75-13.106 5.164-19.8 7.246V423c0-20.167-6.917-35-20.75-44.5 c8.667-0.833 16.625-2 23.875-3.5s14.917-3.667 23-6.5s15.333-6.208 21.75-10.125s12.583-9 18.5-15.25s10.875-13.333 14.875-21.25 s7.167-17.417 9.5-28.5s3.5-23.292 3.5-36.625c0-25.833-8.417-47.833-25.25-66c7.667-20 6.833-41.75-2.5-65.25l-6.25-0.75 c-4.333-0.5-12.125 1.333-23.375 5.5s-23.875 11-37.875 20.5c-19.833-5.5-40.417-8.25-61.75-8.25c-21.5 0-42 2.75-61.5 8.2 c-8.833-6-17.208-10.958-25.125-14.875s-14.25-6.583-19-8s-9.167-2.292-13.25-2.625s-6.708-0.417-7.875-0.25s-2 0.333-2.5 0.5 c-9.333 23.667-10.167 45.417-2.5 65.25c-16.833 18.167-25.25 40.167-25.25 66c0 13.3 1.2 25.5 3.5 36.6 s5.5 20.6 9.5 28.5s8.958 15 14.9 21.25s12.083 11.3 18.5 15.25s13.667 7.3 21.8 10.125s15.75 5 23 6.5 s15.208 2.7 23.9 3.5c-13.667 9.333-20.5 24.167-20.5 44.5v39.115c-7.549-2.247-14.99-4.902-22.3-7.994 c-25.597-10.827-48.594-26.335-68.353-46.093c-19.758-19.759-35.267-42.757-46.093-68.354C46.679 313.2 41 285 41 256 s5.679-57.195 16.879-83.675c10.827-25.597 26.335-48.594 46.093-68.353c19.758-19.759 42.756-35.267 68.353-46.093 C198.805 46.7 227 41 256 41s57.195 5.7 83.7 16.879c25.599 10.8 48.6 26.3 68.4 46.1 c19.758 19.8 35.3 42.8 46.1 68.353C465.321 198.8 471 227 471 256s-5.679 57.195-16.879 83.7 C443.294 365.3 427.8 388.3 408 408.028z"/></svg>';
}
function svg_cms() {
  return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="593.851px" height="593.582px" viewBox="12.806 102.512 593.851 593.582" enable-background="new 12.806 102.512 593.851 593.582" xml:space="preserve"><path d="M578.869 426.113h-21.53c-5.826 0-12.619-5.272-14.835-11.515c-2.053-5.786-4.464-11.545-7.16-17.121 c-2.87-5.93-1.814-14.418 2.305-18.537l15.327-15.328c10.737-10.737 10.737-28.21 0-38.948l-24.878-24.877 c-5.202-5.202-12.117-8.066-19.474-8.066s-14.271 2.864-19.474 8.066l-15.328 15.328c-2.46 2.46-6.558 3.929-10.957 3.9 c-2.754 0-5.374-0.563-7.574-1.628c-5.578-2.699-11.346-5.113-17.145-7.176c-6.239-2.215-11.512-9.008-11.512-14.832v-21.533 c0-15.184-12.354-27.54-27.54-27.54h-35.19c-15.187 0-27.54 12.356-27.54 27.54v21.533c0 5.826-5.272 12.62-11.512 14.8 c-5.792 2.057-11.558 4.465-17.13 7.038c-2.163 1.047-4.847 1.622-7.558 1.622c-4.416 0-8.525-1.472-10.992-3.938l-15.303-15.297 c-5.202-5.202-12.118-8.066-19.477-8.066c-7.356 0-14.272 2.864-19.474 8.063l-24.902 24.9 c-5.205 5.199-8.069 12.115-8.072 19.471c0 7.3 2.8 14.4 8 19.584l15.328 15.327c4.122 4 5.2 12.5 2.4 18.6 c-2.69 5.565-5.098 11.321-7.151 17.111c-2.215 6.239-9.005 11.512-14.832 11.628h-21.53c-15.187 0-27.54 12.356-27.54 27.5 v35.189c0 15.3 12.2 27.5 27.5 27.54h21.53c5.826 0 12.5 5.2 14.7 11.509c2.05 5.8 4.6 11.6 7 17.1 c2.867 5.8 1.8 14.426-2.32 18.55l-15.327 15.328c-10.738 10.737-10.738 28.2 0 38.947l24.911 24.9 c5.202 5.2 12.2 8 19.6 7.956c7.356 0 14.272-2.864 19.474-8.066l15.328-15.327c2.466-2.467 6.573-3.938 10.985-3.938 c2.751 0 5.5 0.6 7.6 1.529c5.575 2.8 11.3 5.2 17.1 7.158c6.239 2.1 11.6 8.9 11.6 14.835v21.533 c0 15.3 12.2 27.5 27.5 27.539h35.189c15.188 0 27.54-12.355 27.54-27.539v-21.533c0-5.827 5.272-12.62 11.512-14.832 c5.793-2.057 11.558-4.465 17.13-7.157c2.164-1.047 4.848-1.622 7.559-1.622c4.415 0 8.6 1.5 11 3.938l15.328 15.3 c5.201 5.2 12.2 8 19.6 8.066c7.359 0 14.274-2.867 19.477-8.072l24.887-24.902c10.731-10.74 10.731-28.213-0.003-38.951 l-15.315-15.321c-4.118-4.118-5.174-12.613-2.307-18.547c2.692-5.569 5.098-11.331 7.154-17.12 c2.212-6.239 9.002-11.509 14.828-11.509h21.53c15.187 0 27.54-12.356 27.54-27.54v-35.19 c-0.248-15.055-12.711-27.601-27.705-27.476H578.869z M462.207 471.248c0 44.502-36.206 80.708-80.708 80.7 c-44.501 0-80.707-36.206-80.707-80.708c0-44.501 36.206-80.707 80.707-80.707c44.502 0 80.7 36.1 80.6 80.707H462.207z"/><path d="M214.634 249.423c0.521-1.466 2.262-2.702 3.082-2.702H228.5c10.123 0 18.36-8.234 18.36-18.36v-17.595 c0-10.125-8.237-18.36-18.36-18.36h-10.781c-0.82 0-2.561-1.236-3.081-2.702c-1.083-3.051-2.353-6.089-3.776-9.024 c-0.664-1.374-0.312-3.446 0.263-4.021l7.666-7.665c7.157-7.161 7.157-18.807 0-25.964l-12.439-12.439 c-3.467-3.467-8.079-5.376-12.983-5.376c-4.905 0-9.517 1.913-12.984 5.379l-7.662 7.65c-0.239 0.238-1.059 0.621-2.237 0.6 c-0.67 0-1.319-0.128-1.784-0.355c-2.94-1.423-5.976-2.693-9.024-3.773c-1.469-0.521-2.705-2.265-2.705-3.082v-10.784 c0-10.125-8.237-18.36-18.36-18.36h-17.595c-10.123 0-18.36 8.235-18.36 18.36v10.784c0 0.82-1.236 2.558-2.702 3.1 c-3.063 1.086-6.102 2.359-9.03 3.776c-0.462 0.224-1.114 0.352-1.784 0.352c-1.178 0-1.995-0.379-2.233-0.618l-7.666-7.662 c-3.467-3.467-8.078-5.376-12.983-5.376s-9.514 1.909-12.98 5.376l-12.439 12.439c-7.157 7.16-7.157 18.7 0 25.967l7.662 7.7 c0.575 0.6 0.9 2.8 0.3 4.018c-1.423 2.938-2.693 5.976-3.776 9.027c-0.52 1.466-2.264 2.705-3.081 2.705H31.166 c-10.123 0-18.36 8.234-18.36 18.36v17.595c0 10.1 8.3 18.4 18.4 18.36H41.95c0.82 0 2.4 1.2 3.1 2.7 c1.083 3.1 2.4 6.1 3.7 8.874c0.664 1.2 0.3 3.446-0.263 4.021l-7.666 7.665c-3.467 3.467-5.376 8.079-5.376 13 c0 4.9 1.8 9.5 5.5 12.852l12.439 12.436c3.467 3.4 8 5.5 12.9 5.376s9.513-1.91 12.98-5.376l7.665-7.665 c0.239-0.239 1.059-0.622 2.237-0.622c0.67 0 1.2 0 1.8 0.355c2.941 1.5 6.1 2.8 8.9 3.8 c1.469 0.6 2.8 2.1 2.8 3.082v10.784c0 10.1 8.3 18.4 18.4 18.36h17.595c10.123 0 18.36-8.234 18.36-18.36v-10.781 c0-0.82 1.236-2.558 2.702-3.078c3.051-1.083 6.086-2.353 9.024-3.776c0.465-0.226 1.117-0.354 1.787-0.354 c1.178 0 2.1 0.3 2.1 0.621l7.666 7.665c3.467 3.4 8 5.5 12.9 5.508c4.905 0 9.514-1.91 12.981-5.376 l12.438-12.439c7.158-7.161 7.158-18.807 0-25.964l-7.662-7.662c-0.575-0.575-0.93-2.65-0.266-4.021 c1.784-2.721 3.087-5.781 4.005-8.844H214.634z M165.577 219.563c0 19.719-16.043 35.765-35.762 35.8 c-19.722 0-35.765-16.043-35.765-35.765s16.043-35.765 35.765-35.765c19.719 0.1 35.7 16 35.7 35.729H165.577z"/></svg>';
}
function svg_book() {
  return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="70px" height="96.737px" viewBox="0 0 70 96.737" xml:space="preserve" ><path d="M68.244 24.082L23.25 0.89C17.088-2.357 4.9 3.9 1.4 9.436c-1.559 2.466-1.443 4.249-1.443 5.256l0.555 52.4 c0.037 1.1 1.4 2.6 2.6 3.348c2.496 1.5 40.3 25.1 41.4 25.799c0.576 0.4 1.3 0.5 1.9 0.5 c0.572 0 1.146-0.127 1.666-0.385C49.293 95.8 50 94.7 50 93.532V38.523c0-1.145-0.668-2.203-1.756-2.775L7.348 12.9 c0.463-0.899 2.283-2.801 5.627-4.552c3.523-1.845 6.162-1.148 6.768-0.914c0 0 39.2 21 40.4 21.6 c1.195 0.6 1.2 0.7 1.2 1.786c0 1.1 0 52.2 0 52.2c0 2.6 2.6 3.7 4.6 3.67c1.938 0 4.006-1.9 4.006-3.67V26.858 C70 25.7 69.3 24.7 68.2 24.082z"/></svg>';
}
function svg_logout($class = '') {
  return '<svg class="' . $class . '" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="512px" version="1.1" viewBox="0 0 512 512" width="512px" xml:space="preserve"><path d="M256 33C132.3 33 32 133.3 32 257c0 123.7 100.3 224 224 224c123.7 0 224-100.3 224-224C480 133.3 379.7 33 256 33z M364.3 332.5c1.5 1.5 2.3 3.5 2.3 5.6c0 2.1-0.8 4.2-2.3 5.6l-21.6 21.7c-1.6 1.6-3.6 2.3-5.6 2.3c-2 0-4.1-0.8-5.6-2.3L256 289.8 l-75.4 75.7c-1.5 1.6-3.6 2.3-5.6 2.3c-2 0-4.1-0.8-5.6-2.3l-21.6-21.7c-1.5-1.5-2.3-3.5-2.3-5.6c0-2.1 0.8-4.2 2.3-5.6l75.7-76 l-75.9-75c-3.1-3.1-3.1-8.2 0-11.3l21.6-21.7c1.5-1.5 3.5-2.3 5.6-2.3c2.1 0 4.1 0.8 5.6 2.3l75.7 74.7l75.7-74.7 c1.5-1.5 3.5-2.3 5.6-2.3c2.1 0 4.1 0.8 5.6 2.3l21.6 21.7c3.1 3.1 3.1 8.2 0 11.3l-75.9 75L364.3 332.5z"/></svg>';
}
function svg_tag() {
  return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M17.6 5.8C17.3 5.3 16.7 5 16 5L5 5C3.9 5 3 5.9 3 7v10c0 1.1 0.9 2 2 2l11 0c0.7 0 1.3-0.3 1.6-0.8L22 12L17.6 5.8z"/></g></svg>';
}
function svg_author() {
  return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="100px" height="100px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve"><path d="M59.287 43.191c4.765-3.063 7.93-8.398 7.93-14.482c0-9.508-7.709-17.217-17.215-17.217c-9.51 0-17.217 7.709-17.217 17.2 c0 6.1 3.2 11.4 7.9 14.482c-11.228 1.005-20.061 10.463-20.061 21.949V83.01l0.045 0.279l1.232 0.4 c11.598 3.6 21.7 4.8 30 4.834c16.199 0 25.59-4.621 26.172-4.914l1.15-0.584h0.121V65.141 C79.346 53.7 70.5 44.2 59.3 43.191z"/></svg>';
}
function svg_series() {
  return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="26px" height="26px" viewBox="0 0 26 26" enable-background="new 0 0 26 26" xml:space="preserve"><path d="M8.984 11.157v9.558c0 0.425-0.268 0.803-0.668 0.943c-0.566 0.199-1.249 0.304-1.976 0.304c0 0 0 0 0 0 C4.249 22 2 21.1 2 19.22V6.99C1.953 6.2 2.2 4.7 3.7 3.863C4.306 3.5 7.7 1.3 9.5 0.2 c0.307-0.199 0.699-0.213 1.021-0.039C10.8 0.3 11 0.6 11 1.011v1.448c0 0.552-0.448 1-1 1c-0.43 0-0.797-0.271-0.938-0.653 c-1.556 1-3.848 2.468-4.417 2.793C4.133 5.9 4 6.4 4 6.722C4 7 4.1 7.3 4.2 7.4 C4.554 7.8 5.7 7.6 7 6.82c1.24-0.744 7.356-4.816 7.417-4.857c0.309-0.205 0.703-0.224 1.026-0.049 C15.797 2.1 16 2.4 16 2.796V2.91c0 0.334-0.167 0.646-0.444 0.832c0 0-4.246 2.834-4.622 3.1 C9.495 7.7 9 8.9 9 11.157z M24 6.331v12.982c0 0.343-0.177 0.663-0.468 0.846c0 0-5.83 4.528-7.013 5.2 c-0.622 0.379-1.414 0.579-2.289 0.579c-2.079 0-4.23-1.125-4.23-3.006V10.863V10.57c0-0.004 0.003-0.008 0.003-0.013 c0.02-0.732 0.191-1.777 1.552-2.781c0.816-0.602 5.676-3.916 5.882-4.056c0.307-0.208 0.704-0.23 1.03-0.058 C18.795 3.8 19 4.2 19 4.546v1.448c0 0.552-0.447 1-1 1c-0.413 0-0.768-0.25-0.92-0.608c-1.551 1.061-3.828 2.624-4.338 3 c-0.644 0.476-0.73 0.79-0.741 1.193c0.002 0.3 0.1 0.5 0.2 0.677c0.51 0.5 1.9 0.3 3.237-0.541 c1.002-0.602 5.32-3.936 6.898-5.17c0.303-0.234 0.711-0.277 1.055-0.11S24 5.9 24 6.331z M22 10.787l-5 3.879v2l5-3.879V10.787z"/></svg>';
}
function svg_login_key() {
  return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="516.375px" height="516.375px" viewBox="0 0 516.375 516.375" xml:space="preserve"><g><path d="M353.812 0C263.925 0 191.2 72.7 191.2 162.562c0 19.1 3.8 38.2 9.6 57.375L0 420.75v95.625h95.625V459H153 v-57.375h57.375l86.062-86.062c17.213 5.7 36.3 9.6 57.4 9.562c89.888 0 162.562-72.675 162.562-162.562S443.7 0 353.8 0 z M401.625 172.125c-32.513 0-57.375-24.862-57.375-57.375s24.862-57.375 57.375-57.375S459 82.2 459 114.8 S434.138 172.1 401.6 172.125z"/></g></svg>';
}
function svg_trash() {
  return '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" x="0" y="0" width="900.5" height="900.5" viewBox="0 0 900.5 900.5" xml:space="preserve" enable-background="new 0 0 900.5 900.5"><path d="M176.4 880.5c0 11 9 20 20 20h507.7c11 0 20-9 20-20V232.5h-547.7V880.5L176.4 880.5zM562.8 342.8h75v436h-75V342.8zM412.8 342.8h75v436h-75V342.8zM262.8 342.8h75v436h-75V342.8zM618.8 91.9V20c0-11-9-20-20-20h-297.1c-11 0-20 9-20 20v71.9 12.5 12.5H141.9c-11 0-20 9-20 20v50.6c0 11 9 20 20 20h34.5 547.7 34.5c11 0 20-9 20-20v-50.6c0-11-9-20-20-20H618.8v-12.5V91.9zM543.8 112.8h-187.1v-8.4 -12.5V75h187.2v16.9 12.5V112.8z"/></svg>';
}
/* SVG CODE STOP */
