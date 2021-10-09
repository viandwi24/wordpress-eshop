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
			<a href="{{ route('home') }}" class="brand self-center text-2xl font-semibold">
				<?php bloginfo('name') ?>
			</a>
			<form action="" class="hidden md:block w-1/5 pl-4">
				<input
					type="text"
					placeholder="Search Product..."
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
					[ 'link' => '/', 'icon' => 'fa-solid fa-cart-shopping text-xl' ],
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
				<li>
					<a class="eshop__button sm navbar-secondary">
						Register
					</a>
				</li>
				<li>
					<a class="eshop__button sm navbar-primary">
					Login
					</a>
				</li>
			</ul>
		</div>
	</div>
</nav>