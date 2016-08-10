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
    function canEdit($Member = null){if(permission::check('EDIT_GALLERY')){return true;}else{return false;}}
    function canCreate($Member = null){if(permission::check('EDIT_GALLERY')){return true;}else{return false;}}

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

  // this function creates the thumnail for the summary fields to use
   public function getThumbnail() {
     return $this->Image()->CMSThumbnail();
  }
  public function generateRotateClockwise(GD $gd)    {
    return $gd->rotate(90);
  }

  public function generateRotateCounterClockwise(GD $gd)    {
    return $gd->rotate(270);
  }

  public function clearResampledImages()    {
    $files = glob(Director::baseFolder().'/'.$this->Parent()->Filename."_resampled/*-$this->Name");
    foreach($files as $file) {unlink($file);}
  }

  public function Landscape()    {
    return $this->getWidth() > $this->getHeight();
  }

  public function Portrait() {
    return $this->getWidth() < $this->getHeight();
  }

  public function generatePaddedImageByWidth(GD $gd,$width=600,$color="fff"){
    return $gd->paddedResize($width, round($gd->getHeight()/($gd->getWidth()/$width),0),$color);
  }

}
