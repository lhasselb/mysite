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
        'News' => 'HTMLVarchar', //255 characters
        'CourseDateStart' => 'SS_Datetime',
        'CourseDateEnd' => 'SS_Datetime',
        'Location' => 'Varchar',
        'Content' => 'HTMLText'
    );

    private static $has_one = array(
        'NewsImage' => 'Image',
        'ContentImage' => 'Image',
        'HomepageSection' => 'Section'
    );

    private static $belongs_many_many = array(
        'Sections' => 'Section',
    );

    private static $summary_fields = array(
        'Title' => 'Name',
        'URLSegment' => 'URL-Segment',
        'News' => 'News(Startseite) zeigt auf Bereich'
    );

    public function fieldLabels($includerelations = true) {
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
        $labels['Sections'] = 'Bereiche';
        return $labels;
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main',
            array(
                TextField::create('Title',$this->fieldLabel('Title')),
                TextField::create('URLSegment','URL-Segment'),
                TextField::create('MenuTitle','Navigationsbezeichnung'),
                DropdownField::create('HomepageSectionID', 'Bereich auf der Startseite', $this->Sections()->map('ID', 'Title'))
                    ->setEmptyString('(Zur Anzeige bitte wählen)')
            )
        );
        //return FieldList::create();
        return $fields;
    }

    public function News() {
        if(!$this->HomepageSectionID) return 'Kein Bereich - Nicht auf der Startseite.';
        elseif($this->HomepageSectionID) {
            return DataObject::get_by_id('Section',$this->HomepageSectionID)->Title;
        }
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
                        SS_Log::log('Else '.$this->HomepageSectionID,SS_Log::WARN);
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
