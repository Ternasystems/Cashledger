// Javascript document

/* Dashboard Index */

$(function (){
    // Declare the strict mode
    "use strict";

    let lang = $('html').attr('lang'), body = $('body');

    $.language();

    $.loadComponents('Inventory', 'Invent', $('#main-content').attr('data-component'), 'main-content');

    // -Language switch
    $('select#lang').on('change', function (){
        let lg = $(this).find('option:selected').attr('value');
        window.location.href = '../../../' + lg + '/' + $(this).attr('data-app') + '/' + $(this).attr('data-controller') + '/' + $(this).attr('data-action');
    });

    $(body).on('activate', '#new-stockinventory', function (){
        let bool = true;

        if (typeof $('#inventorydesc').val() !== 'string')
            bool = false;

        if (bool)
            $(this).submit();
    });

    $(body).on('click', '#stock-list .form-row', function (){
        let stockId = $(this).attr('data-id'), item = $(this);
        //
        $('#stock-list .form-row').removeClass('ts-active');
        $(item).addClass('ts-active');
        //
        $.ajax({
            url: 'AddStockDetails',
            method: 'POST',
            data: {stockId: stockId},
            success: function (response){
                $('#stock-detail').empty().html(response);

                let stock = parseFloat($('#stock-detail').find('[data-id="stock"]').text().replace(/[^\d.-]/g, ''));
                let minStock = parseFloat($('#stock-detail').find('[data-id="min-stock"]').text().replace(/[^\d.-]/g, ''));
                let maxStock = parseFloat($('#stock-detail').find('[data-id="max-stock"]').text().replace(/[^\d.-]/g, ''));

                if (stock <= minStock)
                    $('#stock-detail').find('[data-id="min-stock"]').css('color', 'red');

                if (stock >= maxStock)
                    $('#stock-detail').find('[data-id="max-stock"]').css('color', 'red');
            }
        });
    });

    $(body).on('change', '#warehouseid', function (){
        let warehouseId = $(this).find('option:selected').attr('value'), target = $('#stock-item');
        $('#stock-item').empty();
        //
        $.ajax({
            url: 'AddInventStock',
            method: 'post',
            data: { '_warehouseId': warehouseId },
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
        $('input[name="InventStockModel"]').attr('value', $(this).find('span.d-none').text());
    });

    $(body).on('activate', '#new-inventory', function (){
        let bool = true;

        if ((typeof $('#inventorynumber').val() !== 'string' || $('#inventorynumber').val().trim() == ''))
            bool = false;

        if (bool){
            if (!$('#inventorynumber').hasClass('ts-disabled')){
                const dte = new Date($('#inventorydate').val());
                const day = String(dte.getDate()).padStart(2, '0');
                const month = String(dte.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
                const year = dte.getFullYear();
                const hours = String(dte.getHours()).padStart(2, '0');
                const minutes = String(dte.getMinutes()).padStart(2, '0');
                //
                $('#InventoryNumber').text($('#inventorynumber').val());
                $('#InventoryNumber').attr('data-value', $('#inventorynumber').val());
                $('input[name="inventorynumber"]').attr('value', $('#inventorynumber').val());
                $('#InventoryDate').text(`${day}/${month}/${year} ${hours}:${minutes}`);
                $('#InventoryDate').attr('data-value', $('#inventorydate').val());
                $('input[name="inventorydate"]').attr('value', $('#inventorydate').val());
                $('#Warehouse').text($('#warehouseid').find('option:selected').text());
                $('#Warehouse').attr('data-value', $('#warehouseid').find('option:selected').attr('value'));
                $('input[name="warehouseid"]').attr('value', $('#warehouseid').find('option:selected').attr('value'));
                $('#inventorynumber, #inventorydate, #warehouseid').addClass('ts-disabled');
            }
            //
            if ($('.form-area').children().length > 0){
                let stockid = $('.stock-item.ts-active').find('[data-class="stock-id"] > [data-class="stock-value"]').text();
                let elt = $('.form-area').find('[data-id="stock-id"]').filter(function (){
                    return $(this).text().trim() == stockid.trim();
                }).parent();
                //
                if ($(elt).length > 0){
                    let stockAvailable = parseFloat($(elt).find('[data-id="stock-available"]').text().replace(/[^\d.-]/g, ''));
                    let stockQuantity = parseFloat($('input[name="stockquantity"]').val());
                    let variation = stockAvailable - stockQuantity;
                    //
                    $(elt).find('[data-id="stock-quantity"]').text(stockQuantity);
                    $(elt).find('[data-id="variation"]').text(variation);
                    //
                    let hidden = $(elt).find('input[type="hidden"]');
                    let model = JSON.parse(hidden.val());
                    model.stockquantity = stockQuantity;
                    hidden.val(JSON.stringify(model));
                    //
                    return;
                }
            }
            //
            $.ajax({
                url: $(this).attr('action'), // Make sure your form has an 'action' attribute
                method: $(this).attr('method') || 'POST', // Default to POST if not specified
                data: $(this).serialize(), // Serialize the form data
                success: function (response){
                    $('input[name="state"]').attr('value', 'true');
                    $('.form-area').append(response);
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
        const actionUrl = `../../${app}/Invent/${component}`;

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