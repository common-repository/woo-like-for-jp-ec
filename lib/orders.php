<?php


add_action('all_admin_notices', 'wc_jpec_all_admin_notices', 10, 0);
function wc_jpec_all_admin_notices() {
    if( ! is_admin() )
        return;
    global $post_type;
    global $query;
    if($post_type != "shop_order"){return;}
    if(isset($_GET['action'])){return;}
    ?>
    <div id="ap-wrap" class="hidden no-sidebar" tabindex="-1" aria-label="<?php esc_attr_e('Advanced search', 'ap'); ?>">
        <form method="POST" action="<?php print admin_url(); ?>/edit.php?post_type=shop_order">
            <div class="ap-groups">
                <dl>
                    <dt>注文番号: </dt>
                    <dd><input type="text" name="order_id" value="<?php print filter_input( INPUT_POST, 'order_id', FILTER_VALIDATE_INT );; ?>" placeholder="完全一致"></dd>
                    <dt>日付: </dt>
                    <dd><input type="date" name="date_start" value="<?php print filter_input( INPUT_POST, 'date_start', FILTER_SANITIZE_STRING ); ?>" placeholder="YYYY/MM/DD"> - <input type="date" name="date_end" value="<?php print @$_POST['date_end']; ?>" placeholder="YYYY/MM/DD"></dd>
                    <dt>SKU: </dt>
                    <dd><input type="text" name="_sku" value="<?php print filter_input( INPUT_POST, '_sku', FILTER_SANITIZE_STRING ); ?>" placeholder="部分一致"></dd>
                    <dt>JAN: </dt>
                    <dd><input type="text" name="_jan" value="<?php print filter_input( INPUT_POST, '_jan', FILTER_SANITIZE_STRING ); ?>" placeholder="部分一致"></dd>
                    <dt>姓: </dt>
                    <dd><input type="text" name="_billing_last_name" value="<?php print filter_input( INPUT_POST, '_billing_last_name', FILTER_SANITIZE_STRING ); ?>" placeholder="部分一致"></dd>
                    <dt>名: </dt>
                    <dd><input type="text" name="_billing_first_name" value="<?php print filter_input( INPUT_POST, '_billing_first_name', FILTER_SANITIZE_STRING ); ?>" placeholder="部分一致"></dd>
                    <dt>メール: </dt>
                    <dd><input type="text" name="_billing_email" value="<?php print filter_input( INPUT_POST, '_billing_email', FILTER_SANITIZE_STRING ); ?>" placeholder="部分一致"></dd>
                    <dt>電話番号: </dt>
                    <dd><input type="text" name="_billing_phone" value="<?php print filter_input( INPUT_POST, '_billing_phone', FILTER_SANITIZE_STRING ); ?>" placeholder="部分一致"></dd>
                    <dt>会社名: </dt>
                    <dd><input type="text" name="_billing_company" value="<?php print filter_input( INPUT_POST, '_billing_company', FILTER_SANITIZE_STRING ); ?>" placeholder="部分一致"></dd>
                    <dt>価格: </dt>
                    <dd><input type="number" name="low_order_total" value="<?php print filter_input( INPUT_POST, 'low_order_total', FILTER_SANITIZE_STRING ); ?>" placeholder="以上"> - <input type="number" name="high_order_total" value="<?php print @$_POST['high_order_total']; ?>" placeholder="以下"></dd>
                    <dt>支払い方法: </dt>
                    <dd><input type="text" name="_payment_method_title" value="<?php print filter_input( INPUT_POST, '_payment_method_title', FILTER_SANITIZE_STRING ); ?>" placeholder="部分一致"></dd>
                    <dt>ステータス: </dt>
                    <dd><input type="text" name="post_status"value="<?php print filter_input( INPUT_POST, 'post_status', FILTER_SANITIZE_STRING ); ?>"  placeholder="完全一致"></dd>
                </dl>
            </div>
            <br clear="all">
            <p class="submit">
                <input name="ap-submit" id="ap-submit" class="button button-primary" value="検索" type="submit" />
            </p>
        </form>
    </div>
    <!-- #ap-link-wrap -->
    <div id="ap-link-wrap" class="hide-if-no-js screen-meta-toggle">
        <button type="button" id="ap-link" class="button show-settings" aria-controls="ap-wrap" aria-expanded="false">注文詳細検索</button>
    </div>
    <?php
}

