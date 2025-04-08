// Javascript document

/* Dashboard Index */

$(function (){
    // Declare the strict mode
    "use strict";

    let lang = $('html').attr('lang');

    $.language();

    $.loadResources('Inventory', 'Config', 'NewProduct', 'main-content');

    // -Language switch
    $('select#lang').on('change', function (){
        let lg = $(this).find('option:selected').attr('value');
        window.location.href = '../../../' + lg + '/' + $(this).attr('data-app') + '/' + $(this).attr('data-controller') + '/' + $(this).attr('data-action');
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