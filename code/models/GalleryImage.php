<?php


class GalleryImage extends DataObject {

    private static $db = array(
      'SortOrder' => 'Int',
      'Title' => 'Varchar',
      'Description' => 'Varchar(400)'
  );

  // One-to-one relationship with gallery page
    private static $has_one = array(
    'Image' => 'Image',
    'Gallery' => 'DataObject',
  );

    private static $default_sort='SortOrder';

    //Permissions
    public function canEdit($Member = null){if(permission::check('EDIT_GALLERY')){return true;}else{return false;}}
    public function canCreate($Member = null){if(permission::check('EDIT_GALLERY')){return true;}else{return false;}}

    // Add fields to dataobject
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeFieldFromTab("Root.Main","GalleryID");
        $fields->removeFieldFromTab("Root.Main","SortOrder");

        $fields = new FieldList(
            new TextField('Title','Foto-Titel'),
            new TextAreaField('Description','Foto Beschreibung (Max 280 Zeichen)'),
            new UploadField('Image','Foto')
        );
        return $fields;
    }

  // Add validation
   public function validate() {
        $result = parent::validate();
        $charcount = strlen($this->Description);
        $description = 'Bitte weniger als 280 Zeichen in der Beschreibung. Es sind ';
        if($charcount > 280) {
            $result->error($description.' '.$charcount);
        }
        return $result;
    }

  // Tell the datagrid what fields to show in the table
   public static $summary_fields = array(
       'Title' => 'Title',
/*     'Description'=>'Description',*/
       'Thumbnail' => 'Thumbnail'
   );

    public function CroppedImage($width, $height) {
        SS_Log::log('CroppedImage for '.$this->Title, SS_Log::WARN);
        return $this->isSize($width, $height) && !Config::inst()->get('Image', 'force_resample') ?
            $this : $this->getFormattedImage('CroppedImage', $width, $height);
     }

}