function wc_jpec_get_order_date(){
    if($_POST['date_start'] || $_POST['date_end']){
        $start_date = str_replace('/', '-', filter_input( INPUT_POST, 'date_start', FILTER_SANITIZE_STRING ));
        $end_date = str_replace('/', '-', filter_input( INPUT_POST, 'date_end', FILTER_SANITIZE_STRING ));
        $array = array(
            array(
                'inclusive' => true,
                'after' => $start_date,
                'before' => $end_date,
            )
        );
        return $array;
    }
}
function wc_jpec_get_order_ids(){
    $id = filter_input( INPUT_POST, 'order_id', FILTER_VALIDATE_INT );
    return $id;
}
function wc_jpec_get_order_sku(){
    $sku = filter_input( INPUT_POST, '_sku', FILTER_SANITIZE_STRING );
    return $sku;
}
function wc_jpec_get_order_jan(){
    $jan = filter_input( INPUT_POST, '_jan', FILTER_SANITIZE_STRING );
    return $jan;
}
function wc_jpec_get_order_last_name() {
    $txt = filter_input( INPUT_POST, '_billing_last_name', FILTER_SANITIZE_STRING );
    return $txt;
}
function wc_jpec_get_order_first_name() {
    $txt = filter_input( INPUT_POST, '_billing_first_name', FILTER_SANITIZE_STRING );
    return $txt;
}
function wc_jpec_get_order_email() {
    $txt = filter_input( INPUT_POST, '_billing_email', FILTER_SANITIZE_STRING );
    return $txt;
}
function wc_jpec_get_order_phone() {
    $txt = filter_input( INPUT_POST, '_billing_phone', FILTER_SANITIZE_STRING );
    return $txt;
}
function wc_jpec_get_order_company() {
    $txt = filter_input( INPUT_POST, '_billing_company', FILTER_SANITIZE_STRING );
    return $txt;
}
function wc_jpec_get_order_low_total() {
    $num = filter_input( INPUT_POST, 'low_order_total', FILTER_VALIDATE_INT );
    return $num;
}
function wc_jpec_get_order_high_total() {
    $num = filter_input( INPUT_POST, 'high_order_total', FILTER_VALIDATE_INT );
    return $num;
}
function wc_jpec_get_order_payment_method() {
    $txt = filter_input( INPUT_POST, '_payment_method_title', FILTER_SANITIZE_STRING );
    return $txt;
}

function wc_jpec_get_order_post_status(){
    if($_POST['post_status']){

        $status_name = trim(filter_input( INPUT_POST, 'post_status', FILTER_SANITIZE_STRING ));
        if ($status_name == "新規注文") {
            $status = 'wc-new-order';
        } elseif ($status_name == "入金待ち") {
            $status = 'wc-waiting-payment';
        } elseif ($status_name == "処理中") {
            $status = 'wc-processing';
        } elseif ($status_name == "発送予約") {
            $status = 'wc-reserve_shipping';
        } elseif ($status_name == "発送済み") {
            $status = 'wc-finish-shipping';
        } elseif ($status_name == "完了") {
            $status = 'wc-completed';
        } elseif ($status_name == "キャンセル") {
            $status = 'wc-cancelled';
        } elseif ($status_name == "払い戻し") {
            $status = 'wc-refunded';
        } elseif ($status_name == "保留中") {
            $status = 'wc-on-hold';
        } elseif ($status_name == "注文失敗") {
            $status = 'wc-failed';
        } elseif ($status_name == "予約注文") {
            $status = 'wc-reserve_order';
        } else {
            $status = 1;
        }
        return $status;
    }else{
        return array('any');
    }
}

function wc_jpec_array_and() {
    //有効な配列のをカウント
    //有効な配列の値をキーとして、各配列ごとに足し算
    //有効な配列の数と、値のキーの合計が同じものを有効な値とする
    $ids = array();
    $temp = array();
    $array_count = 0;
    $arrays = func_get_args();

    foreach($arrays as $key=>$array) {
        if (count($array) == 0 || !isset($array[0])) {
            continue;
        }
        foreach($array as $id){
            if(isset($temp[$id])){
                $temp[$id] += 1;
            }else{
                $temp[$id] = 1;
            }
        }
        $array_count += 1;
    }
    foreach($temp as $key=>$value){
        if($value == $array_count){
            $ids[] = $key;
        }
    }    
    return $ids;
}

