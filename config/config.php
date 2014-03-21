<?php
// configuration settings

if (!class_exists('WP_List_Table')) {
  require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

global $wpdb;
define('MY_BASE_URL', plugins_url('wordpress-persistent-call-tracking') . '/');
define('PHONE_TABLE', $wpdb->prefix . "persistent_call_tracking_phones");

require_once(ABSPATH . 'wp-includes/wp-db.php');
require_once(MY_BASE . 'setup/install.php');

require_once(MY_BASE . 'controller.php');
$GLOBALS['tw_cont_obj'] = new persistent_call_tracking_tw_controller();