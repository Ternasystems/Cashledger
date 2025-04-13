// Javascript document

/* Dashboard Index */

$(function (){
    // Declare the strict mode
    "use strict";

    let lang = $('html').attr('lang'), body = $('body');

    $.language();

    $.loadComponents('Inventory', 'Config', $('#main-content').attr('data-component'), 'main-content');

    // Call the load components action
    $.loadItems = function (_app, _ctrl = 'Home', _action = 'Index', _parent) {
        $.ajax({
            url: '../../' + _app + '/' + _ctrl + '/' + _action,
            method: 'POST',
            success: function (result) {
                $('#' + _parent).html(result); // Replace the parent container content with the response
                $('nav [data-component="' + _action + '"]').addClass('ts-active');
            },
            error: function () {
                console.error('Failed to load the view component.');
            }
        });
    }

    // -Language switch
    $(body).on('change', 'select#lang', function (){
        let lg = $(this).find('option:selected').attr('value');
        window.location.href = '../../../' + lg + '/' + $(this).attr('data-app') + '/' + $(this).attr('data-controller') + '/' + $(this).attr('data-action');
    });

    $(body).on('activate', '#new-product, #modify-product', function (){
        let bool = true;

        if (typeof $('#productname').val() !== 'string' || typeof $('#categoryid').val() !== 'string' || typeof $('#unitid').val() !== 'string' ||
            typeof $('#minstock').val() !== 'number' || typeof $('#maxstock').val() !== 'number'|| typeof $('#productlocalefr').val() !== 'string' ||
            typeof $('#productlocaleus').val() !== 'string' || typeof $('#productdesc').val() !== 'string')
            bool = false;

        if (bool)
            $(this).submit();
    });

    $(body).on('change', '#minstock, #maxstock', function (){
        let min = $('#minstock').val(), max = $('#maxstock').val();
        if (min > max){
            max = min;
            $('#maxstock').val(max);
        }
    });

    $(body).on('change', '#attributeck', function(){
        $('#attributes').toggleClass('ts-disabled');
    });

    $(body).on('click', '#product-delete-list .bi-trash', function (){
        let url = '../../Inventory/Config/RemoveProduct', id = $(this).parent().attr('data-id');

        $.ajax({
            url: url,
            method: 'POST',
            data: { productId: id }, // Send as an object (recommended)
            cache: false,
            success: function(response) {
                $(body).find('#product-delete-list [data-id="' + id + '"]').remove();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });

    $('#main-content').on('click', '#product-list .ts-elt', function (){
        let id = $(this).attr('data-id'), form = $(this).parent().prev();
        $.loadResources('Inventory', 'Config', 'LoadProduct', {_productId: id})
            .then(data => {
                $(form).find('input[name="productname"]').val(data['Name']);
                $(form).find('input[name="productid"]').val(id);
                $(form).find('input[name="categoryid"]').val(data['CategoryId']);
                $(form).find('input[name="unitid"]').val(data['UnitId']);
                $(form).find('input[name="minstock"]').val(data['MinStock']);
                $(form).find('input[name="maxstock"]').val(data['MaxStock']);
                $(form).find('input[name="productdesc"]').val(data['Description']);
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