function wc_jpec_get_ids_from_search_query($type, $search_term, $wpdb){
    $prefix = $wpdb->prefix;
    $order_items_table = $prefix."woocommerce_order_items";
    $post_table = $prefix.'posts';
    $post_meta_table = $prefix.'postmeta';

    $returns = array();

    if ($type == "_id") {
        $query_str = "select distinct ID from $post_table where post_type = 'shop_order' and ID = '$search_term'";
        $results = $wpdb->get_results($query_str);
        if(count($results)){
            $returns[] = $results[0]->ID;
        } else {
            $returns[] = NULL;
        }
    }

    else {
        $query_str1 = "select distinct post_id from $post_meta_table where meta_value like '%$search_term%' and (meta_key = '$type')";
        $results1 = $wpdb->get_results($query_str1);
        $post_ids = array();
        foreach( $results1 as $_key => $value ) {
            $post_ids[] = (string) $value->post_id;
        }
        if (count($post_ids)){
            $query_post_ids = join(', ', $post_ids);
            $query_str2 = "select distinct post_title from $post_table where ID in ($query_post_ids)";
            $results2 = $wpdb->get_results($query_str2);
            $post_titles = array();
            foreach( $results2 as $_key => $value ) {
                $post_titles[] = "'".((string) $value->post_title)."'";
            }
            if (count($post_titles)){
                $query_post_titles = join(', ', $post_titles);
                $query_str3 = "select distinct order_id from $order_items_table where order_item_name in ($query_post_titles)";
                $results = $wpdb->get_results($query_str3);
                if(count($results)){
                    foreach($results as $result){
                        $returns[] = $result->order_id;
                    }
                } else {
                    $returns[] = NULL;
                }
            }
        }
    }
    return $returns;
}

add_action( 'pre_get_posts', 'wc_jpec_extend_admin_search' );
function wc_jpec_extend_admin_search( $query ) {

    if( ! is_admin() )
        return;

    if(@$query->query['post_type'] != "shop_order"){return;}


    $args = Array(
        'fields'=>'ids',
        'post_type' => array('shop_order'),
        //'post_status' => array('any'),
        'posts_per_page' => -1,
    );


    if(isset($_GET['show_unshipped']) && isset($_GET['ap-action'])==false){
        $_args = array('wc-new-order',
                'wc-waiting-payment','wc-processing',
                'wc-reserve_shipping','wc-on-hold',
                'wc-refunded','wc-failed','wc-reserve_order'
        );
        $query->set('post_status', $_args);
    }

    $meta_query = array();
    $search_terms = array();
    $meta_search_terms = array();

    if(isset($_POST['ap-submit'])){

        if ($date = wc_jpec_get_order_date()) {
            $args['date_query'] = $date;
        }
        if ($ids = wc_jpec_get_order_ids()) {
            $search_terms["_id"] = $ids;
        }
        if ($sku = wc_jpec_get_order_sku()) {
            $search_terms["_sku"] = $sku;
        }
        if ($jan = wc_jpec_get_order_jan()) {
            $search_terms["_jan"] = $jan;
        }
        if($last_name = wc_jpec_get_order_last_name()){
            $meta_search_terms['_billing_last_name'] = $last_name;
        }
        if($first_name = wc_jpec_get_order_first_name()){
            $meta_search_terms['_billing_first_name'] = $first_name;
        }
        if($email = wc_jpec_get_order_email()){
            $meta_search_terms['_billing_email'] = $email;
        }
        if($phone = wc_jpec_get_order_phone()){
            $meta_search_terms['_billing_phone'] = $phone;
        }
        if($company = wc_jpec_get_order_company()){
            $meta_search_terms['_billing_company'] = $company;
        }
        if($payment_method_title = wc_jpec_get_order_payment_method()){
            $meta_search_terms['_payment_method_title'] = $payment_method_title;
        }
        if($low_order_total = wc_jpec_get_order_low_total()){
            $meta_search_terms['low_order_total'] = $low_order_total;
        }
        if($high_order_total = wc_jpec_get_order_high_total()){
            $meta_search_terms['high_order_total'] = $high_order_total;
        }
        if ($post_status = wc_jpec_get_order_post_status()) {
            $args['post_status'] = $post_status;
        }
  
        foreach( $meta_search_terms as $custom_field => $meta_search_term ) {
            if ($custom_field == 'low_order_total') {
                array_push( $meta_query, array(
                    'key' => '_order_total',
                    'value' => $low_order_total,
                    'compare' => '>=',
                    'type' => 'UNSIGNED'
                ));
            } elseif ($custom_field == 'high_order_total') {
                array_push( $meta_query, array(
                    'key' => '_order_total',
                    'value' => $high_order_total,
                    'compare' => '<=',
                    'type' => 'UNSIGNED'
                ));
            } else {
                array_push( $meta_query, array(
                    'key' => $custom_field,
                    'value' => $meta_search_term,
                    'compare' => 'LIKE'
                ));
            }
        }

        if ($meta_query) {
            $args['meta_query'] = $meta_query;
        }
    }

    $query->query_vars['s'] = '';
    if ( count($search_terms) || count($meta_search_terms) || @$post_status || @$date) {
        global $wpdb;

        $skus = array();
        $jans = array();
        $ids = array();
        
        foreach($search_terms as $key => $search_term) {
            if ($key == "_sku") {
                $skus = wc_jpec_get_ids_from_search_query("_sku", $search_term, $wpdb);
            }
            elseif ($key == "_jan") {
                $jans = wc_jpec_get_ids_from_search_query("_jan", $search_term, $wpdb);
            }
            elseif ($key == "_id") {
                $ids = wc_jpec_get_ids_from_search_query("_id", $search_term, $wpdb);
            }
        }
   
        if(isset($args['meta_query']) || isset($args['date_query']) || $args['post_status'] != array('any')){
            $args['fields']='ids';
            $_status_and_meta_ids = new WP_Query($args);
            $status_and_meta_ids = $_status_and_meta_ids->posts;   
        }else{
            $status_and_meta_ids = array();
        }

        if ($post_status == 1){
            $combine_ids = wc_jpec_array_and($ids, $skus, $jans, array(NULL));
            //$combine_ids = array_merge($ids, $skus, $jans, $status_and_meta_ids);
        } else {
            if (!count($status_and_meta_ids)) {
                $status_and_meta_ids[] = NULL;
            }
            $combine_ids = wc_jpec_array_and($ids, $skus, $jans, $status_and_meta_ids);
            //$combine_ids = array_merge($ids, $skus, $jans, $status_and_meta_ids);
        }

        if(empty($combine_ids)){$combine_ids = array('dummy array');}
        $query->set('post__in', $combine_ids);
    }
}


