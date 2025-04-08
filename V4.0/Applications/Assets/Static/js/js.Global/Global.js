// JavaScript Document

/* Global */

const { from, defer, of } = rxjs;
const { mergeMap, switchMap, tap, map } = rxjs.operators;

$(function () {
    // Declare the strict mode
    "use strict";

    let body = $('body'), lang = $('html').attr('lang');

    // Date extensions
    // -Write date as ISO string
    Date.prototype.toISODateString = function () {
        return this.toISOString().split('T')[0];
    }

    // -Write time to ISO string
    Date.prototype.toISOTimeString = function () {
        return this.toLocaleTimeString('en-GB');
    }

    // jQuery plugins
    // -jQuery fn.extends
    $.fn.extend({
        // Class switch
        switchClass: function (_cls1, _cls2) {
            this.removeClass(_cls1);
            this.addClass(_cls2);
            return this;
        },
        // Class swap
        swapClass: function (_cls1, _cls2) {
            if (this.hasClass(_cls1))
                this.switchClass(_cls1, _cls2);
            else
                this.switchClass(_cls2, _cls1);
        },
        // Form validation
        // -Username input
        checkUsername: function () {
            let format = /^[a-zA-ZÀ-ž]{3,10}/;
            if (this.val().match(format))
                return true;
            else
                return false;
        },
        // -Email input
        checkEmail: function () {
            let format = /^[a-z0-9]+[\._-]?[a-z0-9]*[@][a-z0-9]+[-]?[a-z0-9]*[\.][a-z]{2,3}$/;
            if (this.val().match(format))
                return true;
            else
                return false;
        },
        // -Password input
        checkPwd: function () {
            let format = /[\w\W]{5,25}/;
            if (this.val().match(format))
                return true;
            else
                return false;
        },
        checkPhone: function () {
            let format = /^\d+/;
            if (this.val().match(format))
                return true;
            else
                return false;
        }
    });

    // jQuery utilities
    (function ($) {
        // -File basename
        $.basename = function (str) {
            if (typeof str !== 'string') return false;
            return str.slice(str.lastIndexOf('/') + 1);
        }

        // -Hashbytes
        $.Hashbytes = function (str, algorithm = "SHA256") {
            switch (algorithm) {
                case "MD5":
                    break;
                case "SHA1":
                    break;
                default: {
                    let hash = CryptoJS.SHA256(str);
                    str = hash.toString(CryptoJS.enc.Hex);
                }
            }
            return str;
        }

        // -Phone format
        $.phoneFormat = function (code, phoneNumber) {
            return `(${code.match(/\d+/g)})${phoneNumber.replace(/(\d{3})(?=(\d{3}))/g, '$1-')}`;
        }

        // Call the load resources
        $.loadResources = function (_app, _ctrl = 'Home', _action = 'Index', _param = {}){
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '../../' + _app + '/' + _ctrl + '/' + _action,
                    method: 'POST',
                    data: _param,
                    dataType: 'json',
                    cache: false,
                    accepts: {
                        json: 'application/json'
                    }
                }).
                done(response => {
                    resolve(response);
                }).
                fail((jqXHR, textStatus, errorThrown) => {
                    console.log('Full error details:', {
                        status: jqXHR.status,
                        responseHeaders: jqXHR.getAllResponseHeaders(),
                        responseText: jqXHR.responseText,
                        contentType: jqXHR.getResponseHeader('Content-Type')
                    });
                    reject(new Error(`Failed to load resources: ${errorThrown || textStatus}`));
                });
            });
        }

        // Call the load components action
        $.loadComponents = function (_app, _ctrl = 'Home', _action = 'Index', _parent) {
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

        // Call the load language
        $.language = function (){
            $('select#lang > option').each(function () {
                if ($(this).attr('data-default') === $(this).val()) {
                    $(this).prop('selected', true);
                    return false;
                }
            });
        }

        $.getAuth = function (_sessionId, _str = '') {
            let inputJson = [];
            return from($.getJSON('../json/CredentialJson_' + _sessionId + '.json')).pipe(
                tap(data => {
                    let json = data.Tokens;

                    json.forEach(it => {
                        let id = it.It.Name;
                        if (id != _str) {
                            inputJson.push(id);
                        }
                    });
                }),
                map(() => inputJson)
            );
        }
    })(jQuery);

    // Global event handlers
    // -Hyperlinks
    $(body).on('click', '.ts-link, aside > figure', function () {
        let url = '../../' + $(this).attr('data-app') + '/' + $(this).attr('data-controller') + '/' + $(this).attr('data-action');
        localStorage.setItem('activeMenu', $(this).attr('data-app'));
        window.location.href = url;
    });

    $(body).on('click', 'form .btn-success', function (e){
        e.preventDefault();
        let form = $(this).parents('form'), url = $(form).attr('action');
        $(form).attr('action', url);
        $(form).trigger ('activate');
    });

    $('.ts-menu').on('click', function (){
        if ($(this).find('.ts-list').hasClass('d-none')){
            $('.ts-menu .ts-list').addClass('d-none');
            $(this).find('.ts-list').removeClass('d-none');
        }else{
            $(this).find('.ts-list').addClass('d-none');
        }
    });

    $('.ts-menu span').on('mouseenter', function (){
        let n = $('.ts-menu .ts-list:not(.d-none)').length;
        if (n > 0)
            $(this).parent().triggerHandler('click');
    });

    $(document).on('click', function (event) {
        if (!$(event.target).closest('.ts-menu').length) {
            $('.ts-menu .ts-list').addClass('d-none');
        }
    });

    // -Manage links
    $(document).ready(function () {
        let app = localStorage.getItem('activeMenu');
        if (app)
            $('aside > figure[data-app="' + app + '"]').addClass('ts-active');
    });
});