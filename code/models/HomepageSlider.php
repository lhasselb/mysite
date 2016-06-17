<?php
class HomepageSlider extends DataObject
{

    static $singular_name = 'Slider-Bild';
    static $description = 'Slider Bild(er) für die Startseite';


    private static $db = array(
        'Headline' => 'Varchar(255)',
        'LinkText' => 'Varchar(50)',
        'ExternalURL' => 'Text'
    );

    private static $has_one = array(
        'Parent' => 'DataObject',  // Used as relation for homepage
        'InternalURL' => 'Page',
        'SliderImage' => 'Image',
    );

    private static $summary_fields = array (
        'Headline' => 'Schlagzeile',
        'Link' => 'Link zu Seite',
        'LinkText' => 'Text für den Link',
        'GridThumbnail' => 'Vorschau'
    );

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Include link switcher JavaScript
        Requirements::javascript('mysite/javascript/JimEvCms.js');

        // Firstly. remove the old fields
        $fields->removeByName('ExternalURL');
        $fields->removeByName('InternalURLID'); // Note 'ID' on the end of the field name as this is a has_one
        $fields->removeByName('Headline');
        $fields->removeByName('LinkText');
        $fields->removeByName('SliderImage');

        // Slider Headline
        //$headline = HtmlEditorField::create('Headline',_t('Homepage.HEADLINE','Slider Headline'));
        $headline = TextareaField::create('Headline','Schlagzeile');

        // Settings for UploadField : SliderImage
        $sliderUploadField = new UploadField('SliderImage', 'Slider Bild');
        $sliderUploadField->getValidator()->allowedExtensions = array('jpg', 'gif', 'png');
        $sliderUploadField->setFolderName('Uploads/homepage');

        // Link Text
        $linkText = TextField::create('LinkText', 'Link Text');

        // The two options for which type of link to add
        $linkOptions = array('ExternalURL' => 'Link zu einer externer Seite', 'InternalURLID' => 'Link zu einer internen Seite');
        // If we've set an internal link already, then that option should be pre-selected
        $selectedOption = ($this->InternalURLID) ? 'InternalURLID' : 'ExternalURL';
        $linkTypeField = OptionsetField::create('LinkType', '', $linkOptions, $selectedOption);

        $externalURLField = TextField::create('ExternalURL', 'Link to external page')
            ->addExtraClass('switchable');
        $internalURLField = TreeDropdownField::create('InternalURLID', 'Wählen Sie eine interne Seite', 'SiteTree')
            ->setTreeBaseID(0)->addExtraClass('switchable');

        $fields->addFieldsToTab('Root.Main', array($headline,$sliderUploadField,$linkText,$linkTypeField,$externalURLField, $internalURLField));

        return $fields;
    }

    public function getGridThumbnail()
    {
        if($this->SliderImage()->exists()) {
            return $this->SliderImage()->SetWidth(100);
        }

        return '(kein Bild)';
    }

    public function getTitle()
    {
        if($this->SliderImage()->exists()) {
            return 'Bild: '.$this->SliderImage()->Title;
        }

        return '(kein Bild)';
    }

    /**
     * @return void
     */
    public function onBeforeWrite()
    {
        // If we've set an external link unset any existing internal link
        if($this->ExternalURL && $this->isChanged('ExternalURL')) {
            $this->InternalURLID = false;
        // Otherwise, if we've set an internal link unset any existing external link
        } elseif($this->InternalURLID && $this->isChanged('InternalURLID')) {
            $this->ExternalURL = false;
        }
        parent::onBeforeWrite();
    }
    /**
     * Fetch the current link, use with $Link in templates
     * @return string|false
     */
    public function getLink()
    {
        if($this->ExternalURL) {
            return $this->ExternalURL;
        } elseif($this->InternalURL() && $this->InternalURL()->exists()) {
            return $this->InternalURL()->Link();
        }
        return false;
    }
}
