<?php
class ProjectPage extends Page
{
    private static $singular_name = 'Projekte';
    private static $description = 'Seite zum Darstellen von Projekten.';
    private static $icon = 'mysite/images/projects.png';
    private static $can_be_root = true;
    //TODO: limit to none
    private static $allowed_children = array(
        'ProjectPage',
        '*Page'
    );

    function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab('Root.Main', new LiteralField('Info','
        <p><span style="color:red;">Achtung: </span>Projekte werden unter <a href="admin/projectmanager/">Projekte</a> (auf der linken Seite in der Navigation) verwaltet.</p>
        '),'Content');

        return $fields;
    }

    public function Projects()
    {
        return Project::get();
    }

    public function getProjectPageTags() {
        $usedtags = array();
        foreach ($this->Projects() as $project) {
            $currentTagList = $project->ProjectTags();
            foreach ($currentTagList as $tag) {
                // Add ProjectTag object to array
                array_push($usedtags,$tag);
            }
        }
        // Limit to used ones
        // this requires a __toString() method for the object compared
        // see GalleryTag __toString()
        return new ArrayList(array_unique($usedtags));
    }

    public function getProjectPageYears() {

        $usedYears = array();
        foreach ($this->Projects() as $project) {
            array_push($usedYears,$project);
        }

        // Limit to used ones
        // this requires a __toString() method for the object compared
        // see Project __toString()
        $list = new ArrayList(array_unique($usedYears));
        return $list->sort('ProjectDate', 'DESC');
    }

}

class ProjectPage_Controller extends Page_Controller
{
    private static $allowed_actions = array ('projekt');

    public function init() {
        parent::init();
        $theme = $this->themeDir();
        Requirements::javascript('mysite/javascript/ProjectPage.js');
    }//init()

    public function projekt(SS_HTTPRequest $request) {
        //SS_Log::log('ID='.$request->param('ID'),SS_Log::WARN);
        //$project = Project::get_by_id('Project',$request->param('ID'));
        //SS_Log::log('ID='.Convert::raw2sql($request->param('ID')),SS_Log::WARN);
        $project = Project::get_by_url_segment(Convert::raw2sql($request->param('ID')));
        //SS_Log::log($project->ProjectTitle,SS_Log::WARN);
        if(!$project) {
            return $this->httpError(404,'Das gewünschte Projekt existiert nicht.');
        }
        return array('Project' => $project);
    }

}
