<?php
/**
 * Extends SilverStripe page object to provide additional images.
 */
class GalleryExtension extends DataExtension
{
    private static $db = array();
    private static $has_one = array(
        'AlbumDescription' => 'Varchar(100)'
    );

    // Create a relation table [OWNER]_GalleryImages
    private static $has_many = array(
        'GalleryImages' => 'GalleryImage'
    );

    public function updateCMSFields(FieldList $fields) {
        $gridFieldConfig = GridFieldConfig_RecordEditor::create();
        $gridFieldConfig->addComponent(new GridFieldBulkUpload());
        $gridFieldConfig->addComponent(new GridFieldBulkManager());
        // Used to determine upload folder
        $uploadfoldername = substr($this->owner->Link(), 1, -1);
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
        $gridfield = new GridField("GalleryImages", "Fotos", $this->owner->GalleryImages()->sort("SortOrder"), $gridFieldConfig);
        $fields->addFieldToTab('Root.Fotos', $gridfield);
    }

    public function GetFirstImage() {
        return $this->owner->GalleryImages()->Sort($this->owner->Sorter)->limit(1)->First();
    }

    public function GetGalleryImages() {
        return $this->owner->GalleryImages()->sort("SortOrder");
    }

    public function ImagesJson() {
        $images = $this->owner->GalleryImages();
        //$focuspoint = (class_exists('FocusPointImage')) ? true : false;
        $data = array();
        foreach ($images as $image) {
            $data[] = array(
                "thumb" => $image->Image()->CroppedImage(80, 80)->URL,
                "image" => $image->Image()->CroppedImage(800, 600)->URL,
                "big" => $image->Image()->URL,
                "title" => $image->Title,
                "description" => $image->Description
            );
        }
        return Convert::array2json($data);
    }

    public function contentcontrollerInit() {

        $json = $this->ImagesJson();
        Requirements::customScript(
<<<JS
        var data = $json;
JS
        ,'data');

        $theme = $this->owner->themeDir();
        //Requirements::javascriptTemplate('mysite/javascript/Data.js', array("ImageDataJson" => $json));
        Requirements::css($theme.'mysite/javascript/galleria/themes/twelve/galleria.twelve.css');
        Requirements::javascript($theme.'/bower_components/galleria/src/galleria.js');
        Requirements::javascript('mysite/javascript/galleria/themes/twelve/galleria.twelve.min.js');
        Requirements::javascript('mysite/javascript/Fotos.js');

if(method_exists(Requirements::backend(), "add_dependency")){
    Requirements::backend()->add_dependency("framework/thirdparty/jquery-ui/jquery-ui.js", "framework/thirdparty/jquery/jquery.js");
}

    } //init

}
