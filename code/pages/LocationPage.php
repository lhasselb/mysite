<?php
class LocationPage extends Page
{
    private static $singular_name = 'Treffpunkt';
    private static $description = 'Seite für einen Treffpunkt in den Jongliertreffen';
    //private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = false;
    private static $allowed_children = 'none';//array('*Page');

	private static $db = array(
       'Schedule' => 'HTMLVarchar(255)',            // Wann
       'Location' => 'HTMLVarchar(255)',            // Wo
       'Contact' => 'HTMLVarchar(255)',         // Ansprechpartner
       'Remark' => 'HTMLText(255)',          // Bemerkung (für die Uebersicht)
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

    function getCMSFields() {
        $fields = parent::getCMSFields();
        HtmlEditorConfig::set_active('location');
        $intro = HtmlEditorField::create('Content','Informationen')->setRows(10);
        $schedule = HtmlEditorField::create('Schedule','Wann')->setRows(10);
        $location = HtmlEditorField::create('Location','Wo')->setRows(10);
        $contact = HtmlEditorField::create('Contact','Ansprechpartner')->setRows(10);
        $remark = HtmlEditorField::create('Remark','Bemerkung für die Übersicht')->setRows(10);
        $fields->addFieldsToTab('Root.Main', array($remark, $intro, $schedule, $location, $contact),'Metadata');
        $description = new TextField('LocationDescription','Beschreibung');
        $fields->addFieldToTab('Root.Landkarte', $description);
        $map = TextAreaField::create('Map','Google-IFrame');
        $fields->addFieldToTab('Root.Landkarte', $map);
        $fields->addFieldToTab('Root.Landkarte', new LiteralField('Info','
        <p><span style="color:red;">Achtung: </span>Den Inhalt ür dieses Feld findet man im Quelltext der existierenden JIM-Seite im HTML-Tag iframe, z.B. &lt;iframe src="https://mapsengine.google.com/map/embed?mid=zc1l0sHie8lY.kjVxveFjAD0Q" width="100%" height="550" &gt;&lt;/iframe&gt;. <br/> Die Breite (width) immer auf 100% setzen und die Höhe (height) auf 550. Für neue Karten kann man den <a href="https://www.google.de/mapmaker" target="_blank">Mapmaker</a> nutzen.</p>
        '));
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
        Requirements::javascript('mysite/javascript/Maps.js');
	}//init()

}
