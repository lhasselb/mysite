<?php
class SectionPage extends Page
{
    private static $singular_name = 'Bereich in Workshops und Kurse';
    private static $description = 'Enhält Workshops und Kurse für einen Bereich.';
    //private static $icon = 'mysite/images/workshops.png';
    private static $can_be_root = false;
    private static $allowed_children = array(
        'SectionPage'
    );

    private static $belongs_many_many = array(
        'Courses' => 'Course'
    );

    private static $summary_fields = array(
        'Title' => 'Bereich',
    );

    function getCMSFields(){
        $fields = parent::getCMSFields();
        $fields->removeFieldFromTab('Root.Main','Content');
        $fields->addFieldToTab('Root.Main', new LiteralField('Info','
        <p><span style="color:red;">Achtung: </span>Zum Bearbeiten der Kurse bitte <a href="admin/coursemanager/">Workshops und Kurse</a> auf der linken Seite in der Navigation wählen und dort editieren.</p>
        '),'Metadata');
        return $fields;
    }

    public function OtherCourses($id){
        return $this->Courses()->exclude('ID',$id);
    }
}


class SectionPage_Controller extends Page_Controller
{
    private static $allowed_actions = array ('kurs');

    public function kurs(SS_HTTPRequest $request) {
        $course = Course::get_by_url_segment(Convert::raw2sql($request->param('ID')));
        if(!$course) {
            return $this->httpError(404,'Der gewünschte Kurs existiert nicht.');
        }
        return array (
            'Course' => $course
        );
    }

    public function PaginatedCourses ($num = 5)
    {
        $start = isset($_GET['start']) ? (int) $_GET['start'] : 0;
        //SS_Log::log('start='.$start,SS_Log::WARN);
        //SS_Log::log('list count='.$this->Courses()->count(),SS_Log::WARN);
        $list = $this->Courses()->sort('NewsDate', 'DESC');
        $courses = PaginatedList::create($list,$this->getRequest())->setPageLength($num);
        //SS_Log::log('paginated course count='.$courses->count(),SS_Log::WARN);
        return $courses;
    }

    public function getCurrentCourse() {
        $Params = $this->getURLParams();
        $URLSegment = Convert::raw2sql($Params['ID']);

        if($URLSegment && $course = DataObject::get_one('Course', "URLSegment = '" . $URLSegment . "'")) {
            return $course;
        }
    }

    public function Breadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        $pages = $this->getBreadcrumbItems($maxDepth, $stopAtPageType, $showHidden);
        $template = new SSViewer('BreadcrumbsTemplate');
        return $template->process($this->customise(new ArrayData(array(
            "Pages" => $pages,
            "Unlinked" => $unlinked
        ))));
    }

    public function getBreadcrumbItems($maxDepth = 20, $stopAtPageType = false, $showHidden = false) {
        $page = $this;
        $pages = array();
        if($course = $this->getCurrentCourse()) {
            array_push($pages, $this->getCurrentCourse());
        }
        while(
            $page
            && (!$maxDepth || count($pages) < $maxDepth)
            && (!$stopAtPageType || $page->ClassName != $stopAtPageType)
        ) {
            if($showHidden || $page->ShowInMenus || ($page->ID == $this->ID)) {
                $pages[] = $page;
            }
            $page = $page->Parent;
        }
        return new ArrayList(array_reverse($pages));
    }

    public function init() {
        parent::init();
        $theme = $this->themeDir();
    }//init()

}
