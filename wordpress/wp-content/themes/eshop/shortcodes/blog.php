<?php
// Get blog posts
global $paged;
$curpage = $paged ? $paged : 1;
$args = array(
    'post_type' => 'post',
    'orderby' => 'post_date',
    'posts_per_page' => 1,
    'paged' => $paged
);
$query = new WP_Query($args);
?>

<div class="eshop-blog">
    <div class="flex flex-col md:flex-row md:space-x-10">
        <section class="w-full md:w-3/4 blog">
            <!-- <div class="mb-4">
                <h1 class="text-4xl text-black">
                    Blog
                </h1>
            </div> -->
            <!-- have post -->
            <?php if($query->have_posts()): ?>
                <div class="grid grid-cols-2 gap-4">
                    <?php while ($query->have_posts()): ?>
                        <div id="post-<?php the_ID(); ?>" class="post">
                            <?php
                            $query->the_post();
                            ?>
                            <div class="">
                                <a href="<?php the_permalink(); ?>" class="post-link">
                                    <div class="thumbnail-container">
                                        <?php if (has_post_thumbnail()): the_post_thumbnail(); else: ?>
                                            <img src="<?= esc_url(home_url('woocommerce-placeholder')) ?>">
                                        <?php endif; ?>
                                        <div class="overlay">
                                            <div>Baca</div>
                                        </div>
                                    </div>
                                    <h2 class="title">
                                        <?php the_title(); ?>
                                    </h2>
                                </a>
                                <div class="date">
                                    <?php the_date(); ?>
                                </div>
                            </div>
                        </div>
                        <?php wp_reset_postdata(); ?>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </section>
        <section class="w-full md:w-1/4">
            <div class="font-semibold text-lg mb-1 text-black">
                Kategori
            </div>
            <div>
                <?php
                $categories = get_categories();
                foreach ($categories as $category):
                    ?>
                    <a href="<?php echo get_category_link($category->term_id); ?>" class="category-link">
                        <div class="text-sm text-muted mb-1">
                            <?php echo $category->name; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
    <div class="woocommerce mt-6">
        <nav class="woocommerce-pagination">
            <ul class="page-numbers">
                <?php 
                    $pages = paginate_links( array(
                        'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                        'total'        => $query->max_num_pages,
                        'current'      => max( 1, get_query_var( 'paged' ) ),
                        'format'       => '?paged=%#%',
                        'show_all'     => false,
                        'type'         => 'plain',
                        'end_size'     => 2,
                        'mid_size'     => 1,
                        'prev_next'    => true,
                        'prev_text'    => sprintf( '<i></i> %1$s', __( '←', 'text-domain' ) ),
                        'next_text'    => sprintf( '%1$s <i></i>', __( '→', 'text-domain' ) ),
                        'add_args'     => false,
                        'add_fragment' => '',
                        'type'  => 'array',
                    ));
                    if( is_array( $pages ) ) {
                        $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
                        foreach ( $pages as $page ) {
                            echo "<li>$page</li>";
                        }
                    }
                ?>
            </ul>
        </div>
    </div>
</div>