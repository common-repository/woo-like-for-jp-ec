<?php

function wc_jpec_wcemails_settings_menu() {

    add_submenu_page( 'woocommerce', __( 'Woo Custom Emails', WC_JPEC_TEXT_DOMAIN ), __( 'Custom Emails', WC_JPEC_TEXT_DOMAIN ), 'manage_woocommerce', 'wcemails-settings', 'wc_jpec_emails_settings_callback' );

}

/**
 * woocommerce active check
 */
function wc_jpec_emails_woocommerce_check() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        ?><h2><?php _e( 'WooCommerce is not activated!', WC_JPEC_TEXT_DOMAIN );?></h2><?php
        die();
    }
}

function wc_jpec_show_tag_list_html(){
    ?>
    <th scope="row">
        <?php _e( 'Template', WC_JPEC_TEXT_DOMAIN ); ?>
        <span style="display: block; font-size: 12px; font-weight: 300;">
        <?php _e( '( タグ - <br/>
                    <i>注文日：{order_date},<br>
                    注文番号：{order_number},<br>
                    注文者名：{order_billing_name},<br>
                    注文会社名：{order_billing_company},<br>
                    Eメール：{order_billing_email},<br>
                    電話番号：{order_billing_phone},<br>
                    住所：{addresses},<br>
                    お届け先名：{order_shipping_name},<br>
                    お届け先会社名：{order_shipping_company},<br>
                    お届け先電話：{order_shipping_phone},<br>
                    お届け先住所：{shipping_addresses},<br>
                    お届け希望日：{shipping_addresses},<br>
                    お届け時間帯：{shipping_addresses},<br>
                    商品情報：{order_items},<br>
                    支払い方法：{order_payment_method},<br>
                    注文情報：{email_order_total_plain},<br>
                    発送日：{order_shipping_date},<br>
                    注文メモ：{order_customer_note},<br>
                    自由本文１：{order_custom_content},<br>
                    自由本文２：{order_custom_content2},<br>
                    運送会社：{order_shipper},<br>
                    運送会社伝票番号：{order_shipping_number},
                    </i> )' ); ?>
            </span>
    </th>
    <?php
}

