<?php
/*
Plugin Name: Persistent Call Tracking
Plugin URI: http://www.isl.ca/en/home/plugins/wordpress-call-tracking.aspx
Description: This plugin is used to persist phone numbers based on a source value in the query string.
Version: 1.0
Author: ISL
Author URI: http://www.isl.ca
License: GPLv3
*/

add_filter( 'widget_text', 'do_shortcode' );

define( 'MY_BASE', plugin_dir_path( __FILE__ ) );

require_once( MY_BASE . 'config/config.php' );
register_activation_hook( __FILE__, array( 'tw_install', 'install' ) );

add_action( 'init', 'persistent_call_tracking_thecookie', 1 );

function persistent_call_tracking_thecookie() {
	global $wpdb;
	$cookie_expiry        = (float) get_option( 'persistent_call_tracking_cookie' );
	$phn_no               = get_option( 'persistent_call_tracking_default' );
	$getSrc               = (int) $_GET['src'];
	$cookie_trackable_src = (int) $_COOKIE["trackable_src"];

	if ( $getSrc > 0 && $getSrc != $cookie_trackable_src ) {
		$sql  = "SELECT * FROM " . PHONE_TABLE . " where p_id = " . $getSrc . " and status = 1";
		$data = $wpdb->get_row( $sql, ARRAY_A );
		if ( $data['p_id'] > 0 ) {
			setcookie( "trackable_src", $data['p_id'], time() + ( 3600 * 24 * $cookie_expiry ) );
		}
	}
}

function tw_phone_numbers() {
	global $tw_cont_obj;
	$tw_cont_obj->phone_numbers();
}

function tw_add_phone() {
	global $tw_cont_obj;
	$tw_cont_obj->add_phone();
}

function tw_settings() {
	global $tw_cont_obj;
	$tw_cont_obj->settings();
}

add_shortcode( 'trackable_number', array( 'persistent_call_tracking_tw_controller', 'trackable_number' ) );