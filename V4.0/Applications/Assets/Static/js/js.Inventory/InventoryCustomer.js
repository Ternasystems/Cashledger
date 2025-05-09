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

    $(body).on('activate', '#new-customer, #modify-customer', function (){
        let bool = true;

        if (typeof $('#firstname').val() !== 'string' || typeof $('#lastname').val() !== 'string' || typeof $('#maidenname').val() !== 'string' ||
            typeof $('#phone1').val() !== 'string' || typeof $('#phone2').val() !== 'string')
            bool = false;

        if (bool)
            $(this).submit();
    });

    $(body).on('change', 'select#civilities', function (){
        let txt = $(this).find('option:selected').attr('data-value').toLowerCase();
        //
        $('#new-customer input').removeClass('ts-disabled').prop('disabled', false);
        $('#new-customer select:not(#civilities)').find('option:first-of-type').prop('selected', true);
        switch (txt){
            case 'non applicable':
                $('#firstname, #maidenname').addClass('ts-disabled').prop('disabled', true);
                $('#genders option, #statuses option, #occupations option, #titles option').filter(function (){
                    return $(this).attr('data-value').toLowerCase() == 'non applicable';
                }).prop('selected', true);
                break;
            case 'mister':
                $('#maidenname').addClass('ts-disabled').prop('disabled', true);
                $('#genders option').filter(function (){
                    return $(this).attr('data-value').toLowerCase() == 'male';
                }).prop('selected', true);
                break;
            case 'miss':
                $('#maidenname').addClass('ts-disabled').prop('disabled', true);
                $('#genders option').filter(function (){
                    return $(this).attr('data-value').toLowerCase() == 'female';
                }).prop('selected', true);
                break;
            case 'madam':
                $('#genders option').filter(function (){
                    return $(this).attr('data-value').toLowerCase() == 'female';
                }).prop('selected', true);
                break;
            default:
        }
    });

    $(body).on('click', '#customer-delete-list .bi-trash', function (){
        let url = '../../Inventory/Config/RemoveCustomer', id = $(this).parent().attr('data-id');

        $.ajax({
            url: url,
            method: 'POST',
            data: { categoryId: id }, // Send as an object (recommended)
            cache: false,
            success: function(response) {
                $(body).find('#customer-delete-list [data-id="' + id + '"]').remove();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });

    $('#main-content').on('click', '#customer-list .ts-elt', function (){
        let id = $(this).attr('data-id'), form = $(this).parent().prev();
        $.loadResources('Inventory', 'Config', 'LoadCustomer', {_customerId: id})
            .then(data => {
                console.log(data);
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