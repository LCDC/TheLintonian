	<div id="secondary" class="widget-area" role="complementary">
		<ins class="adsbygoogle" style="background: none; display: block; margin-bottom: 24px; " data-ad-client="ca-pub-7053032879022167" data-ad-slot="9169543177" data-ad-format="auto"></ins>
		<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>

		<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<div class="sidebar-primary">
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
		</div>
		<?php endif; ?>

		<?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
		<div class="sidebar-secondary">
			<?php dynamic_sidebar( 'sidebar-2' ); ?>
		</div>
		<?php endif; ?>

		<?php if ( is_active_sidebar( 'sidebar-3' ) ) : ?>
		<div class="sidebar-tertiary">
			<?php dynamic_sidebar( 'sidebar-3' ); ?>
		</div>
		<?php endif; ?>

	</div><!-- #secondary -->
