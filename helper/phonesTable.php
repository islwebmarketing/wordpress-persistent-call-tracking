<?php

class Phones_Table extends WP_List_Table {
	function __construct() {
		global $status, $page;
		//Set parent defaults
		parent::__construct( array(
			'singular' => 'phone', //singular name of the listed records
			'plural'   => 'phones', //plural name of the listed records
			'ajax'     => FALSE //does this table support ajax?
		) );
	}

	function column_default( $item, $column_name ) {
		return stripslashes( $item[ $column_name ] );
	}

	function column_name( $item ) {
		if ( $item['status'] == 1 ) {
			$actions = array(
				'edit'       => sprintf( '<a href="?page=add-phone-number&action=%s&p_id=%s">Edit</a>', 'edit', $item['p_id'] ),
				'inactivate' => sprintf( '<a href="?page=%s&action=%s&phone=%s">Deactivate</a>', $_REQUEST['page'], 'inactivate', $item['p_id'] )
			);
		} else {
			$actions = array(
				'activate' => sprintf( '<a href="?page=%s&action=%s&phone=%s">Activate</a>', $_REQUEST['page'], 'activate', $item['p_id'] ),
				'delete'   => sprintf( '<a href="?page=%s&action=%s&phone=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['p_id'] ),
			);
		}

		//Return the title contents
		return sprintf( '%1$s %2$s',
			/*$1%s*/
			sprintf( '<a href="?page=persistent-call-tracking-calls&p_id=%s">%s</a>', $item['p_id'], $item['name'] ),
			/*$3%s*/
			$this->row_actions( $actions )
		);
	}

	function column_phn_no( $item ) {
		return $item['phn_no'];
	}

	function column_src( $item ) {
		return '?src=' . $item['p_id'] . ' &nbsp; <a href="#" onclick="copyToClipboard(\'?src=' . $item['p_id'] . '\');"><img title="Copy" style="vertical-align:top;" alt="Copy" src="' . PLUGIN_BASE_URL . 'images/copy.png" /></a>';
	}

	function column_shortcode( $item ) {
		$base_url = PLUGIN_BASE_URL;

		return "[{$item['shortcode']}] &nbsp; <a href='#'
		onclick=\"copyToClipboard('[{$item['shortcode']}]');\"><img title=\"Copy\"
		style=\"vertical-align:top;\" alt=\"Copy\" src=\"{$base_url}images/copy.png\"/></a>";
	}

	/** ************************************************************************
	 * Optional. If you need to include bulk actions in your list table, this is
	 * the place to define them. Bulk actions are an associative array in the format
	 * 'slug'=>'Visible Title'
	 *
	 * If this method returns an empty value, no bulk action will be rendered. If
	 * you specify any bulk actions, the bulk actions box will be rendered with
	 * the table automatically on display().
	 *
	 * Also note that list tables are not automatically wrapped in <form> elements,
	 * so you will need to create those manually in order for bulk actions to function.
	 *
	 * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
	 **************************************************************************/
	function get_bulk_actions() {
		if ( $_GET['post_status'] == 'inactive' ) {
			$actions = array(
				'activate' => 'Activate',
				'delete'   => 'Delete'
			);
		} else {
			$actions = array(
				'inactivate' => 'Deactivate'
			);
		}

		return $actions;
	}

	/** ************************************************************************
	 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	 * For this example package, we will handle it in the class to keep things
	 * clean and organized.
	 *
	 * @see $this->prepare_items()
	 **************************************************************************/
	function process_bulk_action() {
		global $wpdb;
		$phones = '';
		if ( is_array( $_GET['phone'] ) && count( $_GET['phone'] ) > 0 ) {
			$phones = implode( ',', $_GET['phone'] );
		} elseif ( is_numeric( $_GET['phone'] ) && $_GET['phone'] > 0 ) {
			$phones = $_GET['phone'];
		}

		$currentAction = trim( $this->current_action() );
		if ( trim( $phones ) != '' ) {
			$phones = '(' . $phones . ')';
			if ( 'delete' == $currentAction ) {
				$wpdb->query( "DELETE FROM " . PERSISTENT_CALL_TRACKING_TABLE_PHONES . " WHERE p_id in " . $phones );
			} elseif ( 'inactivate' == $currentAction ) {
				$wpdb->query( "update " . PERSISTENT_CALL_TRACKING_TABLE_PHONES . " set status = 0 WHERE p_id in " . $phones );
			} elseif ( 'activate' == $currentAction ) {
				$wpdb->query( "update " . PERSISTENT_CALL_TRACKING_TABLE_PHONES . " set status = 1 WHERE p_id in " . $phones );
			}
		}
	}

	/** ************************************************************************
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 *
	 * @return string Text to be placed inside the column <td>
	 **************************************************************************/
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/
			$this->_args['singular'],
			/*$2%s*/
			$item['p_id']
		);
	}

	/** ************************************************************************
	 * REQUIRED! This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value
	 * is the column's title text. If you need a checkbox for bulk actions, refer
	 * to the $columns array below.
	 *
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a column_cb() method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 **************************************************************************/
	function get_columns() {
		$columns = array(
			'cb'     => '<input type="checkbox" />', //Render a checkbox instead of text
			'name'   => 'Name/Source',
			'phn_no' => 'Phone Number',
			'src'    => 'Parameter',
			'shortcode'    => 'Shortcode'
		);

		return $columns;
	}

	function get_views() {
		global $wpdb;
		$query    = "SELECT a.* FROM " . PERSISTENT_CALL_TRACKING_TABLE_PHONES . " as a";
		$all      = $wpdb->query( $query );
		$active   = $wpdb->query( $query . ' where status = 1' );
		$inactive = $wpdb->query( $query . ' where status = 0' );

		${$_GET['post_status'] . 'cnt'} = ' class="current"';

		$toRet = array(
			'all'      => '<a href="?page=persistent-call-tracking&post_status=all"' . $allcnt . '>All <span class="count">(' . $all . ')</span></a>',
			'active'   => '<a href="?page=persistent-call-tracking"' . $cnt . '>Active <span class="count">(' . $active . ')</span></a>',
			'inactive' => '<a href="?page=persistent-call-tracking&post_status=inactive"' . $inactivecnt . '>Inactive <span class="count">(' . $inactive . ')</span></a>'
		);

		return $toRet;
	}

	/** ************************************************************************
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	 * you will need to register it here. This should return an array where the
	 * key is the column that needs to be sortable, and the value is db column to
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
	 * your data accordingly (usually by modifying your query).
	 *
	 * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
	 **************************************************************************/
	function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', FALSE )
		);

		return $sortable_columns;
	}

	/** ************************************************************************
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here...
	 *
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 **************************************************************************/
	function prepare_items() {
		global $wpdb, $_wp_column_headers;
		$screen = get_current_screen();

		/* -- Preparing your query -- */
		$query = "SELECT * FROM " . PERSISTENT_CALL_TRACKING_TABLE_PHONES;
		if ( $_GET['post_status'] == 'inactive' ) {
			$query .= ' where status = 0';
		} elseif ( $_GET['post_status'] == 'all' ) {
			$query .= ' where 1';
		} else {
			$query .= ' where status = 1';
		}
		/* -- Ordering parameters -- */
		//Parameters that are going to be used to order the result
		$orderby = ! empty( $_GET["orderby"] ) ? mysql_real_escape_string( $_GET["orderby"] ) : 'name';
		$order   = ! empty( $_GET["order"] ) ? mysql_real_escape_string( $_GET["order"] ) : 'desc';
		if ( ! empty( $orderby ) & ! empty( $order ) ) {
			$query .= ' ORDER BY ' . $orderby . ' ' . $order;
		}
		/* -- Pagination parameters -- */
		$per_page = 20;
		//Number of elements in your table?
		$totalitems = $wpdb->query( $query ); //return the total number of affected rows
		$totalpages = ceil( $totalitems / $per_page );

		if ( ! empty( $paged ) && ! empty( $perpage ) ) {
			$offset = ( $paged - 1 ) * $perpage;
			$query .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
		}
		/* -- Register the pagination -- */
		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page"    => $perpage,
		) );
		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );
		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();
		$data = $wpdb->get_results( $query, ARRAY_A );
		/***********************************************************************
		 * ---------------------------------------------------------------------
		 * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
		 *
		 * In a real-world situation, this is where you would place your query.
		 *
		 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
		 * ---------------------------------------------------------------------
		 **********************************************************************/
		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();
		$data         = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;

		return $data;
	}
}