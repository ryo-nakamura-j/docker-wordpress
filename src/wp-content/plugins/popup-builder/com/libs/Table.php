<?php

namespace sgpbDataTable;

use sgpb\AdminHelper;
use sgpb\SubscriptionPopup;
require_once(dirname(__FILE__).'/ListTable.php');
file_exists(SG_POPUP_CLASSES_POPUPS_PATH.'SubscriptionPopup.php') && require_once(SG_POPUP_CLASSES_POPUPS_PATH.'SubscriptionPopup.php');

class SGPBTable extends SGPBListTable
{
	protected $id = '';
	protected $columns = array();
	protected $displayColumns = array();
	protected $sortableColumns = array();
	protected $tablename = '';
	protected $rowsPerPage = 10;
	protected $initialOrder = array();
	private $previewPopup = false;
	private $isVisibleExtraNav = true;

	public function __construct($id, $popupPreviewId = false)
	{
		$this->id = $id;
		$this->previewPopup = $popupPreviewId;
		parent::__construct(array(
			'singular'=> 'wp_'.$id, //singular label
			'plural' => 'wp_'.$id.'s', //plural label
			'ajax' => false
		));
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function setRowsPerPage($rowsPerPage)
	{
		$this->rowsPerPage = $rowsPerPage;
	}

	public function setColumns($columns)
	{
		$this->columns = $columns;
	}

	public function getColumns()
	{
		return $this->columns;
	}

	public function setDisplayColumns($displayColumns)
	{
		$this->displayColumns = $displayColumns;
	}

	public function setSortableColumns($sortableColumns)
	{
		$this->sortableColumns = $sortableColumns;
	}

	public function setTablename($tablename)
	{
		$this->tablename = $tablename;
	}

	public function setInitialSort($orderableColumns)
	{
		$this->initialOrder = $orderableColumns;
	}

	public function get_columns()
	{
		return $this->displayColumns;
	}

	public function setIsVisibleExtraNav($isVisibleExtraNav)
	{
		$this->isVisibleExtraNav = $isVisibleExtraNav;
	}

	public function getIsVisibleExtraNav()
	{
		return $this->isVisibleExtraNav;
	}

	public function getNavPopupsConditions()
	{
		return '';
	}

	public function prepare_items()
	{
		global $wpdb;
		$table = $this->tablename;

		$query = 'SELECT '.implode(', ', $this->columns).' FROM '.$table;
		$this->customizeQuery($query);

		$totalItems = count($wpdb->get_results($query)); //return the total number of affected rows

		if ($this->previewPopup) {
			$totalItems -= 1;
		}
		$perPage = $this->rowsPerPage;

		$totalPages = ceil($totalItems/$perPage);

		$orderby = isset($_GET['orderby']) ? sanitize_sql_orderby($_GET['orderby']) : 'ASC';
		$order = isset($_GET['order']) ? sanitize_sql_orderby($_GET['order']) : '';

		if (isset($this->initialOrder) && empty($order)) {
			foreach ($this->initialOrder as $key => $value) {
				$order = $value;
				$orderby = $key;
			}
		}

		if (!empty($orderby) && !empty($order)) {
			$query .= ' ORDER BY '.$orderby.' '.$order;
		}
		$this->set_pagination_args(array(
			"total_items" => $totalItems,
			"total_pages" => $totalPages,
			"per_page" => $perPage,
		));
		$paged = $this->get_pagenum();

		if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
			$paged = 1;
		}

		//adjust the query to take pagination into account
		if (!empty($paged) && !empty($perPage)) {
			$offset = ($paged - 1) * $perPage;
			$query .= ' LIMIT '.(int)$offset.','.(int)$perPage;
		}

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$items = $wpdb->get_results($query, ARRAY_N);
		/*Remove popup data when its class does not exist.*/
		$this->customizeRowsData($items);

		$this->items = $items;
	}

	public function customizeRowsData(&$items) {

	}

	public function get_sortable_columns() {
		return $this->sortableColumns;
	}

	public function display_rows()
	{
		//get the records registered in the prepare_items method
		$records = $this->items;

		//get the columns registered in the get_columns and get_sortable_columns methods
		list($columns, $hidden) = $this->get_column_info();

		foreach($records as $key => $rec) {
			echo '<tr>';

			$this->customizeRow($rec);

			foreach ($rec as $k => $item) {
				if (0 === $k) {
					echo '<th scope="row" class="check-column">'.wp_kses($item, AdminHelper::allowed_html_tags()).'</th>';
				} else {
					echo '<td>'.wp_kses($item, AdminHelper::allowed_html_tags()).'</td>';
				}
			}
			echo '</tr>';
		}
	}

	public function customizeRow(&$row)
	{

	}

	public function customizeQuery(&$query)
	{

	}

	public function __toString()
	{
		$this->prepare_items(); ?>
		<form method="get" id="posts-filter-<?php echo esc_attr($this->id)?>">
		<p class="search-box">
			 <input type="hidden" name="post_type" value="popupbuilder" />
			 <?php $this->search_box('search', $this->id); ?>
		</p>
		<?php $this->display();?>
		</form>
		<?php
		return '';
	}

