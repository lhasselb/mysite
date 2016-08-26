<?php
class ProjectPage extends Page
{
    private static $singular_name = 'Projekt';
    private static $description = 'Seite für ein Projekt';
    private static $can_be_root = false;
    private static $allowed_children = array(
        'ProjectPage',
        '*Page'
    );

    private static $db = array(
       'Title' => 'Varchar(255)',
    );

    private static $has_one = array(
        'Gallery' => 'Gallery',
    );

    function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab('Root.Main', new LiteralField('Info','
        <p><span style="color:red;">Achtung: </span>Wenn das gewünschte Album noch nicht existiert, kann es unter <a href="admin/gallerymanager/">Foto-Alben</a> (auf der linken Seite in der Navigation) angelegt werden.</p>
        '),'Content');
        $fields->addFieldToTab('Root.Main',
            DropdownField::create('GalleryID', 'Foto-Album', Gallery::get()->map('ID', 'Title'))
            ->setEmptyString('(Bitte Album auswählen)'),'Content');
        return $fields;
    }

    function getGallery() {
        return $gallery = Gallery::get_by_id('Gallery',$this->GalleryID);
    }

}

class ProjectPage_Controller extends Page_Controller
{
    private static $allowed_actions = array ();

    public function init() {
        parent::init();
        $theme = $this->themeDir();
        $gallery = $this->getGallery();
        if($gallery) {
            //$images = $gallery->getImagesString();
            $images = $gallery->getImagesJSON();
            $custom_js = <<<JS
var data = $images;

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
JS;

            Requirements::css('mysite/javascript/galleria/themes/twelve/galleria.twelve.css');
            Requirements::javascript($theme.'/javascript/galleria/src/galleria.js');
            Requirements::javascript('mysite/javascript/galleria/themes/twelve/galleria.twelve.min.js');
            //Requirements::javascriptTemplate('mysite/javascript/Gallery.js', array("imageJson" => $images));
            if(method_exists(Requirements::backend(), "add_callback")) {
                Requirements::backend()->add_callback($theme.'/javascript/galleria/src/galleria.js', $custom_js);
            } else Requirements::customScript($custom_js);

        }//if gallery
    }//init()

}
