<?php

class Slider extends DataObject
{

    private static $db = array(
        'LinkText' => 'Varchar',
        'ExternalURL' => 'Text'
    );

    private static $has_one = array(
        'SliderImage' => 'Image',
        'InternalURL' => 'SiteTree'
    );

    private static $summary_fields = array (
        'LinkText' => 'Text für den Link',
        'GridThumbnail' => '',
        //'SliderImage.Filename' => 'Photo file name',
        //'InternalURL' => 'Externe Verknüpfung',
        //'InternalURL' => 'Interne Verknüpfung',
    );

    public function getGridThumbnail() {
        if($this->SliderImage()->exists()) {
            return $this->SliderImage()->SetWidth(100);
        }

        return "(no image)";
    }


    /**
     * @return FieldList
     */
    public function getCMSFields() {
    $fields = parent::getCMSFields();

    // Include link switcher JavaScript
    Requirements::javascript('mysite/javascript/JimEvCms.js');

    // Firstly. remove the old fields
    $fields->removeByName("ExternalURL");
    $fields->removeByName("InternalURLID"); // Note 'ID' on the end of the field name as this is a has_one

    // The two options for which type of link to add
    $linkOptions = array("ExternalURL" => "Link to an external page", "InternalURLID" => "Link to an internal page");
    // If we've set an internal link already, then that option should be pre-selected
    $selectedOption = ($this->InternalURLID) ? "InternalURLID" : "ExternalURL";
    $linkTypeField = OptionsetField::create("LinkType", "", $linkOptions, $selectedOption);

    $externalURLField = TextField::create("ExternalURL", "Link to external page")
        ->addExtraClass('switchable');
    $internalURLField = TreeDropdownField::create("InternalURLID", "Choose a page to link to", "SiteTree")
        ->addExtraClass('switchable');

    $fields->addFieldsToTab('Root.Main', array($linkTypeField, $externalURLField, $internalURLField));

    return $fields;
    }
}
