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
                <?php if (has_post_thumbnail()): ?>
                    <div class="thumbnail-container">
                        <?= the_post_thumbnail() ?>
                    </div>
                <?php endif; ?>
                <?php the_title('<h1>', '</h1>'); ?>
                <?php the_date('d/m/Y', '<div class="date">', '</div>'); ?>
                <div class="content">
                    <?php the_content(); ?>
                </div>
                <?php comments_template() ?>
            </div>
        </div>
    </article>
<?php } ?>