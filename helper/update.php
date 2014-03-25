<?php

class Update {
	static function do_update() {
		global $wpdb;

        // Version 1.0.0
        $old_table = $wpdb->get_results("SELECT 1 FROM wp_ak_phones LIMIT 1;");

        if ( !empty( $old_table ) ) {
            $new_table = $wpdb->get_results("SELECT 1 FROM " . PERSISTENT_CALL_TRACKING_TABLE_PHONES . " LIMIT 1;");

            if ( empty( $new_table ) ) {
              require_once( PLUGIN_BASE . 'setup/install.php' );

              $installer = new persistent_call_tracking_install();
              $installer->install();
            }

            $wpdb->query("INSERT INTO " . PERSISTENT_CALL_TRACKING_TABLE_PHONES . " (p_id, phn_no, name, status, created)
                SELECT p_id, phn_no, name, status, created FROM wp_ak_phones;");
        }

		// Version 1.1.0
		$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = '" . PERSISTENT_CALL_TRACKING_TABLE_PHONES . "' AND column_name = 'shortcode'" );

		if ( empty( $row ) ) {
			$wpdb->query( "ALTER TABLE " . PERSISTENT_CALL_TRACKING_TABLE_PHONES . " ADD shortcode INT DEFAULT 0 NOT NULL" );
		}

		// Update the version
		update_option(WORDPRESS_PERSISTENT_CALL_TRACKING_VERSION_KEY, WORDPRESS_PERSISTENT_CALL_TRACKING_VERSION_NUM);

		return true;
	}
}