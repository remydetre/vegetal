<?php
/**
 * Product Pictograms module
 *
 * @author Jonathan Gaudé
 * @copyright 2018
 * @license Commercial
 */

if (!defined('_PS_VERSION_'))
	exit;

class PureProductPictograms extends Module
{
	protected $_errors;
	private $IMG_DIR_NAME;
	private $IMG_DIR;
	
	public function __construct()
	{
		
		$this->IMG_DIR_NAME = 'pureproductpictograms';
		$this->IMG_DIR = _PS_IMG_ . 'pureproductpictograms';
	
		$this->name = 'pureproductpictograms';
		$this->tab = 'front_office_features';
		$this->version = '1.5.1';
		$this->author = 'Jonathan Gaudé';
		$this->module_key = '20936abc927c1b02797777e4b3319406';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.5.6.2', 'max' => _PS_VERSION_); 
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Product Pictograms');
		$this->description = $this->l('Add pictograms to your product page');
	}
	
	public function install()
	{
		if (!parent::install() ||
			!$this->alterTable('add') ||
			!$this->registerHook('actionAdminControllerSetMedia') ||
			!$this->registerHook('actionProductUpdate') ||
			!$this->registerHook('header') ||
			!$this->registerHook('displayAdminProductsExtra') ||
			!$this->registerHook('displayRightColumnProduct') ||
			!$this->registerHook('displayProductAdditionalInfo') ||
			!$this->registerHook('displayPureProductPictograms'))
			return false;
		return true;
	}
	
	public function uninstall()
	{
		if (!parent::uninstall() || !$this->alterTable('remove'))
			return false;
		return true;
	}


	public function alterTable($method)
	{
		switch ($method) {
			case 'add':
				$sql = "CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."pureproductpictograms (
						  id_pictogram int(11) NOT NULL AUTO_INCREMENT,
						  file_name varchar(255) NOT NULL,
						  show_when_stock tinyint(1) NOT NULL DEFAULT '1',
						  show_when_no_stock tinyint(1) NOT NULL DEFAULT '1',
						  active tinyint(11) NOT NULL DEFAULT 1,
						  PRIMARY KEY (id_pictogram),
						  UNIQUE KEY (file_name)
						) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8; ";
				
				$sql .= "CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."pureproductpictograms_lang (
						  id_pictogram_lang int(11) NOT NULL AUTO_INCREMENT,
						  id_pictogram int(11) NOT NULL,
						  id_lang int(11) NOT NULL,
						  title varchar(255) NULL,
						  link_to varchar(255) NULL,
						  PRIMARY KEY (id_pictogram_lang, id_pictogram, id_lang)
						) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8; ";
				
				$sql .= "CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."pureproductpictograms_product_lang (
						  id_pictogram_product_lang int(11) NOT NULL AUTO_INCREMENT ,
						  id_pictogram int(11) NOT NULL ,
						  id_product int(11) NOT NULL,
						  id_lang int(11) NOT NULL,
						  PRIMARY KEY (id_pictogram_product_lang, id_pictogram, id_product, id_lang)
						) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8; ";
						
					if(!Db::getInstance()->Execute($sql))
						return false;
				break;
			
			case 'remove':
				$sql = '';
				break;
		}

		return true;
	}

	public function prepareNewTab()
	{
		if(($pictogramsUpdated = $this->updatePictogramsList()) !== true)
			return $pictogramsUpdated;
	}
	
	private function updatePictogramsList()
	{
		// List pictograms in db and in pictograms folder
		$available_imgs = $this->listPictogramImages();
		$pictograms_in_db = $this->listPictograms();
		$pictograms_in_db_filenames = array();
		$available_imgs_filenames = array();
		
		// List filenames in db
		foreach($pictograms_in_db as $pictogram_in_db)
			$pictograms_in_db_filenames[] = $pictogram_in_db['file_name'];
		// List filenames in pictograms folder
		foreach ($available_imgs as $available_img)
			$available_imgs_filenames[] = $available_img[0];
		
		// Delete non-existent images:
		foreach ($pictograms_in_db as $pictogram_in_db)
		{
			if (!in_array($pictogram_in_db['file_name'], $available_imgs_filenames))
			{
				try {
					Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."pureproductpictograms WHERE id_pictogram = '" . (int)$pictogram_in_db['id_pictogram'] . "'");
				}
				catch (Exception $e) {
					return $this->displayError($this->l('An error occurred while attempting to delete old pictograms.')
						. "<br>" . $e->getMessage());
				}
			}
		}
		
