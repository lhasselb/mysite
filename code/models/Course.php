<?php
/**
 * Course
 */
class Course extends News
{
    private static $singular_name = 'Kurs';
    private static $plural_name = 'Kurse';

    private static $db = array(
        'Title' => 'Varchar(255)',
        'MenuTitle' => 'Varchar', // Not used
        'URLSegment' => 'Varchar(255)',
        'CourseDateStart' => 'SS_Datetime',
        'CourseDateEnd' => 'SS_Datetime',
        'Content' => 'HTMLText',
    );

    private static $has_one = array(
        'ContentImage' => 'Image',
        'HomepageSection' => 'Section'
    );

    private static $many_many = array(
        'Sections' => 'Section',
    );


    private static $summary_fields = array(
        'Title' => 'Kursname',
        //'URLSegment' => 'URL-Segment',
        'NewsTitle' => 'News',
        'News' => 'News auf der Startseite',
        'SectionList' => 'Bereiche'
    );

    public function News()
    {
        if(!$this->HomepageSectionID) return 'Nicht auf der Startseite.';
        elseif($this->HomepageSectionID) {
            return DataObject::get_by_id('Section',$this->HomepageSectionID)->Title;
        }
    }

    public function SectionList ()
    {
        //SS_Log::log('getChildList - Children? '.$this->Children()->exists().' for ' .$this->Title,SS_Log::WARN);
        if($this->Sections()->exists()) {
            return implode(', ', $this->Sections()->column('Title'));
        }
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();
        //MAiN TAB
        $fields->addFieldToTab('Root.Main', TextField::create('Title', $this->fieldLabel('Title'))
            ->setDescription('Der Titel des Kurses, Workshops oder der Veranstaltung.'));
        $fields->addFieldToTab('Root.Main', TextField::create('URLSegment', $this->fieldLabel('URLSegment'))
            ->setDescription('Wird beim Speichern generiert, bitte nur in vollem Bewusstsein ändern.'));
        //$fields->addFieldToTab('Root.Main', TextField::create('MenuTitle', $this->fieldLabel('MenuTitle'))
        //    ->setDescription('Wird automatisch vom Seitennamen übernommen, kann geändert werden.'));
        $fields->removeByName('MenuTitle');

        $startDate = DatetimeField::create('CourseDateStart', $this->fieldLabel('CourseDateStart'))
            ->setConfig('datavalueformat', 'dd.MM.yyyy HH:mm');
        $startDate->getDateField()->setConfig('showcalendar', true);
        $startDate->getDateField()->setDescription(sprintf('z.B. %s', Convert::raw2xml(Zend_Date::now()->toString('dd.MM.yyyy'))));
        $startDate->getTimeField()->setDescription(sprintf('z.B. %s', Convert::raw2xml(Zend_Date::now()->toString('HH:mm'))));
        $fields->addFieldToTab('Root.Main', $startDate);
        $endDate = DatetimeField::create('CourseDateEnd', $this->fieldLabel('CourseDateEnd'))
            ->setConfig('datavalueformat', 'dd-MM-yyyy HH:mm');
        $endDate->getDateField()->setConfig('showcalendar', true);
        $endDate->getDateField()->setDescription(sprintf('z.B. %s', Convert::raw2xml(Zend_Date::now()->toString('dd.MM.yyyy'))));
        $endDate->getTimeField()->setDescription(sprintf('z.B. %s', Convert::raw2xml(Zend_Date::now()->toString('HH:mm'))));
        $fields->addFieldToTab('Root.Main', $endDate);
        $contentImage = new UploadField('ContentImage', $this->fieldLabel('ContentImage'));
        $contentImage->setConfig('allowedMaxFileNumber', 1);
        $contentImage->getValidator()->allowedExtensions = array('jpg', 'gif', 'png');
        $fields->addFieldToTab('Root.Main', $contentImage);
        $fields->addFieldToTab('Root.Main', HtmlEditorField::create('Content', $this->fieldLabel('Content')));

        //NEWS TAB
        $fields->insertBefore(new Tab('News', 'News'), 'Sections');
        $title = TextField::create('NewsTitle', $this->fieldLabel('NewsTitle'))->setDescription('Der Titel der News.');
        $fields->addFieldToTab('Root.News', $title);
        //$fields->removeByName('NewsDate');
        $newsDate = DateField::create('NewsDate', $this->fieldLabel('NewsDate'))
            ->setConfig('dataformat', 'dd.MM.yyyy')
            ->setConfig('showcalendar', true);
        $newsDate->setDescription(sprintf('z.B. %s', Convert::raw2xml(Zend_Date::now()->toString('dd.MM.yyyy'))));
        $fields->addFieldToTab('Root.News', $newsDate);
        //$fields->addFieldToTab('Root.News', LinkField::create('NewsLinkID', 'Link'));
        $fields->removeByName('NewsLinkID');
        $fields->addFieldToTab('Root.News', DropdownField::create('HomepageSectionID',
            $this->fieldLabel('HomepageSection'), $this->Sections()->map('ID', 'Title'))
            ->setEmptyString('(Zur Anzeige bitte wählen)')
            ->setDescription('Wenn kein Bereich gewählt ist erscheint die News nicht auf der Startseite!')
         );
        $newsImage = new UploadField('NewsImage', $this->fieldLabel('NewsImage'));
        $newsImage->setConfig('allowedMaxFileNumber', 1);
        $newsImage->getValidator()->allowedExtensions = array('jpg', 'gif', 'png');
        $newsImage->setFolderName('news');
        $fields->addFieldToTab('Root.News', $newsImage);
        $fields->addFieldToTab('Root.News', HtmlEditorField::create('NewsContent', $this->fieldLabel('NewsContent')));

        //SECTIONS TAB
        $fields->removeByName('Sections');
        $config = GridFieldConfig_RelationEditor::create();
        $config->removeComponentsByType($config->getComponentByType('GridFieldAddNewButton'));
        $gridfield = GridField::create("Sections", "Bereich", $this->Sections(), $config);
        $fields->addFieldToTab('Root.Bereiche', $gridfield);

        return $fields;
    }

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);
        $labels['Title'] = 'Kursname';
        $labels['MenuTitle'] = 'Navigationsbezeichnung';
        $labels['URLSegment'] = 'URL-Segment';
        $labels['CourseDateStart'] = 'Kurs-Datum - Anfang';
        $labels['CourseDateEnd'] = 'Kurs-Datum - Ende';
        $labels['Content'] = 'Inhalt';
        $labels['NewsTitle'] = 'Schlagzeile';
        $labels['News'] = 'News';
        $labels['NewsImage'] = 'Bild';
        $labels['ContentImage'] = 'Bild';
        $labels['HomepageSection'] = 'Bereich für Links auf der Startseite';
        $labels['Sections'] = 'Bereiche';
        return $labels;
    }

    /**
     * Get news items
     *
     * @param int $offset
     * @param int $maxitems Max number of items to return
     * @return DataList
     */
    public static function Entries($offset=0, $maxitems=5) {
        $filters = array('HomepageSectionID:GreaterThan'=>'0');
        //->sort('Date DESC')
        return Course::get()->filter($filters)->limit($maxitems, $offset);
    }

    public function NewsSection()
    {
            return $this->HomepageSection()->Title;
    }

    protected function onBeforeWrite() {
        parent::onBeforeWrite();

        $filter = URLSegmentFilter::create();
        if (!$this->URLSegment) {
            $this->URLSegment = $this->Title;
        }
        $this->URLSegment = $filter->filter($this->URLSegment);
        if (!$this->URLSegment) {
            $this->URLSegment = uniqid();
        }
        $count = 2;
        while (static::get_by_url_segment($this->URLSegment, $this->ID)) {
            // add a -n to the URLSegment if it already existed
            $this->URLSegment = preg_replace('/-[0-9]+$/', null, $this->URLSegment) . '-' . $count;
            $count++;
        }
    }

    /**
     * @return string
     */
    public function Link() {
        SS_Log::log('Link() called',SS_Log::WARN);
        SS_Log::log('Link() count='.$this->Sections()->count(),SS_Log::WARN);

        if($this->isInDB()) {
            // Course is just linked once
            if($this->Sections()->count() == 1) {
                return Controller::join_links($this->Sections()->First()->Link(),'kurs',$this->URLSegment);
            }
            // Course is linked several times
            elseif($this->Sections()->count() > 1) {
                foreach ($this->Sections() as $section) {
        //SS_Log::log('Link() homepage section='.$section->HomepageSection(),SS_Log::WARN);
                        //SS_Log::log(' Link() compare ['.$section->Link().'] with ['.Controller::curr()->Link().']',SS_Log::WARN);
                    // Create link for current context
                    if ($section->Link() == Controller::curr()->Link()) {
                        //SS_Log::log(' Link() section->Link()='.$section->Link(),SS_Log::WARN);
                        return Controller::join_links($section->Link(),'kurs',$this->URLSegment);
                    } else { // For Homepage
                        //SS_Log::log('Else '.$this->HomepageSectionID,SS_Log::WARN);
                        if($this->HomepageSectionID) {
                            $section = DataObject::get_by_id('Section',$this->HomepageSectionID);
                            SS_Log::log('Homepage? ='.$section->Link(),SS_Log::WARN);
                            return Controller::join_links($section->Link(),'kurs',$this->URLSegment);
                        }

                        else
                            return Controller::join_links($this->Sections()->First()->Link(),'kurs',$this->URLSegment);
                    }
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

}
