<?php
global $wpdb;
define( 'PLUGIN_BASE_URL', plugins_url( 'wordpress-persistent-call-tracking' ) . '/' );
define( 'PERSISTENT_CALL_TRACKING_TABLE_PHONES', $wpdb->prefix . "persistent_call_tracking_phones" );
define( 'PERSISTENT_CALL_TRACKING_TABLE_SHORTCODES', $wpdb->prefix . "persistent_call_tracking_shortcodes" );

require_once( ABSPATH . 'wp-includes/wp-db.php' );
require_once( PLUGIN_BASE . 'config/constants.php' );
require_once( PLUGIN_BASE . 'setup/install.php' );
require_once( PLUGIN_BASE . 'controller.php' );

$GLOBALS['persistent_call_tracking_cont_obj'] = new persistent_call_tracking_controller();

// Update if we're not at the latest version
if (get_option(WORDPRESS_PERSISTENT_CALL_TRACKING_VERSION_KEY) != WORDPRESS_PERSISTENT_CALL_TRACKING_VERSION_NUM) {
	require_once( PLUGIN_BASE . 'helper/update.php' );

	Update::do_update();
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}