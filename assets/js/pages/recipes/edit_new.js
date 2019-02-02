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
        var parts = $this.val().split(" ");
        var lastPart = parts[parts.length - 1];
        var $select = $this.parents('div.row').find('div.unit select');
        $select.find('option').each(function (index, element) {
            var $element = $(element);
            if ($element.html() == lastPart) {
                $select.val(lastPart);
                $element.attr('selected', 'selected');
                parts.pop();
                $this.val(parts.join(' '));
                $select.focus();
            }
        });
    });
});
