<?php

/* 代引き手数料の追加　start 
	 -woocommerce for JP　のCOD設定は行わないこと
	 -独自の料金を設定したい時は、各テーマに　ap_change_cod_fee（引数 $add, $total）　を定義すること
*/
add_action( 'woocommerce_cart_calculate_fees', 'wc_jpec_add_cod_fee' );
function wc_jpec_add_cod_fee( $cart ){
	if ( ! $_POST || ( is_admin() && ! is_ajax() ) ) {
		return;
	}
	if ( isset( $_POST['post_data'] ) ) {
		parse_str( filter_input( INPUT_POST, 'post_data', FILTER_SANITIZE_STRING ), $post_data );
	} else {
		$post_data = $_POST; // fallback for final checkout (non-ajax)
	}

	if(WC()->session->chosen_payment_method == "cod" ){

		$total = 1.08*(WC()->cart->get_subtotal()+WC()->cart->get_shipping_total());

		$add = wc_jpec_set_ced_fee($total);
		$add = apply_filters( 'ap_change_cod_fee', $add, $total);

		WC()->cart->add_fee( '代引き手数料', $add, true );		
	}
}
/** 代引き手数料の追加　end */


if(!function_exists('wc_jpec_set_ced_fee')){
	function wc_jpec_set_ced_fee($total){
		switch (true){
			case $total < 10000:
				$add = 300;
				break;
			case $total >= 10000 && $total < 30000:
				$add = 400;
				break;
			case $total >= 30000 && $total < 100000:
				$add = 600;
				break;
			default:
				$add = 1000;
			
		}
		return $add;
	}
}



/** 送料無料の時に、他の送料表示を消す  start*/
if(!function_exists('wc_jpec_hide_shipping_when_free_is_available')){
	function wc_jpec_hide_shipping_when_free_is_available( $rates ) {
		$free = array();
		foreach ( $rates as $rate_id => $rate ) {
			if ( 'free_shipping' === $rate->method_id ) {
				$free[ $rate_id ] = $rate;
				break;
			}
		}
		return ! empty( $free ) ? $free : $rates;
	}
	add_filter( 'woocommerce_package_rates', 'wc_jpec_hide_shipping_when_free_is_available', 100 );	
}
/** 送料無料の時に、他の送料表示を消す  end*/

/* 送料のラベルを消す sart */
if(!function_exists('wc_jpec_remove_shipping_label')){
	function wc_jpec_remove_shipping_label( $label, $method ) {
		$new_label = preg_replace( '/^.+:/', '', $label );
	
		return $new_label;
	}
	add_filter( 'woocommerce_cart_shipping_method_full_label', 'wc_jpec_remove_shipping_label', 10, 2 );	
}
/* 送料のラベルを消す end */

/** 送料欄の見出しを変更 start */
add_filter( 'woocommerce_shipping_package_name', 'wc_jpec_change_shipping_package_name' , 1);
function wc_jpec_change_shipping_package_name( $name ) {
  return '送料';
}
/** 送料欄の見出しを変更 end */
