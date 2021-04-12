(function($) {
    $(document).ready(function() {

        $("#doaction").on('click', function(e){
            var select =$("#bulk-action-selector-top, #bulk-action-selector-bottom").val();
            if(select == 'print_bulk'){

                var orderIds = [];

                var arr = $("[id^=cb-select-]:checked");

                $.each(arr, function(index) {
                       orderIds[index]  = $(this).val();
                });

                if (orderIds.includes("on")){
                    orderIds.pop();
                    orderIds.shift();
                }

                $.post(ajaxurl, {

                    nonce:  egexpress_order_bulk.nonce,
                    action: 'egexpress_orders_bulk_admin_handler' ,
                    orderIds: orderIds
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

            }

        });
    })
})( jQuery );









