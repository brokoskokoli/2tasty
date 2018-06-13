
require('assets/js/3rdParty/03-jquery-ui.min.js');
//require('bootstrap-tagsinput');
require('eonasdan-bootstrap-datetimepicker');
require('assets/js/base/user.js');

require('assets/js/3rdParty/10-jquery.instantSearch.js');
require('assets/js/base/search.js');

$('option').mousedown(function(e) {
    e.preventDefault();
    $(this).prop('selected', !$(this).prop('selected'));
    return false;
});
