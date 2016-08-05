<?php
class HomePage extends Page
{
    private static $singular_name = 'Startseite';
    private static $description = 'Startseite für JIMEV';
    private static $icon = 'mysite/images/homepage.png';

    private static $has_many = array(
        'Sliders' => 'HomepageSlider.Parent',
        'Alarm' => 'Alarm',
        'News' => 'News'
    );

	function getCMSFields() {
		$fields = parent::getCMSFields();
        $sliderConfig = GridFieldConfig_RecordEditor::create();
        $sliderGridField = new GridField('SLider', 'Bild(er) auf der Startseite', $this->Sliders());
        $sliderGridField->setConfig($sliderConfig);
        $alarmConfig = GridFieldConfig_RecordEditor::create();
        $alarmGridField = new GridField('Alarm', 'Alarm auf der Startseite', $this->Alarm());
        $alarmGridField->setConfig($alarmConfig);
        /*$newsConfig = GridFieldConfig_RecordEditor::create();
        $newsGridField = new GridField('News', 'News auf der Startseite', $this->News());
        $newsGridField->setConfig($newsConfig);*/
        $fields->addFieldToTab("Root.Alarm", $alarmGridField);
        /*$fields->addFieldToTab("Root.News", $newsGridField);*/
        $fields->addFieldToTab("Root.Slider-Bilder", $sliderGridField);
        return $fields;
	}

    public function LatestNews() {
        $itemsToSkip = 0;
        $itemsToReturn = 5;
        return News::Entries($itemsToSkip, $itemsToReturn);
    }
}


class HomePage_Controller extends Page_Controller
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
	private static $allowed_actions = array ();

	public function init()
	{
		parent::init();
		$theme = $this->themeDir();


	}//init()

}
