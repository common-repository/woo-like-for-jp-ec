<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( ! class_exists( 'WC_JPEC_Emails_List' ) ) {

	class WC_JPEC_Emails_List extends WP_List_Table {

		function __construct() {
			global $status, $page;

			parent::__construct( array(
				'singular' => 'メール設定',
				'plural'   => 'メール設定', 
				'ajax'     => false,
			) );

		}

		function get_columns() {
			$columns = array(
				'wcemails_title'        => 'タイトル',
				'wcemails_description'  => '説明',
				'wcemails_subject'      => '件名',
			);
			return $columns;
		}


        function column_wcemails_title($item){
			ob_start() ?>
			<strong><a class="row-title"
				href="<?php echo add_query_arg( array( 'type' => 'add-email', 'wcemails_edit' => $item['ID'] ), admin_url( 'admin.php?page=wcemails-settings' ) ); ?>"
				title="Edit “<?php echo $item['title'] ?>”"><?php echo $item['title'] ?></a>
			</strong>
			<div class="row-actions">
				<span class="edit">
					<a href="<?php echo add_query_arg( array( 'type' => 'add-email', 'wcemails_edit' => $item['ID'] ), admin_url( 'admin.php?page=wcemails-settings' ) ); ?>"
						data-key="<?php echo $item['ID']; ?>"
						title="Edit this item">編集</a> |
				</span>
				<span class="delete">
					<a href="<?php echo add_query_arg( array( 'type' => 'view-email', 'wcemails_delete' => $item['ID'] ), admin_url( 'admin.php?page=wcemails-settings' ) ); ?>"
						class="wcemails_delete"
						data-key="<?php echo $item['ID']; ?>"
						title="Edit this item">削除</a> |
				</span>
			</div><?php
			return ob_get_clean();
        }
        

		function get_sortable_columns() {
			$sortable_columns = array();
			return $sortable_columns;
		}

		function prepare_items() {

			$columns  = $this->get_columns();
			$hidden   = array();
			$sortable = $this->get_sortable_columns();

			$this->_column_headers = array( $columns, $hidden, $sortable );

			$data = get_option( 'wcemails_email_details', array() );
			foreach ( $data as $key => $data_item ) {
				$data[ $key ]['ID'] = $key;
			}
			$current_page = $this->get_pagenum();

			$total_items = count( $data );
			$this->items = $data;

		}

	}
}
