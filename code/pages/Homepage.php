<?php
class HomePage extends Page
{
    private static $singular_name = 'Startseite';
    private static $description = 'Startseite für JIMEV';
    private static $icon = 'mysite/images/homepage.png';
    private static $can_be_root = true;
    private static $allowed_children = 'none';

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
        if ($this->Alarm()->count() > 0) {
            // remove the buttons if we don't want to allow more records to be added/created
            $alarmConfig->removeComponentsByType('GridFieldAddNewButton');
            $alarmConfig->removeComponentsByType('GridFieldAddExistingAutocompleter');
        }

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

	public function init() {
		parent::init();
		$theme = $this->themeDir();
        Requirements::javascript('mysite/javascript/slider-4.js');
	}//init()

    public function PaginatedLatestNews($num = 10) {
        $start = isset($_GET['start']) ? (int) $_GET['start'] : 0;
        /*
        $itemsToSkip = 0;
        $itemsToReturn = 5;
        return News::Entries($itemsToSkip, $itemsToReturn);*/
        //$item->ClassName == 'Course' && $item->HomepageSectionID == 0
        $list = News::get()
        ->filterAny(array(
            'ClassName' => 'News',
            'HomepageSectionID:GreaterThan' => '0'
        ));
        //->sort('NewsDate','DESC');

        return new PaginatedList($list, $this->getRequest());
    }
}
