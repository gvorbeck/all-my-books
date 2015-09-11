<!doctype html>
<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--><html <?php language_attributes(); ?>><!--<![endif]-->
  <head>
    <meta charset="utf-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>
      <?php
      if ( is_home() ) {
        bloginfo( 'name' );
      } else {
        wp_title('');
        echo ' | ';
        bloginfo( 'name' );
      }
      ?>
    </title>
    <script>
      var templateDirectory = '<?php bloginfo( 'template_directory' ); ?>';
    </script>
    <!-- icons & favicons --> <?php //(for more: http://www.jonathantneal.com/blog/understand-the-favicon/) ?>
    <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-icon-touch.png">
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
    <!--[if IE]>
      <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
    <![endif]-->
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <!-- wordpress head functions -->
    <?php wp_head(); ?>
    <!-- end of wordpress head -->
  </head>
  <body <?php body_class(); ?>>
    <div class="lightbox lightbox--shade">
      <a title="Close" href="javascript:;" class="lightbox--close"><?php echo svg_logout(); ?></a>
      <div class="lightbox--content"></div>
    </div>
    <header id="site-header">
      <div class="header-content">
        <?php
        if (!is_user_logged_in()) {
          $args = array(
            'redirect' => home_url(),
          );
          wp_login_form();
        }
        ?>
        <form method="post" action="/the/post/url" name="add-book-form" id="add-book-form" class="site-form add-a-book">
          <fieldset>
            <legend>add a book</legend>
            <div>
              <label class="required">title
                <input id="add-book-form--title" name="add-book-form--title" type="text" placeholder="Lonesome Dove" required autofocus>
              </label>
            </div>
            <div>
              <label class="required">author
                <input id="add-book-form--author" name="add-book-form--author" type="text" placeholder="Harry Turtledove" required>
              </label>
            </div>
            <div> 
              <a title="submit" href="javascript:;" class="add-book-form--button button">submit</a> 
            </div> 
          </fieldset>
        </form>
        <a href="javascript:;" title="<?php echo is_user_logged_in() ? 'Add a book' : 'Sign in'; ?>" class="site-logo--action <?php echo is_user_logged_in() ? 'action--add-book' : 'action--login'; ?>">
          <img class="site-logo--img" src="<?php echo get_template_directory_uri(); ?>/images/amb_logo_new.png">
          <img class="site-logo--img-big" src="<?php echo get_template_directory_uri(); ?>/images/amb_logo_new_big.png">
          <?php echo is_user_logged_in() ? '<span>+</span>' : '<span>></span>'; ?>
        </a>
        <h1>
          <?php
          $title = explode(' ', get_bloginfo('title'));
          $title_last = end(explode(' ', get_bloginfo('title')));
          foreach (array_keys($title, $title_last) as $key) {
            unset($title[$key]);
          }
          $title = implode(' ', $title);
          echo "<span class='first'>$title</span> <span class='last'>$title_last</span>";
          ?>
        </h1>
      </div>
    </header>
    <main id="site-content">
