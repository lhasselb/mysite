<?php
class FotosPage extends Page
{
    private static $singular_name = 'Fotos';
    private static $description = 'Seite für Fotos';
    //private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = true;
    //private static $allowed_children = array('Page');

    private static $db = array(
       'Title' => 'Varchar(255)',
    );

    private static $many_many = array(
        'Galleries' => 'Gallery',
    );

    public function getTags() {
        return GalleryTag::get();
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
    private static $allowed_actions = array ();

    public function init() {
        parent::init();
        $theme = $this->themeDir();
        Requirements::javascript($theme.'/dist/javascript/scripts/pages/index-gallery.js');
    }//init()

}
