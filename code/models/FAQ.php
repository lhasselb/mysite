<?php
class FAQ extends DataObject
{
    private static $db = array(
       'Question' => 'Varchar(255)',
       'Answer' => 'HTMLText'
    );

    private static $has_one = array(
        'FaqPage' => 'FaqPage'
    );

    private static $many_many = array(
        'FAQTags' => 'FAQTag'
    );

    private static $summary_fields = array(
        'Question'=> 'Frage',
        'Tags' => 'Bereich(e)'
    );

    public function Tags() {
        $tags = [];
        foreach ($this->FAQTags() as $tag) {
            array_push($tags,$tag->Title);
        }
        return implode(',',$tags);
    }

    public function getTitle() {
        return $this->Question;
    }

    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Question'] = 'Frage';
        $labels['Answer'] = 'Antwort';
        return $labels;
    }

    function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('FaqPageID');
        $fields->removeByName('FAQTags');
        $question = TextAreaField::create('Question','Frage')->setRows(1);
        $answer = HtmlEditorField::create('Answer','Antwort');
        $tag = TagField::create(
            'FAQTags',
            'FAQ Bereich',
            FAQTag::get(),
            $this->FAQTags()
        )
        ->setShouldLazyLoad(true) // tags should be lazy loaded
        ->setCanCreate(true);     // new tag DataObjects can be created

        $fields->addFieldsToTab('Root.Main', array($question,$answer,$tag));

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
