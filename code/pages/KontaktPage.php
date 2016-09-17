<?php
class KontaktPage extends Page
{
    private static $singular_name = 'Kontakt';
    private static $description = 'Seite für Kontakt';
    //private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = true;
    private static $allowed_children = 'none';

    private static $db = array(
       //'Mailinglists' => 'HTMLText'
    );

    private static $has_many = array(
        'FacebookLinks' => 'FacebookLink'
    );

    function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $facebookGruppen = GridField::create('FacebookLinks', 'Facebook Gruppen', $this->FacebookLinks(), GridFieldConfig_RecordEditor::create());
        $fields->addFieldToTab('Root.Facebook', $facebookGruppen);
        return $fields;
    }

}

class KontaktPage_Controller extends Page_Controller
{
    private static $allowed_actions = array ();

    public function init() {
        parent::init();
        $theme = $this->themeDir();
    }//init()

}
