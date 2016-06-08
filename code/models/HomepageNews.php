<?php
class HomepageNews extends DataObject
{
    static $singular_name = 'Homepage-News';
    static $description = 'News für die Startseite';

    private static $db = array(
        'Title' => 'Varchar(255)',
        'Content' => 'Text',
        'PublishDate' => 'SS_Datetime',
        'EventDate' => 'SS_Datetime',
    );

    private static $has_one = array(
        'Parent' => 'Homepage',  // Used as relation for homepage
        'InternalURL' => 'Page',
        'NewsImage' => 'Image',
    );

    private static $summary_fields = array (
        'Title' => 'Title',
        'Link' => 'Link zu Seite',
        'GridThumbnail' => 'Bild'
    );

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Title');
        $fields->removeByName('Content');
        $fields->removeByName('PublishDate');
        $fields->removeByName('EventDate');
        $fields->removeByName('ParentID');
        $fields->removeByName('InternalURLID');
        $fields->removeByName('NewsImage');

        $title = TextField::create('Title','Titel');
        $content = TextareaField::create('Content','Inhalt');
        $publishDate = DateField::create('PublishDate','Publizierungsdatum')
                ->setConfig('showcalendar', true)
                ->setConfig('dateformat', 'dd.MM.yyyy')
                ->setDescription('Wird zum sortieren benutzt.');
        $eventDate = DateField::create('EventDate','Datum des Events')
                ->setConfig('showcalendar', true)
                ->setConfig('dateformat', 'dd.MM.yyyy');

        $internalURLField = TreeDropdownField::create('InternalURLID', 'Link', 'SiteTree');

        $image = new UploadField('NewsImage','Bild');
        $image->getValidator()->allowedExtensions = array('jpg', 'gif', 'png');
        $image->setFolderName('news');

        $fields->addFieldsToTab('Root.Main', array($title,$content,$eventDate,$publishDate,$internalURLField,$image));
        return $fields;
    }

    public function getGridThumbnail()
    {
        if($this->NewsImage()->exists()) {
            return $this->NewsImage()->SetWidth(100);
        }
        return '(kein Bild)';
    }

    /**
     * Fetch the current link, use with $Link in templates
     * @return string|false
     */
    public function getLink()
    {
        if($this->InternalURL() && $this->InternalURL()->exists()) {
            return $this->InternalURL()->Link();
        }
        return false;
    }
}
