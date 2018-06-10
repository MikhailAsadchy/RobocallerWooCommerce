<?php


class RCallerAdminLinks {

    public static function addAdminActionLinks($links) {
        $settings_link = '<a href="'.esc_url( self::getOptionsPageUrl(RCallerAdminConstants::ADMIN_OPTIONS_PAGE) ).'">'."Settings".'</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    public static function addOptionsPageMappings($param) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        add_options_page( "RCaller settings", "RCaller settings", 'manage_options', RCallerAdminConstants::ADMIN_OPTIONS_PAGE, array( 'RCallerSettingsPageRenderer', 'render_settings_page' ) );
    }

    private static function getOptionsPageUrl($settingsPageCode) {
        $args = array( 'page' => $settingsPageCode);
        $url = add_query_arg( $args, admin_url( 'options-general.php' ) );
        return $url;
    }
}
