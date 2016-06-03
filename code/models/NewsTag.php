<?php
class NewsTag extends DataObject {

    private static $db = array(
        'Title' => 'Varchar(200)',
    );

    private static $belongs_many_many = array(
        'NewsItems' => 'NewsItem'
    );

}
