jQuery(function($){
    //メール送信用フォーム
    $('body').prepend('<div id="check_email_over_layer"></div>');
    $('#wpbody').prepend('<div id="check_email_over_layer_wrap"></div>');
    $('#check_mail').click(function(){
        var type = $('[name=email_type]').val();
        var order_id =  $('[name=_order_id]').val();
        if(type == ""){alert('Please choose mail type.'); return;}
        $('#check_email_over_layer').css('display', 'inline');
        $("html, body").animate({scrollTop:0}, 300, "swing");        
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            cache: false,
            data: {
                'action' : 'check_email',
                'type':type,
                'order_id':order_id,
            },
            success: function( response ){
                console.log('log'+response);
                $('#check_email_over_layer_wrap').html(response);        
                $('.check_email_close').click(function(){
                    init_css();
                });
            }
        });
        return;
    });

    function init_css(){
        $('#check_email_over_layer').css('display', 'none');
        $('#check_email_over_layer_wrap').html('');     
    }

    $('.order_actions #actions').css('display', 'none');


    //メール用フィールド更新
    $('[name="woocommerce_shipping_date"], [name="woocommerce_shipper"], [name="woocommerce_shipping_num"], [name="woocommerce_free_text1"], [name="woocommerce_free_text2"]')
        .change(function(){
       var val = $(this).val();
       var name = $(this).prop('name');
       var post_id = $('#mail_setting [name="post_id"]').val();
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action' : 'update_mail_field',
                'val':val,
                'name':name,
                'post_id':post_id
            },
            success: function( response ){
                console.log(response);
            }
        });
        return false;
    })

});