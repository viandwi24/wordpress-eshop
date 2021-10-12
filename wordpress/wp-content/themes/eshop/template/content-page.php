<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package eshop
 */

?>
<div class="eshop-container">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php
		$register = [is_cart(), is_checkout(), is_account_page()];
		$allow = true;
		foreach ($register as $item) { if (!$item) {$allow = false;} }
		if ($allow) {
			the_title('<h1>', '</h1>');
		}
		?>
		<?php the_content(); ?>
	</article><!-- #post-## -->
</div>
