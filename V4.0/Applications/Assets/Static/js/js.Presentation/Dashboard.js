// Javascript document

/* Dashboard Index */

$(function (){
    // Declare the strict mode
    "use strict";

    let lang = $('html').attr('lang'), body = $('body');

    $.language();

    $.userConfig($('nav').attr('data-usr')).then(config => {
        const sidebarConfig = config['Applications']['Sidebar'];
        Object.entries(sidebarConfig).forEach(([key, value]) => {
            $(`nav #nav-${key} > span#${key}-${value}`).addClass('ts-active');
        });
    });

    // -Language switch
    $(body).on('change', 'select#lang', function (){
        let lg = $(this).find('option:selected').attr('value');
        window.location.href = '../../../' + lg + '/' + $(this).attr('data-app') + '/' + $(this).attr('data-controller') + '/' + $(this).attr('data-action');
    });
});