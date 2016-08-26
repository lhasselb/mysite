<?php
class FotosPage extends Page
{
    private static $singular_name = 'Fotos';
    private static $description = 'Seite für Fotos';
    private static $can_be_root = true;
    private static $allowed_children = 'none';

    private static $db = array(
       'Title' => 'Varchar(255)',
    );

    private static $many_many = array(
        'Galleries' => 'Gallery',
    );

    public function getFotosPageTags() {

        $usedtags = array();
        foreach ($this->Galleries() as $gallery) {
            $currentTagList = $gallery->GalleryTags();
            foreach ($currentTagList as $tag) {
                array_push($usedtags,$tag);
            }
        }
        // Limit to used ones
        return new ArrayList(array_unique($usedtags));
        // return all even the non used ones
        //return GalleryTag::get();
    }


    function getCMSFields() {
        $fields = parent::getCMSFields();
        $galleries = CheckboxSetField::create('Galleries','Zeige Alben', DataObject::get('Gallery')->map());
        $fields->addFieldToTab('Root.Main', $galleries,'Content');
        return $fields;
    }

}

class FotosPage_Controller extends Page_Controller
{
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

}
