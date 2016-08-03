<?php
class HomepageAlarm extends DataObject
{

    static $singular_name = 'Alarm';
    static $description = 'Alarme für die Startseite';

    private static $db = array(
        'StartDate' => 'SS_Datetime',
        'EndDate' => 'SS_Datetime',
        'Title' => 'Varchar',
        'Meldung' => 'HTMLText()'
    );

    private static $has_one = array(
        'Homepage' => 'HomePage'
    );

    private static $casting = array(
        "StartYear" => 'Int',
        "StartMonth" => 'Int',
        "StartDay" => 'Int',
        "StartHour" => 'Int',
        "StartMinute" => 'Int',
        "EndYear" => 'Int',
        "EndMonth" => 'Int',
        "EndDay" => 'Int',
        "EndHour" => 'Int',
        "EndMinute" => 'Int',
    );

    public function getStartYear() {
        return $this->obj('StartDate')->Year();
    }
    public function getStartMonth() {
        return $this->obj('StartDate')->Format('m');
    }
    public function getStartDay() {
        return $this->obj('StartDate')->Format('d');
    }
    public function getStartHour() {
        return $this->obj('StartDate')->Format('H');
    }
    public function getStartMinute() {
        return $this->obj('StartDate')->Format('i');
    }
    public function getEndYear() {
        return $this->obj('EndDate')->Year();
    }
    public function getEndMonth() {
        return $this->obj('EndDate')->Format('m');
    }
    public function getEndDay() {
        return $this->obj('EndDate')->Format('d');
    }
    public function getEndHour() {
        return $this->obj('EndDate')->Format('H');

    }
    public function getEndMinute() {
        return $this->obj('EndDate')->Format('i');
    }

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
        $fields->removeByName('HomepageID');

        //$fields->dataFieldByName('Meldung')->setTargetLength(300, 50, 300);

        //$fields->dataFieldByName('StartDate')->setConfig('datavalueformat', 'dd.MM.yyyy HH:mm');
        $fields->dataFieldByName('StartDate')->timefield->setConfig('timeformat', 'HH:mm');
        $fields->dataFieldByName('StartDate')->getDateField()->setConfig('showcalendar', true);
        $fields->dataFieldByName('StartDate')->getTimeField()->setConfig('use_strtotime', true);
        $fields->dataFieldByName('StartDate')->getTimeField()->setValue('now');
        $fields->dataFieldByName('StartDate')->getTimeField()->setDescription('z.B. 15:30 (von 00:00 -> 23:59)');

        //$fields->dataFieldByName('EndDate')->setConfig('datavalueformat', 'dd.MM.yyyy HH:mm');
        $fields->dataFieldByName('EndDate')->timefield->setConfig('timeformat', 'HH:mm');
        $fields->dataFieldByName('EndDate')->getDateField()->setConfig('showcalendar', true);
        $fields->dataFieldByName('EndDate')->getTimeField()->setConfig('use_strtotime', true);
        $fields->dataFieldByName('EndDate')->getTimeField()->setValue('now');
        $fields->dataFieldByName('EndDate')->getTimeField()->setDescription('z.B. 15:30 (von 00:00 -> 23:59)');

        return $fields;
    }

}
