<?php

// install table and other required settings for plugin
class tw_install {
	function install() {
		$sql = "CREATE TABLE " . PHONE_TABLE . " (
				p_id INT NOT NULL AUTO_INCREMENT,
				phn_no VARCHAR(255) NOT NULL,
				shortcode VARCHAR(255) DEFAULT 'trackable_number',
				name VARCHAR(255) NOT NULL,
				status TINYINT NOT NULL,
				created DATETIME NOT NULL,
				UNIQUE KEY p_id (p_id)
			);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );

		add_option( 'persistent_call_tracking_cookie', 90 );
		add_option( 'persistent_call_tracking_default', 0 );
	}

	function admin_menu() {
		add_menu_page( 'Call Tracker', 'Call Tracker', 'manage_options', 'tw-phone-tracker', 'tw_phone_numbers', MY_BASE_URL . 'images/phone_grey.png', 36 );

		add_submenu_page( 'tw-phone-tracker', 'Phone Numbers', 'Phone Numbers', 'manage_options', 'tw-phone-tracker', 'tw_phone_numbers' );
		add_submenu_page( 'tw-phone-tracker', 'Add New', 'Add New', 'manage_options', 'add-phone-number', 'tw_add_phone' );
		add_submenu_page( 'tw-phone-tracker', 'Settings', 'Settings', 'manage_options', 'tw-settings', 'tw_settings' );
	}
}

add_action( 'admin_menu', array( 'tw_install', 'admin_menu' ) );