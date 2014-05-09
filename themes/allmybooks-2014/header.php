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
		<?php
		if ( 'localhost:8888' == SITE_URL ) {
			echo "<div id='dev--window-width' class='dev-env-element'></div>";
		}	
		?>
		<div id="container">
			<div id="loading-container"><div class="loading-spinner"></div></div>
			<header id="site-header">
				<h1><?php bloginfo('title'); ?></h1>
				<div class="navigation--button">*</div>
				<div id="navigation--popup">
					<div class="navigation--popup-arrow"></div>
					<div class="navigation--popup-content">
						<div id="loginout" class="<?php if ( is_user_logged_in() ) { echo 'loginout-out'; } else { echo 'loginout-in'; } ?>">
							<?php
							if ( ! is_user_logged_in() ) {
								wp_login_form();
							} else {
								wp_loginout( get_bloginfo('url') );
							}
							?>
						</div>
						<nav>
							<ul>
								<li><a class="navigation--popup-link" href="<?php echo get_admin_url(); ?>" title="AMB Admin Area" target="_blank"><?php echo file_get_contents( locate_template( "_images/icons/cms.svg" ) ); ?></a></li>
								<li><a class="navigation--popup-link" href="<?php echo get_admin_url(); ?>post-new.php" title="Add a New Book" target="_blank"><?php echo file_get_contents( locate_template( "_images/icons/book.svg" ) ); ?></a></li>
							</ul>
						</nav>
					</div>
				</div>
			</header>

			<main id="content">
