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

    function getGalleryImages() {
        $gallery = Gallery::get_by_id('Gallery',$this->GalleryID);
        return $images = $gallery->GetSortedImages();
    }

    function getImagesString() {
        SS_Log::log('ID='.$this->GalleryID,SS_Log::WARN);
        $gallery = Gallery::get_by_id('Gallery',$this->GalleryID);
        SS_Log::log('gallery='.$gallery,SS_Log::WARN);
        $images = $gallery->GetSortedImages();
        SS_Log::log('count'.$images->count(),SS_Log::WARN);
        $images_string = "data = [";

        $num_items = count($images);
        $i = 1;
        foreach ($images as $image) {
            $thumb = Convert::raw2js($image->Image()->CroppedImage(80, 60)->URL);
            $img = Convert::raw2js($image->Image()->CroppedImage(400, 300)->URL);
            $big = Convert::raw2js($image->Image()->URL);
            $title = Convert::raw2js($image->Title);
            $description = Convert::raw2js($image->Description);
            $images_string .= "{thumb: '$thumb', image: '$img', big: '$big', title: '$title', description: '$description' }";
            if ($i ++ < $num_items)
                $images_string .= ",";
        }
        $images_string .= "]";

        return $images_string;
    }
}

class ProjectPage_Controller extends Page_Controller
{
    private static $allowed_actions = array (
    );

    public function init() {
        parent::init();
        $theme = $this->themeDir();
        Requirements::css($theme.'mysite/javascript/galleria/themes/twelve/galleria.twelve.css');
        Requirements::javascript($theme.'/bower_components/galleria/src/galleria.js');
        Requirements::javascript('mysite/javascript/galleria/themes/twelve/galleria.twelve.min.js');
        Requirements::javascriptTemplate('mysite/javascript/Gallery.js', array("imageJson" => $this->getImagesString()));
    }//init()

}
