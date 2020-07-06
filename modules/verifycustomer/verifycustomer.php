<?php
/**
 * Module to verify new customers and hide prices for not authorized customers.
 * 
 * @author    Singleton software <info@singleton-software.com>
 * @copyright 2017 Singleton software
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if(!defined('_PS_VERSION_'))
	exit;

class VerifyCustomer extends Module
{
	public function  __construct()
	{
		$this->name = 'verifycustomer';
		$this->tab = 'pricing_promotion';
		$this->version = '1.5.0';
		$this->author = 'Singleton software';
		$this->module_key = '6bf8b0929fdf1f52858982d9329344ff';
		$this->author_address = '0x82BBBf54B369bf4dB2704ed3b97c85294950231C';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => (version_compare(_PS_VERSION_, '1.6', '<') ? '1.6' : _PS_VERSION_ ));
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Verify Customer');
		$this->description = $this->l('Module to verify new customers and hide prices for not authorized customers.');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
		if (!Configuration::get($this->name))      
			$this->warning = $this->l('No name provided');
	}

	public function install()
	{
		if(Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);

		if (parent::install()) {
			$verifyCustomerConfigData = $this->initVerifyCustomerConfigData();
			$productDetailHook = true;
			if (!version_compare(_PS_VERSION_, '1.7', '>')) {
				$productDetailHook = $this->registerHook('actionProductOutOfStock') && $this->registerHook('displayCustomerAccountForm') && $this->registerHook('actionBeforeSubmitAccount') && $this->registerHook('displayBackOfficeTop');
			} else {
				$productDetailHook = $this->registerHook('additionalCustomerFormFields') && $this->registerHook('validateCustomerFormFields') && $this->registerHook('actionAdminControllerSetMedia');
			}
			if ($verifyCustomerConfigData && $productDetailHook 
				&& Configuration::updateGlobalValue($this->name, Tools::jsonEncode($verifyCustomerConfigData))
				&& $this->registerHook('actionCustomerAccountAdd')
				&& $this->registerHook('actionObjectCustomerAddAfter')
				&& $this->registerHook('displayCustomerAccountFormTop')
				&& $this->registerHook('actionObjectCustomerUpdateAfter')
				&& $this->registerHook('displayHeader')
				&& $this->alterDB(true)
				) {
					return true;
			}
		}
		return false;
	}
	
	public function uninstall()
	{
		if (parent::uninstall()) {
			$productDetailUnHook = true;
			if (!version_compare(_PS_VERSION_, '1.7', '>')) {
				$productDetailUnHook = $this->unregisterHook('actionProductOutOfStock') && $this->unregisterHook('displayCustomerAccountForm') && $this->unregisterHook('actionBeforeSubmitAccount') && $this->unregisterHook('displayBackOfficeTop');
			} else {
				$productDetailUnHook = $this->unregisterHook('additionalCustomerFormFields') && $this->unregisterHook('validateCustomerFormFields') && $this->unregisterHook('actionAdminControllerSetMedia');
			}
			if ($productDetailUnHook && Configuration::deleteByName($this->name)
				&& $this->unregisterHook('actionCustomerAccountAdd')
				&& $this->unregisterHook('actionObjectCustomerAddAfter')
				&& $this->unregisterHook('displayCustomerAccountFormTop')
				&& $this->unregisterHook('actionObjectCustomerUpdateAfter')
				&& $this->unregisterHook('displayHeader')
				&& $this->alterDB(false)
				) {
					return true;
			}
		}
		return false;
	}
	
	private function alterDB($isInstall)
	{
		if ($isInstall) {
			$isCustomerVerifyCreated = Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'customer_verify` (
    			`id_customer` int(10) NOT NULL,
    			`verify` int(1) NOT NULL DEFAULT 0
    			) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
			$isCustomerVerifyFilesCreated = Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'customer_verify_files` (
		        `id_customer` INT UNSIGNED NOT NULL,
		        `file_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
		        ) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci');
			$customers = Db::getInstance()->executeS("SELECT id_customer, active FROM `"._DB_PREFIX_."customer`");
			if (count($customers) > 0) {
				foreach ($customers as $key => $customer) {
					if (!Db::getInstance()->insert('customer_verify', array('id_customer' => (int)$customer['id_customer'], 'verify' => (int)$customer['active']))) {
						return false;
					}
				}
			}
			$visitorGroup = new Group((int)Configuration::get('PS_UNIDENTIFIED_GROUP'));
			$visitorGroup->show_prices = 0;
			$guestGroup = new Group((int)Configuration::get('PS_GUEST_GROUP'));
			$guestGroup->show_prices = 0;
			return $isCustomerVerifyCreated && $isCustomerVerifyFilesCreated && $visitorGroup->update() && $guestGroup->update() && Configuration::updateGlobalValue('PS_GROUP_FEATURE_ACTIVE', '1');
		} else {
			$visitorGroup = new Group((int)Configuration::get('PS_UNIDENTIFIED_GROUP'));
			$visitorGroup->show_prices = 1;
			$guestGroup = new Group((int)Configuration::get('PS_GUEST_GROUP'));
			$guestGroup->show_prices = 1;
			$dropCustomerVerifyTable = Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'customer_verify`');
			$dropCustomerVerifyFilesTable = Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'customer_verify_files`');
			return $dropCustomerVerifyTable && $dropCustomerVerifyFilesTable && $visitorGroup->update() && $guestGroup->update();
		}
	}
	
	public function getContent()
	{
		$settingsUpdated = '';
		if (Tools::isSubmit('submitVerifyCustomerData'))
		{
			$updateVerifyCustomerConfigData = array();
			$updateVerifyCustomerConfigData['approve_customer'] = Tools::getValue('approve_customer');
			$updateVerifyCustomerConfigData['send_mail_after_reg_to_admin'] = Tools::getValue('send_mail_after_reg_to_admin');
			$employees = $this->getEmployeesWithEmails();
			if (count($employees) > 0) {
				foreach ($employees as $key => $value) {
					$employee = new Employee($value['id_employee']);
					$updateVerifyCustomerConfigData['employee_'.$value['id_employee'].'_1'] = Tools::getValue('employee_'.$value['id_employee'].'_1');
				}
			}
			$groups = Group::getGroups($this->context->language->id, $this->context->shop->id);
			if (count($groups) > 0 && Group::isFeatureActive()) {
				foreach ($groups as $key => $value) {
					if ((int)$value['id_group'] != (int)Configuration::get('PS_UNIDENTIFIED_GROUP') && 
						(int)$value['id_group'] != (int)Configuration::get('PS_CUSTOMER_GROUP') && 
						(int)$value['id_group'] != (int)Configuration::get('PS_GUEST_GROUP')) {
						$updateVerifyCustomerConfigData['group_'.$value['id_group'].'_1'] = Tools::getValue('group_'.$value['id_group'].'_1');
						$updateVerifyCustomerConfigData['auto_approve_group_'.$value['id_group'].'_1'] = Tools::getValue('auto_approve_group_'.$value['id_group'].'_1');
					}
				}
			}
			$updateVerifyCustomerConfigData['send_mail_after_approve_to_customer'] = Tools::getValue('send_mail_after_approve_to_customer');
			$updateVerifyCustomerConfigData['allow_choose_custom_group_to_customer'] = Tools::getValue('allow_choose_custom_group_to_customer');
			$updateVerifyCustomerConfigData['custom_group_position'] = Tools::getValue('custom_group_position');
			$updateVerifyCustomerConfigData['custom_group_select_type'] = Tools::getValue('custom_group_select_type');
			
			$updateVerifyCustomerConfigData['show_product_list_box'] = Tools::getValue('show_product_list_box');
			$updateVerifyCustomerConfigData['show_product_detail_box'] = Tools::getValue('show_product_detail_box');
			$updateVerifyCustomerConfigData['show_upload_button'] = Tools::getValue('show_upload_button');
			$updateVerifyCustomerConfigData['upload_file_required'] = Tools::getValue('upload_file_required');
			$updateVerifyCustomerConfigData['upload_file_allowed_files'] = Tools::getValue('upload_file_allowed_files');
			$updateVerifyCustomerConfigData['upload_file_max_file_size'] = Tools::getValue('upload_file_max_file_size');
			$updateVerifyCustomerConfigData['upload_file_position'] = Tools::getValue('upload_file_position');
			
			foreach (Language::getLanguages() as $key => $value) {
				$updateVerifyCustomerConfigData['text_not_authorized_pl'][$value['id_lang']] = Tools::getValue('text_not_authorized_pl_'.$value['id_lang']);
				$updateVerifyCustomerConfigData['link_text_pl'][$value['id_lang']] = Tools::getValue('link_text_pl_'.$value['id_lang']);
				$updateVerifyCustomerConfigData['text_not_authorized_pd'][$value['id_lang']] = Tools::getValue('text_not_authorized_pd_'.$value['id_lang']);
				$updateVerifyCustomerConfigData['link_text_pd'][$value['id_lang']] = Tools::getValue('link_text_pd_'.$value['id_lang']);
				$updateVerifyCustomerConfigData['upload_file_label'][$value['id_lang']] = Tools::getValue('upload_file_label_'.$value['id_lang']);;
				$updateVerifyCustomerConfigData['upload_file_description'][$value['id_lang']] = Tools::getValue('upload_file_description_'.$value['id_lang']);;
			}
			$updateVerifyCustomerConfigData['background_color_pl'] = Tools::getValue('background_color_pl');
			$updateVerifyCustomerConfigData['text_color_pl'] = Tools::getValue('text_color_pl');
			$updateVerifyCustomerConfigData['text_size_pl'] = Tools::getValue('text_size_pl');
			$updateVerifyCustomerConfigData['show_borders_pl'] = Tools::getValue('show_borders_pl');
			$updateVerifyCustomerConfigData['border_radius_pl'] = Tools::getValue('border_radius_pl');
			$updateVerifyCustomerConfigData['background_color_pd'] = Tools::getValue('background_color_pd');
			$updateVerifyCustomerConfigData['text_color_pd'] = Tools::getValue('text_color_pd');
			$updateVerifyCustomerConfigData['text_size_pd'] = Tools::getValue('text_size_pd');
			$updateVerifyCustomerConfigData['show_borders_pd'] = Tools::getValue('show_borders_pd');
			$updateVerifyCustomerConfigData['border_radius_pd'] = Tools::getValue('border_radius_pd');
			$updateVerifyCustomerConfigData['product_detail_position'] = Tools::getValue('product_detail_position');
			$updateVerifyCustomerConfigData['product_list_position'] = Tools::getValue('product_list_position');
			Configuration::updateValue($this->name, Tools::jsonEncode($updateVerifyCustomerConfigData));
			$settingsUpdated .= $this->displayConfirmation($this->l('Settings updated'));
		}
		
		$canUseGroups = 0;
		$groups = Group::getGroups($this->context->language->id, $this->context->shop->id);
		if (count($groups) > 0 && Group::isFeatureActive()) {
			foreach ($groups as $key => $value) {
				if ((int)$value['id_group'] != (int)Configuration::get('PS_UNIDENTIFIED_GROUP') && 
					(int)$value['id_group'] != (int)Configuration::get('PS_CUSTOMER_GROUP') && 
					(int)$value['id_group'] != (int)Configuration::get('PS_GUEST_GROUP')) {
					$canUseGroups = 1;
				}
			}
		}
		$this->smarty->assign(
			array(
				'psVersion17' => version_compare(_PS_VERSION_, '1.7', '>'),
				'moduleDir' => _MODULE_DIR_,
				'translate' => Tools::jsonEncode($this->getTranslates()),
				'canUseGroups' => $canUseGroups
			)
		);
		return $settingsUpdated . $this->display(__FILE__,'BOconfig.tpl') . $this->displayForm();
	}
	
	public function displayForm()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$helper = new Helper();
		$fields_form = array();
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Settings'),
			),
			'input' => array(),
			'submit' => array(
				'title' => $this->l('Save'),
				'name' => 'submitVerifyCustomerData',
			)
		);
		$approveCustomer = array(
			'type'		=>'switch',
			'label'     => $this->l('Customer have to be approved:'),
			'desc'      => $this->l('Customer have to be approved by administrator before he can login to your store and see product prices'),
			'name'      => 'approve_customer',
			'required'  => false,
			'class'     => 'approveCustomer',
			'is_bool'   => true,
			'values'    => array(
				array(
					'id'    => 'approve_customer_on',
					'value' => 1,   
					'label' => $this->l('No')
				),
				array(
					'id'    => 'approve_customer_off',
					'value' => 0,
					'label' => $this->l('Yes')
				)
			)
		);
		array_push($fields_form[0]['form']['input'], $approveCustomer);
		$sendMailAfterApproveToCustomer = array(
			'type'		=>'switch',
			'label'     => $this->l('Send to customer mail after approval:'),
			'desc'      => $this->l('Email will be automatically send to customer after this customer will be approved in back office'),
			'name'      => 'send_mail_after_approve_to_customer',
			'required'  => false,
			'class'     => 'sendMailAfterApproveToCustomer',
			'is_bool'   => true,
			'values'    => array(
				array(
					'id'    => 'send_mail_after_approve_to_customer_on',
					'value' => 1,   
					'label' => $this->l('No')
				),
				array(
					'id'    => 'send_mail_after_approve_to_customer_off',
					'value' => 0,
					'label' => $this->l('Yes')
				)
			)
		);
		array_push($fields_form[0]['form']['input'], $sendMailAfterApproveToCustomer);
		$sendMailAfterRegToAdmin = array(
			'type'		=>'switch',
			'label'     => $this->l('Send mail after registration to employee(s):'),
			'desc'      => $this->l('Email will be automatically send to selected administrators after somebody will register to your store'),
			'name'      => 'send_mail_after_reg_to_admin',
			'required'  => false,
			'class'     => 'sendMailAfterRegToAdmin',
			'is_bool'   => true,
			'values'    => array(
				array(
					'id'    => 'send_mail_after_reg_to_admin_on',
					'value' => 1,   
					'label' => $this->l('No')
				),
				array(
					'id'    => 'send_mail_after_reg_to_admin_off',
					'value' => 0,
					'label' => $this->l('Yes')
				)
			)
		);
		array_push($fields_form[0]['form']['input'], $sendMailAfterRegToAdmin);
		$employees = $this->getEmployeesWithEmails();
		if (count($employees) > 0) {
			foreach ($employees as $key => $value) {
				array_push($fields_form[0]['form']['input'], array(
					'type'		=>'checkbox',
					'label'     => '',
					'name'      => 'employee_'.$value['id_employee'],
					'required'  => false,
					'class'     => 'employees',
					'values'  => array(
						'query' => array(
							array(
								'id_option' => '1',
								'name' => $value['firstname']." ".$value['lastname'].' ('.$value['email'].')'          
							)
						),
						'id'    => 'id_option',
						'name'  => 'name'
					),
				));
			}
		}
		$showUploadButton = array(
	        'type'		=>'switch',
	        'label'     => $this->l('Allow upload file in registration form:'),
	        'desc'      => $this->l('Customer can upload file from his computer in registration form. In back office, in customer detail, you can see and download his uploaded file.'),
	        'name'      => 'show_upload_button',
	        'required'  => false,
	        'class'     => 'showUploadButton',
	        'is_bool'   => true,
	        'values'    => array(
                array(
                    'id'    => 'show_upload_button_on',
                    'value' => 1,
                    'label' => $this->l('No')
                ),
                array(
                    'id'    => 'show_upload_button_off',
                    'value' => 0,
                    'label' => $this->l('Yes')
                )
	        )
		);
		array_push($fields_form[0]['form']['input'], $showUploadButton);
		$uploadFileLabel = array(
	        'type'     => 'text',
	        'label'    => $this->l('Label for upload file field:'),
	        'name'     => 'upload_file_label',
	        'class'    => 'upload_file_label',
	        'required' => false,
	        'lang'		=> true,
	        'desc'     => $this->l('You can choose label of upload file field in each language. This label will be displayed in registration form as name of this field.')
		);
		array_push($fields_form[0]['form']['input'], $uploadFileLabel);
		$uploadFileDescription = array(
	        'type'     => 'text',
	        'label'    => $this->l('Description for upload file field:'),
	        'name'     => 'upload_file_description',
	        'class'    => 'upload_file_description',
	        'required' => false,
	        'lang'		=> true,
	        'desc'     => $this->l('You can choose description for upload file field in each language. This description will be displayed in registration form at the bottom of this field.')
		);
		array_push($fields_form[0]['form']['input'], $uploadFileDescription);
		$uploadFileRequired = array(
	        'type'		=>'switch',
	        'label'     => $this->l('Set upload file as required:'),
	        'desc'      => $this->l('You can choose if upload file will be required for customer and he have to upload file.'),
	        'name'      => 'upload_file_required',
	        'required'  => false,
	        'class'     => 'uploadFileRequired',
	        'is_bool'   => true,
	        'values'    => array(
                array(
                    'id'    => 'upload_file_required_on',
                    'value' => 1,
                    'label' => $this->l('No')
                ),
                array(
                    'id'    => 'upload_file_required_off',
                    'value' => 0,
                    'label' => $this->l('Yes')
                )
	        )
		);
		array_push($fields_form[0]['form']['input'], $uploadFileRequired);
		$uploadFileAllowedFiles = array(
	        'type'     => 'text',
	        'label'    => $this->l('Allow to upload only selected file types:'),
	        'name'     => 'upload_file_allowed_files',
	        'class'    => 'upload_file_allowed_files',
	        'required' => false,
	        'lang'		=> false,
	        'desc'     => $this->l('You can choose extensions, which can be uploaded by upload file field in registration form. You have to write selected extensions to this field separated by comma, for example "jpg,png,pdf". If this fiel will be blank, system automatically allow all files to upload.')
		);
		array_push($fields_form[0]['form']['input'], $uploadFileAllowedFiles);
		$uploadFileMaxFileSize = array(
	        'type'     => 'text',
	        'label'    => $this->l('Maximal size of uploaded file:'),
	        'name'     => 'upload_file_max_file_size',
	        'class'    => 'upload_file_max_file_size',
	        'required' => false,
	        'lang'		=> false,
	        'desc'     => $this->l('You can choose maximal file size in Mega Bytes(MB). You can set for example "2", or "0.5" (with dot), or "0,7" (with comma)')
		);
		array_push($fields_form[0]['form']['input'], $uploadFileMaxFileSize);
		$uploadFilePosition = array(
	        'type'		=>'radio',
	        'label'     => $this->l('Upload file field in registration form are placed:'),
	        'desc'      => $this->l('Position of upload file field in registration form'),
	        'name'      => 'upload_file_position',
	        'required'  => false,
	        'class'     => 'uploadFilePosition',
	        'values'    => $this->getFieldPositions('upload_file')
		);
		array_push($fields_form[0]['form']['input'], $uploadFilePosition);
		$allowChooseCustomGroupToCustomer = array(
			'type'		=>'switch',
			'label'     => $this->l('Customer can join to group(s) in registration:'),
			'desc'      => $this->l('Customer can choose from selected groups to which he wishes to be assigned'),
			'name'      => 'allow_choose_custom_group_to_customer',
			'required'  => false,
			'class'     => 'allowChooseCustomGroupToCustomer',
			'is_bool'   => true,
			'values'    => array(
				array(
					'id'    => 'allow_choose_custom_group_to_customer_on',
					'value' => 1,   
					'label' => $this->l('No')
				),
				array(
					'id'    => 'allow_choose_custom_group_to_customer_off',
					'value' => 0,
					'label' => $this->l('Yes')
				)
			)
		);
		array_push($fields_form[0]['form']['input'], $allowChooseCustomGroupToCustomer);
		$customGroupPosition = array(
			'type'		=>'radio',
			'label'     => $this->l('Customer groups in registration form are placed:'),
			'desc'      => $this->l('Position of customer groups section in registration form'),
			'name'      => 'custom_group_position',
			'required'  => false,
			'class'     => 'customGroupPosition',
			'values'    => $this->getFieldPositions('custom_group')
		);
		array_push($fields_form[0]['form']['input'], $customGroupPosition);
		$customGroupSelectType = array(
			'type'		=>'radio',
			'label'     => $this->l('Customer can select:'),
			'desc'      => $this->l('You can choose if customer can select only one(radion buttons), or more(selects) customer groups to which he wishes to be assigned'),
			'name'      => 'custom_group_select_type',
			'required'  => false,
			'class'     => 'customGroupSelectType',
			'values'    => array(
				array(
					'id'    => 'custom_group_select_type_on',
					'value' => 1,
					'label' => $this->l('Only one of customer groups to join in registration form')
				),
				array(
					'id'    => 'custom_group_select_type_off',
					'value' => 0,
					'label' => $this->l('One or more customer groups to join in registration form')
				)
			)
		);
		array_push($fields_form[0]['form']['input'], $customGroupSelectType);
		$groups = Group::getGroups($this->context->language->id, $this->context->shop->id);
		if (count($groups) > 0 && Group::isFeatureActive()) {
			foreach ($groups as $key => $value) {
				if ((int)$value['id_group'] != (int)Configuration::get('PS_UNIDENTIFIED_GROUP') && 
					(int)$value['id_group'] != (int)Configuration::get('PS_CUSTOMER_GROUP') && 
					(int)$value['id_group'] != (int)Configuration::get('PS_GUEST_GROUP')) {
					array_push($fields_form[0]['form']['input'], array(
						'type'		=>'checkbox',
						'label'     => '',
						'name'      => 'group_'.$value['id_group'],
						'required'  => false,
						'desc'     => '',
						'class'     => 'groups',
						'values'  => array(
							'query' => array(
								array(
									'id_option' => '1',
									'name' => $value['name']           
								)
							),                           
							'id'    => 'id_option',      
							'name'  => 'name'
						),
					));
				}
			}
			foreach ($groups as $key => $value) {
				if ((int)$value['id_group'] != (int)Configuration::get('PS_UNIDENTIFIED_GROUP') &&
					(int)$value['id_group'] != (int)Configuration::get('PS_CUSTOMER_GROUP') &&
					(int)$value['id_group'] != (int)Configuration::get('PS_GUEST_GROUP')) {
						array_push($fields_form[0]['form']['input'], array(
							'type'		=>'checkbox',
							'label'     => '',
							'name'      => 'auto_approve_group_'.$value['id_group'],
							'required'  => false,
							'desc'     => '',
							'class'     => 'autoApprovedGroups',
							'values'  => array(
								'query' => array(
									array(
										'id_option' => '1',
										'name' => $value['name']
									)
								),
								'id'    => 'id_option',
								'name'  => 'name'
							),
						));
					}
			}
		}
		$showProductDetailBox = array(
			'type'		=>'switch',
			'label'     => $this->l('Show this message box in product detail:'),
			'desc'      => $this->l('If you choose "Yes", visitor will see selected message box in product detail, instead of price and add to cart button. If you choose "No", visitor will not see this selected message box, but always, he will not see price and add to cart button'),
			'name'      => 'show_product_detail_box',
			'required'  => false,
			'class'     => 'showProductDetailBox',
			'is_bool'   => true,
			'values'    => array(
				array(
					'id'    => 'show_product_detail_box_on',
					'value' => 1,   
					'label' => $this->l('No')
				),
				array(
					'id'    => 'show_product_detail_box_off',
					'value' => 0,
					'label' => $this->l('Yes')
				)
			)
		);
		array_push($fields_form[0]['form']['input'], $showProductDetailBox);
		$textNotAuthorizedPdField = array(
			'type'     => 'textarea',
			'label'    => $this->l('Message text for not authorized users:'),
			'name'     => 'text_not_authorized_pd',
			'class'    => 'text_not_authorized_pd',
			'required' => false,
			'lang'		=> true,
			'desc'     => $this->l('Text for users who are not in selected groups for showing prices. You can use {REGISTRATION} tag everywhere in this text for including link to registration page. Text of registration text you can set in "Text of link to registration" field.')
		);
		array_push($fields_form[0]['form']['input'], $textNotAuthorizedPdField);
		$linkTextPdField = array(
			'type'     => 'text',
			'label'    => $this->l('Text of link to registration:'),
			'name'     => 'link_text_pd',
			'class'    => 'link_text_pd',
			'required' => false,
			'lang'		=> true,
			'desc'     => $this->l('This text represent text of registration link whitch is include in "Message text for not authorized users" field by {REGISTRATION} tag.')
		);
		array_push($fields_form[0]['form']['input'], $linkTextPdField);
		$backgroundColorPdField = array(
			'type'     => 'color',
			'label'    => $this->l('Background color:'),
			'name'     => 'background_color_pd',
			'class'    => 'backgroundColor_pd',
			'required' => false,
			'desc'     => $this->l('Background color of message text for not authorized users.')
		);
		array_push($fields_form[0]['form']['input'], $backgroundColorPdField);
		$textColorPdField = array(
			'type'     => 'color',
			'label'    => $this->l('Text color:'),
			'name'     => 'text_color_pd',
			'class'    => 'textColor_pd',
			'required' => false,
			'desc'     => $this->l('Text color of message text for not authorized users.')
		);
		array_push($fields_form[0]['form']['input'], $textColorPdField);
		$textSizePdField = array(
			'type'     => 'text',
			'label'    => $this->l('Text size (px):'),
			'name'     => 'text_size_pd',
			'class'    => 'textSize_pd',
			'required' => false,
			'desc'     => $this->l('Text size of message text for not authorized users.')
		);
		array_push($fields_form[0]['form']['input'], $textSizePdField);
		$showBordersPd = array(
			'type'		=>'switch',
			'label'     => $this->l('Show borders:'),
			'desc'      => $this->l('You can show or hide border in message box'),
			'name'      => 'show_borders_pd',
			'required'  => false,
			'class'     => 'showBordersPd',
			'is_bool'   => true,
			'values'    => array(
				array(
					'id'    => 'show_borders_pd_on',
					'value' => 1,   
					'label' => $this->l('No')
				),
				array(
					'id'    => 'show_borders_pd_off',
					'value' => 0,
					'label' => $this->l('Yes')
				)
			)
		);
		array_push($fields_form[0]['form']['input'], $showBordersPd);
		$borderRadiusPd = array(
			'type'		=>'switch',
			'label'     => $this->l('Show border radius:'),
			'desc'      => $this->l('You can choose if message box will have rounded corners'),
			'name'      => 'border_radius_pd',
			'required'  => false,
			'class'     => 'borderRadiusPd',
			'is_bool'   => true,
			'values'    => array(
				array(
					'id'    => 'border_radius_pd_on',
					'value' => 1,   
					'label' => $this->l('No')
				),
				array(
					'id'    => 'border_radius_pd_off',
					'value' => 0,
					'label' => $this->l('Yes')
				)
			)
		);
		array_push($fields_form[0]['form']['input'], $borderRadiusPd);
		$productDetailPositionField = array(
			'type'		=>'radio',
			'label'     => $this->l('Target position of message text in product detail:'),
			'desc'      => $this->l('Position of text for not authorized users in product detail'),
			'name'      => 'product_detail_position',
			'required'  => false,
			'class'     => 'productDetailPosition',
			'values'    => array(
				array(
					'id'    => 'product_detail_position_on',
					'value' => 2,   
					'label' => $this->l('Instead of "add to card" button')
				),
				array(
					'id'    => 'product_detail_position_on',
					'value' => 1,   
					'label' => $this->l('Instead of price')
				),
				array(
					'id'    => 'product_detail_position_off',
					'value' => 0,
					'label' => $this->l('In center (under the short description)')
				)
			)
		);
		array_push($fields_form[0]['form']['input'], $productDetailPositionField);
		$showProductListBox = array(
			'type'		=>'switch',
			'label'     => $this->l('Show this message box in product list:'),
			'desc'      => $this->l('If you choose "Yes", visitor will see selected message box in product list, instead of price and add to cart button. If you choose "No", visitor will not see this selected message box, but always, he will not see price and add to cart button'),
			'name'      => 'show_product_list_box',
			'required'  => false,
			'class'     => 'showProductListBox',
			'is_bool'   => true,
			'values'    => array(
				array(
					'id'    => 'show_product_list_box_on',
					'value' => 1,   
					'label' => $this->l('No')
				),
				array(
					'id'    => 'show_product_list_box_off',
					'value' => 0,
					'label' => $this->l('Yes')
				)
			)
		);
		array_push($fields_form[0]['form']['input'], $showProductListBox);
		$textNotAuthorizedPlField = array(
			'type'     => 'textarea',
			'label'    => $this->l('Message text for not authorized users:'),
			'name'     => 'text_not_authorized_pl',
			'class'    => 'text_not_authorized_pl',
			'required' => false,
			'lang'		=> true,
			'desc'     => $this->l('Text for users who are not in selected groups for showing prices. You can use {REGISTRATION} tag everywhere in this text for including link to registration page. Text of registration text you can set in "Text of link to registration" field.')
		);
		array_push($fields_form[0]['form']['input'], $textNotAuthorizedPlField);
		$linkTextPlField = array(
			'type'     => 'text',
			'label'    => $this->l('Text of link to registration:'),
			'name'     => 'link_text_pl',
			'class'    => 'link_text_pl',
			'required' => false,
			'lang'		=> true,
			'desc'     => $this->l('This text represent text of registration link whitch is include in "Message text for not authorized users" field by {REGISTRATION} tag.')
		);
		array_push($fields_form[0]['form']['input'], $linkTextPlField);
		$backgroundColorPlField = array(
			'type'     => 'color',
			'label'    => $this->l('Background color:'),
			'name'     => 'background_color_pl',
			'class'    => 'backgroundColor_pl',
			'required' => false,
			'desc'     => $this->l('Background color of message text for not authorized users.')
		);
		array_push($fields_form[0]['form']['input'], $backgroundColorPlField);
		$textColorPlField = array(
			'type'     => 'color',
			'label'    => $this->l('Text color:'),
			'name'     => 'text_color_pl',
			'class'    => 'textColor_pl',
			'required' => false,
			'desc'     => $this->l('Text color of message text for not authorized users.')
		);
		array_push($fields_form[0]['form']['input'], $textColorPlField);
		$textSizePlField = array(
			'type'     => 'text',
			'label'    => $this->l('Text size (px):'),
			'name'     => 'text_size_pl',
			'class'    => 'textSize_pl',
			'required' => false,
			'desc'     => $this->l('Text size of message text for not authorized users.')
		);
		array_push($fields_form[0]['form']['input'], $textSizePlField);
		$showBordersPl = array(
			'type'		=>'switch',
			'label'     => $this->l('Show borders:'),
			'desc'      => $this->l('You can show or hide border in message box'),
			'name'      => 'show_borders_pl',
			'required'  => false,
			'class'     => 'showBordersPl',
			'is_bool'   => true,
			'values'    => array(
				array(
					'id'    => 'show_borders_pl_on',
					'value' => 1,   
					'label' => $this->l('No')
				),
				array(
					'id'    => 'show_borders_pl_off',
					'value' => 0,
					'label' => $this->l('Yes')
				)
			)
		);
		array_push($fields_form[0]['form']['input'], $showBordersPl);
		$borderRadiusPl = array(
			'type'		=>'switch',
			'label'     => $this->l('Show border radius:'),
			'desc'      => $this->l('You can choose if message box will have rounded corners'),
			'name'      => 'border_radius_pl',
			'required'  => false,
			'class'     => 'borderRadiusPl',
			'is_bool'   => true,
			'values'    => array(
				array(
					'id'    => 'border_radius_pl_on',
					'value' => 1,   
					'label' => $this->l('No')
				),
				array(
					'id'    => 'border_radius_pl_off',
					'value' => 0,
					'label' => $this->l('Yes')
				)
			)
		);
		array_push($fields_form[0]['form']['input'], $borderRadiusPl);
		$productListPositionField = array(
			'type'		=>'radio',
			'label'     => $this->l('Target position of message text in products categories:'),
			'desc'      => $this->l('Position of text for not authorized users in products categories.'),
			'name'      => 'product_list_position',
			'required'  => false,
			'class'     => 'productListPosition',
			'values'    => array(
				array(
					'id'    => 'product_list_position_on',
					'value' => 1,
					'label' => $this->l('Under the short description')
				),
				array(
					'id'    => 'product_list_position_off',
					'value' => 0,
					'label' => $this->l('Instead of price')
				)
			)
		);
		array_push($fields_form[0]['form']['input'], $productListPositionField);
		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = true;
		$helper->title = $this->displayName;
		$helper->show_toolbar = false;
		$helper->toolbar_scroll = false;
		$helper->submit_action = 'submit'.$this->name;
		$helper->tpl_vars = array(
			'fields_value' => Tools::jsonDecode(Configuration::get($this->name), true),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		
		return $helper->generateForm($fields_form);
	}

	// pre prestu 1.6 je volany tento hook pre zobrazenie linku na uploadnuty subor v detaile customera v BO
	// je to preto ze v 1.6 nie je vo vsetkych verziach implementovany Media::addJsDefL, ktory vklada globalne js premenne
	// cize to treba riesit klasicky cez tpl
	public function hookDisplayBackOfficeTop() {
	    return $this->showCustomerFileInBO();
	}
	
	// pre prestu 1.7 je volany tento hook pre zobrazenie linku na uploadnuty subor v detaile customera v BO
	public function hookActionAdminControllerSetMedia() {
	    $this->showCustomerFileInBO();
	}
	
	public function hookActionCustomerAccountAdd($params)
	{
		$customer = new Customer($this->context->customer->id);
		$verifyCustomerConfigData = Tools::jsonDecode(Configuration::get($this->name), true);
		$selectedGroupsNames = array();
		$selectedGroupsIDs = array();
		if (Tools::getIsset('id_group')) {
			if ((int)Tools::getValue('id_group') > 0) {
				$customer->addGroups(array((int)Tools::getValue('id_group')));	
				$group = new Group((int)Tools::getValue('id_group'));
				if (Validate::isLoadedObject($group)) {
					$selectedGroupsNames[] = $group->name[$this->context->language->id];
					$selectedGroupsIDs[] = (int)$group->id;
				}
			}
		} else if (Tools::getIsset('groups')) {
			if (count(Tools::getValue('groups')) > 0) {
				$customer->addGroups(Tools::getValue('groups'));
				foreach(Tools::getValue('groups') as $key => $value) {
					$group = new Group($value);
					if (Validate::isLoadedObject($group)) {
						$selectedGroupsNames[] = $group->name[$this->context->language->id];
						$selectedGroupsIDs[] = (int)$group->id;
					}
				}
			}
		}
		$isAutoValidatedByGroup = $this->isAutoValidatedByGroup($selectedGroupsIDs);
		if ((int)$verifyCustomerConfigData['approve_customer'] == 1 && !$isAutoValidatedByGroup) {
			$this->context->customer->mylogout();
			$customer->active = 0;
			$customer->update();
			Db::getInstance()->update('customer_verify', array('verify' => 0), '`id_customer` = '.(int)$customer->id);
		}
		if ((int)$verifyCustomerConfigData['send_mail_after_reg_to_admin'] == 1) {
			$employeeEmailsToSend = array();
			foreach ($verifyCustomerConfigData as $key => $value) {
				if (Tools::substr($key, 0, 9) == 'employee_' && $value == 'on') {
					$parseKeys = explode("_", $key);
					$employee = new Employee((int)$parseKeys[1]);
					if (Validate::isLoadedObject($employee)) {
						$employeeEmailsToSend[] = array(
							'email' => $employee->email,
							'name' => $employee->firstname .' '. $employee->lastname,
						);
					}
				}
			}
			$selectedGroups = '';
			if ((int)$verifyCustomerConfigData['allow_choose_custom_group_to_customer'] == 1) {
				if (count($selectedGroupsNames) > 0) {
					$selectedGroups = $this->l('Selected groups by customer').': '.implode(", ", $selectedGroupsNames).'.';
				} else {
					$selectedGroups = $this->l('Customer did not choose any of customer groups.');
				}
			}
			foreach ($employeeEmailsToSend as $key => $value) {
				$approveText = '';
				if ((int)$verifyCustomerConfigData['approve_customer'] == 1) {
					if ($isAutoValidatedByGroup) {
						$approveText = $this->l('Customer was approved automatically.');
					} else {
						$approveText = $this->l('You can approve this customer in your back office in customers list.');
					}
				}
				Mail::Send(
					Configuration::get('PS_LANG_DEFAULT'),
					'new_reg',
					Mail::l('A new customers has registered', Configuration::get('PS_LANG_DEFAULT')),
					array(
						'{customer_email}' => $customer->email, 
						'{customer_name}' => $customer->firstname .' '. $customer->lastname,
						'{employee_name}' => $value['name'],
						'{approve_text}' => $approveText,
						'{selected_groups}' => $selectedGroups,
						'{shopname}' => $this->context->shop->name
					),
					$value['email'],
					$this->context->shop->name,
					NULL,
					$this->context->shop->name,
					NULL,
					NULL,
					dirname(__FILE__).'/mails/'
				);
			}
		}
		if ((int)$verifyCustomerConfigData['approve_customer'] == 1 && !$isAutoValidatedByGroup) {
			Tools::redirect($this->context->link->getModuleLink('verifycustomer', 'verify'));
		}
	}
	
	public function hookActionObjectCustomerAddAfter($params)
	{
		Db::getInstance()->insert('customer_verify', array('id_customer' => (int)$params['object']->id, 'verify' => (int)$params['object']->active));
		$file = Tools::fileAttachment('uploadFile');
		if ($file !== null) {
		    $uploadedResult = $this->uploadFile($file, $params['object']->id);
		    if ($uploadedResult !== false) {
		        Db::getInstance()->insert('customer_verify_files', array('id_customer' => (int)$params['object']->id, 'file_name' => $uploadedResult));
		    }
		}
	}
	
	public function hookActionObjectCustomerUpdateAfter($params)
	{
		$verifyCustomerConfigData = Tools::jsonDecode(Configuration::get($this->name), true);
		$customer = new Customer($params['object']->id);
		$isVerify = Db::getInstance()->getValue('SELECT verify
				FROM `'._DB_PREFIX_.'customer_verify`
				WHERE `id_customer` = '.(int)$customer->id);
		if ($customer->active == 1 && (int)$isVerify == 0) {
			Db::getInstance()->update('customer_verify', array('verify' => 1), '`id_customer` = '.(int)$customer->id);	
			if ((int)$verifyCustomerConfigData['send_mail_after_approve_to_customer'] == 1 && (int)$verifyCustomerConfigData['approve_customer'] == 1) {
				Mail::Send(
					$customer->id_lang,
					'account_activated',
					Mail::l('Your account has been activated', $customer->id_lang),
					array(
						'{email}' => $customer->email,
						'{firstname}' => $customer->firstname,
						'{lastname}' => $customer->lastname, 
						'{shopname}' => $this->context->shop->name
					),
					$customer->email,
					$customer->lastname,
					NULL,
					$this->context->shop->name,
					NULL,
					NULL,
					dirname(__FILE__).'/mails/'
				);
			}
		}
	}
	
	public function hookDisplayCustomerAccountForm($params)
	{
		// pri preste 16 existuje tento hook a vykresli sa pod regitracnym formularom
		// pri preste 17 tento hook neexistuje na prvych verziach a musi sa situacia riesit vlozenim skupin cez js cez hook hookDisplayCustomerAccountFormTop
		$verifyCustomerConfigData = Tools::jsonDecode(Configuration::get($this->name), true);
		if (((int)$verifyCustomerConfigData['custom_group_position'] == 4 && (bool)$verifyCustomerConfigData['allow_choose_custom_group_to_customer'])
	        || ((int)$verifyCustomerConfigData['upload_file_position'] == 4 && (bool)$verifyCustomerConfigData['show_upload_button'])) {
                return $this->displayCustomerGroupsAndUploader('ps16/customerGroupsAndUploader.tpl', 4);
		}
	}
	
	public function hookDisplayCustomerAccountFormTop()
	{
		// pokialide o prestu 16, tneto hook vlozi html so skupinami nad registracny formular
		// pri preste 17 existuje len tento hook a hook na vlozenie grup pod formular neexistuje (hookDisplayCustomerAccountForm), preto sa situacia musi riesit vlozenim skupin cez js cez tento hook
		$verifyCustomerConfigData = Tools::jsonDecode(Configuration::get($this->name), true);
		if (version_compare(_PS_VERSION_, '1.7', '>')) {
			if ((bool)$verifyCustomerConfigData['allow_choose_custom_group_to_customer']) {
				return $this->displayCustomerGroupsAndUploader('ps17/customergroups.tpl');
			}
		} else {
			if (((int)$verifyCustomerConfigData['custom_group_position'] == 1 && (bool)$verifyCustomerConfigData['allow_choose_custom_group_to_customer'])
		        || ((int)$verifyCustomerConfigData['upload_file_position'] == 1 && (bool)$verifyCustomerConfigData['show_upload_button'])) {
					return $this->displayCustomerGroupsAndUploader('ps16/customerGroupsAndUploader.tpl', 1);
			}
		}
	}
	
	public function hookDisplayHeader($params)
	{
		$psVersion17 = version_compare(_PS_VERSION_, '1.7', '>');
		if ($psVersion17) {
			$this->context->controller->registerJavascript('modules-verifycustomer-displaytop', 'modules/'.$this->name.'/views/js/ps17/vcGlobal.js', array('position' => 'bottom', 'priority' => 150));
			// pri preste 17, pre zobrazenie message bloku v detaile produktu
			if ($this->context->controller->getPageName() == 'product') {
				$this->context->controller->registerJavascript('modules-verifycustomer-actionproductoutofstock', 'modules/'.$this->name.'/views/js/ps17/vcProductDetail.js', array('position' => 'bottom', 'priority' => 150));
			}
			// pri preste 17, pre zobrazenie grup a ostylovanie file inputu v registracnom formulary (hore alebo dole)
			if ($this->context->controller->getPageName() == 'authentication') {
			    $verifyCustomerConfigData = Tools::jsonDecode(Configuration::get($this->name), true);
			    $this->context->controller->registerJavascript('modules-verifycustomer-customergroups', 'modules/'.$this->name.'/views/js/ps17/vcCustomerGroups.js', array('position' => 'bottom', 'priority' => 150));
				Media::addJsDefL('fileDescText', $verifyCustomerConfigData['upload_file_description'][$this->context->language->id]);
				Media::addJsDefL('uploadFilePosition', (int)$verifyCustomerConfigData['upload_file_position']);
				$this->context->controller->registerJavascript('modules-verifycustomer-uploadfile', 'modules/'.$this->name.'/views/js/ps17/vcUploadFile.js', array('position' => 'bottom', 'priority' => 150));
			}
		} else {
			$this->context->controller->addJs($this->_path.'views/js/ps16/vcGlobal.js');
		}
		$this->smarty->assign(
			array(
				'psVersion17' => $psVersion17,
				'hidePriceConfig' => Configuration::get($this->name),
				'langId' => $this->context->language->id,
				'defaultCustomerGroup' => $this->context->customer->id_default_group,
				'accountHaveToBeApprove' => $this->l('Your account must be approved by an admin before you can login')
			)
		);
		return $this->display(__FILE__, 'views/templates/hook/vcGlobal.tpl');
	}
	
	public function hookActionProductOutOfStock($params)
	{
		$this->context->controller->addJs($this->_path.'views/js/ps16/vcProductDetail.js');
		$this->smarty->assign(
			array(
				'hidePriceConfig' => Configuration::get($this->name),
				'langId' => $this->context->language->id
			)
		);
		return $this->display(__FILE__, 'views/templates/hook/vcProductDetail.tpl');
	}

	public function hookAdditionalCustomerFormFields($params)
	{
	    $verifyCustomerConfigData = Tools::jsonDecode(Configuration::get($this->name), true);
	    if ((bool)$verifyCustomerConfigData['show_upload_button'] && $this->context->controller->getPageName() == 'authentication') {
	        return array(
                (new FormField())
                ->setName('uploadFile')
                ->setType('file')
                ->setLabel($verifyCustomerConfigData['upload_file_label'][$this->context->language->id])
	        );
	    }
	    return array();
	}
	
	public function hookValidateCustomerFormFields($params)
	{
	    $verifyCustomerConfigData = Tools::jsonDecode(Configuration::get($this->name), true);
	    if ((bool)$verifyCustomerConfigData['show_upload_button']) {
	        $uploadField = $params['fields'][0];
	        $validateUploadedFile = $this->validateUploadFile(Tools::fileAttachment('uploadFile'));
	        if ($validateUploadedFile !== true) {
	            $uploadField->addError($validateUploadedFile);
	        }
	        return $uploadField;
	    }
	    return true;
	}
	
	public function hookActionBeforeSubmitAccount()
	{
	    $verifyCustomerConfigData = Tools::jsonDecode(Configuration::get($this->name), true);
	    if ((bool)$verifyCustomerConfigData['show_upload_button']) {
	        $validateUploadedFile = $this->validateUploadFile(Tools::fileAttachment('uploadFile'));
	        if ($validateUploadedFile !== true) {
	            $this->context->controller->errors[] = $validateUploadedFile;
	        }
	    }
	}
	
	private function isAutoValidatedByGroup($selectedGroupsIDs) {
		$verifyCustomerConfigData = Tools::jsonDecode(Configuration::get($this->name), true);
		if ((int)$verifyCustomerConfigData['approve_customer'] == 1) {
			foreach ($verifyCustomerConfigData as $configKey => $configValue) {
				foreach ($selectedGroupsIDs as $selectedGroupKey => $selectedGroupValue) {
					if ($configKey == 'auto_approve_group_'.$selectedGroupValue.'_1' && $configValue == 'on') {
						return true;
					}
				}
			}
		}
		return false;
	}
	
	private function showCustomerFileInBO() {
	    if ($this->context->controller instanceof AdminCustomersController) {
	        if (!Tools::getValue('ajax') && (int)Tools::getValue('id_customer') > 0) {
	            $useSSL = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
	            $protocol_content = ($useSSL) ? 'https://' : 'http://';
	            $customerFile = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'customer_verify_files WHERE id_customer = '.Tools::getValue('id_customer'));
	            if ($customerFile !== false && file_exists(dirname(__FILE__).'/views/img/customers/' . $customerFile['file_name'])) {
	                $this->context->controller->addJs($this->_path.'views/js/showCustomerFile.js');
	                if (version_compare(_PS_VERSION_, '1.7', '>')) {
	                    Media::addJsDefL('linkToFile', $protocol_content.Tools::getHttpHost().__PS_BASE_URI__.'modules/verifycustomer/views/img/customers/' . $customerFile['file_name']);
	                    Media::addJsDefL('fileTypeText', $this->l('Link to customer uploaded file'));
	                } else {
	                    $this->smarty->assign(
                            array(
                                'linkToFile' => $protocol_content.Tools::getHttpHost().__PS_BASE_URI__.'modules/verifycustomer/views/img/customers/' . $customerFile['file_name'],
                                'fileTypeText' => $this->l('Link to customer uploaded file')
                            )
                        );
	                    return $this->display(__FILE__, 'views/templates/hook/ps16/showCustomerFile.tpl');
	                }
	            }
	        }
	    }
	}
	
	private function validateUploadFile($file) {
	    $verifyCustomerConfigData = Tools::jsonDecode(Configuration::get($this->name), true);
	    if ($file == null) {
            if ((bool)$verifyCustomerConfigData['upload_file_required']) {
                return $this->l('No file was uploaded');
            } else {
                return true;
            }
	    } else {
	        // v prvych verziach prestashopu (napr. v 1607 nie je, v 16014 uz je) nevracia Tools::fileAttachment('uploadFile') paramter size
	        // preto ho tam pridam natvrdo
	        if(!array_key_exists('size', $file)) {
	            $file["size"] = 1;
	        }
            return $this->validFileSizeAndFormat($verifyCustomerConfigData, $file);
        }
	}
	
	private function validFileSizeAndFormat($verifyCustomerConfigData, $file) {
        $maxFileSize = str_replace(",", ".", $verifyCustomerConfigData['upload_file_max_file_size']);
        if (is_numeric($maxFileSize) && (float)$maxFileSize > 0) {
            $maxFileSizeInBytes = ((float)$maxFileSize * 1048576);
        } else {
            $maxFileSizeInBytes = false;
        }
	    if ($maxFileSizeInBytes !== false && $file["size"] > $maxFileSizeInBytes) {
	        return $this->l('The uploaded file exceeds').' '.$maxFileSize.' MB';
	    }
	    
	    $imgName = $file['name'];
	    if (Tools::strlen($imgName) > 0) {
	        $imgExtension = pathinfo($imgName, PATHINFO_EXTENSION);
	        $allowedExtensions = explode(",", trim($verifyCustomerConfigData['upload_file_allowed_files']));
	        foreach($allowedExtensions as $configExtension) {
	            if ($imgExtension == $configExtension) {
	                return true;
	            }
	        }
	        return $this->l('Filetype not allowed').' ('.$verifyCustomerConfigData['upload_file_allowed_files'].')';
	    }
	    return true;
	}
	
	private function uploadFile($file, $customerId) {
	    $fileName = rand(1, 99999).'_'.$customerId.'.'.pathinfo($file['name'], PATHINFO_EXTENSION);
	    if (!move_uploaded_file($file['tmp_name'], dirname(__FILE__).'/views/img/customers/'.$fileName))
	        return false;
        return $fileName;
	}
	
	private function displayUploadForm() {
	    $verifyCustomerConfigData = Tools::jsonDecode(Configuration::get($this->name), true);
	    $this->smarty->assign(
            array(
                'configuration' => $verifyCustomerConfigData,
                'langID' => $this->context->language->id,
            )
        );
	    return $this->display(__FILE__, 'ps16/uploadfile.tpl');
	}
	
	// pri preste 1.6 zobrazuje tato metoda uzivatelske skupiny aj uploader v jedno tpl
	// pri preste 1.7 zobrazuje iba uzivatelske skupiny, pretoze uploader je zobrazovany cez hook hookAdditionalCustomerFormFields
	private function displayCustomerGroupsAndUploader($template, $positionID = 0) {
		$verifyCustomerConfigData = Tools::jsonDecode(Configuration::get($this->name), true);
		$groupsToSelect = array();
		if ((bool)$verifyCustomerConfigData['allow_choose_custom_group_to_customer']) {
    		$canUseGroups = false;
    		$groups = Group::getGroups($this->context->language->id, $this->context->shop->id);
    		if (count($groups) > 0 && Group::isFeatureActive()) {
    			foreach ($groups as $key => $value) {
    				if ((int)$value['id_group'] != (int)Configuration::get('PS_UNIDENTIFIED_GROUP') &&
    					(int)$value['id_group'] != (int)Configuration::get('PS_CUSTOMER_GROUP') &&
    					(int)$value['id_group'] != (int)Configuration::get('PS_GUEST_GROUP')) {
    						$canUseGroups = true;
    				}
    			}
    		}
    		if ($canUseGroups) {
    			if ((int)$verifyCustomerConfigData['allow_choose_custom_group_to_customer'] == 1) {
    				foreach ($verifyCustomerConfigData as $key => $value) {
    					if (Tools::substr($key, 0, 6) == 'group_' && $value == 'on') {
    						$parseKeys = explode("_", $key);
    						$group = new Group((int)$parseKeys[1]);
    						if (Validate::isLoadedObject($group)) {
    							$groupsToSelect[] = array(
    								'id_group' => (int)$parseKeys[1],
    								'name' => $group->name[$this->context->language->id]
    							);
    						}
    					}
    				}
    			}
    		}
		}
		$this->smarty->assign(
			array(
		        'configuration' => $verifyCustomerConfigData,
		        'langID' => $this->context->language->id,
		        'positionID' => $positionID,
				'groups' => $groupsToSelect,
		        'selectedRadioGroup' => Tools::getValue('id_group'),
		        'selectedCheckboxGroups' => Tools::getValue('groups') !== false ? Tools::getValue('groups') : array()
			)
		);
		return $this->display(__FILE__, $template);
	}
	
	private function initVerifyCustomerConfigData()
	{
		$verifyCustomerConfigData = array();
		$verifyCustomerConfigData['approve_customer'] = '0';
		$verifyCustomerConfigData['send_mail_after_reg_to_admin'] = '0';
		$employees = $this->getEmployeesWithEmails();
		if (count($employees) > 0) {
			foreach ($employees as $key => $value) {
				$employee = new Employee($value['id_employee']);
				$verifyCustomerConfigData['employee_'.$value['id_employee'].'_1'] = '';
			}
		}
		$verifyCustomerConfigData['send_mail_after_approve_to_customer'] = '0';
		$verifyCustomerConfigData['allow_choose_custom_group_to_customer'] = '0';
		$verifyCustomerConfigData['custom_group_position'] = '1';
		$verifyCustomerConfigData['custom_group_select_type'] = '0';
		
		$verifyCustomerConfigData['show_upload_button'] = '0';
		$verifyCustomerConfigData['upload_file_required'] = '0';
		$verifyCustomerConfigData['upload_file_allowed_files'] = 'jpg,png,pdf';
		$verifyCustomerConfigData['upload_file_max_file_size'] = '2';
		$verifyCustomerConfigData['upload_file_position'] = '1';
		
		$groups = Group::getGroups($this->context->language->id, $this->context->shop->id);
		if (count($groups) > 0 && Group::isFeatureActive()) {
			foreach ($groups as $key => $value) {
				if ((int)$value['id_group'] != (int)Configuration::get('PS_UNIDENTIFIED_GROUP') && 
					(int)$value['id_group'] != (int)Configuration::get('PS_CUSTOMER_GROUP') && 
					(int)$value['id_group'] != (int)Configuration::get('PS_GUEST_GROUP')) {
					$verifyCustomerConfigData['group_'.$value['id_group'].'_1'] = '';
					$verifyCustomerConfigData['auto_approve_group_'.$value['id_group'].'_1'] = '';
				}
			}
		}
		
		$verifyCustomerConfigData['show_product_list_box'] = '0';
		$verifyCustomerConfigData['show_product_detail_box'] = '0';
		
		foreach (Language::getLanguages() as $key => $value)
		{
			$verifyCustomerConfigData['text_not_authorized_pl'][$value['id_lang']] = 'Price can be display only for {REGISTRATION}';
			$verifyCustomerConfigData['link_text_pl'][$value['id_lang']] = 'registered users';
			$verifyCustomerConfigData['text_not_authorized_pd'][$value['id_lang']] = 'Price can be display only for {REGISTRATION}';
			$verifyCustomerConfigData['link_text_pd'][$value['id_lang']] = 'registered users';
			$verifyCustomerConfigData['upload_file_label'][$value['id_lang']] = 'Upload document';
			$verifyCustomerConfigData['upload_file_description'][$value['id_lang']] = 'You can upload document in jpg, png, or pdf formats';
		}
		$verifyCustomerConfigData['background_color_pl'] = '#00b9e3';
		$verifyCustomerConfigData['text_color_pl'] = '#ffffff';
		$verifyCustomerConfigData['text_size_pl'] = '13';
		$verifyCustomerConfigData['show_borders_pl'] = '0';
		$verifyCustomerConfigData['border_radius_pl'] = '1';
		$verifyCustomerConfigData['background_color_pd'] = '#00b9e3';
		$verifyCustomerConfigData['text_color_pd'] = '#ffffff';
		$verifyCustomerConfigData['text_size_pd'] = '16';
		$verifyCustomerConfigData['show_borders_pd'] = '0';
		$verifyCustomerConfigData['border_radius_pd'] = '1';
		$verifyCustomerConfigData['product_detail_position'] = '0';
		$verifyCustomerConfigData['product_list_position'] = '0';
		return $verifyCustomerConfigData;
	}
	
	private function getTranslates()
	{
		return array(
			'mainSettingsTitle' => $this->l('Main settings'),
			'groupsTitle' => $this->l('Customer can select from these customer groups'),
			'autoApprovedGroupsTitle' => $this->l('If customer select one of these groups, he will be approved automatically'),
			'employeesTitle' => $this->l('Send mail after customer is registred for employees'),
			'productDetailTitle' => $this->l('Message box in product detail for visitors, instead of prices and add to cart button'),
			'productListTitle' => $this->l('Message box in product list for visitors, instead of prices and add to cart button'),
			'noGroups' => $this->l('You have to create one or more custom customer groups to work with this section')
		);
	}
	
	private function getEmployeesWithEmails($active_only = true)
    {
        return Db::getInstance()->executeS('
			SELECT `id_employee`, `firstname`, `lastname`, `email`
			FROM `'._DB_PREFIX_.'employee`
			'.($active_only ? ' WHERE `active` = 1' : '').'
			ORDER BY `lastname` ASC
		');
    }
    
    private function getFieldPositions($fieldName) {
        $fieldPositions = array();
        $fieldPositions[] = array(
            'id'    => $fieldName.'_position_1',
            'value' => 1,
            'label' => $this->l('On the top of registration form')
        );
        if (version_compare(_PS_VERSION_, '1.7', '>')) {
            if ((bool)Configuration::get('PS_B2B_ENABLE')) {
                $fieldPositions[] = array(
                    'id'    => $fieldName.'_position_2',
                    'value' => 2,
                    'label' => $this->l('After company fields')
                );
            }
            $fieldPositions[] = array(
                'id'    => $fieldName.'_position_3',
                'value' => 3,
                'label' => $this->l('After birthday field')
            );
        }
        $fieldPositions[] = array(
            'id'    => $fieldName.'_position_4',
            'value' => 4,
            'label' => $this->l('On the bottom of registration form')
        );
        
        return $fieldPositions;
    }
}
?>