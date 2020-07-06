<?php
/**
 * 2007-2019 boostmyshop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

if (! defined('_PS_VERSION_')) {
    exit();
}

require_once _PS_MODULE_DIR_ . 'bmserpcloud/ConnectWs.php';

class BMSErpCloud extends Module
{

    protected $_configurationFields;

    public function __construct()
    {
        $this->name = 'bmserpcloud';
        $this->tab = 'administration';
        $this->version = '1.0.1';
        $this->author = 'BoostMyShop';
        $this->need_instance = 0;
        $this->module_key = '151e8d3a39bb0b0ec3f22c22475c4c1c';
        $this->ps_versions_compliancy = array(
            'min' => '1.5.0.0',
            'max' => _PS_VERSION_
        );
        $this->displayName = $this->l('BoostMyShop Connect');
        $this->description = $this->l('BoostMyShop Connect');
        $this->bootstrap = true;

        parent::__construct();
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->_configurationFields = array(
            'bmserpcloud_account_server',
            'bmserpcloud_account_login',
            'bmserpcloud_account_password',
        );
    }

    public function install()
    {
        if (! parent::install()
            || ! $this->registerHook('displayAdminProductsExtra')
            || ! $this->registerHook('displayAdminOrder')
        )
        {
            return false;
        }

        return true;
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            foreach ($this->_configurationFields as $field) {
                $value = Tools::getValue($field);
                $value = (is_array($value) ? implode(',', $value) : $value);
                $value = trim($value, "/");

                if ($value)     //prevent to save empty password
                    Configuration::updateValue($field, $value);

            }
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }

        try
        {
            ConnectWs::initToken();
            $output .= $this->displayConfirmation('Connection with Connect Works !');
        }
        catch(\Exception $ex)
        {
            $output .= $this->displayError('Connection with Connect doesnt work : '.$ex->getMessage());
        }

        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        $fields_form = array();

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Connect account')
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => $this->l('Server URL'),
                    'name' => 'bmserpcloud_account_server',
                    'required' => true,
                    'options' => array(
                        'query' => $this->getServerOptions(),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => $this->l('Login'),
                    'name' => 'bmserpcloud_account_login',
                    'required' => true,
                ),
                array(
                    'type' => 'password',
                    'required' => true,
                    'label' => $this->l('Password'),
                    'name' => 'bmserpcloud_account_password',
                    'required' => true,
                )
            ),
            'submit' => array(
                'title' => $this->l('Save')
            )
        );

        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules')
            )
        );

        $fields_value = array();
        foreach ($this->_configurationFields as $field) {
            $value = Configuration::get($field);
            $fields_value[$field] = (strpos($value, ',') !== false ? explode(',', $value) : $value);
        }
        $helper->fields_value = $fields_value;


        return $helper->generateForm($fields_form);
    }

    public function getServerOptions()
    {
        $options = array();

        $countries = array('FR', 'UK', 'US', 'DE', 'AU');
        foreach($countries as $countryId)
        {
            for($i=1;$i<5;$i++)
            {
                $options[] = array('id_option' => $countryId.$i, 'name' => $countryId.$i);
            }
        }

        return $options;
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $idProduct = isset($params['id_product']) ? $params['id_product'] : Tools::getValue('id_product');
        if ($idProduct)
        {
            $product = new Product((int)$idProduct, $this->context->language->id);
            if($product->hasAttributes() > 0) {
                $attributeIds =  array_keys($this->getProductAttributeCombinations($product));
            }else {
                $attributeIds = array(0);
            }

            $tpl = Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'bmserpcloud/views/templates/hook/hookProductTab.tpl');
            $tpl->assign('id_product', $idProduct);
            try{
                ConnectWs::initToken();
                $productIds = $this->getProductKey($idProduct, $attributeIds);
                $productIds = implode( ",", $productIds);
                $productDetail = ConnectWs::callWs('erpcloud/product/?externalIds=' . $productIds);
                $productDetail = json_decode($productDetail, true);
                if(!isset($productDetail)){
                    return $this->displayError('This product not found on connect.');
                }
                $tpl->assign('ws_data', $productDetail);
            }
            catch(\Exception $ex)
            {
                return $this->displayError('Connection with Connect doesnt work. Please check configuration: '. $ex->getMessage());
            }

            return $tpl->fetch();
        }
    }

    public function getProductAttributeCombinations($product)
    {
        $data = array();
        $combinations = $product->getAttributeCombinations($this->context->language->id);
        foreach ($combinations as $combination) {
            $data[$combination['id_product_attribute']] = array(
                'group_name' => $combination['group_name'],
                'attribute_name' => $combination['attribute_name']
            );
            $data[] = $combination['id_product_attribute'];
        }

        return $data;
    }

    public function hookDisplayAdminOrder($params)
    {
        $orderId = $params['id_order'];
        if($orderId) {
            $tpl = Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'bmserpcloud/views/templates/hook/hookOrderTab.tpl');
            $tpl->assign(array('order_id' => $orderId));

            try{
                ConnectWs::initToken();
                $order = ConnectWs::callWs('erpcloud/order/' . $orderId);
                $order = json_decode($order, true);
                if(count($order['products'])==0 && count($order['shipments'])==0){
                    return $this->displayError('This order not found in connect.');
                }
                $tpl->assign('products', $order['products']);
                $tpl->assign('shipments', $order['shipments']);
            }
            catch(\Exception $ex)
            {
                return $this->displayError('Connection with Connect doesnt work. Please check configuration: '. $ex->getMessage());
            }

            return $tpl->fetch();
        }
    }

    protected function getProductKey($productId, $attributeIds)
    {
        $ids = array();
        foreach ($attributeIds as $attributeId) {
            $ids[] = str_pad($productId, 6, "0", STR_PAD_LEFT) . '_' . str_pad($attributeId, 6, "0", STR_PAD_LEFT);
        }
        return $ids;
    }

}
