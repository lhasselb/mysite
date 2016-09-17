<?php
/**
 * ContactAddressPage object
 *
 * @package mysite
 * @subpackage pages
 *
 */
class ContactAddressPage extends Page
{
    private static $singular_name = 'Adresse und Vorstand';
    private static $description = 'Seite für Adresse und Vorstand';
    //private static $icon = 'mysite/images/image.png';
    private static $can_be_root = false;
    private static $allowed_children = 'none';

    private static $db = array(
       'ManagementTitle' => 'Varchar(255)',
       'AddressTitle' => 'Varchar(255)',
    );

    private static $has_many = array(
        'Directors' => 'Vorstand'
    );

    function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root.Main')->setTitle('Anschrift');
        $addressTitle = TextField::create('AddressTitle','Anschrift-Ü̱berschrift');
        $address = HtmlEditorField::create('Content','Anschrift');
        $fields->addFieldsToTab('Root.Main', array($addressTitle,$address),'Metadata');
        $fields->insertBefore(new Tab('Vorstand', 'Vorstand'), 'Dependent');
        $managementTitle = TextField::create('ManagementTitle','Vorstand-Ü̱berschrift');
        $fields->addFieldToTab('Root.Vorstand', $managementTitle);
        $directors = GridField::create('Directors', 'Vorstand', $this->Directors(), GridFieldConfig_RecordEditor::create());
        $fields->addFieldToTab('Root.Vorstand', $directors);
        return $fields;
    }

}

class ContactAddressPage_Controller extends Page_Controller
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
    } //init()

} //eof
