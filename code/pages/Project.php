<?php
class ProjectPage extends Page
{
    private static $singular_name = 'Projekt';
    private static $description = 'Seite für Projekte';
    //private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = false;
    private static $allowed_children = array('GalleryPage');

    private static $db = array(
       'Title' => 'Varchar(255)',
    );

    private static $has_many = array(
        'Galleries' => 'Gallery',
    );

    private static $casting = array(
        'ExistingGoogleMap' => 'HTMLText'
    );

    /* Declared within _config.php
     * ShortcodeParser::get('default')
     * ->register('existinggooglemap', array('LocationPage','ExistingGoogleMap'));
    */
    public static function ExistingGoogleMap($arguments, $address = null, $parser = null, $shortcode) {
        $iframeUrl = sprintf(
            "https://mapsengine.google.com/map/embed?mid=%s",
            urlencode($address)
        );

        $width = (isset($arguments['width']) && $arguments['width']) ? $arguments['width'] : "100%";
        $height = (isset($arguments['height']) && $arguments['height']) ? $arguments['height'] : "100%";

        return sprintf(
            '<iframe class="embedded-maps" width="%s" height="%s" src="%s" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>',
            $width,
            $height,
            $iframeUrl
        );
    }

    function getCMSFields() {
        $fields = parent::getCMSFields();

        return $fields;
    }

}

class ProjectPage_Controller extends Page_Controller
{
    private static $allowed_actions = array (
    );

    public function init() {
        parent::init();
    }//init()

}