		// Insert new pictograms
		foreach ($available_imgs as $available_img)
		{
			if (!in_array($available_img[0], $pictograms_in_db_filenames))
			{
				try {
					Db::getInstance()->Execute("
						INSERT INTO "._DB_PREFIX_."pureproductpictograms (file_name)
						VALUES ('" . pSQL($available_img[0]) . "')");
				}
				catch (Exception $e) {
					return $this->displayError($this->l('An error occurred while attempting to update the pictograms.')
						. "<br>" . $e->getMessage());
				}
			}
		}
		
		return true;
	}

	public function hookDisplayAdminProductsExtra($params)
	{		
		if (_PS_VERSION_ > '1.7')
			$id_product = $params['id_product'];
		else
			$id_product = (int)Tools::getValue('id_product');
		
		$pictograms_for_this_product = $this->getPictogramsByProductId($id_product);
		$pictograms_available = $this->listPictograms();
		
		$ids_pictograms_for_this_product = array();
		foreach($pictograms_for_this_product as $pictogram_for_this_product)
			array_push($ids_pictograms_for_this_product, $pictogram_for_this_product['id_pictogram']);
		
		$this->context->smarty->assign(array(
			'pureproductpictograms' => $pictograms_for_this_product,
			'languages' => Language::getLanguages(true, $this->context->shop->id),
			'default_language' => (int)Configuration::get('PS_LANG_DEFAULT'),
			'current_language' => (int)$this->context->language->id,
			'pictograms_available' => $pictograms_available,
			'ids_pictograms_for_this_product' => $ids_pictograms_for_this_product,
			'pictograms_img_dir' => $this->IMG_DIR,
			'pictograms_img_dir_name' => $this->IMG_DIR_NAME
		));
		
		if (Validate::isLoadedObject(new Product($id_product)))
		{
			$this->prepareNewTab();
			if (is_dir ("../img/" . $this->IMG_DIR_NAME) || mkdir ("../img/" . $this->IMG_DIR_NAME, 0755))
				return $this->display(__FILE__, '/views/templates/admin/pureproductpictograms.tpl');
			else {
				return $this->display(__FILE__, '/views/templates/admin/noimgfolder.tpl');
			}
		}
		
		return $this->display(__FILE__, '/views/templates/admin/save_product_first.tpl');
	}	

	public function hookActionAdminControllerSetMedia($params)
	{		
		// Add necessary javascript to products back office
		if($this->context->controller->controller_name == 'AdminProducts')
		{
			$this->context->controller->addJS($this->_path.'/views/js/admin/pureproductpictograms.js');
			$this->context->controller->addCSS($this->_path.'/views/css/admin/pureproductpictograms.css', 'all');
		}
	}

	public function hookActionProductUpdate($params)
	{
		if (_PS_VERSION_ > '1.7')
			$id_product = $params['id_product'];
		else
			$id_product = Tools::getValue('id_product');
		
		if(!Db::getInstance()->delete('pureproductpictograms_product_lang', 'id_lang = ' . (int)$this->context->language->id .' AND id_product = ' .(int)$id_product))
			$this->context->controller->_errors[] = Tools::displayError($this->l('Unable to delete the pictogram. Please check your SQL configuration.'));

		$input = Tools::getValue('pureproductpictograms');
		$pictograms = explode(',', $input[$this->context->language->id]);
		$pictograms = array_filter($pictograms);

		foreach($pictograms as $id_pictogram)
			if(!Db::getInstance()->insert('pureproductpictograms_product_lang',
				array('id_pictogram' => (int)$id_pictogram,
					'id_product' => (int)$id_product,
					'id_lang' => (int)$this->context->language->id)))
				$this->context->controller->_errors[] = Tools::displayError($this->l('Unable to insert the pictogram. Please check your SQL configuration.'));
	}
	
