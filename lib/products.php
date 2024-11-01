<?php
/** JAN start */
// add jan input field
add_action('woocommerce_product_options_inventory_product_data','wc_jpec_simple_product_jan_field', 10, 1 );
function wc_jpec_simple_product_jan_field(){
   global $woocommerce, $post;
   $product = new WC_Product(get_the_ID());
   echo '<div id="jan_attr" class="options_group">';
   //add jan field for simple product
   woocommerce_wp_text_input( 
      array(	
         'id' => '_jan',
         'label' => 'JAN',
         'desc_tip' => 'true',
         'description' => 'Enter JAN code')
   );
   echo '</div>';
}
// save simple product jan
add_action('woocommerce_process_product_meta','wc_jpec_simple_product_jan_save');
function wc_jpec_simple_product_jan_save($post_id){
   $jan_post = filter_input( INPUT_POST, '_jan', FILTER_SANITIZE_STRING );
   // save the jan
   if(isset($jan_post)){
      update_post_meta($post_id,'_jan', esc_attr($jan_post));
   }
   // remove if jan meta is empty
   $jan_data = get_post_meta($post_id,'_jan', true);
   if (empty($jan_data)){
      delete_post_meta($post_id,'_jan', '');
   }
}
/**
 * Add a jan meta fields to variations
 *
 */

add_action( 'woocommerce_product_after_variable_attributes', 'wc_jpec_variation_add_jan_field', 10, 3);
function wc_jpec_variation_add_jan_field($loop, $variation_data, $variation) {
	woocommerce_wp_text_input( array(
      'id' => '_vari_jan[' . $variation->ID . ']',
      'label' => 'JAN',
      'description' => '',
      'desc_tip' => 'false',
      'value' => get_post_meta( $variation->ID, '_jan', true ),
      'placeholder' => '',
      'wrapper_class' => 'form-row form-row-first',
      'type' => 'text'
    ));

}

//save the new meta data to the variations
add_action( 'woocommerce_save_product_variation', 'wc_jpec_add_jan_field_save' );
function wc_jpec_add_jan_field_save($post_id){
	$jan = sanitize_text_field($_POST['_vari_jan'][ $post_id ]);
	if ( ! empty( $_POST['_vari_jan'] ) ) {
		update_post_meta( $post_id, '_jan', esc_attr( $jan ) );
    }
    // remove if jan meta is empty
   $jan_data = get_post_meta($post_id,'_jan', true);
   if (empty($jan_data)){
      delete_post_meta($post_id,'_jan', '');
   }

}
/** JAN end */