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
    <!-- google fonts -->
    <link href='http://fonts.googleapis.com/css?family=Domine|Sintony' rel='stylesheet' type='text/css'>
    <!-- wordpress head functions -->
    <?php wp_head(); ?>
    <!-- end of wordpress head -->
    <!-- drop Google Analytics Here -->
    <!-- end analytics -->
  </head>
  <body <?php body_class(); ?>>
    <header id="site-header">
      <?php
      if ( ! is_user_logged_in() ) {
        $args = array(
          'redirect' => home_url('/', 'relative'),
        );
        wp_login_form($args);
      }
      else { ?>
        <nav id="site-navigation">
          <ul>
            <li class="site-navigation--item">
              <a class="site-navigation--link" href="<?php echo get_admin_url(); ?>" title="AMB Admin Area" target="_blank"><?php echo svg_cms(); ?></a>
            </li>
            <li class="site-navigation--item">
              <a class="site-navigation--link" href="<?php echo get_admin_url(); ?>post-new.php" title="Add a New Book" target="_blank"><?php echo svg_plus_book(); ?></a>
            </li>
            <li class="site-navigation--item">
              <a class="site-navigation--link" href="<?php echo wp_logout_url( home_url() ); ?>" title="Logout"><?php echo svg_logout(); ?></a>
            </li>
          </ul>
        </nav>
      <?php } ?>
      <h1>
        <?php
        $title = explode( ' ', get_bloginfo('title') );
        $title_last = end( explode( ' ', get_bloginfo('title') ) );
        foreach (array_keys($title, $title_last) as $key) {
          unset($title[$key]);
        }
        $title = implode(' ', $title);
        echo svg_book() . "<!--div class='shape'></div--><span class='first'>$title</span> <span class='last'>$title_last</span>";
        ?>
      </h1>
    </header>
    <main id="site-content">
