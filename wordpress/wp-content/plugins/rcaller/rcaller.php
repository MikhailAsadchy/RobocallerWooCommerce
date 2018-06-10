<?php
/**
 * @package Akismet
 */
/*
Plugin Name: WooCommerce RCaller
Plugin URI: https://rcaller.com
Description:
Author: RCaller
Version: 1.0
Author URI:
*/
// Make sure we don't expose any info if called directly
const PLUGIN_ACTION_LINKS_EVENT = 'plugin_action_links_';

function import()
{
    include_once ABSPATH . "wp-includes/option.php";
    include_once plugin_dir_path(__FILE__) . "RCallerConstants.php";
    include_once plugin_dir_path(__FILE__) . "admin/RCallerAdminLinks.php";
    include_once plugin_dir_path(__FILE__) . "admin/RCallerAdminConstants.php";
    include_once plugin_dir_path(__FILE__) . "admin/RCallerSettingsPageRenderer.php";
    include_once plugin_dir_path(__FILE__) . "install/RCallerPluginInstallHandler.php";
    include_once plugin_dir_path(__FILE__) . "uninstall/RCallerPluginUninstallHandler.php";
    include_once plugin_dir_path(__FILE__) . "client/RCallerOrderSender.php";
}

function registerPluginHooks()
{
    register_activation_hook(__FILE__, array('RCallerPluginInstallHandler', 'pluginInstall'));
    register_deactivation_hook(__FILE__, array('RCallerPluginUninstallHandler', 'pluginUninstall'));
}

/**
 * @return bool
 */
function isWooCommercePluginActive()
{
    return is_plugin_active("woocommerce/woocommerce.php");
}

/**
 * @return bool
 */
function isDirectRequest()
{
    return !function_exists('add_action');
}

function subscribeEvents()
{
    $rcallerClient = new RCallerOrderSender();
    add_action('admin_menu', array('RCallerAdminLinks', 'addOptionsPageMappings'));
    add_action('woocommerce_checkout_order_processed', array($rcallerClient, 'sendOrderToRCaller'), 10, 3);

    $pluginAdminActionLinksEventName = PLUGIN_ACTION_LINKS_EVENT . RCallerConstants::PLUGIN_MAIN_FILE;
    add_filter($pluginAdminActionLinksEventName, array('RCallerAdminLinks', 'addAdminActionLinks'));
}

if (isDirectRequest()) {
    exit;
}
include_once ABSPATH . 'wp-admin/includes/plugin.php';
if (isWooCommercePluginActive()) {
    import();
    registerPluginHooks();
    subscribeEvents();
}
