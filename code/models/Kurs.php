<?php
/**
 * Workshop oder Kurs
 */
class Kurs extends DataObject
{
    private static $singular_name = 'Kurs oder Workshop';
    private static $description = 'Kurs oder Workshop';

    private static $db = array(
        'Title' => 'Varchar(255)',
        'Content' => 'Text'
    );

    private static $has_one = array(
        'Kurs' => 'WorkshopPage'
    );

    private static $summary_fields = array(
        'Title' => 'Titel'
    );

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        return $fields;
    }
}
