<?php
class LocationHolder extends Page
{
    private static $singular_name = 'Jongliertreffen';
    private static $description = 'Seite zum Gruppieren von Treffpunkten.';
    private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = true;
    private static $allowed_children = array(
        'LocationPage',
        '*Page'
    );

    function getCMSFields(){
        $fields = parent::getCMSFields();
        return $fields;
    }
}

class LocationHolder_Controller extends Page_Controller
{
    private static $allowed_actions = array ();

    public function init()
    {
        parent::init();
        $theme = $this->themeDir();
    }//init()

}
