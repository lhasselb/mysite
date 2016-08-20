<?php
class Gallery extends DataObject
{
    private static $singular_name = 'Album';

    private static $db = array(
        'ImageFolder' => 'Varchar()',
        'AlbumName' => 'Varchar()',
        'AlbumDescription' => 'Varchar()',
        'AlbumYear' => 'Date',
    );

    private static $belongs_to = array(
        'Project' => 'ProjectPage.Gallery'
    );

    private static $has_one = array(
        'AlbumImage' => 'Image'
    );

    private static $belongs_many_many = array(
        'FotosPage' => 'FotosPage'
    );

    private static $has_many = array(
        'GalleryImages' => 'GalleryImage'
    );

    private static $many_many = array(
        'GalleryTags' => 'GalleryTag'
    );

    /**
     * @config @var string upload folder name used to store/load images
     */
    private static $gallery_folder_name = "fotoalben";

    private static $summary_fields = array(
        'AlbumName' => 'Name',
        'AlbumDescription' => 'Beschreibung',
        'NiceAlbumYear' => 'Datum',
        'getTags' => 'Tags',
        'ImageNumber' => 'Anzahl der Bilder',
        'ImageFolder' => 'Verzeichnis'
    );

    public function getNiceAlbumYear()
    {
        $date = new Date();
        $date->setValue($this->AlbumYear);
        return $date->Format('d.m.Y');
    }

    public function getImageNumber() {
        return $this->GalleryImages()->count();
    }

    public function getTitle() {
        return $this->AlbumName;
    }

    public function AlbumOrFirstImage() {
        if($this->AlbumImageID) {
            return $image = DataObject::get_by_id('Image',$this->AlbumImageID);
        }
        if($this->getFirstImage()) {
            return $image = DataObject::get_by_id('Image',$this->getFirstImage()->ImageID);
        }
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();
        $fields->removeByName('GalleryImages');
        $fields->removeByName('GalleryTags');
        $fields->removeByName('FotosPage');
        $fields->removeByName('AlbumImage');

        $fields->fieldByName('Root.Main')->setTitle('Album');

        $fields->addFieldToTab('Root.Main',ReadonlyField::create('ImageFolder','Verzeichnis'));
        $year = DateField::create('AlbumYear','Datum')
            ->setConfig('dataformat', 'yyyy')
            ->setConfig('showcalendar', true);
        $year->setDescription(sprintf('z.B. %s', Convert::raw2xml(Zend_Date::now()->toString('dd.MM.yyyy'))));
        $fields->addFieldToTab('Root.Main', $year);
        $tag = TagField::create(
            'GalleryTags',
            'Album-Tag(s)',
            GalleryTag::get(),
            $this->GalleryTags()
        )
        ->setShouldLazyLoad(true) // tags should be lazy loaded
        ->setCanCreate(true);     // new tag DataObjects can be created
        $fields->addFieldToTab('Root.Main', $tag);

        /*$fields->addFieldToTab('Root.Main',
            DropdownField::create('ProjectID', 'Projekt', ProjectPage::get()->map('ID', 'Title'))
                ->setEmptyString('(Bitte auswählen)')
        );*/

        $uploadfoldername = $this->ImageFolder;
        if(!empty($uploadfoldername)) {
            $albumImage = new UploadField('AlbumImage', 'Album-Bild');
            $albumImage->setFolderName($uploadfoldername);
            $albumImage->setDisplayFolderName($uploadfoldername);
            $fields->addFieldToTab('Root.Main', $albumImage);

            $gridFieldConfig = GridFieldConfig_RecordEditor::create();
            $gridFieldConfig->addComponent(new GridFieldBulkUpload());
            $gridFieldConfig->addComponent(new GridFieldBulkManager());
            // Used to determine upload folder
            $gridFieldConfig->getComponentByType('GridFieldBulkUpload')->setUfSetup('setFolderName', $uploadfoldername);
            //$gridFieldConfig->getComponentByType('GridFieldBulkUpload')->setUfSetup('setCanUpload', false);
            $gridFieldConfig->getComponentByType('GridFieldBulkUpload')->setUfSetup('setDisplayFolderName', $uploadfoldername);
            // Customise gridfield
            $gridFieldConfig->removeComponentsByType('GridFieldPaginator'); // Remove default paginator
            $gridFieldConfig->addComponent(new GridFieldPaginator(20)); // Add custom paginator
            $gridFieldConfig->addComponent(new GridFieldSortableRows('SortOrder'));
            $gridFieldConfig->removeComponentsByType('GridFieldAddNewButton'); // We only use bulk upload button
            // Creates sortable grid field
            $gridfield = new GridField('GalleryImages', 'Fotos', $this->GalleryImages()->sort('SortOrder'), $gridFieldConfig);
            $fields->addFieldToTab('Root.Fotos', $gridfield);
        }

        return $fields;
    }

