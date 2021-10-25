<?php
$groups = [
	[
		'title' => 'Hot Products',
		'args' => [
			'post_type' => 'product',
			'posts_per_page' => 6,
		]
	],
	[
		'title' => 'News Products',
		'args' => [
			'post_type' => 'product',
			'posts_per_page' => 6,
		]
	],
];

$taxonomy     = 'product_cat';
$orderby      = 'name';
$show_count   = 0;
$pad_counts   = 0;
$hierarchical = 1;
$title        = '';
$empty        = 0;
$args = array(
	'taxonomy'     => $taxonomy,
	'orderby'      => $orderby,
	'show_count'   => $show_count,
	'pad_counts'   => $pad_counts,
	'hierarchical' => $hierarchical,
	'title_li'     => $title,
	'hide_empty'   => $empty
);
$categories = get_categories( $args );
// dd($categories);

// Header
get_header()
?>
<main class="my-4">
	<section>
		<div class="eshop-container mb-6">
			<div id="banner" class="owl-carousel owl-theme">
				<div class="item">
					<img src="<?= site_url('banner-min-1800') ?>" alt="Banner">
				</div>
				<!-- <div class="item">
					<img src="<?= site_url('wall-fans-page-01-2') ?>" alt="Banner">
				</div> -->
			</div>
		</div>
	</section>

	<section class="hidden md:block">
		<div class="eshop-container mb-6">
			<div class="rounded-lg p-6 flex flex-col divide-y-4 md:divide-y-0 md:flex-row md:divide-x-4 divide-red-600 divide-dashed border border-red-300">
				<div class="w-full md:w-1/5 flex py-4 space-x-4 justify-center items-center">
					<i class="fas fa-truck text-5xl self-center text-red-500"></i>
					<div>
						<div class="text-lg font-semibold">Free Delivery</div>
						<div class="text-xs text-muted">From $50</div>
					</div>
				</div>
				<div class="w-full md:w-1/5 flex py-4 space-x-4 justify-center items-center">
					<i class="fas fa-exchange-alt text-5xl self-center text-red-500"></i>
					<div>
						<div class="text-lg font-semibold">99% Positive</div>
						<div class="text-xs text-muted">Feedbacks</div>
					</div>
				</div>
				<div class="w-full md:w-1/5 flex py-4 space-x-4 justify-center items-center">
					<i class="fas fa-exchange-alt text-5xl self-center text-red-500"></i>
					<div>
						<div class="text-lg font-semibold">365 days</div>
						<div class="text-xs text-muted">For free return</div>
					</div>
				</div>
				<div class="w-full md:w-1/5 flex py-4 space-x-4 justify-center items-center">
					<i class="far fa-credit-card text-5xl self-center text-red-500"></i>
					<div>
						<div class="text-lg font-semibold">Payment</div>
						<div class="text-xs text-muted">Secure Payment</div>
					</div>
				</div>
				<div class="w-full md:w-1/5 flex py-4 space-x-4 justify-center items-center">
					<i class="fas fa-tag text-5xl self-center text-red-500"></i>
					<div>
						<div class="text-lg font-semibold">Only Best</div>
						<div class="text-xs text-muted">Brands</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section>
		<div class="eshop-container mb-6">
			<div class="w-full px-4 py-3 rounded bg-gray-50">
				<div class="text-2xl font-bold mb-2 text-black">
					Categories
				</div>
				<div id="categories" class="owl-carousel owl-theme">
					<?php foreach ($categories as $category) : ?>
						<?php
						$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true ); 
						$image = wp_get_attachment_url( $thumbnail_id );
						// get permalink of product category
						$permalink = get_term_link( $category->term_id );
						?>
						<a href="<?= $permalink ?>" class="item py-4 px-4 bg-clearly-white">
							<img src="<?= $image ?>" alt="<?= $category->name ?>" class="flex-1">
							<div class="text-center truncate">
								<?= $category->name ?>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>


	<?php foreach ($groups as $group) : ?>
		<section>
			<div class="eshop-container mb-6">
				<div class="flex items-baseline mb-2">
					<div class="text-2xl font-bold text-black">
						<?= $group['title']; ?>
					</div>
					<a href="<?= site_url('shop') ?>" class="text-xs ml-2 text-red-500">
						<span>View All</span>
						<i class="fa fa-arrow-right"></i>
					</a>
				</div>
				<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
					<?php
						$args = $group['args'];
						$loop = new WP_Query($args);
						if($loop->have_posts()):
							while($loop->have_posts()):
								$loop->the_post();
								$product = wc_get_product($post->ID);
								?>
								<div class="">
									<a href="<?= $product->get_permalink(); ?>" class="eshop__card product" title="<?= $post->post_title ?>">
										<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $loop->post->ID ), 'single-post-thumbnail' );?>
										<!-- <img class="image" src="{{ product_images($product->images[0]['path']) }}" alt="Product Image"> -->
										<img class="image" src="<?php  echo $image[0]; ?>" alt="Product Image">
										<div class="detail">
											<div class="title">
												<?= $post->post_title ?>
											</div>
											<div class="price">
												<?php if( $product->is_on_sale() ): ?>
													<div class="sale">
														<?= money($product->get_sale_price()) ?>
													</div>
												<?php endif; ?>
												<div class="regular">
													<?= money($product->get_regular_price()) ?>
												</div>
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

								<?php
							endwhile;
						endif;
						wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
	<?php endforeach; ?>

	<!-- <section>
		<div class="eshop-container mb-6">
			<div class="flex items-baseline mb-2">
				<div class="text-2xl font-bold text-black">
					Ilham Camera
				</div>
			</div>
		</div>
	</section> -->

</main>
<script>
	jQuery(document).ready(function(){
		jQuery("#categories").owlCarousel({
			navigation : false,
			nav : false,
			items: 6,
			responsive: {
				0: {
					items: 2
				},
				1000: {
					items: 6
				}
			}
		});
		jQuery("#banner").owlCarousel({
			navigation : true,
			nav : true,
			slideSpeed : 300,
			paginationSpeed : 400,
			singleItem: true,
			items: 1,
			autoplay: true,
			autoplayTimeout: 5000,
			loop: true,
			autoplayHoverPause: true,
		});
	});
</script>
<?php get_footer(); ?>