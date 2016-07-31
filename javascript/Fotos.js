(function ($) {

   $(document).ready(function() {
        //Galleria.loadTheme('mysite/javascript/galleria/themes/twelve/galleria.twelve.min.js');
        Galleria.configure({
            variation: 'light',
            imageCrop: 'landscape',
            lightbox: true,
            //fullscreenCrop: false,
            maxScaleRatio: 1,
            thumbnails: 'lazy',


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
            responsive:true,
            height:0.5625,
            dataSource: data,
            dataConfig: function(img) {
                return {
                    description: $(img).next('p').html()
                };
            }
        });

        Galleria.ready(function() {
            this.$('thumblink').click();
            this.lazyLoadChunks(5);
        });

    });

})(jQuery);
