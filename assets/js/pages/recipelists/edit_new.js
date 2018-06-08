
require("assets/js/stX/symfonyArrayCollection.js");
require("assets/js/stX/autoComplete.js");
require('assets/js/3rdParty/bootstrap-tokenfield.min.js');


$(function() {


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
    })

});
