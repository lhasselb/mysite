<?php
class CalendarPage extends Page {

    private static $singular_name = 'Termine';
    private static $description = 'Terminkalender mit Daten vom Google Kalender';
    private static $icon = 'mysite/images/calendar.png';

	private static $db = array(
	);

    static $defaults = array();

    function getCMSFields() {
        //HtmlEditorConfig::set_active('calendar');
        $fields = parent::getCMSFields();
        $fields->removeByName('Content');
        $fields->addFieldToTab('Root.Main', new HTMLEditorField('Content','Legende',$this->Content,'calendar'));
        return $fields;
    }

}


class CalendarPage_Controller extends Page_Controller {

	public function init() {
		parent::init();
		$theme = $this->themeDir();
        //Requirements::css($theme.'/javascript/fullcalendar/dist/fullcalendar.print.css');
		Requirements::css($theme.'/javascript/fullcalendar/dist/fullcalendar.min.css');
		//Requirements::javascript($theme.'/javascript/moment/min/moment-with-locales.js');
        Requirements::javascript($theme.'/javascript/moment/min/moment.min.js');
		Requirements::javascript($theme.'/javascript/fullcalendar/dist/fullcalendar.min.js');
		Requirements::javascript($theme.'/javascript/fullcalendar/dist/lang/de.js');
		Requirements::javascript($theme.'/javascript/fullcalendar/dist/gcal.js');

		if(Director::isDev()) {
			if(method_exists(Requirements::backend(), "add_dependency")){
			    Requirements::backend()->add_dependency('mysite/javascript/Termine.js', $theme.'/javascript/jquery/dist/jquery.js');
			} else {
                Requirements::javascript('mysite/javascript/Termine.js');
            }
		} else {
			if(method_exists(Requirements::backend(), "add_dependency")){
			    Requirements::backend()->add_dependency('mysite/javascript/Termine.js', $theme.'/javascript/jquery/dist/jquery.min.js');
			} else {
                Requirements::javascript('mysite/javascript/Termine.js');
            }
		}

	} //init

} //eof
