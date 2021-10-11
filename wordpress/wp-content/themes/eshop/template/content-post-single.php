<?php
/**
 * Template used to display post content on single pages.
 *
 * @package storefront
 */

?>

<?php if (is_product()) { ?>
    <div class="eshop-container">
        <?php the_content(); ?>
    </div>
<?php } else { ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="eshop-container">
            <?php the_title('<h1>', '</h1>'); ?>
            <?php the_content(); ?>
        </div>
    </article>
<?php } ?>