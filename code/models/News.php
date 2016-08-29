<?php
/**
 * News object
 */
class News extends DataObject
{
    private static $singular_name = 'News';
    private static $plural_name = 'News';

    private static $db = array(
        'NewsTitle' => 'Varchar(255)',
        'NewsDate' => 'Date',
        'NewsContent' => 'HTMLText',
        'Section' => 'Varchar(255)',
    );

    private static $has_one = array(
        'NewsImage' => 'Image',
        'HomepageSection' => 'SectionPage'
    );

    private static $default_sort='NewsDate DESC';

    private static $summary_fields = array(
        'NewsTitle' => 'Schlagzeile',
        'NiceNewsDate' => 'Datum',
        'onHomepage' => 'Wird auf der Startseite angezeigt?',
        'Thumbnail' => 'Bild'
    );
    // Used for $summary_fields
    public function getNiceNewsDate() {
        $date = new Date();
        $date->setValue($this->NewsDate);
        return $date->Format('d.m.Y');
    }
    // Used for $summary_fields
    public function getThumbnail() {
        return $this->NewsImage()->SetHeight(50);
    }
    // Used for $summary_fields
    public function onHomepage() {
        return 'Ja';
    }
    // Offer a Title
    public function getTitle() {
        return $this->NewsTitle;
    }
    // Get the section - Frontend
    public function getNewsSection() {
        return empty($this->Section) ? 'News' : $this->Section;
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();
        HtmlEditorConfig::set_active('basic');

        $fields->removeByName('HomepageSectionID');
        $title = TextField::create('NewsTitle', $this->fieldLabel('NewsTitle'))->setDescription('Der Titel der News.');
        $fields->addFieldToTab('Root.Main', $title);

        $newsDate = DateField::create('NewsDate', $this->fieldLabel('NewsDate'))
            ->setConfig('dataformat', 'dd.MM.yyyy')
            ->setConfig('showcalendar', true);
        $newsDate->setDescription(sprintf('z.B. %s', Convert::raw2xml(Zend_Date::now()->toString('dd.MM.yyyy'))));
        $fields->addFieldToTab('Root.Main', $newsDate);

        $fields->addFieldToTab('Root.Main', TextField::create('Section', 'Bereich')
            ->setDescription('Bereich ist optional. Ohne Eingabe wird "News" als Bereich angezeigt.'));

        $newsImage = new UploadField('NewsImage', $this->fieldLabel('NewsImage'));
        $newsImage->setConfig('allowedMaxFileNumber', 1);
        $newsImage->getValidator()->allowedExtensions = array('jpg', 'gif', 'png');
        $newsImage->setFolderName('news')->setDisplayFolderName('news');
        $fields->addFieldToTab('Root.Main', $newsImage);

        $fields->addFieldToTab('Root.Main',
            HtmlEditorField::create('NewsContent', $this->fieldLabel('NewsContent'))
            ->setDescription('Bitte die maximale Textlänge begrenzen. Es handelt sich hier um eine News für die Homepage!')
            ->setRows(12)
        );
        return $fields;
    }

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);
        $labels['NewsTitle'] = 'Schlagzeile';
        $labels['NewsDate'] = 'Anzeige-Datum';
        $labels['NewsContent'] = 'News-Inhalt';
        $labels['NewsImage'] = 'News-Bild';
        return $labels;
    }


    /**
     * @return string
     * NOT used yet, a link to news is not required
     * because they will only be displayed on the Homepage
     */
    public function Link() {
        //SS_Log::log('Link() called',SS_Log::WARN);
    }

    public function canView($member = null) {
        return Permission::check('CMS_ACCESS_CourseAdmin', 'any', $member);
    }

    public function canEdit($member = null) {
        return Permission::check('CMS_ACCESS_CourseAdmin', 'any', $member);
    }

    public function canDelete($member = null) {
        return Permission::check('CMS_ACCESS_CourseAdmin', 'any', $member);
    }

    public function canCreate($member = null) {
        return Permission::check('CMS_ACCESS_CourseAdmin', 'any', $member);
    }

}
