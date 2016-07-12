<?php
class HomepageAlarm extends DataObject
{

    static $singular_name = 'Alarm';
    static $description = 'Alarme für die Startseite';

    private static $db = array(
        'StartDate' => 'SS_Datetime',
        'EndDate' => 'SS_Datetime',
        'Title' => 'Varchar',
        'Meldung' => 'HTMLVarchar(255)'
    );

    private static $has_one = array(
        'Homepage' => 'HomePage'
    );

    private static $summary_fields = array(
        'Title' => 'Titel',
        'StartDate.FormatFromSettings' => 'Anzeigen ab',
        'EndDate.FormatFromSettings' => 'Anzeigen bis'
    );

    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Title'] = 'Titel';
        $labels['StartDate'] = 'Anzeigen ab';
        $labels['EndDate'] = 'Anzeigen bis';
        return $labels;
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('HomeageID');

        $fields->dataFieldByName('StartDate')->getDateField()->setConfig('showcalendar', true);
        $fields->dataFieldByName('StartDate')->getTimeField()->setConfig('use_strtotime', true);
        $fields->dataFieldByName('StartDate')->getTimeField()->setValue('now');

        $fields->dataFieldByName('EndDate')->getDateField()->setConfig('showcalendar', true);
        $fields->dataFieldByName('EndDate')->getTimeField()->setConfig('use_strtotime', true);
        $fields->dataFieldByName('EndDate')->getTimeField()->setValue('now');

        return $fields;
    }

}
