<?php
/**
 * Course object
 */
class Course extends News
{
    private static $singular_name = 'Kurs';
    private static $plural_name = 'Kurse';

    private static $db = array(
        'CourseTitle' => 'Varchar(255)',
        'URLSegment' => 'Varchar(255)',
        'CourseShort' => 'HTMLText',
        'CourseContent' => 'HTMLText',
    );

    private static $has_one = array(
        'CourseImage' => 'Image',
        //'Album' => 'Gallery'
    );

    private static $many_many = array(
        'Sections' => 'SectionPage',
    );

    private static $summary_fields = array(
        'CourseTitle' => 'Kurstitel',
        'URLSegment' => 'URL-Segment',
        'SectionList' => 'Bereiche',
        'Thumbnail' => 'Bild',
    );
    // Used for $summary_fields
    public function getSectionList() {
        if($this->Sections()->exists()) {
            return implode(', ', $this->Sections()->column('Title'));
        }
    }
    // Used for $summary_fields
    public function getThumbnail() {
        return $this->CourseImage()->SetHeight(50);
    }
    // Used for $summary_fields
    public function onHomepage() {
        $today = date("Y-m-d");
        $state = 'Nein';
        if (!$this->ExpireDate) {
            $state .= '-Ablaufdatum fehlt';
        } elseif($this->ExpireDate < $today) {
            $state .= '-Abgelaufen';
        } if ( $this->HomepageSectionID == 0 ) {
            $state .= '-Bereich fehlt';
        } elseif ( $this->HomepageSectionID > 0 && $state == 'Nein') {
            $state = "Ja";
        }
        return $state;
    }
    // Offer a Title
    public function getTitle() {
        return $this->CourseTitle;
    }
    // Offer a MenuTitle
    public function getMenuTitle() {
        return $this->CourseTitle;
    }
    // Get the section - Frontend
    public function getNewsSection() {
            return $this->HomepageSection()->Title;
    }

