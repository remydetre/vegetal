<?php
/**
 * 2007-2017 PrestaShop
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!class_exists('TopBannerClass')) {
    include_once(dirname(__FILE__) . '/classes/TopBannerClass.php');
}

if (!class_exists('TopBannerLangClass')) {
    include_once(dirname(__FILE__) . '/classes/TopBannerLangClass.php');
}

class Topbanner extends Module
{

    protected $config_form = false;
    private $bannerTypes = array();
    private $bannerSubTypes = array();
    private $fontsCss = array(
        'https://fonts.googleapis.com/css?family=Roboto', // font-family: 'Roboto', sans-serif;
        'https://fonts.googleapis.com/css?family=Hind', // font-family: 'Hind', sans-serif;
        'https://fonts.googleapis.com/css?family=Maven+Pro', // font-family: 'Maven Pro', sans-serif;
        'https://fonts.googleapis.com/css?family=Noto+Serif', // font-family: 'Noto Serif', serif;
        'https://fonts.googleapis.com/css?family=Bitter', // font-family: 'Bitter', serif;
        'https://fonts.googleapis.com/css?family=Forum', // font-family: 'Forum', serif;
    );
    private $fonts = array(1 => 'Roboto', 2 => 'Hind', 3 => 'Maven Pro', 4 => 'Noto Serif', 5 => 'Bitter', 6 => 'Forum');

    protected $front_controller = null;

    public function __construct()
    {
        $this->name = 'topbanner';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;
        $this->module_key = '846d23693276021d1cc5962a2683baf2';
        $this->author_address = '0x64aa3c1e4034d07015f639b0e171b0d7b27d01aa';

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Promo / Top Banner');
        $this->description = $this->l('This module allows you to active a banner on top of your website pages.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module ?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->js_path = $this->_path . 'views/js/';
        $this->css_path = $this->_path . 'views/css/';

        $this->bannerTypes = array(
            1 => $this->l('Information'),
            2 => $this->l('Free shipping'),
            3 => $this->l('Sales'),
        );

        $this->bannerSubTypes = array(
            1 => $this->l('Cart rule'),
            2 => $this->l('Carrier preference'),
        );

        $this->front_controller = Context::getContext()->link->getModuleLink($this->name, 'FrontAjaxTopbanner', array(), true);

        $this->controller_url = _PS_MODULE_DIR_ . $this->name . '/controllers/admin/AdminTopbannerController.php';
    }

    private function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminTopbanner';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->displayName;
        }
        unset($lang);
        $tab->id_parent = -1;
        $tab->module = $this->name;
        return $tab->add();
    }

    private function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminTopbanner');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            if (Validate::isLoadedObject($tab)) {
                return $tab->delete();
            }
            else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');

        $token = uniqid(rand(), true);
        Configuration::updateValue('TOPBANNER_TOKEN', $token, false, null, 1);
        unset($token);

        $tokenFront = uniqid(rand(), true);
        Configuration::updateValue('TOPBANNER_TOKEN_FRONT', $tokenFront, false, null, 1);
        unset($tokenFront);

        return parent::install() &&
                $this->registerHook('header') &&
                $this->registerHook('displayBanner') &&
                $this->registerHook('actionFrontControllerSetMedia') &&
                $this->installTab();
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallTab();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        if (Module::isInstalled('blockbanner') && Module::isEnabled('blockbanner')) {
            $link = new Link();
            $url = _PS_BASE_URL_ . __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/' . $link->getAdminLink('AdminModules') . '&anchor=blockbanner';

            return $this->displayError($this->l('Please disable the blockbanner module in order for this module to work properly, click on this link to disable the module')
                    . '&nbsp;<a href="' . $url . '">Modules</a>');
        }

        $currentUrl = $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=front_office_features&module_name=' . $this->name;

        if (Tools::getIsset('delete_id_banner')) {
            TopBannerClass::deleteBanner(Tools::getValue('delete_id_banner'));
            if (version_compare(_PS_VERSION_, '1.7.0.0') >= 0) {
                Tools::redirect($currentUrl . '&success=remove');
            } else {
                Tools::redirect(_PS_BASE_URL_ . __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/' . $currentUrl . '&success=remove');
            }
        }

        if (((bool) Tools::isSubmit('submitNewBanner')) == true) {
            $postProcess = $this->postProcess();
            if ($postProcess) {
                if (version_compare(_PS_VERSION_, '1.7.0.0') >= 0) {
                    Tools::redirect($currentUrl . '&success=true');
                } else {
                    Tools::redirect(Tools::getHttpHost(true) . __PS_BASE_URI__ . basename(PS_ADMIN_DIR) . '/' . $currentUrl . '&success=true');
                }
            }
        }

        $assets = $this->_loadAsset();

        if (Tools::getIsset('success')) {
            switch (Tools::getValue('success')) {
                case 'true':
                    $this->context->smarty->assign('success', true);
                    break;
                case 'remove':
                    $this->context->smarty->assign('remove', true);
                    break;
            }
        }

        // API FAQ Update
        include_once('classes/APIFAQClass.php');
        $api = new APIFAQ();
        $faq = $api->getData($this->module_key, $this->version);

        $freeShippingCartRules = $this->_getFreeShippingCartRules();
        $salesCartRules = $this->_getSalesCartRules();
        $shippingFreePrice = ConfigurationCore::get('PS_SHIPPING_FREE_PRICE');

        $banners = TopBannerClass::getAllBanners((int) $this->context->employee->id_lang);
        foreach ($banners as &$banner) {
            $banner['type'] = $this->bannerTypes[$banner['type']];
        }

        $token_ajax = Tools::getAdminTokenLite('AdminTopbanner');
        $controller_url_ajax = 'index.php?controller=AdminTopbanner&' . $token_ajax;

        $current_iso_code = Language::getIsoById((int) $this->context->employee->id_lang);

        $issetBanner = Tools::getIsset('id_banner');
        $this->context->smarty->assign(array(
            'module_dir' => $this->_path,
            'module_version' => $this->version,
            'module_display_name' => $this->displayName,
            'banners' => $banners,
            'bannerTypes' => $this->bannerTypes,
            'bannerSubTypes' => $this->bannerSubTypes,
            'freeCartRules' => $freeShippingCartRules,
            'salesCartRules' => $salesCartRules,
            'shippingFreePrice' => $shippingFreePrice,
            'current_url' => $currentUrl,
            'languages' => $this->context->controller->getLanguages(),
            'defaultFormLanguage' => (int) $this->context->employee->id_lang,
            'fonts' => $this->fonts,
            'ajax_url' => __PS_BASE_URI__ . 'modules/' . $this->name . '/ajax-preview.php',
            'token' => Configuration::get('TOPBANNER_TOKEN', null, null, 1),
            'controller_url_ajax' => $controller_url_ajax,
            'token_ajax' => $token_ajax,
            'psversion' => (version_compare(_PS_VERSION_, '1.7.0.0') >= 0) ? '17' : '16',
            'module_url' => (version_compare(_PS_VERSION_, '1.7.0.0') >= 0) ? $currentUrl : _PS_BASE_URL_ . __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/' . $currentUrl,
            'iso_code' => $current_iso_code,
            'apifaq' => $faq
        ));

        if ($issetBanner) {
            $id_banner_edit = (int) Tools::getValue('id_banner');
            $banner_edit = TopBannerClass::getBannerById($id_banner_edit);

            $banner_langs = TopBannerLangClass::getLangs($id_banner_edit);

            foreach ($banner_langs as $lang) {
                $banner_edit[$lang['name'] . '-' . $lang['id_lang']] = $lang['value'];
            }

            $this->context->smarty->assign(array(
                'id_banner_edit' => $id_banner_edit,
                'banner_edit' => $banner_edit
            ));
        }

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $assets . $output;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $id_banner = null;
        if (Tools::getIsset('topbanner_banner_id')) {
            $id_banner = (int) Tools::getValue('topbanner_banner_id', 0);
        }

        $this->errors = array();

        $languages = $this->context->controller->getLanguages();

        $topBanner = new TopBannerClass($id_banner);

        $topBanner->name = pSQL(Tools::getValue('topbanner_banner_name', ''));
        if ($topBanner->name == '') {
            $this->errors[] = Tools::displayError('Please check the name');
        }

        $topBanner->height = (int) Tools::getValue('topbanner_banner_height', 0);
        if ($topBanner->height == '') {
            $this->errors[] = Tools::displayError('Please check the height');
        }

        $topBanner->background = pSQL(Tools::getValue('topbanner_banner_background', ''));
        if ($topBanner->height == '') {
            $this->errors[] = Tools::displayError('Please check the background color');
        }

        $topBanner->type = (int) Tools::getValue('topbanner_banner_type', 0);
        $topBanner->subtype = (int) Tools::getValue('topbanner_banner_subtype', 0);
        if ($topBanner->type == 0) {
            $this->errors[] = Tools::displayError('Please check the banner type');
        }
        if ($topBanner->type == 2 && $topBanner->subtype == 0) {
            $this->errors[] = Tools::displayError('Please check the banner subtype');
        }

        $topBanner->cartrule = 0;
        if ($topBanner->type == 2) {
            $topBanner->cartrule = ($topBanner->subtype == 1) ? (int) Tools::getValue('topbanner_banner_cartrule', 0) : 0;
        } else if ($topBanner->type == 3) {
            $topBanner->cartrule = (int) Tools::getValue('topbanner_banner_subtype_sales', 0);
        }

        $topBanner->timer = (int) Tools::getValue('topbanner_banner_timer', 0);

        $topBanner->timer_background = pSQL(Tools::getValue('topbanner_banner_timer_background', ''));
        $topBanner->timer_text_color = pSQL(Tools::getValue('topbanner_banner_timer_text_color', ''));
        if ($topBanner->timer > 0 && ($topBanner->timer_background == '' || $topBanner->timer_text_color == '')) {
            $this->errors[] = Tools::displayError('Please check the timer settings');
        }

        $topBanner->text_size = (int) Tools::getValue('topbanner_banner_text_size', 0);
        $topBanner->text_font = pSQL(Tools::getValue('topbanner_banner_text_font', ''));
        $topBanner->text_color = pSQL(Tools::getValue('topbanner_banner_text_color', ''));
        if ($topBanner->text_size == 0 || $topBanner->text_font == '' || $topBanner->text_color == '') {
            $this->errors[] = Tools::displayError('Please check the text settings');
        }

        $topBanner->cta = (int) Tools::getValue('topbanner_banner_cta', 0);

        $topBanner->cta_text_color = pSQL(Tools::getValue('topbanner_banner_cta_text_color', ''));
        $topBanner->cta_background = pSQL(Tools::getValue('topbanner_banner_cta_background', ''));
        if ($topBanner->cta > 0 && ($topBanner->cta_text_color == '' || $topBanner->cta_background == '')) {
            $this->errors[] = Tools::displayError('Please check the cta settings');
        }

        $topBanner->status = (int) Tools::getValue('topbanner_banner_status', 0);

        if ($topBanner->status == 1) {
            TopBannerClass::disableAll();
        }

        if (count($this->errors) == 0 && $topBanner->save()) {
            if (is_null($id_banner)) {
                $id_banner = Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
            }

            foreach ($languages as $language) {
                TopBannerLangClass::saveValue($id_banner, $language['id_lang'], 'text', Tools::getValue('topbanner_banner_text-' . $language['id_lang'], ''));

                TopBannerLangClass::saveValue($id_banner, $language['id_lang'], 'cta_text', Tools::getValue('topbanner_banner_cta_text-' . $language['id_lang'], ''));
                TopBannerLangClass::saveValue($id_banner, $language['id_lang'], 'cta_link', Tools::getValue('topbanner_banner_cta_link-' . $language['id_lang'], ''));

                TopBannerLangClass::saveValue($id_banner, $language['id_lang'], 'text_carrier_empty', Tools::getValue('topbanner_banner_text_carrier_empty-' . $language['id_lang'], ''));
                TopBannerLangClass::saveValue($id_banner, $language['id_lang'], 'text_carrier_between', Tools::getValue('topbanner_banner_text_carrier_between-' . $language['id_lang'], ''));
                TopBannerLangClass::saveValue($id_banner, $language['id_lang'], 'text_carrier_full', Tools::getValue('topbanner_banner_text_carrier_full-' . $language['id_lang'], ''));

                TopBannerLangClass::saveValue($id_banner, $language['id_lang'], 'timer_left_text', Tools::getValue('topbanner_banner_timer_left_text-' . $language['id_lang'], ''));
                TopBannerLangClass::saveValue($id_banner, $language['id_lang'], 'timer_right_text', Tools::getValue('topbanner_banner_timer_right_text-' . $language['id_lang'], ''));
            }
            return true;
        } else {
            $this->context->smarty->assign(array('errors' => $this->errors));
            return false;
        }
    }

    public function hookHeader()
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0') < 0) {
            $this->context->controller->addJS($this->_path . '/views/js/front_common.js');
            $this->context->controller->addJS($this->_path . '/views/js/front16.js');
        }
    }

    public function previewBanner($banner, $iso_lang)
    {
        $id_lang = LanguageCore::getIdByIso($iso_lang);

        $banner = $this->_loadBannerInfos($banner, true, $id_lang);

        $this->smarty->assign(array(
            'banner' => $banner,
            'fontFamily' => $this->fonts[$banner['text_font']],
            'psversion' => '16' // for some reasons ... no need to throw 17
        ));

        return $this->display(__FILE__, 'views/templates/hook/banner-html.tpl');
        ;
    }

    public function hookDisplayBanner()
    {
        if (Module::isInstalled('blockbanner') && Module::isEnabled('blockbanner')) {
            return false;
        }

        $banner = TopBannerClass::getBanner();

        if (!$banner) {
            return false;
        }

        $banner = $this->_loadBannerInfos($banner);

        $this->smarty->assign(array(
            'banner' => $banner,
            'fontFamily' => (isset($this->fonts[$banner['text_font']])) ? $this->fonts[$banner['text_font']] : '',
            'front_controller' => $this->front_controller,
            'token' => Configuration::get('TOPBANNER_TOKEN_FRONT', null, null, 1),
            'psversion' => (version_compare(_PS_VERSION_, '1.7.0.0') >= 0) ? '17' : '16'
        ));

        return $this->display(__FILE__, 'views/templates/hook/display-banner.tpl');
    }

    protected function _loadBannerInfos($banner, $preview = false, $id_lang = null)
    {
        $languageId = 1;
        if ($preview === true) {
            $languageId = $id_lang;
            $langs = TopBannerLangClass::getLangsPreview($banner);
        } else {
            $languageId = $this->context->language->id;
            $langs = TopBannerLangClass::getLangs($banner['id_banner']);
        }

        $banner['text'] = TopBannerLangClass::getValue($languageId, 'text', $langs);

        $banner['timer_left_text'] = TopBannerLangClass::getValue($languageId, 'timer_left_text', $langs);
        $banner['timer_right_text'] = TopBannerLangClass::getValue($languageId, 'timer_right_text', $langs);

        $banner['cta_link'] = TopBannerLangClass::getValue($languageId, 'cta_link', $langs);
        $banner['cta_text'] = TopBannerLangClass::getValue($languageId, 'cta_text', $langs);

        // free shipping > carrier preference
        if ($banner['type'] == 2 && $banner['subtype'] == 2) {
            $shippingFreePrice = ConfigurationCore::get('PS_SHIPPING_FREE_PRICE');

            $cart = $this->context->cart;

            $total = (!is_null($cart)) ? $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS ) : 0;

            if ($total == 0) {
                $banner['text'] = TopBannerLangClass::getValue($languageId, 'text_carrier_empty', $langs);
            } else if ($total < $shippingFreePrice) {
                $remaining = $shippingFreePrice - $total;
                $banner['text'] = str_replace('{{price}}', $remaining, TopBannerLangClass::getValue($languageId, 'text_carrier_between', $langs));
            } else {
                $banner['text'] = TopBannerLangClass::getValue($languageId, 'text_carrier_full', $langs);
            }

            $banner['text'] = str_replace('{{currency}}', $this->context->currency->sign, $banner['text']);
        }

        if ($banner['timer'] == 1) {
            $now = new DateTime();

            $cartRule = new CartRuleCore((int) $banner['cartrule']);
            $cartRuleDateTo = DateTime::createFromFormat('Y-m-d H:i:s', $cartRule->date_to);

            if ($now > $cartRuleDateTo || $cartRule->active == 0) {
                TopBannerClass::disableBanner((int) $banner['id_banner']);
                return false;
            }

            $this->smarty->assign(array(
                'deadline' => $cartRuleDateTo->format('U'),
            ));
        }

        return $banner;
    }

    protected function _loadAsset()
    {
        $css_compatibility = $js_compatibility = $css = array();

        $return = '';

        // Load JS
        $js = array(
            $this->js_path . 'topbanner.js', // custom js
            $this->js_path . 'front_common.js',
            $this->js_path . 'faq.js'
        ); //
        // Jquery is required
        if (method_exists($this->context->controller, 'addJquery')) {
            $this->context->controller->addJquery('2.1.0', $this->js_path);
        }

        $this->context->controller->addJqueryPlugin('colorpicker');
        $this->context->controller->addJqueryPlugin('validate');
        $this->context->controller->addJS($js);

        $this->context->controller->addCss($this->css_path . 'back.css');
        $this->context->controller->addCss($this->css_path . 'faq.css');
        $this->context->controller->addCSS($this->fontsCss);

        $this->context->controller->addCss($this->getProtocol() . 'cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css');
        $this->context->controller->addJs($this->getProtocol() . 'cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js');

        // Clean memory
        unset($js, $css, $js_compatibility, $css_compatibility);

        return $return;
    }

    protected function _getFreeShippingCartRules()
    {
        $date = new DateTime();
        $query = 'SELECT * '
                . 'FROM ' . _DB_PREFIX_ . 'cart_rule cr '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_lang crl ON cr.id_cart_rule = crl.id_cart_rule '
                . 'WHERE cr.active = 1 '
                . 'AND cr.free_shipping = 1 '
                . 'AND cr.date_to >= "' . $date->format('Y-m-d H:i:s') . '" '
                . 'AND crl.id_lang = ' . (int) $this->context->employee->id_lang . ' '
                . 'AND id_customer = 0 '
                . 'AND group_restriction = 0 ';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    protected function _getSalesCartRules()
    {
        $date = new DateTime();
        $query = 'SELECT * '
                . 'FROM ' . _DB_PREFIX_ . 'cart_rule cr '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_lang crl ON cr.id_cart_rule = crl.id_cart_rule '
                . 'WHERE cr.active = 1 '
                . 'AND cr.date_to >= "' . $date->format('Y-m-d H:i:s') . '" '
                . 'AND crl.id_lang = ' . (int) $this->context->employee->id_lang . ' '
                . 'AND id_customer = 0 '
                . 'AND group_restriction = 0 ';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    public function disableAll()
    {
        TopBannerClass::disableAll();
    }

    public function changeState($id_banner, $state)
    {
        TopBannerClass::changeState($id_banner, $state);
    }

    public function hookActionFrontControllerSetMedia()
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0') >= 0) {
            $this->context->controller->registerJavascript(
                'front_common',
                'modules/' . $this->name . '/views/js/front_common.js'
            );
            $this->context->controller->registerJavascript(
                'front17',
                'modules/' . $this->name . '/views/js/front17.js'
            );
        }
    }

    protected function getProtocol() {
        if (isset($_SERVER['HTTPS'])
        && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
        || isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
        && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        return $protocol;
    }
}
