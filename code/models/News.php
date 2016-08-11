<?php
/**
 * News
 */
class News extends DataObject
{
    private static $singular_name = 'News';
    private static $plural_name = 'News';

    private static $db = array(
        'NewsTitle' => 'Varchar(255)',
        'NewsDate' => 'Date',
        'NewsContent' => 'HTMLVarchar(255)', //HTMLText 2MB
    );

    private static $has_one = array(
        'NewsImage' => 'Image',
        'NewsLink' => 'Link',
    );

    private static $default_sort='NewsDate DESC';

    private static $summary_fields = array(
        'NewsTitle' => 'Schlagzeile',
        'NiceNewsDate' => 'Datum',
        'Thumbnail' => 'Bild'
    );

    public function getNiceNewsDate() {
        $date = new Date();
        $date->setValue($this->NewsDate);
        return $date->Format('d.m.Y');
    }

    public function getTitle() {
        return $this->NewsTitle;
    }

    public function Thumbnail() {
        return $this->NewsImage()->SetHeight(50);
    }


    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $title = TextField::create('NewsTitle', $this->fieldLabel('NewsTitle'))->setDescription('Der Titel der News.');
        $fields->addFieldToTab('Root.Main', $title);

        $newsDate = DateField::create('NewsDate', $this->fieldLabel('NewsDate'))
            ->setConfig('dataformat', 'dd.MM.yyyy')
            ->setConfig('showcalendar', true);
        $newsDate->setDescription(sprintf('z.B. %s', Convert::raw2xml(Zend_Date::now()->toString('dd.MM.yyyy'))));
        $fields->addFieldToTab('Root.Main', $newsDate);

        $fields->addFieldToTab('Root.Main', LinkField::create('NewsLinkID', 'Link'));

        $newsImage = new UploadField('NewsImage', $this->fieldLabel('NewsImage'));
        $newsImage->setConfig('allowedMaxFileNumber', 1);
        $newsImage->getValidator()->allowedExtensions = array('jpg', 'gif', 'png');
        $newsImage->setFolderName('news');
        $fields->addFieldToTab('Root.Main', $newsImage);
        $fields->addFieldToTab('Root.Main',
            HtmlEditorField::create('NewsContent', $this->fieldLabel('NewsContent'))
            ->setRows(3)
            ->setTargetLength(250, 50, 250)
            ->setDescription('Die maximale Textlänge ist begrenzt. Eingaben über 100% werden abgeschnitten.')
        );
        return $fields;
    }

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);
        $labels['NewsTitle'] = 'News-Schlagzeile';
        $labels['NewsDate'] = 'Anzeige-Datum';
        $labels['NewsContent'] = 'News-Inhalt';
        $labels['NewsImage'] = 'News-Bild';
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
        $newsList = News::get();
        foreach ($newsList as $item) {
            //SS_Log::log('Classname='.$item->ClassName,SS_Log::WARN);
            //SS_Log::log('HomepageSectionID='.$item->HomepageSectionID,SS_Log::WARN);
            if($item->ClassName == 'Course' && $item->HomepageSectionID == 0) $newsList->remove($item);
        }
        $filters = array();
        //->sort('Date DESC')
        //$news = News::get()->filter($filters)->limit($maxitems, $offset);
        return $newsList;
    }


    /**
     * @return string
     */
    public function Link() {
        //SS_Log::log('Link() called',SS_Log::WARN);
    }

}
