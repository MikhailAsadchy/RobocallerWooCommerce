<?php


class RCallerPluginInstallHandler {

    public static function pluginInstall() {
        include_once ABSPATH . "wp-includes/option.php";

        add_option(RCallerAdminConstants::USER_NAME_OPTION);
        add_option(RCallerAdminConstants::PASSWORD_OPTION);
    }
}
