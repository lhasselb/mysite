<?php
class ProjectsHolder extends Page
{
    private static $singular_name = 'Projekte';
    private static $description = 'Seite zum Gruppieren von Projekten.';
    private static $icon = 'mysite/images/projects.png';
    private static $can_be_root = true;
    private static $allowed_children = array(
        'ProjectPage',
        '*Page'
    );

    function getCMSFields(){
        $fields = parent::getCMSFields();
        return $fields;
    }
}

class ProjectsHolder_Controller extends Page_Controller
{
    private static $allowed_actions = array ();

    public function init()
    {
        parent::init();
        $theme = $this->themeDir();
    }//init()

}
