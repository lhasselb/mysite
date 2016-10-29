<?php
class NewsletterAddress extends DataObject
{
    static $singular_name = 'Newsletter-Adresse';
    static $description = 'E-Mail Adressen für den Newsletter';

    private static $db = array(
        'Email' => 'Varchar',
        'AddedDate' => 'SS_Datetime',
    );

    private static $summary_fields = array(
        'Email' => 'Email-Adresse',
        'Date' => 'Datum'
    );

    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['AddedDate'] = 'Eingetragen am';

        return $labels;
    }

    public function getDate()
    {
        $date = new DateTime($this->Created);
        return $date->Format('d.m.Y H:i:s');
    }

    public function getTitle()
    {
        return $this->Email;
    }


    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $email = EmailField::create('Email','E-Mail');
        $fields->addFieldToTab('Root.Main', $email);

        return $fields;
    }

}