add_filter( 'bulk_actions-edit-shop_order', 'wc_jpec_order_download_bulk_actions', 200 );
function wc_jpec_order_download_bulk_actions($bulk_actions) {
    $bulk_actions['send_shipping_email'] = '発送メール一括送信';
    if(function_exists('ap_download_header') == false || 
        function_exists('ap_download_first_row') == false ||
        function_exists('ap_download_rows') == false
        ){
        return $bulk_actions;
    }
    $bulk_actions[''] = '----';
    $bulk_actions['order_download'] = '注文情報ダウンロード';
    return $bulk_actions;
}

function wc_jpec_is_order_edit_page() {
    global $typenow, $pagenow;
    if( $typenow == 'shop_order' && $pagenow == 'edit.php' ) {
        return true;
    } else {
        return false;
    }
}

/**
 * ダウンロード機能・アップロード機能を提供する。
 * デフォルトのCSVダウンロード機能に項目を追加する場合は、各テーマに
 * add_custom_header_for_download　と add_custom_row_for_download
 * を定義して、そこに追加情報を入力する
 */

add_action('init', 'wc_jpec_ap_download_orders');
function wc_jpec_ap_download_orders(){
    if(@$_GET['action']  == "order_download" || @$_GET['action2']  == "order_download"):
        $path = dirname(__FILE__).'/master.csv';
        $fp = fopen($path, 'w');
        if(isset($_GET['post'])==false){return;}
        $orders = ($_GET['post']);
        $header_array =ap_download_header();
        mb_convert_variables('SJIS-win','UTF-8',$header_array);        
        fputcsv($fp, $header_array);
        foreach($orders as $order_id):
            $row_array = wc_jpec_make_row_array($order_id);
            foreach($row_array as $row):
                mb_convert_variables('SJIS-win','UTF-8',$row);
                fputcsv($fp, $row);
            endforeach;
        endforeach; 
        $filename = 'orders.csv';
        header('Content-Type: application/csv');
        header('Content-Length: '.filesize($path));
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        readfile($path);
        unlink($path);
        die();
    endif;
}

