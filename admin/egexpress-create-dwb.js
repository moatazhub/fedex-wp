(function($) {

    $(document).ready(function() {
        // Quick & dirty toggle to demonstrate modal toggle behavior
        $('.modal-toggle').on('click', function(e) {
            e.preventDefault();
            $('.modal').toggleClass('is-visible');
        });
        // when user submits the form
        $('.egexpress-create-dwb-ajax-form').on( 'submit', function(event) {

            // prevent form submission
            event.preventDefault();

            // add loading message
            $('.egexpress-create-dwb-ajax-response').html('Loading...');

            var shipper_phone = $('#shipper_phone').val();
            var shipper_city = $('#shipper_city').val();
            var shipper_address1 = $('#shipper_address1').val();
            var shipper_address2 = $('#shipper_address2').val();
            var recipient_name = $('#recipient_name').val();
            var recipient_phone = $('#recipient_phone').val();
            var recipient_city = $('#recipient_city').val();
            var recipient_address1 = $('#recipient_address1').val();
            var recipient_address2 = $('#recipient_address2').val();
            var payment_method = $('#payment_method').val();
            var COD_amount = $('#COD_amount').val();
            var no_of_pieces = $('#no_of_pieces').val();
            var weight = $('#weight').val();
            var dimensions = $('#dimensions').val();
            var goods_description = $('#goods_description').val();
            var goods_origin_country = $('#goods_origin_country').val();
            var shipper_name = $('#shipper_name').val();
            var order_id = $('#order_id').val();
                // submit the data
            $.post(ajaxurl, {

                nonce:  egexpress_create_dwb.nonce,
                action: 'egexpress_create_dwb_ajax_admin_handler',
                shipper_phone : shipper_phone,
                shipper_city : shipper_city,
                shipper_address1 : shipper_address1,
                shipper_address2 : shipper_address2,
                recipient_name : recipient_name,
                recipient_phone : recipient_phone,
                recipient_city : recipient_city,
                recipient_address1 : recipient_address1,
                recipient_address2 : recipient_address2,
                payment_method : payment_method,
                COD_amount : COD_amount,
                no_of_pieces : no_of_pieces,
                weight : weight,
                dimensions : dimensions,
                goods_description : goods_description,
                goods_origin_country : goods_origin_country,
                shipper_name : shipper_name ,
                order_id : order_id

            }, function(data) {

                var res = JSON.parse(data);
                // display data
                if (res.status == false) {
                    $('.egexpress-create-dwb-ajax-response').html(res.message);
                } else {
                    $('.egexpress-create-dwb-ajax-response').html(res.message.message);
                    // var newwindow = window.open();
                    // newwindow.document.write(res.message.html);
                    $('.egexpress-create-dwb-ajax-form').html(res.message.html);
                    $('.form-print').show();
                }

            });

        });
        $('.form-print-btn').click(function (e) {
            e.preventDefault();
            window.print();
        });
    });

})( jQuery );