function wc_jpec_emails_render_add_email_section() {

    $wcemails_detail = array();
    if ( isset( $_REQUEST['wcemails_edit'] ) ) {
        $wcemails_email_details = get_option( 'wcemails_email_details', array() );
        if ( ! empty( $wcemails_email_details ) ) {
            foreach ( $wcemails_email_details as $key => $details ) {
                if ( $_REQUEST['wcemails_edit'] == $key ) {
                    $wcemails_detail = $details;
                    $wcemails_detail['template'] = stripslashes( $wcemails_detail['template'] );
                }
            }
        }
    }

    $wc_statuses = wc_get_order_statuses();
    if ( ! empty( $wc_statuses ) ) {
        foreach ( $wc_statuses as $k => $status ) {
            $key = ( 'wc-' === substr( $k, 0, 3 ) ) ? substr( $k, 3 ) : $k;
            $wc_statuses[ $key ] = $status;
            unset( $wc_statuses[ $k ] );
        }
    }

    wp_enqueue_script( 'jquery-cloneya' );
    wp_enqueue_script( 'wcemails-custom-scripts' );

    ?>
    <form method="post" action="">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row" style="width:400px!important;">
                    <?php _e( 'Title', WC_JPEC_TEXT_DOMAIN ); ?>
                    <span style="display: block; font-size: 12px; font-weight: 300;">
                    <?php _e( '( Title of the Email. )', WC_JPEC_TEXT_DOMAIN ); ?>
                        </span>
                </th>
                <td>
                    <input name="wcemails_title" id="wcemails_title" type="text" required value="<?php echo isset( $wcemails_detail['title'] ) ? $wcemails_detail['title'] : ''; ?>" placeholder="<?php _e( 'Title', WC_JPEC_TEXT_DOMAIN ); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php _e( 'Description', WC_JPEC_TEXT_DOMAIN ); ?>
                    <span style="display: block; font-size: 12px; font-weight: 300;">
                    <?php _e( '( Email Description to display at Woocommerce Email Setting. )', WC_JPEC_TEXT_DOMAIN ); ?>
                        </span>
                </th>
                <td>
                    <textarea name="wcemails_description" id="wcemails_description" required placeholder="<?php _e( 'Description', WC_JPEC_TEXT_DOMAIN ); ?>" ><?php echo isset( $wcemails_detail['description'] ) ? $wcemails_detail['description'] : ''; ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php _e( 'Subject', WC_JPEC_TEXT_DOMAIN ); ?>
                    <span style="display: block; font-size: 12px; font-weight: 300;">
                    <?php _e( '( Email Subject <br/>[Try this placeholders : <i>{site_title}, {order_number}, {order_date}</i>] )', WC_JPEC_TEXT_DOMAIN ); ?>
                        </span>
                </th>
                <td>
                    <input name="wcemails_subject" id="wcemails_subject" type="text" required value="<?php echo isset( $wcemails_detail['subject'] ) ? $wcemails_detail['subject'] : ''; ?>" placeholder="<?php _e( 'Subject', WC_JPEC_TEXT_DOMAIN ); ?>" style="width:100%;" />
                </td>
            </tr>
            <tr>
                <?php wc_jpec_show_tag_list_html(); ?>
                <td>
                    <textarea name="wcemails_template" rows="20" style="width:100%;"><?php print ( isset( $wcemails_detail['template'] ) ? $wcemails_detail['template'] : '' ); ?></textarea><br>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php print '発送メール'; ?>
                    <span style="display: block; font-size: 12px; font-weight: 300;">
                    <?php print 'この項目にチェックを付けると、送信時に発送メール送信ステータスが付与されます。'; ?>
                        </span>
                </th>
                <td>
                    <?php
                    @$shipping_checked = get_option( 'wcemails_email_details', array() )[$_REQUEST['wcemails_edit']]['shipping_send_mail_flag'];
                    if ($shipping_checked == 1):
                        print '<input name="wcemails_mail_flag" id="wcemails_mail_flag" type="checkbox" checked="checked" value="1" />';
                    else:
                        print '<input name="wcemails_mail_flag" id="wcemails_mail_flag" type="checkbox"  value="1" />';
                    endif;
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php print '管理者メール解除'; ?>
                    <span style="display: block; font-size: 12px; font-weight: 300;">
                    <?php print 'この項目にチェックを付けると、送信時に管理者へCCが送られなくなります。'; ?>
                        </span>
                </th>
                <td>
                    <?php
                    @$cc_checked = get_option( 'wcemails_email_details', array() )[$_REQUEST['wcemails_edit']]['cc_admin_flag'];
                    if ($cc_checked == 1):
                        print '<input name="wccc_admin_flag" id="wccc_admin_flag" type="checkbox" checked="checked" value="1" />';
                    else:
                        print '<input name="wccc_admin_flag" id="wccc_admin_flag" type="checkbox" value="1" />';
                    endif;
                    ?>
                </td>
            </tr>

            <input name="wcemails_enable" id="wcemails_enable" type="hidden" checked="checked" />
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="wcemails_submit" id="wcemails_submit" class="button button-primary" value="<?php _e('Save Changes', WC_JPEC_TEXT_DOMAIN); ?>">
        </p>
        <?php
        if ( isset( $_REQUEST['wcemails_edit'] ) ) {
            ?>
            <input type="hidden" name="wcemails_update" id="wcemails_update" value="<?php echo $_REQUEST['wcemails_edit']; ?>" />
            <?php
        }
        ?>
    </form>
    <?php

}

