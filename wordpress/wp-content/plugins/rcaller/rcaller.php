<?php
/*
Plugin Name: WooCommerce RCaller
Plugin URI: https://rcaller.com
Description:
Author: RCaller
Version: 1.0
Author URI:
*/

use rcaller\adapter\RCallerAdapterImport;
use rcaller\adapter\WooCommerceChannelNameProvider;
use rcaller\adapter\WooCommerceEventService;
use rcaller\adapter\WooCommerceLogger;
use rcaller\adapter\WooCommerceOptionsRepository;
use rcaller\adapter\WooCommerceOrderEntryFieldResolver;
use rcaller\lib\constants\RCallerConstants;
use rcaller\lib\ioc\RCallerDependencyContainer;
use rcaller\lib\RCallerImport;
use rcaller\wooCommerceAdapter\RCallerAdminLinks;
const PLUGIN_ACTION_LINKS_EVENT = 'plugin_action_links_';

function import()
{
    include_once ABSPATH . "wp-includes/option.php";

    $pluginRoot = plugin_dir_path(__FILE__);
    include_once $pluginRoot . "lib/util/StrictImporter.php";
    include_once $pluginRoot . "lib/RCallerImport.php";
    RCallerImport::importRCallerLib();

    include_once $pluginRoot . "adapter/RCallerAdapterImport.php";
    RCallerAdapterImport::importRCallerAdapter();

    include_once $pluginRoot . "wooCommerceAdapter/RCallerPlaceOrderHandler.php";
    include_once $pluginRoot . "wooCommerceAdapter/RCallerAdminConstants.php";
    include_once $pluginRoot . "wooCommerceAdapter/RCallerAdminLinks.php";
}

function registerPluginHooks($ioc)
{
    $pluginManager = $ioc->getPluginManager();

    register_activation_hook(__FILE__, array($pluginManager, 'addOptions'));
    register_deactivation_hook(__FILE__, array($pluginManager, 'removeOptions'));
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

function subscribeEvents($ioc)
{
    $pluginManager = $ioc->getPluginManager();
    $pluginManager->subscribePlaceOrderEvent();

    $rCallerSettingsPageRenderer = $ioc->getRCallerSettingsPageRenderer();
    $rCallerAdminLinks = new RCallerAdminLinks($rCallerSettingsPageRenderer);

    add_action('admin_menu', array($rCallerAdminLinks, 'addOptionsPageMappings'));
    $pluginAdminActionLinksEventName = PLUGIN_ACTION_LINKS_EVENT . RCallerConstants::PLUGIN_MAIN_FILE;
    add_filter($pluginAdminActionLinksEventName, array($rCallerAdminLinks, 'addAdminActionLinks'));
}

if (isDirectRequest()) {
    exit;
}
include_once ABSPATH . 'wp-admin/includes/plugin.php';
if (isWooCommercePluginActive()) {
    import();
    $ioc = new RCallerDependencyContainer(new WooCommerceEventService(), new WooCommerceLogger(), new WooCommerceOptionsRepository(), new WooCommerceChannelNameProvider(), new WooCommerceOrderEntryFieldResolver());

    registerPluginHooks($ioc);
    subscribeEvents($ioc);
}
