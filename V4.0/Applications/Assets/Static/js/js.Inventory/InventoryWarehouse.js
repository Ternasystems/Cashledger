// Javascript document

/* Dashboard Index */

$(function (){
    // Declare the strict mode
    "use strict";

    let lang = $('html').attr('lang'), body = $('body');

    $.language();

    $.loadComponents('Inventory', 'Config', $('#main-content').attr('data-component'), 'main-content');

    // -Language switch
    $(body).on('change', 'select#lang', function (){
        let lg = $(this).find('option:selected').attr('value');
        window.location.href = '../../../' + lg + '/' + $(this).attr('data-app') + '/' + $(this).attr('data-controller') + '/' + $(this).attr('data-action');
    });

    $(body).on('activate', '#new-warehouse, #modify-warehouse', function (){
        let bool = true;

        if (typeof $('#warehousename').val() !== 'string' || typeof $('#warehouselocation').val() !== 'string' || typeof $('#warehousedesc').val() !== 'string')
            bool = false;

        if (bool)
            $(this).submit();
    });

    $(body).on('click', '#warehouse-delete-list .bi-trash', function (){
        let url = '../../Inventory/Config/RemoveWarehouse', id = $(this).parent().attr('data-id');

        $.ajax({
            url: url,
            method: 'POST',
            data: { warehouseId: id }, // Send as an object (recommended)
            cache: false,
            success: function(response) {
                $(body).find('#warehouse-delete-list [data-id="' + id + '"]').remove();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });

    $('#main-content').on('click', '#warehouse-list .ts-elt', function (){
        let id = $(this).attr('data-id'), form = $(this).parent().prev();
        $.loadResources('Inventory', 'Config', 'LoadWarehouse', {_warehouseId: id})
            .then(data => {
                $(form).find('input[name="warehousename"]').val(data['Name']);
                $(form).find('input[name="warehouseid"]').val(id);
                $(form).find('input[name="warehouselocation"]').val(data['Location']);
                $(form).find('input[name="warehousedesc"]').val(data['Description']);
            })
            .catch(error => {
                console.error('Error:', error.message);
            });
    });

    // -Nav links
    $('.ts-view').on('click', function (e) {
        e.preventDefault(); // Prevent default anchor behavior
        const app = $(this).attr('data-app');
        const component = $(this).attr('data-component');
        const parent = $(this).attr('data-parent');
        const actionUrl = `../../${app}/Config/${component}`;

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