<?php
/**
 * ClubHolder Page object
 *
 * @package mysite
 * @subpackage pages
 *
 */
class ClubHolder extends Page
{
    private static $singular_name = 'Verein';
    private static $description = 'Seite zum Gruppieren von Vereinsseiten.';
    private static $icon = 'mysite/images/club.png';
    private static $can_be_root = true;
    private static $allowed_children = array(
        '*Page',
        'EnrollPage',
    );

    function getCMSFields(){
        $fields = parent::getCMSFields();
        return $fields;
    }
}

class ClubHolder_Controller extends Page_Controller
{
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
    private static $allowed_actions = array ();

    public function init()
    {
        parent::init();
        $theme = $this->themeDir();
    } //init()

} //eof