    public function getCMSFields() {

        $controller = Controller::curr();
        //SS_Log::log('controller='.$controller,SS_Log::WARN);

        $fields = parent::getCMSFields();
        // Remove "autogenerated" (by manymany relation) Sections tab
        $fields->removeByName(array('Sections','AlbumID'));

        if($controller == 'NewsAdmin')
        {
            HtmlEditorConfig::set_active('basic');
            //News Main TAB
            $title = TextField::create('NewsTitle', $this->fieldLabel('NewsTitle'))->setDescription('Der Titel der News.');
            $fields->addFieldToTab('Root.Main', $title);
            $newsDate = DateField::create('NewsDate', $this->fieldLabel('NewsDate'))
                ->setConfig('dataformat', 'dd.MM.yyyy')
                ->setConfig('showcalendar', true);
            $newsDate->setDescription(sprintf('z.B. %s', Convert::raw2xml(Zend_Date::now()->toString('dd.MM.yyyy'))));
            $fields->addFieldToTab('Root.Main', $newsDate);

            $expireDate = DateField::create('ExpireDate', $this->fieldLabel('ExpireDate'))
                ->setConfig('dataformat', 'dd.MM.yyyy')
                ->setConfig('showcalendar', true);
            $expireDate->setDescription(sprintf('z.B. %s', Convert::raw2xml(Zend_Date::now()->toString('dd.MM.yyyy'))).' , Datum ist notwendig um die News auf der Startseite zu zeigen. ' );
            $fields->addFieldToTab('Root.Main', $expireDate);

            //$fields->addFieldToTab('Root.News', LinkField::create('NewsLinkID', 'Link'));
            $fields->removeByName('NewsLinkID');
            $fields->addFieldToTab('Root.Main', DropdownField::create('HomepageSectionID',
                $this->fieldLabel('HomepageSection'), $this->Sections()->map('ID', 'Title'))
                ->setEmptyString('(Zur Anzeige bitte wählen)')
                ->setDescription('Wenn kein Bereich gewählt ist erscheint die News nicht auf der Startseite!')
             );

            $newsImage = new UploadField('NewsImage', $this->fieldLabel('NewsImage'));
            $newsImage->setConfig('allowedMaxFileNumber', 1);
            $newsImage->getValidator()->allowedExtensions = array('jpg', 'gif', 'png');
            $newsImage->setFolderName('news')->setDisplayFolderName('news');
            $fields->addFieldToTab('Root.Main', $newsImage);

            $fields->addFieldToTab('Root.Main',
                HtmlEditorField::create('NewsContent', $this->fieldLabel('NewsContent'))
                ->setRows(12)
                //->setDescription('Bitte die maximale Textlänge begrenzen. Es handelt sich hier um eine News für die Homepage!')
            );
            $fields->addFieldToTab('Root.Main', new LiteralField('Info','
            <p><span style="color:red;">Achtung: </span>Bitte die maximale Textlänge begrenzen. Es handelt sich hier um eine News für die Homepage!</p>'),'NewsContent');

            $fields->removeFieldsFromTab('Root.Main',array('CourseTitle','URLSegment','MenuTitle','Section','CourseContent','CourseShort','CourseImage'));
        }
        if($controller == 'CourseAdmin')
        {
            HtmlEditorConfig::set_active('cms');
            $fields->addFieldToTab('Root.Main', new LiteralField('Info','
            <p><span style="color:red;">Achtung: </span> Beim erstmaligen speichern eines Kurses wird automatisch auch eine News angelegt.
                <br/>
                Diese News übernimmt den Titel, das Anzeige-Datum, das Bild und den ersten Absatz des Inhalts.
                <br/>
                Sie wird jedoch erst dann auf der Startseite angezeigt wenn beim editieren der <a href="http://jimev.internal.epo.org/admin/newsmanager/">News</a> ein Bereich gewählt wird.
            </p>
            '));

            //Course Main TAB
            $fields->removeByName('NewsLinkID');
            $fields->removeByName('ExpireDate');

            $fields->addFieldToTab('Root.Main', TextField::create('CourseTitle', $this->fieldLabel('CourseTitle'))
                ->setDescription('Der Titel des Kurses, Workshops oder der Veranstaltung.'));

            $newsDate = DateField::create('NewsDate', $this->fieldLabel('NewsDate'))
                ->setConfig('dataformat', 'dd.MM.yyyy')
                ->setConfig('showcalendar', true);
            $newsDate->setDescription(sprintf('z.B. %s', Convert::raw2xml(Zend_Date::now()->toString('dd.MM.yyyy'))));
            $fields->addFieldToTab('Root.Main', $newsDate);


            $fields->addFieldToTab('Root.Main', TextField::create('URLSegment', $this->fieldLabel('URLSegment'))
                ->setDescription('Wird beim Speichern generiert, bitte nur in vollem Bewusstsein ändern.'));
            //$fields->addFieldToTab('Root.Main', TextField::create('MenuTitle', $this->fieldLabel('MenuTitle'))
            //    ->setDescription('Wird automatisch vom Seitennamen übernommen, kann geändert werden.'));
            $fields->removeByName('MenuTitle');

            $map = DataObject::get('SectionPage')->map();
            /*foreach ($map as $key => $value) {
                SS_Log::log('key='.$key.' value='.$value,SS_Log::WARN);
            }*/
            $sectionCheck = CheckboxSetField::create('Sections','Bereiche', $map);
            $fields->addFieldToTab('Root.Main', $sectionCheck);

            /*$album = DropdownField::create('AlbumID', 'Album', Gallery::get()->map('ID', 'Title'))
                ->setEmptyString('(Bitte wählen)');
            $fields->addFieldToTab('Root.Main', $album, 'Sections');*/

            $courseImage = new UploadField('CourseImage', $this->fieldLabel('CourseImage'));
            $courseImage->setConfig('allowedMaxFileNumber', 1);
            $courseImage->getValidator()->allowedExtensions = array('jpg', 'gif', 'png');
            $courseImage->setFolderName('kurse')->setDisplayFolderName('kurse');
            $fields->addFieldToTab('Root.Main', $courseImage);
            $fields->addFieldToTab('Root.Main', HtmlEditorField::create('CourseShort', $this->fieldLabel('CourseShort'))->setRows(14));
            $fields->addFieldToTab('Root.Main', HtmlEditorField::create('CourseContent', $this->fieldLabel('CourseContent')));
            $fields->removeFieldsFromTab('Root.Main',array('NewsTitle','NewsContent','Section','NewsImage','NewsLink','HomepageSectionID'));
        }

        return $fields;
    }

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);
        $labels['CourseTitle'] = 'Kursname';
        $labels['MenuTitle'] = 'Navigationsbezeichnung';
        $labels['URLSegment'] = 'URL-Segment';
        $labels['CourseShort'] = 'Kurs-Kurzbeschreibung';
        $labels['CourseContent'] = 'Kurs-Inhalt';
        $labels['CourseImage'] = 'Kurs-Bild';
        $labels['HomepageSection'] = 'Bereich';
        $labels['Sections'] = 'Bereiche';
        return $labels;
    }

    public function onBeforeWrite() {
        parent::onBeforeWrite();

        // If there is no URLSegment set, generate one from Title
        if(!$this->URLSegment) {
            $this->URLSegment = $this->generateURLSegment($this->CourseTitle);
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

        $this->addCourseNewsProperties();
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

    public function addCourseNewsProperties()
    {
        //SS_Log::log('addNewsProperties(), for '.$this->CourseTitle,SS_Log::WARN);
        // NewsTitle
        if(empty($this->NewsTitle)) $this->NewsTitle = $this->CourseTitle;
        // NewsDate
        if(empty($this->NewsDate)) $this->NewsDate = $this->CourseDateStart;
        // NewsContent
        if(empty($this->NewsContent)) $this->NewsContent = $this->dbobject('CourseContent')->FirstParagraph();
        // NewsImage
        if(empty($this->NewsImageID)) $this->NewsImageID = $this->CourseImageID;
    }

    /**
     * @return string
     */
    public function Link() {
        //SS_Log::log('Link() for '.$this->Title .' count='.$this->Sections()->count(),SS_Log::WARN);
        if($this->isInDB()) {
            //SS_Log::log(' Link()  controller = '.Controller::curr(),SS_Log::WARN);
            // For HomePage
            if(Controller::curr() == 'HomePage_Controller') {
                if($this->HomepageSectionID) {
                    $section = DataObject::get_by_id('SectionPage',$this->HomepageSectionID);
                    //SS_Log::log('Homepage? ='.$section->Link(),SS_Log::WARN);
                    return Controller::join_links($section->Link(),'kurs',$this->URLSegment);

                }
            }
            // NewsPage
            if(Controller::curr() == 'NewsPage_Controller') {
                if($this->HomepageSectionID) {
                    $section = DataObject::get_by_id('SectionPage',$this->HomepageSectionID);
                    //SS_Log::log('Homepage? ='.$section->Link(),SS_Log::WARN);
                    return Controller::join_links($section->Link(),'kurs',$this->URLSegment);
                }
            }
            // SectionPage
            else {
                // Course is just linked once
                if($this->Sections()->count() == 1) {
                    return Controller::join_links($this->Sections()->First()->Link(),'kurs',$this->URLSegment);
                }
                // Course is linked several times
                elseif($this->Sections()->count() > 1) {
                    foreach ($this->Sections() as $section) {
                        // Create link for current context
                        if ($section->Link() == Controller::curr()->Link()) {
                            return Controller::join_links($section->Link(),'kurs',$this->URLSegment);
                        }
                    } //foreach
                }
            }
        }
        return '';
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
        return Permission::check('CMS_ACCESS_NewsAdmin', 'any', $member);
    }

    public function canEdit($member = null) {
        return Permission::check('CMS_ACCESS_NewsAdmin', 'any', $member);
    }

    public function canDelete($member = null) {
        if (Controller::curr() == 'NewsAdmin') return false;
        return Permission::check('CMS_ACCESS_NewsAdmin', 'any', $member);
    }

    public function canCreate($member = null) {
        return Permission::check('CMS_ACCESS_NewsAdmin', 'any', $member);
    }

}
