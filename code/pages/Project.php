<?php
class ProjectPage extends Page
{
    private static $singular_name = 'Projekt';
    private static $description = 'Seite für Projekte';
    //private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = false;
    //private static $allowed_children = array('GalleryPage');

    private static $db = array(
       'Title' => 'Varchar(255)',
    );

    private static $has_one = array(
        'Gallery' => 'Gallery',
    );

    private static $casting = array(
        'ExistingGoogleMap' => 'HTMLText'
    );

    function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab('Root.Main', new LiteralField('Info','
        <p><span style="color:red;">Achtung: </span>Wenn das gewünschte Album noch nicht existiert, kann es unter <a href="admin/gallerymanager/">Foto-Alben</a> (auf der linken Seite in der Navigation) angelegt werden.</p>
        '),'Content');
        $fields->addFieldToTab('Root.Main',
            DropdownField::create('GalleryID', 'Foto-Album', Gallery::get()->map('ID', 'Title'))
            ->setEmptyString('(Bitte Album auswählen)'),'Content');
        return $fields;
    }

}

class ProjectPage_Controller extends Page_Controller
{
    private static $allowed_actions = array (
    );

    public function init() {
        parent::init();
    }//init()

}
