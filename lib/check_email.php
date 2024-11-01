<?php

/*
* based on code by https://github.com/wp3sixty/woo-custom-emails/
* License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

class Wc_jpec_check_email_before_send{
  public $order_id;
  public $order;
  public $order_meta;

  public function __construct() {
      add_action( 'add_meta_boxes', array($this, 'ex_metabox'));
      add_action( 'wp_ajax_check_email', array($this, 'check_email' ));
      add_action( 'wp_ajax_update_mail_field', array($this, 'update_mail_field' ));
      //add_action('save_post', array($this, 'save_woo_shipper_field'));
      add_action('admin_menu', array($this, 'woo_add_mail_field'));

      add_action( 'woocommerce_email', array($this, 'add_send_custom_new_order_mail') );
      add_action( 'woocommerce_email', array($this, 'unhook_those_pesky_emails' ));

      add_filter( 'woocommerce_settings_tabs_array', array($this, 'change_woocommerce_setting_tabs'), 200, 1 );
      add_filter( 'woocommerce_email_settings', array($this, 'wc_jpec_woocommerce_email_settings'), 10); 
  }

  function wc_jpec_woocommerce_email_settings($args){
    $args[0]['desc'] = '<span style="color:red;">このメール設定は、Woocommerce カスタムEメール設定の設定とは異なります。<br>送信不要なメールは「管理」をクリックして「メール通知を有効化」からチェックを外してください。</span>'
                      .'<br><br>WooCommerce から送信される電子メール通知は、以下に記載されています。メール名をクリックして設定できます。';
    return $args;
  }

  function change_woocommerce_setting_tabs($tabs){
    $tabs['email'] = "メール（woocommerce機能）";
    return $tabs;
  }

  function woo_add_mail_field(){
      add_meta_box( "mail_setting", __('Mail Setting Field', 'wc_jpec_check_email_before_send'), 
              array($this,'woo_shipper_field'), 'shop_order', 'side');
  }


  function send_custom_new_order_mail($order_id){
    if ( ! $order_id ) return;
    $order =  wc_get_order( $order_id );
    $this->order_id = $order_id;
    $this->order = wc_get_order( $order_id );
    $this->order_meta = get_post_meta($order_id);
    $title = get_option( "wcemails_new_order_subject" );
    $title = self::replace_tag($title);
    $content = get_option( "wcemails_new_order_template" );
    $content = self::replace_tag($content)."\n\n".self::get_footer();
    $content = apply_filters( 'ap_check_email_before_send_add_text_for_new', $content, $order_id);
    $send_to = $order->billing_email;
    $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
    $sender_name = (get_option('wcemails_new_order_admin_name'))?get_option('wcemails_new_order_admin_name'):$blogname;
    $from = get_option('wcemails_new_order_admin_email');
    $cc_mail = get_option('wcemails_new_order_admin_email');
    $headers = "From: \"{$sender_name}\" <{$from}>\n";
    $headers .= "Cc: $cc_mail\n";
    $headers .= "Content-Type: text/plain; charset=\"UTF-8\"\n";
    wp_mail($send_to, $title, $content, $headers);
  }

  function woo_shipper_field(){
      global $post;
      $order = new WC_Order($post->ID);
      $order_id = trim(str_replace('#', '', $order->get_order_number()));    
      $meta = get_post_meta($order_id);
      $shipping_date = (isset($meta['woocommerce_shipping_date']))?$meta['woocommerce_shipping_date'][0]:"";
      $shipper = (isset($meta['woocommerce_shipper']))?$meta['woocommerce_shipper'][0]:"";
      $shipping_num = (isset($meta['woocommerce_shipping_num']))?$meta['woocommerce_shipping_num'][0]:"";
      $woocommerce_free_text1 = (isset($meta['woocommerce_free_text1']))?$meta['woocommerce_free_text1'][0]:"";
      $woocommerce_free_text2 = (isset($meta['woocommerce_free_text2']))?$meta['woocommerce_free_text2'][0]:"";
      ?>
          <input type="hidden" name="post_id" value="<?php print $post->ID; ?>">
          <div class="woo_shipping_inner_box">
          <label><?php _e('shipping date', 'wc_jpec_check_email_before_send'); ?></label>
          <input type="text" name="woocommerce_shipping_date" value="<?php print $shipping_date; ?>">
          </div>
          <div class="woo_shipping_inner_box">
          <label><?php _e('shipper', 'wc_jpec_check_email_before_send'); ?></label>
          <div>
          <?php if($shipper): ?>

            <input type="text" value="<?php print $shipper; ?>" name="woocommerce_shipper">

          <?php else: ?>

            <select name="woocommerce_shipper">
            <option value="">------</option>
            <?php 
            $shippers = array('ヤマト運輸（クロネコ）','佐川急便（飛脚）','日本郵便','西濃運輸（カンガルー）','福山通運','エコ配','その他');
            foreach($shippers as $shipper_name):
            ?>
            <option value="<?php print $shipper_name; ?>" <?php ($shipper == $shipper_name)?print "selected":""; ?>><?php print $shipper_name; ?></option>
            <?php endforeach; ?>
            </select>

          <?php endif; ?>
          </div>
          </div>
          <div class="woo_shipping_inner_box">
          <label><?php _e('tracking number', 'wc_jpec_check_email_before_send'); ?></label>
          <input type="text" name="woocommerce_shipping_num" value="<?php print $shipping_num; ?>">
          </div>
          <div class="woo_shipping_inner_box">
          <label><?php _e('woocommerce_free_text', 'wc_jpec_check_email_before_send'); ?>1</label>
          <textarea name="woocommerce_free_text1" rows="8"><?php print $woocommerce_free_text1; ?></textarea>
          </div>
          <div class="woo_shipping_inner_box">
          <label><?php _e('woocommerce_free_text', 'wc_jpec_check_email_before_send'); ?>2</label>
          <textarea name="woocommerce_free_text2" rows="8"><?php print $woocommerce_free_text2; ?></textarea>
          </div>
      <?php
  }

  // function save_woo_shipper_field(){
  //   global $post_id;
  //   $fields = ['woocommerce_shipping_date', 'woocommerce_shipper', 
  //                 'woocommerce_shipping_num', 'woocommerce_free_text1', 'woocommerce_free_text2']; 
  //   foreach($fields as $field){
  //     $value = filter_input( INPUT_POST, $field, FILTER_SANITIZE_STRING );
  //     if( strcmp($value, get_post_meta($post_id, $field, true)) != 0 ){
  //       update_post_meta($post_id, $field, $value);
  //     }elseif($value == ""){
  //       delete_post_meta($post_id, $field, get_post_meta($post_id, $field, true));
  //     }
  //   }
  // }

  function add_send_custom_new_order_mail(){ 
    add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'send_custom_new_order_mail' )  ); 
    add_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $this, 'send_custom_new_order_mail' )  ); 
  }

  function unhook_those_pesky_emails( $email_class ) {

    $actions = array('WC_Email_Customer_Completed_Order', 'WC_Email_New_Order',
                      'WC_Email_Cancelled_Order', 'WC_Email_Customer_On_Hold_Order',
                      'WC_Email_Customer_Processing_Order', 'WC_Email_Customer_Invoice',
                      'WC_Email_Customer_Refunded_Order'
                     );
    foreach($actions as $action){
      remove_action( 'woocommerce_order_status_on-hold_to_processing_notification', array( $email_class->emails[$action], 'trigger' ) ); 
      remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails[$action], 'trigger' ) ); 
      remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails[$action], 'trigger' ) ); 
      remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails[$action], 'trigger' ) );      
    }
	}

  function update_mail_field(){
    $post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
    $val = filter_input( INPUT_POST, 'val', FILTER_SANITIZE_STRING );
    $name = filter_input( INPUT_POST, 'name', FILTER_SANITIZE_STRING );
    update_post_meta( $post_id, $name, $val);
  }

  function send_mail($order_id){
    if(isset($_POST['check_email'])){
      $send_to = filter_input( INPUT_POST, 'send_to', FILTER_SANITIZE_STRING );
      $title = filter_input( INPUT_POST, 'title', FILTER_SANITIZE_STRING );
      $content = filter_input( INPUT_POST, 'content', FILTER_SANITIZE_STRING );
      $shipping_mail_flag = $_POST['shipping_mail_flag'];
      $cc_mail_flag = filter_input( INPUT_POST, 'cc_mail_flag', FILTER_SANITIZE_NUMBER_INT );
      $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
      $sender_name = (get_option('wcemails_new_order_admin_name'))?get_option('wcemails_new_order_admin_name'):$blogname;
      $from = get_option('wcemails_new_order_admin_email');
      $headers = "From: \"{$sender_name}\" <{$from}>\n";
      if ($cc_mail_flag != 1) {
        $cc_mail = get_option('wcemails_new_order_admin_email');
        $headers .= "Cc: $cc_mail\n";
      }
      $headers .= "Content-Type: text/plain; charset=\"UTF-8\"\n";
      wp_mail($send_to, $title, $content, $headers);
      do_action('wc_check_email_after_send_email');
      if($shipping_mail_flag == 1){
        add_post_meta( $order_id, 'ap_send_shipping_mail', '1');
      }
    }
  }

  function ex_metabox() {
    add_meta_box( 
      'check_email_before_send',
      __('Send Mail', 'wc_jpec_check_email_before_send'),
      array($this, 'show_button'),
      'shop_order', 'side', 'core'
    );
  }

  function show_button(){
    $options = get_option( "wcemails_email_details" );
    global $woocommerce, $post;
    $order = new WC_Order($post->ID);
    $order_id = trim($order->get_order_number());
    self::send_mail($order_id);
    ?>
    <form>
    <select name="email_type">
    <option value="" selected><?php _e('Please select email.', 'wc_jpec_check_email_before_send'); ?></option>
    <?php foreach($options as $option): ?>
    <option value="<?php print $option['title']; ?>"><?php print $option['title']; ?></option>
    <?php endforeach; ?>
    </select>
    <br>
    <input type="hidden" value="<?php print $order_id; ?>" name="_order_id">
    <input type="button" name="action" class="button button-primary" id="check_mail" value="<?php echo __('Confirm', 'wc_jpec_check_email_before_send'); ?>"  />
    </form>
    <?php
  }

  function check_email(){
    $order_id = filter_input( INPUT_POST, 'order_id', FILTER_VALIDATE_INT );
    $this->order_id = $order_id;
    $type = trim(filter_input( INPUT_POST, 'type', FILTER_SANITIZE_STRING ));
    
    $this->order = wc_get_order( $this->order_id );
    $this->order_meta = get_post_meta($this->order_id);
    $options = get_option( "wcemails_email_details" );
    $array = self::get_this_email_title($type, $options);
    $this_email_title = $array[0];
    $shipping_mail_flag = $array[1];
    $cc_mail_flag = $array[2];
    $title = self::get_title($type, $options);
    $title = self::replace_tag($title);
    $content = self::get_content($type, $options);
    $content = self::replace_tag($content)."\n\n".self::get_footer();
    $content = apply_filters( 'ap_check_email_before_send_add_text', $content, $order_id );
  
    ?>
    <div class="check_email_over_layer">
    <div class="check_email_close">&#10005;</div>
    <form method="POST" action="<?php print admin_url().'post.php?post='.$this->order_id.'&action=edit'; ?>">
    <h1>件名</h1>
    <input type="text" value="<?php print $title; ?>" size="80" name="title">
    <h1>本文</h1>
    <div>
    <textarea name="content" cols="80" rows="30"><?php print $content; ?></textarea>
    </div>

    <input type="hidden" value="check_email" name="check_email">
    <input type="hidden" value="<?php print $this_email_title; ?>" name="this_email_title">
    <input type="hidden" name="send_to" value="<?php print $this->order->billing_email; ?>">
    <input type="hidden" name="shipping_mail_flag" value="<?php print $shipping_mail_flag; ?>">
    <input type="hidden" name="cc_mail_flag" value="<?php print $cc_mail_flag; ?>">
    <input type="submit" class="button button-primary" value="<?php _e('Send Mail', 'wc_jpec_check_email_before_send') ?>" >
    
    </form>
    </div>
    <?php
    die();
  }

    function get_this_email_title($type, $options){
      $shipping_flag = 0;
      $cc_flag = 0;
      foreach($options as $option){
        if(trim($option['title']) == $type){
          if(isset($option['shipping_send_mail_flag'])){
            if ($option['shipping_send_mail_flag'] == '1'){
              $shipping_flag = 1;
            }
          }
          if(isset($option['cc_admin_flag'])){
            if ($option['cc_admin_flag'] == '1'){
              $cc_flag = 1;
            }
          }
          return array(trim($option['title']), $shipping_flag, $cc_flag);
          // return trim($option['title']);
        }
      }
    }

    function get_title($type, $options){
      foreach($options as $option){
        if(trim($option['title']) == $type){
          return trim($option['subject']);
        }
      }
    }

    function get_content($type, $options){
      foreach($options as $option){
        if(trim($option['title']) == $type){
          return trim($option['template']);
        }
      }
    }

    function get_header_from($type, $options){
      foreach($options as $option){
        if(trim($option['title']) == $type){
          return trim($option['heading']);
        }
      }
    }

    function replace_tag($text){

      $tag_arg = array();
      $replace = array();

      $tag_arg[] = '{order_number}';
      $replace[] = $this->order_id;

      //$tag_arg[] = '{woocommerce_email_order_meta}';
      //$replace[] = $this->woocommerce_email_order_meta();
      
      $tag_arg[] = '{order_billing_name}';
      $replace[] = $this->order->billing_last_name. ' ' .$this->order->billing_first_name;

      $tag_arg[] = '{order_shipping_name}';
      $replace[] = $this->order->shipping_last_name. ' ' .$this->order->shipping_first_name;

      $tag_arg[] = '{order_shipper}';
      $replace[] = $this->order_meta['woocommerce_shipper'][0];

      $tag_arg[] = '{order_shipping_number}';
      $replace[] = $this->order_meta['woocommerce_shipping_num'][0];

      $tag_arg[] = '{order_date}';
      $replace[] = date_i18n( wc_date_format(), strtotime( $this->order->order_date ) );

      $tag_arg[] = '{order_items}';
      $replace[] = self::order_items($this->order);

      $tag_arg[] = '{order_payment_method}';
      $replace[] = $this->order_payment_method($this->order);

      $tag_arg[] = '{email_order_items_table}';
      $replace[] = $this->order->email_order_items_table( false, true );

      $tag_arg[] = '{email_order_total_footer}';
      $replace[] = self::email_order_total_footer();

      $tag_arg[] = '{email_order_total_plain}';
      $replace[] = self::make_plain(self::email_order_total_footer());

      $tag_arg[] = '{order_billing_email}';
      $replace[] = $this->order->billing_email;

      $tag_arg[] = '{order_billing_phone}';
      $replace[] = $this->order->billing_phone;

      $tag_arg[] = '{order_shipping_phone}';
      $replace[] = $this->order->shipping_phone;

      $tag_arg[] = '{addresses}';
      $replace[] = $this->get_addresses($this->order);

      $tag_arg[] = '{shipping_addresses}';
      $replace[] = $this->get_shipping_addresses($this->order);

      $tag_arg[] = '{order_shipping_date}';
      $replace[] = $this->order_meta['woocommerce_shipping_date'][0];;

      /* 銀行振込時に入力できるように変更
      $tag_arg[] = '{order_bankjp_information}';
      $replace[] = self::bank_jp_information();
      */

      $tag_arg[] = '{order_custom_content}';
      $replace[] = $this->order_meta['woocommerce_free_text1'][0];

      $tag_arg[] = '{order_custom_content2}';
      $replace[] = $this->order_meta['woocommerce_free_text2'][0];

			$tag_arg[] = '{delivery-date}';
			$replace[] = $this->order_meta['wc4jp-delivery-date'][0];
			
			$tag_arg[] = '{delivery-time-zone}';
			$replace[] = $this->order_meta['wc4jp-delivery-time-zone'][0];

			$tag_arg[] = '{order_billing_company}';
			$replace[] = $this->order_meta['_billing_company'][0];

			$tag_arg[] = '{order_shipping_company}';
			$replace[] = $this->order_meta['_shipping_company'][0];

			$post = get_post($this->order_id);
			$order_notes = $post->post_excerpt;
			$tag_arg[] = '{order_customer_note}';
			$replace[] = $order_notes;

      $tag_arg[] = '{site_title}';
      $replace[] = get_bloginfo();

      for($i=0; $i<count($tag_arg); $i++){
        $text = str_replace($tag_arg[$i], $replace[$i], $text);
      }
      $text = str_replace('&yen;', '¥', $text);
      return $text;
    }

		function order_items($order){
      $totals = $this->order->get_order_item_totals();
      /** ここで税込表示か税抜き位表示か判断する */
      $is_ex_tax = 0;
      foreach($totals as $key=>$total){
        if(preg_match('/tax/', $key)){
          $is_ex_tax = 1;
        }
      }
      /**ここまで */

      $items = $order->get_items();
      $str = "\n";
			foreach ( $items as $item_id => $item ) {
        $this_product_id = ($item->get_variation_id())?$item->get_variation_id():$item->get_product_id();
        $sku = wc_get_product( $this_product_id ) -> get_sku();
        $jan = get_post_meta($this_product_id,'_jan', true);
        if($sku || $jan){
          switch (true){
            case $jan && $sku:
              $_str =  " [$jan / $sku]";
              break;
            case $jan:
              $_str =  " [$jan]";
              break;
            case $sku:
              $_str =  " [$sku]";
              break;
          }
        }else{
          $_str = "";
        }
        $special_color = get_post_meta($this_product_id,'_ap_color', true);
        if($special_color){
          $_str = '/'.$special_color.$_str;
        }
        $qty = $item->get_quantity();
        $price = ($is_ex_tax)?round($item->get_subtotal()): round($item->get_subtotal() + $item->get_subtotal_tax());
        $str .= $item['name'].$_str.' × '.$qty.'   '.number_format($price).'円'."\n";
      }
      $str = rtrim($str, "\n");
      return $str;
		}

		function email_order_total_footer() {
			ob_start();
			if ( $totals = $this->order->get_order_item_totals() ) {
				$i = 0;
				foreach ( $totals as $key=>$total ) {
          $i ++;
					?>
					<tr>
					<th scope='row' colspan='2'
					    style='text-align:left; border: 1px solid #eee; <?php echo 1 == $i ? 'border-top-width: 4px;' : ''; ?>'><?php echo $total['label']; ?></th>
					<td style='text-align:left; border: 1px solid #eee; <?php echo 1 == $i ? 'border-top-width: 4px;' : ''; ?>'><?php echo $total['value']; ?></td>
					</tr><?php
				}
			}

			return ob_get_clean();
		}

		function get_footer() {
      $footer = get_option('ap_wcemails_footer_setting_templateorder');
      return $footer;
		}

    function make_plain($html){
			$html = preg_replace('/<(\/?)th(.*?)>/s', '', $html);
			$html = preg_replace('/<(\/?)td(.*?)>/s', '', $html);
			$html = preg_replace('/<(\/?)span(.*?)>/s', '', $html);
			$html = preg_replace('/<small(.*?)\/small>/s', '', $html);
			$html = preg_replace('/<tr(.?)>/', '', $html);
			$html = trim(preg_replace('/\s/', '', $html));
			$html = preg_replace("/<\/tr>/", "\r\n", $html);
			$html = preg_replace("/ /", "", $html);
			$html = preg_replace("/:/", " : ", $html);
			return $html;
		}

		function bank_jp_information(){
			$bank_details = get_option( 'woocommerce_bankjp_accounts');
			$bank_text = "\n";
			foreach($bank_details as $bank_detail){
				$bank_text .= "  ".$bank_detail['bank_name']."  ".$bank_detail['bank_branch']."\n".
        "  ".$bank_detail['bank_type']."  ".$bank_detail['account_number']."\n".
        "  ".$bank_detail['account_name']."\n";
      }
      $bank_text = apply_filters( 'ap_check_email_add_bank_text', $bank_text);
			return $bank_text;
		}

		function woocommerce_email_order_meta() {
      ob_start();

      do_action( 'woocommerce_email_order_meta', $this->order, true );

			return ob_get_clean();
		}
    
		function order_payment_method($post_meta){
			$payment_method = (isset($this->order_meta['_payment_method_title']))?($this->order_meta['_payment_method_title'][0]):"";
      $payment_method = preg_replace('/（日本国内）/', '', $payment_method);
      $str = "支払い方法：".$payment_method."\n";
      if(preg_match('/銀行/', $str)){
        $str = "  [銀行振込]";
        $str .= $this->bank_jp_information()."\n";
      }
			return $str;
		}

		function get_addresses($order) {
			$address = '郵便番号 : ';
      $address .= $order->billing_postcode."\n";

      global $woocommerce;
      $countries_obj   = new WC_Countries();
      $states = $countries_obj->get_states( $order->billing_country );
      $prefec = $states[$order->billing_state];
      $address .= $prefec;

      $address .= $order->billing_city;
      $address .= $order->billing_address_1."\n";
      $address .= $order->billing_address_2;

      $address = rtrim($address,"\n");
      return $address;
		}

		function get_shipping_addresses($order) {
			$address = '郵便番号 : ';
      $address .= $order->shipping_postcode."\n";

      global $woocommerce;
      $countries_obj   = new WC_Countries();
      $states = $countries_obj->get_states( $order->shipping_country );
      $prefec = $states[$order->shipping_state];
      $address .= $prefec;

      $address .= $order->shipping_city;
      $address .= $order->shipping_address_1."\n";
      $address .= $order->shipping_address_2;

      return $address;
		}

}

