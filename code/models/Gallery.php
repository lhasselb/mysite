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

    private static $has_one = array(
        'ProjectPage' => 'ProjectPage'
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
        'Tags' => 'Tags',
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

    public function getCMSFields() {

        $fields = parent::getCMSFields();
        $fields->removeByName('GalleryImages');
        $fields->removeByName('GalleryTags');
        $fields->fieldByName('Root.Main')->setTitle('Album');
        $fields->addFieldToTab('Root.Main',ReadonlyField::create('ImageFolder','Verzeichnis'));
        $year = DateField::create('AlbumYear','Datum')
            ->setConfig('dataformat', 'yyyy')
            ->setConfig('showcalendar', true);
        $year->setDescription(sprintf('z.B. %s', Convert::raw2xml(Zend_Date::now()->toString('dd.MM.yyyy'))));
        $fields->addFieldToTab('Root.Main', $year);
        $tag = TagField::create(
            'GalleryTags',
            'Album Bereich',
            GalleryTag::get(),
            $this->GalleryTags()
        )
        ->setShouldLazyLoad(true) // tags should be lazy loaded
        ->setCanCreate(true);     // new tag DataObjects can be created
        $fields->addFieldToTab('Root.Main', $tag);
        $fields->addFieldToTab('Root.Main',
            DropdownField::create('ProjectPageID', 'Projekt', ProjectPage::get()->map('ID', 'Title'))
                ->setEmptyString('(Bitte auswählen)')
        );

        $uploadfoldername = $this->ImageFolder;
        if(!empty($uploadfoldername)) {
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
        return parent::onBeforeWrite();
    }

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);
        $labels['AlbumName'] = 'Name';
        $labels['AlbumDescription'] = 'Beschreibung';
        $labels['ImageFolder'] = 'Verzeichnisname';
        return $labels;
    }

    public function Tags() {
        $tags = [];
        foreach ($this->GalleryTags() as $tag) {
            array_push($tags,$tag->Title);
        }
        return implode(',',$tags);
    }

    public function GetFirstImage() {
        return $this->GalleryImages()->Sort($this->Sorter)->limit(1)->First();
    }

    public function GetSortedImages() {
        return $this->GalleryImages()->sort('SortOrder');
    }

    /*public function ImagesJson() {
        $images = $this->GalleryImages();
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
    }*/

    function ImagesString() {
        $images_string = "data = [";
        $images = $this->GetSortedImages();
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

}
