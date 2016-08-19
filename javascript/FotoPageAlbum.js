(function($, window, document, undefined) {
    'use strict';

    // init cubeportfolio
    $('#grid-container').cubeportfolio({
        filters: '#filters-container',
        loadMore: '#loadMore-container',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'quicksand',
        gapHorizontal: 35,
        gapVertical: 25,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1100,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,

        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',

        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageInlineInFocus: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} von {{total}} Alben</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;
            var body = $('body');
            $(document).off('keydown.cbp');

            // close
            t.closeButton = $('<div/>', {
                'class': 'cbp-popup-close',
                'title': 'Schließen (Escape Taste)',
                'data-action': 'close'
            }).appendTo(t.navigation);

            // next
            t.nextButton = $('<div/>', {
                'class': 'cbp-popup-next',
                'title': 'Weiter',
                'data-action': 'next'
            }).appendTo(t.navigation);

            // prev
            t.prevButton = $('<div/>', {
                'class': 'cbp-popup-prev',
                'title': 'Zurück',
                'data-action': 'prev'
            }).appendTo(t.navigation);

            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    timeout: 5000
                })
                .done(function(result) {
                    //console.log(result);
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    //console.log(url);
                    t.updateSinglePage("Ups! Bitte die Seite neu laden!");
                });
        },
    });

})(jQuery, window, document);
