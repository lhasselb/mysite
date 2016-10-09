<?php
/**
 * HomePage object
 *
 * @package mysite
 * @subpackage pages
 *
 */
class HomePage extends Page
{
    private static $singular_name = 'Startseite';
    private static $description = 'Startseite für JIMEV';
    private static $icon = 'mysite/images/home.png';
    private static $can_be_root = true;
    private static $allowed_children = 'none';

    private static $has_many = array(
        'Sliders' => 'HomepageSlider.Parent',
        'Alarm' => 'Alarm',
        'News' => 'News'
    );

    function getCMSFields() {

        $fields = parent::getCMSFields();

        // SLIDER
        $sliderConfig = GridFieldConfig_RecordEditor::create();
        $sliderConfig->addComponent(new GridFieldSortableRows('SortOrder'));
        $sliderGridField = new GridField('SLider', 'Bild(er) auf der Startseite', $this->Sliders());
        $sliderGridField->setConfig($sliderConfig);
        $fields->addFieldToTab("Root.Slider-Bilder", $sliderGridField);

        // ALARM
        $alarmConfig = GridFieldConfig_RecordEditor::create();
        if ($this->Alarm()->count() > 0) {
            // remove the buttons if we don't want to allow more records to be added/created
            $alarmConfig->removeComponentsByType('GridFieldAddNewButton');
            $alarmConfig->removeComponentsByType('GridFieldAddExistingAutocompleter');
        }

        $alarmGridField = new GridField('Alarm', 'Alarm auf der Startseite', $this->Alarm());
        $alarmGridField->setConfig($alarmConfig);
        $fields->addFieldToTab("Root.Alarm", $alarmGridField);

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
	private static $allowed_actions = array ('NewsletterForm');

	public function init() {
		parent::init();
		$theme = $this->themeDir();
        Requirements::javascript('mysite/javascript/slider-4.js');
        Requirements::javascript('mysite/javascript/Newsletter.js');
	}//init()

    public function NewsletterForm() {

        $emailField = EmailField::create('Email','E-Mail');
        $emailField->addExtraClass('form-control input-lg c-square');
        $emailField->setAttribute('placeholder','Email');
        //$emailField->setTemplate('FormField_holder_Newsletter');
        // Create fields
        $fields = new FieldList($emailField);
        $fields->setTemplate('FormField_holder_Newsletter');

        // Create actions
        $actions = new FieldList(
            new FormAction('doAddEmail', 'Absenden')
        );

        $form = new Form($this, 'NewsletterForm', $fields, $actions);
        $form->setTemplate('NewsletterForm');
        return $form;
    }

    public function index(SS_HTTPRequest $request) {

        if($request->isAjax()) {
            SS_Log::log('Ajax',SS_Log::WARN);
            return "Ajax response!";
        }

        return array();
    }

    /**
     * Create a news items list
     * @return PaginatedList list containing news items
     */
    public function PaginatedLatestNews($num = 10) {
        $today = date("Y-m-d");
        //SS_Log::log('Today='.$today,SS_Log::WARN);
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
        ))
        ->filter('ExpireDate:GreaterThan' ,$today);
        //->exclude('ExpireDate:LessThan',$today);
        //->sort('NewsDate','DESC');
        foreach ($list as $news) {
            //SS_Log::log('ExpireDate'.$news->ExpireDate,SS_Log::WARN);
        }

        return new PaginatedList($list, $this->getRequest());
    }
} //eof
