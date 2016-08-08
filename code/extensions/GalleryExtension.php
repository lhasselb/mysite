<?php
/**
 * Extends SilverStripe page object to provide additional images.
 */
class GalleryExtension extends DataExtension
{
    private static $db = array();
    private static $has_one = array(
        'AlbumDescription' => 'Varchar(100)'
    );

    // Create a relation table [OWNER]_GalleryImages
    private static $has_many = array(
        'GalleryImages' => 'GalleryImage'
    );

    public function updateCMSFields(FieldList $fields) {
        $gridFieldConfig = GridFieldConfig_RecordEditor::create();
        $gridFieldConfig->addComponent(new GridFieldBulkUpload());
        $gridFieldConfig->addComponent(new GridFieldBulkManager());
        // Used to determine upload folder
        $uploadfoldername = substr($this->owner->Link(), 1, -1);
        SS_Log::log('if uploadfoldername=' . $uploadfoldername,SS_Log::WARN);
        $gridFieldConfig->getComponentByType('GridFieldBulkUpload')->setUfSetup('setFolderName', $uploadfoldername);
        //$gridFieldConfig->getComponentByType('GridFieldBulkUpload')->setUfSetup('setCanUpload', false);
        $gridFieldConfig->getComponentByType('GridFieldBulkUpload')->setUfSetup('setDisplayFolderName', $uploadfoldername);
        // Customise gridfield
        $gridFieldConfig->removeComponentsByType('GridFieldPaginator'); // Remove default paginator
        $gridFieldConfig->addComponent(new GridFieldPaginator(20)); // Add custom paginator
        $gridFieldConfig->addComponent(new GridFieldSortableRows('SortOrder'));
        $gridFieldConfig->removeComponentsByType('GridFieldAddNewButton'); // We only use bulk upload button
        // Creates sortable grid field
        $gridfield = new GridField("GalleryImages", "Fotos", $this->owner->GalleryImages()->sort("SortOrder"), $gridFieldConfig);
        $fields->addFieldToTab('Root.Fotos', $gridfield);
    }

    public function GetFirstImage() {
        return $this->owner->GalleryImages()->Sort($this->owner->Sorter)->limit(1)->First();
    }

    public function GetGalleryImages() {
        return $this->owner->GalleryImages()->sort("SortOrder");
    }

    public function ImagesJson() {
        $images = $this->owner->GalleryImages();
        //$focuspoint = (class_exists('FocusPointImage')) ? true : false;
        $data = array();
        $thb = 'thumb';
        $img = 'image';
        $big = 'big';
        $tle = 'title';
        $des = 'description';
        foreach ($images as $image) {
            $data[] = array(
                $thb => $image->Image()->CroppedImage(80, 80)->URL,
                $img => $image->Image()->CroppedImage(800, 600)->URL,
                $big => $image->Image()->URL,
                $tle => $image->Title,
                $des => $image->Description
            );
        }
        return Convert::array2json($data);
    }

    public function contentcontrollerInit() {

        $theme = $this->owner->themeDir();
        if(Director::isDev()) {
            Requirements::javascript($theme.'/bower_components/jquery/dist/jquery.js');
        } else {
            Requirements::javascript($theme.'/bower_components/jquery/dist/jquery.min.js');
        }
        //Requirements::javascriptTemplate('mysite/javascript/Data.js', array("ImageDataJson" => $json));
        Requirements::css($theme.'mysite/javascript/galleria/themes/twelve/galleria.twelve.css');
        Requirements::javascript($theme.'/bower_components/galleria/src/galleria.js');
        Requirements::javascript('mysite/javascript/galleria/themes/twelve/galleria.twelve.min.js');
        //Requirements::javascript('mysite/javascript/Fotos.js');
        $json = $this->ImagesJson();
        Requirements::customScript(
<<<JS
        var data = $json;
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
                imageCrop: 'true',
                lightbox: true,
                swipe: true,
                maxScaleRatio: 1,
                thumbnails: 'lazy',
                responsive:true,
                height: 0.75,

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
                dataConfig: function(img) {
                    return {
                        description: $(img).next('p').html()
                    };
                }
            });
            /* Show thunbs as default view */
            /*Galleria.ready(function() {
                this.$('thumblink').click();
                this.lazyLoadChunks(5);
            });*/
        });
    })(jQuery);
JS
        );


    } //init

}
