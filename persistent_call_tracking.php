<?php
/*
Plugin Name: Persistent Call Tracking
Plugin URI: http://www.isl.ca/en/home/plugins/wordpress-call-tracking.aspx
Description: This plugin is used to persist phone numbers based on a source value in the query string.
Version: 1.1.0
Author: ISL
Author URI: http://www.isl.ca
License: GPLv3
*/

add_filter( 'widget_text', 'do_shortcode' );

define( 'PLUGIN_BASE', plugin_dir_path( __FILE__ ) );

require_once( PLUGIN_BASE . 'config/config.php' );
register_activation_hook( __FILE__, array( 'persistent_call_tracking_install', 'install' ) );

add_action( 'admin_init', 'persistent_call_tracking_deactivate_obsolete_plugin' );
add_action( 'init', 'persistent_call_tracking_thecookie', 1 );

function persistent_call_tracking_thecookie() {
	global $wpdb;
	$cookie_expiry        = (float) get_option( 'persistent_call_tracking_cookie' );
	$getSrc               = (int) $_GET['src'];
	$cookie_trackable_src = (int) $_COOKIE["trackable_src"];

	if ( $getSrc > 0 && $getSrc != $cookie_trackable_src ) {
		$sql  = "SELECT * FROM " . PERSISTENT_CALL_TRACKING_TABLE_PHONES . " where p_id = " . $getSrc . " and status = 1";
		$data = $wpdb->get_row( $sql, ARRAY_A );
		if ( $data['p_id'] > 0 ) {
			setcookie( "trackable_src", $data['p_id'], time() + ( 3600 * 24 * $cookie_expiry ), "/" );
		}
	}
}

function persistent_call_tracking_phone_numbers() {
	global $persistent_call_tracking_cont_obj;
	$persistent_call_tracking_cont_obj->phone_numbers();
}
function persistent_call_tracking_shortcodes() {
	global $persistent_call_tracking_cont_obj;
	$persistent_call_tracking_cont_obj->shortcodes();
}

function persistent_call_tracking_add_phone() {
	global $persistent_call_tracking_cont_obj;
	$persistent_call_tracking_cont_obj->add_phone();
}

function persistent_call_tracking_add_shortcode() {
	global $persistent_call_tracking_cont_obj;
	$persistent_call_tracking_cont_obj->add_shortcode();
}

function persistent_call_tracking_settings() {
	global $persistent_call_tracking_cont_obj;
	$persistent_call_tracking_cont_obj->settings();
}

function persistent_call_tracking_deactivate_obsolete_plugin() {
    if( is_plugin_active( 'ak_call_track/ak_call_track.php' ) ) {
        deactivate_plugins( 'ak_call_track/ak_call_track.php' );
    }
}

function persistent_call_tracking_add_shortcodes() {
	global $wpdb;

	$sql  = "SELECT shortcode FROM " . PERSISTENT_CALL_TRACKING_TABLE_SHORTCODES . " where status = 1";

	$data = $wpdb->get_results( $sql );

	foreach ($data as $result) {
		add_shortcode( $result->shortcode, array( 'persistent_call_tracking_controller', 'trackable_number' ) );
	}
}

persistent_call_tracking_add_shortcodes();