	public function hookHeader()
	{
		$this->context->controller->addCSS($this->_path.'/views/css/front/pureproductpictograms.css', 'all');
	}
	
	public function hookDisplayRightColumnProduct($params)
	{
		return $this->hookDisplayPureProductPictograms($params);
	}
	
	public function hookDisplayProductAdditionalInfo($params)
	{
		return $this->hookDisplayPureProductPictograms($params);
	}
	
	public function hookDisplayPureProductPictograms($params)
	{
		if (isset ($params['product']))
			$id_product = $params['product']['id_product'];
		else
			$id_product = Tools::getValue('id_product');
			
		$pictograms = $this->getPictogramsByProductId((int)$id_product, true);

		$this->context->smarty->assign(array(
			'product' => $params['product'],
			'pictograms' => $pictograms,
			'pictograms_img_dir' => $this->IMG_DIR,
			'pictograms_img_dir_name' => $this->IMG_DIR_NAME
		));

		return ($this->display(__FILE__, '/views/templates/front/pictograms.tpl'));
	}

	public function getPictogramsByProductId($id_product, $get_default_if_not_found = false)
	{
		$sql = function($id_language_pictograms, $id_language_text) use($id_product) {
			$string = 'SELECT p.id_pictogram, p.file_name, p.show_when_stock, p.show_when_no_stock, pl.link_to, pl.title
			FROM '._DB_PREFIX_.'pureproductpictograms_product_lang AS ppl
			LEFT JOIN '._DB_PREFIX_.'pureproductpictograms AS p ON p.id_pictogram = ppl.id_pictogram
			LEFT JOIN '._DB_PREFIX_.'pureproductpictograms_lang AS pl ON p.id_pictogram = pl.id_pictogram
				AND pl.id_lang = ' . (int)$id_language_text . '
				WHERE ppl.id_product = ' . (int)$id_product . '
				AND p.active = 1
				AND ppl.id_lang = ' . (int)$id_language_pictograms . '
				ORDER BY ppl.id_pictogram_product_lang';
			
			return $string;
		};
		
		if (!$get_default_if_not_found)
		{
			// Try getting pictograms in current shop language, and the translated text
			$pictograms = Db::getInstance()->ExecuteS(
				$sql($this->context->language->id, $this->context->language->id)
			);
		}
		else
		{
			// Try getting pictograms in current shop language, and the translated text
			$pictograms = Db::getInstance()->ExecuteS(
				$sql($this->context->language->id, $this->context->language->id, true)
			);
			
			// Otherwise get the pictograms in the default shop language, but with translated text
			if(!$pictograms)
				$pictograms = Db::getInstance()->ExecuteS(
					$sql(Configuration::get('PS_LANG_DEFAULT'), $this->context->language->id, true)
				);
			
			// Finally try getting the pictograms in the default shop language, with the default language text
			if(!$pictograms)
				$pictograms = Db::getInstance()->ExecuteS(
					$sql(Configuration::get('PS_LANG_DEFAULT'), Configuration::get('PS_LANG_DEFAULT'))
				);
		}
		
		// If still nothing is found, return an empty array
		if(!$pictograms)
			return array();

		return $pictograms;
	}
	
	public function getContent()
	{
		$this->_html  = '';
		if (Tools::isSubmit('updatepureproductpictogramslist'))
		{
			$this->_html .= $this->renderPictogramForm((int)Tools::getValue('id_pictogram'));
		}
		else
		{
			if (Tools::isSubmit('deletepureproductpictogramslist'))
			{
				$pictogram = $this->getPictogramById((int)Tools::getValue('id_pictogram'));
				unlink("../img/" . $this->IMG_DIR_NAME . '/' . $pictogram['file_name']);
				
				$this->_html .= $this->displayConfirmation($this->l('The pictogram has been deleted.'));
			}
			
			$this->_html .= $this->postProcess();
			
			// Tests:
			if (is_dir ("../img/" . $this->IMG_DIR_NAME) || mkdir ("../img/" . $this->IMG_DIR_NAME, 0755))
				$this->_html .= $this->renderUploadForm((int)Tools::getValue('id_pictogram'));
			else
				$this->_html .= $this->displayError($this->l('Unable to create the pictograms folder on the server. Please check for write permissions on the /img directory.'));
			
			$this->_html .= $this->renderPictogramsListForm();
		}

		return $this->_html;
	}

