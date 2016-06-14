<?php
class Section extends Page
{
    private static $singular_name = 'Bereich';
    private static $description = 'Seite für Workshop und Kurse.';
    //private static $icon = 'mysite/images/workshops.png';
    private static $can_be_root = false;
    private static $allowed_children = array(
        'Section'
    );

    private static $has_one = array();

    private static $many_many = array(
        'Courses' => 'Course'
    );

    function getCMSFields(){
        $fields = parent::getCMSFields();
        //$gridFieldConfig = GridFieldConfig_RelationEditor::create()
        //->addComponents(new GridFieldDeleteAction('unlinkrelation'));

        $gridfield = new GridField("Courses", "Workshops und Kurse", $this->Courses(), GridFieldConfig_RelationEditor::create());
        $fields->addFieldToTab('Root.Kurse', $gridfield);
        return $fields;
    }
}


class Section_Controller extends Page_Controller
{
    private static $allowed_actions = array ();

    public function init()
    {
        parent::init();
        $theme = $this->themeDir();
    }//init()


}
