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

    $(body).on('activate', '#new-packaging, #modify-packaging', function (){
        let bool = true;

        if (typeof $('#packagingname').val() !== 'string' || typeof $('#packaginglocalefr').val() !== 'string' || typeof $('#packaginglocaleus').val() !== 'string' ||
            typeof $('#packagingdesc').val() !== 'string')
            bool = false;

        if (bool)
            $(this).submit();
    });

    $(body).on('click', '#packaging-delete-list .bi-trash', function (){
        let url = '../../Inventory/Config/RemovePackaging', id = $(this).parent().attr('data-id');

        $.ajax({
            url: url,
            method: 'POST',
            data: { packagingId: id }, // Send as an object (recommended)
            cache: false,
            success: function(response) {
                $(body).find('#packaging-delete-list [data-id="' + id + '"]').remove();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });

    $('#main-content').on('click', '#packaging-list .ts-elt', function (){
        let id = $(this).attr('data-id'), form = $(this).parent().prev();
        $.loadResources('Inventory', 'Config', 'LoadPackaging', {_packagingId: id})
            .then(data => {
                $(form).find('input[name="packagingname"]').val(data['Name']);
                $(form).find('input[name="packagingid"]').val(id);
                $(form).find('input[name="packaginglocale[FR]"]').val(data['FR']);
                $(form).find('input[name="packaginglocale[US]"]').val(data['US']);
                $(form).find('input[name="packagingdesc"]').val(data['Description']);
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