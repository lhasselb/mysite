<?php
class Section extends Page
{
    private static $singular_name = 'Bereich';
    private static $plural_name = 'Bereiche';
    private static $description = 'Seite für Workshop und Kurse.';
    //private static $icon = 'mysite/images/workshops.png';
    private static $can_be_root = false;
    private static $allowed_children = array(
        'Section'
    );

    private static $belongs_many_many = array(
        'Courses' => 'Course'
    );

    private static $summary_fields = array(
        'Title' => 'Bereich',
    );

    function getCMSFields(){
        $fields = parent::getCMSFields();
        return $fields;
    }
}


class Section_Controller extends Page_Controller
{
    /**
     * An array of actions that can be accessed via a request. Each array element should be an action name, and the
     * permissions or conditions required to allow the user to access it.
     *
     * <code>
     * array (
     *     'action', // anyone can access this action
     *     'action' => true, // same as above
     *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
     *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
     * );
     * </code>
     *
     * @var array
     */
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