function wc_jpec_emails_render_view_email_section() {
    $wcemails_list = new WC_JPEC_Emails_List();
    $wcemails_list->prepare_items();
    $wcemails_list->display();
}

function wc_jpec_email_render_view_new_email_section(){
    wp_enqueue_script( 'jquery-cloneya' );
    wp_enqueue_script( 'wcemails-custom-scripts' );

    ?>
    <br clear="all">
    <br clear="all">

    <form method="post" action="<?php print $_SERVER["REQUEST_URI"]; ?>">
        <table class="form-table">
        <tr>
                    <th scope="row">
                        <?php _e( 'Heading', WC_JPEC_TEXT_DOMAIN ); ?>
                    </th>
                    <td>
                        <?php $admin_email = get_option('wcemails_new_order_admin_email'); ?>
                        <input name="wcemails_new_order_admin_email" id="wcemails_new_order_admin_email" type="text" required value="<?php echo ( $admin_email ) ? $admin_email : get_option('admin_email'); ?>" placeholder="<?php _e( 'Heading', WC_JPEC_TEXT_DOMAIN ); ?>" />
                    </td>
            </tr>
            <tr>
                    <th scope="row">
                        送信者名
                    </th>
                    <td>
                        <?php $sender_name = (get_option('wcemails_new_order_admin_name'))?get_option('wcemails_new_order_admin_name'):get_bloginfo(); ?>
                        <input name="wcemails_new_order_admin_name" id="wcemails_new_order_admin_name" type="text" required value="<?php print $sender_name; ?>" placeholder="送信者名" />
                    </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php _e( 'Subject', WC_JPEC_TEXT_DOMAIN ); ?>
                    <span style="display: block; font-size: 12px; font-weight: 300;">
                    <?php _e( '( Email Subject <br/>[Try this placeholders : <i>{site_title}, {order_number}, {order_date}</i>] )', WC_JPEC_TEXT_DOMAIN ); ?>
                        </span>
                </th>
                <td>
                    <?php $content = get_option('wcemails_new_order_subject'); ?>
                    <input name="wcemails_new_order_subject" id="wcemails_new_order_subject" type="text" required value="<?php echo ($content ) ? $content : ''; ?>" placeholder="<?php _e( 'Subject', WC_JPEC_TEXT_DOMAIN ); ?>" style="width:100%;" />
                </td>
            </tr>
            <tr>
                <?php wc_jpec_show_tag_list_html(); ?>
                <td>

        <?php $content = get_option('wcemails_new_order_template'); ?>
                <textarea name="wcemails_new_order_template" rows="20" style="width:100%;"><?php print ( esc_textarea($content ) ? $content : '' ) ?></textarea><br>

                </td>
        </tr>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="wcemails_submit" class="button button-primary" value="<?php _e('Save Changes', WC_JPEC_TEXT_DOMAIN); ?>">
        </p>			
    </form>
    <?php
}


function wc_jpec_email_render_footer_setting_section(){
    wp_enqueue_script( 'jquery-cloneya' );
    wp_enqueue_script( 'wcemails-custom-scripts' );

    ?>
    <br clear="all">
    <br clear="all">
    
    <h2><?php _e( 'Footer Setting', WC_JPEC_TEXT_DOMAIN ); ?></h2>

    <br clear="all">

    <form method="post" action="<?php print $_SERVER["REQUEST_URI"]; ?>">
        <table class="form-table">
            <tr>
                <td>
                <?php $content = get_option('ap_wcemails_footer_setting_templateorder'); ?>
                <textarea name="ap_wcemails_footer_setting_templateorder" rows="20" style="width:50%;"><?php print ( esc_textarea($content ) ? $content : '' ) ?></textarea><br>
                </td>
        </tr>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="wcemails_submit" class="button button-primary" value="<?php _e('Save Changes', WC_JPEC_TEXT_DOMAIN); ?>">
        </p>			
    </form>
    <?php
}


