(function( $ ) {
    function build_select(cities , name) {
        var select = "<select name='"+name+"'>";
        for (var city in cities) {
            select += "<option>" + cities[city].name + "</option>";
        }
        select += "</select>";

        return select;
    }

    if($('input[name=billing_country]').length > 0) {
        $('input[name=billing_country]').change(function () {
            if ($('input[name=billing_country]').val() == 'EG') {
                console.log('EG');
            } else {
                console.log($('input[name=billing_country]').val());
            }
        });

        $('input[name=billing_country]').trigger('change');
    }


    if($('select[name=billing_country]').length > 0) {
        $('select[name=billing_country]').change(function () {
            if($('input[name=billing_country]').val() == 'EG') {
                console.log('EG');
            } else {
                console.log($('select[name=billing_country]').val());
            }
        });

        $('select[name=billing_country]').trigger('change');
    }


})( jQuery );