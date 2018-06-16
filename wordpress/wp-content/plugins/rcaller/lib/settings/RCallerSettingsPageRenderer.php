<?php

namespace rcaller\lib\settings;

class RCallerSettingsPageRenderer
{
    const SETTINGS_FORM_USERNAME = "rcaller_username";
    const SETTINGS_FORM_PASSWORD = "rcaller_password";

    private $credentialsManager;
    private $rCallerClient;

    public function __construct($credentialsManager, $rCallerClient)
    {
        $this->credentialsManager = $credentialsManager;
        $this->rCallerClient = $rCallerClient;
    }

    public function renderSettingsPage()
    {
        $checkCredentialsStatus = "";
        if ($this->isPostMethod() && $this->shouldHandlePost()) {
            if ($this->isCheckCredentialsRequest()) {
                $checkCredentialsStatus = $this->doCheckCredentials();
                if ($checkCredentialsStatus === "success") {
                    $this->doSaveSettings();
                }
            } else if ($this->isSaveSettingsRequest()) {
                $this->doSaveSettings();
            }
        }
        $username = $this->credentialsManager->getUserName();
        $password = $this->credentialsManager->getPassword();
        echo $this->renderSettingsPageInternal($checkCredentialsStatus, $username, $password);
    }

    /**
     * @param $httpCode
     * @return string
     */
    private function processResponse($httpCode)
    {
        if ($httpCode === 200) {
            $checkCredentialsResult = "success";
        } else if ($httpCode === 401) {
            $checkCredentialsResult = "bad credentials";
        } else if ($httpCode == 403) {
            $checkCredentialsResult = "You have negative balance, so the requests to rcaller will not be sent";
        } else {
            $checkCredentialsResult = "unknown error";
        }
        return $checkCredentialsResult;
    }

    /**
     * @param $checkCredentialsStatus
     * @param $username
     * @param $password
     * @return string
     */
    private function renderSettingsPageInternal($checkCredentialsStatus, $username, $password)
    {
        return $this->renderSettingsTitle() . $this->renderSettingsForm($username, $password) . $this->renderCheckCredentialsStatus($checkCredentialsStatus);
    }

    /**
     * @param $checkCredentialsStatus
     * @return string
     */
    private function renderCheckCredentialsStatus($checkCredentialsStatus)
    {
        if (!empty($checkCredentialsStatus)) {
            return "<div>RCaller credentials status: " . $checkCredentialsStatus . "</div>";
        } else {
            return "";
        }
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
        <input name=\"" . self::SETTINGS_FORM_USERNAME . "\" type=\"text\" size=\"25\"
               value=\"" . $username . "\">
        <input name=\"" . self::SETTINGS_FORM_PASSWORD . "\" type=\"password\" size=\"25\"
               value=\"" . $password . "\">
        <input type=\"submit\" name=\"checkCredentials\" value=\"Check credentials\">
        <input type=\"submit\" name=\"save\" value=\"Save\">
    </form> 
    ";
    }

    /**
     * @return string
     */
    private function renderSettingsTitle()
    {
        return "<div>Configure RCaller credentials</div>";
    }

    /**
     * @return mixed
     */
    private function isCheckCredentialsRequest()
    {
        return $_POST["checkCredentials"];
    }

    /**
     * @return mixed
     */
    private function isSaveSettingsRequest()
    {
        return $_POST["save"];
    }

    /**
     * @return bool
     */
    private function shouldHandlePost()
    {
        return $this->isCheckCredentialsRequest() || $this->isSaveSettingsRequest();
    }

    /**
     * @return bool
     */
    private function isPostMethod()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    private function doCheckCredentials()
    {
        $userName = $_POST[self::SETTINGS_FORM_USERNAME];
        $password = $_POST[self::SETTINGS_FORM_PASSWORD];
        $responseCode = $this->rCallerClient->checkRCallerCredentials($userName, $password);
        return $this->processResponse($responseCode);
    }

    private function doSaveSettings()
    {
        $userName = $_POST[self::SETTINGS_FORM_USERNAME];
        $password = $_POST[self::SETTINGS_FORM_PASSWORD];
        $this->credentialsManager->storeCredentials($userName, $password);
    }
}