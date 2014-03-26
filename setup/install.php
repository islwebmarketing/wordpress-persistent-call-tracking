<?php

// install table and other required settings for plugin
class persistent_call_tracking_install {
	function install() {
		global $wpdb;

		$sql = "CREATE TABLE IF NOT EXISTS " . PERSISTENT_CALL_TRACKING_TABLE_PHONES . " (
				p_id INT NOT NULL AUTO_INCREMENT,
				phn_no VARCHAR(255) NOT NULL,
				shortcode INT DEFAULT 1 NOT NULL,
				name VARCHAR(255) NOT NULL,
				status TINYINT NOT NULL,
				created DATETIME NOT NULL,
				UNIQUE KEY p_id (p_id)
			);";

		$wpdb->query( $sql );

        $sql = "CREATE TABLE IF NOT EXISTS " . PERSISTENT_CALL_TRACKING_TABLE_SHORTCODES . " (
				s_id INT NOT NULL AUTO_INCREMENT,
				name VARCHAR(255) NOT NULL,
				shortcode VARCHAR(255) NOT NULL,
				default_number VARCHAR(255) NOT NULL,
				status TINYINT NOT NULL,
				created DATETIME NOT NULL,
				UNIQUE KEY s_id (s_id)
			);";

        $wpdb->query( $sql );

		add_option( 'persistent_call_tracking_cookie', 90 );
	}

	function admin_menu() {
		add_menu_page( 'Call Tracker', 'Call Tracker', 'manage_options', 'persistent-call-tracking', 'persistent_call_tracking_phone_numbers', PLUGIN_BASE_URL . 'images/phone_grey.png', 36 );

        add_submenu_page( 'persistent-call-tracking', 'Phone Numbers', 'Phone Numbers', 'manage_options', 'persistent-call-tracking', 'persistent_call_tracking_phone_numbers' );
        add_submenu_page( 'persistent-call-tracking', 'Add Number', 'Add Number', 'manage_options', 'add-phone-number', 'persistent_call_tracking_add_phone' );
        add_submenu_page( 'persistent-call-tracking', 'Shortcodes', 'Shortcodes', 'manage_options', 'persistent-call-tracking-shortcodes', 'persistent_call_tracking_shortcodes' );
        add_submenu_page( 'persistent-call-tracking', 'Add Shortcode', 'Add Shortcode', 'manage_options', 'add-shortcode', 'persistent_call_tracking_add_shortcode' );
        add_submenu_page( 'persistent-call-tracking', 'Settings', 'Settings', 'manage_options', 'persistent-call-tracking-settings', 'persistent_call_tracking_settings' );
	}
}

add_action( 'admin_menu', array( 'persistent_call_tracking_install', 'admin_menu' ) );