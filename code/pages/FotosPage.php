<?php
/**
 * FotosPage object
 *
 * @package mysite
 * @subpackage pages
 *
 */
class FotosPage extends Page
{
    private static $singular_name = 'Fotos';
    private static $description = 'Seite für Fotos';
    private static $icon = 'mysite/images/fotos.png';
    private static $can_be_root = true;
    private static $allowed_children = 'none';

    private static $db = array(
       'Title' => 'Varchar(255)',
    );

    private static $many_many = array(
        'Galleries' => 'Gallery',
    );

    /*public function Galleries() {
        return Gallery::get();
    }*/

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $galleries = CheckboxSetField::create('Galleries','Zeige Alben', DataObject::get('Gallery')->map());
        $fields->addFieldToTab('Root.Main', $galleries,'Content');
        return $fields;
    }

    public function getFotosPageTags() {
        $usedtags = array();
        foreach ($this->Galleries() as $gallery) {
            $currentTagList = $gallery->GalleryTags();
            foreach ($currentTagList as $tag) {
                // Add GalleryTag object to array
                array_push($usedtags,$tag);
            }
        }
        // Limit to used ones
        // this requires a __toString() method for the object compared
        // see GalleryTag __toString()
        return new ArrayList(array_unique($usedtags));
        // return all even the non used ones
        //return GalleryTag::get();
    }

    public function getFotosPageYears() {

        $usedYears = array();
        foreach ($this->Galleries() as $gallery) {
            array_push($usedYears,$gallery);
        }

        // Limit to used ones
        // this requires a __toString() method for the object compared
        // see Gallery __toString()
        return new ArrayList(array_unique($usedYears));
    }

}

class FotosPage_Controller extends Page_Controller
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
    private static $allowed_actions = array ('album');

    public function init() {
        parent::init();
        $theme = $this->themeDir();
        Requirements::javascript('mysite/javascript/FotoPageAlbum.js');
        Requirements::css('mysite/javascript/galleria/themes/twelve/galleria.twelve.css');
        Requirements::javascript($theme.'/javascript/galleria/src/galleria.js');
        Requirements::javascript('mysite/javascript/galleria/themes/twelve/galleria.twelve.min.js');
    }//init()

    public function album(SS_HTTPRequest $request) {

        $gallery = Gallery::get_by_id('Gallery',$request->param('ID'));
        $imageJson = $gallery->getFotosJson();

        $data = array(
            'Gallery' => $gallery,
            'ImageJson' => $imageJson
        );

        if(!$gallery) {
            return $this->httpError(404,'Das gewünschte Album existiert nicht.');
        }

        if($request->isAjax()) {
            return $this->customise($data)->renderWith('FotosPageGallery');
        }

        return array ();
    }

    //public function index(SS_HTTPRequest $request) {}

} //eof
