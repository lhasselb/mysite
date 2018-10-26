<?php
class FAQTag extends DataObject
{
    private static $db = array(
        'Title' => 'Varchar(200)',
    );

    private static $belongs_many_many = array(
        'FAQS' => 'FAQ'
    );

    function getCMSFields()
    {
        $fields = parent::getCMSFields();
        return $fields;
    }
    
    /**
     * @return boolean
     */
    public function canView($member = null) {
        return Permission::check('CMS_ACCESS_CMSMain');
    }
    
    /**
     * @return boolean
     */
    public function canCreate($member = null) {
        return Permission::check('CMS_ACCESS_CMSMain');
    }
    
    /**
     * @return boolean
     */
    public function canEdit($member=null) {
        return Permission::check('CMS_ACCESS_CMSMain');
    }
    
    /**
     * @return boolean
     */
    public function canDelete($member = null) {
        return Permission::check('CMS_ACCESS_CMSMain');
    }
}