function wc_jpec_make_row_array($order_id){
    $order = wc_jpec_get_items($order_id);
    $array = array();
    $i = 0;
    foreach($order['item_array'] as $item):
        if($i==0):
            $array[] = wc_jpec_get_row($order, $item, 'first', $order_id);
            $i++;
        else:
            $array[] =  wc_jpec_get_row($order, $item, 'other', $order_id);
        endif;
    endforeach;
    return $array;
}

function wc_jpec_get_row($order, $item, $type, $order_id){
    if($type == "first"){
        $row_data = ap_download_first_row($order_id);
    }else{
        $row_data = ap_download_rows($order_id);
    }
    $array = array();
    foreach($row_data as $data){
        if(preg_match('/item-/', $data)){
            $data = preg_replace('/item-/','',$data);
            $array[] = $item[$data];
        }else{
            if(array_key_exists($data, $order)){
                $array[] = $order[$data];
            }else{
                $array[] = $data;
            }
        }
    }
    return $array;
}

function wc_jpec_get_items($order_id){
    
    $order = wc_get_order( $order_id );

    $order_data = $order->get_data(); 
    $order_id = $order_data['id'];
    $order_parent_id = $order_data['parent_id'];
    $order_status = $order_data['status'];
    $order_currency = $order_data['currency'];
    $order_version = $order_data['version'];
    $order_payment_method = $order_data['payment_method'];
    $order_payment_method_title = $order_data['payment_method_title'];
    $order_payment_method_title = apply_filters( 'wc-like-jp-ec_change_method_title', $order_payment_method_title );
    $order_payment_method = $order_data['payment_method'];
    
    $order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');
    $order_date_modified = $order_data['date_modified']->date('Y-m-d H:i:s');
    
    $order_timestamp_created = $order_data['date_created']->getTimestamp();
    $order_timestamp_modified = $order_data['date_modified']->getTimestamp();
        
    $order_discount_total = round($order_data['discount_total']);
    $order_discount_tax = $order_data['discount_tax'];
    $order_discount_total_with_tax = $order_discount_total + $order_discount_tax;

    $order_shipping_total = $order_data['shipping_total'];
    $order_shipping_tax = $order_data['shipping_tax'];
    $order_shipping_total_with_tax = $order_shipping_total+$order_shipping_tax;
    $order_total_tax = $order_data['total_tax'];
    $order_customer_id = $order_data['customer_id'];
        
    $order_billing_first_name = $order_data['billing']['first_name'];
    $order_billing_last_name = $order_data['billing']['last_name'];
    $order_billing_company = $order_data['billing']['company'];
    $order_billing_address_1 = $order_data['billing']['address_1'];
    $order_billing_address_2 = $order_data['billing']['address_2'];
    $order_billing_city = $order_data['billing']['city'];
    $order_billing_state = $order_data['billing']['state'];
    $order_billing_postcode = $order_data['billing']['postcode'];
    $order_billing_country = $order_data['billing']['country'];
    $order_billing_email = $order_data['billing']['email'];
    $order_billing_phone = $order_data['billing']['phone'];
    $order_billing_company = get_post_meta($order_id, '_billing_company', true);

    $countries_obj   = new WC_Countries();
    $states = $countries_obj->get_states( $order_billing_country );

    if(preg_match('/JP/', $order_billing_state)){
        $order_billing_pref = $states[$order_billing_state];
    }else{
        $order_billing_pref =  $order_billing_state;
    }
    
    $order_shipping_first_name = $order_data['shipping']['first_name'];
    $order_shipping_last_name = $order_data['shipping']['last_name'];
    $order_shipping_company = $order_data['shipping']['company'];
    $order_shipping_address_1 = $order_data['shipping']['address_1'];
    $order_shipping_address_2 = $order_data['shipping']['address_2'];
    $order_shipping_city = $order_data['shipping']['city'];
    $order_shipping_state = $order_data['shipping']['state'];
    $order_shipping_postcode = $order_data['shipping']['postcode'];
    $order_shipping_country = $order_data['shipping']['country'];
    $order_shipping_phone = get_post_meta($order_id, '_shipping_phone', true);
    $order_shipping_company = get_post_meta($order_id, '_shipping_company', true);

    if(preg_match('/JP/', $order_shipping_state)){
        $order_shipping_pref = $states[$order_shipping_state];
    }else{
        $order_shipping_pref =  $order_shipping_state;
    }

    $post = get_post($order_id);
    $order_notes = $post->post_excerpt;
    
    $item_array = array();
    $item_total = 0;
    $item_total_tax = 0;
    $item_total_with_tax = 0;
    foreach ($order->get_items() as $item_key => $item_values):

        $item_id = $item_values->get_id();
        $item_name = $item_values->get_name(); 
        $item_type = $item_values->get_type(); 
    
        $product_id = $item_values->get_product_id(); 
        $wc_product = $item_values->get_product(); 
        $item_data = $item_values->get_data();

        $jan = get_post_meta($product_id, '_jan', true);    
        $sku = get_post_meta($product_id, '_sku', true);    

        $product_name = $item_data['name'];
        $product_id = $item_data['product_id'];
        $variation_id = $item_data['variation_id'];
        $quantity = $item_data['quantity'];
        $tax_class = $item_data['tax_class'];
        $line_subtotal = $item_data['subtotal'];
        $line_subtotal_tax = $item_data['subtotal_tax'];
        //$unit_price = round(($line_subtotal+$line_subtotal_tax)/$quantity);
        $line_total = $item_data['total'];
        $line_total_tax = $item_data['total_tax'];
        $line_total_with_tax = $line_total + $line_total_tax;

        $diff_total = round($line_subtotal - $line_total, 2);
        $diff_total_tax = round($line_subtotal_tax - $line_total_tax, 2);
        $unit_price = round(($line_total+$diff_total+$line_total_tax+$diff_total_tax)/$quantity, 2);

        //$item_total += round($line_total+$diff_total, 2);
        //$item_total_tax += round($line_total_tax+$diff_total_tax, 2);
        $item_total_with_tax += round($unit_price * $quantity);

        if($variation_id){
            $available_variations = wc_get_product( $variation_id );
            $item_name = $available_variations->get_name();
            $product_name = $available_variations->get_name();
            $product_id = $variation_id;
            $jan = get_post_meta($variation_id, '_jan', true);    
            $sku = $available_variations->get_sku();    
        }
        $item_array[] = compact('item_id', 'item_name', 'product_id', 'jan', 'sku',
                            'product_name','line_subtotal','line_total', 'quantity', 
                            'line_total_tax','line_total_with_tax','unit_price');
    
    endforeach;
    //$item_total_with_tax = round($item_total+$item_total_tax);

    $fee_array = array();
    $fees = $order->get_items('fee');
    $total_fee = 0;
    $total_fee_tax = 0;
    foreach($fees as $fee){
        $fee_name = $fee->get_name();
        $fee_amount = $fee->get_amount();
        $fee_tax = $fee->get_total_tax();
        $fee_array[] = compact('fee_name', 'fee_amount','fee_tax');
        $total_fee += $fee_amount;
        $total_fee_tax += $fee_tax;
    }
    $total_fee_with_tax = $total_fee + $total_fee_tax;

    $customer_shipping_date = get_post_meta($order_id, 'wc4jp-delivery-date', true);
    $customer_shipping_time = get_post_meta($order_id, 'wc4jp-delivery-time-zone', true);
    $shipping_date = get_post_meta($order_id, 'woocommerce_shipping_date', true);
    $shipping_shipper = get_post_meta($order_id, 'woocommerce_shipper', true);
    $shipping_tracking = get_post_meta($order_id, 'woocommerce_shipping_num', true);
        
    $user_id = $order->get_user_id();
    $user_login = ($user_id)?get_userdata( $user_id )->user_login:"";

    $order_total = round($item_total+$order_shipping_total+$total_fee);
    //$order_total_with_tax = round($order_total+$order_total_tax-$order_discount_total);
    $order_total_with_tax = $order->get_total();

    //woocommerceのポイント計算が、商品ごとにポイントを割り振るため消費税計算で誤差がでる。ここで最終調整を行う
    $total_diff = $order_total_with_tax + $order_discount_total_with_tax - $order_shipping_total_with_tax - $item_total_with_tax - $total_fee_with_tax;
    $order_discount_total_with_tax = $order_discount_total_with_tax - $total_diff;    

    $order_discount_total_with_tax = apply_filters('customise_order_discount_tota', $order_discount_total_with_tax, $order);

    $array = compact('item_array', 'variation_array', 
        "order_id",
        "order_status",
        "order_currency",
        "order_version",
        "order_payment_method",
        "order_payment_method_title",
        "order_payment_method",      
        "order_date_created",
        "order_date_modified",       
        "order_timestamp_created",
        "order_timestamp_modified",          
        "order_discount_total",
        "order_discount_tax",
        "order_discount_total_with_tax",
        "order_shipping_total",
        "order_shipping_tax",
        "order_total_tax",
        "order_customer_id",         
        "order_billing_first_name",
        "order_billing_last_name",
        "order_billing_company",
        "order_billing_address_1",
        "order_billing_address_2",
        "order_billing_city",
        "order_billing_state",
        "order_billing_postcode",
        "order_billing_country",
        "order_billing_email",
        "order_billing_phone",   
        "order_shipping_first_name",
        "order_shipping_last_name",
        "order_shipping_company",
        "order_shipping_address_1",
        "order_shipping_address_2",
        "order_shipping_city",
        "order_shipping_state",
        "order_shipping_postcode",
        "order_shipping_country",
        "order_shipping_phone",

        "order_billing_pref",
        "order_shipping_pref",
        'fee_array',
        'customer_shipping_date',
        'customer_shipping_time',
        'shipping_date',
        'shipping_shipper',
        'shipping_tracking',
        'order_billing_company',
        'order_shipping_company',
        'user_id',
        'user_login',
        'order_notes',
        'total_fee',
        'total_fee_tax',
        'total_fee_with_tax',
        'order_shipping_total_with_tax',
        'item_total',
        'item_total_tax',
        'item_total_with_tax',
        'order_total',
        'order_total_with_tax'
    );

    return $array;
}


