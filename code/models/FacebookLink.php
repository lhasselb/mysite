<?php
class FacebookLink extends DataObject
{
    static $singular_name = 'Facebookgruppen-Link';
    static $description = 'Ein Link zu einer Facebook Seite';

    private static $db = array(
        'Description' => 'Varchar(255)'
    );

    private static $has_one = array(
        'KontaktPage' => 'KontaktPage',
        'FacebookLink' => 'Link'
    );


    private static $summary_fields = array(
        'FacebookLink'=>'Link',
        'Description'=>'Beschreibung'
    );

    public function getTitle() {
        return $this->Description;
    }

    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['FacebookLink'] = 'Link';
        $labels['Description'] = 'Beschreibung';
        return $labels;
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('KontaktPageID');
        $fields->addFieldToTab('Root.Main', LinkField::create('FacebookLinkID', 'Facebook-Gruppe'));
        return $fields;
    }

}
