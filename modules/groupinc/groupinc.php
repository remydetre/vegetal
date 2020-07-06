<?php
/**
* Price increment/reduction by groups, categories and more
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

if (!defined('_PS_VERSION_'))
	exit;
if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

include_once(_PS_MODULE_DIR_.'groupinc/classes/GroupincConfiguration.php');

class GroupInc extends Module
{
	private $errors = array();
	private $success;

	public function __construct()
	{
		$this->name = 'groupinc';
		$this->tab = 'front_office_features';
		$this->version = '1.4.9';
		$this->author = 'idnovate';
		$this->module_key = 'f98f7f28a084f6b59d6f0b1daa57450b';
		$this->addons_id_product = '7422';
        $this->module_path = $this->_path;

		parent::__construct();

		$this->displayName = $this->l('Price increment/reduction by groups, categories and more');
		$this->description = $this->l('Increase or reduce your catalog product price with flexible and configurable conditions by categories, products, groups, customers, countries, zones, manufacturers and suppliers');


        $parent_class_name = version_compare(_PS_VERSION_, '1.7', '<') ? 'AdminPriceRule' : 'AdminCatalog';

        $this->tabMenu = array(
            'class_name' => 'AdminGroupinc',
            'parent_class_name' => $parent_class_name,
            'name' => $this->l('Increments and Discounts'),
            'visible' => true,
        );

		/* Backward compatibility */
		if (version_compare(_PS_VERSION_, '1.5', '<')) {
			require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
		}
	}

    public function copyOverrideFolder()
    {
        $version_override_folder = _PS_MODULE_DIR_.$this->name.'/override_'.Tools::substr(str_replace('.', '', _PS_VERSION_), 0, 2);
        $override_folder = _PS_MODULE_DIR_.$this->name.'/override';

        if (file_exists($override_folder) && is_dir($override_folder)) {
            $this->recursiveRmdir($override_folder);
        }

        if (is_dir($version_override_folder)) {
            $this->copyDir($version_override_folder, $override_folder);
        }

        return true;
    }

    protected function copyDir($src, $dst)
    {
        if (is_dir($src)) {
            $dir = opendir($src);
            @mkdir($dst);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($src.'/'.$file)) {
                        $this->copyDir($src.'/'.$file, $dst.'/'.$file);
                    } else {
                        copy($src.'/'.$file, $dst.'/'.$file);
                    }
                }
            }
            closedir($dir);
        }
    }

    protected function recursiveRmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        $this->recursiveRmdir($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

	public function reset()
	{
		if (!$this->uninstall(false)) {
			return false;
		}

		if (!$this->install(false)) {
			return false;
		}

		return true;
	}

	public function install()
	{
		$this->copyOverrideFolder();

		if (version_compare(_PS_VERSION_, '1.6', '>=') && version_compare(_PS_VERSION_, '1.7', '<')) {
			$this->registerHook('displayAdminProductsExtra');
		}

		return parent::install()
			&& $this->initSQLGI()
			//&& $this->addTab($this->tabName, $this->tabClassName)
			&& $this->addTab($this->tabMenu)
			&& $this->registerHook('header')
			&& $this->registerHook('footer')
			&& Configuration::updateValue('GI_GROUP_VALUES', '')
			&& (version_compare(_PS_VERSION_, '1.5', '>=') || (version_compare(_PS_VERSION_, '1.5', '<') && !$this->installGIOverride()));
	}

	public function uninstall()
	{
		return parent::uninstall()
			&& Configuration::deleteByName('GI_GROUP_VALUES')
			&& (version_compare(_PS_VERSION_, '1.5', '>=') || (version_compare(_PS_VERSION_, '1.5', '<') && !$this->removeGIOverride()))
			&& $this->removeTab($this->tabMenu)
			&& $this->uninstallSQL();
	}

	public function installGIOverride()
	{
		// Make sure the environment is OK
		if (!is_dir(dirname(__FILE__).'/../../override/classes/'))
			mkdir(dirname(__FILE__).'/../../override/classes/', 0777, true);

		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			if (file_exists(dirname(__FILE__).'/../../override/classes/Group.php'))
			{
				if (!md5_file(dirname(__FILE__).'/../../override/classes/Group.php') == md5_file(dirname(__FILE__).'/override_14/classes/Group.php'))
					$this->errors[] = '/override/classes/Group.php';
			}
			if (!copy(dirname(__FILE__).'/override_14/classes/Group.php', dirname(__FILE__).'/../../override/classes/Group.php'))
				$this->errors[] = '/override/classes/Group.php';
		}

		if (count($this->errors))
			die('<div class="conf warn">
					<img src="../img/admin/warn2.png" alt="" title="" />'.
				$this->l('The module was successfully installed but the following file already exist. Please, merge the file manually.').'<br />'.
				implode('<br />', $this->errors).
				'</div>');

		return true;
	}

	public function removeGIOverride()
	{
		// Make sure the environment is OK
		if (!is_dir(dirname(__FILE__).'/../../override/classes/'))
			mkdir(dirname(__FILE__).'/../../override/classes/', 0777, true);

		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			if (file_exists(dirname(__FILE__).'/../../override/classes/Group.php'))
			{
				if (!md5_file(dirname(__FILE__).'/../../override/classes/Group.php') == md5_file(dirname(__FILE__).'/override_14/classes/Group.php'))
					return false;
			}
			if (!unlink(dirname(__FILE__).'/../../override/classes/Group.php'))
				return false;
		}

		return true;
	}

	private function postValidation()
	{
		$replaced_array = array();
		foreach (Tools::getValue('reduction') as $key => $value)
		{
			$value = str_replace(',', '.', $value);
			if (is_numeric($value))
				$replaced_array[$key] = $value;
			else
			{
				$replaced_array[$key] = 0;
				$this->errors[] = $this->l('Please define a correct percentage');
			}
		}

		if (!count($this->errors))
			$this->success = true;

		Configuration::updateValue('GI_GROUP_VALUES', serialize($replaced_array));

		/* clean cache to show all prices properly */
		if (class_exists(Tools::clearCache())) {
			Tools::clearCache();
		}
	}

	public function getContent()
	{
		$warnings_to_show = '';
		if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            if (Configuration::get('PS_DISABLE_NON_NATIVE_MODULE')) {
            	$warnings_to_show = $warnings_to_show . $this->displayError($this->l('You have to disable the option Disable non native modules at ADVANCED PARAMETERS - PERFORMANCE'));
           	}

			if (Configuration::get('PS_DISABLE_OVERRIDES')) {
                $warnings_to_show = $warnings_to_show . $this->displayError($this->l('You have to disable the option Disable all overrides at ADVANCED PARAMETERS - PERFORMANCE'));
       		}
        }

        if (!empty($warnings_to_show)) {
        	$this->context->smarty->assign(array(
				'performance_link' => $this->context->link->getAdminLink('AdminPerformance'),
			));
			return $warnings_to_show . $this->display(__FILE__, 'views/templates/admin/admin_warnings.tpl');
        }

		// check if the tab was not created in the installation
        $id_tab = Tab::getIdFromClassName($this->tabMenu['class_name']);
        if (!$id_tab) {
            $this->addTab($this->tabMenu);
        }

		if (version_compare(_PS_VERSION_, '1.5', '<')) {
			if (Tools::isSubmit('submitForm')) {
				$this->postValidation();
			}

			$this->context->smarty->assign(array(
				'displayName'	=> $this->displayName,
				'group_values'	=> unserialize(Configuration::get('GI_GROUP_VALUES')),
				'groups'		=> Group::getGroups($this->context->language->id),
				'errors'		=> $this->errors,
				'success'		=> $this->success,
				'gi_path' 		=> $this->_path,
			));

			return $this->display(__FILE__, 'views/templates/admin/admin_form.tpl');
		} else {
            return Tools::redirectAdmin('index.php?controller=' . $this->tabMenu['class_name'] . '&token=' . Tools::getAdminTokenLite($this->tabMenu['class_name']));
        }
	}

    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product = 0;

		if (version_compare(_PS_VERSION_, '1.7', '<')) {
        	$id_product = (int)Tools::getValue('id_product');
        } else {
        	if (isset($params['id_product'])) {
        		$id_product = $params['id_product'];
        	}
        }

        if ($id_product) {
            include_once(_PS_MODULE_DIR_.'groupinc/controllers/admin/AdminGroupincController.php');
            $groupincCtrl = new AdminGroupincController();
            $contentHtmlReturn = $groupincCtrl->getConfigurations($id_product);
            if (!empty($contentHtmlReturn)) {
	            $_html_configs .= '<style type="text/css">';
	            $_html_configs .= ' .time-column {width: auto;    height: 20px;    display: block;} ';
	            $_html_configs .= ' .time-column.valid-date-icon { color: #72c279; } ';
	            $_html_configs .= ' .time-column.expired-date-icon { color: #e08f95; } ';
	            $_html_configs .= ' .time-column.future-date-icon { color: #f3e838; }';
	            $_html_configs .= ' .time-column [class^="icon-"] { font-size: 18px !important; }';
	            $_html_configs .= ' </style>';
	            $_html_configs .= $contentHtmlReturn;
				$output .= $_html_configs;
				return $output;
			}
        }
        return false;
    }

    public function hookDisplayHeader()
    {
    	if (Context::getContext()->controller->php_self == 'product') {
	    	if ($this->checkRulesExist()) {
	    		if (version_compare(_PS_VERSION_, '1.7', '>=')) {
   					$this->context->controller->addJS($this->_path.'views/js/front17.js');
   				} else {
   					$this->context->controller->addJS($this->_path.'views/js/gi_functions_front.js');
   				}
		    }
		}
    }

    public function hookDisplayFooter()
    {
    	if (Context::getContext()->controller->php_self == 'product') {
	    	if ($this->checkRulesExist()) {
   				if (version_compare(_PS_VERSION_, '1.7', '>=') || version_compare(_PS_VERSION_, '1.6', '<=')) {
                    return $this->display(__FILE__, 'views/templates/front/front.tpl');
                }
		    }
		}
    }

	private function addTab($tabMenu)
    {
    	if (version_compare(_PS_VERSION_, '1.7.1', '<')) {
	        /*Create Tab*/
	        $id_tab = Tab::getIdFromClassName($tabMenu['class_name']);
	        $tabNames = array();

	        if(!$id_tab) {
	            if (version_compare(_PS_VERSION_, '1.5', '<')) {
	                $langs = Language::getlanguages(false);

	                foreach ($langs as $l) {
	                    $tabNames[$l['id_lang']] = $tabMenu['name'];
	                }

	                $tab = new Tab();
	                $tab->module = $this->name;
	                $tab->name = $tabNames;
	                $tab->class_name = $tabMenu['class_name'];
					if (isset($tabMenu['parent_class_name'])) {
	                    $tab->id_parent = Tab::getIdFromClassName($tabMenu['parent_class_name']);
	                } else {
	                    $tab->id_parent = -1;
	                }

	                if(!$tab->save()) {
	                    return false;
	                }
	            } else {
	                $tab = new Tab();
	                $tab->class_name = $tabMenu['class_name'];
	                $tab->module = $this->name;
	                $languages = Language::getLanguages();
	                foreach ($languages as $language) {
	                    $tab->name[$language['id_lang']] = $this->l($tabMenu['name']);
	                }

	                if (isset($tabMenu['parent_class_name'])) {
	                    $tab->id_parent = Tab::getIdFromClassName($tabMenu['parent_class_name']);
	                } else {
	                    $tab->id_parent = -1;
	                }

	                if(!$tab->add()) {
	                    return false;
	                }
	            }
	        }
	    }

        return true;
    }

    private function removeTab($tab)
    {
    	if (version_compare(_PS_VERSION_, '1.7.1', '<')) {
	        $idTab = Tab::getIdFromClassName($tab['class_name']);

	        if ($idTab) {
	            $tab = new Tab($idTab);
	            $tab->delete();
	            return true;
	        }
	    }

        return true;
    }

	protected function initSQLGI()
    {
        Db::getInstance()->Execute('
	        CREATE TABLE IF NOT EXISTS `'.pSQL(_DB_PREFIX_.$this->name).'_configuration` (
				`id_groupinc_configuration` int(10) unsigned NOT NULL auto_increment,
	            `name` VARCHAR(100) NULL,
				`type` int(1) unsigned NOT NULL DEFAULT "1",
				`mode` int(1) unsigned NOT NULL DEFAULT "1",
				`price_calculation` int(1) unsigned NOT NULL DEFAULT "1",
				`price_application` int(1) unsigned NOT NULL DEFAULT "1",
	            `fix` decimal(10,3) NULL DEFAULT "0.000",
	            `percentage` decimal(10,3) NULL DEFAULT "0.000",
	            `min_result_price` decimal(10,3) NULL DEFAULT "0.000",
	            `max_result_price` decimal(10,3) NULL DEFAULT "0.000",
	            `threshold_min_price` decimal(10,3) NULL DEFAULT "0.000",
	            `threshold_max_price` decimal(10,3) NULL DEFAULT "0.000",
				`threshold_price` int(1) unsigned NOT NULL DEFAULT "1",
	            `skip_discounts` tinyint(1) unsigned NOT NULL DEFAULT "0",
	            `override_discounts` tinyint(1) unsigned NOT NULL DEFAULT "0",
	            `groups` TEXT NULL,
	            `customers` TEXT NULL,
				`products` TEXT NULL,
	            `countries` TEXT NULL,
	            `zones` TEXT NULL,
	            `categories` TEXT NULL,
	            `manufacturers` TEXT NULL,
	            `suppliers` TEXT NULL,
	            `languages` TEXT NULL,
	            `currencies` TEXT NULL,
	            `features` TEXT NULL,
	            `attributes` TEXT NULL,
	            `product_qty` int(4) unsigned NOT NULL DEFAULT "0",
	            `show_as_discount` tinyint(1) unsigned NULL DEFAULT "0",
	            `active` tinyint(1) unsigned NOT NULL DEFAULT "0",
				`show_on_sale` tinyint(1) unsigned NOT NULL DEFAULT "0",
				`show_prices_drop` tinyint(1) unsigned NOT NULL DEFAULT "0",
				`show_decimals` tinyint(1) unsigned NOT NULL DEFAULT "0",
        		`filter_prices` tinyint(1) unsigned DEFAULT "0",
        		`filter_store` tinyint(1) unsigned DEFAULT "0",
        		`filter_stock` tinyint(1) unsigned,
        		`min_stock` int(10) NULL,
        		`max_stock` int(10) NULL,
	            `backoffice` tinyint(1) unsigned NOT NULL DEFAULT "0",
	            `priority` int(1) unsigned DEFAULT "0",
				`first_condition` tinyint(1) unsigned NOT NULL DEFAULT "0",
	            `id_shop` tinyint(1) unsigned NOT NULL DEFAULT "0",
				`schedule` TEXT NULL DEFAULT "",
				`filter_weight` tinyint(1) unsigned,
        		`min_weight` decimal(10,3) NULL DEFAULT "0.000",
        		`max_weight` decimal(10,3) NULL DEFAULT "0.000",
	            `date_from` DATETIME,
	            `date_to` DATETIME,
	            `date_add` DATETIME,
	            `date_upd` DATETIME,
	        PRIMARY KEY (`id_groupinc_configuration`),
	        KEY `id_groupinc_configuration` (`id_groupinc_configuration`)
	        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;');

		return true;
    }

    protected function uninstallSQL()
    {
        return Db::getInstance()->Execute('DROP TABLE IF EXISTS `'.pSQL(_DB_PREFIX_.$this->name).'_configuration`');
    }

    protected function checkRulesExist()
    {
    	$id_shop = Context::getContext()->shop->id;

		$query = '
                SELECT gi.* FROM `'._DB_PREFIX_.'groupinc_configuration` gi WHERE gi.`id_shop` = '.(int)$id_shop.'
                AND gi.`active` = 1';
        $rules = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return ($rules == false) ? false : true;
    }
}