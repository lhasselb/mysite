<?php
class GoogleCalendarPage extends Page {

    private static $singular_name = 'Calendar Page';
    private static $description = 'Google Kalender';


    private static $icon = 'mysite/images/calendar.png';

	private static $db = array(
	);

	private static $has_one = array(
	);

}
class GoogleCalendarPage_Controller extends Page_Controller {

	/**
	 * An array of actions that can be accessed via a request. Each array element should be an action name, and the
	 * permissions or conditions required to allow the user to access it.
	 *
	 * <code>
	 * array (
	 *     'action', // anyone can access this action
	 *     'action' => true, // same as above
	 *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
	 *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
	 * );
	 * </code>
	 *
	 * @var array
	 */
	private static $allowed_actions = array (
	);

	public function init() {

		parent::init();
		$theme = $this->themeDir();

		Requirements::css($theme.'/bower_components/fullcalendar/dist/fullcalendar.css');

		Requirements::javascript($theme.'/bower_components/moment/min/moment-with-locales.js');
		Requirements::javascript($theme.'/bower_components/fullcalendar/dist/fullcalendar.min.js');
		Requirements::javascript($theme.'/bower_components/fullcalendar/dist/lang/de.js');
		Requirements::javascript($theme.'/bower_components/fullcalendar/dist/gcal.js');

		if(Director::isDev()) {
			if(method_exists(Requirements::backend(), "add_dependency")){
			    Requirements::backend()->add_dependency($theme.'/dist/javascript/termine.js', $theme.'/bower_components/jquery/dist/jquery.js');
			}
		} else {
			if(method_exists(Requirements::backend(), "add_dependency")){
			    Requirements::backend()->add_dependency($theme.'/dist/javascript/termine.js', $theme.'/bower_components/jquery/dist/jquery.min.js');
			}
		}

	} //init

} //eof