	public function postProcess()
	{
		if (Tools::isSubmit('savePictogram'))
		{
			$languages = $this->context->controller->getLanguages(true, $this->context->shop->id);
			foreach($languages as $language)
			{
				$pictogram = Db::getInstance()->ExecuteS("SELECT id_pictogram_lang
					FROM "._DB_PREFIX_."pureproductpictograms_lang
					WHERE id_pictogram = " . (int)Tools::getValue('id_pictogram') . "
						AND id_lang = " . (int)$language['id_lang']);
			
				if($pictogram)
				{
					Db::getInstance()->update(
						'pureproductpictograms',
						array(
							'show_when_stock' => pSQL(Tools::getValue('show_when_stock')),
							'show_when_no_stock' => pSQL(Tools::getValue('show_when_no_stock'))
						),
						"id_pictogram = " . (int)Tools::getValue('id_pictogram'));
						
					Db::getInstance()->update(
						'pureproductpictograms_lang',
						array(
							'title' => pSQL(Tools::getValue('title' . '_' . $language['id_lang'])),
							'link_to' => pSQL(Tools::getValue('link_to' . '_' . $language['id_lang']))
						),
						"id_pictogram = " . (int)Tools::getValue('id_pictogram') . " AND id_lang = " . (int)$language['id_lang']);
				}
				else
				{
					Db::getInstance()->Execute("
						INSERT INTO "._DB_PREFIX_."pureproductpictograms_lang (id_pictogram, id_lang, title, link_to)
						VALUES ('" . (int)Tools::getValue('id_pictogram') . "',
							'" . (int)$language['id_lang'] . "',
							'" . pSQL(Tools::getValue('title' . '_' . $language['id_lang'])) . "',
							'" . pSQL(Tools::getValue('link_to' . '_' . $language['id_lang'])) . "'
						)");
				}
			}
			
			return $this->displayConfirmation($this->l('The pictogram has been saved.'));
		}
		elseif (Tools::isSubmit('uploadPictogram'))
		{
			/* Uploads image and sets slide */
			$type = Tools::strtolower(Tools::substr(strrchr($_FILES['image']['name'], '.'), 1));
			$imagesize = @getimagesize($_FILES['image']['tmp_name']);
			
			if (!isset($_FILES['image'])
				|| !isset($_FILES['image']['tmp_name'])
				|| empty($_FILES['image']['tmp_name'])
				|| empty($imagesize))
				return $this->displayError($this->l('Unable to upload the pictogram (code 1).'));
	
			if (!in_array(
					Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)), array(
						'jpg',
						'gif',
						'jpeg',
						'png'
					))
				&& !in_array($type, array('jpg', 'gif', 'jpeg', 'png')))
				return $this->displayError($this->l('Incorrect file type.'));
			
			if ($error = ImageManager::validateUpload($_FILES['image']))
				return $this->displayError($error);
			
			if (!move_uploaded_file($_FILES['image']['tmp_name'], '../img/' . $this->IMG_DIR_NAME . '/' . time() . '.' . $type))
				return $this->displayError($this->l('Unable to upload the pictogram (code 2).'));
			
			return $this->displayConfirmation($this->l('The pictogram has been successfully uploaded.'));
		}
		
		return '';
	}
	
