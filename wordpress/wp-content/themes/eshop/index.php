<?php get_header(); ?>
<main class="my-4">
	<section>
		<div class="eshop-container">
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
	</section>
</main>
<script>
	jQuery(document).ready(function(){
		// jQuery("#categories").owlCarousel({
		// 	navigation : false,
		// 	nav : false,
		// 	items: 6,
		// 	responsive: {
		// 		0: {
		// 			items: 4
		// 		},
		// 		1000: {
		// 			items: 6
		// 		}
		// 	}
		// });
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