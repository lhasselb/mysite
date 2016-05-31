<?php
class LocationPage extends Page
{

    private static $singular_name = 'Location Page';
    private static $description = 'Location';
    //private static $icon = 'mysite/images/calendar.png';

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
        //SS_Log::log($this->Title,SS_Log::WARN);
        return $this->LocationName;
    }

}

class LocationPage_Controller extends Page_Controller
{

	/**
	 * An array of actions that can be accessed via a request. Each array element should be an action name, and the
	 * permissions or conditions required to allow the user to access it.
	 *
	 * <code>
	 * array (
	 *     'action', // anyone can access this action
	 *     'action' => true, // same as above
	 *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
	 *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
	 * );
	 * </code>
	 *
	 * @var array
	 */
	private static $allowed_actions = array (
	);

	public function init() {
		parent::init();
		// You can include any CSS or JS required by your project here.
		// See: http://doc.silverstripe.org/framework/en/reference/requirements
        //SS_Log::log($this->Title,SS_Log::WARN);

	}

}
