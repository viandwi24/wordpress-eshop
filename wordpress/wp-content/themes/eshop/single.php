<?php
while ( have_posts() ) :
	the_post();
	if (is_attachment()) {
		$permalink = $post->guid;
		// redirect to permalink
		wp_redirect($permalink, 301);
		die('');
	}
?>
<?php get_header(); ?>
	<main class="my-4">
		<?php
			do_action('eshop_single_post_before');
			get_template_part('template/content', 'post-single');
			do_action('eshop_single_post_after');
		?>
	</main>
<?php
get_footer();
endwhile; // End of the loop.
?>