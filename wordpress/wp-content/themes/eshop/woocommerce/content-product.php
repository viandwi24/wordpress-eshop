<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
// dd($product->get_id());
?>
<div class="">
	<a href="<?= $product->get_permalink(); ?>" class="eshop__card product" title="<?= $product->get_title() ?>">
		<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );?>
		<!-- <img class="image" src="{{ product_images($product->images[0]['path']) }}" alt="Product Image"> -->
		<img class="image" src="<?php  echo $image[0]; ?>" alt="Product Image">
		<div class="detail">
			<div class="title">
				<?= $post->post_title ?>
			</div>
			<div class="price">
				<?= money($product->get_price()) ?>
			</div>
			<div class="rating">
				<?php
				$rate = $product->get_average_rating();
				?>
				<?php for($i = 0; $i < 5; $i++): ?>
					<?php if($i < floor($rate)): ?>
						<i class="fa fa-star icon text-yellow-500"></i>
					<?php else: ?>
						<i class="fa fa-star icon text-yellow-300"></i>
					<?php endif; ?>
				<?php endfor; ?>
				<span class="text">
					<?= ($rate > 0) ? $rate : '' ?>
				</span>
			</div>
		</div>
	</a>
</div>

