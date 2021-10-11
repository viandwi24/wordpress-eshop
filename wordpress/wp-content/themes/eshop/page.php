<?php get_header(); ?>
<main class="my-4">
	<?php
	while ( have_posts() ) :
		the_post();
		do_action( 'eshop_page_before' );
		get_template_part( 'template/content', 'page' );
		/**
		 * Functions hooked in to eshop_page_after action
		 *
		 * @hooked storefront_display_comments - 10
		 */
		do_action( 'eshop_page_after' );
	endwhile; // End of the loop.
	?>
</main>
<?php get_footer(); ?>