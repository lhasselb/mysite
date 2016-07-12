<?php
class FAQTag extends DataObject
{
    private static $db = array(
        'Title' => 'Varchar(200)',
    );

    private static $belongs_many_many = array(
        'FAQS' => 'FAQ'
    );

    function getCMSFields(){
        $fields = parent::getCMSFields();
        return $fields;
    }
}
