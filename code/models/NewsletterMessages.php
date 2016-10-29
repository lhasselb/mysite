<?php
class NewsletterMessages extends DataObject
{
    static $singular_name = 'Newsletter-Texte';
    static $description = 'Texte für das Newsletter-Formular';

    private static $db = array(
        'Headline' => 'Varchar(255)',
        'AddText' => 'Varchar(255)',
        'RemoveText' => 'HTMLText',
        'SuccessText' => 'HTMLText'
    );

    private static $has_one = array(
        'Homepage' => 'HomePage'
    );

    private static $summary_fields = array(
        'Headline' => '',
        'AddText' => '',
        'RemoveText' => '',
        'SuccessText' => '',
    );

    // Offer a Title
    public function getTitle() {
        return 'Newsletter-Texte';
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('HomepageID');
        $fields->addFieldToTab('Root.Main', TextField::create('Headline', 'Üerschrift'));
        $fields->addFieldToTab('Root.Main', TextField::create('AddText', 'Text zum Registrieren'));
        $fields->addFieldToTab('Root.Main', HtmlEditorField::create('RemoveText', 'Text zum Abmelden')->setRows(10));
        $fields->addFieldToTab('Root.Main', HtmlEditorField::create('SuccessText', 'Erfolgreiche Registrierung')->setRows(10));
        return $fields;
    }

}
