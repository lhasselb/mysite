<?php
class LinkSet extends DataObject
{

    static $singular_name = 'Sammlung';
    static $description = 'Zusammenfassung von Links';

    private static $db = array(
        'Title' => 'Varchar(255)',
    );

    private static $has_one = array(
        'LinkPage' => 'LinkPage'
    );

    private static $many_many = array(
        'Links' => 'FriendlyLink'
    );

    private static $summary_fields = array(
        'Title' => 'Sammlung'
    );

    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Title'] = 'Sammlungs-Titel';
        $labels['Links'] = 'Sammlung';
        return $labels;
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('LinkPageID');
        $fields->removeByName('Links');
        $config = GridFieldConfig_RecordEditor::create();
        $gridfield = GridField::create('Links', 'Links', $this->Links(), $config);
        $fields->addFieldToTab('Root.Main', $gridfield);

        return $fields;
    }

}
