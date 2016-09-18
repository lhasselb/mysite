<?php
class Project extends DataObject implements Linkable
{
    private static $singular_name = 'Projekt';

    private static $db = array(
        'ProjectTitle' => 'Varchar(255)',
        'URLSegment' => 'Varchar(255)',
        'ProjectDescription' => 'Varchar(255)',
        'ProjectDate' => 'Date',
        'ProjectContent' => 'HTMLText'
    );

    private static $has_one = array(
        'ProjectPage' => 'ProjectPage',
        'ProjectImage' => 'Image'
    );

    private static $many_many = array(
        'ProjectTags' => 'ProjectTag'
    );

    private static $summary_fields = array(
        'ProjectTitle' => 'Name',
        'ProjectDescription' => 'Beschreibung',
        'getNiceProjectDate' => 'Datum',
        'getTags' => 'Bereiche',
        'getThumbnail' => 'Bild'
    );

    public function getNiceProjectDate()
    {
        $date = new Date();
        $date->setValue($this->ProjectDate);
        return $date->Format('d.m.Y');
    }

    public function getTags() {
        $tags = [];
        foreach ($this->ProjectTags() as $tag) {
            array_push($tags,$tag->Title);
        }
        return implode(',',$tags);
    }

    public function getThumbnail() {
        return $this->ProjectImage()->SetHeight(50);
    }

    public function getProjectYear() {
        $date = new Date();
        $date->setValue($this->ProjectDate);
        //SS_Log::log('YEAR='. $date->Year(), SS_Log::WARN);
        return $date->Year();
    }

    public function getTitle() {
        return $this->ProjectTitle;
    }

