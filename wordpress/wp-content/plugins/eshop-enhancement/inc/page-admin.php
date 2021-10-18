<?php
// Page
function eshop_enhancement_external_shop_page () {
	$action = isset($_GET['action']) ? $_GET['action'] : '';
	$view = '';
	if ($action == '' || $action == 'list') {
		$view = 'external-shop';
	} else if ($action == 'create' || $action == 'edit') {
		$view = 'external-shop-create';
	}
	eshop_enhancement_view($view, $action);
}
function eshop_enhancement_social_media_page () {
	$action = isset($_GET['action']) ? $_GET['action'] : '';
	$view = '';
	if ($action == '' || $action == 'list') {
		$view = 'social-media';
	} else if ($action == 'create' || $action == 'edit') {
		$view = 'social-media-create';
	}
	eshop_enhancement_view($view, $action);
}

