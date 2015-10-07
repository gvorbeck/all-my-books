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
      <?php echo svg_logout('lightbox--close js-lightbox-toggle'); ?>
      <div class="lightbox--content js-lightbox-content"></div>
    </div>
    <header id="site-header">
      <div>
        <?php get_template_part('header', 'form'); ?>
        <img class="site-logo--img site-logo--img-small" src="<?php echo get_template_directory_uri(); ?>/images/amb_logo_new.png">
        <img class="site-logo--img site-logo--img-big" src="<?php echo get_template_directory_uri(); ?>/images/amb_logo_new_big.png">
        <h1 class="site-logo--text">
          <?php
          $title = explode(' ', get_bloginfo('title'));
          echo '<span>' . $title[0] . ' ' . $title[1] . '</span> <span>' . $title[2] . '</span>';
          ?>
        </h1>
        <a href="javascript:;" title="<?php echo is_user_logged_in() ? 'Add a book' : 'Sign in'; ?>" class="js-lightbox-toggle site-action <?php echo is_user_logged_in() ? 'site-action--add-book' : 'site-action--login'; ?>">
          <?php echo is_user_logged_in() ? '+' : '>'; ?>
        </a>
      </div>
    </header>
    <main id="site-content">
