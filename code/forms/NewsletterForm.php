<?php

class NewsletterForm extends Form {

    /**
     * Our constructor only requires the controller and the name of the form
     * method. We'll create the fields and actions in here.
     *
     */
    public function __construct($controller, $name) {
        // Create fields
        $fields = new FieldList(
            EmailField::create('Email','E-Mail')
            ->setAttribute('placeholder','Email'));
        // Create actions
        $actions = new FieldList(
            FormAction::create('doAddEmail', 'Absenden')
        );
        // Create required fields
        $required = RequiredFields::create('Email');
        // now we create the actual form with our fields and actions defined
        // within this class
        parent::__construct($controller, $name, $fields, $actions, $required);
    }

    public function getSucessMessage() {
        $messages = HomePage::get()->First()->Messages()->First();
        //SS_Log::log($messages->SuccessText,SS_Log::WARN);
        return $messages->SuccessText;
    }

    public function getHeadline() {
        $messages = HomePage::get()->First()->Messages()->First();
        return $messages->Headline;
    }

    public function getAddText() {
        $messages = HomePage::get()->First()->Messages()->First();
        return $messages->AddText;
    }

    public function getRemoveText() {
        $messages = HomePage::get()->First()->Messages()->First();
        return $messages->RemoveText;
    }

    public function doAddEmail($data, $form)
    {
        $email = $data['Email'];
        $newsletter = NewsletterAddress::create();
        if(!DataObject::get_one('NewsletterAddress', "Email = '$email'"))
        {
            //SS_Log::log('Found '.$data['Email'],SS_Log::WARN);
            $newsletter->Email = $data['Email'];
            $newsletter->write();
        }

        if ($this->request->isAjax()) {
            return $this->customise(array(
                'ShowSuccess' => true,
                'SuccessMessage' => $this->getSucessMessage()
            ))->renderWith('NewsletterForm');

        } else {
            //this would be if it wasn't an ajax request, generally a redirect to success/failure page
            $controller = $this->getController();
            return $controller->redirectBack();
        }

    }

}
