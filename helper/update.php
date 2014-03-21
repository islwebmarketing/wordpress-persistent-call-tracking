<?php

class Update {
	static function do_update() {
		global $wpdb;

		// Version 1.1.0
		$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = '" . PHONE_TABLE . "' AND column_name = 'shortcode'" );

		if ( empty( $row ) ) {
			$wpdb->query( "ALTER TABLE " . PHONE_TABLE . " ADD shortcode VARCHAR(255) DEFAULT 'trackable_number' NOT NULL" );
		}

		// Update the version
		update_option(WORDPRESS_PERSISTENT_CALL_TRACKING_VERSION_KEY, WORDPRESS_PERSISTENT_CALL_TRACKING_VERSION_NUM);

		return true;
	}
}