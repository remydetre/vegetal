<?php
/**
* 2007-2019 PrestaShop
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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . '/psrecaptcha/classes/reCaptchaGetExecuteClass.php';

class Psrecaptcha extends Module
{
  
    public $hookList17 = array(
        'actionAuthenticationBefore',
        'actionObjectCustomerAddBefore',
        'actionObjectCustomerThreadUpdateBefore',
        'displayFooter'
    );

    public $hookList16 = array(
        'actionBeforeAuthentication',
        'actionObjectCustomerAddBefore',
        'actionObjectCustomerThreadUpdateBefore',
        'displayFooter'
    );

    private $configuration = array(
        "RECAPTCHA_SITEKEY" => "",
        "RECAPTCHA_SECRETKEY" => "",
        "RECAPTCHA_TYPE" => "1",
        "RECAPTCHA_ACTIVE" => "0",
        "RECAPTCHA_REGISTRATIONFORM" => "1",
        "RECAPTCHA_LOGINFORM" => "1",
        "RECAPTCHA_CONTACTFORM" => "1",
        "RECAPTCHA_NUMBERLOGINFAIL" => "3",
        "RECAPTCHA_THEME" => "1",
        "RECAPTCHA_SIZE" => "1",
        "RECAPTCHA_LANGUAGE" => "1",
        "RECAPTCHA_IPADDRESSNAME" => "",
        "RECAPTCHA_IPADDRESS" => "",
        "RECAPTCHA-WHITELIST" => "",
        "RECAPTCHA-BLACKLIST" => "",
    );

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->name = 'psrecaptcha';
        $this->tab = 'administration';
        $this->version = '1.0.3';
        $this->author = 'PrestaShop';
        $this->bootstrap = true;

        $this->module_key = 'dfeaab947cb282cf95f70b26f6e1733d';

        parent::__construct();

        $this->displayName = $this->l('reCaptcha - Google anti-spam');
        $this->description = $this->l('This module allows you to prevent and secure your store from robot spams and abuses.');

        // Settings paths
        $this->js_path = $this->_path.'views/js/';
        $this->css_path = $this->_path.'views/css/';
        $this->img_path = $this->_path.'views/img/';
        $this->docs_path = $this->_path.'docs/';
        $this->logo_path = $this->_path.'logo.png';
        $this->module_path = $this->_path;
        $this->ps_version = (bool)version_compare(_PS_VERSION_, '1.7', '>=');

        // Confirm uninstall
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * install
     *
     * @return void
     */
    public function install()
    {
        $this->setDefaultConfig();
        reCaptchaGetExecuteClass::getOrCreateHook();

        // Install default's configuration in DB
        if ($this->ps_version) {
            $hook = $this->hookList17;
        } else {
            $hook = $this->hookList16;
        }

        if (parent::install() &&
            $this->installTab() &&
            $this->registerHook($hook)) {
                return true;
        } else {
            $this->_errors[] = $this->l('There was an error during the installation.');
            return false;
        }
    }

    /**
     * uninstall
     * Uninstall default's Configuration in DB
     *
     * @return bool
     */
    public function uninstall()
    {
        Configuration::deleteByName('RECAPTCHA_SITEKEY');
        Configuration::deleteByName('RECAPTCHA_SECRETKEY');
        Configuration::deleteByName('RECAPTCHA_TYPE');
        Configuration::deleteByName('RECAPTCHA_ACTIVE');
        Configuration::deleteByName('RECAPTCHA_REGISTRATIONFORM');
        Configuration::deleteByName('RECAPTCHA_LOGINFORM');
        Configuration::deleteByName('RECAPTCHA_CONTACTFORM');
        Configuration::deleteByName('RECAPTCHA_NUMBERLOGINFAIL');
        Configuration::deleteByName('RECAPTCHA_THEME');
        Configuration::deleteByName('RECAPTCHA_SIZE');
        Configuration::deleteByName('RECAPTCHA_LANGUAGE');
        Configuration::deleteByName('RECAPTCHA_IPADDRESSNAME');
        Configuration::deleteByName('RECAPTCHA_IPADDRESS');
        Configuration::deleteByName('RECAPTCHA-WHITELIST');
        Configuration::deleteByName('RECAPTCHA-BLACKLIST');

        if (parent::uninstall() &&
        $this->uninstallTab()) {
            return true;
        } else {
            $this->_errors[] = $this->l('There was an error during the uninstallation.');
            return false;
        }
    }

    /**
     * installTab
     * This method is often use to create an ajax controller
     *
     * @return array
     */
    public function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminPsRecaptcha';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->name;
        }
        $tab->id_parent = -1;
        $tab->module = $this->name;
        return $tab->add();
    }

    /**
     * uninstallTab
     *
     * @return bool
     */
    public function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminPsRecaptcha');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            if (Validate::isLoadedObject($tab)) {
                return ($tab->delete());
            } else {
                $return = false;
            }
        } else {
            $return = true;
        }
        return $return;
    }

    /**
     * loadAsset
     * load dependencies in the configuration of the module
     *
     * @return void
     */
    public function loadAsset()
    {
        // Load CSS
        $css = array(
            $this->css_path.'menu.css',
            $this->css_path.'faq.css',
            $this->css_path.'back.css',
            $this->css_path.'fontawesome-all.min.css',
            $this->css_path.'sweetalert2.min.css',
        );

        $this->context->controller->addCSS($css, 'all');

        // Load JS
        $jss = array(
            $this->js_path.'vue.min.js',
            $this->js_path.'sweetalert2.min.js',
            $this->js_path.'menu.js',
            $this->js_path.'faq.js',
            $this->js_path.'recaptcha_back.js',
            $this->js_path.'faq.js',
        );
        // $this->context->controller->addJqueryPlugin('colorpicker');
        $this->context->controller->addJS($jss);
    }

    /**
     * loadFaq
     *
     * @return object
     */
    public function loadFaq()
    {
        include_once('classes/APIFAQClass.php');
        $api = new APIFAQ();
        $faq = $api->getData($this->module_key, $this->version);

        return $faq;
    }

    /**
     * getContent
     * Load the configuration form
     *
     * @return string
     */
    public function getContent()
    {
        // $this->setDefaultConfig();
        if ($this->ps_version) {
            $params = array('configure' => $this->name);
            $moduleAdminLink = $this->context->link->getAdminLink('AdminModules', true, false, $params);
        } else {
            $moduleAdminLink = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&module_name='.$this->name;
        }

        $recaptcha_controller = $this->context->link->getAdminLink('AdminPsRecaptcha', true);

        Media::addJsDef(
            array('recaptcha_controller' => $recaptcha_controller,
                'psrecap_PS_succes_deleted' => $this->l('IP Address has been succely deleted!'),
                'psrecap_PS_succes_added' => $this->l('IP Address has been succely added!'),
                'psrecap_PS_error_added' => $this->l('Wrong format IP Address!')
            )
        );

        $this->loadAsset(); // load js and css

        $id_lang = $this->context->language->id;
        $iso_lang = Language::getIsoById($id_lang);

        // get readme
        $doc = $this->docs_path.'readme_en.pdf';
        if ($iso_lang == "fr") {
            $doc = $this->docs_path.'readme_fr.pdf';
        }

        // get current page
        $currentPage = 'configuration';
        $page = Tools::getValue('page');
        
        if (!empty($page)) {
            $currentPage = $page;
        }

        $faq = $this->loadFaq(); // load faq from addons api

        // Translatable JS content
        $this->context->smarty->assign(array(
            'psrecap_SA_sucess_title' => $this->l('Great'),
            'psrecap_SA_sucess_message' => $this->l('Configuration saved!'))
        );

        // assign var to smarty
        $this->context->smarty->assign(array(
            'module_name' => $this->name,
            'module_version' => $this->version,
            'moduleAdminLink' => $moduleAdminLink,
            'module_display' => $this->displayName,
            'apifaq' => $faq,
            'img_path' => $this->img_path,
            'recaptcha_whitelist' => $this->getRecaptchaWhiteList(),
            'recaptcha_config' => $this->getConfig(),
            'doc' => $doc,
            'logo_path' => $this->logo_path,
            'languages' => $this->context->controller->getLanguages(),
            'defaultFormLanguage' => (int) $this->context->employee->id_lang,
            'currentPage' => $currentPage,
            'ps_base_dir' => Tools::getHttpHost(true),
            'ps_version' => _PS_VERSION_,
            'isPs17' => $this->ps_version,
        ));

        return $this->context->smarty->fetch($this->local_path.'views/templates/admin/menu.tpl');
    }

    /**
     * loadGlobalAsset
     * load the javascript who is used in all the backoffice
     *
     * @return void
     */
    public function loadGlobalAsset()
    {
        $jss = array($this->js_path.'favico.js');
        $this->context->controller->addJS($jss);
    }

    /**
     * setDefaultConfig
     *
     * @return void
     */
    public function setDefaultConfig()
    {
        foreach ($this->configuration as $key => $value) {
            Configuration::updateValue($key, $value);
        }
    }

    /**
     * getConfig
     *
     * @return array
     */
    public function getConfig()
    {
        $configurationRow = array(
            "RECAPTCHA_SITEKEY",
            "RECAPTCHA_SECRETKEY",
            "RECAPTCHA_TYPE",
            "RECAPTCHA_ACTIVE",
            "RECAPTCHA_REGISTRATIONFORM",
            "RECAPTCHA_LOGINFORM",
            "RECAPTCHA_CONTACTFORM",
            "RECAPTCHA_NUMBERLOGINFAIL",
            "RECAPTCHA_THEME",
            "RECAPTCHA_SIZE",
            "RECAPTCHA_LANGUAGE",
            "RECAPTCHA_IPADDRESSNAME",
            "RECAPTCHA_IPADDRESS",
            "RECAPTCHA-WHITELIST",
            "RECAPTCHA-BLACKLIST",
        );
        return Configuration::getMultiple($configurationRow);
    }

    /**
     * getRecaptchaWhiteList
     *
     * @return array
     */
    public function getRecaptchaWhiteList()
    {
        return (array)Tools::jsonDecode(Configuration::get('RECAPTCHA-WHITELIST'));
    }

    /**
     * getBlackList
     *
     * @return array
     */
    public function getBlackList()
    {
        return (array)Tools::jsonDecode(Configuration::get('RECAPTCHA-BLACKLIST'));
    }

    //
    // HOOKS - START ---------------------------------------------------------------
    //

    /**
     * hookDisplayFooter
     *
     * @return string
     */
    public function hookDisplayFooter()
    {
        // Check if reCaptcha is enable in Module manager and in module
        if (!$this->isModuleActive()) {
            return;
        }

        // Get whitelist's data and checking if the current_ip (user) is in it
        $current_ip = Tools::getRemoteAddr();
        $whitelistRawData = $this->getRecaptchaWhiteList();
        
        foreach ($whitelistRawData as $key) {
            if ($key === $current_ip) {
                return;
            }
        }

        // Get all reCaptcha's module values in DB
        $recaptchaConfiguration = $this->getConfig();
        $currentController = $this->context->controller->php_self;

        // If the current user didn't reach the login limit yet & ReCaptcha Checkbox is selected, we do not display Recaptcha
        if ($currentController == 'authentication'
            && Tools::getValue("create_account") == "0"
            && $recaptchaConfiguration['RECAPTCHA_TYPE'] == '1') {
            if (!$this->hasUserReachedLoginLimit($current_ip)) {
                return;
            }
            $this->context->smarty->assign("create_account_recaptcha", false);
        }
        $this->context->smarty->assign("create_account_recaptcha", false);

        // On 1.6 , Since the page is reloaded, we display the recaptcha checkbox again if the user failed his account creation attempt
        if (!$this->ps_version) {
            if ($currentController == 'authentication' && Tools::getValue("email_create") == "1") {
                $isEnableOnRegisterForm = false;
                if (Configuration::get('RECAPTCHA_REGISTRATIONFORM') == "1") {
                    $isEnableOnRegisterForm = true;
                }
                $this->context->smarty->assign("create_account_recaptcha", $isEnableOnRegisterForm);
            }
        }

        // Checking if the current page can show the reCaptcha
        // Allowed pages: authentication?back || authentication?create_account || contact
        $active = '';
        if ($this->ps_version) {
            $active = $this->canShowRecaptchaOnCurrentPageOn17($currentController);
        } else {
            $active = $this->canShowRecaptchaOnCurrentPageOn16($currentController);
        }
        if (empty($active) || $active == "0") {
            return;
        }

        $lang = Tools::substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $psrecaptcha_theme = $recaptchaConfiguration['RECAPTCHA_THEME'] == 0 ? 'dark' : 'light';
        $psrecaptcha_size = $recaptchaConfiguration['RECAPTCHA_SIZE'] == 0 ? 'compact' : 'normal';
        $psrecaptcha_lang = $recaptchaConfiguration['RECAPTCHA_LANGUAGE'] == 0 ? $lang : $this->context->language->iso_code;

        $this->context->smarty->assign([
            'RECAPTCHA_SITEKEY' => Configuration::get('RECAPTCHA_SITEKEY'),
            'RECAPTCHA_THEME'=> $psrecaptcha_theme,
            'RECAPTCHA_SIZE'=> $psrecaptcha_size,
            'RECAPTCHA_LANG'=> $psrecaptcha_lang,
            'recap_ps_version' => $this->ps_version
        ]);

        if ($recaptchaConfiguration['RECAPTCHA_TYPE'] == '1') {
            return $this->context->smarty->fetch($this->local_path.'views/templates/hook/reCaptchaV2Checkbox.tpl');
        } else {
            return $this->context->smarty->fetch($this->local_path.'views/templates/hook/reCaptchaV2Invisible.tpl');
        }
    }

    # SIGN IN
    /**
     * HookActionAuthenticationBefore
     * Hook only for PrestaShop v 1.7
     *
     * @return void
     */
    public function hookActionAuthenticationBefore()
    {
        if (!$this->isModuleActive()) {
            return;
        }
        
        $configRecaptcha = Configuration::get('RECAPTCHA_TYPE');
        
        if ($configRecaptcha == "0") {
            $this->verifyRecaptcha();
        }

        $this->userFailLoginAndVerifyIsInBlacklist();
    }

    /**
     * HookActionBeforeAuthentication
     * Hook only for PrestaShop v 1.6
     *
     * @return void
     */
    public function hookActionBeforeAuthentication()
    {
        if (!$this->isModuleActive()) {
            return;
        }

        $configRecaptcha = Configuration::get('RECAPTCHA_TYPE');
        if ($configRecaptcha == "0") {
            $this->verifyRecaptcha();
        }

        $this->userFailLoginAndVerifyIsInBlacklist();
    }

    # SIGN UP
    /**
     * HookActionObjectCustomerAddBefore
     * Magic Hook
     *
     * @return void
     */
    public function hookActionObjectCustomerAddBefore()
    {
        if (!$this->isModuleActive()) {
            return;
        }
        
        if (Context::getContext()->controller->controller_type == 'front') {
            if (!$this->verifyRecaptcha()) {
                // This is a very specific case where google says it's a bot. No error message, just redirecting
                Tools::redirect($this->context->link->getPageLink('authentication'));
            }
        }
    }

    # CONTACT FORM
    /**
     * HookActionObjectCustomerThreadAddBefore
     * Magic hook
     *
     * @return void
     */
    public function hookActionObjectCustomerThreadUpdateBefore()
    {
        if (!$this->isModuleActive()) {
            return;
        }

        if (Context::getContext()->controller->controller_type == 'front') {
            if (!$this->verifyRecaptcha()) {
                // This is a very specific case where google says it's a bot. No error message, just redirecting
                Tools::redirect($this->context->link->getPageLink('contact'));
            }
        }
    }

    //
    // FUNCTIONS - START ------------------------------------------------------------------------
    //

    /**
     * CanShowRecaptchaOnCurrentPageOn17
     *
     * @param  string $currentController
     *
     * @return void
     */
    public function canShowRecaptchaOnCurrentPageOn17($currentController)
    {
        $active = '0';
        switch ($currentController) {
            case 'authentication':
                if (Tools::getValue('back')) {
                    $active = Configuration::get('RECAPTCHA_LOGINFORM');
                } else if (Tools::getValue('create_account')) {
                    $active = Configuration::get('RECAPTCHA_REGISTRATIONFORM');
                }
                break;

            case 'contact':
                $active = Configuration::get('RECAPTCHA_CONTACTFORM');
                break;

            default:
                $active = '0';
                break;
        }

        return $active;
    }

    /**
     * CanShowRecaptchaOnCurrentPageOn16
     *
     * @param  string $currentController
     *
     * @return void
     */
    public function canShowRecaptchaOnCurrentPageOn16($currentController)
    {
        switch ($currentController) {
            case 'authentication':
                if (!Tools::getValue('email_create')) {
                    $active = Configuration::get('RECAPTCHA_LOGINFORM');
                } else if (Tools::getValue('email_create')) {
                    $active = Configuration::get('RECAPTCHA_REGISTRATIONFORM');
                }
                break;

            case 'contact':
                $active = Configuration::get('RECAPTCHA_CONTACTFORM');
                break;

            default:
                $active = '0';
                break;
        }

        return $active;
    }

    /**
     * UserFailLoginAndVerifyIsInBlacklist
     *
     * @return void
     */
    public function userFailLoginAndVerifyIsInBlacklist()
    {
        $UserIP = Tools::getRemoteAddr();
        $login = Tools::getValue('email');
        $pwd = Tools::getValue('password');
        $customer = new Customer();
        $authentication = $customer->getByEmail($login, $pwd);

        if (!$authentication || !$customer->id || $customer->is_guest) {
            $this->addOrUpdateToBlackList($UserIP);
        } else {
            $this->removeFromBlackList($UserIP);
        }
        $this->verifyRecaptcha();
    }

    /**
     * VerifyRecaptcha
     *
     * @return bool
     */
    public function verifyRecaptcha()
    {
        require_once 'recaptcha/src/autoload.php';

        $response = Tools::getValue('g-recaptcha-response');

        if (empty($response)) {
            return false;
        }

        $RECAPTCHA_SECRETEKEY = Configuration::get('RECAPTCHA_SECRETKEY');
        $recaptcha = new \ReCaptcha\ReCaptcha($RECAPTCHA_SECRETEKEY);
        $resp = $recaptcha->setExpectedHostname(Tools::getServerName())
            ->verify($response, Tools::getRemoteAddr());

        if (!$resp->isSuccess()) {
            $errors = $resp->getErrorCodes();
            return false;
        }
        return true;
    }

    /**
     * AddOrUpdateToBlackList
     *
     * @param  string $userIP
     *
     * @return void
     */
    private function addOrUpdateToBlackList($userIP)
    {
        $getBlackList = $this->getBlackList();

        if ($this->isUserAlreadyBlackListed($userIP, $getBlackList)) {
            $getBlackList[$userIP] += 1;
        } else {
            $getBlackList[$userIP] = 1;
        }

        Configuration::updateValue('RECAPTCHA-BLACKLIST', Tools::jsonEncode($getBlackList));
    }

    /**
     * RemoveFromBlackList
     *
     * @param  string $userIP
     *
     * @return array
     */
    private function removeFromBlackList($userIP)
    {
        $getBlackList = $this->getBlackList();

        if ($this->isUserAlreadyBlackListed($userIP, $getBlackList)) {
            unset($getBlackList[$userIP]);
            Configuration::updateValue('RECAPTCHA-BLACKLIST', Tools::jsonEncode($getBlackList));
        }
    }

    /**
     * IsUserAlreadyBlackListed
     *
     * @param  string $userIP
     * @param  array $getBlackList
     *
     * @return void
     */
    private function isUserAlreadyBlackListed($userIP, $getBlackList)
    {
        return array_key_exists($userIP, $getBlackList);
    }

    /**
     * HasUserReachedLoginLimit
     *
     * @param  string $userIP
     *
     * @return bool
     */
    private function hasUserReachedLoginLimit($userIP)
    {
        $getBlackList = $this->getBlackList();
        $maxNbrLogin = Configuration::get('RECAPTCHA_NUMBERLOGINFAIL');

        if ($maxNbrLogin == 0) {
            return true;
        }

        if ($this->isUserAlreadyBlackListed($userIP, $getBlackList) && $getBlackList[$userIP] > $maxNbrLogin) {
            return true;
        }

        return false;
    }

    /**
     * Is the module active and totally configured
     *
     * @return bool
     */
    private function isModuleActive()
    {
        if (!$this->active || Configuration::get('RECAPTCHA_ACTIVE') === '0') {
            return false;
        }
        
        // Double verification on if the mandatory parameters are defined
        if (empty(Configuration::get('RECAPTCHA_SITEKEY')) || 
            empty(Configuration::get('RECAPTCHA_SECRETKEY'))) {
            return false;
        }

        return true;
    }
}

