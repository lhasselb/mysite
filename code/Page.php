<?php
/**
 * Default Page object
 *
 * @package mysite
 * @subpackage pages
 *
 */
class Page extends SiteTree
{
    private static $singular_name = 'Seite';
    private static $description = 'Standard-Seite';
    private static $db = array();
    private static $has_one = array();

    /**
     * Make Homepage Alerts accessible from all pages
     */
    public function getAlert() {
        return $alerts = HomePage::get()->First()->Alarm();
    }

    /**
     * Make Facebook Links accessible from all pages
     */
    public function getFacebookLinks() {
        if(KontaktPage::get()->First()) {
            return KontaktPage::get()->First()->FacebookLinks();
        }
    }

}

class Page_Controller extends ContentController
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
    private static $allowed_actions = array ('NewsletterForm');

    public function init() {
        // You can include any CSS or JS required by your project here.
        // See: http://doc.silverstripe.org/framework/en/reference/requirements
        $theme = $this->themeDir();

        if(Director::isDev()) {
        	Requirements::javascript($theme.'/javascript/jquery/dist/jquery.js');
            Requirements::javascript($theme.'/javascript/velocity/velocity.js');
            Requirements::javascript($theme.'/javascript/velocity/velocity.ui.js');
        } else {
            Requirements::javascript($theme.'/javascript/jquery/dist/jquery.min.js');
            Requirements::javascript($theme.'/javascript/velocity/velocity.min.js');
            Requirements::javascript($theme.'/javascript/velocity/velocity.ui.js');
        }

        Requirements::javascript($theme.'/javascript/js-cookie/src/js.cookie.js');
        Requirements::javascript($theme.'/javascript/bootstrap/dist/js/bootstrap.min.js');
        Requirements::javascript($theme.'/javascript/jquery_lazyload/jquery.lazyload.js');
		Requirements::javascript($theme.'/dist/javascript/plugins/jquery-migrate.min.js');
        Requirements::javascript($theme.'/dist/javascript/plugins/jquery.easing.min.js');
        Requirements::javascript($theme.'/dist/javascript/plugins/reveal-animate/wow.js');
        Requirements::javascript($theme.'/dist/javascript/scripts/reveal-animate/reveal-animate.js');
        Requirements::javascript($theme.'/dist/javascript/plugins/revo-slider/js/jquery.themepunch.tools.min.js');
        Requirements::javascript($theme.'/dist/javascript/plugins/revo-slider/js/jquery.themepunch.revolution.min.js');
        Requirements::javascript($theme.'/dist/javascript/plugins/revo-slider/js/extensions/revolution.extension.slideanims.min.js');
        Requirements::javascript($theme.'/dist/javascript/plugins/revo-slider/js/extensions/revolution.extension.layeranimation.min.js');
        Requirements::javascript($theme.'/dist/javascript/plugins/revo-slider/js/extensions/revolution.extension.navigation.min.js');
        Requirements::javascript($theme.'/dist/javascript/plugins/revo-slider/js/extensions/revolution.extension.video.min.js');
        Requirements::javascript($theme.'/dist/javascript/plugins/cubeportfolio/js/jquery.cubeportfolio.min.js');
        Requirements::javascript($theme.'/dist/javascript/plugins/owl-carousel/owl.carousel.min.js');
        Requirements::javascript($theme.'/dist/javascript/plugins/counterup/jquery.waypoints.min.js');
        Requirements::javascript($theme.'/dist/javascript/plugins/counterup/jquery.counterup.min.js');
        Requirements::javascript($theme.'/dist/javascript/plugins/fancybox/jquery.fancybox.pack.js');
        Requirements::javascript($theme.'/dist/javascript/plugins/slider-for-bootstrap/js/bootstrap-slider.js');

        if(Director::isDev()) {
            Requirements::javascript($theme.'/dist/javascript/components.js');
            Requirements::javascript($theme.'/dist/javascript/app.js');
            Requirements::javascript($theme.'/dist/javascript/main.js');
        } else {
            Requirements::javascript($theme.'/dist/javascript/script.min.js');
        }

        // Used for handling AJAX to Newsletter
        Requirements::javascript('mysite/javascript/Newsletter.js');
        parent::init();
    } //init

    /**
     * Information about dev environment type
     * @return boolean true if environment type equals dev
     */
    public function isDev() {
        return Director::isDev();
    }

    /*public function index(SS_HTTPRequest $request) {
        if($request->isAjax()) {
            SS_Log::log('-- Homepage AJAX index --',SS_Log::WARN);
        } else {
            SS_Log::log('-- Homeage index --',SS_Log::WARN);
            return $this;
        }
    }*/

    public function NewsletterForm() {
        return new NewsletterForm($this, 'NewsletterForm');
    }

    public function Copyright($startYear = "2007", $separator = "-") {
        $currentYear = date('Y');
        if(!empty($startYear)) {
            $output = ($startYear>=$currentYear ? $currentYear : $startYear.$separator.$currentYear);
        } else {
            $output = $currentYear;
        }
        return $output;
    }
} //eof
