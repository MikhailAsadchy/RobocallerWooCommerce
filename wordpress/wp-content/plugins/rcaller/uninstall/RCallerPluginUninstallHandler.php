<?php


class RCallerPluginUninstallHandler {

    public static function pluginUninstall() {
        include_once ABSPATH . "wp-includes/option.php";

        delete_option(RCallerAdminConstants::USER_NAME_OPTION);
        delete_option(RCallerAdminConstants::PASSWORD_OPTION);
    }
}
