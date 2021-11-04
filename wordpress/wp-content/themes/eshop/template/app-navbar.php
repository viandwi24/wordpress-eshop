<nav id="navbar" class="navbar">
	<div id="navbar-top" class="hidden md:block bg-red-100">
		<div class="eshop-container">
			<?php
			wp_nav_menu([
				'theme_location' => 'navbar-top-menu',
				'container' => 'nav',
			]);
			?>
		</div>
	</div>
	<div id="navbar-bottom" class="bg-red-500">
		<div class="eshop-container py-4">
			<div class="flex text-white">
				<a class="brand self-center flex">
					<?php (has_custom_logo()) ? the_custom_logo() : '' ?>
					<a href="<?php echo esc_url(home_url('/')); ?>" class="ml-2 self-center text-2xl font-semibold"><?php bloginfo('name') ?></a>
					<div class="flex-1 flex lg:hidden justify-end justify-items-end items-center">
						<button class="self-center toggle-sidebar-mobile">
							<i class="fas fa-bars text-2xl"></i>
						</button>
					</div>
				</a>
				<form action="<?= site_url('shop') ?>" class="hidden lg:flex w-1/5 pl-4 items-center">
					<input type="hidden" name="wpf" value="filter">
					<input
						name="wpf_cari"
						type="text"
						placeholder="Cari produk..."
						class="
							w-full
							py-2 px-4 text-sm
							rounded outline-none transition-all duration-300
							bg-red-400 text-white placeholder-white
							hover:text-primary hover:bg-red-200 hover:placeholder-primary
							focus:text-primary focus:bg-red-200 focus:placeholder-primary
						"
						value="<?= isset($_GET['wpf_cari']) ? $_GET['wpf_cari'] : ''  ?>"
					>
				</form>
				<ul class="menu flex-1 hidden lg:flex justify-end items-center">
					<li class="ml-4">
						<a
							href="javascript:void(0)"
							class="category-dropdown ml-3 text-sm transition-colors duration-300 justify-center align-middle items-center flex space-x-2 text-white hover:text-black"
						>
							<!-- <i class="fas fa-tags"></i> -->
							<span class="self-center">Kategory</span>
							<i class="fas fa-chevron-down"></i>
						</a>
						<div class="absolute z-20 hidden">
							<div
								class="
									relative px-4 py-2 w-40 mt-6
									border border-red-500 shadow-xl
									bg-red-600
								"
							>
								<?php
								$args = array(
									'taxonomy'     => 'product_cat',
									'orderby'      => 'name',
									'show_count'   => 0,
									'pad_counts'   => 0,
									'hierarchical' => 1,
									'title_li'     => '',
									'hide_empty'   => 0
								);
								$all_categories = get_categories($args);
								?>
								<ul class="flex flex-col text-sm space-y-1">
									<?php foreach ($all_categories as $cat): if($cat->category_parent == 0) { ?>
										<li class="duration-300 transition-all hover:text-black">
											<a href="<?= get_term_link($cat->term_id) ?>">
												<?= $cat->name ?>
											</a>
										</li>
									<?php } endforeach; ?>
								</ul>
							</div>
						</div>
					</li>
					<?php if ($socials = eshop_get_social_media()): ?>
						<li class="ml-4">
							<a
								href="javascript:void(0)"
								class="category-dropdown ml-3 text-sm transition-colors duration-300 justify-center align-middle items-center flex space-x-2 text-white hover:text-black"
							>
								<!-- <i class="fas fa-tags"></i> -->
								<span class="self-center">Sosial Media</span>
								<i class="fas fa-chevron-down"></i>
							</a>
							<div class="absolute z-20 hidden">
								<div
									class="
										relative px-4 py-2 w-40 mt-6
										border border-red-500 shadow-xl
										bg-red-600
									"
								>
									<ul class="flex flex-col text-sm space-y-1">
										<?php foreach ($socials as $social): ?>
											<li class="duration-300 transition-all hover:text-black">
												<a href="<?= $social->social_media_link ?>" class="flex space-x-2">
													<span>
														<?php if (strpos($social->social_media_logo, 'fa-') !== false): ?>
															<i class="<?= $social->social_media_logo ?> fa-fw"></i>
														<?php else: ?>
															<img src="<?= $social->social_media_logo ?>" alt="<?= $social->social_media_name ?>" style="max-width: 54px;">
														<?php endif; ?>
													</span>
													<span>
														<?= $social->social_media_name ?>
													</span>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							</div>
						</li>
					<?php endif; ?>
					<li class="ml-4">
						<a href="<?= esc_url(home_url('shop')) ?>" class="text-sm transition-colors duration-300 flex space-x-2 text-white hover:text-black">
							<span class="self-center">Produk</span>
						</a>
					</li>
					<li class="ml-4">
						<a href="<?= esc_url(home_url('blog')) ?>" class="text-sm transition-colors duration-300 flex space-x-2 text-white hover:text-black">
							<span class="self-center">Blog</span>
						</a>
					</li>
					<li class="ml-4">
						<a href="<?= get_permalink(wc_get_page_id('cart')) ?>" class="transition-colors duration-300 flex space-x-2 text-white hover:text-black">
							<i class="fa-solid fa-cart-shopping text-xl"></i>
						</a>
					</li>
					<li class="pl-4 pr-2">
						<div class="h-5 w-0.5 bg-white"></div>
					</li>
					<?php
					$current_user = wp_get_current_user();
					if(is_user_logged_in()):
					?>
						<li class="ml-2">
							<a href="<?= esc_url(home_url('/my-account')) ?>">
								<i class="fa fa-user mr-1"></i>
								<?= $current_user->display_name; ?>
							</a>
						</li>
					<?php else: ?>
						<li class="ml-2">
							<a href="<?= esc_url(home_url('/my-account')) ?>" class="eshop__button sm navbar-secondary">
								Register
							</a>
						</li>
						<li class="ml-2">
							<a href="<?= esc_url(home_url('/my-account')) ?>" class="eshop__button sm navbar-primary">
							Login
							</a>
						</li>
					<?php endif ?>
				</ul>
			</div>
			<!-- <div class="hidden lg:flex text-white">
				<?php
				wp_nav_menu([
					'theme_location' => 'navbar-bottom-menu',
					'container' => 'nav',
				]);
				?>
			</div> -->
		</div>
	</div>
