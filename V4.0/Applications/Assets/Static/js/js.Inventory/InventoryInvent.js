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