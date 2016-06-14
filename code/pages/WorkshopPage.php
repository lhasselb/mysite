<?php
class WorkshopPage extends Page {

    private static $singular_name = 'Sammlung für Workshops und Kurse';
    private static $description = 'Seite mit Workshops und Kursen';
    private static $icon = 'mysite/images/workshop.png';

    private static $db = array();
    private static $has_one = array();

    private static $has_many = array(
        'Categories' => 'Category',
        'Courses' => 'Kurs'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
/*
        $kurseConfig = GridFieldConfig_RecordEditor::create();
        $kurseGridField = new GridField('Kurse', 'Workshop oder Kurs', $this->Kurse());
        $kurseGridField->setConfig($kurseConfig);

        $fields->addFieldToTab("Root.WorkshopsundKurse", $kurseGridField);
*/
        $kurseGridField = GridField::create(
            'Courses','Kurse',$this->Courses(),GridFieldConfig_RecordEditor::create())
                ->setDescription('Kurs oder Workshop.');

        $fields->addFieldToTab("Root.Kurse oder Workshop", $kurseGridField);

        $categoriesGridField = GridField::create(
            'Categories','Kategorien',$this->Categories(),GridFieldConfig_RecordEditor::create())
                ->setDescription('Wird als Name zum filtern nach Kategorie verwendet.');

        $fields->addFieldToTab("Root.Kategorien", $categoriesGridField);

        return $fields;
    }

}
class WorkshopPage_Controller extends Page_Controller {

}
