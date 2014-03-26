<?php

class persistent_call_tracking_controller {
	function view( $view = '', $data = array() ) {
		$viewPath = PLUGIN_BASE . 'views/' . $view . '.php';
		if ( file_exists( $viewPath ) ) {
			include_once( $viewPath );
		} else {
			echo '<h1 style="color:#FF0000">Wrong parameters, please try again.<h1>';
		}
	}

	function wp_messages( $data ) {
		if ( trim( $data['wp_error'] ) != '' ) {
			echo '<div id="message" class="error"><p><strong>' . $data['wp_error'] . '</strong></p></div>';
		}
		if ( trim( $data['wp_msg'] ) != '' ) {
			echo '<div id="message" class="updated fade"><p><strong>' . $data['wp_msg'] . '</strong></p></div>';
		}
	}

	function trackable_number( $atts, $content, $tag ) {
		global $wpdb;
		$getSrc               = (int) $_GET['src'];
		$cookie_trackable_src = (int) $_COOKIE["trackable_src"];

        // TODO: Get the shortcode, save the default value.
        $sql  = "SELECT * FROM " . PERSISTENT_CALL_TRACKING_TABLE_SHORTCODES . " where shortcode = '" . $tag . "' and status = 1";

        $shortcode = $wpdb->get_row( $sql );

        if ( empty( $shortcode ) ) {
            return "";
        }

		if ( $getSrc > 0 && $getSrc != $cookie_trackable_src ) {
			$sql  = "SELECT * FROM " . PERSISTENT_CALL_TRACKING_TABLE_PHONES . " where p_id = " . $getSrc . " and status = 1";

			$data = $wpdb->get_row( $sql, ARRAY_A );

			if ( $data['p_id'] > 0 && $data['shortcode'] == $shortcode->s_id ) {
				return $data['phn_no'];
			}
		}
		if ( $cookie_trackable_src > 0 ) {
			$sql  = "SELECT * FROM " . PERSISTENT_CALL_TRACKING_TABLE_PHONES . " where p_id = " . $cookie_trackable_src . " and status = 1";
			$data = $wpdb->get_row( $sql, ARRAY_A );

			if ( $data['p_id'] > 0 && $data['shortcode'] == $shortcode->s_id ) {
				return $data['phn_no'];
			}
		}

		return $shortcode->default_number;
	}

	function settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$data                  = array();
		$data['cookie_expiry'] = get_option( 'persistent_call_tracking_cookie' );

