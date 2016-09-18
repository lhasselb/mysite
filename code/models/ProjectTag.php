<?php
class ProjectTag extends DataObject
{
    private static $singular_name = 'Bereich';

    private static $db = array(
        'Title' => 'Varchar(200)',
    );

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);
        $labels['Title'] = 'Name';
        return $labels;
    }

    private static $belongs_many_many = array(
        'Projects' => 'Project'
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
     * Used to compare within array_unique() in ProjectPage.php
     */
    public function __toString() {
        return $this->Title;
    }
}
