// Javascript document

/* Dashboard Index */

$(function (){
    // Declare the strict mode
    "use strict";

    let lang = $('html').attr('lang'), body = $('body');

    $.language();

    $.loadComponents('Inventory', 'StockOut', $('#main-content').attr('data-component'), 'main-content');

    // -Language switch
    $('select#lang').on('change', function (){
        let lg = $(this).find('option:selected').attr('value');
        window.location.href = '../../../' + lg + '/' + $(this).attr('data-app') + '/' + $(this).attr('data-controller') + '/' + $(this).attr('data-action');
    });

    $(body).on('activate', '#new-stockdispatch', function (){
        let bool = true;

        if (typeof $('#dispatchdesc').val() !== 'string')
            bool = false;

        if (bool)
            $(this).submit();
    });

    $(body).on('change', '#productid', function (){
        let productId = $(this).find('option:selected').attr('value'), target = $('#stock-item');
        $('#stock-item').empty();
        //
        $.ajax({
            url: 'AddStockItem',
            method: 'post',
            data: { '_productId': productId },
            success: function (response){
                $(target).removeClass('d-none').html(response);
            },
            error: function (xhr, status, error){
                console.error('Stock item failed to load:', status, error);
            }
        });
    });

    $(body).on('click', '.stock-item', function (){
        $('.stock-item').removeClass('ts-active');
        $(this).addClass('ts-active');
        $('input[name="StockItemModel"]').attr('value', $(this).find('span.d-none').text());
    });

    $(body).on('activate', '#modify-stock', function (){
        let bool = true;

        if ((typeof $('#dispatchnumber').val() !== 'string' || $('#dispatchnumber').val().trim() == '') || (typeof $('#dispatchreference').val() !== 'string'
            || $('#dispatchreference').val().trim() == ''))
            bool = false;

        if (bool){
            if (!$('#dispatchnumber').hasClass('ts-disabled')){
                const dte = new Date($('#dispatchdate').val());
                const day = String(dte.getDate()).padStart(2, '0');
                const month = String(dte.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
                const year = dte.getFullYear();
                const hours = String(dte.getHours()).padStart(2, '0');
                const minutes = String(dte.getMinutes()).padStart(2, '0');
                //
                $('#DispatchNumber').text($('#dispatchnumber').val());
                $('#DispatchNumber').attr('data-value', $('#dispatchnumber').val());
                $('input[name="dispatchnumber"]').attr('value', $('#dispatchnumber').val());
                $('#DispatchDate').text(`${day}/${month}/${year} ${hours}:${minutes}`);
                $('#DispatchDate').attr('data-value', $('#dispatchdate').val());
                $('input[name="dispatchdate"]').attr('value', $('#dispatchdate').val());
                $('#Reference').text($('#dispatchreference').val());
                $('#Reference').attr('data-value', $('#dispatchreference').val());
                $('input[name="dispatchreference"]').attr('value',$('#dispatchreference').val() );
                //
                $('#dispatchnumber, #dispatchreference, #dispatchdate').addClass('ts-disabled');
            }
            //
            $.ajax({
                url: $(this).attr('action'), // Make sure your form has an 'action' attribute
                method: $(this).attr('method') || 'POST', // Default to POST if not specified
                data: $(this).serialize(), // Serialize the form data
                success: function (response) {
                    const currentTotal = parseFloat($('.form-area #totalcost span').text().replace(/[^\d.-]/g, '')) || 0;
                    const responseTotal = parseFloat($(response).find('div[data-id="total"]').text().replace(/[^\d.-]/g, '')) || 0;
                    const total = currentTotal + responseTotal;

                    $('input[name="state"]').attr('value', 'true');
                    $('.form-area #totalcost').before(response);
                    $('.form-area #totalcost span').text(new Intl.NumberFormat(lang, {
                        style: 'decimal'
                    }).format(total));
                },
                error: function (xhr, status, error) {
                    console.error('Submission failed:', status, error);
                }
            });
        }
    });

    // -Nav links
    $(body).on('click', '.ts-view', function (e) {
        e.preventDefault(); // Prevent default anchor behavior
        const app = $(this).attr('data-app');
        const component = $(this).attr('data-component');
        const parent = $(this).attr('data-parent');
        const actionUrl = `../../${app}/StockOut/${component}`;

        $.ajax({
            url: actionUrl,
            method: 'POST',
            success: function (result) {
                $('#' + parent).html(result); // Replace the parent container content with the response
                $('nav [data-component]').removeClass('ts-active');
                $('nav [data-component="' + component + '"]').addClass('ts-active');
            },
            error: function () {
                console.error('Failed to load the view component.');
            }
        });
    });
});