    var $imageJson;

    (function($) {
        $(document).ready(function(){

            /*Galleria.loadTheme('mysite/javascript/galleria/themes/twelve/galleria.twelve.min.js');*/
            /*
             * Setting a relative height (16/9 ratio = 0.5625)
             * Setting a relative height (4/3 ratio = 0.75)
             * imageCrop: true,
             * thumbCrop: 'height',
             * transition: 'fade',
             * easing: 'galleriaOut',
             * initialTransition: 'fadeslide',
             * show: 0,
             * _hideDock: Galleria.TOUCH ? false : true,
             * //autoplay: 5000
             */
            Galleria.configure({
                variation: 'light',
                lightbox: true,
                swipe: true,
                maxScaleRatio: 1,
                thumbnails: 'lazy',
                responsive:true,
                show: 0,
                width: 400,
                height: 300,


                // Toggles the fullscreen button
                _showFullscreen: true,
                // Toggles the lightbox button
                _showPopout: true,
                // Toggles the progress bar when playing a slideshow
                _showProgress: true,
                // Toggles tooltip
                _showTooltip: true,

                // Localized strings, modify these if you want tooltips in your language
                _locale: {
                    show_thumbnails: "Zeige Miniaturbild ",
                    hide_thumbnails: "Verberge Miniaturbild ",
                    play: "Diashow abspielen ",
                    pause: "Diashow anhalten",
                    enter_fullscreen: "Öffne Vollbild",
                    exit_fullscreen: "Beende Vollbild",
                    popout_image: "Bild in eigenem Fenster",
                    showing_image: "Anzeige von Bild %s von %s"
                }
            });

            Galleria.run('.galleria', {
                dataSource: data,
                /*dataConfig: function(img) {
                    return {
                        description: $(img).next('p').html()
                    };
                }*/
            });
            /* Show thunbs as default view */
            Galleria.ready(function() {
                //this.$('thumblink').click();
                this.lazyLoadChunks(5);
            });
        });
    })(jQuery);
