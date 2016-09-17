<?php
class Vorstand extends DataObject
{
    private static $db = array(
       'Name' => 'Varchar(255)',
       'Role' => 'Varchar(255)',
       'Mail' =>  'Varchar(255)'
    );

    private static $has_one = array(
       'Bild' => 'Image',
        'ContactAddressPage' => 'ContactAddressPage'
    );

    private static $summary_fields = array(
        'Name'=> 'Name',
        'Role' => 'Funktion',
        'GridThumbnail' => 'Vorschau'
    );

    public function getTitle() {
        return $this->Name;
    }

    public function getGridThumbnail()
    {
        if($this->Bild()->exists()) {
            return $this->Bild()->SetWidth(100);
        }

        return '(kein Bild)';
    }

    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);
        $labels['Name'] = 'Name';
        $labels['Role'] = 'Funktion';
        $labels['Mail'] = 'E-Mail';
        return $labels;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $uploadfoldername = substr($this->Link(), 1, -1);
        //SS_Log::log($uploadfoldername ,SS_Log::WARN);
        $fields->removeByName('ContactAddressPageID');
        $name = TextField::create('Name','Name');
        $role = TextField::create('Role','Funktion');
        $mail = TextField::create('Mail','E-Mail');
        $bildUploadField = new UploadField('Bild', 'Bild');
        $bildUploadField->getValidator()->allowedExtensions = array('jpg', 'gif', 'png');
        $bildUploadField->setFolderName($uploadfoldername);
        $bildUploadField->setDisplayFolderName($uploadfoldername);
        $fields->addFieldsToTab('Root.Main', array($name,$role,$mail,$bildUploadField));
        return $fields;
    }

    public function Link() {
        $page = DataObject::get_by_id('ContactAddressPage',$this->ContactAddressPageID);
        return Controller::join_links($page->Link());
        //return Controller::curr()->Link();
    }
}
