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

if (!defined('_PS_VERSION_')) {
    exit;
}

$path = _PS_MODULE_DIR_ . 'paygreen'
    . DIRECTORY_SEPARATOR . 'bundles'
    . DIRECTORY_SEPARATOR . 'PGModule'
    . DIRECTORY_SEPARATOR . 'Bridges'
    . DIRECTORY_SEPARATOR . 'PrestashopBridge.php';

require_once $path;

/**
 * Class Paygreen
 */
class Paygreen extends PGModuleBridgesPrestashopBridge
{
    /**
     * Paygreen constructor.
     * @throws Exception
     */
    public function __construct()
    {
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

        $this->name = 'paygreen';
        $this->displayName = 'PayGreen';
        $this->description = 'PayGreen payment solution.';
        $this->tab = 'payments_gateways';
        $this->version = '3.0.1';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7');
        $this->author = 'Watt Is It';
        $this->need_instance = 1;
        $this->module_key = '0403f32afdc88566f1209530d6f6241c';

        //module compliant with bootstrap
        $this->bootstrap = true;

        parent::__construct();
    }
}
