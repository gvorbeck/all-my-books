<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="initial-scale=1.0">

		<!-- Google Chrome Frame for IE -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

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
		<link href='http://fonts.googleapis.com/css?family=Petrona|Sintony' rel='stylesheet' type='text/css'>

		<!-- wordpress head functions -->
		<?php wp_head(); ?>
		<!-- end of wordpress head -->

		<!-- drop Google Analytics Here -->
		<!-- end analytics -->

	</head>

	<body <?php body_class(); ?>>
		<div id="container">
			<div id="loading-indicator"><span>*</span></div>
			<header id="site-header" role="banner">
				<!--div id="site-header--logo">
					<div class="circle"><div class="ribbon"></div></div>
					<div class="book-logo"></div>
				</div-->
				<?php if ( is_home() ) { $tag = h1; } else { $tag = h2; } ?>
				<<?php echo $tag; ?> id="site-title"><a href="<?php bloginfo( 'url' ); ?>" title="Home"><?php bloginfo( 'title' ); ?></a></<?php echo $tag; ?>>
				<div id="header-navigation">
					<span id="header-navigation-button">*</span>
					<div id="header-navigation-popup">
						<div id="header-navigation-arrow"></div>
						<div id="header-navigation-content">
							<div id="loginout" class="<?php if ( is_user_logged_in() ) { echo 'loginout-out'; } else { echo 'loginout-in'; } ?>">
								<?php
								if ( ! is_user_logged_in() ) {
									wp_login_form();
								} else {
									wp_loginout( '/' );
								}
								?>
							</div>
							<nav>
								<ul>
									<li><a href="<?php bloginfo('url'); ?>/wordpress/wp-admin" title="AMB Admin Area" target="_blank"><span class="icon--font icon--settings"></span></a></li>
									<li><a href="<?php bloginfo('url'); ?>/wordpress/wp-admin/post-new.php" title="Add a New Book" target="_blank"><span class="icon--font icon--book"></span></a></li>
								</ul>
							</nav>
						</div>
					</div>
				</div>
			</header> <!-- end header -->

			<main id="content">
