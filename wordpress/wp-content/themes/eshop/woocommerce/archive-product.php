<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;
get_header( 'shop' );
?>
<section>
	<div class="eshop-container mb-6">
		<div>
			<?php
			/**
			 * Hook: woocommerce_before_main_content.
			 *
			 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
			 * @hooked woocommerce_breadcrumb - 20
			 * @hooked WC_Structured_Data::generate_website_data() - 30
			 */
			do_action( 'woocommerce_before_main_content' );

			?>
			<header class="woocommerce-products-header">
				<?php
				/**
				 * Hook: woocommerce_archive_description.
				 *
				 * @hooked woocommerce_taxonomy_archive_description - 10
				 * @hooked woocommerce_product_archive_description - 10
				 */
				do_action( 'woocommerce_archive_description' );
				?>
			</header>
		</div>
		<div class="flex flex-col md:flex-row md:space-x-12">
			<div class="w-full md:w-1/4 accordion-mobile">
				<!-- <div class="font-semibold text-2xl text-black">
					Filter
				</div> -->
				<div class="title">
					Filter
					<span class="float-right">
						<i class="icon fa fa-chevron-down"></i>
					</span>
				</div>
				<div class="content">
					<?= apply_shortcodes('[searchandfilter id="filter"]'); ?>
				</div>
			</div>
			<div class="w-full md:w-3/4">
				<?php
				if ( woocommerce_product_loop() ) {

					/**
					 * Hook: woocommerce_before_shop_loop.
					 *
					 * @hooked woocommerce_output_all_notices - 10
					 * @hooked woocommerce_result_count - 20
					 * @hooked woocommerce_catalog_ordering - 30
					 */
					do_action( 'woocommerce_before_shop_loop' );

					woocommerce_product_loop_start();

					if ( wc_get_loop_prop( 'total' ) ) { ?>
						<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
							<?php while ( have_posts() ) {
								the_post();

								/**
								 * Hook: woocommerce_shop_loop.
								 */
								do_action( 'woocommerce_shop_loop' );
								wc_get_template_part( 'content', 'product' );
							}
							?>
						</div>
					<?php }

					woocommerce_product_loop_end();

					/**
					 * Hook: woocommerce_after_shop_loop.
					 *
					 * @hooked woocommerce_pagination - 10
					 */
					do_action( 'woocommerce_after_shop_loop' );
				} else {
					/**
					 * Hook: woocommerce_no_products_found.
					 *
					 * @hooked wc_no_products_found - 10
					 */
					do_action( 'woocommerce_no_products_found' );
				}

				/**
				 * Hook: woocommerce_after_main_content.
				 *
				 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
				 */
				do_action( 'woocommerce_after_main_content' );

				/**
				 * Hook: woocommerce_sidebar.
				 *
				 * @hooked woocommerce_get_sidebar - 10
				 */
				do_action( 'woocommerce_sidebar' );
				?>
			</div>
		</div>
	</div>
</section>

<script>
	const accordion = document.querySelector('.accordion-mobile')
	accordion.querySelector('.title').addEventListener('click', () => {
		const state = accordion.classList.contains('collapsed')
		if (state) {
			accordion.classList.remove('collapsed')
		} else {
			accordion.classList.add('collapsed')
		}
	})
	if (window.innerWidth < 768) {
		accordion.classList.add('collapsed')
	}
</script>

<?php
get_footer( 'shop' );
?>