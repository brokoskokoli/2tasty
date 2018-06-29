
/**
 * Ajoute un bouton pour ajouter des nouveau entities au Collection
 */
$.fn.stXquickSearch = function() {

    this.widget = this;
    var lastQuery = '';
    var widget = this.widget;
    var $widget = $(widget);
    var self = this;
    var resultContainerId = widget.data('result');
    var $resultContainer = $('#' + resultContainerId);
    var $loader = $(widget.data('loader'));

    var ajaxCounter = 0;
    var timeout = null;

    var link = widget.data('link');
    var parameter = widget.data('parameter');

    $widget.on('keyup', function (event) {
        lastQuery = $widget.val();
        self.debounceInputChange(self.loadResults);
    });

    $widget.on('click', function (event) {
        lastQuery = $widget.val();
        self.loadResults();
    });

    $("body").on('click', (function(e) {
        if (e.target.id == resultContainerId || $(e.target).parents("#"+resultContainerId).length) {

        } else {
            self.hideResults();
        }
    }));

    window.addEventListener('click', function(e){
        if (!$.contains($(resultContainerId), $(e.target))){
            //self.hideResults();
        }
    });

    this.hideResults = function () {
        $resultContainer.html('');
        $resultContainer.css('display', 'none');
    };

    this.showResults = function (data) {
        $resultContainer.html(data);
        $resultContainer.css('display', 'block');
    };

    this.loadResults = function (event) {
        if (lastQuery == '') {
            self.hideResults();
            return;
        }

        $loader.css('visibility', 'visible');
        $.ajax({
            url: link,
            data: parameter + '=' + lastQuery.replace(" ", /\+/g),
            success: function (data) {
                self.showResults(data);
                $loader.css('visibility', 'hidden');
            },
            error: function(){
                $loader.css('visibility', 'hidden');
            },
            timeout: 5000
        });
    };

    this.debounceInputChange = function(callback) {
        var later = function () {
            timeout = null;
            callback();
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, 200);
    }

};


$('input.stXquickSearch').each(function (index, obj) {
    $(obj).stXquickSearch();
})

