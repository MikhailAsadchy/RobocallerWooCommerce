<?php

namespace rcaller\lib\settings;

class RCallerSettingsPageRenderer
{
    private $credentialsManager;
    private $rCallerClient;
    private $rCallerFormHelper;

    /**
     * RCallerSettingsPageRenderer constructor.
     * @param $credentialsManager
     * @param $rCallerClient
     * @param $rCallerFormHelper
     */
    public function __construct($credentialsManager, $rCallerClient, $rCallerFormHelper)
    {
        $this->credentialsManager = $credentialsManager;
        $this->rCallerClient = $rCallerClient;
        $this->rCallerFormHelper = $rCallerFormHelper;
    }


    public function renderSettingsPage()
    {
        $checkCredentialsStatus = $this->rCallerFormHelper->processFormSubmission();
        $username = $this->credentialsManager->getUserName();
        $password = $this->credentialsManager->getPassword();
        echo $this->renderSettingsPageInternal($checkCredentialsStatus, $username, $password);
    }

    /**
     * @param $checkCredentialsStatus
     * @param $username
     * @param $password
     * @return string
     */
    private function renderSettingsPageInternal($checkCredentialsStatus, $username, $password)
    {
        return $this->rCallerFormHelper->renderSettingsTitle() . $this->renderSettingsForm($username, $password) . $this->rCallerFormHelper->renderCheckCredentialsStatus($checkCredentialsStatus);
    }

    /**
     * @param $username
     * @param $password
     * @return string
     */
    private function renderSettingsForm($username, $password)
    {
        return "
    <form method=\"post\">
        " . $this->rCallerFormHelper->renderUserNameField($username) . $this->rCallerFormHelper->renderPasswordField($password)
            . $this->rCallerFormHelper->renderCheckCredentialsButton() .
            $this->rCallerFormHelper->renderSaveButton() . "
    </form> 
    ";
    }

}