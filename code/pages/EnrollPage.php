<?php
class EnrollPage extends Page
{
    private static $singular_name = 'Mitgliedsantrag';
    private static $description = 'Seite für Mitgliedsantrag';
    private static $icon = 'pageimages/images/enrollform.png';
    private static $can_be_root = false;
    private static $allowed_children = array('EnrollSuccessPage');

    private static $db = array();

    // Store relation to folder(FolderID)
    private static $has_one = array(
        // Store selected folder
        'Folder' => 'Folder'
    );

    /**
     * @config
     */
    private static $request_folder = 'antraege';

    function onBeforeWrite()
    {

        parent::onBeforeWrite();
        // Create a default folder to store forms
        if ($this->Folder()->ID == '0') {
            Config::inst()->get('EnrollPage', 'request_folder');
            $defaultFolderID = Folder::find_or_make('antraege')->ID;
            $this->owner->FolderID = $defaultFolderID;
        }
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab('Root.Main',LabelField::create('Das Formular wird im PHP-Code gepflegt.'),'Content');
        $fields->removeFieldFromTab('Root.Main','Content');
        //$config = HtmlEditorConfig::get('basic');
        //$fields->addFieldToTab('Root.Main', HtmlEditorField::create('Content','Inhalt',$config));
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsFields()
    {
        $fields = parent::getSettingsFields();
        // Get the current member
        $member = $this->getMember();

        // Limit user access to settings by permission for "Change site structure" (SITETREE_REORGANISE)
        if (Permission::checkMember($member, 'SITETREE_REORGANISE')) {
            // Add folder to be selectable from settings (Root.Settings)
            $requestFolderTreeDropDown = TreeDropdownField::create('FolderID',
                _t('EnrollPage.REQUESTSFOLDER', 'Folder:'), 'Folder')
                ->setDescription(_t('EnrollPage.REQUESTSFOLDERDESCRIPTION', 'Folder to store files created by form'), 'Folder to store files created by form');

            $fields->addFieldToTab("Root.Settings", $requestFolderTreeDropDown);
        }

        return $fields;
    }

    /**
     * Obtain a Member
     *
     * @param null|int|Member $member
     *
     * @return null|Member
     */
    protected function getMember($member = null)
    {
        if (!$member) {
            $member = Member::currentUser();
        }

        if (is_numeric($member)) {
            $member = Member::get()->byID($member);
        }

        return $member;
    }
}

class EnrollPage_Controller extends Page_Controller
{

    private static $allowed_actions = array(
        'EnrollForm'
    );

    public function EnrollForm() {
        $today = SS_Datetime::now()->FormatI18N("%d.%m.%Y");

        $fields = FieldList::create(
            DropdownField::create('Salutation', 'Anrede',
                //singleton('ClubMember')->dbObject('Salutation')->enumValues()
                array('Frau'=>'Frau','Herr'=>'Herr','Schülerin'=>'Schülerin','Schüler'=>'Schüler')
            ),
            TextField::create('FirstName', 'Vorname')
                ->setAttribute('placeholder', 'Vorname'),
            TextField::create('LastName', 'Nachname')
                ->setAttribute('placeholder', 'Nachname'),
            DateField::create('Birthday', 'Geburtstag')
                ->setAttribute('placeholder', $today)
                ->setAttribute('data-date-format', 'DD.MM.YYYY'),
            CountryDropdownField::create('Nationality', 'Nationalität'),
            TextField::create('Street', 'Straße')
                ->setAttribute('placeholder', 'Straße'),
            TextField::create('StreetNumber', 'Hausnummer')
                ->setAttribute('placeholder', 'Hausnummer'),
            ZipField::create('Zip', 'Postleitzahl')
                ->setAttribute('placeholder', '12345'),
            TextField::create('City', 'Wohnort')
                ->setAttribute('placeholder', 'Wohnort'),
            EmailField::create('Email', 'E-Mailadresse')
                ->setAttribute('placeholder', 'name@domain.de'),
            TextField::create('Mobil', 'Mobiltelefom')
                ->setAttribute('placeholder', 'Handynummer'),//PhoneNumberField
            TextField::create('Phone', 'Telefon')
                ->setAttribute('placeholder', 'Telefonnummer'),//PhoneNumberField
            DropdownField::create('TypeID', 'Mitgliedstyp',array('Vollverdiener'=>'Vollverdiener','Student / Azubi / Schüler'=>'Student / Azubi / Schüler')),
                //->setSource(ClubMemberType::get()->map('ID', 'TypeName')),
            DateField::create('Since', 'Mitglied ab')->setValue(SS_Datetime::now()->FormatI18N('%d.%m.%Y')),
            CheckboxField::create('EqualAddress', 'Mitgliedsadresse ist Kontoadresse')->setValue(true),
            TextField::create('AccountHolderFirstName', 'Kontoinhaber Vorname'),
            TextField::create('AccountHolderLastName', 'Kontoinhaber Nachname'),
            TextField::create('AccountHolderStreet', 'Kontoinhaber Straße'),
            TextField::create('AccountHolderStreetNumber', 'Kontoinhaber Hausnummer'),
            ZipField::create('AccountHolderZip', 'Kontoinhaber Postleitzahl'),
            TextField::create('AccountHolderCity', 'Kontoinhaber Wohnort'),
            IbanField::create('Iban', 'IBAN')
                ->setAttribute('placeholder', "DE12500105170648489890")->addExtraClass("text"),
            BicField::create('Bic', 'BIC')
                ->setAttribute('placeholder', "VOBADEXX")->addExtraClass("text")
        );

        $actions = new FieldList(
            FormAction::create('doEnroll')->setTitle(_t('EnrollPage.ENROLL', 'Enroll'))->setUseButtonTag(true)
        );

        $required = new RequiredFields('Salutation', 'FirstName', 'LastName', 'Birthday', 'Nationality', 'Street', 'StreetNumber', 'Zip', 'City', 'Email', 'Mobil', 'Phone', 'TypeID', 'Since', 'AccountHolderFirstName', 'AccountHolderLastName', 'AccountHolderStreet', 'AccountHolderStreetNumber', 'AccountHolderZip', 'AccountHolderCity', 'Iban', 'Bic');
        $form = new Form($this, 'EnrollForm', $fields, $actions, $required);
        $form->setTemplate('EnrollForm');
        $form->setFormMethod('POST', true);

        return $form;
    }