	public function renderPictogramsListForm()
	{
		if(($pictogramsUpdated = $this->updatePictogramsList()) !== true)
			return $pictogramsUpdated;
		
		$pictograms = $this->listPictograms();

		$fields_list = array(
			'id_pictogram' => array(
				'title' => $this->l('ID'),
				'type' => 'text',
			),
			'file_name' => array(
				'title' => $this->l('Image'),
				'type' => 'text',
				'prefix' => '<img src="' . $this->IMG_DIR . '/',
				'suffix' => '" width="64" height="64">',
			),
			'title' => array(
				'title' => $this->l('Title'),
				'type' => 'text',
			),
			'active' => array(
				'title' => $this->l('Status'),
				'active' => 'status',
				'type' => 'bool',
			),
		);

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->actions = array('edit', 'delete');
		$helper->show_toolbar = true;
		$helper->module = $this;
		$helper->identifier = 'id_pictogram';
		$helper->title = $this->l('Pictograms');
		$helper->table = $this->name.'list';
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

		return $helper->generateList($pictograms, $fields_list);
	}
	
	public function renderUploadForm()
	{
		// Get default language
		$default_lang = (int)$this->context->language->id;

		// Init Fields form array
		$fields_form = array();
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Upload a pictogram') ,
				'icon' => 'icon-cogs'
			) ,
			'input' => array(
				array(
					'type' => 'file',
					'label' => $this->l('Image'),
					'name' => 'image',
					'required' => true,
					'lang' => true,
					'desc' => $this->l('Maximum image size') . ': ' . ini_get('upload_max_filesize')
						. '<br>' . $this->l('Supported file types : jpg, png, gif')
				)
			) ,
			'submit' => array(
				'title' => $this->l('Save')
			)
		);
		
		$helper = new HelperForm();

		// Module, token and currentIndex
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

		// Language
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;

		// Title and toolbar
		$helper->title = $this->displayName;
		$helper->show_toolbar = true; // false -> remove toolbar
		$helper->toolbar_scroll = true; // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action = 'uploadPictogram';
		
		return $helper->generateForm($fields_form);
	}
	
	public function renderPictogramForm($id_pictogram)
	{
		$pictogram = $this->getPictogramById($id_pictogram);

		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Pictograms'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'hidden',
						'lang' => true,
						'name' => 'id_pictogram',
					),
					array(
						'type' => 'text',
						'lang' => true,
						'label' => $this->l('Title'),
						'name' => 'title',
						'desc' => $this->l('Pictogram title to be displayed on mouse over.')
					),
					array(
						'type' => 'text',
						'lang' => true,
						'label' => $this->l('URL'),
						'name' => 'link_to',
						'desc' => $this->l('Link to a page of the site where to redirect the user after clicking the pictogram. Leave empty to disable.')
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Display when there is stock'),
						'name' => 'show_when_stock',
						'is_bool' => true,
						// 'required' => true,
						'desc' => $this->l('Show the pictogram when there is stock available for the current product'),
						'values' => array(
							array(
								'id' => 'show_when_stock_yes',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'show_when_stock_no',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						)
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Display when there is no stock'),
						'name' => 'show_when_no_stock',
						'is_bool' => true,
						// 'required' => true,
						'desc' => $this->l('Show the pictogram when there is no stock available for the current product'),
						'values' => array(
							array(
								'id' => 'show_when_no_stock_yes',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'show_when_no_stock_no',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						)
					)
				),
				'submit' => array(
					'title' => $this->l('Save')
				)
			),
		);
		
		// Getting all strings for that pictogram
		$langs = $this->getPictogramLangByPictogramId($id_pictogram);
		$langs_arr = array();
		foreach($langs as $lang)
			$langs_arr[$lang['id_lang']] = $lang;
		$langs = $langs_arr;
		
		// Initializing form
		$helper = new HelperForm();
		
		// Setting up default values
		$helper->fields_value['id_pictogram'] = $id_pictogram;
		$helper->fields_value['show_when_stock'] = $pictogram['show_when_stock'];
		$helper->fields_value['show_when_no_stock'] = $pictogram['show_when_no_stock'];
		
		$languages = $this->context->controller->getLanguages(true, $this->context->shop->id);
		foreach($languages as $language)
		{
			if (isset($langs[$language['id_lang']]))
			{
				// Assigning form values per language id
				$helper->fields_value['title'][$language['id_lang']] = $langs[$language['id_lang']]['title'];
				$helper->fields_value['link_to'][$language['id_lang']] = $langs[$language['id_lang']]['link_to'];
			}
			else
				$helper->fields_value['title'][$language['id_lang']]
					= $helper->fields_value['link_to'][$language['id_lang']] = '';
		}
		
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->default_form_language = $this->context->language->id;
		$helper->module = $this;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'savePictogram';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'uri' => $this->getPathUri(),
			'languages' => $languages,
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}
	
	private function getPictogramById($id)
	{
		$pictogram = Db::getInstance()->ExecuteS('SELECT *
			FROM '._DB_PREFIX_.'pureproductpictograms
			WHERE id_pictogram = ' . (int)$id);
		
		if(!$pictogram)
			return null;

		return $pictogram[0];
	}
	
	private function getPictogramLangByPictogramId($id_pictogram)
	{
		$langs = Db::getInstance()->ExecuteS(
			'SELECT * FROM '._DB_PREFIX_.'pureproductpictograms_lang
			WHERE id_pictogram = ' . (int)$id_pictogram);
		
		if(!$langs)
			return array();

		return $langs;
	}
	
	private function getPictogramProductLangByLangId($id_lang)
	{
		$pictogram = Db::getInstance()->ExecuteS(
			'SELECT * FROM '._DB_PREFIX_.'pureproductpictograms_product_lang AS plang
			LEFT JOIN '._DB_PREFIX_.'pureproductpictograms AS p ON p.id_pictogram = plang.id_pictogram
			WHERE plang.id_pictogram_product_lang = ' . (int)$id_lang);
		
		if(!$pictogram)
			return null;

		return $pictogram[0];
	}
	
	private function listPictograms($id_lang = null)
	{
		if (is_null($id_lang))
			$id_lang = $this->context->language->id;
		
		$pictograms = Db::getInstance()->ExecuteS('SELECT p.id_pictogram, p.file_name, p.active, pl.title
			FROM '._DB_PREFIX_.'pureproductpictograms AS p
			LEFT OUTER JOIN '._DB_PREFIX_.'pureproductpictograms_lang AS pl
				ON pl.id_pictogram = p.id_pictogram
					AND pl.id_lang = ' . (int)$id_lang . '
			GROUP BY p.id_pictogram');
			
		if(!$pictograms)
			return array();

		return $pictograms;
	}
	
	private function listPictogramsByLangId($lang_id)
	{
		$pictograms = Db::getInstance()->ExecuteS(
			'SELECT p.id_pictogram, ppl.id_product, pl.title, pl.link_to
			FROM '._DB_PREFIX_.'pureproductpictograms_product_lang AS ppl
			LEFT JOIN '._DB_PREFIX_.'pureproductpictograms AS p ON p.id_pictogram = ppl.id_pictogram
			LEFT JOIN '._DB_PREFIX_.'pureproductpictograms_lang AS pl ON pl.id_pictogram = ppl.id_pictogram
				AND pl.id_lang = ' . (int)$lang_id . '
			WHERE ppl.id_lang = ' . (int)$lang_id . '
			ORDER BY ppl.id_pictogram ASC');
		
		if(!$pictograms)
			return array();

		return $pictograms;
	}
	
	private function listPictogramImages()
	{
		$p = glob("../img/" . $this->IMG_DIR_NAME . "/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
		
		for($i = 0; $i < count($p); $i++)
		{
			$exp = explode('/', $p[$i]);
			$p[$i] = end($exp);
		}
			
		sort($p);
		
		$pictograms = array();
		$i = 0;
		
		foreach ($p as &$value) {
			// If the filename isn't UTF-8, encode it
			if (!mb_detect_encoding($value, 'UTF-8', true))
				$value = utf8_encode($value);
			
			array_push ($pictograms, array($value, '0', $i));
			$i++;
		}

		return $pictograms;
	}
}
