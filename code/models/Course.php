<?php
/**
 * Tag
 */
class Course extends DataObject
{
    private static $singular_name = 'Kurs';
    private static $plural_name = 'Kurse';

    private static $db = array(
        'Title' => 'Varchar',
        'URLSegment' => 'Varchar(255)',
    );

    private static $belongs_many_many = array(
        'Sections' => 'Section',
    );

    private static $summary_fields = array(
        'Title' => 'Titel'
    );

    public function getCMSFields() {
        return FieldList::create(
            TextField::create('Title','Titel'),
            TextField::create('URLSegment', 'URLSegment')
        );
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

    /*public function Link()
    {
        //$courses = $this->Courses();
        SS_Log::log('Course Link() called',SS_Log::WARN);
        //SS_Log::log('this link='.$this->Courses()->Link('tag/'.$this->ID),SS_Log::WARN);
        //return $this->Courses()->Link('tag/'.$this->ID);
    }*/

    /**
     * @return string
     */
    public function Link() {

        if ($this->isInDB()) {
            $sections = $this->Sections();
            foreach ($sections as $section) {
                SS_Log::log(' Link='.$section->Link,SS_Log::WARN);
            }
            //return $this->Sections()->Link($this->URLSegment);
            //return Controller::join_links($category->Link(), 'product', $this->URLSegment);
            return Controller::join_links($this->URLSegment);
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
