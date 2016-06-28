<?php
class SectionHolder extends Page
{
    private static $singular_name = 'Workshops und Kurse';
    private static $description = 'Seite zum Gruppieren von Bereichen.';
    private static $icon = 'mysite/images/workshops.png';
    private static $allowed_children = array(
        'SectionPage'
    );

    function getCMSFields(){
        $fields = parent::getCMSFields();
        return $fields;
    }
}

class SectionHolder_Controller extends Page_Controller
{
    private static $allowed_actions = array ();

    public function init()
    {
        parent::init();
        $theme = $this->themeDir();
    }//init()

}
