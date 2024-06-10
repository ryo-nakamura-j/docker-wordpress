<?php

/**
 * Generates the table on the plugin overview page.
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Overview_Table extends WP_List_Table {

	/**
	 * Number of popups to show per page.
	 *
	 * @since 2.0.0
	 */
	public $per_page;

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// Utilize the parent constructor to build the main class properties.
		parent::__construct(
			[
				'singular' => 'popup',
				'plural'   => 'popups',
				'ajax'     => false,
			]
		);

		// Default number of popups to show per page
		$this->per_page = apply_filters( 'wppopups_overview_per_page', 20 );
	}

	/**
	 * Retrieve the table columns.
	 *
	 * @return array $columns Array of all the list table columns.
	 * @since 2.0.0
	 */
	public function get_columns() {

		$columns = [
			'cb'            => '<input type="checkbox" />',
			'spu_switch'    => esc_html__( 'Off / On', 'wp-popups-lite' ),
			'popup_name'    => esc_html__( 'Name', 'wp-popups-lite' ),
			'trigger_class' => esc_html__( 'Trigger class', 'wp-popups-lite' ),
			'created'       => esc_html__( 'Created', 'wp-popups-lite' ),
		];

		return apply_filters( 'wppopups_overview_table_columns', $columns );
	}

	/**
	 * Render the checkbox column.
	 *
	 * @param WPPopups_Popup $popup
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function column_cb( $popup ) {
		return '<input type="checkbox" name="popup_id[]" value="' . absint( $popup->id ) . '" />';
	}

	/**
	 * Renders the columns.
	 *
	 * @param WPPopups_Popup $popup
	 * @param string $column_name
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function column_default( $popup, $column_name ) {

		switch ( $column_name ) {
			case 'id':
				$value = $popup->id;
				break;

			case 'trigger_class':
				$value = 'spu-open-' . $popup->id;
				break;

			case 'spu_switch' :
				$value = '<a title="' . ( $popup->status == 'publish' ? esc_html__( 'Toggle off', 'wp-popups-lite' ) : esc_html__( 'Toggle on', 'wp-popups-lite' ) ) . '" class="' . ( $popup->status == 'publish' ? 'wppopups-on' : 'wppopups-off' ) . '"  href="' . wp_nonce_url( admin_url( 'admin.php?page=wppopups-overview&popup_id=' . $popup->id . '&action=toggle_on' ), 'wppopups_toggle_popup_nonce' ) . '"><img src="' . WPPOPUPS_PLUGIN_URL . 'assets/images/icon-';
				$value .= $popup->status == 'publish' ? 'toggle-on' : 'toggle-off';
				$value .= '.png"/></a>';
				break;

			case 'created':
				$value = mysql2date( get_option( 'date_format' ), $popup->date );
				break;

			case 'modified':
				$value = get_post_modified_time( get_option( 'date_format' ), false, $popup );
				break;

			case 'author':
				$author = get_userdata( $popup->post_author );
				$value  = $author->display_name;
				break;

			default:
				$value = '';
		}

		return apply_filters( 'wppopups_overview_table_column_value', $value, $popup, $column_name );
	}

	/**
	 * Render the popup name column with action links.
	 *
	 * @param WPPopups_Popup $popup
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function column_popup_name( $popup ) {

		// Prepare variables.
		$name = ! empty( $popup->title ) ? $popup->title : __( 'Unnamed popup', 'wp-popups-lite' );
		$name = sprintf(
			'<a class="row-title" href="%s" title="%s"><strong>%s</strong></a>',
			add_query_arg(
				[
					'view'     => 'content',
					'popup_id' => $popup->id,
				],
				admin_url( 'admin.php?page=wppopups-builder' )
			),
			esc_html__( 'Edit this popup', 'wp-popups-lite' ),
			$name
		);

		// Build all of the row action links.
		$row_actions = [];

		// Edit.
		$row_actions['edit'] = sprintf(
			'<a href="%s" title="%s">%s</a>',
			add_query_arg(
				[
					'view'     => 'content',
					'popup_id' => $popup->id,
				],
				admin_url( 'admin.php?page=wppopups-builder' )
			),
			esc_html__( 'Edit this popup', 'wp-popups-lite' ),
			esc_html__( 'Edit', 'wp-popups-lite' )
		);


		// Preview.
		$row_actions['preview_'] = sprintf(
			'<a href="%s" title="%s" target="_blank" rel="noopener noreferrer">%s</a>',
			esc_url( wppopups()->preview->popup_preview_url( $popup->id ) ),
			esc_html__( 'View preview', 'wp-popups-lite' ),
			esc_html__( 'Preview', 'wp-popups-lite' )
		);

		// Duplicate.
		$row_actions['duplicate'] = sprintf(
			'<a href="%s" title="%s">%s</a>',
			wp_nonce_url(
				add_query_arg(
					[
						'action'   => 'duplicate',
						'popup_id' => $popup->id,
					],
					admin_url( 'admin.php?page=wppopups-overview' )
				),
				'wppopups_duplicate_popup_nonce'
			),
			esc_html__( 'Duplicate this popup', 'wp-popups-lite' ),
			esc_html__( 'Duplicate', 'wp-popups-lite' )
		);

		// Delete.
		$row_actions['delete'] = sprintf(
			'<a href="%s" title="%s">%s</a>',
			wp_nonce_url(
				add_query_arg(
					[
						'action'   => 'delete',
						'popup_id' => $popup->id,
					],
					admin_url( 'admin.php?page=wppopups-overview' )
				),
				'wppopups_delete_popup_nonce'
			),
			esc_html__( 'Delete this popup', 'wp-popups-lite' ),
			esc_html__( 'Delete', 'wp-popups-lite' )
		);

		// Build the row action links and return the value.
		return $name . $this->row_actions( apply_filters( 'wppopups_overview_row_actions', $row_actions, $popup ) );
	}

	/**
	 * Define bulk actions available for our table listing.
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function get_bulk_actions() {

		$actions = [
			'delete' => esc_html__( 'Delete', 'wp-popups-lite' ),
			'toggle_on' => esc_html__( 'Toggle status', 'wp-popups-lite' ),
		];

		return $actions;
	}

	/**
	 * Process the bulk actions.
	 *
	 * @since 2.0.0
	 */
	public function process_bulk_actions() {

		$ids = isset( $_GET['popup_id'] ) ? $_GET['popup_id'] : [];

		if ( ! is_array( $ids ) ) {
			$ids = [ $ids ];
		}

		$ids    = array_map( 'absint', $ids );
		$action = ! empty( $_REQUEST['action'] ) ? $_REQUEST['action'] : false;

		if ( empty( $ids ) || empty( $action ) ) {
			return;
		}

		// Delete one or multiple popups - both delete links and bulk actions.
		if ( 'delete' === $this->current_action() ) {

			if (
				wp_verify_nonce( $_GET['_wpnonce'], 'bulk-popups' ) ||
				wp_verify_nonce( $_GET['_wpnonce'], 'wppopups_delete_popup_nonce' )
			) {
				foreach ( $ids as $id ) {
					wppopups()->popups->delete( $id );
				}
				?>
				<div class="notice updated">
					<p>
						<?php
						if ( count( $ids ) === 1 ) {
							esc_html_e( 'Popup was successfully deleted.', 'wp-popups-lite' );
						} else {
							esc_html_e( 'Popups were successfully deleted.', 'wp-popups-lite' );
						}
						?>
					</p>
				</div>
				<?php
			} else {
				wppopups_security_failed();
			}
		}

		if ( 'toggle_on' === $this->current_action() ) {
			//checks
			if ( wp_verify_nonce( $_GET['_wpnonce'], 'bulk-popups' ) ||
			     wp_verify_nonce( $_GET['_wpnonce'], 'wppopups_toggle_popup_nonce' )
			) {

				foreach ( $ids as $id ) {
					wppopups()->popups->toggle_status( $id );
				}
				?>
				<div class="notice updated">
					<p>
						<?php
						if ( count( $ids ) === 1 ) {
							esc_html_e( 'Popup was successfully toggled.', 'wp-popups-lite' );
						} else {
							esc_html_e( 'Popups were successfully toggled.', 'wp-popups-lite' );
						}
						?>
					</p>
				</div>
				<?php
			} else {
				wppopups_security_failed();
			}
		}

		if ( 'duplicate' === $this->current_action() ) {
			if ( wp_verify_nonce( $_GET['_wpnonce'], 'wppopups_duplicate_popup_nonce' ) ) {
				foreach ( $ids as $id ) {
					wppopups()->popups->duplicate( $id );
				}
				?>
				<div class="notice updated">
					<p>
						<?php
						if ( count( $ids ) === 1 ) {
							esc_html_e( 'Popup was successfully duplicated.', 'wp-popups-lite' );
						} else {
							esc_html_e( 'Popups were successfully duplicated.', 'wp-popups-lite' );
						}
						?>
					</p>
				</div>
				<?php
			} else {
				wppopups_security_failed();
			}
		}
		do_action( 'wppopups_process_bulk_actions', $this );
		// clean url with a redirect
		echo '<script> location.replace("' . admin_url( 'admin.php?page=wppopups-overview' ) . '")</script>';
	}
	// Duplicate popup - currently just delete links (no bulk action at the moment).

	/**
	 * Message to be displayed when there are no popups.
	 *
	 * @since 2.0.0
	 */
	public function no_items() {
		printf(
			wp_kses(
			/* translators: %s - admin area page builder page URL. */
				__( 'Whoops, you haven\'t created a popup yet. Want to <a href="%s">give it a go</a>?', 'wp-popups-lite' ),
				[
					'a' => [
						'href' => [],
					],
				]
			),
			admin_url( 'admin.php?page=wppopups-builder' )
		);
	}

	/**
	 * Fetch and setup the final data for the table.
	 *
	 * @since 2.0.0
	 */
	public function prepare_items() {

		// Process bulk actions if found.
		$this->process_bulk_actions();

		// Setup the columns.
		$columns = $this->get_columns();

		// Hidden columns (none).
		$hidden = [];

		// Define which columns can be sorted - popup name, date.
		$sortable = [
			'popup_name' => [ 'title', false ],
			'created'    => [ 'date', false ],
		];

		// Set column headers.
		$this->_column_headers = [ $columns, $hidden, $sortable ];

		// Get popups.
		$totals   = wp_count_posts( 'wppopups' );
		$total    = $totals->publish + $totals->draft;
		$page     = $this->get_pagenum();
		$order    = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'DESC';
		$orderby  = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'ID';
		$per_page = $this->get_items_per_page( 'wppopups_popups_per_page', $this->per_page );
		$data     = wppopups()->popups->get(
			'', [
				'orderby'        => $orderby,
				'order'          => $order,
				'nopaging'       => false,
				'posts_per_page' => $per_page,
				'paged'          => $page,
				'post_status'    => [ 'publish', 'draft' ],
				'no_found_rows'  => false,
				'post_parent'   => 0
			]
		);

		$this->items = $data;

		// Finalize pagination.
		$this->set_pagination_args(
			[
				'total_items' => $total,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total / $per_page ),
			]
		);
	}
}
