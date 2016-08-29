<?php
class FriendlyLink extends DataObject
{
    static $singular_name = 'Link';
    static $description = 'Ein Link zu einer anderen Seite';

    private static $db = array(
        'Description' => 'Varchar(255)'
    );

    private static $belongs_many_many = array(
        'LinkSet' => 'LinkSet'
    );

    private static $has_one = array(
        'FriendlyLink' => 'Link'
    );

    private static $summary_fields = array(
        'Description'=>'Beschreibung',
        'FriendlyLink'=>'Link',
    );

    public function getTitle() {
        return $this->Description;
    }

    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Description'] = 'Beschreibung';
        return $labels;
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab('Root.Main', LinkField::create('FriendlyLinkID', 'Link'));
        return $fields;
    }

}
