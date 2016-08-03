<?php
class LocationPage extends Page
{
    private static $singular_name = 'Treffpunkt';
    private static $description = 'Seite für einen Treffpunkt in den Jongliertreffen';
    //private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = false;
    private static $allowed_children = array();

	private static $db = array(
       'Schedule' => 'Varchar(255)',            // Wann
       'Location' => 'Varchar(255)',            // Wo
       'Contact' => 'HTMLVarchar(255)',         // Ansprechpartner
       'Remark' => 'HTMLVarchar(255)',          // Bemerkung (für die Uebersicht)

       'LocationDescription' => 'Varchar(255)', // Beschreibung
       'Map' => 'Text()',             // Karte
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

    /* Declared within _config.php
     * ShortcodeParser::get('default')
     * ->register('directionsgooglemap', array('LocationPage','DirectionsGoogleMap'));
    */
    public static function DirectionsGoogleMap($arguments, $address = null, $parser = null, $shortcode) {
        $iframeUrl = sprintf(
            "https://maps.google.de/maps?%s",
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

    function getCMSFields(){
        $fields = parent::getCMSFields();

        $intro = HtmlEditorField::create('Content','Informationen');
        $schedule = TextareaField::create('Schedule','Wann');
        $location = TextareaField::create('Location','Wo');
        $contact = HtmlEditorField::create('Contact','Ansprechpartner');
        $remark = HtmlEditorField::create('Remark','Bemerkung für die Übersicht');
        $fields->addFieldsToTab('Root.Main', array($intro, $schedule, $location, $contact, $remark),'Metadata');

        $description = new TextField('LocationDescription','Beschreibung');
        $fields->addFieldToTab('Root.Landkarte', $description);
        $map = TextAreaField::create('Map','Google-IFrame');
        $fields->addFieldToTab('Root.Landkarte', $map);

        return $fields;
    }

    public function getName() {
        return $this->LocationName;
    }

}

class LocationPage_Controller extends Page_Controller
{
	private static $allowed_actions = array (
	);

	public function init() {
		parent::init();
	}//init()

}
