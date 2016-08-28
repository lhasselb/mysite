<?php
class ClubHolder extends Page
{
    private static $singular_name = 'Verein';
    private static $description = 'Seite zum Gruppieren von Vereinsseiten.';
    //private static $icon = 'mysite/images/projects.png';
    private static $can_be_root = true;
    private static $allowed_children = array(
        '*Page',
        'EnrollPage',
    );

    function getCMSFields(){
        $fields = parent::getCMSFields();
        return $fields;
    }
}

class ClubHolder_Controller extends Page_Controller
{
    private static $allowed_actions = array ();

    public function init()
    {
        parent::init();
        $theme = $this->themeDir();
    }//init()

}
