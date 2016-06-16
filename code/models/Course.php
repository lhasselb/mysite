<?php
/**
 * Course
 * @property Varchar Title
 * @property Varchar MenuTitle
 * @property Varchar URLSegment
 * @property Section HomepageSection
 *
 * @property HTMLText Content
 *
 * @method ManyManyList Sections
 */
class Course extends DataObject
{
    private static $singular_name = 'Kurs';
    private static $plural_name = 'Kurse';

    private static $db = array(
        'Title' => 'Varchar(255)',
        'MenuTitle' => 'Varchar',
        'URLSegment' => 'Varchar(255)',
        'CourseDateStart' => 'SS_Datetime',
        'CourseDateEnd' => 'SS_Datetime',
        'Content' => 'HTMLText',
        'NewsTitle' => 'Varchar(255)',
        'News' => 'HTMLVarchar', //255 characters
    );

    private static $has_one = array(
        'NewsImage' => 'Image',
        'ContentImage' => 'Image',
        'HomepageSection' => 'Section'
    );

    private static $many_many = array(
        'Sections' => 'Section',
    );

    static $many_many_extraFields = array(
        'Sections' => array(
            'SortOrder' => 'Int'
        )
    );

    private static $summary_fields = array(
        'Title' => 'Kursname',
        'URLSegment' => 'URL-Segment',
        'News' => 'Bereich für Links auf der Startseite'
    );

    public function News() {
        if(!$this->HomepageSectionID) return 'Kein Bereich - Nicht auf der Startseite.';
        elseif($this->HomepageSectionID) {
            return DataObject::get_by_id('Section',$this->HomepageSectionID)->Title;
        }
    }

    public function Sections() {
        //SS_Log::log(' getSections() called',SS_Log::WARN);
        return $this->getManyManyComponents('Sections')->sort('SortOrder');
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();
        //MAiN TAB
        $fields->addFieldToTab('Root.Main', TextField::create('Title', $this->fieldLabel('Title'))
            ->setDescription('Der Titel des Kurses, Workshops oder der Veranstaltung.'));
        $fields->addFieldToTab('Root.Main', TextField::create('URLSegment', $this->fieldLabel('URLSegment'))
            ->setDescription('Wird automatisch generiert, bitte nur in vollem Bewusstsein ändern.'));
        $fields->addFieldToTab('Root.Main', TextField::create('MenuTitle', $this->fieldLabel('MenuTitle'))
            ->setDescription('Wird automatisch vom Seitennamen übernommen, kann geändert werden.'));
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
        $fields->addFieldToTab('Root.News', TextField::create('NewsTitle', $this->fieldLabel('NewsTitle')));
        $fields->addFieldToTab('Root.News', DropdownField::create('HomepageSectionID',
            $this->fieldLabel('HomepageSection'), $this->Sections()->map('ID', 'Title'))
            ->setEmptyString('(Zur Anzeige bitte wählen)')
            ->setDescription('Wenn kein Bereich gewählt ist erscheint die News nicht auf der Startseite.')
         );
        $newsImage = new UploadField('NewsImage', $this->fieldLabel('NewsImage'));
        $newsImage->setConfig('allowedMaxFileNumber', 1);
        $newsImage->getValidator()->allowedExtensions = array('jpg', 'gif', 'png');
        $fields->addFieldToTab('Root.News', $newsImage);
        $fields->addFieldToTab('Root.News', HtmlEditorField::create('News', $this->fieldLabel('News')));

        $fields->removeByName('Sections');
        /*SS_Log::log('Bereiche '. DataObject::get('Section')->count(),SS_Log::WARN);
        if($sections = DataObject::get('Section')) $map = $sections->map();
        $select = CheckboxSetField::create('Sections','Bereiche',$sections->map());
        $fields->addFieldToTab('Root.Main', $select);*/

        $config = GridFieldConfig_RelationEditor::create();
        $config->addComponents(new GridFieldSortableRows('SortOrder'));
        $gridfield = GridField::create("Sections", "Bereich", $this->Sections(), $config);
        $fields->addFieldToTab('Root.TEST', $gridfield);

        return $fields;
    }

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);
        $labels['Title'] = 'Kursname';
        $labels['MenuTitle'] = 'Navigationsbezeichnung';
        $labels['URLSegment'] = 'URL-Segment';
        $labels['CourseDateStart'] = 'Start-Datum';
        $labels['CourseDateEnd'] = 'End-Datum';
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