    public function onBeforeWrite() {
        if(empty($this->ImageFolder)) {
            $base = Config::inst()->get('Gallery', 'gallery_folder_name');
            $albumName = preg_replace('/[^A-Za-z0-9]+/','-',$this->AlbumName);
            $albumName = strtolower(preg_replace('/-+/','-',$albumName));
            $this->ImageFolder = $base.'/'.$albumName;
        }
        // FotosPage is not visible in Editor, store a value automatically
        $fotosPage = DataObject::get_one('FotosPage');
        $this->FotosPage()->add($fotosPage);
        return parent::onBeforeWrite();
    }

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);
        $labels['AlbumName'] = 'Name';
        $labels['AlbumDescription'] = 'Beschreibung';
        $labels['ImageFolder'] = 'Verzeichnisname';
        $labels['AlbumImage'] = 'Album-Bild';
        return $labels;
    }

    public function getTags() {
        $tags = [];
        foreach ($this->GalleryTags() as $tag) {
            array_push($tags,$tag->Title);
        }
        return implode(',',$tags);
    }

    public function getSortedImages() {
        return $this->GalleryImages()->sort('SortOrder');
    }

    public function getFirstImage() {
        return $this->getSortedImages()->limit(1)->First();
    }

    public function cropImageFor($image, $width,$height) {
        return ($image->getWidth() > $width && $image->getHeight() > $height) ? 1 : 0;
    }

    public function getImagesJson() {
        $images = $this->getSortedImages();
        foreach ($images as $image) {
            //SS_Log::log($image->Image()->getWidth().' '.$image->Image()->getHeight(),SS_Log::WARN);
            $width = '400';
            $height ='300';
            $crop = $this->cropImageFor($image->Image(),$width,$height);
            //SS_Log::log('crop ? '.$crop,SS_Log::WARN);
            $data[] = array(
                'thumb' => $image->Image()->CroppedImage(80, 60)->URL,
                'image' => $image->Image()->CroppedImage(400, 300)->URL,
                'big' => $image->Image()->URL,
                'title' => $image->Title,
                'description' => $image->Description
            );
        }
        return Convert::array2json($data);
    }

    public function getFotosJson() {
        $images = $this->getSortedImages();
        foreach ($images as $image) {
            //SS_Log::log($image->Image()->getWidth().' '.$image->Image()->getHeight(),SS_Log::WARN);
            $width = '1024';
            $height ='768';
            $crop = $this->cropImageFor($image->Image(),$width,$height);
            //SS_Log::log('crop ? '.$crop,SS_Log::WARN);
            $data[] = array(
                'thumb' => $image->Image()->CroppedImage(80, 60)->URL,
                'image' => ($crop) ? $image->Image()->CroppedImage(1024, 786)->URL : $image->Image()->URL,
                'big' => $image->Image()->URL,
                'title' => $image->Title,
                'description' => $image->Description
            );
        }
        return Convert::array2json($data);
    }
    function Link() {
        $fotoPage = DataObject::get_one('FotosPage');//->Link('album').'?Album='.$this->AlbumName;
        return Controller::join_links($fotoPage->Link(),'album',$this->ID);
        //Link("showfaculty")."/".$this->ID;
        //return $this->AlbumName;
    }

/*
    public function getImagesString() {
        $images_string = "data = [";
        $images = $this->getSortedImages();
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
        $theme = $this->themeDir();
        if($this->GalleryImages()->count() > 0) {
            Requirements::css($theme.'mysite/javascript/galleria/themes/twelve/galleria.twelve.css');
            Requirements::javascript($theme.'/bower_components/galleria/src/galleria.js');
            Requirements::javascript('mysite/javascript/galleria/themes/twelve/galleria.twelve.min.js');
            Requirements::javascriptTemplate('mysite/javascript/Gallery.js', array("imageJson" => $this->ImagesString()));
        }
    } //init
*/
}
