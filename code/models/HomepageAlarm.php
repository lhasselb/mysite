<?php
class HomepageAlarm extends DataObject
{

    static $singular_name = 'Alarm';
    static $plural_name = 'Alarme';
    static $description = 'Alarme für die Startseite';


    private static $db = array(
        'Titel' => 'Varchar',
        'Meldung' => 'HTMLVarchar(255)',
        'StartDate' => 'SS_Datetime',
        'EndDate' => 'SS_Datetime',
    );

    private static $has_one = array(
        'Homepage' => 'Homepage'
    );

    private static $summary_fields = array(
        'Titel' => 'Titel'
    );

    // to change the default sorting to the new SortOrder
    //public static $default_sort = 'SortOrder';

    // Set default values
    public static $defaults = array();

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('Homepage');
        $fields->addFieldToTab('Root.Main', TextField::create('Titel','Schlagzeile'));
        $fields->addFieldToTab('Root.Main', HtmlEditorField::create('Meldung','Meldung'));
        $fields->addFieldToTab('Root.Main', DatetimeField::create('StartDate','Wird angezeigt ab')
                ->setConfig('showcalendar', true)
                ->setConfig('dateformat', 'd MMMM yyyy HH:MM')
                ->setDescription('Text'));
        $fields->addFieldToTab('Root.Main', DatetimeField::create('EndDate','Wird angezeigt bis')
                ->setConfig('showcalendar', true)
                ->setConfig('dateformat', 'd MMMM yyyy HH:MM')
                ->setDescription('Text'));
        return $fields;
    }

}
