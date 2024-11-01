<?php
 
 add_filter( 'bulk_actions-edit-shop_order', 'wc_jpec_register_my_bulk_actions', 100 );
 function wc_jpec_register_my_bulk_actions($actions) {
    $actions['mark_waiting-payment'] = '入金待ちに変更';
    $actions['mark_processing'] = '処理中に変更';
    $actions['mark_reserve_shipping'] = '発送予約に変更';
    $actions['mark_finish-shipping'] = '出荷済みに変更';
    $actions['mark_completed'] = '完了に変更';
    $actions['mark_cancelled'] = 'キャンセルに変更';
    $actions['trash'] = 'ゴミ箱に移動';
    $action_array= array(
       'mark_waiting-payment', 'mark_processing', 'mark_reserve_shipping',
        'mark_finish-shipping','mark_completed',
       'mark_cancelled', 'trash'
   );
   $new_actions = array();
   foreach($action_array as $action){
       $new_actions[$action] = $actions[$action];
   }
   $new_actions['none'] = "----";
   return $new_actions;
 }

function wc_jpec_change_shop_order_status_admin_menu($views) {
    $new_view = array();     
    $order_array = array("all","wc-new-order","wc-waiting-payment","wc-processing","wc-reserve_shipping",
                "wc-finish-shipping","wc-completed",
                "wc-on-hold","wc-refunded", "wc-failed",
                "wc-cancelled","trash");
    foreach($order_array as $status){
        if(isset($views[$status])){
            $new_view[$status] = $views[$status];
        }
    }
    return $new_view;
 
}
add_filter('views_edit-shop_order','wc_jpec_change_shop_order_status_admin_menu');

function wc_jpec_register_custom_order_status() {
    register_post_status(
        'wc-new-order', array(
            'label'                     => '新規注文',
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( '新規注文 <span class="count">(%s)</span>', '新規注文 <span class="count">(%s)</span>' )
        )
    );

    register_post_status(
        'wc-waiting-payment', array(
            'label'                     => '入金待ち',
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( '入金待ち <span class="count">(%s)</span>', '入金待ち <span class="count">(%s)</span>' )
        )
    );

    register_post_status(
        'wc-reserve_shipping', array(
            'label'                     => '発送予約',
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( '発送予約 <span class="count">(%s)</span>', '発送予約 <span class="count">(%s)</span>' )
        )
    );

    register_post_status(
        'wc-finish-shipping', array(
            'label'                     => '発送済み',
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( '発送済み <span class="count">(%s)</span>', '発送済み <span class="count">(%s)</span>' )
        )
    );
}

// Add to list of WC Order statuses
function wc_jpec_add_custom_order_statuses( $order_statuses ) {
    $order_status['wc-new-order'] = "新規注文";
    $order_status['wc-waiting-payment'] = "入金待ち";
    $order_status['wc-processing'] = "処理中";
    $order_status['wc-reserve_shipping'] = "発送予約";
    $order_status['wc-finish-shipping'] = "発送済み";
    $order_status['wc-completed'] = "完了";
    $order_status['wc-cancelled'] = "キャンセル";
    $order_status['wc-refunded'] = "払い戻し";
    $order_status['wc-on-hold'] = "保留中";
    $order_status['wc-failed'] = "注文失敗";
    return $order_status;
}

//add dashbord report for custom status
add_filter( 'woocommerce_reports_order_statuses', 'wc_jpec_include_custom_order_status_to_reports', 20, 1 );
function wc_jpec_include_custom_order_status_to_reports( $statuses ){
    $statuses[] = "new-order";
    $statuses[] = "waiting-payment";
    $statuses[] = "reserve_shipping";
    $statuses[] = "finish-shipping";
    return $statuses;
}


/* make custom order editable  start*/
function wc_jpec_custom_status_is_editable( $editable, $order ) {
    if( 
        $order->get_status() == 'new-order' ||
        $order->get_status() == 'waiting-payment' ||
        $order->get_status() == 'processing'
    ){
        $editable = true;
    }else{
        $editable = false;
    }
    return $editable;
}
add_filter( 'wc_order_is_editable', 'wc_jpec_custom_status_is_editable', 10, 2 );
/* make custom order editable  end*/


function wc_jpec_make_the_order_status_new($order_id){

    $is_pass = apply_filters("wc_jpec_make_the_order_status_pass", '');
    if($is_pass){
        return;
    }

    $order = new WC_Order( $order_id );
    $order->update_status('wc-new-order', 'order_note');
}


// Woocommerce show time on order start
add_filter('woocommerce_admin_order_date_format', 'wc_jpec_custom_post_date_column_time');
function wc_jpec_custom_post_date_column_time($h_time)
{
    return get_the_time(__('Y/m/d H:i', 'woocommerce'));
}
// Woocommerce show time on order end


// Woocommerce show payment method start
add_filter('manage_edit-shop_order_columns', 'wc_jpec_payment_items_column' );
function wc_jpec_payment_items_column( $order_columns ) {
    $order_columns = array();
    $order_columns["cb"] = '<input type="checkbox" />';
    $order_columns["order_number"] = "注文番号";
    $order_columns["order_date"] = "注文日時";
    $order_columns["order_status"] = "ステータス";
    $order_columns["ap_send_shipping_mail"] = "発送メールステータス";
    $order_columns["order_payment"] = "支払い方法";
    $order_columns["billing_address"] = "請求先情報";
    $order_columns["shipping_address"] = "配送先";
    $order_columns["order_total"] = "合計";
    $order_columns["wc_actions"] = "アクション";
    return $order_columns;
}
 
add_action( 'manage_shop_order_posts_custom_column' , 'wc_jpec_order_payment_column' );
function wc_jpec_order_payment_column( $colname ) {
	global $the_order; // the global order object
 	if( $colname == 'order_payment' ) {
        $payment = $the_order->get_payment_method_title();
        print $payment;
	}
}
// Woocommerce show payment method end


add_action( 'manage_shop_order_posts_custom_column' , 'wc_jpec_send_shipping_mail_column' );
function wc_jpec_send_shipping_mail_column( $colname ) {
	global $the_order; // the global order object
 	if( $colname == 'ap_send_shipping_mail' ) {
        $status = "";
        $order_id = $the_order->get_order_number();
        $flag_array = get_post_meta( $order_id, 'ap_send_shipping_mail');
        if ( isset($flag_array[0]) ){
            $img_url = plugins_url('../image/mail.svg', __FILE__ );
            $status = "<div align='center'><img src='".$img_url."' alt='' width='25' ></div>";
        }
        print $status;
	}
}