<?php

class RCallerSettingsPageRenderer {

    public static function render_settings_page() {
        $file = RCallerConstants::PLUGIN_ROOT_DIR . 'admin/views/pluginSettingsPage.php';
        include( $file );
    }

    public static function saveSettings($userName, $password)
    {
        update_option(RCallerAdminConstants::USER_NAME_OPTION, $userName);
        update_option(RCallerAdminConstants::PASSWORD_OPTION, $password);
    }

    public static function checkCredentials($userName, $password)
    {
        $rcallerClient = new RCallerOrderSender();
        $data = self::getCheckCredentialsSampleOrder();

        $response = $rcallerClient->sendOrderToRCallerInternal($data, $userName, $password);

        return self::processResponse($response);
    }

    /**
     * @return array
     */
    private static function getCheckCredentialsSampleOrder()
    {
        $data = array(
            'price' => "0.1",
            'entries' => "check credentials entry",
            'customerAddress' => "check credentials address",
            'customerPhone' => "+375292765123",
            'customerName' => "Credentials checker",
            'priceCurrency' => "RUB",
            'channel' => "WooCommerce credentials checker");
        return $data;
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
