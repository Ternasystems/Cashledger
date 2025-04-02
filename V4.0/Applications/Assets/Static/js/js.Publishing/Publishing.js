// Javascript document

/* Dashboard Index */

$(function (){
    // Declare the strict mode
    "use strict";

    let lang = $('html').attr('lang');

    $.language();

    // -Language switch
    $('select#lang').on('change', function (){
        let lg = $(this).find('option:selected').attr('value');
        window.location.href = '../../../' + lg + '/' + $(this).attr('data-app') + '/' + $(this).attr('data-controller') + '/' + $(this).attr('data-action');
    });
});