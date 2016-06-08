<?php
/**
 * Category
 */
class Category extends DataObject
{
    private static $singular_name = 'Kategorie';
    private static $plural_name = 'Kategorien';

    private static $db = array(
        'Title' => 'Varchar'
    );

    private static $has_one = array(
        'Parent' => 'WorkshopPage'
    );

    private static $belongs_many_many = array(
        'Kurs' => 'WorkshopOderKurs',
    );

    private static $summary_fields = array(
        'Title'
    );

    function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Title'] = 'Title';
        return $labels;
    }

    public function getCMSFields() {
        return FieldList::create(
            TextField::create('Title')
        );
    }

    public function Link () {
        return $this->Parent()->Link(
            'category/'.$this->ID
        );
    }

}