function wc_jpec_emails_render_sectionson( $type ) {

    if ( 'add-email' == $type ) {
        wc_jpec_emails_render_add_email_section();
    } else if ( 'view-email' == $type ) {
        wc_jpec_emails_render_view_email_section();
    } else if( 'new-order-email' == $type ) {
        wc_jpec_email_render_view_new_email_section();
    } else if( 'footer-setting' == $type ) {
        wc_jpec_email_render_footer_setting_section();
    }else {
        wc_jpec_emails_render_add_email_section();
    }

}


function wc_jpec_emails_settings_callback() {

    wc_jpec_emails_woocommerce_check();

    ?>
    <div class="wrap">
        <h2><?php _e( 'Woocommerce Custom Emails Settings', WC_JPEC_TEXT_DOMAIN ); ?></h2>
        <?php
        if ( ! isset( $_REQUEST['type'] ) ) {
            $type = 'today';
        } else {
            $type = $_REQUEST['type'];
        }
        $all_types = array( 'add-email', 'view-email', 'new-order-email', 'footer-setting' );
        if ( ! in_array( $type, $all_types ) ) {
            $type = 'add-email';
        }
        ?>
        <ul class="subsubsub">
            <li class="today"><a class ="<?php echo ( 'add-email' == $type ) ? 'current' : ''; ?>" href="<?php echo add_query_arg( array( 'type' => 'add-email' ), admin_url( 'admin.php?page=wcemails-settings' ) ); ?>"><?php _e( 'Add Custom Emails', WC_JPEC_TEXT_DOMAIN ); ?></a> |</li>
            <li class="today"><a class ="<?php echo ( 'new-order-email' == $type ) ? 'current' : ''; ?>" href="<?php echo add_query_arg( array( 'type' => 'new-order-email' ), admin_url( 'admin.php?page=wcemails-settings' ) ); ?>"><?php _e( 'New Order Email', WC_JPEC_TEXT_DOMAIN ); ?></a> |</li>
            <li class="today"><a class ="<?php echo ( 'view-email' == $type ) ? 'current' : ''; ?>" href="<?php echo add_query_arg( array( 'type' => 'view-email' ), admin_url( 'admin.php?page=wcemails-settings' ) ); ?>"><?php _e( 'View Your Custom Emails', WC_JPEC_TEXT_DOMAIN ); ?></a> |</li>
            <li class="today"><a class ="<?php echo ( 'footer-setting' == $type ) ? 'current' : ''; ?>" href="<?php echo add_query_arg( array( 'type' => 'footer-setting' ), admin_url( 'admin.php?page=wcemails-settings' ) ); ?>"><?php _e( 'Footer Setting', WC_JPEC_TEXT_DOMAIN ); ?></a></li>
        </ul>
        <?php wc_jpec_emails_render_sectionson( $type ); ?>
    </div>
    <?php

}

/**
 * Save email options
 */
