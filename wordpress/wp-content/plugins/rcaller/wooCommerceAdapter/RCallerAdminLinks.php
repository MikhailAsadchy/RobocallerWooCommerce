<?php
namespace rcaller\wooCommerceAdapter;

class RCallerAdminLinks {

    private $rCallerSettingsPageRenderer;

    public function __construct($rCallerSettingsPageRenderer)
    {
        $this->rCallerSettingsPageRenderer = $rCallerSettingsPageRenderer;
    }

    public function addAdminActionLinks($links) {
        $settings_link = '<a href="'.esc_url( self::getOptionsPageUrl(RCallerAdminConstants::ADMIN_OPTIONS_PAGE) ).'">'."Settings".'</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    public function addOptionsPageMappings($param) {
        add_options_page( "RCaller settings", "RCaller settings", 'manage_options', RCallerAdminConstants::ADMIN_OPTIONS_PAGE, array( $this->rCallerSettingsPageRenderer, 'renderSettingsPage' ) );
    }

    private function getOptionsPageUrl($settingsPageCode) {
        $args = array( 'page' => $settingsPageCode);
        $url = add_query_arg( $args, admin_url( 'options-general.php' ) );
        return $url;
    }
}