		if ( $_POST['persistent_call_tracking_submit'] == 'Save Changes' ) {
			$persistent_call_tracking_cookie  = (float) trim( $_POST['cookie_expiry'] );
			$data['cookie_expiry']            = $persistent_call_tracking_cookie;

			if ( trim( $_POST['cookie_expiry'] ) == '' ) {
				$data['wp_error'] = 'Cookie Expiry is required and must not be left empty.';
			} elseif ( ! is_numeric( trim( $_POST['cookie_expiry'] ) ) || trim( $_POST['cookie_expiry'] ) < 0 || trim( $_POST['cookie_expiry'] ) > 730 ) {
				$data['wp_error'] = 'Cookie Expiry should be between 1 to 730 Days';
			} else {
				$data['wp_msg'] = 'Call Tracker Settings Update. From now on all new cookies created will expire after ' . $persistent_call_tracking_cookie . ' days(s).';
				update_option( 'persistent_call_tracking_cookie', $persistent_call_tracking_cookie );
			}
		}
		$this->view( 'settings', $data );
	}

	function phone_numbers() {
		global $wpdb;

		$data = array();
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		require_once( PLUGIN_BASE . 'helper/phonesTable.php' );

		//Create an instance of our package class...
		$phonesTable = new Phones_Table();
		//Fetch, prepare, sort, and filter our data...
		$curGridData = $phonesTable->prepare_items();

		$data['phonesTable'] = $phonesTable;
		$this->view( 'phone_numbers', $data );
	}

	function shortcodes() {
		global $wpdb;

		$data = array();
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		require_once( PLUGIN_BASE . 'helper/shortcodesTable.php' );

		//Create an instance of our package class...
		$shortcodesTable = new Shortcodes_Table();

		//Fetch, prepare, sort, and filter our data...
		$curGridData = $shortcodesTable->prepare_items();

		$data['shortcodesTable'] = $shortcodesTable;
		$this->view( 'shortcodes', $data );
	}

	function add_phone() {
		global $wpdb;
		$data = array();
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		if ( isset( $_GET["p_id"] ) ) {
			$p_id = htmlspecialchars( $_GET["p_id"] );
			if ( ! is_numeric( $p_id ) ) {
				$p_id = 0;
			}
		} else {
			$p_id = 0;
		}

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['action'] ) && $_POST['action'] == "new_record" ) {
			$data = $_POST;

			$chkNoExists = $wpdb->query( "SELECT p_id FROM " . PERSISTENT_CALL_TRACKING_TABLE_PHONES . " where phn_no = '" . trim( $_POST['phn_no'] ) . "' and p_id <> '" . trim( $_POST['p_id'] ) . "'" );

			if ( trim( $_POST['name'] ) == '' || trim( $_POST['phn_no'] ) == '' ) {
				$data['wp_error'] = "Please enter value for Name and Phone Number.";
			} elseif ( $chkNoExists > 0 ) {
				$data['wp_error'] = trim( $_POST['phn_no'] ) . " Phone Number already exists.";
			} else {
				if ( isset ( $_POST['p_id'] ) ) {
					$id = $_POST['p_id'];
				} else {
					$id = 0;
				}

				$name   = $_POST['name'];
				$phn_no = $_POST['phn_no'];
				$shortcode = $_POST['shortcode'];

				$table = PERSISTENT_CALL_TRACKING_TABLE_PHONES;
				// add value to new record array
				$new_record = array(
					'name'   => $name,
					'phn_no' => $phn_no,
					'shortcode' => $shortcode,
					'status' => 1
				);
				//save the post
				if ( $id == 0 ) {
					$new_record['created'] = date( 'Y-m-d H:i:s' );
					$wpdb->insert( $table, $new_record );
					$data['p_id']   = $wpdb->insert_id;
					$data['wp_msg'] = "New Phone Number Added";
				} else {
					//if we have an ID, update
					$where = array( 'p_id' => $id );
					$p_id  = $id;
					$wpdb->update( $table, $new_record, $where );
					$data['wp_msg'] = "Phone Number Updated";
				}
			}
		}
		if ( $p_id > 0 ) {
			$sql              = "SELECT * FROM " . PERSISTENT_CALL_TRACKING_TABLE_PHONES . " where p_id = " . $p_id;
			$tmpMsg           = $data['wp_msg'];
			$tmpError         = $data['wp_error'];
			$data             = $wpdb->get_row( $sql, ARRAY_A );
			$data['wp_msg']   = $tmpMsg;
			$data['wp_error'] = $tmpError;
		}
		$this->view( 'add_phone', $data );
	}

	function add_shortcode() {
		global $wpdb;
		$data = array();

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		if ( isset( $_GET["s_id"] ) ) {
			$s_id = htmlspecialchars( $_GET["s_id"] );
			if ( ! is_numeric( $s_id ) ) {
				$s_id = 0;
			}
		} else {
			$s_id = 0;
		}

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['action'] ) && $_POST['action'] == "new_record" ) {
			$data = $_POST;

			$chkNoExists = $wpdb->query( "SELECT s_id FROM " . PERSISTENT_CALL_TRACKING_TABLE_SHORTCODES . " where shortcode = '" . trim( $_POST['shortcode'] ) . "' and s_id <> '" . trim( $_POST['s_id'] ) . "'" );

			if ( trim( $_POST['name'] ) == '' || trim( $_POST['shortcode'] ) == '' || trim( $_POST['default_number'] ) == '' ) {
				$data['wp_error'] = "Please enter value for Name, Default Number and Shortcode.";
			} elseif ( $chkNoExists > 0 ) {
				$data['wp_error'] = "The shortcode '" . trim( $_POST['shortcode'] ) . "' already exists.";
			} else {
				if ( isset ( $_POST['s_id'] ) ) {
					$id = $_POST['s_id'];
				} else {
					$id = 0;
				}

				$name   = $_POST['name'];
				$default_number = $_POST['default_number'];
				$shortcode = $_POST['shortcode'];

				$table = PERSISTENT_CALL_TRACKING_TABLE_SHORTCODES;

				// add value to new record array
				$new_record = array(
					'name'   => $name,
					'default_number' => $default_number,
					'shortcode' => $shortcode,
					'status' => 1
				);

				//save the post
				if ( $id == 0 ) {
					$new_record['created'] = date( 'Y-m-d H:i:s' );
					$wpdb->insert( $table, $new_record );
					$data['s_id']   = $wpdb->insert_id;
					$data['wp_msg'] = "New Shortcode Added";
				} else {
					//if we have an ID, update
					$where = array( 's_id' => $id );
					$s_id  = $id;
					$wpdb->update( $table, $new_record, $where );
					$data['wp_msg'] = "Shortcode Updated";
				}
			}
		}
		if ( $s_id > 0 ) {
			$sql              = "SELECT * FROM " . PERSISTENT_CALL_TRACKING_TABLE_SHORTCODES . " where s_id = " . $s_id;
			$tmpMsg           = $data['wp_msg'];
			$tmpError         = $data['wp_error'];
			$data             = $wpdb->get_row( $sql, ARRAY_A );
			$data['wp_msg']   = $tmpMsg;
			$data['wp_error'] = $tmpError;
		}
		$this->view( 'add_shortcode', $data );
	}
}