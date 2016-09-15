<?php
class FaqPage extends Page
{
    private static $singular_name = 'FAQ';
    private static $description = 'Seite für FAQ';
    //private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = true;
    private static $allowed_children = 'none';

    private static $has_many = array(
        'FAQS' => 'FAQ'
    );

    function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $config = GridFieldConfig_RelationEditor::create();
        $gridfield = GridField::create('FAQS', 'FAQs', $this->FAQS(), $config);
        $fields->addFieldToTab('Root.FAQs', $gridfield);

        $gridfield = GridField::create('Tags', 'Tags', FAQTag::get(), GridFieldConfig_RecordEditor::create());
        $fields->addFieldToTab('Root.Tags', $gridfield);


        return $fields;
    }

    public function Sections()
    {
        return FAQTag::get();
    }
}

class FaqPage_Controller extends Page_Controller
{
    private static $allowed_actions = array ();

    public function init() {
        parent::init();
        $theme = $this->themeDir();
        Requirements::javascript($theme.'/dist/javascript/scripts/pages/faq.js');
    }//init()

}
