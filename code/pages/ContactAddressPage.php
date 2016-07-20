<?php
class ContactAddressPage extends Page
{
    private static $singular_name = 'Adresse und Vorstand';
    private static $description = 'Seite für Adresse und Vorstand';
    //private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = true;
    private static $allowed_children = array();

    private static $db = array(
    );

    private static $has_many = array(
        'Directors' => 'Vorstand'
    );

    function getCMSFields()
    {
        $fields = parent::getCMSFields();


        $directorsConfig = GridFieldConfig_RelationEditor::create();
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
    }//init()

}
