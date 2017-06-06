<?php

add_action( 'pre_amp_render_post', 'jetpack_amp_disable_the_content_filters' );

function jetpack_amp_disable_the_content_filters( $post_id ) {
	remove_filter( 'the_content', 'lintonian_insert_featured_image' );
	return $post_id;
}


function thelintonian_amp_ad_footer( $amp_template ) {
	?>
	<amp-ad layout="fixed-height" height=100 type="adsense" data-ad-client="ca-pub-7053032879022167" data-ad-slot="9169543177"></amp-ad>
	<?php
}
add_action( 'amp_post_template_footer', 'thelintonian_amp_ad_footer', 10, 1 );

function thelintonian_amp_ad_header( $amp_template ) {
	?>
	<amp-ad layout="fixed-height" height=100 type="adsense" data-ad-client="ca-pub-7053032879022167" data-ad-slot="3321485973"></amp-ad>
	<?php
}
add_action( 'amp_post_template_head', 'thelintonian_amp_ad_header', 10, 1 );

function thelintonian_amp_component_scripts( $data ) {
	$custom_component_scripts = array(
		'amp-ad' => 'https://cdn.ampproject.org/v0/amp-ad-0.1.js',
	);
	$data['amp_component_scripts'] = array_merge( $data['amp_component_scripts'], $custom_component_scripts );

	return $data;
}
add_filter( 'amp_post_template_data', 'thelintonian_amp_component_scripts', 10, 1 );

// We don't want the featured image in AMP pages anymore.  It's added automatically.
/*
add_action( 'pre_amp_render_post', 'thelintonian_amp_add_custom_actions' );
function thelintonian_amp_add_custom_actions() {
	add_filter( 'the_content', 'thelintonian_amp_add_featured_image' );
}

function thelintonian_amp_add_featured_image( $content ) {
	if ( has_post_thumbnail() ) {
		// Just add the raw <img /> tag; our sanitizer will take care of it later.
		$image = sprintf( '<p class="thelintonian-featured-image">%s</p>', get_the_post_thumbnail() );
		$content = $image . $content;
	}
	return $content;
}
*/

add_filter( 'amp_post_template_metadata', 'thelintonian_amp_modify_json_metadata', 10, 2 );

function thelintonian_amp_modify_json_metadata( $metadata, $post ) {
	$logo = site_logo()->logo;
	$metadata['@type'] = 'NewsArticle';

	// Switched to SVG logo, need to manually fix these
	$logo['sizes']['full']['width'] = 300;
	$logo['sizes']['full']['height'] = 84;

	$metadata['publisher']['logo'] = array(
		'@type' => 'ImageObject',
		// Switched to SVG logo, need to manually fix these
		'url' => add_query_arg( 'h', 60, jetpack_photon_url( 'http://thelintonian.com/files/2015/06/lintonian-logo1.png' ) ),
		'height' => absint( ( 60 / $logo['sizes']['full']['height'] ) * $logo['sizes']['full']['height'] ),
		'width' => absint( ( 60 / $logo['sizes']['full']['height'] ) * $logo['sizes']['full']['height'] ),
	);

	if ( ! isset( $metadata['image'] ) ) {
		$metadata['image'] = array(
			'@type' => 'ImageObject',
			'url' => add_query_arg( 'h', 200, jetpack_photon_url( 'http://thelintonian.com/files/2015/06/lintonian-logo1.png' ) ),
			'width' => 714,
			'height' => 200,
		);
	}
	return $metadata;
}
