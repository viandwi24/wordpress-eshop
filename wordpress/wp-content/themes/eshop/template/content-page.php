<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package eshop
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	the_title('<h1>', '</h1>');
	?>
    <?php the_content(); ?>
</article><!-- #post-## -->
