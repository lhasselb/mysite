<?php
class EnrollSuccessPage extends Page
{
    private static $singular_name = 'EnrollSuccess';
    private static $description = 'Seite für erfolgreichen Mitgliedsantrag';
    //private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = false;
    private static $allowed_children = array();
    private static $defaults = array (
        'ShowInMenus' => false,
        'ShowInSearch' => false
    );

    private static $db = array();


    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        return $labels;
    }

    function getCMSFields() {
        $fields = parent::getCMSFields();
        return $fields;
    }

    function FormData() {
        return $list = new ArrayData(Session::get('Data'));
    }

}

class EnrollSuccessPage_Controller extends Page_Controller
{
    private static $allowed_actions = array ();

    public function init() {
        parent::init();
    }//init()

}
