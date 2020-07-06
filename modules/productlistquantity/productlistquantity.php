<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class Productlistquantity
 */
class Productlistquantity extends Module
{

    /**
     *
     */
    public function __construct()
    {
        $this->name = 'productlistquantity';
        $this->tab = 'advertising_marketing';
        $this->version = '1.1.8';
        $this->module_key = '1031ce482fc46b17da47654c58bc492e';
        $this->author = 'Evolutive Group';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Add quantity button on product list');
        $this->description = $this->l('Allow your customer to add quantity to shopping cart directly from product list');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall the module?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * @return bool
     */
    public function install()
    {

        if (!parent::install() || !$this->registerHook('header') || !$this->registerHook('displayProductListReviews')) {
            Tools::clearSmartyCache();
            return false;
        }
        Tools::clearSmartyCache();
        return true;
    }

    /**
     * @return bool
     */
    public function disable($forceAll = false)
    {
        Tools::clearSmartyCache();
        parent::disable($forceAll = false);
    }
    
    public function enable($forceAll = false)
    {
        Tools::clearSmartyCache();
        parent::enable($forceAll = false);
    }
     
    
    public function uninstall()
    {
        Tools::clearSmartyCache();
        return parent::uninstall();
    }

    /**
     * @param $params
     */
    public function hookDisplayProductListReviews($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.0', '>=') === true) {
            $link = $this->context->link->getPageLink('cart');
            $this->context->smarty->assign(
                array(
                    'product' => $params['product'],
                    'static_token' => Tools::getToken(false),
                    'page_cart' => $link,
                    'is_stock_management' => Configuration::get('PS_STOCK_MANAGEMENT'),
                )
            );
            return $this->display(__FILE__, 'hookDisplayProductListReviews.tpl');
        }
    }

    public function hookHeader($params)
    {
        $this->context->controller->addJS($this->_path . 'views/js/quantity.js');
        $this->context->controller->addCSS($this->_path . 'views/css/quantity.css');
        $this->context->controller->addJqueryPlugin('fancybox');
        $version = '';
        if (version_compare(_PS_VERSION_, '1.7.0', '>=') === true) {
            $version = "last";
            $error_stock = $this->l('There are not enough products in stock.');
            $error_quantity = $this->l('You must add');
            $min_quantity = $this->l('minimum quantity');
            Media::addJsDef(array('stock' => $error_stock, 'error_min' => $min_quantity, 'error_quantity' => $error_quantity));
        }
        Media::addJsDef(array('version' => $version));
    }
}
