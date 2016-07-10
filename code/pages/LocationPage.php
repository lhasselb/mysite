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
	   'LocationName' => 'Varchar(255)',
       'LocationDescription' => 'Varchar(255)',
       'Schedule' => 'Varchar(255)',
       'Location' => 'Varchar(255)',
       'Contact' => 'HTMLVarchar(255)',
       'Map' => 'HTMLVarchar(255)',
    );

	private static $has_one = array(
	);

    function getCMSFields(){
        $fields = parent::getCMSFields();
        //$fields->removeFieldFromTab("Root.Main", "Content");

        $schedule = TextareaField::create('Schedule','Wann')->setRows(3);
        $location = TextareaField::create('Location','Wo')->setRows(2);
        $contact = HtmlEditorField::create('Contact','Ansprechpartner')->setRows(1);

        $fields->addFieldsToTab('Root.Main', array($schedule,$location,$contact));

        $mapTab = $fields->findOrMakeTab('Root.Location');
        $mapTab->setTitle('Landkarte');
        $name = new TextField('LocationName','Ortsbezeichnung');
        $fields->addFieldToTab('Root.Location',$name,'MapPinIcon');
        $description = new TextField('LocationDescription','Beschreibung');
        $fields->addFieldToTab('Root.Location',$description,'MapPinIcon');
        $mapPinIcon = $mapTab->fieldByName('MapPinIcon');
        $mapPinIcon->setTitle('Grafik zur Positionsanzeige.  Leer lassen für Standardgrafik.');
        $map = HtmlEditorField::create('Map','Landkarte')->setRows(1);
        $fields->addFieldToTab('Root.Location',$map,'MapPinIcon');

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
