<?php


class RCallerPluginUninstallHandler {

    public static function pluginUninstall() {
        delete_option(RCallerAdminConstants::USER_NAME_OPTION);
        delete_option(RCallerAdminConstants::PASSWORD_OPTION);
    }
}
