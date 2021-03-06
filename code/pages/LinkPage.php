<?php
class LinkPage extends Page
{
    private static $singular_name = 'Linksammlung';
    private static $description = 'Seite für Links';
    //private static $icon = 'mysite/images/treffen.png';
    private static $can_be_root = true;
    private static $allowed_children = 'none';

    private static $db = array();

    private static $has_many = array(
        'Linkset' => 'LinkSet');

    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Linkset'] = 'Sammlung';
        return $labels;
    }

    function getCMSFields(){
        $fields = parent::getCMSFields();
        /*$fields->addFieldToTab('Root.Main',
            HtmlEditorField::create('Content', $this->fieldLabel('Content'))
            ->setRows(3)
        );*/
        $fields->removeByName('Content');
        $config = GridFieldConfig_RecordEditor::create();
        //$config->removeComponentsByType($config->getComponentByType('GridFieldAddNewButton'));
        $gridfield = GridField::create("Linkset", "Link-Sammlungen", $this->Linkset(), $config);
        //$fields->addFieldToTab('Root.Link-Sammlungen', $gridfield);
        $fields->addFieldToTab('Root.Main', $gridfield,'Metadata');

        return $fields;
    }

}

class LinkPage_Controller extends Page_Controller
{
    private static $allowed_actions = array ();

    public function init() {
        parent::init();
    }//init()

}
