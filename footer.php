<?php
	/**
	 * @package Apostrophe
	 *
	 * The template for displaying the footer.
	 *
	 * Contains the closing of the #content div and all content after
	 */
?>

			</div><!-- #content -->

			<footer id="colophon" class="site-footer" role="contentinfo">

				<?php if ( is_active_sidebar( 'footer-sidebar' ) ) : ?>
				<div class="widget-area">
					<div id="footer-sidebar">
						<?php dynamic_sidebar( 'footer-sidebar' ); ?>
					</div>
				</div>
				<?php endif; ?>

				<div class="site-info">
					&copy; <?php echo esc_html( date( 'Y' ) ); ?> <a href="https://lintoncdc.org/">Linton Community Development Corporation</a> | <a href="https://thelintonian.com/about-us/">About</a> | <a href="https://thelintonian.com/privacy-policy/">Privacy Policy</a>
				</div><!-- .site-info -->
			</footer><!-- #colophon -->
		</div><!-- #page -->

		<?php wp_footer(); ?>

		<script>
			( adsbygoogle = window.adsbygoogle || [] ).push( {
				google_ad_client: "ca-pub-7053032879022167",
				enable_page_level_ads: true
			} );
		</script>
	</body>
</html>