function wc_jpec_wcemails_email_actions_details() {

    if ( isset( $_POST['wcemails_submit'] ) ) {

        $title         = filter_input( INPUT_POST, 'wcemails_title', FILTER_SANITIZE_STRING );
        $description   = filter_input( INPUT_POST, 'wcemails_description', FILTER_SANITIZE_STRING );
        $subject       = filter_input( INPUT_POST, 'wcemails_subject', FILTER_SANITIZE_STRING );
        $recipients    = filter_input( INPUT_POST, 'wcemails_recipients', FILTER_SANITIZE_STRING );
        $heading       = filter_input( INPUT_POST, 'wcemails_heading', FILTER_SANITIZE_STRING );
        $template      = filter_input( INPUT_POST, 'wcemails_template', FILTER_SANITIZE_STRING );
        $order_action  = filter_input( INPUT_POST, 'wcemails_order_action', FILTER_SANITIZE_STRING );
        $order_action  = empty( $order_action ) ? 'off' : $order_action;
        $enable        = filter_input( INPUT_POST, 'wcemails_enable', FILTER_SANITIZE_STRING );
        $enable        = empty( $enable ) ? 'off' : $enable;
        $send_customer = filter_input( INPUT_POST, 'wcemails_send_customer', FILTER_SANITIZE_STRING );
        $send_customer = empty( $send_customer ) ? 'off' : $send_customer;
        $shipping_send_mail_flag = filter_input( INPUT_POST, 'wcemails_mail_flag', FILTER_SANITIZE_STRING );
        $cc_admin_flag = filter_input( INPUT_POST, 'wccc_admin_flag', FILTER_SANITIZE_STRING );
        $wcemails_email_details = get_option( 'wcemails_email_details', array() );


        $data = array(
            'title'         => $title,
            'description'   => $description,
            'subject'       => $subject,
            'recipients'    => $recipients,
            'heading'       => $heading,
            'template'      => $template,
            'order_action'  => $order_action,
            'enable'        => $enable,
            'send_customer' => $send_customer,
            'shipping_send_mail_flag' => $shipping_send_mail_flag,
            'cc_admin_flag' => $cc_admin_flag,
        );

        if ( isset( $_POST['wcemails_update'] ) ) {
            if ( ! empty( $wcemails_email_details ) ) {
                foreach ( $wcemails_email_details as $key => $details ) {
                    if ( $key == filter_input( INPUT_POST, 'wcemails_update', FILTER_SANITIZE_STRING ) ) {
                        $data['id'] = $details['id'];
                        $wcemails_email_details[ $key ] = $data;
                    }
                }
            }
        } else {
            $id = uniqid( 'wcemails' );
            $data['id'] = $id;
            array_push( $wcemails_email_details, $data );
        }

        update_option( 'wcemails_email_details', $wcemails_email_details );

        add_settings_error( 'wcemails-settings', 'error_code', $title.' is saved and if you have enabled it then you can see it in Woocommerce Email Settings Now', 'success' );

    } else if ( isset( $_REQUEST['wcemails_delete'] ) ) {

        $wcemails_email_details = get_option( 'wcemails_email_details', array() );

        $delete_key = $_REQUEST['wcemails_delete'];

        if ( ! empty( $wcemails_email_details ) ) {
            foreach ( $wcemails_email_details as $key => $details ) {
                if ( $key == $delete_key ) {
                    unset( $wcemails_email_details[ $key ] );
                }
            }
        }

        update_option( 'wcemails_email_details', $wcemails_email_details );

        add_settings_error( 'wcemails-settings', 'error_code', 'Email settings deleted!', 'success' );

    }

}

function wc_jpec_wcemails_email_actions_for_new_order(){
    if(isset($_POST['wcemails_new_order_template'])){
        $admin_email = filter_input( INPUT_POST, 'wcemails_new_order_admin_email', FILTER_SANITIZE_STRING ) ;
        update_option( 'wcemails_new_order_admin_email', $admin_email );
        $sender_name = filter_input( INPUT_POST, 'wcemails_new_order_admin_name', FILTER_SANITIZE_STRING ) ;
        update_option( 'wcemails_new_order_admin_name', $sender_name );
        $content = filter_input( INPUT_POST, 'wcemails_new_order_subject', FILTER_SANITIZE_STRING ) ;
        update_option( 'wcemails_new_order_subject', $content );
        $content = filter_input( INPUT_POST, 'wcemails_new_order_template', FILTER_SANITIZE_STRING ) ;
        update_option( 'wcemails_new_order_template', $content );
    }
}

function wc_jpec_wcemails_footer_setting_templateorder(){
    if(isset($_POST['ap_wcemails_footer_setting_templateorder'])){
        $content = filter_input( INPUT_POST, 'ap_wcemails_footer_setting_templateorder', FILTER_SANITIZE_STRING ) ;
        update_option( 'ap_wcemails_footer_setting_templateorder', $content );
    }			
}