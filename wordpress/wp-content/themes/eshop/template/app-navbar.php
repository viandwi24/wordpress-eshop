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
		<div class="eshop-container flex py-4 text-white">
			<a class="brand self-center flex">
				<?php (has_custom_logo()) ? the_custom_logo() : '' ?>
				<a href="<?php echo esc_url(home_url('/')); ?>" class="ml-2 self-center text-2xl font-semibold"><?php bloginfo('name') ?></a>
				<div class="flex-1 flex md:hidden justify-end justify-items-end items-center">
					<button class="self-center toggle-sidebar-mobile">
						<i class="fas fa-bars text-2xl"></i>
					</button>
				</div>
			</a>
			<form action="<?= site_url('shop') ?>" class="hidden md:flex w-1/5 pl-4 items-center">
				<input
					name="q"
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
				>
			</form>
			<ul class="menu flex-1 hidden md:flex justify-end items-center space-x-2">
				<?php
				$actionMenu = [
					[ 'link' => get_permalink(wc_get_page_id('cart')), 'icon' => 'fa-solid fa-cart-shopping text-xl' ],
				];
				?>
				<?php foreach ($actionMenu as $item) : ?>
					<li>
						<a href="<?= $item['link'] ?>" class="transition-colors duration-300 text-white hover:text-black">
							<?php if(isset($item['text'])): ?>
								<?= $item['text'] ?>
							<?php endif; ?>
							<?php if(isset($item['icon'])): ?>
								<i class="<?= $item['icon'] ?> text-xl"></i>
							<?php endif; ?>
						</a>
					</li>
				<?php endforeach; ?>
				<li class="px-4">
					<div class="h-5 w-0.5 bg-white"></div>
				</li>
				<!-- @auth('customer')
					<li>
						<a href="" class="transition-colors duration-300 text-white hover:text-black">
							{{-- <span class="text-xs">Rp 10000</span> --}}
							<i class="fa fa-user text-xl"></i>
						</a>
					</li>
				@endauth
				@guest('customer') -->
				<?php
				$current_user = wp_get_current_user();
				if(is_user_logged_in()):
				?>
					<li>
						<a href="<?= esc_url(home_url('/my-account')) ?>">
							<i class="fa fa-user mr-1"></i>
							<?= $current_user->display_name; ?>
						</a>
					</li>
				<?php else: ?>
					<li>
						<a href="<?= esc_url(home_url('/my-account')) ?>" class="eshop__button sm navbar-secondary">
							Register
						</a>
					</li>
					<li>
						<a href="<?= esc_url(home_url('/my-account')) ?>" class="eshop__button sm navbar-primary">
						Login
						</a>
					</li>
				<?php endif ?>
			</ul>
		</div>
	</div>
</nav>

<?php
$mobileMenus = [
	['type' => 'item', 'text' => 'Beranda', 'link' => esc_url(home_url())],
	['type' => 'item', 'text' => 'Produk', 'link' => get_permalink(wc_get_page_id('shop'))],
	['type' => 'item', 'text' => 'Blog', 'link' =>esc_url(home_url('blog'))],
	['type' => 'item', 'text' => 'Keranjang', 'link' => get_permalink(wc_get_page_id('cart'))],
	['type' => 'header', 'text' => 'Browse'],
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
		<ul class="w-9/12 sidebar-menu px-4 py-4 bg-white">
			<?php foreach ($mobileMenus as $item) : ?>
				<!-- if header -->
				<?php if ($item['type'] == 'header') : ?>
					<li class="header font-thin mt-3 py-1 px-1 text-muted">
						<?= $item['text'] ?>
					</li>
				<?php else : ?>
					<li class="item font-thin py-1 px-1 text-black">
						<a href="<?= $item['link'] ?>">
							<?= $item['text'] ?>
						</a>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		<div class="bg w-3/12 text-center pt-4">
			<button>
				<i class="fas fa-times text-4xl text-white"></i>
			</button>
		</div>
	</div>
</div>