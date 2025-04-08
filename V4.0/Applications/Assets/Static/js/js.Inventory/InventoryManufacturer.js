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

    $(body).on('activate', '#new-manufacturer, #modify-manufacturer', function (){
        let bool = true;

        if (typeof $('#manufacturername').val() !== 'string' || typeof $('#manufacturerdesc').val() !== 'string')
            bool = false;

        if (bool)
            $(this).submit();
    });

    $(body).on('click', '#manufacturer-delete-list .bi-trash', function (){
        let url = '../../Inventory/Config/RemoveManufacturer', id = $(this).parent().attr('data-id');

        $.ajax({
            url: url,
            method: 'POST',
            data: { manufacturerId: id }, // Send as an object (recommended)
            cache: false,
            success: function(response) {
                $(body).find('#manufacturer-delete-list [data-id="' + id + '"]').remove();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });

    $('#main-content').on('click', '#manufacturer-list .ts-elt', function (){
        let id = $(this).attr('data-id'), form = $(this).parent().prev();
        $.loadResources('Inventory', 'Config', 'LoadManufacturer', {_manufacturerId: id})
            .then(data => {
                $(form).find('input[name="manufacturername"]').val(data['Name']);
                $(form).find('input[name="manufacturerid"]').val(id)
                $(form).find('input[name="manufacturerdesc"]').val(data['Description']);
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