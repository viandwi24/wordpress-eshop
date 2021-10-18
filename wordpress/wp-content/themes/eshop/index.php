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
					<img src="https://picsum.photos/520/140?random=1" alt="Banner">
				</div>
				<div class="item">
					<img src="https://picsum.photos/520/140?random=2" alt="Banner">
				</div>
				<div class="item">
					<img src="https://picsum.photos/520/140?random=3" alt="Banner">
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
						?>
						<div class="item py-4 px-4 bg-clearly-white">
							<img src="<?= $image ?>" alt="<?= $category->name ?>" class="flex-1">
							<div class="text-center truncate">
								<?= $category->name ?>
							</div>
						</div>
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

								<?php
							endwhile;
						endif;
						wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
	<?php endforeach; ?>

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