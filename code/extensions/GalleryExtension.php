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
        foreach ($images as $image) {
            $data[] = array(
                'thumb' => $image->Image()->CroppedImage(40, 30)->URL,
                'image' => $image->Image()->CroppedImage(800, 600)->URL,
                'big' => $image->Image()->URL,
                'title' => $image->Title,
                'description' => $image->Description
            );
        }
        return Convert::array2json($data);
    }

    function ImagesString()
    {
        $images_string = "data = [";
        $images = $this->owner->GalleryImages();
        $num_items = count($images);
        $i = 1;
        foreach ($images as $image)
        {
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

    public function contentcontrollerInit() {
        $theme = $this->owner->themeDir();
        if($this->owner->GalleryImages()->count() > 0) {
            Requirements::css($theme.'mysite/javascript/galleria/themes/twelve/galleria.twelve.css');
            Requirements::javascript($theme.'/bower_components/galleria/src/galleria.js');
            Requirements::javascript('mysite/javascript/galleria/themes/twelve/galleria.twelve.min.js');
            Requirements::javascriptTemplate('mysite/javascript/Gallery.js', array("imageJson" => $this->ImagesString()));
        }
    } //init

}
