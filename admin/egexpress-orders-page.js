(function($) {
    $(document).ready(function() {
        $('.print_dwb').click(function () {
            var dwb_sn = $(this).html();
            $.post(ajaxurl, {

                nonce:  egexpress_orders_page.nonce,
                action: 'egexpress_orders_page_admin_handler',
                dwb_sn: dwb_sn
            }, function(data) {
                // display data
               // alert(data);
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