	// parent class method overriding
	public function extra_tablenav($which)
	{
		$isVisibleExtraNav = $this->getIsVisibleExtraNav();

		if (!$isVisibleExtraNav) {
			return '';
		}
		?>
		<div class="alignleft actions daterangeactions">
			<label class="screen-reader-text" for="sgpb-subscription-popup"><?php esc_html_e('Filter by popup', SG_POPUP_TEXT_DOMAIN)?></label>
			<?php echo wp_kses($this->getNavPopupsConditions(), AdminHelper::allowed_html_tags()); ?>

			<label class="screen-reader-text" for="sgpb-subscribers-dates"><?php esc_html_e('Filter by date', SG_POPUP_TEXT_DOMAIN)?></label>
			<?php  echo wp_kses($this->getNavDateConditions(), AdminHelper::allowed_html_tags()); ?>

			<input name="filter_action" id="post-query-submit" class="button" value="<?php esc_html_e('Filter', SG_POPUP_TEXT_DOMAIN)?>" type="submit">
		</div>
		<?php
	}

	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
			return;
		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( sanitize_text_field($_REQUEST['orderby']) ) . '" />';
		if ( ! empty( $_REQUEST['order'] ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( sanitize_text_field($_REQUEST['order']) ) . '" />';
		if ( ! empty( $_REQUEST['post_mime_type'] ) )
			echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( sanitize_text_field($_REQUEST['post_mime_type']) ) . '" />';
		if ( ! empty( $_REQUEST['detached'] ) )
			echo '<input type="hidden" name="detached" value="' . esc_attr( sanitize_text_field($_REQUEST['detached']) ) . '" />';
		?>
			<div class="search search-box">
				<input type="search" class="search__input" id="<?php echo esc_attr($input_id) ?>" name="s" value="<?php _admin_search_query(); ?>" />
				<div>
					<?php submit_button($text, 'search__button', '', false, array('id' => 'search-submit')); ?>
				</div>
			</div>
		<?php
	}

	public function display() {
		$singular = $this->_args['singular'];

		$this->display_tablenav( 'top' );
		?>
		<table class="table sgpb-table <?php echo esc_attr(implode( ' ', $this->get_table_classes() )); ?>">
			<thead >
				<tr>
					<?php $this->print_column_headers(); ?>
				</tr>
			</thead>

			<tbody class='sgpb-table-body'<?php
				if ( $singular ) {
					echo esc_attr(" data-wp-lists='list:$singular'");
				} ?>>
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>
	<!--		<div class="table__row">
				<?php /*$this->print_column_headers(); */?>
			</div>-->

		</table>
		<?php
		$this->display_tablenav( 'bottom' );
	}

	public function display_rows_or_placeholder() {
		if ( $this->has_items() ) {
			$this->display_rows();
		} else {
			echo '<tr class="no-items"><td class="colspanchange" colspan="' . esc_attr($this->get_column_count()) . '">';
			$this->no_items();
			echo '</td></tr>';
		}
	}

	public function no_items() {
		esc_html_e( 'No items found.' );
	}

	public function has_items() {
		return !empty( $this->items );
	}

	public function print_column_headers( $with_id = true ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url = remove_query_arg( 'paged', $current_url );

		if ( isset( $_GET['orderby'] ) )
			$current_orderby = sanitize_text_field($_GET['orderby']);
		else
			$current_orderby = '';

		if ( isset( $_GET['order'] ) && 'desc' == $_GET['order'] )
			$current_order = 'desc';
		else
			$current_order = 'asc';

		/* TODO check this part of code in each imported table file */
		/*if ( ! empty( $columns['bulk'] ) ) {
			static $cb_counter = 1;
			$columns['bulk'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
				. '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
			$cb_counter++;
		}*/

		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', "column-$column_key", 'table__head' );
			if ( in_array( $column_key, $hidden ) ) {
				$class[] = 'hidden';
			}

			if ( 'bulk' == $column_key )
				$class[] = 'check-column';
			elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) )
				$class[] = 'num';

			if ( $column_key === $primary ) {
				$class[] = 'column-primary';
			}

			if ( isset( $sortable[$column_key] ) ) {
				list( $orderby, $desc_first ) = $sortable[$column_key];

				if ( $current_orderby == $orderby ) {
					$order = 'asc' == $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order = $desc_first ? 'desc' : 'asc';
					$class[] = 'sortable';
					$class[] = $desc_first ? 'asc' : 'desc';
				}

				$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
			}

			$tag = ( 'bulk' === $column_key ) ? 'td' : 'th';

			$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
			$id = $with_id ? "id='$column_key'" : '';

			if ( !empty( $class ) )
				$class = "class='" . join( ' ', $class ) . "'";
			echo wp_kses("<$tag $scope $id $class>$column_display_name</$tag>", AdminHelper::allowed_html_tags());
		}
	}

}