    public function doEnroll($data, Form $form)
    {
        // Add a success message
        //$form->sessionMessage('Vielen Dank für die Anmeldung ' .$data['FirstName']. ' ' .$data['LastName'], 'success');
        // Create a ClubMember object
        $clubMember = new ClubMemberPending();
        // Save data into object
        $form->saveInto($clubMember);
        // Serialize object safely
        $serialized = base64_encode(serialize($clubMember));
        // Get the desired folder to store the serialized object
        $folder = $this->Folder();
        // Get the path for the folder and add a filename
        $path = $folder->getFullPath() . $data['FirstName'][0] . $data['LastName'][0] . '_' . $data['Birthday'] . '_' . date('d.m.Y_H_i_s') . '.antrag';
        //SS_Log::log("path=".$path,SS_Log::WARN);
        /* Store the object at calculated path
         * If filename does not exist, the file is created. Otherwise,
         * the existing file is overwritten, unless the FILE_APPEND flag is set.
         */
        file_put_contents($path, $serialized);

        //Send an E-Mail
        $email = new Email();
        $email->setTo($data['Email'])->setSubject('Anmeldung bei Jim e.V.')->setTemplate('EnrollMail')->populateTemplate(new ArrayData($data));
        $email->send();

        //return $this->redirectBack();
        SS_Log::log(EnrollSuccessPage::get()->First()->Link(),SS_Log::WARN);
        Session::set('Data',$data);
        return $this->redirect(EnrollSuccessPage::get()->First()->Link());
    }

    function init()
    {
        parent::init();
        $theme = $this->themeDir();
        //Add javascript here
        Requirements::block(THIRDPARTY_DIR . '/jquery/jquery.js');
        Requirements::block('framework/javascript/DateField.js');
        Requirements::block('framework/thirdparty/jquery-ui/jquery-ui.js');
        Requirements::block('framework/thirdparty/jquery-ui/datepicker/i18n/jquery.ui.datepicker-de.js');
        Requirements::block(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');

        //Front-End validation
        Requirements::javascript('mysite/javascript/jquery-validate/jquery.validate.js');
        Requirements::javascript('mysite/javascript/jquery-validate/additional-methods.js');
        Requirements::javascript('mysite/javascript/jquery-validate/localization/messages_de.js');

        if(Director::isDev()) {
            // eonasdan Datetimepicker
            Requirements::css($theme.'/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css');
            Requirements::javascript($theme.'/bower_components/moment/min/moment-with-locales.js');
            Requirements::javascript($theme.'/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js');
            if(method_exists(Requirements::backend(), 'add_dependency')){
                Requirements::backend()->add_dependency('mysite/javascript/Enroll.js',$theme.'/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js');
            }
        } else {
            // eonasdan Datetimepicker
            Requirements::css($theme.'/dist/bower/css/bootstrap-datetimepicker.css');
            Requirements::javascript($theme.'/dist/bower/moment-with-locales.js');
            Requirements::javascript($theme.'/dist/bower/bootstrap-datetimepicker.min.js');
            if(method_exists(Requirements::backend(), 'add_dependency')){
                Requirements::backend()->add_dependency('mysite/javascript/Enroll.js',$theme.'/dist/bower/bootstrap-datetimepicker.min.js');
            }
        }

    } //init
}
