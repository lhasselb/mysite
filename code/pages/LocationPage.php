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
       'Contact' => 'Varchar(255)',
    );

	private static $has_one = array(
	);

    function getCMSFields(){
        $fields = parent::getCMSFields();

        $name = new TextField("LocationName","Ortsbezeichnung");
        $schedule = TextareaField::create("Schedule","Wann")->setRows(3);
        $location = TextareaField::create("Location","Wo")->setRows(2);
        $contact = TextareaField::create("Contact","Ansprechpartner")->setRows(2);
        $description = new TextField("LocationDescription","Beschreibung");

        $fields->addFieldsToTab("Root.Main", array($name,$schedule,$location,$contact,$description), "Content");

        //$fields->removeFieldFromTab("Root.Main", "Content");
        $mapTab = $fields->findOrMakeTab('Root.Location');
        $mapTab->setTitle('Landkarte');
        $mapPinIcon = $mapTab->fieldByName('MapPinIcon');
        $mapPinIcon->setTitle('Grafik zur Positionsanzeige.  Leer lassen für Standardgrafik.');
        SS_Log::log('map '.$mapTab->Title(),SS_Log::WARN);

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
