<?php
global $wpdb;
define( 'MY_BASE_URL', plugins_url( 'wordpress-persistent-call-tracking' ) . '/' );
define( 'PHONE_TABLE', $wpdb->prefix . "persistent_call_tracking_phones" );

require_once( ABSPATH . 'wp-includes/wp-db.php' );
require_once( MY_BASE . 'setup/install.php' );

require_once( MY_BASE . 'controller.php' );
$GLOBALS['tw_cont_obj'] = new persistent_call_tracking_tw_controller();

// Set the key and version
if (!defined('WORDPRESS_PERSISTENT_CALL_TRACKING_VERSION_NUM')) {
	define('WORDPRESS_PERSISTENT_CALL_TRACKING_VERSION_NUM', '1.1.0');
}
if (!defined('WORDPRESS_PERSISTENT_CALL_TRACKING_VERSION_KEY')) {
	define('WORDPRESS_PERSISTENT_CALL_TRACKING_VERSION_KEY', 'wordpress_persistent_call_tracking_version');
}

// Update if we're not at the latest version
if (get_option(WORDPRESS_PERSISTENT_CALL_TRACKING_VERSION_KEY) != WORDPRESS_PERSISTENT_CALL_TRACKING_VERSION_NUM) {
	require_once( MY_BASE . 'helper/update.php' );

	Update::do_update();
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}