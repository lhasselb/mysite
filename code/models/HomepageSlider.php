<?php

class HomepageSlider extends DataObject
{

    static $singular_name = 'Slider';
    static $plural_name = 'Sliders';
    static $description = 'Slider image(s) for Homepage';


    private static $db = array(
        'Headline' => 'HTMLVarchar',
        'LinkText' => 'Varchar',
        'ExternalURL' => 'Text'
    );

    private static $has_one = array(
        'Parent' => 'DataObject',  // Used as relation for homepage
        'InternalURL' => 'Page',
        'SliderImage' => 'Image',
    );

    private static $summary_fields = array (
        'Headline' => 'Schlagzeile',
        'SliderURL' => 'Link',
        'LinkText' => 'Text für den Link',
        'GridThumbnail' => 'Vorschau',
        //'InternalURL' => 'Externe Verknüpfung',
        //'InternalURL' => 'Interne Verknüpfung',
    );

    // to change the default sorting to the new SortOrder
    //public static $default_sort = 'SortOrder';

    // Set default values
    public static $defaults = array();


    public function getGridThumbnail() {
        if($this->SliderImage()->exists()) {
            return $this->SliderImage()->SetWidth(100);
        }

        return "(kein Bild)";
    }

    public function getSliderURL() {
        if($this->ExternalURL) {
            return $this->ExternalURL;
        } else if($this->InternalURLID) {
            return 'interne Seite: ' .Page::get()->byID($this->InternalURLID)->Title;
            //return $this->InternalURLID;
        }

        return "(kein Link)";
    }

    public function getLink() {
        if($this->ExternalURL) {
            return $this->ExternalURL;
        } else if($this->InternalURLID) {
            return Page::get()->byID($this->InternalURLID)->Link();
            //return $this->InternalURLID;
        }
    }

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
        $headline = HtmlEditorField::create('Headline',_t('Homepage.HEADLINE','Slider Headline'));

        // Settings for UploadField : SliderImage
        $sliderUploadField = new UploadField('SliderImage', _t('Homepage.SLIDERIMAGE','Slider Image'));
        $sliderUploadField->getValidator()->allowedExtensions = array('jpg', 'gif', 'png');
        $sliderUploadField->setFolderName('Uploads/homepage');

        // Link Text
        $linkText = TextField::create('LinkText',_t('Homepage.LINKTEXT','Link Text'));

        // The two options for which type of link to add
        $linkOptions = array('ExternalURL' => 'Link to an external page', 'InternalURLID' => 'Link to an internal page');
        // If we've set an internal link already, then that option should be pre-selected
        $selectedOption = ($this->InternalURLID) ? 'InternalURLID' : 'ExternalURL';
        $linkTypeField = OptionsetField::create('LinkType', '', $linkOptions, $selectedOption);

        $externalURLField = TextField::create('ExternalURL', 'Link to external page')
            ->addExtraClass('switchable');
        $internalURLField = TreeDropdownField::create('InternalURLID', 'Choose a page to link to', 'SiteTree')
            ->setTreeBaseID(0)->addExtraClass('switchable');

        $fields->addFieldsToTab('Root.Main', array($headline,$sliderUploadField,$linkText,$linkTypeField,$externalURLField, $internalURLField));

        return $fields;
    }
}
