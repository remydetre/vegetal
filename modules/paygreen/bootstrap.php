<?php
/**
 * 2014 - 2020 Watt Is It
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons BY-ND 4.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://creativecommons.org/licenses/by-nd/4.0/fr/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@paygreen.fr so we can send you a copy immediately.
 *
 * @author    PayGreen <contact@paygreen.fr>
 * @copyright 2014 - 2020 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   3.0.1
 */

try {
    // #############################################################################################
    // Setting module constants
    // #############################################################################################

    if (!defined('DS')) {
        define('DS', DIRECTORY_SEPARATOR);
    }

    define('PAYGREEN_MODULE_VERSION', '3.0.1');

    define('PAYGREEN_MODULE_DIR', _PS_MODULE_DIR_ . 'paygreen');
    define('PAYGREEN_MODULE_NAME', 'paygreen');
    define('PAYGREEN_VENDOR_DIR', PAYGREEN_MODULE_DIR . DS . 'bundles');
    define('PAYGREEN_VAR_DIR', _PS_ROOT_DIR_ . DS . 'var' . DS . 'paygreen');
    define('PAYGREEN_MEDIA_DIR', _PS_ROOT_DIR_ . DS . 'img' . DS . 'paygreen');
    define('PAYGREEN_CONFIG_DIR', _PS_ROOT_DIR_ . DS . 'config' . DS . 'paygreen');
    define('PAYGREEN_PLATEFORM_VERSION', (float) implode('.', explode('.', _PS_VERSION_, 2)));

    if (Shop::isFeatureActive() && (Shop::getContext() === Shop::CONTEXT_SHOP)) {
        define('PAYGREEN_CACHE_PREFIX', 'shop-' . Context::getContext()->shop->id);
    }


    // #############################################################################################
    // Running Bootstrap
    // #############################################################################################

    require_once PAYGREEN_VENDOR_DIR . DS . 'PGFramework' . DS . 'Bootstrap.php';

    $bootstrap = new PGFrameworkBootstrap(PAYGREEN_VENDOR_DIR);

    $additionalVendors = array('PGLegacy');

    switch (PAYGREEN_PLATEFORM_VERSION) {
        case 1.6:
            $additionalVendors[] = 'PGUnaSextus';
            break;
        case 1.7:
            $additionalVendors[] = 'PGUnaSeptimus';
            break;
        default:
            throw new Exception("PayGreen module only support 1.6 or 1.7 Prestashop versions.");
    }

    $bootstrap
        ->buildAppliance('Paygreen payment module for Prestashop')
        ->addVendors($additionalVendors)
        ->buildPathfinder(array(
            'static' => PAYGREEN_MODULE_DIR . '/views/static',
            'module' => PAYGREEN_MODULE_DIR,
            'var' => PAYGREEN_VAR_DIR,
            'media' => PAYGREEN_MEDIA_DIR,
            'config' => PAYGREEN_CONFIG_DIR
        ))
        ->preloadFunctions()
        ->createVarFolder()
        ->registerAutoloader()
        ->buildContainer()
        ->insertStaticServices()
    ;


    // #############################################################################################
    // Init Shop
    // #############################################################################################

    /** @var PGFrameworkServicesLogger $shopHandler */
    $logger = $bootstrap->getContainer()->get('logger');

    /** @var PGDomainInterfacesShopHandlerInterface $shopHandler */
    $shopHandler = $bootstrap->getContainer()->get('handler.shop');

    /** @var PGDomainInterfacesEntitiesShopInterface $shop */
    $shop = $shopHandler->getCurrentShop();

    $logger->debug("Current shop detected : {$shop->getName()} #{$shop->id()}.");
    $logger->debug("Running PayGreen module for URI : {$_SERVER['REQUEST_URI']}");


    // #############################################################################################
    // Logging PHP errors
    // #############################################################################################

    if (PAYGREEN_ENV === 'DEV') {
        ini_set('error_log', PAYGREEN_VAR_DIR . DS . 'error.log');
    }
} catch (Exception $exception) {
    $file = $exception->getFile();
    $line = $exception->getLine();
    $message = "Error during Paygreen bootstrap ($file#$line) : {$exception->getMessage()}";

    PrestaShopLogger::addLog($message, 4);

    if (PAYGREEN_ENV === 'DEV') {
        die($message);
    } else {
        throw $exception;
    }
}
