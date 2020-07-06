<?php
/**
* Price increment/Reduction by groups, categories and more
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate
*  @copyright 2018 idnovate
*  @license   See above
*/

class AdminGroupincController extends ModuleAdminController
{
    protected $delete_mode;

    protected $_defaultOrderBy = 'date_add';
    protected $_defaultOrderWay = 'DESC';
    protected $can_add_giconf = true;
    protected $top_elements_in_list = 4;
    protected $orderBy = 'id_product';
    protected $orderWay = 'ASC';

    protected static $meaning_status = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'groupinc_configuration';
        $this->className = 'GroupincConfiguration';
        $this->tabClassName = 'AdminGroupinc';
        $this->lang = false;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowAction('duplicate');
        $this->_orderWay = $this->_defaultOrderWay;
        $this->taxes_included = (Configuration::get('PS_TAX') == '0' ? false : true);
        $this->allow_duplicate = true;

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->context = Context::getContext();

        $this->default_form_language = $this->context->language->id;

        $this->fields_list = array(
            'id_groupinc_configuration' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'filter_key' => 'a!name'
            ),
            'type' => array(
                'title' => $this->l('Type'),
                'callback' => 'getGroupincType',
                'type' => 'select',
                'list' => array(0 => $this->l('Fix'), 1 => $this->l('Percentage'), 2 => $this->l('Fix + Percentage')),
                'filter_key' => 'a!type',
                'align' => 'text-center'
            ),
            'mode' => array(
                'title' => $this->l('Mode'),
                'callback' => 'getGroupincMode',
                'type' => 'select',
                'filter_key' => 'a!mode',
                'list' => array(0 => $this->l('Increment'), 1 => $this->l('Reduction'), 2 => $this->l('Set Fixed price')),
                'align' => 'text-center'
            ),
            'fix' => array(
                'title' => $this->l('Fix'),
                'align' => 'text-center'
            ),
            'percentage' => array(
                'title' => $this->l('Percentage'),
                'align' => 'text-center'
            ),
            'suppliers' => array(
                'title' => $this->l('Supplier(s)'),
                'callback' => 'getSuppliers',
                'align' => 'text-center'
            ),
            'groups' => array(
                'title' => $this->l('Group(s)'),
                'callback' => 'getCustomerGroups',
                'align' => 'text-center'
            ),
            'customers' => array(
                'title' => $this->l('Customers(s)'),
                'callback' => 'getCustomers',
                'align' => 'text-center'
            ),
            'categories' => array(
                'title' => $this->l('Category(s)'),
                'callback' => 'getCategories',
                'align' => 'text-center'
            ),
            'products' => array(
                'title' => $this->l('Products(s)'),
                'callback' => 'getProducts',
                'align' => 'text-center'
            ),
            'countries' => array(
                'title' => $this->l('Country(s)'),
                'callback' => 'getCountries',
                'align' => 'text-center'
            ),
            'priority' => array(
                'title' => $this->l('Priority'),
                'align' => 'text-center'
            ),
            'backoffice' => array(
                'title' => $this->l('Backoffice'),
                'align' => 'text-center',
                'type' => 'select',
                'list' => array(0 => $this->l('No'), 1 => $this->l('Yes')),
                'callback' => 'printBackofficeIcon',
                'filter_key' => 'a!backoffice'
            ),
            'date_to' => array(
                'title' => $this->l('Valid'),
                'align' => 'text-center',
                'callback' => 'printValidIcon',
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'callback' => 'printActiveIcon'
            ),
        );

        if (Shop::isFeatureActive() && (Shop::getContext() == Shop::CONTEXT_ALL || Shop::getContext() == Shop::CONTEXT_GROUP)) {
            $this->can_add_giconf = false;
        }

        if (!Shop::isFeatureActive()) {
            $this->shopLinkType = '';
        } else {
            $this->shopLinkType = 'shop';
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryPlugin(array('typewatch', 'fancybox', 'autocomplete'));

        $this->addJqueryUI('ui.button');
        $this->addJqueryUI('ui.sortable');
        $this->addJqueryUI('ui.droppable');
        $_path = _MODULE_DIR_.$this->module->name;

        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $this->context->controller->addCSS($_path.'/views/css/groupinc.css');
        } else {
            $this->context->controller->addCSS($_path.'/views/css/groupinc_15.css');
        }

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->context->controller->addJS($_path.'/views/js/gi_functions_15.js');
        } else {
            $this->context->controller->addJS($_path.'/views/js/gi_functions.js');
            if ($this->display) {
                $this->context->controller->addJS($_path.'/views/js/tabs.js');
            }
        }
    }

    public function postProcess()
    {
        return parent::postProcess();
    }

    public function initContent()
    {
        if ($this->action == 'select_delete') {
            $this->context->smarty->assign(array(
                'delete_form' => true,
                'url_delete' => htmlentities($_SERVER['REQUEST_URI']),
                'boxes' => $this->boxes,
            ));
        }

        if (!$this->can_add_giconf && !$this->display) {
            $this->informations[] = $this->l('You have to select a shop if you want to create a new configuration.');
        }

        $module = new Groupinc();
        if ($this->action != 'new' && !Tools::isSubmit('updategroupinc_configuration')) {
            if (Tools::isSubmit('submitGroupincModuleGlobalConfig')) {
                $form_values = $this->getGlobalConfigFormValues();
                foreach (array_keys($form_values) as $key) {
                    if ((version_compare(_PS_VERSION_, '1.6', '>=') ? Tools::strpos($key, '[]') > 0 : strpos($key, '[]') > 0)) {
                        $key = Tools::str_replace_once('[]', '', $key);
                        Configuration::updateValue($key, implode(';', Tools::getValue($key)));
                    } else {
                        Configuration::updateValue($key, Tools::getValue($key));
                    }
                }
                $this->content .= $module->displayConfirmation($this->l('Configuration saved successfully.'));
            }
        }

        $this->content .= $this->_createTemplate('admin_translations.tpl')->fetch();

        if (!$this->display) {
            $this->content .= $this->renderGlobalConfigForm();
        }

        if (($id_conf = Tools::getValue('id_groupinc_configuration')) && Tools::getIsset('duplicategroupinc_configuration')) {
            $this->processDuplicate();
        }

        parent::initContent();

        if (!$this->display) {
            if (version_compare(_PS_VERSION_, '1.6', '>=')) {
                $this->context->smarty->assign(array(
                    'this_path'                 => $this->module->getPathUri(),
                    'support_id'                => $module->addons_id_product,
                ));

                $available_iso_codes = array('en', 'es');
                $default_iso_code = 'en';
                $template_iso_suffix = in_array($this->context->language->iso_code, $available_iso_codes) ? $this->context->language->iso_code : $default_iso_code;
                $this->content .= $this->context->smarty->fetch($this->module->getLocalPath().'views/templates/admin/company/information_'.$template_iso_suffix.'.tpl');
            }

            $this->context->smarty->assign(array(
                'content' => $this->content,
                'token' => $this->token,
            ));
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();

        if (!$this->can_add_giconf) {
            unset($this->toolbar_btn['new']);
        }
    }

    public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = null)
    {
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);
    }

    public function initModal()
    {
        parent::initModal();

        $languages = Language::getLanguages(false);
        $translateLinks = array();

        if (version_compare(_PS_VERSION_, '1.7.2.1', '>=')) {
            $module = Module::getInstanceByName($this->module->name);
            $isNewTranslateSystem = $module->isUsingNewTranslationSystem();
            $link = Context::getContext()->link;
            foreach ($languages as $lang) {
                if ($isNewTranslateSystem) {
                    $translateLinks[$lang['iso_code']] = $link->getAdminLink('AdminTranslationSf', true, array(
                        'lang' => $lang['iso_code'],
                        'type' => 'modules',
                        'selected' => $module->name,
                        'locale' => $lang['locale'],
                    ));
                } else {
                    $translateLinks[$lang['iso_code']] = $link->getAdminLink('AdminTranslations', true, array(), array(
                        'type' => 'modules',
                        'module' => $module->name,
                        'lang' => $lang['iso_code'],
                    ));
                }
            }
        }

        $this->context->smarty->assign(array(
            'trad_link' => 'index.php?tab=AdminTranslations&token='.Tools::getAdminTokenLite('AdminTranslations').'&type=modules&module='.$this->module->name.'&lang=',
            'module_languages' => $languages,
            'module_name' => $this->module->name,
            'translateLinks' => $translateLinks,
        ));

        $modal_content = $this->context->smarty->fetch('controllers/modules/modal_translation.tpl');

        $this->modals[] = array(
            'modal_id' => 'moduleTradLangSelect',
            'modal_class' => 'modal-sm',
            'modal_title' => $this->l('Translate this module'),
            'modal_content' => $modal_content
        );
    }

    public function initToolbarTitle()
    {
        parent::initToolbarTitle();

        switch ($this->display) {
            case '':
            case 'list':
                array_pop($this->toolbar_title);
                $this->toolbar_title[] = $this->l('Manage Groupinc Configuration');
                break;
            case 'view':
                if (($giconf = $this->loadObject(true)) && Validate::isLoadedObject($giconf)) {
                    array_pop($this->toolbar_title);
                    $this->toolbar_title[] = sprintf($this->l('Configuration: %s'), $giconf->name);
                }
                break;
            case 'add':
            case 'edit':
                array_pop($this->toolbar_title);
                if (($giconf = $this->loadObject(true)) && Validate::isLoadedObject($giconf)) {
                    $this->toolbar_title[] = sprintf($this->l('Editing Configuration: %s'), $giconf->name);
                } else {
                    $this->toolbar_title[] = $this->l('Creating a new Increment/Reduction configuration:');
                }
                break;
        }
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if (empty($this->display)) {
            $this->page_header_toolbar_btn['desc-module-back'] = array(
                'href' => 'index.php?controller=AdminModules&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back'),
                'icon' => 'process-icon-back'
            );
            $this->page_header_toolbar_btn['desc-module-new'] = array(
                'href' => 'index.php?controller='.$this->tabClassName.'&add'.$this->table.'&token='.Tools::getAdminTokenLite($this->tabClassName),
                'desc' => $this->l('New'),
                'icon' => 'process-icon-new'
            );
            $this->page_header_toolbar_btn['desc-module-reload'] = array(
                'href' => 'index.php?controller='.$this->tabClassName.'&token='.Tools::getAdminTokenLite($this->tabClassName).'&reload=1',
                'desc' => $this->l('Reload'),
                'icon' => 'process-icon-refresh'
            );
            /*$this->page_header_toolbar_btn['desc-module-exportrules'] = array(
                'href' => 'index.php?controller='.$this->tabClassName.'&token='.Tools::getAdminTokenLite($this->tabClassName).'&exportrules=1',
                'desc' => $this->l('Export Rules'),
                'icon' => 'process-icon-export'
            );*/
            $this->page_header_toolbar_btn['desc-module-translate'] = array(
                'href' => '#',
                'desc' => $this->l('Translate'),
                'modal_target' => '#moduleTradLangSelect',
                'icon' => 'process-icon-flag'
            );
            $this->page_header_toolbar_btn['desc-module-hook'] = array(
                'href' => 'index.php?tab=AdminModulesPositions&token='.Tools::getAdminTokenLite('AdminModulesPositions').'&show_modules='.Module::getModuleIdByName('groupinc'),
                'desc' => $this->l('Manage hooks'),
                'icon' => 'process-icon-anchor'
            );
        }

        if (!$this->can_add_giconf) {
            unset($this->page_header_toolbar_btn['desc-module-new']);
        }
    }

    public function initProcess()
    {
        parent::initProcess();

        if (Tools::isSubmit('changeBackofficeVal') && $this->id_object) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'change_backoffice_val';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
    }

    public function renderList()
    {
        if ((Tools::isSubmit('submitBulkdelete'.$this->table) || Tools::isSubmit('delete'.$this->table)) && $this->tabAccess['delete'] === '1') {
            $this->tpl_list_vars = array(
                'delete_groupincconf' => true,
                'REQUEST_URI' => $_SERVER['REQUEST_URI'],
                'POST' => $_POST
            );
        }

        return parent::renderList();
    }

    public function renderForm()
    {

        if (!($giconf = $this->loadObject(true))) {
            return;
        }

        $types = $this->getGroupincTypes();
        $modes = $this->getGroupincModes();
        $price_calc_options = $this->getGroupincPriceOptions(true);
        $price_options = $this->getGroupincPriceOptions();
        $id_lang = 0;
        $id_shop = 0;
        $categories = array();

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
        	$id_lang = (int)$this->context->cookie->id_lang;
        	$id_shop = (int)$this->context->cookie->id_shop;
        	$currencies = Currency::getCurrencies(false, true);
        } else {
        	$id_lang = (int)$this->context->language->id;
        	$id_shop = (int)$this->context->shop->id;
			if (Shop::isFeatureActive()) {
                $currencies = Currency::getCurrenciesByIdShop($this->context->shop->id);
            } else {
                $currencies = Currency::getCurrencies(false, true);
            }
        }

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $categories = array_merge($categories, Category::getCategories((int)($this->context->cookie->id_lang), false, false, '', 'ORDER BY cl.`name` ASC'));
        }

        $groups = Group::getGroups($id_lang, true);
		$customers = array(); //Customer::getCustomers(true);
        $products = array();
		$countries = Country::getCountries($id_lang);
		$zones = Zone::getZones();
		$manufacturers = Manufacturer::getManufacturers(false, $id_lang, false);
		$suppliers = Supplier::getSuppliers(false, $id_lang, false);
		$num_products = $this->getNumProducts($id_lang, true, true);
		$languages = Language::getLanguages(false, $id_shop);

        $this->multiple_fieldsets = true;
        $this->default_form_language = $this->context->language->id;

        $this->fields_form[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Increment/Reduction Configuration'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'class' => 't',
                    'col' => '5',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('Enable or Disable this configuration'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'required' => true,
                    'col' => '5',
                    'desc' => $this->l('Invalid characters:').' !&lt;&gt;,;?=+()@#"°{}_$%:',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Mode'),
                    'name' => 'mode',
                    'required' => true,
                    'col' => '5',
                    'options' => array(
                        'query' => $modes,
                        'id' => 'id',
                        'name' => 'name',
                        'default' => array(
                            'value' => '',
                            'label' => $this->l('-- Choose --')
                        )
                    ),
                    'desc' => $this->l('Select if you want to configure an Increment, a Reduction or a Fixed Price'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Type'),
                    'name' => 'type',
                    'required' => true,
                    'col' => '5',
                    'options' => array(
                        'query' => $types,
                        'id' => 'id',
                        'name' => 'name',
                        'default' => array(
                            'value' => '',
                            'label' => $this->l('-- Choose --')
                        )
                    ),
                    'desc' => $this->l('Select the type that you want to apply, percentage, fix import or a combination of fix + percentage'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Price percentage calculation'),
                    'name' => 'price_calculation',
                    'required' => true,
                    'col' => '5',
                    'class' => 'price_calculation',
                    'options' => array(
                        'query' => $price_calc_options,
                        'id' => 'id',
                        'name' => 'name',
                        'default' => array(
                            'value' => '',
                            'label' => $this->l('-- Choose --')
                        )
                    ),
                    'desc' => $this->l('Select the price from where you want to calculate the percentage of the Increment/Reduction'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Price to apply the Increment/Reduction'),
                    'name' => 'price_application',
                    'required' => true,
                    'col' => '5',
                    'options' => array(
                        'query' => $price_options,
                        'id' => 'id',
                        'name' => 'name',
                        'default' => array(
                            'value' => '',
                            'label' => $this->l('-- Choose --')
                        )
                    ),
                    'desc' => $this->l('Select the price where you want to apply the Increment/Reduction. The amount (percentage or fix) will be added, subtract or established to this price'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Fix'),
                    'name' => 'fix',
                    'col' => '5',
                    'desc' => sprintf($this->l('Fix Increment/Reduction (by default currency) %s'), ($this->taxes_included) ? $this->l('taxes included') : $this->l('without taxes')),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Percentage'),
                    'name' => 'percentage',
                    'col' => '1',
                    'suffix' => '%',
                    'desc' => $this->l('Percentage amount'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Minimum result price'),
                    'name' => 'min_result_price',
                    'col' => '5',
                    'default' => '0',
                    'desc' => $this->l('Set a minimum result price if the calculated price of product is lower than this. If this value is 0 there\'s no minimum result price'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Maximum result price'),
                    'name' => 'max_result_price',
                    'col' => '5',
                    'default' => '0',
                    'desc' => $this->l('Set a maximum result price if the calculated price of product is greater than this. If this value is 0 there\'s no maximum result price'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product quantity units'),
                    'name' => 'product_qty',
                    'col' => '4',
                    'default' => '0',
                    'desc' => sprintf($this->l('Minimum quantity of units of products in cart')),
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Show Reduction as discount'),
                    'name' => 'show_as_discount',
                    'class' => 't',
                    'col' => '5',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'show_as_discount_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'show_as_discount_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('Show the reduction in the price like a discount (specific price). Only enable it if you have configured a Reduction'),
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Show decimals in percentages'),
                    'name' => 'show_decimals',
                    'class' => 't',
                    'col' => '5',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'show_decimals_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'show_decimals_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('If the calculation of the reduction percentage has decimals, you can decide if it will be displayed or not.'),
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Show "On Sale" icon in Products'),
                    'name' => 'show_on_sale',
                    'class' => 't',
                    'col' => '5',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'show_on_sale_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'show_on_sale_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('Display the "On Sale" icon on the product page, and in the text found within the product listing'),
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Show in "Prices Drop" section'),
                    'name' => 'show_prices_drop',
                    'class' => 't',
                    'col' => '5',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'show_prices_drop_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'show_prices_drop_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('Display the products affected of this configuration in the "Prices Drop" (Sales) section'),
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Skip other discounts'),
                    'name' => 'skip_discounts',
                    'class' => 't',
                    'col' => '5',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'skip_discounts_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'skip_discounts_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('If the product has a previous discount, the rule will not be applied, will be skipped'),
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Override discounts'),
                    'name' => 'override_discounts',
                    'class' => 't',
                    'col' => '5',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'override_discounts_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'override_discounts_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('If the product has a previous discount, the rule will be applied and the previous discount will be ommited (not deleted)'),
                ),

                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Apply only one condition?'),
                    'name' => 'first_condition',
                    'class' => 't',
                    'col' => '5',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'first_condition_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'first_condition_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('If you enable this option, when this rule is applied, the other rules with low priority will be ommited'),
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Use this configuration in Admin (Backoffice)'),
                    'name' => 'backoffice',
                    'class' => 't',
                    'col' => '5',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'backoffice_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'backoffice_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('Use this configuration in Prestashop Backoffice to show the prices of products modified with this conditions'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Priority'),
                    'name' => 'priority',
                    'default' => '1',
                    'col' => '5',
                    'desc' => $this->l('Set priority when 2 or more configurations overlaps. Configuration with less number here will have more priority'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'type' => 'submit',
            ),
        );

        $this->fields_form[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Price, Stock and Weight filters'),
                'icon' => 'icon-edit'
            ),
            'input' => array(
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Filter by prices'),
                    'name' => 'filter_prices',
                    'class' => 't',
                    'col' => '5',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'filter_prices_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'filter_prices_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('Enable or Disable the filter by prices range. For example: Appy the rule to the products with Retail price with taxes between 10€ (minimum) and 50€ (maximum)'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Price from calculate the minimum and maximum'),
                    'name' => 'threshold_price',
                    'class' => 'toggle_filter_prices',
                    'col' => '5',
                    'options' => array(
                        'query' => $price_options,
                        'id' => 'id',
                        'name' => 'name',
                        'default' => array(
                            'value' => '',
                            'label' => $this->l('-- Choose --')
                        )
                    ),
                    'desc' => $this->l('Select the price which the threshold will be calculated. For example: Appy the rule to the products that have a Retail price with taxes prices between 10€ (minimum) and 50€ (maximum)'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Threshold minimum price'),
                    'name' => 'threshold_min_price',
                    'class' => 'toggle_filter_prices',
                    'col' => '5',
                    'default' => '0',
                    'desc' => sprintf($this->l('Minimum price to set a less Threshold price (by default currency with %s) to apply the rule. If this value is 0 there\'s no minimum limit'), ($this->taxes_included) ? $this->l('taxes included') : $this->l('without taxes')),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Threshold maximum price'),
                    'name' => 'threshold_max_price',
                    'class' => 'toggle_filter_prices',
                    'col' => '5',
                    'default' => '0',
                    'desc' => sprintf($this->l('Maximum price to set a top Threshold price (by default currency with %s) to apply the rule. If this value is 0 there\'s no maximum limit'), ($this->taxes_included) ? $this->l('taxes included') : $this->l('without taxes')),
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Filter by stock'),
                    'name' => 'filter_stock',
                    'class' => 't',
                    'col' => '5',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'filter_stock_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'filter_stock_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('Enable or Disable the filter by stock range. For example: Appy the rule to the products with stock quantity between 50 and 100'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Products with stock from'),
                    'name' => 'min_stock',
                    'class' => 'toggle_filter_stock',
                    'col' => '5',
                    'default' => '0',
                    'desc' => $this->l('Enable rule to products with stock from'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Products with stock until'),
                    'name' => 'max_stock',
                    'class' => 'toggle_filter_stock',
                    'col' => '5',
                    'default' => '0',
                    'desc' => $this->l('Enable rule to products with stock until'),
                ),
                array(
                    'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                    'label' => $this->l('Filter by weight'),
                    'name' => 'filter_weight',
                    'class' => 't',
                    'col' => '5',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'filter_weight_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'filter_weight_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('Enable or Disable the filter by weight range. For example: Appy the rule to the products with weight between 0 and 6'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Products with weight from'),
                    'name' => 'min_weight',
                    'class' => 'toggle_filter_weight',
                    'col' => '5',
                    'default' => '0',
                    'desc' => $this->l('Enable rule to products with weight from'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Products with weight until'),
                    'name' => 'max_weight',
                    'class' => 'toggle_filter_weight',
                    'col' => '5',
                    'default' => '0',
                    'desc' => $this->l('Enable rule to products with weight until'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'type' => 'submit',
            ),
        );

        $this->fields_form[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Store filters and conditions'),
                'icon' => 'icon-globe'
            ),
            'input' => array(
                 array(
                        'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                        'label' => $this->l('Store filters'),
                        'name' => 'filter_store',
                        'class' => 't',
                        'col' => '5',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'filter_store_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'filter_store_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'desc' => $this->l('Enable or Disable the store filters: Categories, Products, Customers, Groups, Zones, Countries, Manufacturers, Suppliers, Languages, Currencies, Attributes and Features'),
                    ),
                ),
            );

        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $selected_categories = array();
            if ($giconf->categories != '') {
                if (@unserialize($giconf->categories) !== false) {
                    $selected_categories = unserialize($giconf->categories);
                } else {
                    $selected_categories = explode(';', $giconf->categories);
                }
            }

            $categories_form_array = array(
               'type'  => 'categories',
               'label' => $this->l('Select Category(s)'),
               'multiple' => true,
               'name'  => 'categories',
               'col' => '5',
               'tree'  => array(
                    'id' => 'id_category',
                    'use_checkbox' => true,
                    'selected_categories' => $selected_categories,
                    ),
                'desc' => $this->l('Select the Category(es) where the rule will be applied. If you don\'t select any value, the rule will be applied to all Categories'),
            );
        } else {
            $categories_form_array = array(
                'type' => 'select',
                'label' => $this->l('Select Category(s)'),
                'class' => 'multiple_select',
                'name'  => 'categories[]',
                'multiple' => true,
                'required' => false,
                'col' => '2',
                'options' => array(
                    'query' => $categories,
                    'id' => 'id_category',
                    'name' => 'name'
                ),
                'desc' => $this->l('Select the Category(es) where the rule will be applied. If you don\'t select any value, the rule will be applied to all Categories'),
            );
        }

        array_push($this->fields_form[2]['form']['input'], $categories_form_array);

        $render_form_end = array();

        if (Configuration::get('GROUPINC_USE_PRODUCTS')) {
            $render_form_end[] =
                array(
                    'type' => 'select',
                    'label' => $this->l('Select Product(s)'),
                    'name' => 'products[]',
                    'class' => 'multiple_select',
                    'multiple' => true,
                    'required' => false,
                    'col' => '5',
                    'options' => array(
                        'query' => $products,
                        'id' => 'id_product',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Select the Product(s) where the rule will be applied. If you don\'t select any value, the rule will be applied to all Products'),
                );
        }

        $render_form_end[] =
            array(
                'type' => 'select',
                'label' => $this->l('Select Currency(es)'),
                'name' => 'currencies[]',
                'class' => 'multiple_select',
                'multiple' => true,
                'required' => false,
                'col' => '5',
                'options' => array(
                    'query' => $currencies,
                    'id' => 'id_currency',
                    'name' => 'name'
                ),
                'desc' => $this->l('Currency(es) to apply this configuration'),
            );

        $render_form_end[] =
            array(
                'type' => 'select',
                'label' => $this->l('Select Language(s)'),
                'name' => 'languages[]',
                'class' => 'multiple_select',
                'multiple' => true,
                'required' => false,
                'col' => '5',
                'options' => array(
                    'query' => $languages,
                    'id' => 'id_lang',
                    'name' => 'name'
                ),
                'desc' => $this->l('Select the Language(s) where the rule will be applied. If you don\'t select any value, the rule will be applied to all Languages'),
            );
        $render_form_end[] =
            array(
                'type' => 'select',
                'label' => $this->l('Select Customer group(s)'),
                'name' => 'groups[]',
                'class' => 'multiple_select',
                'multiple' => true,
                'required' => false,
                'col' => '5',
                'options' => array(
                    'query' => $groups,
                    'id' => 'id_group',
                    'name' => 'name'
                ),
                'desc' => $this->l('Select the Customer Group(s) where the rule will be applied. If you don\'t select any value, the rule will be applied to all Groups'),
            );

        if (Configuration::get('GROUPINC_USE_CUSTOMERS')) {
            $render_form_end[] =
                array(
                    'type' => 'select',
                    'label' => $this->l('Select Customer(s)'),
                    'name' => 'customers[]',
                    'class' => 'multiple_select',
                    'multiple' => true,
                    'required' => false,
                    'col' => '5',
                    'options' => array(
                        'query' => $customers,
                        'id' => 'id_customer',
                        'name' => 'email'
                    ),
                    'desc' => $this->l('Select the Customer(s) where the rule will be applied. If you don\'t select any value, the rule will be applied to all Customers'),
                );
        }

        $render_form_end[] =
            array(
                'type' => 'select',
                'label' => $this->l('Select Country(s)'),
                'name' => 'countries[]',
                'class' => 'multiple_select',
                'multiple' => true,
                'required' => false,
                'col' => '5',
                'options' => array(
                    'query' => $countries,
                    'id' => 'id_country',
                    'name' => 'name'
                ),
                'desc' => $this->l('Select the Country(s) where the rule will be applied. If you don\'t select any value, the rule will be applied to all Countries'),
            );
        $render_form_end[] =
            array(
                'type' => 'select',
                'label' => $this->l('Select Zone(s)'),
                'name' => 'zones[]',
                'class' => 'multiple_select',
                'multiple' => true,
                'required' => false,
                'col' => '5',
                'options' => array(
                    'query' => $zones,
                    'id' => 'id_zone',
                    'name' => 'name'
                ),
                'desc' => $this->l('Select the Zone(s) where the rule will be applied. If you don\'t select any value, the rule will be applied to all Zones'),
            );
        $render_form_end[] =
            array(
                'type' => 'select',
                'label' => $this->l('Select Manufacturer(s)'),
                'name' => 'manufacturers[]',
                'class' => 'multiple_select',
                'multiple' => true,
                'required' => false,
                'col' => '5',
                'options' => array(
                    'query' => $manufacturers,
                    'id' => 'id_manufacturer',
                    'name' => 'name'
                ),
                'desc' => $this->l('Select the Manufacturer(s) where the rule will be applied. If you don\'t select any value, the rule will be applied to all Manufacturers'),
            );
        $render_form_end[] =
            array(
                'type' => 'select',
                'label' => $this->l('Select Supplier(s)'),
                'name' => 'suppliers[]',
                'class' => 'multiple_select',
                'multiple' => true,
                'required' => false,
                'col' => '5',
                'options' => array(
                    'query' => $suppliers,
                    'id' => 'id_supplier',
                    'name' => 'name'
                ),
                'desc' => $this->l('Select the Supplier(s) where the rule will be applied. If you don\'t select any value, the rule will be applied to all Suppliers'),
            );


        if (Configuration::get('GROUPINC_USE_FEATURES')) {
            $features = Feature::getFeatures((int)$id_lang);
            $array_features = array();

            foreach ($features as $key => $feature) {
                if ($feature['name']) {
                    $feature_values = FeatureValue::getFeatureValuesWithLang((int)$id_lang, $feature['id_feature']);
                    if (!empty($feature_values)) {
                        $array_features[] = array(
                            'type' => 'select',
                            'label' => $this->l('Select').' '.$feature['name'],
                            'name' => 'feature_'.$feature['id_feature'].'[]',
                            'multiple' => true,
                            'required' => false,
                            'class' => 'multiple_select',
                            'col' => '3',
                            'options' => array(
                                'query' => $feature_values,
                                'id' => 'id_feature_value',
                                'name' => 'value'
                            ),
                            'desc' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? $this->l('Select the ').' '.$feature['name'].' '.$this->l('to apply this configuration') : '',
                        );
                    }
                }
            }

            foreach ($array_features as $f) {
                array_push($render_form_end, $f);
            }
        }

        if (Configuration::get('GROUPINC_USE_ATTRIBUTES')) {
            $attributeGroups = AttributeGroup::getAttributesGroups((int)$id_lang);

            $array_attributes = array();
            foreach ($attributeGroups as $key => $attributeGroup) {
                if ($attributeGroup['name']) {
                    if (!empty($attributeGroups)) {
                        $array_attributes[] = array(
                            'type' => 'select',
                            'label' => $this->l('Select').' '.$attributeGroup['name'],
                            'name' => 'attribute_'.$attributeGroup['id_attribute_group'].'[]',
                            'multiple' => true,
                            'required' => false,
                            'class' => 'multiple_select',
                            'col' => '3',
                            'options' => array(
                                'query' => AttributeGroup::getAttributes((int)$id_lang, $attributeGroup['id_attribute_group']),
                                'id' => 'id_attribute',
                                'name' => 'name'
                            ),
                            'desc' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? $this->l('Select the ').' '.$attributeGroup['name'].' '.$this->l('to apply this configuration') : '',
                        );
                    }
                }
            }

            foreach ($array_attributes as $a) {
                array_push($render_form_end, $a);
            }
        }

        // add the final part of the form
        foreach ($render_form_end as $f) {
            array_push($this->fields_form[2]['form']['input'], $f);
        }

        $this->fields_form[2]['form']['submit'] = array(
                'title' => $this->l('Save'),
                'type' => 'submit',
        );



        $this->fields_form[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Schedule'),
                'icon' => 'icon-calendar'
            ),
            'input' => array(
                array(
                    'type' =>  (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'datetime' : 'date',
                    'label' => $this->l('Date From'),
                    'name' => 'date_from',
                    'col' => '4',
                    'desc' => $this->l('Date from which the rule is valid. You can use hours, minutes and secons. Example: 2016-10-27 is considered 2016-10-27 00:00:00 and it means that the rule is valid from 2016-10-27 00:00:00'),
                ),
                array(
                    'type' =>  (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'datetime' : 'date',
                    'label' => $this->l('Date To'),
                    'name' => 'date_to',
                    'col' => '4',
                    'desc' => $this->l('Date to which the rule is valid. You can use hours, minutes and secons. Example: 2016-10-27 is considered 2016-10-27 00:00:00 and it means that the rule is valid until 2016-10-26 23:59:59'),
                ),
                array(
                    'type' => 'free',
                    'label' => $this->l('Schedule'),
                    'name' => 'schedule',
                    'hint' => $this->l('Select days of week and hours to show apply the rule (Click on the box to enable or disable the day and define the time range)')
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'type' => 'submit',
            ),
        );

        //Load db values for select inputs
        if ($giconf->id) {
            $groups_db = explode(';', $giconf->groups);
            $zones_db = explode(';', $giconf->zones);
            $countries_db = explode(';', $giconf->countries);
            $manufacturers_db = explode(';', $giconf->manufacturers);
            $suppliers_db = explode(';', $giconf->suppliers);
            $currencies_db = explode(';', $giconf->currencies);
            $languages_db = explode(';', $giconf->languages);
            $attributes_db = Tools::jsonDecode($giconf->attributes);

            $features_decoded_array = Tools::jsonDecode($giconf->features);
            $attributes_decoded_array = Tools::jsonDecode($giconf->attributes);

            $this->fields_value = array(
                'groups[]' => $groups_db,
                'zones[]' => $zones_db,
                'countries[]' => $countries_db,
                'manufacturers[]' => $manufacturers_db,
                'suppliers[]' => $suppliers_db,
                'currencies[]' => $currencies_db,
                'languages[]' => $languages_db,
            );

            $i = 0;
            if (!empty($features_decoded_array)) {
                foreach($features_decoded_array as $key => $feature) {
                    $feature_values['feature_'.$key.'[]'] = explode(';',$feature);
                    $i++;
                }

                foreach ($feature_values as $key => $f) {
                    $this->fields_value[$key] = $f;
                }
            }

            $i = 0;
            $attribute_values = array();
            if (!empty($attributes_decoded_array)) {
                foreach($attributes_decoded_array as $key => $attribute) {
                    $attribute_values['attribute_'.$key.'[]'] = explode(';',$attribute);
                    $i++;
                }

                foreach ($attribute_values as $key => $f) {
                    $this->fields_value[$key] = $f;
                }
            }

            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $this->fields_value['categories[]'] = explode(';', $giconf->categories);
            }
        }

        if (Configuration::get('GROUPINC_USE_PRODUCTS')) {
            $products = $this->getProductsLite($id_lang, true, true);
            $this->context->smarty->assign(array(
                'products_selected' => $giconf->products,
                'products_available' => $products,
            ));
        }

        if (Configuration::get('GROUPINC_USE_CUSTOMERS')) {
            $customers = Customer::getCustomers(true);
            $this->context->smarty->assign(array(
                'customers_selected' => $giconf->customers,
                'customers_available' => $customers,
            ));
        }

        if ($giconf->id) {
            $this->context->smarty->assign(array(
                'schedule' => $giconf->schedule,
            ));
        } else {
            $this->context->smarty->assign(array(
                'schedule' => '',
            ));
        }

        $this->fields_value['schedule'] =  $this->context->smarty->fetch($this->module->getLocalPath().'views/templates/admin/schedule.tpl');

        $this->content .= parent::renderForm();
        $this->content .= $this->_createTemplate('admin_content.tpl')->fetch();
        return;
    }

    protected function renderGlobalConfigForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = new Groupinc();
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->currentIndex = self::$currentIndex;
        $helper->submit_action = 'submitGroupincModuleGlobalConfig';
        $helper->token = Tools::getAdminTokenLite($this->tabClassName);
        $helper->tpl_vars = array(
            'fields_value' => $this->getGlobalConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getGlobalConfigForm()));
    }

    protected function getGlobalConfigForm()
    {
        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Global settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 5,
                        'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                        'name' => 'GROUPINC_PRIORIZE_MIN',
                        'label' => $this->l('Show minimum price'),
                        'class' => 't',
                        'default_value' => true,
                        'desc' => $this->l('Enable if you want to analyze all the rules and apply only the rule with minimum result price'),
                        'values' => array(
                            array(
                                'id' => 'GROUPINC_PRIORIZE_MIN_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'GROUPINC_PRIORIZE_MIN_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'col' => 5,
                        'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                        'name' => 'GROUPINC_USE_PRODUCTS',
                        'label' => $this->l('Use products filter'),
                        'class' => 't',
                        'default_value' => true,
                        'desc' => (version_compare(_PS_VERSION_, '1.6', '<')) ? $this->l('Enable if you want to use the features. Disable if you will not need to create rules by products') : '',
                        'hint' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? $this->l('Enable if you want to use the features. Disable if you will not need to create rules by products') : '',
                        'values' => array(
                            array(
                                'id' => 'GROUPINC_USE_PRODUCTS_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'GROUPINC_USE_PRODUCTS_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'col' => 5,
                        'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                        'name' => 'GROUPINC_USE_CUSTOMERS',
                        'label' => $this->l('Use customers filter'),
                        'class' => 't',
                        'default_value' => true,
                        'desc' => (version_compare(_PS_VERSION_, '1.6', '<')) ? $this->l('Enable if you want to use the attributes. Disable if you will not need to create rules by customers') : '',
                        'hint' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? $this->l('Enable if you want to use the attributes. Disable if you will not need to create rules by customers') : '',
                        'values' => array(
                            array(
                                'id' => 'GROUPINC_USE_CUSTOMERS_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'GROUPINC_USE_CUSTOMERS_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'col' => 5,
                        'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                        'name' => 'GROUPINC_USE_FEATURES',
                        'label' => $this->l('Use features filter'),
                        'class' => 't',
                        'default_value' => true,
                        'desc' => (version_compare(_PS_VERSION_, '1.6', '<')) ? $this->l('Enable if you want to use the features. Disable if you will not need to create rules by features') : '',
                        'hint' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? $this->l('Enable if you want to use the features. Disable if you will not need to create rules by features') : '',
                        'values' => array(
                            array(
                                'id' => 'GROUPINC_USE_FEATURES_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'GROUPINC_USE_FEATURES_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'col' => 5,
                        'type' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'switch' : 'radio',
                        'name' => 'GROUPINC_USE_ATTRIBUTES',
                        'label' => $this->l('Use attributes filter'),
                        'class' => 't',
                        'default_value' => true,
                        'desc' => (version_compare(_PS_VERSION_, '1.6', '<')) ? $this->l('Enable if you want to use the attributes. Disable if you will not need to create rules by attributes') : '',
                        'hint' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? $this->l('Enable if you want to use the attributes. Disable if you will not need to create rules by attributes') : '',
                        'values' => array(
                            array(
                                'id' => 'GROUPINC_USE_ATTRIBUTES_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'GROUPINC_USE_ATTRIBUTES_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'type' => 'submit',
                    'class' => (version_compare(_PS_VERSION_, '1.6', '>=')) ? 'btn btn-default pull-right' : 'button big',
                    'name' => 'submitGroupincModuleGlobalConfig',
                ),
            ),
        );

        return $form;
    }

    protected function getGlobalConfigFormValues()
    {
        return array(
            'GROUPINC_PRIORIZE_MIN' => Configuration::get('GROUPINC_PRIORIZE_MIN'),
            'GROUPINC_USE_FEATURES' => Configuration::get('GROUPINC_USE_FEATURES'),
            'GROUPINC_USE_ATTRIBUTES' => Configuration::get('GROUPINC_USE_ATTRIBUTES'),
            'GROUPINC_USE_PRODUCTS' => Configuration::get('GROUPINC_USE_PRODUCTS'),
            'GROUPINC_USE_CUSTOMERS' => Configuration::get('GROUPINC_USE_CUSTOMERS'),
        );
    }

    public function renderView()
    {
        return parent::renderView();
    }

    public function processDelete()
    {
        parent::processDelete();
    }

    public function processSave()
    {
        if (Tools::getValue('submitFormAjax')) {
            $this->redirect_after = false;
        }

        if ($this->_formValidations()) {
            $_POST['groups'] = (!Tools::getValue('groups')) ? '' : implode(';', Tools::getValue('groups'));
            $_POST['countries'] = (!Tools::getValue('countries')) ? '' : implode(';', Tools::getValue('countries'));
            $_POST['zones'] = (!Tools::getValue('zones')) ? '' : implode(';', Tools::getValue('zones'));
            $_POST['manufacturers'] = (!Tools::getValue('manufacturers')) ? '' : implode(';', Tools::getValue('manufacturers'));
            $_POST['suppliers'] = (!Tools::getValue('suppliers')) ? '' : implode(';', Tools::getValue('suppliers'));
            $_POST['customers'] = (!Tools::getValue('customers')) ? '' : implode(';', Tools::getValue('customers'));
            $_POST['products'] = (!Tools::getValue('products')) ? '' : implode(';', Tools::getValue('products'));
            $_POST['currencies'] = (!Tools::getValue('currencies')) ? '' : implode(';', Tools::getValue('currencies'));
            $_POST['languages'] = (!Tools::getValue('languages')) ? '' : implode(';', Tools::getValue('languages'));
            $_POST['attributes'] = (!Tools::getValue('attributes')) ? '' : implode(';', Tools::getValue('attributes'));
            $_POST['features'] = (!Tools::getValue('features')) ? '' : implode(';', Tools::getValue('features'));

            if (version_compare(_PS_VERSION_, '1.6', '>=')) {
                if (Tools::isSubmit('categories')) {
                    $cats = Tools::getValue('categories');
                    $_POST['categories'] = serialize($cats);
                } else {
                    $_POST['categories'] = '';
                }
            } else {
                $_POST['categories'] = (!Tools::isSubmit('categories')) ? '' : implode(';', Tools::getValue('categories'));
            }

            if (Tools::getValue('type') == 1) {
                $_POST['fix'] = 0;
            } else if (Tools::getValue('type') == 0) {
                $_POST['percentage'] = 0;
            }

            if (version_compare(_PS_VERSION_, '1.6', '>=')) {
                $this->cleanCache();
            }
            return parent::processSave();
        }
    }

    public function processDuplicate()
    {
        $id_conf = Tools::getValue($this->identifier);
        $conf = new GroupincConfiguration($id_conf);
        unset($conf->id_groupinc_configuration);

        if (!$conf->add()) {
            $this->errors[] = Tools::displayError('An error occurred while duplicating the report #'.$id_conf);
        } else {
            $this->confirmations[] = sprintf($this->l('Rule #%s - %s successfully duplicated.'), $id_conf, $conf->name);
            $this->afterUpdate($conf, $conf->id);
            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                return Tools::redirectAdmin('index.php?tab='.$this->tabClassName.'&token='.Tools::getAdminTokenLite($this->tabClassName));
            } else {
                return Tools::redirectAdmin('index.php?controller='.$this->tabClassName.'&token='.Tools::getAdminTokenLite($this->tabClassName));
            }
        }
    }

    protected function afterAdd($object)
    {
        $id = Tools::getValue('id_groupinc_configuration');
        $this->afterUpdate($object, $id);
        return true;
    }

    protected function afterUpdate($object, $id = false)
    {
        if ($id) {
            $giconf = new GroupincConfiguration((int)$id);
        } else {
            $giconf = new GroupincConfiguration((int)$object->id);
        }
        if (Validate::isLoadedObject($giconf)) {
            $features = Feature::getFeatures((int)$this->context->cookie->id_lang);
            $attributeGroups = AttributeGroup::getAttributesGroups((int)$this->context->cookie->id_lang);

            $array_features_result = array();
            $array_attributes_result = array();

            foreach ($features as $f) {
                if (Tools::getValue('feature_'.$f['id_feature'])) {
                    $array_features_result[$f['id_feature']] = implode(';', Tools::getValue('feature_'.$f['id_feature']));
                }
            }

            foreach ($attributeGroups as $a) {
                if (Tools::getValue('attribute_'.$a['id_attribute_group'])) {
                    $array_attributes_result[$a['id_attribute_group']] = implode(';', Tools::getValue('attribute_'.$a['id_attribute_group']));
                }
            }
            if (!empty($array_features_result)) {
                $giconf->features = Tools::jsonEncode($array_features_result);
            } else {
                $giconf->features = '';
            }

            if (!empty($array_attributes_result)) {
                $giconf->attributes = Tools::jsonEncode($array_attributes_result);
            } else {
                $giconf->attributes = '';
            }
            $giconf->save();
        }
        return true;
    }

    /**
     * @param string $token
     * @param int $id
     * @param string $name
     * @return mixed
     */
    public function displayDeleteLink($token = null, $id, $name = null)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_delete.tpl');

        $tpl->assign(array(
            'href' => self::$currentIndex.'&'.$this->identifier.'='.$id.'&delete'.$this->table.'&token='.($token != null ? $token : $this->token),
            'confirm' => $this->l('Delete the selected item? ').$name,
            'action' => $this->l('Delete'),
            'id' => $id,
        ));

        return $tpl->fetch();
    }

    protected function getGroupincTypes()
    {
        //$types = array($this->l('Fix'), $this->l('Percentage'), $this->l('Fix + Percentage'), $this->l('Percentage + Fix'));
        $types = array($this->l('Fix'), $this->l('Percentage'), $this->l('Fix + Percentage'));

        $list_types = array();
        foreach ($types as $key => $type) {
            $list_types[$key]['id'] = $key;
            $list_types[$key]['value'] = $key;
            $list_types[$key]['name'] = $type;
        }
        return $list_types;
    }

    protected function getGroupincModes()
    {
        $modes = array($this->l('Increment'), $this->l('Reduction'), $this->l('Set Fixed price'));

        $list_modes = array();
        foreach ($modes as $key => $mode) {
            $list_modes[$key]['id'] = $key;
            $list_modes[$key]['value'] = $key;
            $list_modes[$key]['name'] = $mode;
        }
        return $list_modes;
    }

    public function getGroupincType($type)
    {
        if ($type == '0') {
            return $this->l('Fix');
        } elseif ($type == '1') {
            return $this->l('Percentage');
        } else if ($type == '2')
            return $this->l('Fix + Percentage');
    }

    public function getGroupincMode($mode)
    {
        if ($mode == '0') {
            return $this->l('Increment');
        } elseif ($mode == '1') {
            return $this->l('Reduction');
        }  elseif ($mode == '2') {
			 return $this->l('Fixed Price');
		}
    }

    public function getGroupincPriceOptions($calculation = false)
    {
        $price_options = array($this->l('Wholesale Price without Taxes'), $this->l('Retail Price Without Taxes'), $this->l('Wholesale Price with Taxes'), $this->l('Retail Price with Taxes'), $this->l('Supplier Price without Taxes'), $this->l('Supplier Price with Taxes'));

        if ($calculation) {
            array_push($price_options, $this->l('Benefits margin without taxes (Retail - Wholesale)'));
            array_push($price_options, $this->l('Benefits margin with taxes (Retail - Wholesale)'));
        }

        $list_price_options = array();
        foreach ($price_options as $key => $mode) {
            $list_price_options[$key]['id'] = $key;
            $list_price_options[$key]['value'] = $key;
            $list_price_options[$key]['name'] = $mode;
        }
        return $list_price_options;
    }

    public function getCustomerGroups($ids_customer_groups)
    {
        if ($ids_customer_groups === '' || $ids_customer_groups === 'all') {
            return $this->l('All');
        }
        $groups = array();
        $groups_array = explode(';', $ids_customer_groups);
        foreach ($groups_array as $key => $group) {
            if ($key == $this->top_elements_in_list) {
                $groups[] = $this->l('...and more');
                break;
            }
            $group = new Group($group, $this->context->language->id);
            $groups[] = $group->name;
        }
        return implode('<br />', $groups);
    }

    public function getCountries($ids_countries)
    {
        if ($ids_countries === '' || $ids_countries === 'all') {
            return $this->l('All');
        }
        $countries = array();
        $countries_array = explode(';', $ids_countries);
        foreach ($countries_array as $key => $country) {
            if ($key == $this->top_elements_in_list) {
                $countries[] = $this->l('...and more');
                break;
            }
            $country = new Country($country, $this->context->language->id);
            $countries[] = $country->name;
        }
        return implode('<br />', $countries);
    }

    public function getZones($ids_zones)
    {
        if ($ids_zones === '' || $ids_zones === 'all') {
            return $this->l('All');
        }
        $zones = array();
        $zones_array = explode(';', $ids_zones);
        foreach ($zones_array as $key => $zone) {
            if ($key == $this->top_elements_in_list) {
                $zones[] = $this->l('...and more');
                break;
            }
            $zone = new Zone($zone, $this->context->language->id);
            $zones[] = $zone->name;
        }
        return implode('<br />', $zones);
    }

    public function getSuppliers($ids_suppliers)
    {
        if ($ids_suppliers === '' || $ids_suppliers === 'all') {
            return $this->l('All');
        }

        $suppliers = array();
        $suppliers_array = explode(';', $ids_suppliers);
        foreach ($suppliers_array as $key => $supplier) {
            if ($key == $this->top_elements_in_list) {
                $suppliers[] = $this->l('...and more');
                break;
            }
            $supplier = new Supplier($supplier);
            $suppliers[] = $supplier->name;
        }
        return implode('<br />', $suppliers);
    }


    public function getCategories($ids_categories)
    {
        if ($ids_categories === '' || $ids_categories === 'all') {
            return $this->l('All');
        }

        $categories = array();

		if (@unserialize($ids_categories) !== false) {
			$categories_array = unserialize($ids_categories);
        } else {
			$categories_array = explode(';', $ids_categories);
        }

        foreach ($categories_array as $key => $category) {
            if ($key == $this->top_elements_in_list) {
                $categories[] = $this->l('...and more');
                break;
            }
            $category = new Category($category, $this->context->language->id);
            $categories[] = $category->name;
        }
        return implode('<br />', $categories);
    }

    public function getCurrencies($ids_currencies)
    {
        if ($ids_currencies === '' || $ids_currencies === 'all') {
            return $this->l('All');
        }
        $currencies = array();
        $currencies_array = explode(';', $ids_currencies);
        foreach ($currencies_array as $key => $currency) {
            if ($key == $this->top_elements_in_list) {
                $currencies[] = $this->l('...and more');
                break;
            }
            $currency = new Currency($currency);
            $currencies[] = $currency->name;
        }
        return implode('<br />', $currencies);
    }

    public function getProducts($ids_products)
    {
        if ($ids_products === '' || $ids_products === 'all') {
            return $this->l('All');
        }
        $products = array();
        $products_array = explode(';', $ids_products);
        foreach ($products_array as $key => $product) {
            if ($key == $this->top_elements_in_list) {
                $products[] = $this->l('...and more');
                break;
            }
            $product = new Product($product, $this->context->language->id);
            $products[] = '['.$product->id.'] - '.$product->name[$this->context->language->id];
        }
        return implode('<br />', $products);
    }

    public function getCustomers($ids_customers)
    {
        if ($ids_customers === '' || $ids_customers === 'all') {
            return $this->l('All');
        }
        $customers = array();
        $customers_array = explode(';', $ids_customers);
        foreach ($customers_array as $key => $customer) {
            if ($key == $this->top_elements_in_list) {
                $customers[] = $this->l('...and more');
                break;
            }
            $customer = new Customer($customer, $this->context->language->id);
            $customers[] = $customer->firstname.' '.$customer->lastname;
        }
        return implode('<br />', $customers);
    }

    private function _createTemplate($tpl_name)
    {
        if ($this->override_folder) {
            if ($this->context->controller instanceof ModuleAdminController) {
                $override_tpl_path = $this->context->controller->getTemplatePath().$tpl_name;
            } elseif ($this->module) {
                $override_tpl_path = _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/'.$tpl_name;
            } else {
                if (file_exists($this->context->smarty->getTemplateDir(1).DIRECTORY_SEPARATOR.$this->override_folder.$this->base_folder.$tpl_name)) {
                    $override_tpl_path = $this->context->smarty->getTemplateDir(1).DIRECTORY_SEPARATOR.$this->override_folder.$this->base_folder.$tpl_name;
                } elseif (file_exists($this->context->smarty->getTemplateDir(0).DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$this->override_folder.$this->base_folder.$tpl_name)) {
                    $override_tpl_path = $this->context->smarty->getTemplateDir(0).'controllers'.DIRECTORY_SEPARATOR.$this->override_folder.$this->base_folder.$tpl_name;
                }
            }
        } else if ($this->module) {
            $override_tpl_path = _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/'.$tpl_name;
        }
        if (isset($override_tpl_path) && file_exists($override_tpl_path)) {
            return $this->context->smarty->createTemplate($override_tpl_path, $this->context->smarty);
        } else {
            return $this->context->smarty->createTemplate($tpl_name, $this->context->smarty);
        }
    }

    private function _formValidations()
    {
        if (trim(Tools::getValue('name')) == '') {
            $this->validateRules();
            $this->errors[] = Tools::displayError($this->l('Field Name can not be empty.'));
            $this->display = 'edit';
        }

        if (Tools::getValue('date_from') > 0) {
	        if (!Validate::isDate(Tools::getValue('date_from'))) {
	            $this->errors[] = $this->l('Invalid "Date From" format');
	            $this->display = 'edit';
	        }
	    }

	    if (Tools::getValue('date_to') > 0) {
	        if (!Validate::isDate(Tools::getValue('date_to'))) {
	            $this->errors[] = $this->l('Invalid "Date To" format');
	            $this->display = 'edit';
	        }
	    }

        if (count($this->errors)) {
            return false;
        }

        return true;
    }

    public function processChangeBackofficeVal()
    {
        $gi = new GroupincConfiguration($this->id_object);
        if (!Validate::isLoadedObject($gi)) {
            $this->errors[] = Tools::displayError('An error occurred while updating Virtual POS information.');
        }
        $gi->backoffice = $gi->backoffice ? 0 : 1;
        if (!$gi->update()) {
            $this->errors[] = Tools::displayError('An error occurred while updating customer information.');
        }
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    public function printValidIcon($value, $gi)
    {
        $today = date("Y-m-d H:i:s");
        $date_title = '';

        if ($gi['date_from'] > $today) {
            $date_title = $this->l("Future rule");
            if ($gi['date_from'] != "0000-00-00 00:00:00") {
                $date_title = $date_title.'. '.$this->l("Begins in:").' '.$gi['date_from'];
            }
            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                return '<span class="time-column future-date" title="'.$date_title.'"></span>';
            } else {
                return '<span class="time-column future-date-icon" title="'.$date_title.'"><i class="icon-time"></i></span>';
            }
        }

        if ($gi['date_to'] == "0000-00-00 00:00:00" || $today < $gi['date_to']) {
            $date_title = $this->l("Valid rule");
            if ($gi['date_from'] != "0000-00-00 00:00:00" && $gi['date_to'] != "0000-00-00 00:00:00") {
                $date_title = $date_title.'. '.$this->l("From:").' '.$gi['date_from'].'. '.$this->l("Until:").' '.$gi['date_to'];
            } else if ($gi['date_from'] != "0000-00-00 00:00:00" && $gi['date_to'] == "0000-00-00 00:00:00") {
                $date_title = $date_title.'. '.$this->l("From:").' '.$gi['date_from'].' ('.$this->l("no expires").')';
            } else if ($gi['date_from'] == "0000-00-00 00:00:00" && $gi['date_to'] != "0000-00-00 00:00:00") {
                $date_title = $date_title.'. '.$this->l("Until:").' '.$gi['date_to'];
            } else {
                $date_title = $date_title.' ('.$this->l("no expires").')';
            }

            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                return '<span class="time-column valid-date" title="'.$date_title.'"></span>';
            } else {
                return '<span class="time-column valid-date-icon" title="'.$date_title.'"><i class="icon-time"></i></span>';
            }
        } else {
            $date_title = $this->l("Expired rule");
            if ($gi['date_from'] != "0000-00-00 00:00:00" && $gi['date_to'] != "0000-00-00 00:00:00") {
                $date_title = $date_title.'. '.$this->l("Between:").' '.$gi['date_from'].' '.$this->l("and:").' '.$gi['date_to'];
            } else {
                $date_title = $date_title.'. '.$this->l("From:").' '.$gi['date_to'];
            }
            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                return '<span class="time-column expired-date" title="'.$date_title.'"></span>';
            } else {
                return '<span class="time-column expired-date-icon" title="'.$date_title.'"><i class="icon-time"></i></span>';
            }
        }
    }

    public function printBackofficeIcon($value, $gi)
    {
        return '<a class="list-action-enable '.($value ? 'action-enabled' : 'action-disabled').'" href="index.php?'.htmlspecialchars('tab=AdminGroupinc&id_groupinc_configuration='.(int)$gi['id_groupinc_configuration'].'&changeBackofficeVal&token='.Tools::getAdminTokenLite('AdminGroupinc')).'">
                '.($value ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>').
            '</a>';
    }

    protected function getProductsLite($id_lang, $only_active = false, $front = false, Context $context = null)
    {
        if (!$context)
            $context = Context::getContext();

        $sql = 'SELECT p.`id_product`, CONCAT(p.`reference`, " - ", pl.`name`) as name FROM `'._DB_PREFIX_.'product` p
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
                WHERE pl.`id_lang` = '.(int)$id_lang.
                    ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
                    ($only_active ? ' AND product_shop.`active` = 1' : '');

        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return ($rq);
    }

    protected function getNumProducts($id_lang, $only_active = false, $front = false, Context $context = null)
    {
        if (!$context)
            $context = Context::getContext();

        $sql = 'SELECT count(p.`id_product`) as num_products FROM `'._DB_PREFIX_.'product` p
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
                WHERE pl.`id_lang` = '.(int)$id_lang.
                    ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
                    ($only_active ? ' AND product_shop.`active` = 1' : '');

        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        return ($rq);
    }

    protected function cleanCache() {
        /* delete smarty cache to refresh the static blocks */
        Tools::clearSmartyCache();
        Tools::clearXMLCache();
        Media::clearCache();
        if (version_compare(_PS_VERSION_, '1.7', '<') && version_compare(_PS_VERSION_, '1.4', '>')) {
            PrestaShopAutoload::getInstance()->generateIndex();
        } else if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            Tools::generateIndex();
        }
    }

    public function getConfigurations($id_product)
    {
        $configs = $this->getListConfigurations($id_product);

        if (empty($configs) || !$configs) {
            $configs = array();
        }

        $fields_list = array(
            'id_groupinc_configuration' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name')
            ),
            'type' => array(
                'title' => $this->l('Type'),
                'callback' => 'getGroupincType',
                'type' => 'select',
                'list' => array(0 => $this->l('Fix'), 1 => $this->l('Percentage'), 2 => $this->l('Fix + Percentage')),
                'filter_key' => 'a!type',
                'align' => 'text-center'
            ),
            'mode' => array(
                'title' => $this->l('Mode'),
                'callback' => 'getGroupincMode',
                'type' => 'select',
                'filter_key' => 'a!mode',
                'list' => array(0 => $this->l('Increment'), 1 => $this->l('Reduction'), 2 => $this->l('Set Fixed price')),
                'align' => 'text-center'
            ),
            'fix' => array(
                'title' => $this->l('Fix'),
                'align' => 'text-center'
            ),
            'percentage' => array(
                'title' => $this->l('Percentage'),
                'align' => 'text-center'
            ),
            'customers' => array(
                'title' => $this->l('Customers(s)'),
                'callback' => 'getCustomers',
                'align' => 'text-center'
            ),
            'categories' => array(
                'title' => $this->l('Category(s)'),
                'callback' => 'getCategories',
                'align' => 'text-center'
            ),
            'products' => array(
                'title' => $this->l('Products(s)'),
                'callback' => 'getProducts',
                'align' => 'text-center'
            ),
            'priority' => array(
                'title' => $this->l('Priority'),
                'align' => 'text-center'
            ),
            'backoffice' => array(
                'title' => $this->l('Backoffice'),
                'align' => 'text-center',
                'type' => 'select',
                'list' => array(0 => $this->l('No'), 1 => $this->l('Yes')),
                'callback' => 'printBackofficeIcon',
                'filter_key' => 'a!backoffice'
            ),
            'date_to' => array(
                'title' => $this->l('Valid'),
                'align' => 'text-center',
                'callback' => 'printValidIcon',
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'callback' => 'printActiveIcon'
            ),
        );

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $list = '<table class="table tableDnD" cellspacing="0" cellpadding="0"><tr>';
            foreach ($fields_list as $key => $field) {
                $list .= '<th>'.$field['title'].'</th>';
            }
            $list .= '</tr>';

            foreach ($configs as $config) {
                $list .= '<tr>';
                foreach ($fields_list as $key => $field) {
                    $list .= '<td>'.$config[$key].'</td>';
                }

                $list .= '</tr>';
            }

            $list .= '</table>';
        } else {
            $helper_list = new HelperList();
            $helper_list->name = 'GroupincConfiguration';
            $helper_list->module = $this;
            $helper_list->title = $this->l('Groupinc Configs');
            $helper_list->shopLinkType = 'shop';
            $helper_list->no_link = true;
            $helper_list->default_form_language = 1;
            $helper_list->show_toolbar = true;
            $helper_list->simple_header = false;
            $helper_list->identifier = 'id_groupinc_configuration';
            $helper_list->actions = array('edit');
            $helper_list->table = $this->name.'_configuration';
            $helper_list->list_id = $helper_list->table;
            $helper_list->currentIndex = $this->context->link->getAdminLink($this->tabClassName, false).'&updategroupinc_configuration';
            $helper_list->token = Tools::getAdminTokenLite($this->tabClassName);

            // This is needed for displayEnableLink to avoid code duplication
            $this->_helperlist = $helper_list;
            $helper_list->listTotal = count($configs);
            $helper_list->tpl_vars['icon'] = 'icon-AdminParentOrders';
            $generated_list = $helper_list->generateList($configs, $fields_list);
        }

        return $generated_list;
    }

    private function getListConfigurations($id_product = 0)
    {
        $id_shop = Context::getContext()->shop->id;

        $categories = Product::getProductCategories($id_product);
        $product = new Product($id_product);
        $id_manufacturer = $product->id_manufacturer;
        $product_suppliers_array = ProductSupplier::getSupplierCollection($id_product);
        $id_manufacturer = $product->id_manufacturer;

        $query = '';
        $today = date("Y-m-d H:i:s");

        $query = '
                 SELECT gi.* FROM `'._DB_PREFIX_.'groupinc_configuration` gi ';

        $datefilters = ' WHERE (date_from <= "'.$today. '" OR date_from = "0000-00-00 00:00:00") AND (date_to >= "'.$today.'" OR date_to = "0000-00-00 00:00:00")';

        $query = $query.$datefilters;

        $query = $query.' AND gi.`id_shop` = '.(int)$id_shop.' AND gi.`active` = 1 ';

        $configs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (empty($configs) || $configs === false) {
            return false;
        }

        foreach ($configs as $key => $row)
        {
            $configs_priority[$key] = $row['priority'];
        }

        array_multisort($configs_priority, SORT_ASC, $configs);

        $array_configurations_result = array();
        $tax = new Tax();
        $tax->rate = $product->getTaxesRate();
        $tax_calculator = new TaxCalculator(array($tax));

        $wholesale_price_without_taxes = $product->wholesale_price;
        $wholesale_price_with_taxes = $tax_calculator->addTaxes($product->wholesale_price);
        $retail_price_without_taxes = $product->price;
        $retail_price_with_taxes = $tax_calculator->addTaxes($product->price);

        foreach ($configs as $conf) {
            if ($conf['threshold_min_price'] > 0 || $conf['threshold_max_price'] > 0) {
                if ($conf['threshold_price'] == 0) {
                    $price_to_compare = $wholesale_price_without_taxes;
                } else if ($conf['threshold_price'] == 1) {
                    $price_to_compare = $retail_price_without_taxes;
                } else if ($conf['threshold_price'] == 2) {
                    $price_to_compare = $wholesale_price_with_taxes;
                } else if ($conf['threshold_price'] == 3) {
                    $price_to_compare = $retail_price_with_taxes;
                }
            }

            if ($conf['threshold_min_price'] > 0 && $conf['threshold_min_price'] >= $price_to_compare) {
                continue;
            }

            if ($conf['threshold_max_price'] > 0 && $conf['threshold_max_price'] <= $price_to_compare) {
                continue;
            }

            if ($conf['filter_stock']) {
                if (!$product->hasAttributes()) {
                    $stock = Product::getQuantity($id_product);
                } else if ($conf['attributes'] != '') {
                    $stock = StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute);
                } else {
                    $stock = Product::getQuantity($id_product);
                }

                if ($stock < $conf['min_stock'] || $stock > $conf['max_stock']) {
                    continue;
                }
            }

            if ($conf['filter_weight']) {
                $weight = $product->weight;
                if ($product->hasAttributes()) {
                    $combination = new Combination($id_product_attribute);
                    $weight += $combination->weight;
                }

                if ($weight < $conf['min_weight'] || ($conf['max_weight'] > 0 && $weight > $conf['max_weight'])) {
                    continue;
                }
            }

            if (!$conf['filter_store']) {
                $array_configurations_result[] = $conf;
                if ($conf['first_condition']) {
                    break;
                } else {
                    continue;
                }
            }

            if ($conf['currencies'] == 'all') {
                $conf['currencies'] = '';
            }
            if ($conf['languages'] == 'all') {
                $conf['languages'] = '';
            }
            if ($conf['groups'] == 'all') {
                $conf['groups'] = '';
            }
            if ($conf['products'] == 'all') {
                $conf['products'] = '';
            }
            if ($conf['customers'] == 'all') {
                $conf['customers'] = '';
            }
            if ($conf['countries'] == 'all') {
                $conf['countries'] = '';
            }
            if ($conf['zones'] == 'all') {
                $conf['zones'] = '';
            }
            if ($conf['categories'] == 'all') {
                $conf['categories'] = '';
            }
            if ($conf['manufacturers'] == 'all') {
                $conf['manufacturers'] = '';
            }
            if ($conf['suppliers'] == 'all') {
                $conf['suppliers'] = '';
            }
            if ($conf['features'] == 'all' || empty($conf['features'])) {
                $conf['features'] = '';
            }
            if ($conf['attributes'] == 'all' || empty($conf['attributes'])) {
                $conf['attributes'] = '';
            }

            if ($conf['attributes'] == '' && $conf['features'] == '' && $conf['products'] == '' && $conf['categories'] == '' && $conf['manufacturers'] == '' && $conf['suppliers'] == '') {
                $array_configurations_result[] = $conf;
                if ($conf['first_condition']) {
                    break;
                } else {
                    continue;
                }
            }

            $filter_features = false;
            $array_features_selected = Tools::jsonDecode($conf['features'], true);
            $product_features = Product::getFeaturesStatic((int)$id_product);

            $flag_features = 0;
            if (!empty($array_features_selected) && count($array_features_selected) > 0) {
                foreach ($product_features as $pf) {
                    if (isset($array_features_selected[$pf['id_feature']])) {
                        $array_f = explode(";", $array_features_selected[$pf['id_feature']]);
                        if (in_array($pf['id_feature_value'], $array_f)) {
                            $flag_features++;
                            continue;
                        }
                    }
                }
            } else {
                $filter_features = true;
            }

            if ($flag_features > 0) {
                $filter_features = true;
            }

            $filter_attributes = false;
            $array_attributes_selected = json_decode($conf['attributes'], true);
            if (!empty($array_attributes_selected)) {
                $product_attribute_combinations = $product->getAttributeCombinationsById($id_product_attribute, $id_lang);
                foreach ($product_attribute_combinations as $key => $prod_attr_comb) {
                    if (isset($array_attributes_selected[(int)$prod_attr_comb['id_attribute_group']])) {
                        $array_a = explode(";", $array_attributes_selected[(int)$prod_attr_comb['id_attribute_group']]);
                        if (in_array((int)$prod_attr_comb['id_attribute'], $array_a)) {
                            $filter_attributes = true;
                            break;
                        } else {
                            $filter_attributes = false;
                        }
                    }
                }
            } else {
                $filter_attributes = true;
            }

            if ($id_product_attribute == 0) {
                $filter_attributes = true;
            }

            $filter_categories = true;
            $filter_products = true;

            if (@unserialize($conf['categories']) !== false) {
                $categories_array = unserialize($conf['categories']);
            } else {
                $categories_array = explode(';', $conf['categories']);
            }

            if ($conf['categories'] !== '' && $conf['products'] == '') {
                foreach ($categories as $category) {
                    if (in_array($category, $categories_array)) {
                        $filter_categories = true;
                        $filter_products = true;
                        break;
                    } else {
                        $filter_categories = false;
                    }
                }
                if (!$filter_categories) {
                    $filter_products = false;
                }
            } else if ($conf['categories'] == '' && $conf['products'] !== '') {

                $products_array = explode(';', $conf['products']);
                if (!in_array($id_product, $products_array)) {
                    $filter_products = false;
                    $filter_categories = true;
                }
            } else if ($conf['categories'] !== '' && $conf['products'] !== '') {
                foreach ($categories as $category) {
                    if (!in_array($category, $categories_array)) {
                        $filter_categories = false;
                    } else {
                        $filter_categories = true;
                        break;
                    }
                }
                if (!$filter_categories) {
                    $products_array = explode(';', $conf['products']);
                    if (!in_array($id_product, $products_array)) {
                        $filter_products = false;
                    } else {
                        $filter_products = true;
                    }
                } else {
                    $products_array = explode(';', $conf['products']);
                    if (!in_array($id_product, $products_array)) {
                        $filter_products = false;
                    }
                }
            }
            $filter_manufacturers = true;
            if ($conf['manufacturers'] !== '') {
                $manufacturers_array = explode(';', $conf['manufacturers']);
                if (!in_array($id_manufacturer, $manufacturers_array)) {
                    $filter_manufacturers = false;
                }
            }
            $filter_suppliers = true;
            if ($conf['suppliers'] !== '') {
                $filter_suppliers = false;
                $suppliers_array = explode(';', $conf['suppliers']);
                if (!empty($product_suppliers_array)) {
                    foreach ($product_suppliers_array as $ps) {
                        if (in_array($ps->id_supplier, $suppliers_array)) {
                            $filter_suppliers = true;
                            break;
                        }
                    }
                }
            }

            if ($filter_attributes && $filter_features && $filter_categories && $filter_products && $filter_manufacturers && $filter_suppliers) {
                $array_configurations_result[] = $conf;
                if ($conf['first_condition']) {
                    break;
                } else {
                    continue;
                }
            }
        }

        if (count($array_configurations_result) > 0) {
            return $array_configurations_result;
        } else {
            return false;
        }
    }
}
