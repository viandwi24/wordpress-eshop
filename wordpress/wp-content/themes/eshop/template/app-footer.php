<footer class="footer bg-gray-50 relative pt-1 border-t-2 border-red-300">
	<div class="container mx-auto px-6">
		<div class="sm:flex sm:mt-2 w-full">
			<div class="mt-8 flex text-center flex-col md:flex-row flex-1">
				<div class="xl:w-1/3 mb-6 md:mb-0 text-center md:text-left">
					<a href="" class="block font-bold text-black text-2xl">
						<?= bloginfo('name'); ?>
					</a>
					<div class="text-sm">
						<?= bloginfo('description'); ?>
					</div>
					<div class="mt-3 h-32 w-32 hidden md:inline-block">
						<?php (has_custom_logo()) ? the_custom_logo() : '' ?>
					</div>
				</div>
				<div class="xl:w-2/3 flex flex-col md:flex-row text-center md:text-left space-y-3 md:space-y-0">
					<div class="w-full md:w-1/3 flex flex-col">
						<span class="text-black font-semibold text-lg">Marketplace</span>
						<?php if ($external_shops = eshop_get_external_shop()): ?>
							<?php foreach ($external_shops as $shop): ?>
								<span class="mb-1"><a href="<?= $shop->shop_link ?>" class="text-muted text-sm hover:text-black"><?= $shop->shop_name ?></a></span>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<div class="w-full md:w-1/3 flex flex-col">
						<div class="text-black font-semibold text-lg mb-1">Social Media</div>
						<div class="w-full md:w-auto justify-center md:justify-start flex flex-row space-x-2">
							<?php if ($socials = eshop_get_social_media()): ?>
								<?php foreach ($socials as $social): ?>
									<span class="mb-1">
										<a href="<?= $social->social_media_link ?>" class="text-muted text-sm hover:text-black">
											<?php if (strpos($social->social_media_logo, 'fa-') !== false): ?>
												<i class="<?= $social->social_media_logo ?> fa-2x"></i>
											<?php else: ?>
												<img src="<?= $social->social_media_logo ?>" alt="<?= $social->social_media_name ?>" style="max-width: 54px;">
											<?php endif; ?>
										</a>
									</span>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>
					<div class="w-full md:w-1/3 flex flex-col">
						<span class="mb-1"><a href="#" class="text-muted text-sm hover:text-black">BRI</a></span>
						<span class="mb-1"><a href="#" class="text-muted text-sm hover:text-black">DANA</a></span>
						<span class="mb-1"><a href="#" class="text-muted text-sm hover:text-black">OVO</a></span>
						<span class="mb-1"><a href="#" class="text-muted text-sm hover:text-black">BCA</a></span>
					</div>
				</div>
			</div>
		</div>
		<!-- <div class="bg-red-500">
			<img src="<?= home_url('payment-method') ?>" alt="Payment Method">
		</div> -->
	</div>
	<div class="mt-16 border-t border-red-700 bg-red-600 flex flex-col items-center">
		<div class="container mx-auto px-6">
			<div class="text-center py-3">
				<img class="inline-block h-5 mb-2" src="<?= home_url('payment-method') ?>" alt="Payment Method">
				<p class="text-xs text-white">
					© 2020 <?= bloginfo('name'); ?>
				</p>
			</div>
		</div>
	</div>
</footer>