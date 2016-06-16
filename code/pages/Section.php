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

    /*public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);
        $labels['Title'] = 'Seitenname';
        $labels['MenuTitle'] = 'Navigationsbezeichnung';
        $labels['URLSegment'] = 'URL-Segment';
        $labels['News'] = 'News';
        $labels['CourseDateStart'] = 'Start-Datum';
        $labels['CourseDateEnd'] = 'End-Datum';
        $labels['Location'] = 'Ort';
        $labels['Content'] = 'Inhalt';
        $labels['NewsImage'] = 'News-Bild';
        $labels['ContentImage'] = 'Inhalts-Bild';
        $labels['HomepageSection'] = 'Bereich auf der Startseite';
        return $labels;
    }*/

    function getCMSFields(){
        $fields = parent::getCMSFields();
        return $fields;
    }

    /*
     * Overloading many_many method Courses to use SortOrder
     */
    /*public function Courses() {
        //SS_Log::log(' getCourses() called',SS_Log::WARN);
        return $this->getManyManyComponents('Courses')->sort('SortOrder');
    }*/

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
