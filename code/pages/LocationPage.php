<?php
class LocationPage extends Page
{
    private static $singular_name = 'Treffpunkt';
    private static $plural_name = 'Treffpunkte';
    private static $description = 'Seite für Jongliertreffen';
    //private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = false;
    private static $allowed_children = array();

	private static $db = array(
       'Remark' => 'HTMLVarchar(255)',
       'Schedule' => 'Varchar(255)',
       'Location' => 'Varchar(255)',
       'Contact' => 'HTMLVarchar(255)',
       'LocationDescription' => 'Varchar(255)',
       'Map' => 'HTMLVarchar(255)',
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

    function getCMSFields(){
        $fields = parent::getCMSFields();
        $fields->removeFieldFromTab("Root.Main", "Content");
        $content = HtmlEditorField::create('Content','Intro');
        $schedule = TextareaField::create('Schedule','Wann');
        $location = TextareaField::create('Location','Wo');
        $contact = HtmlEditorField::create('Contact','Ansprechpartner');
        $remark = HtmlEditorField::create('Remark','Bemerkung');
        $fields->addFieldToTab('Root.Main', $content,'Metadata');
        $fields->addFieldsToTab('Root.Tabelle', array($schedule,$location,$contact,$remark));
        $description = new TextField('LocationDescription','Beschreibung');
        $fields->addFieldToTab('Root.Landkarte', $description);

        //$mapTab = $fields->findOrMakeTab('Root.Location');
        //$mapTab->setTitle('Landkarte');
        //$description = new TextField('LocationDescription','Beschreibung');
        //$fields->addFieldToTab('Root.Location',$description);
        //$fields->addFieldToTab('Root.Location',$description,'MapPinIcon');
        //$mapPinIcon = $mapTab->fieldByName('MapPinIcon');
        //$mapPinIcon->setTitle('Grafik zur Positionsanzeige.  Leer lassen für Standardgrafik.');

        //$map = HtmlEditorField::create('Map','Google-Karte')->setRows(1);
        //$fields->addFieldToTab('Root.Location',$map,'MapPinIcon');

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
