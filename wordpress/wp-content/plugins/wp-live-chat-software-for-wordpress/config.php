<?php

define( 'LC_PARTNER_ID', '' );
define( 'LC_UTM_CAMPAIGN', '' );
define( 'PLUGIN_SLUG', 'wp-live-chat-software-for-wordpress' );
define( 'PLUGIN_MAIN_FILE', PLUGIN_SLUG . '/livechat.php' );
define( 'OPTION_PREFIX', 'livechat_' );
define( 'MENU_SLUG', 'livechat' );
define( 'LC_RESOURCES_URL', 'https://www.livechat.com/wp-resources-integration/' );
define( 'LC_APP_URL_PATTERN', 'https://connect.livechatinc.com/%s/%s%s' );
define( 'LC_API_URL_PATTERN', 'https://%s.livechatinc.com' );
define( 'LC_WIDGET_URL_REGEX', '/^https:\/\/connect(-eu)?\.livechatinc\.com\/api\/v1\/script\/([a-z]|[A-Z]|[0-9]|[-]){36}\/widget\.js(\?lcv=([a-z]|[A-Z]|[0-9]|[-]){36})?$/' );
define( 'LC_AA_URL', 'https://my.livechatinc.com' );
define( 'CONNECT_BRIDGE_SCRIPT_URL', 'https://cdn.livechat-static.com/integrations/integrations-connect/connect-bridge.js' );

// Below has to be done this way because of PHP 5.6 limitations for using arrays in define.
const DEPRECATED_OPTION_PREFIXES = array(
	'wp-legacy'  => 'livechat_',
	'woo-legacy' => 'wc-lc_',
	'woo-2.x'    => 'woo_livechat_',
);
