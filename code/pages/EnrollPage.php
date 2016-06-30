<?php

/**
 * Enoll page
 *
 * @package clubmaster
 * @subpackage pages
 *
 */
class EnrollPage extends Page
{
    private static $singular_name = 'Enroll';
    private static $description = 'Enroll page using a form';
    private static $icon = 'pageimages/images/enrollform.png';
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
            DropdownField::create('Salutation', _t('ClubMember.SALUTATION', 'Salutation'),
                singleton('ClubMember')->dbObject('Salutation')->enumValues())
                ->setAttribute('placeholder', 'Herr'),
            TextField::create('FirstName', _t('ClubMember.FIRSTNAME', 'FirstName'))
                ->setAttribute('placeholder', _t('ClubMember.FIRSTNAME', 'Firstname')),
            TextField::create('LastName', _t('ClubMember.LASTNAME', 'LastName'))
                ->setAttribute('placeholder', _t('ClubMember.LASTNAME', 'Lastname')),
            DateField::create('Birthday', _t('ClubMember.BIRTHDAY', 'Birthday'))
                ->setConfig('showcalendar', true)
                ->setAttribute('placeholder', $today)
                ->setAttribute('data-provide', 'datepicker'),

            CountryDropdownField::create('Nationality', _t('ClubMember.NATIONALITY', 'Nationality')),
            TextField::create('Street', _t('ClubMember.STREET', 'Street')),
            TextField::create('StreetNumber', _t('ClubMember.STREETNUMBER', 'StreetNumber')),
            ZipField::create('Zip', _t('ClubMember.ZIP', 'Zip')),
            TextField::create('City', _t('ClubMember.CITY', 'City')),
            EmailField::create('Email', _t('ClubMember.EMAIL', 'Email')),
            TextField::create('Mobil', _t('ClubMember.MOBIL', 'Mobil')),//PhoneNumberField
            TextField::create('Phone', _t('ClubMember.PHONE', 'Phone')),//PhoneNumberField
            DropdownField::create('TypeID', _t('ClubMember.TYPE', 'Type'))
                ->setSource(ClubMemberType::get()->map('ID', 'TypeName')),
            DateField::create('Since', _t('ClubMember.FROM', 'From'))->setConfig('showcalendar', true)
                ->setValue(SS_Datetime::now()->FormatI18N('%d.%m.%Y')),
            CheckboxField::create('EqualAddress', _t('ClubMember.EQUALADDRESS', 'EqualAddress'))
                ->setValue(true),
            TextField::create('AccountHolderFirstName', _t('ClubMember.ACCOUNTHOLDERFIRSTNAME', 'AccountHolderFirstName')),
            TextField::create('AccountHolderLastName', _t('ClubMember.ACCOUNTHOLDERLASTNAME', 'AccountHolderLastName')),
            TextField::create('AccountHolderStreet', _t('ClubMember.ACCOUNTHOLDERSTREET', 'AccountHolderStreet')),
            TextField::create('AccountHolderStreetNumber', _t('ClubMember.ACCOUNTHOLDERSTREETNUMBER', 'AccountHolderStreetNumber')),
            ZipField::create('AccountHolderZip', _t('ClubMember.ACCOUNTHOLDERZIP', 'AccountHolderZip')),
            TextField::create('AccountHolderCity', _t('ClubMember.ACCOUNTHOLDERCITY', 'AccountHolderCity')),
            IbanField::create('Iban', _t('ClubMember.IBAN', 'Iban'))
                ->setAttribute('placeholder', "DE12500105170648489890")->addExtraClass("text"),
            BicField::create('Bic', _t('ClubMember.BIC', 'Bic'))
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

        $form->sessionMessage('Vielen Dank für die Anmeldung ' .$data['FirstName']. ' ' .$data['LastName'], 'success');

        /*foreach ($data as $key => $value) {
            SS_Log::log("key=".$key." value=".$value,SS_Log::WARN);
        }*/
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

        //Send E-Mail
        $email = new Email();
        $email->setTo($data['Email'])->setSubject('Anmeldung bei Jim e.V.')->setTemplate('EnrolllEmail')->populateTemplate(new ArrayData($data));
        $email->send();

        return $this->redirectBack();
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
        //Requirements::css("thirdparty/datepicker/bootstrap-datepicker.css");
        //Requirements::javascript("thirdparty/datepicker/bootstrap-datepicker.js");

        //Front-End validation
        Requirements::javascript('mysite/javascript/jquery-validate/jquery.validate.js');
        Requirements::javascript('mysite/javascript/jquery-validate/additional-methods.js');
        Requirements::javascript('mysite/javascript/jquery-validate/localization/messages_de.js');
/*
        Requirements::css($theme.'/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css');
        Requirements::javascript($theme.'/bower_components/moment/min/moment-with-locales.js');
        Requirements::javascript($theme.'/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js');
        //SS_Log::log('init()',SS_Log::WARN);
        if(method_exists(Requirements::backend(), 'add_dependency')){
            Requirements::backend()->add_dependency('mysite/javascript/Enroll.js',$theme.'/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js');
        }
*/
        Requirements::css($theme.'/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css');
        Requirements::javascript($theme.'/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.js');
        Requirements::javascript($theme.'/bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.de.min.js');
        Requirements::javascript('mysite/javascript/Enroll.js');
    }
}
