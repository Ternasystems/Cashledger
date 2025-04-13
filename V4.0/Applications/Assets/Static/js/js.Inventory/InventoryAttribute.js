// Javascript document

/* Dashboard Index */

$(function (){
    // Declare the strict mode
    "use strict";

    let lang = $('html').attr('lang'), body = $('body');

    $.language();

    $.loadComponents('Inventory', 'Config', $('#main-content').attr('data-component'), 'main-content');

    const observer = new MutationObserver((mutationList, observer) => {
        const $radio = $('input#none');
        if ($radio.length){
            $radio.prop('checked', true);
            observer.disconnect();
        }
    });

    observer.observe(document.body, {childList: true, subtree: true});

    // -Language switch
    $(body).on('change', 'select#lang', function (){
        let lg = $(this).find('option:selected').attr('value');
        window.location.href = '../../../' + lg + '/' + $(this).attr('data-app') + '/' + $(this).attr('data-controller') + '/' + $(this).attr('data-action');
    });

    $(body).on('activate', '#new-attribute, #modify-attribute', function (){
        let bool = true;

        if (typeof $('#attributename').val() !== 'string' || typeof $('#attributetype').val() !== 'string' || typeof $('#attributelocalefr').val() !== 'string' ||
            typeof $('#attributelocaleus').val() !== 'string' || typeof $('#attributeconstraint').val() !== 'string' || typeof $('#attributedesc').val() !== 'string')
            bool = false;

        if (bool)
            $(this).submit();
    });

    $(body).on('change', '.constraint-type input[type="radio"]', function (){
        $('#attributeconstraint').val($(this).val() == 'none' ? '' : $(this).val());
    });

    $(body).on('click', '#attribute-delete-list .bi-trash', function (){
        let url = '../../Inventory/Config/RemoveAttribute', id = $(this).parent().attr('data-id');

        $.ajax({
            url: url,
            method: 'POST',
            data: { attributeId: id }, // Send as an object (recommended)
            cache: false,
            success: function(response) {
                $(body).find('#attribute-delete-list [data-id="' + id + '"]').remove();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });

    $('#main-content').on('click', '#attribute-list .ts-elt', function (){
        let id = $(this).attr('data-id'), form = $(this).parent().prev();
        $.loadResources('Inventory', 'Config', 'LoadAttribute', {_attributeId: id})
            .then(data => {
                $(form).find('input[name="attributename"]').val(data['Name']);
                $(form).find('input[name="attributeid"]').val(id);
                $(form).find('input[name="attributetype"]').val(data['AttributeType']);
                $(form).find('input[name="attributelocale[FR]"]').val(data['FR']);
                $(form).find('input[name="attributelocale[US]"]').val(data['US']);
                $(form).find('input[name="constrainttype"][value="' + data['ConstraintType'] + '"]').prop('checked', true);
                $(form).find('input[name="attributeconstraint"]').val(data['AttributeConstraint']);
                $(form).find('input[name="attributedesc"]').val(data['Description']);
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