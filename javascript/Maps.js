/** Unused  */
(function ($) {

    $(document).ready(function() {

        $('.map-container').click(function() {
            $(this).find('iframe').addClass('clicked');
        }).mouseleave(function() {
            $(this).find('iframe').removeClass('clicked');
        });

    });

})(jQuery);
