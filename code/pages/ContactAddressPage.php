<?php
class ContactAddressPage extends Page
{
    private static $singular_name = 'Adresse und Vorstand';
    private static $description = 'Seite für Adresse und Vorstand';
    //private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = true;
    private static $allowed_children = array();

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
        $directorsConfig = GridFieldConfig_RelationEditor::create();
        $fields->insertBefore(new Tab('Vorstand', 'Vorstand'), 'Dependent');
        $managementTitle = TextField::create('ManagementTitle','Vorstand-Ü̱berschrift');
        $fields->addFieldToTab('Root.Vorstand', $managementTitle);
        $directors = GridField::create('Directors', 'Vorstand', $this->Directors(), $directorsConfig);
        $fields->addFieldToTab('Root.Vorstand', $directors);
        return $fields;
    }

}

class ContactAddressPage_Controller extends Page_Controller
{
    private static $allowed_actions = array ();

    public function init() {
        parent::init();
        $theme = $this->themeDir();
    } //init()

}