add_action( 'admin_menu','wc_jpec_settings_menu' , 100 );
function wc_jpec_settings_menu() {
        if(is_plugin_active( 'woocommerce_like_for_JP_EC/woocommerce_like_for_jp_ec.php' )){
            add_submenu_page( 'woocommerce',  '注文アップロード', '注文アップロード', 'manage_woocommerce', 'upload_wc_orders', 'wc_jpec_upload_wc_orders' );
        }else{
            print '<div id="wc_jpec_upload_wc_orders">please activate "woocommerce_like_for_jp_ec".</div>';
        }    
}
function wc_jpec_upload_wc_orders(){
    if(isset($_FILES['order_file'])){
        wc_jpec_update_wc_orders_action();
    }
    ?>
    <div id="wc_jpec_upload_wc_orders">
        <h1>注文アップロード</h1>
        <p>このページでは、以下の登録をファイルアップロードを使って行います。</p>
        <ul>
            <li>問い合わせ伝票番号</li>
            <li>運送会社</li>
            <li>発送日</li>
        </ul>
        <h2>ファイル形式</h2>
        <p>注文番号,問い合わせ伝票番号,運送会社,発送日</p>

        <h2>CSV作成例</h2>
        <p>注文番号,問い合わせ伝票番号,運送会社,発送日<br>
        1234546,45539136141,佐川急便,2018/12/21</p>

        <?php if($_FILES): ?>
            <div class="upload_success">アップロードしました。</div>
        <?php endif; ?>
        <form method="POST" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" enctype="multipart/form-data" id="wc_jpec_upload_wc_orders_form">
            <input type="file" name="order_file" size="30">  
            <input type="submit" value="登録">            
        </form>
        
    </div>
    <?php
}

function wc_jpec_update_wc_orders_action(){
    $file = $_FILES['order_file']['tmp_name'];
    $data = file_get_contents($file);
    $data = mb_convert_encoding($data, 'UTF-8', 'sjis-win, shift-jis, utf-8');
    $temp = tmpfile();
        
    fwrite($temp, $data);
    rewind($temp);
        
    while (($data = fgetcsv($temp, 0, ",")) !== FALSE) {
        $order_id = trim($data[0]);
        $woocommerce_shipping_num = trim($data[1]);
        $woocommerce_shipper = trim($data[2]);
        $woocommerce_shipping_date = trim($data[3]);
        $array = compact("woocommerce_shipping_num","woocommerce_shipper","woocommerce_shipping_date");
        foreach($array as $key=>$value){
            update_post_meta($order_id, $key, $value);
        }
    }
    fclose($temp);
        
}
