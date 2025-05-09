// Javascript document

/* Dashboard Index */

$(function (){
    // Declare the strict mode
    "use strict";

    let lang = $('html').attr('lang'), body = $('body');

    $.language();

    $.loadComponents('Inventory', 'StockIn', $('#main-content').attr('data-component'), 'main-content');

    // -Language switch
    $('select#lang').on('change', function (){
        let lg = $(this).find('option:selected').attr('value');
        window.location.href = '../../../' + lg + '/' + $(this).attr('data-app') + '/' + $(this).attr('data-controller') + '/' + $(this).attr('data-action');
    });

    $(body).on('activate', '#new-stockdelivery', function (){
        let bool = true;

        if (typeof $('#deliverydesc').val() !== 'string')
            bool = false;

        if (bool)
            $(this).submit();
    });

    $(body).on('click', '#delivery-list .form-row', function (){
        let deliveryId = $(this).attr('data-id'), item = $(this);
        $(item).toggleClass('ts-active');
        $.ajax({
            url: 'AddListItem',
            method: 'POST',
            data: {deliveryId: deliveryId},
            success: function (response){
                if ($(item).hasClass('ts-active'))
                    $(item).after(response);
                else
                    $(item).next().remove();
            }
        });
    });

    $(body).on('activate', '#new-stock', function (){
        let bool = true;

        if ((typeof $('#deliverynumber').val() !== 'string' || $('#deliverynumber').val().trim() == '') || ($('#unitcost').val().trim() == '' || isNaN($('#unitcost').val()))
            || (typeof $('#deliveryreference').val() !== 'string' || $('#deliveryreference').val().trim() == '')
            || (typeof $('#batchnumber').val() !== 'string' || $('#batchnumber').val().trim() == '') || ($('#stockquantity').val().trim() == '' || isNaN($('#stockquantity').val())))
            bool = false;

        if (bool){
            if (!$('#deliverynumber').hasClass('ts-disabled')){
                const dte = new Date($('#deliverydate').val());
                const day = String(dte.getDate()).padStart(2, '0');
                const month = String(dte.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
                const year = dte.getFullYear();
                const hours = String(dte.getHours()).padStart(2, '0');
                const minutes = String(dte.getMinutes()).padStart(2, '0');
                //
                $('#DeliveryNumber').text($('#deliverynumber').val());
                $('#DeliveryNumber').attr('data-value', $('#deliverynumber').val());
                $('input[name="deliverynumber"]').attr('value', $('#deliverynumber').val());
                $('#DeliveryDate').text(`${day}/${month}/${year} ${hours}:${minutes}`);
                $('#DeliveryDate').attr('data-value', $('#deliverydate').val());
                $('input[name="deliverydate"]').attr('value', $('#deliverydate').val());
                $('#Reference').text($('#deliveryreference').val());
                $('#Reference').attr('data-value', $('#deliveryreference').val());
                $('input[name="deliveryreference"]').attr('value',$('#deliveryreference').val() );
                $('#HeaderSupplier').text($('#supplierid').find('option:selected').text());
                $('#HeaderSupplier').attr('data-value', $('#supplierid').find('option:selected').attr('value'));
                $('input[name="supplierid"]').attr('value', $('#supplierid').find('option:selected').attr('value'));
                //
                $('#deliverynumber, #deliveryreference, #deliverydate, #supplierid').addClass('ts-disabled');
            }
            //
            $.ajax({
                url: $(this).attr('action'), // Make sure your form has an 'action' attribute
                method: $(this).attr('method') || 'POST', // Default to POST if not specified
                data: $(this).serialize(), // Serialize the form data
                success: function (response) {
                    const currentTotal = parseFloat($('.form-area #totalcost span').text().replace(/[^\d.-]/g, '')) || 0;
                    const responseTotal = parseFloat($(response).find('div[data-id="total"]').text().replace(/[^\d.-]/g, '')) || 0;
                    const total = currentTotal + responseTotal;

                    $('input[name="state"]').attr('value', 'true');
                    $('.form-area #totalcost').before(response);
                    $('.form-area #totalcost span').text(new Intl.NumberFormat(lang, {
                        style: 'currency',
                        currency: 'XAF'
                    }).format(total));
                },
                error: function (xhr, status, error) {
                    console.error('Submission failed:', status, error);
                }
            });
        }
    });

    // Attributes
    $(body).on('change', '#attributcheck', function(){
        if ($(this).prop('checked'))
            $('#attributes').addClass('ts-disabled');
        else
            $('#attributes').removeClass('ts-disabled');
        //
        $('[data-class="formelement"]').parent().remove();
    });

    let isProcessingChange = false, previousSelection = [];

    $(document).on('change', 'select[name="attributes"]', function () {
        if (isProcessingChange) return;
        isProcessingChange = true;

        const $select = $(this);
        const currentSelection = Array.from(this.selectedOptions).map(opt => opt.value);
        const selectionLocale = Array.from(this.selectedOptions).map(opt => opt.text);
        const selectionType = Array.from(this.selectedOptions).map(opt => opt.getAttribute('data-type'));
        const selectionTable = Array.from(this.selectedOptions).map(opt => opt.getAttribute('data-table'));

        const added = currentSelection.filter(val => !previousSelection.includes(val));
        const removed = previousSelection.filter(val => !currentSelection.includes(val));

        // Cleanup
        removed.forEach(val => {
            $(`[data-class="formelement"][id="${val}"]`).parent().remove();
        });

        const additions = added.map(val => {
            const label = selectionLocale[currentSelection.indexOf(val)];
            const attrType = selectionType[currentSelection.indexOf(val)];
            const attrTable = selectionTable[currentSelection.indexOf(val)];
            return $.loadItems('Inventory', 'Config', 'AddItem', 'select[name="attributes"]', {attrType, attrTable, label, value: val});
        });

        Promise.all(additions).then(() => {
            isProcessingChange = false;
            $select.trigger('attributes:changeComplete');
        });

        previousSelection = currentSelection;
    });

    // Helper: Sequentially select attributes and wait for AJAX + DOM updates
    async function selectAttributesAsync(form, attributes) {
        const $select = $(form).find('select[name="attributes"]');
        const attributeKeys = Object.keys(attributes);

        previousSelection = []; // 🛠️ Reset selection tracking

        $select.val(attributeKeys).trigger('change');

        await new Promise(resolve => {
            $select.one('attributes:changeComplete', resolve);
        });
    }

    // Helper: Set values for dynamically loaded inputs
    async function setAttributeInputValues(form, attributes) {
        for (const attr of Object.keys(attributes)) {
            await waitForInput(form, attr); // Wait until input is in the DOM

            const input = $(form).find(`[data-class="formelement"][id="${attr}"]`);
            if (input.length) input.val(attributes[attr]);
        }
    }

    // Helper: Wait for the input to exist in the DOM (max 500ms)
    function waitForInput(form, attr) {
        return new Promise(resolve => {
            const selector = `[data-class="formelement"][id="${attr}"]`;

            const checkExist = () => {
                const input = $(form).find(selector);
                if (input.length) {
                    resolve();
                } else {
                    setTimeout(checkExist, 10); // Retry after 10ms
                }
            };

            checkExist();
        });
    }

    // -Nav links
    $(body).on('click', '.ts-view', function (e) {
        e.preventDefault(); // Prevent default anchor behavior
        const app = $(this).attr('data-app');
        const component = $(this).attr('data-component');
        const parent = $(this).attr('data-parent');
        const actionUrl = `../../${app}/StockIn/${component}`;

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