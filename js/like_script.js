jQuery(function($){
    $('input#wc4jp-delivery-date').removeAttr('placeholder');
    $('input#wc4jp_tracking_ship_date').removeAttr('placeholder');

    if($('input[name="woocommerce_cod_extra_charge_name"]').length){
        $('input[name="woocommerce_cod_extra_charge_name"]').parents('table').css('visibility', 'hidden');
        $('input[name="woocommerce_cod_extra_charge_name"]').next('.submit').css('visibility', 'hidden');
        $('input[name="woocommerce_cod_extra_charge_name"]').parents('table').before('金額ごとの設定は、apply_filters( "ap_change_cod_fee", $add, $total)を設定して行えます。<br>デフォルトの設定は以下になります。<ul><li>〜10000円 300円</li><li>10000~30000円 400円</li><li>30000~100000円 600円</li><li>100000円~ 1000円</li></ul>');
    }

    if($('#woocommerce-order-data').length){
        $("#woocommerce-order-data .woocommerce .order_data_column h3").each(function(){
            html = $(this).html();
            html = html.replace(/送料/, '送付先');
            $(this).html(html);            
            $('h3 .edit_address').click(function(){
                $(this).parent().parent().find('.address').css('display', 'none');
                $(this).parent().parent().find('.edit_address').css('display', 'block');
                return false;
            })
        })

        $("._billing_state_field label").text('都道府県');
        $("._shipping_state_field label").text('都道府県');

    }

    // if($('#search-submit').val() == "注文を検索"){
    //     $('#post-search-input').attr('placeholder','注文番号,SKU,JAN,支払い方法,姓,名,メール,電話番号,会社名　で検索可能です。');
    //     $('p.search-box').css('width', '100%');
    //     $('#post-search-input').css('width', '70%');
    // }

	$('#screen-meta').append($('#ap-wrap'));
    $('#screen-meta-links').append($('#ap-link-wrap'));
    $('#ap-link-wrap').click(function(){
        $('#ap-wrap').css('display','block');
    })
    screenMeta.toggles.unbind('click');
	screenMeta.init();

});