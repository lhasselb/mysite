<?php
class WorkshopsAndCourses extends Page {

    private static $singular_name = 'Workshops und Kurse';
    private static $description = 'Seite mit Workshops und Kursen';
    private static $icon = 'mysite/images/workshop.png';
    private static $allowed_children = array(
        'WorkshopPage'
    );

    private static $db = array();
    private static $has_one = array();
    private static $has_many = array();

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        return $fields;
    }

}
class WorkshopsAndCourses_Controller extends Page_Controller {
}
