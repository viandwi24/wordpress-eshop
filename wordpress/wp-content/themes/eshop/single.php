<?php get_header(); ?>
	<main class="my-4">
		<?php
		while ( have_posts() ) :
			the_post();
			do_action('eshop_single_post_before');
			get_template_part('template/content', 'post-single');
			do_action('eshop_single_post_after');
		endwhile; // End of the loop.
		?>
	</main>
<?php get_footer(); ?>