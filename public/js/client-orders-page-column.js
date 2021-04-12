(function($) {
    $(document).ready(function() {
        $('.print_dwb').click(function () {
            var dwb_sn = $(this).html();
            $.post(egexpress_client_orders_page.ajaxurl, {
                nonce:  egexpress_client_orders_page.nonce,
                action: 'egexpress_orders_page_client_handler',
                dwb_sn: dwb_sn
            }, function(data) {
                // display data
                var res = JSON.parse(data);
                if (res.status == false) {
                    alert(res.message);
                } else {
                    var newwindow = window.open();
                    newwindow.document.write(res.message);
                }

            });
        });
    })
})( jQuery );
