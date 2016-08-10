<?php
class GalleryTag extends DataObject
{
    private static $db = array(
        'Title' => 'Varchar(200)',
    );

    private static $belongs_many_many = array(
        'Galleries' => 'Gallery'
    );

    function getCMSFields(){
        $fields = parent::getCMSFields();
        return $fields;
    }
}