</nav>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		const openedDropdowns = []
		document.querySelectorAll('.category-dropdown').forEach(function (el) {
			el.addEventListener('click', function (e) {
				if (this.parentElement.querySelector('div').classList.contains('hidden')) {
					openedDropdowns.push(this)
				}
				this.parentElement.querySelector('div').classList.toggle('hidden')
			});
		})
		document.addEventListener('click', function (e) {
			if (openedDropdowns.length > 0) {
				openedDropdowns.forEach(function (el) {
					if (!el.parentElement.querySelector('div').classList.contains('hidden')) {
						// check if the clicked element is outside the dropdown
						if (!el.contains(e.target)) {
							el.parentElement.querySelector('div').classList.add('hidden')
						}
					}
				})
			}
		});
	})
</script>

<?php
$mobileMenus = [
	['type' => 'item', 'text' => 'Beranda', 'link' => esc_url(home_url())],
	['type' => 'item', 'text' => 'Produk', 'link' => get_permalink(wc_get_page_id('shop'))],
	['type' => 'item', 'text' => 'Kategori', 'link' => 'javascript:void(0)', 'class' => 'kategori-toggle'],
	['type' => 'item', 'text' => 'Blog', 'link' =>esc_url(home_url('blog'))],
	['type' => 'item', 'text' => 'Keranjang', 'link' => get_permalink(wc_get_page_id('cart'))],
	['type' => 'header', 'text' => 'Eksplor'],
];

if(is_user_logged_in()) {
	$mobileMenus[] = ['type' => 'item', 'text' => 'Akun Saya', 'link' => esc_url(home_url('/my-account'))];
} else {
	$mobileMenus[] = ['type' => 'item', 'text' => 'Masuk', 'link' => esc_url(home_url('/my-account'))];
	$mobileMenus[] = ['type' => 'item', 'text' => 'Daftar', 'link' => esc_url(home_url('/my-account'))];
}


if ($socials = eshop_get_social_media()) {
	$mobileMenus[] = ['type' => 'header', 'text' => 'Media Sosial'];
	foreach ($socials as $social) {
		$mobileMenus[] = [
			'type' => 'item',
			'text' => $social->social_media_name,
			'link' => $social->social_media_link,
		];
	}
}


// $mobileMenus = [
// 	...$mobileMenus,
// 	['type' => 'header', 'text' => 'Browse'],
// 	['type' => 'item', 'text' => 'My Account', 'link' => ''],
// ];
?>
<div class="sidebar-mobile hidden z-50 w-full h-full left-0 top-0">
	<div class="c absolute w-full h-full" style="background: rgba(0, 0, 0, 0.5);z-index: -1;"></div>
	<div class="w-full h-full flex z-50">
		<div class="w-9/12 overflow-y-scroll sidebar-menu px-4 py-4 bg-white">
			<ul class="main">
				<?php foreach ($mobileMenus as $item) : ?>
					<!-- if header -->
					<?php if ($item['type'] == 'header') : ?>
						<li class="header font-thin mt-3 py-1 px-1 text-muted">
							<?= $item['text'] ?>
						</li>
					<?php else : ?>
						<li class="item font-thin py-1 px-1 text-black <?= @$item['class'] ?>">
							<a href="<?= $item['link'] ?>">
								<?= $item['text'] ?>
							</a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			<ul class="kategori hidden">
				<li class="font-thin py-1 px-1 text-muted">
					<a href="#" class="kategori-toggle">< Kembali</a>
				</li>
				<li class="header font-thin mt-3 py-1 px-1 text-muted">
					Kategori
				</li>
				<?php
				$categories = get_terms([
					'taxonomy' => 'product_cat',
					'hide_empty' => false,
				]);
				foreach ($categories as $category) :
					?>
					<li class="item font-thin py-1 px-1 text-black">
						<a href="<?= get_term_link($category) ?>">
							<?= $category->name ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="bg w-3/12 text-center pt-4">
			<button>
				<i class="fas fa-times text-4xl text-white"></i>
			</button>
		</div>
	</div>
</div>