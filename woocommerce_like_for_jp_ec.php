<?php
/*
Plugin Name: Woocommerce Like For JP EC
Plugin URI:
Description: This plugin makes Woocommerce EC like JP EC
Version: 3.0
Author: AutoProject
Author http://www.autoproject.nagoya
Tags: woocommerce, japan, email, order, download
Requires at least: 5.0.2
Tested up to: 5.0.2
Stable tag: 1.0
License: GPL2
*/


require_once dirname(__FILE__).'/lib/email.php';
require_once dirname(__FILE__).'/lib/order_status.php';
require_once dirname(__FILE__).'/lib/orders.php';
require_once dirname(__FILE__).'/lib/payment.php';
require_once dirname(__FILE__).'/lib/products.php';

function wc_jpec_style(){ 
    wp_enqueue_style('wc-like-jp-ec', plugins_url('woocommerce_like_for_JP_EC/css/like_style.css'), array(), 1.2);
    wp_enqueue_script('wc-like-jp-ec-js', plugins_url('woocommerce_like_for_JP_EC/js/like_script.js'), array(), 1.298);
  }
add_action ('admin_head', 'wc_jpec_style', 1);  


function wc_jpec_style_front(){
  wp_enqueue_script('wc-like-jp-ec', plugins_url('woocommerce_like_for_JP_EC/js/like_script_front.js'));
}
add_action ('wp_head', 'wc_jpec_style_front', 1);  


/** status関連のアクション */
add_action( 'init', 'wc_jpec_register_custom_order_status', 1 );
add_filter( 'wc_order_statuses', 'wc_jpec_add_custom_order_statuses', 1);
add_action( 'woocommerce_thankyou', 'wc_jpec_make_the_order_status_new');

add_action('wc_check_email_after_send_email', 'wc_jpec_add_comment_after_email');


/**カスタムメール作成部分 */
require_once dirname(__FILE__).'/lib/ap_functions.php';
include_once( dirname(__FILE__).'/lib/ap-class-wcemails-list.php' );
define( 'WC_JPEC_TEXT_DOMAIN', 'wc_jpec' );

load_plugin_textdomain( 'wc_jpec', false, basename( dirname( __FILE__ ) ) . '/languages' );


add_action( 'admin_menu', 'wc_jpec_wcemails_settings_menu', 100 );
add_action( 'admin_init', 'wc_jpec_wcemails_email_actions_details' );
add_action( 'admin_init', 'wc_jpec_wcemails_email_actions_for_new_order' );
add_action( 'admin_init', 'wc_jpec_wcemails_footer_setting_templateorder' );


/**確認メール部分 */
load_plugin_textdomain( 'wc_jpec_check_email_before_send', false, basename( dirname( __FILE__ ) ) . '/languages' );

require_once dirname(__FILE__) . '/lib/check_email.php';

$instance = new Wc_jpec_check_email_before_send();

function wc_jpec_check_email_styles_script() {
    wp_enqueue_style( 'woocommerce_check_email_styles', plugins_url( 'css/check_mail_style.css', __FILE__ ), array(), null, 'all');
    wp_enqueue_script( 'woocommerce_check_email_script', plugins_url( 'js/check_mail.js', __FILE__ ), array(), '3.1095', true);
}
add_action( 'admin_enqueue_scripts', 'wc_jpec_check_email_styles_script');


?>