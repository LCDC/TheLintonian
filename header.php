<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php esc_url( get_bloginfo( 'pingback_url' ) ); ?>">
		<?php wp_head(); ?>
	</head>

	<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
	<body <?php body_class(); ?>>
		<div id="pre-page">
			<ins class="adsbygoogle" style="background: none; display: block;" data-ad-client="ca-pub-7053032879022167" data-ad-slot="3321485973" data-ad-format="auto"></ins>
		</div>
		<div id="page" class="hfeed site">

			<header id="masthead" class="site-header" role="banner">
				<div class="site-branding">
					<?php
					if ( function_exists( 'jetpack_the_site_logo' ) ) :
						jetpack_the_site_logo();
					endif;
					?>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
					<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
				</div>

				<nav id="site-navigation" class="main-navigation" role="navigation">
					<a class="menu-toggle"><?php esc_html_e( 'Menu', 'apostrophe' ); ?></a>
					<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'apostrophe' ); ?></a>

					<?php wp_nav_menu( array(
						'theme_location' => 'primary',
						'menu_class'     => 'apostrophe-navigation',
					) ); ?>

					<?php wp_nav_menu( array(
						'theme_location' => 'social',
						'menu_class'     => 'apostrophe-social',
						'link_before'    => '<span>',
						'link_after'     => '</span>',
						'fallback_cb'    => '',
						'depth'          => 1,
					) ); ?>

				</nav><!-- #site-navigation -->
			</header><!-- #masthead -->

			<div id="content" class="site-content">
