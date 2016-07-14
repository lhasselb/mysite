<?php
class KontaktPage extends Page
{
    private static $singular_name = 'Kontakt';
    private static $description = 'Seite für Kontakt';
    //private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = true;
    private static $allowed_children = array();

    private static $db = array(
       'Mailinglists' => 'HTMLText'
    );
    private static $has_many = array(
        'Directors' => 'Vorstand',
        'FacebookLinks' => 'FacebookLink'
    );

    function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $facebooConfig = GridFieldConfig_RelationEditor::create();

        $facebookGruppen = GridField::create('FacebookLinks', 'Facebook Gruppen', $this->FacebookLinks(), $facebooConfig);
        $fields->addFieldToTab('Root.Facebook', $facebookGruppen);

        $directorsConfig = GridFieldConfig_RelationEditor::create();
        $directors = GridField::create('Directors', 'Vorstand', $this->Directors(), $directorsConfig);
        $fields->addFieldToTab('Root.Vorstand', $directors);

        $mailingLists = HtmlEditorField::create('Mailinglists','Mailing-Listen');
        $fields->addFieldToTab('Root.Seiteninhalt', $mailingLists);
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
