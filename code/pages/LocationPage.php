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
	   'LocationName' => 'Varchar(255)'
    );

	private static $has_one = array(
	);

    function getCMSFields(){
        $fields = parent::getCMSFields();
        $textField = new TextField("LocationName","Ortsbezeichnung");
        $fields->addFieldToTab("Root.Main", $textField, "Content");
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
