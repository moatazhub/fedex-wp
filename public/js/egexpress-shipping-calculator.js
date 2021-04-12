(function( $ ) {
    function build_select(cities , name) {
        var select = "<select name='"+name+"'>";
        for (var city in cities) {
            select += "<option>" + cities[city].name + "</option>";
        }
        select += "</select>";

        return select;
    }

    function build_input(name , placeholder) {
        return "<input name='"+name+"' placeholder='"+placeholder+"'>";
    }


    if($('select[name=calc_shipping_country]').length > 0) {
        $('input[name=calc_shipping_city]').remove();
        $('select[name=calc_shipping_country]').change(function () {
            if($('select[name=calc_shipping_country]').val() == 'EG') {
                // document.getElementsByName("calc_shipping_city").innerHtml = build_select(shipping.cities , 'calc_shipping_city');
                $('select[name=calc_shipping_country]').after( build_select(shipping.cities , 'calc_shipping_city'));
                // console.log("here");
            } else {
                if ($('input[name=calc_shipping_city]').length > 0) {
                    $('select[name=calc_shipping_country]').append(build_input('calc_shipping_city' , shipping.city_input_placeholder));
                } else {
                    $('select[name=calc_shipping_city]').html(build_input('calc_shipping_city' , shipping.city_input_placeholder));
                }
            }
        });

        $('select[name=calc_shipping_country]').trigger('change');
    } else {
        console.log("Zero");
    }


})( jQuery );