    /*
     * Used to compare within array_unique() in ProjectPage.php
     */
    public function __toString() {
        return $this->getProjectYear();
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();
        $fields->removeByName('ProjectPageID');
        $fields->removeByName('ProjectTags');

        $fields->fieldByName('Root.Main')->setTitle('Projekt');

        $fields->addFieldToTab('Root.Main', TextField::create('URLSegment', $this->fieldLabel('URLSegment'))
        ->setDescription('Wird beim Speichern generiert, bitte nur in vollem Bewusstsein ändern.'));

        $fields->addFieldToTab('Root.Projekt-Inhalt', HtmlEditorField::create('ProjectContent','Inhalt'));

        $date = DateField::create('ProjectDate','Datum')
            ->setConfig('dataformat', 'yyyy')
            ->setConfig('showcalendar', true);
        $date->setDescription(sprintf('z.B. %s', Convert::raw2xml(Zend_Date::now()->toString('dd.MM.yyyy'))));
        $fields->addFieldToTab('Root.Main', $date);

        $tags = TagField::create('ProjectTags','Projekt-Bereich(e)',ProjectTag::get(),$this->ProjectTags())
        ->setShouldLazyLoad(true) // tags should be lazy loaded
        ->setCanCreate(true);     // new tag DataObjects can be created
        $fields->addFieldToTab('Root.Main', $tags);

        $projectImage = new UploadField('ProjectImage', 'Projekt-Bild');
        $projectImage->setFolderName('projekte');
        $projectImage->setDisplayFolderName('projekte');
        $fields->addFieldToTab('Root.Main', $projectImage);

        return $fields;
    }

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);
        $labels['ProjectTitle'] = 'Name';
        $labels['ProjectDescription'] = 'Beschreibung';
        $labels['ProjectDate'] = 'Datum';

        return $labels;
    }

    public function Link() {
        $projectPage = DataObject::get_one('ProjectPage');
        //return $projectPage->Link();
        //return Controller::join_links($projectPage->Link(),'projekt',$this->ID);
        return Controller::join_links($projectPage->Link(),'projekt',$this->URLSegment);
    }

    /**
     * Label displayed in "Insert link" menu
     * @return string
     */
    public static function LinkLabel() {
         return 'Projekt';
    }

    /**
     * Replace a "[{$class}_link,id=n]" shortcode with a link to the page with the corresponding ID.
     * @param array  $arguments Arguments to the shortcode
     * @param string $content   Content of the returned link (optional)
     * @param object $parser    Specify a parser to parse the content (see {@link ShortCodeParser})
     * @return string anchor Link to the DO page
     *
     * @return string
     */
    static public function link_shortcode_handler($arguments, $content = null, $parser = null) {
        if (!isset($arguments['id']) || !is_numeric($arguments['id'])) {
            return;
        }

        $id =  $arguments['id'];
        $do = DataObject::get_one(__CLASS__, "ID=$id");

        if (!$do) {
            $do = DataObject::get_one('ErrorPage', '"ErrorCode" = \'404\'');
            return $do->Link();
        }

        if ($content) {
            return sprintf('<a href="%s">%s</a>', $do->Link(), $parser->parse($content));
        } else {
            return $do->Link();
        }
    }

    /**
     * Check if there is already a DOAP with this URLSegment
     */
    public function LookForExistingURLSegment($URLSegment, $ID)
    {
        return Course::get()->filter(
            'URLSegment',$URLSegment
        )->exclude('ID', $ID)->exists();
    }

    /**
     * Generate a URL segment based on the title provided.
     *
     * If {@link Extension}s wish to alter URL segment generation, they can do so by defining
     * updateURLSegment(&$url, $title).  $url will be passed by reference and should be modified.
     * $title will contain the title that was originally used as the source of this generated URL.
     * This lets extensions either start from scratch, or incrementally modify the generated URL.
     *
     * @param string $title Page title.
     * @return string Generated url segment
     */
    public function generateURLSegment($title)
    {
        $filter = URLSegmentFilter::create();
        $t = $filter->filter($title);

        // Fallback to generic page name if path is empty (= no valid, convertable characters)
        if(!$t || $t == '-' || $t == '-1') $t = "page-$this->ID";

        // Hook for extensions
        $this->extend('updateURLSegment', $t, $title);

        return $t;
    }

    public function onBeforeWrite() {
        parent::onBeforeWrite();

        // If there is no URLSegment set, generate one from Title
        if(!$this->URLSegment) {
            $this->URLSegment = $this->generateURLSegment($this->ProjectTitle.$this->getProjectYear());
        } else if($this->isChanged('URLSegment')) {
            // Make sure the URLSegment is valid for use in a URL
            $segment = preg_replace('/[^A-Za-z0-9]+/','-',$this->URLSegment);
            $segment = preg_replace('/-+/','-',$segment);
            // If after sanitising there is no URLSegment, give it a reasonable default
            if(!$segment) {
                $segment = "item-$this->ID";
            }
            $this->URLSegment = $segment;
        }
        // Ensure that this object has a non-conflicting URLSegment value.
        $count = 2;
        $URLSegment = $this->URLSegment;
        $ID = $this->ID;
        while($this->LookForExistingURLSegment($URLSegment, $ID))
        {
            $URLSegment = preg_replace('/-[0-9]+$/', null, $URLSegment) . '-' . $count;
            $count++;
        }
        $this->URLSegment = $URLSegment;
    }

    /**
     * @var array
     */
    protected static $_cached_get_by_url = array();
    /**
     * @param $str
     * @return Course|Boolean
     */
    public static function get_by_url_segment($str, $excludeID = null) {
        if (!isset(static::$_cached_get_by_url[$str])) {
            $list = static::get()->filter('URLSegment', $str);
            if ($excludeID) {
                $list = $list->exclude('ID', $excludeID);
            }
            $obj = $list->First();
            static::$_cached_get_by_url[$str] = ($obj && $obj->exists()) ? $obj : false;
        }
        return static::$_cached_get_by_url[$str];
    }

    public function canView($member = null) {
        return Permission::check('CMS_ACCESS_ProjectAdmin', 'any', $member);
    }

    public function canEdit($member = null) {
        return Permission::check('CMS_ACCESS_ProjectAdmin', 'any', $member);
    }

    public function canDelete($member = null) {
        if (Controller::curr() == 'NewsAdmin') return false;
        return Permission::check('CMS_ACCESS_ProjectAdmin', 'any', $member);
    }

    public function canCreate($member = null) {
        return Permission::check('CMS_ACCESS_ProjectAdmin', 'any', $member);
    }
}
