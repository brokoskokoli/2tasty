require("assets/js/stX/symfonyArrayCollection.js");
require("assets/js/stX/autoComplete.js");
require('assets/js/3rdParty/bootstrap-tokenfield.min.js');


$(function () {


    $('input.stXtokenField').each(function (index, element) {
        var $element = $(element);
        var tokens = $element.attr('data-availabeTokens').split('|');
        var settings = {
            autocomplete: {
                source: tokens,
                delay: 100
            },
            showAutocompleteOnFocus: true
        };
        $element.tokenfield(settings);
    });

    $('div.ingredients').on('keyup', 'div.amount input', function (event) {
        var $this = $(this);
        var amountText = $this.val();
        var lastChar = amountText[amountText.length -1];
        if (lastChar.length === 1 && lastChar.match(/[a-z]/i) && amountText.indexOf(' ') <= -1) {
            amountText = amountText.substring(0, amountText.length - 1) + ' ' + lastChar;
            $this.val(amountText);
        }

        var parts = amountText.split(" ");
        var lastPart = parts[parts.length - 1];
        var $select = $this.parents('div.row').find('div.unit select');
        $select.find('option').each(function (index, element) {
            var $element = $(element);
            var text = $element.html();
            if (text != '' && text == lastPart) {
                $select.val($element.val());
                $element.attr('selected', 'selected');
                parts.pop();
                $this.val(parts.join(' '));
                $select.focus();
            }
        });
    });
});
