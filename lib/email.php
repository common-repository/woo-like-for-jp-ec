<?php

function wc_jpec_add_comment_after_email(){
    if(is_plugin_active( 'flamingo/flamingo.php' )){return;}
    $order = wc_get_order();
    $title = filter_input( INPUT_POST, 'this_email_title', FILTER_SANITIZE_STRING );
    //↓第三引数が空だと、メモ表示箇所で .system　のクラスが追加される。
    //この箇所では .systemクラスはdisplay:noneにしている。
    $order->add_order_note('【メール送信履歴】 '.$title.' のメールを送信しました。', 0, 'program');
    $order->save();
}

function wc_jpec_send_shipping_email_notice() {
    ?>
    <div class="notice notice-success">
        <p><?php print 'メールを送信しました'; ?></p>
    </div>
    <?php
}

add_action('init', 'wc_jpec_send_shpping_email');
function wc_jpec_send_shpping_email(){
    if(@$_GET['action']  == "send_shipping_email" || @$_GET['action2']  == "send_shipping_email" ):
        if(isset($_GET['post'])==false){return;}
        $orders = ($_GET['post']);
        foreach($orders as $order_id):
            $order_id = sanitize_text_field($order_id);
            wc_jpec_send_shipping_email_action($order_id);
        endforeach;
        $url = $_SERVER['HTTP_REFERER'];
        header('Location: '.$url.'&send_mail_action=1');
        exit;
    endif;
    if(isset($_GET['send_mail_action'])){
        add_action( 'admin_notices', 'wc_jpec_send_shipping_email_notice' );
    }
}

function wc_jpec_send_shipping_email_action($order_id){
    $instance = new Wc_jpec_check_email_before_send();
    $type = "一括発送連絡";
    $instance->order_id = $order_id;
    $instance->order = wc_get_order( $order_id );
    $instance->order_meta = get_post_meta($order_id);
    $options = get_option( "wcemails_email_details" );
    $array = $instance->get_this_email_title($type, $options);
    $this_email_title = $array[0];
    $shipping_mail_flag = $array[1];
    $cc_mail_flag = $array[2];
    @$title = $instance->get_title($type, $options);
    if($title==""){wp_die( '一括発送用メールには、「一括発送連絡」というタイトルのメールが必要です。' );}
    @$title = $instance->replace_tag($title);
    @$content = $instance->get_content($type, $options);
    @$content = $instance->replace_tag($content)."\n\n".$instance->get_footer();
    $from = get_option('wcemails_new_order_admin_email');
    $sender_name = (get_option('wcemails_new_order_admin_name'))?get_option('wcemails_new_order_admin_name'):$blogname;
    @$send_to = $instance->order->billing_email;
    $headers = "From: \"{$sender_name}\" <{$from}>\n";
    if ($cc_mail_flag != 1){
        $cc_mail = get_option('wcemails_new_order_admin_email');
        $headers .= "Cc: $cc_mail\n";
    }
    $headers .= "Content-Type: text/plain; charset=\"UTF-8\"\n";
    @$headers .= "Content-Type: text/plain; charset=\"UTF-8\"\n";
    wp_mail($send_to, $title, $content, $headers);
    if ( $shipping_mail_flag == 1 ){
        add_post_meta( $order_id, 'ap_send_shipping_mail', '1');
    }
}


