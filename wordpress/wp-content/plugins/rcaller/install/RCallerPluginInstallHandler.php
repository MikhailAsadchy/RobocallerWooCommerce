<?php


class RCallerPluginInstallHandler {

    public static function pluginInstall() {
        add_option(RCallerAdminConstants::USER_NAME_OPTION);
        add_option(RCallerAdminConstants::PASSWORD_OPTION);
    }
}
