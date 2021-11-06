<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php
	global $post, $wpdb;
    $social_media_table_name = eshop_config('social_media_table_name');
    $social_medias = $wpdb->get_results("SELECT * FROM {$social_media_table_name} WHERE `social_media_name` LIKE '%whatsapp%'");
	if (count($social_medias) > 0) {
		$whatsapp = $social_medias[0];
		$wa = $whatsapp->social_media_link;
		// $wa = explode('/', $wa); $wa = $wa[count($wa) - 1];
		?>
		<meta name="social-media-whatsapp" content="<?= $wa ?>">
	<?php } ?>
	<?php wp_head() ?>
</head>
<body <?php body_class(); ?>>
    <?php if (function_exists( 'wp_body_open' )) { wp_body_open(); } ?>
    <div id="app">
		<?php get_template_part('template/app', 'navbar'); ?>