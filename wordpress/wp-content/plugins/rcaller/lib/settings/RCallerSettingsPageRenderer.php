<?php

namespace rcaller\lib\settings;

class RCallerSettingsPageRenderer {

    private static $credentialsManager;

    public static function render_settings_page() {
        echo "";
//        $file = RCallerConstants::PLUGIN_ROOT_DIR . 'admin/views/pluginSettingsPage.php';
//        include( $file );
    }

    public static function saveSettings($userName, $password)
    {
//        self::$credentialsManager->storeCredentials($userName, $password);
    }

    public static function checkCredentials($userName, $password)
    {
//        $response = RCallerClient::checkRCallerCredentials($userName, $password);
//        return self::processResponse($response);
    }

    /**
     * @param $httpCode
     * @return string
     */
    private static function processResponse($httpCode)
    {
        if ($httpCode === 200) {
            $checkCredentialsResult = "success";
        } else if ($httpCode === 404) {
            $checkCredentialsResult = "bad credentials";
        } else {
            $checkCredentialsResult = "unknown error";
        }
        return $checkCredentialsResult;
    }

}
