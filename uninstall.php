<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( ! is_user_logged_in() ) {
	wp_die( 'You must be logged in to run this script.' );
}

if ( ! current_user_can( 'install_plugins' ) ) {
	wp_die( 'You do not have permission to run this script.' );
}

global $wpdb;

$table = $wpdb->prefix . "persistent_call_tracking_phones";
$sql   = "DROP TABLE " . $table . ";";
$wpdb->query( $sql );

$table = $wpdb->prefix . "persistent_call_tracking_shortcodes";
$sql   = "DROP TABLE " . $table . ";";
$wpdb->query( $sql );

require_once( plugin_dir_path( __FILE__ ) . 'config/constants.php' );

delete_option( 'persistent_call_tracking_cookie' );
delete_option( WORDPRESS_PERSISTENT_CALL_TRACKING_VERSION_KEY );