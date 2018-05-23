
require("assets/js/stX/symfonyArrayCollection.js");
require("assets/js/stX/autoComplete.js");
require('assets/js/3rdParty/bootstrap-tokenfield.min.js');


$(function() {
    var tokens = $('input.stXtokenField').attr('data-availabeTokens').split('|');
    var settings = {
        autocomplete: {
            source: tokens,
            delay: 100
        },
        showAutocompleteOnFocus: true
    };

    $('input.stXtokenField').first().tokenfield(settings);

});
