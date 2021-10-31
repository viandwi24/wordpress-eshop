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
    <article class="post" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="eshop-container">
            <div class="md:px-12">
                <div class="thumbnail-container">
                    <?php if (has_post_thumbnail()): the_post_thumbnail(); else: ?>
                        <img src="<?= esc_url(home_url('woocommerce-placeholder')) ?>">
                    <?php endif; ?>
                </div>
                <?php the_title('<h1>', '</h1>'); ?>
                <?php the_date('d/m/Y', '<div class="date">', '</div>'); ?>
                <div class="content">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </article>
<?php } ?>