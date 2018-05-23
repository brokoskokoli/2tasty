
/**
 * Ajoute un bouton pour ajouter des nouveau entities au Collection
 */
$.fn.stXautoComplete = function() {

    this.widget = this;
    var widget = this.widget;

    var values = widget.data('autocomplete').split('|');

    var $inputWidget = $(widget.find('input')[0]);
    $inputWidget.attr('autocomplete', 'off');

    $inputWidget.autocomplete({
       source: values,
        minLength: 0
    });

    $inputWidget.on('focus', function () {
        $(this).autocomplete("search")
    });


};


$('div.stXautoComplete').each(function (index, obj) {
    $(obj).stXautoComplete();
})

