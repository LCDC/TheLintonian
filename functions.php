<?php
include 'inc/class-lcdc-lintoncinema.php';
include 'inc/amp-functions.php';

if ( ! function_exists( 'wp_in' ) ) :
	function wp_in( $needle, $haystack ) {
		return false !== strpos( $haystack, $needle );
	}
endif;

function thelintonian_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'bootstrap-grid', get_stylesheet_directory_uri() . '/bootstrap-grid.min.css' );
}
add_action( 'wp_enqueue_scripts', 'thelintonian_enqueue_styles' );

// Stop Photon from making the logo look funky.
function thelintonian_photon_exceptions( $val, $src, $tag ) {
	if ( wp_in( $src, 'lintonian-logo' ) ) {
		return true;
	}
	return $val;
}
add_filter( 'jetpack_photon_skip_image', 'thelintonian_photon_exceptions', 10, 3 );

function thelintonian_insert_featured_image( $content ) {
	return get_the_post_thumbnail( get_the_ID(), 'lintonian-large' ) . $content;
}
add_filter( 'the_content', 'thelintonian_insert_featured_image' );

function thelintonian_setup() {
	add_image_size( 'lintonian-large', 1000, 9999, false );
}
add_action( 'after_setup_theme', 'thelintonian_setup' );

function thelintonian_upload_mime_types( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'thelintonian_upload_mime_types' );

function thelintonian_serve_mime_types( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'mime_types', 'thelintonian_serve_mime_types' );

function thelintonian_adsense_code() {
	?>
	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<?php
}
add_action( 'wp_head', 'thelintonian_adsense_code' );
