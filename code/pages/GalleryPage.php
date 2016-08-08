<?php

class GalleryPage extends Page {

    // Add CMS description
    static $description = "Fotos zu einem Album hinzufügen";
    static $singular_name = 'Foto-Album';
    //private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = false;
    private static $allowed_children = array();

    // Used to automatically include photos in a specific folder
    public static $db = array(
        //'GalFolder' => 'Varchar(100)'
    );

    static $has_one = array(
        'AlbumDescription' => 'Varchar(100)'
    );

    // One gallery page has many gallery images
    public static $has_many = array(
        'GalleryImages' => 'GalleryImage'
    );

    // Set Permissions
    function canEdit($Member = null){if(permission::check('EDIT_GALLERY')){return true;}else{return false;}}
    function canCreate($Member = null){if(permission::check('EDIT_GALLERY')){return true;}else{return false;}}


    public function getCMSFields() {

        $fields = parent::getCMSFields();

        $gridFieldConfig = GridFieldConfig_RecordEditor::create();
        $gridFieldConfig->addComponent(new GridFieldBulkUpload());
        $gridFieldConfig->addComponent(new GridFieldBulkManager());

        // Used to determine upload folder
        $uploadfoldername = substr($this->Parent()->Link(), 1, -1);
        SS_Log::log('if uploadfoldername=' . $uploadfoldername,SS_Log::WARN);
        $gridFieldConfig->getComponentByType('GridFieldBulkUpload')->setUfSetup('setFolderName', $uploadfoldername);
        //$gridFieldConfig->getComponentByType('GridFieldBulkUpload')->setUfSetup('setCanUpload', false);
        $gridFieldConfig->getComponentByType('GridFieldBulkUpload')->setUfSetup('setDisplayFolderName', $uploadfoldername);

        // Customise gridfield
        $gridFieldConfig->removeComponentsByType('GridFieldPaginator'); // Remove default paginator
        $gridFieldConfig->addComponent(new GridFieldPaginator(20)); // Add custom paginator
        $gridFieldConfig->addComponent(new GridFieldSortableRows('SortOrder'));
        $gridFieldConfig->removeComponentsByType('GridFieldAddNewButton'); // We only use bulk upload button

        // Creates sortable grid field
        $gridfield = new GridField("GalleryImages", "Fotos", $this->GalleryImages()->sort("SortOrder"), $gridFieldConfig);
        $fields->addFieldToTab('Root.Fotos', $gridfield);

        return $fields;

    }
        // Check that folder name conforms to assets class standards. remove spaces and special charachters if used
        function onBeforeWrite() {
            $this->GalFolder = str_replace(array(' ','-'),'-', preg_replace('/\.[^.]+$/', '-', $this->GalFolder));

        parent::onBeforeWrite();
        }


}


class GalleryPage_Controller extends Page_Controller implements PermissionProvider  {

    public function init() {
        parent::init();
        $theme = $this->themeDir();
        Requirements::css($theme.'mysite/javascript/galleria/themes/twelve/galleria.twelve.css');
        Requirements::javascript($theme.'/bower_components/galleria/src/galleria.js');
        Requirements::javascript('mysite/javascript/galleria/themes/twelve/galleria.twelve.min.js');
        $json = $this->ImagesJson();
        Requirements::javascriptTemplate('mysite/javascript/Data.js', array("ImageDataJson" => $json));
        Requirements::javascript('mysite/javascript/Fotos.js');
    }

    public function ImagesJson() {
        $json = '[';
        $imageList = $this->GalleryImages();
        $number = count($imageList);
        $i = 1;
        foreach ($imageList as $image)
        {
            $imageURL = $image->Image()->URL;
            $thumbURL = $image->Image()->CroppedImage(80, 80)->URL;
            $title = $image->Title;
            $description = $image->Description;
            $json .= "{thumb:"."'".$thumbURL."', image:'".$imageURL."', big:'".$imageURL."', title:'".$title."', description:'".$description."'}";
            if ($i ++ < $number)
                $json .= ',';
        }
        $json .= ']';
        return $json;
    }

    //Add permission check boxes to CMS
    public function providePermissions() {
        return array(
          "VIEW_GALLERY" => "View Gallery Pages",
          "EDIT_GALLERY" => "Edit Gallery Pages",
        );
    }

    // Set sort order for images
    public function GetGalleryImages() {
        return $this->GalleryImages()->sort("SortOrder");
    }
}
