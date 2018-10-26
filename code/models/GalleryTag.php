<?php
class GalleryTag extends DataObject
{
    private static $singular_name = 'Tag';

    private static $db = array(
        'Title' => 'Varchar(200)',
    );

    private static $belongs_many_many = array(
        'Galleries' => 'Gallery'
    );

    public function getTagTitle() {
        $tagTitle = preg_replace('/[^A-Za-z0-9]+/','-',$this->Title);
        $tagTitle = strtolower(preg_replace('/-+/','-',$tagTitle));
        return $tagTitle;
    }

    public function getCMSFields(){
        $fields = parent::getCMSFields();
        return $fields;
    }
    /*
     * Used to compare within array_unique() in FotosPage.php
     */
    public function __toString() {
        return $this->Title;
    }
    
    public function canView($member = null) {
        return Permission::check('CMS_ACCESS_GalleryAdmin', 'any', $member);
    }

    public function canEdit($member = null) {
        return Permission::check('CMS_ACCESS_GalleryAdmin', 'any', $member);
    }

    public function canDelete($member = null) {
        return Permission::check('CMS_ACCESS_GalleryAdmin', 'any', $member);
    }

    public function canCreate($member = null) {
        return Permission::check('CMS_ACCESS_GalleryAdmin', 'any', $member);
    }  
}
