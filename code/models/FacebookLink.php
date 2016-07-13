<?php
class FacebookLink extends DataObject
{
    static $singular_name = 'FacebookLink';
    static $description = 'Ein Link zu einer Facebook Seite';

    private static $has_one = array(
        'KontaktPage' => 'KontaktPage',
        'Link' => 'Link'
    );


    private static $summary_fields = array(
        'Link'=>'FacebookLink'
    );

    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        //$labels['Description'] = 'Beschreibung';
        return $labels;
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab('Root.Main', LinkField::create('Link', 'Facebook-Gruppe'));
        return $fields;
    }

}
