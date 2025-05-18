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

    $(body).on('activate', '#new-product, #modify-product', function (){
        let bool = true;

        if (typeof $('#productname').val() !== 'string' || typeof $('#categoryid').val() !== 'string' || typeof $('#unitid').val() !== 'string' ||
            ($('#minstock').val().trim() == '' || isNaN($('#minstock').val())) || ($('#maxstock').val().trim() == '' || isNaN($('#maxstock').val()))||
            typeof $('#productlocalefr').val() !== 'string' || typeof $('#productlocaleus').val() !== 'string' || typeof $('#productdesc').val() !== 'string')
            bool = false;

        if (bool)
            $(this).submit();
    });

    $(body).on('change', '#minstock, #maxstock', function (){
        let min = Number($('#minstock').val()), max = Number($('#maxstock').val());
        if (min > max){
            max = min;
            $('#maxstock').val(max);
        }
    });

    $(body).on('change', '#attributcheck', function(){
        if ($(this).prop('checked'))
            $('#attributes').addClass('ts-disabled');
        else
            $('#attributes').removeClass('ts-disabled');
        //
        $('select[name="attributes"]').val('0');
        $('[data-class="formelement"]').parent().remove();
    });

    $(body).on('click', '#product-delete-list .bi-trash', function (){
        let url = '../../Inventory/Config/RemoveProduct', id = $(this).parent().attr('data-id');

        $.ajax({
            url: url,
            method: 'POST',
            data: { productId: id }, // Send as an object (recommended)
            cache: false,
            success: function(response) {
                $(body).find('#product-delete-list [data-id="' + id + '"]').remove();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
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

    $('#main-content').on('click', '#product-list .ts-elt', function () {
        const id = $(this).attr('data-id');
        const form = $(this).parent().prev();

        $.loadResources('Inventory', 'Config', 'LoadProduct', { _productId: id })
            .then(data => {
                // Update basic fields
                $(form).find('input[name="productname"]').val(data.Name);
                $(form).find('input[name="productid"]').val(id);
                $(form).find(`select[name="categoryid"] option[value="${data.CategoryId}"]`).prop('selected', true);
                $(form).find(`select[name="unitid"] option[value="${data.UnitId}"]`).prop('selected', true);
                $(form).find('input[name="minstock"]').val(data.MinStock);
                $(form).find('input[name="maxstock"]').val(data.MaxStock);
                $(form).find('input[name="productlocale[FR]"]').val(data.Locales?.FR || '');
                $(form).find('input[name="productlocale[US]"]').val(data.Locales?.US || '');
                $(form).find('input[name="productdesc"]').val(data.Description || '');

                // Reset attributes
                $(form).find('select[name="attributes"]').val('0'); // Reset to default
                $(form).find('[data-class="formelement"]').parent().remove();

                // Toggle attribute checkbox
                const hasAttributes = data.hasOwnProperty('Attributes');
                $(form).find('input#attributcheck').prop('checked', !hasAttributes).trigger('change');

                // Handle attributes if they exist
                if (hasAttributes) {
                    $(form).find('select[name="attributes"] option[value="0"]').prop('selected', false);
                    (async () => {
                        await selectAttributesAsync(form, data.Attributes);
                        await setAttributeInputValues(form, data.Attributes);
                    })();
                }
            })
            .catch(console.error);
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