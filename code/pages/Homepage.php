<?php
class Homepage extends Page
{
	private static $singular_name = 'Homepage';
	private static $description = 'Homepage für JIMEV';
	private static $icon = 'mysite/images/homepage.png';
	private static $db = array('Alarm' => 'HTMLVarchar(255)');
	private static $has_one = array();
    private static $has_many = array(
        'Sliders' => 'HomepageSlider.Parent'
    );

	function getCMSFields(){
		$fields = parent::getCMSFields();

		$htmlEditorField = new HTMLEditorField("Alarm","Alarmmeldung");
		$htmlEditorField->setRows(1);
		$fields->addFieldToTab("Root.Main", $htmlEditorField, "Content");

        $gridFieldConfig = GridFieldConfig::create()->addComponents(
          new GridFieldToolbarHeader(),
          new GridFieldAddNewButton('toolbar-header-right'),
          new GridFieldSortableHeader(),
          new GridFieldDataColumns(),
          new GridFieldPaginator(10),
          new GridFieldEditButton(),
          new GridFieldDeleteAction(),
          new GridFieldDetailForm()
        );

        $gridField = new GridField('SLiders', 'Homepage Slider', $this->Sliders(), $gridFieldConfig);
        $fields->addFieldToTab("Root.Sliders", $gridField);

        return $fields;
	}
}


class Homepage_Controller extends Page_Controller
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
