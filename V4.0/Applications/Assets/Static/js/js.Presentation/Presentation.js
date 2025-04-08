// Javascript document

/* Presentation Index */

$(function (){
    // Declare the strict mode
    "use strict";

    let lang = $('html').attr('lang'), body = $('body');

    $.language();

    // -Language switch
    $(body).on('change', 'select#lang', function (){
        let lg = $(this).find('option:selected').attr('value');
        window.location.href = '../../../' + lg + '/' + $(this).attr('data-app') + '/' + $(this).attr('data-controller') + '/' + $(this).attr('data-action');
    });

    $(body).on('activate', '#connection', function (){
        let bool = true;

        if (!$('#username').checkUsername()){
            bool = false;
            $('#username-validation').toggleClass('d-none');
        }

        if (!$('#pwd').checkPwd()){
            bool = false;
            $('#pwd-validation').toggleClass('d-none');
        }

        if (bool)
            $(this).submit();
    });

    $('.ts-view').on('click', function (e) {
        e.preventDefault(); // Prevent default anchor behavior
        const app = $(this).attr('data-app');
        const component = $(this).attr('data-component');
        const parent = $(this).attr('data-parent');
        const actionUrl = `../../${app}/Home/${component}`;

        $.ajax({
            url: actionUrl,
            method: 'POST',
            success: function (result) {
                $('#' + parent).html(result); // Replace the parent container content with the response
            },
            error: function () {
                console.error('Failed to load the view component.');
            }
        });
    });

    $('#form-body').on('click', 'a.ts-usr', function (e){
        const app = $(this).attr('data-app');
        const component = $(this).attr('data-component');
        const parent = $(this).attr('data-parent');
        const value = $(this).attr('data-value');
        const actionUrl = `../../${app}/Home/${component}`;

        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: { userName: value }, // Pass the username to the server
            success: function (result) {
                $(parent).html(result); // Replace the parent container content with the response
                $.language();
            },
            error: function () {
                console.error('Failed to load the view component.');
            }
        });
    });
});