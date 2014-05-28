<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

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
    <!-- or, set /favicon.ico for IE10 win -->
    <meta name="msapplication-TileColor" content="#f01d4f">
    <meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">

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
    <div id="loading-container"><div class="loading-spinner"></div></div>
    <div class="container">
      <header id="site-header" class="content">
      
        <?php if ( ! is_user_logged_in() ) { ?>
          <form name="loginform" id="loginform" action="<?php bloginfo('wpurl');?>/wp-login.php" method="post">
            <div>
              <label for="user_login">Username</label>
              <input type="text" name="log" id="user_login">
            </div>
            <div>
              <label for="user_pass">Password</label>
              <input type="password" name="pwd" id="user_pass">
            </div>
            <div>
              <input type="submit" name="wp-submit" id="wp-submit" value="Login">
              <input type="hidden" name="redirect_to" value="<?php echo home_url(); ?>">
            </div>
          </form>
        <?php } ?>
      <?php echo $_SERVER["REMOTE_ADDR"]; ?>
      
        <h1><?php bloginfo('title'); ?></h1>
      </header>

      <main id="site-content" class="content">
