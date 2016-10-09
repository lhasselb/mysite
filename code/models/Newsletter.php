<?php
class Newsletter extends DataObject
{

    static $singular_name = 'Newsletter';
    static $description = 'E-Mail Adresen für den Newsletter';

    private static $db = array(
        'Email' => 'Varchar'
    );

    private static $summary_fields = array(
        'Email' => 'Email',
    );

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
