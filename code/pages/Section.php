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

    static $many_many_extraFields = array(
        'Courses' => array(
            'SortOrder' => 'Int'
        )
    );

    function getCMSFields(){
        $fields = parent::getCMSFields();
        $config = GridFieldConfig_RelationEditor::create();
        $config->addComponents(new GridFieldSortableRows('SortOrder'));
        $gridfield = GridField::create("Courses", "Workshops und Kurse", $this->Courses(), $config);
        $fields->addFieldToTab('Root.Kurse', $gridfield);
        return $fields;
    }

    public function Courses() {
        SS_Log::log(' getCourses() called',SS_Log::WARN);
        return $this->getManyManyComponents('Courses')->sort('SortOrder');
    }

}


class Section_Controller extends Page_Controller
{
    private static $allowed_actions = array ('kurs');

    public function kurs(SS_HTTPRequest $request) {

        $course = Course::get_by_url_segment(Convert::raw2sql($request->param('ID')));
        //SS_Log::log(' kurs() course='.$course->Title,SS_Log::WARN);
        //$course = Course::get()->byID($request->param('ID'));

        if(!$course) {
            return $this->httpError(404,'Der gewünschte Kurs existiert nicht.');
        }

        return array (
            'Course' => $course
        );

    }

    public function init()
    {
        parent::init();
        $theme = $this->themeDir();
    }//init()


}
