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

function upgrade_module_1_4_0()
{
	if (!Db::getInstance()->Execute(
		'RENAME TABLE `'._DB_PREFIX_.'pure_product_pictogram` TO `'._DB_PREFIX_.'pureproductpictograms`;'))
		return false;
		
	if (!Db::getInstance()->Execute(
		'RENAME TABLE `'._DB_PREFIX_.'pure_product_pictogram_lang` TO `'._DB_PREFIX_.'pureproductpictograms_product_lang`;'))
		return false;

	if (!Db::getInstance()->Execute(
		'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pureproductpictograms_lang` (
		  `id_pictogram_lang` int(11) NOT NULL AUTO_INCREMENT,
		  `id_pictogram` int(11) NOT NULL,
		  `id_lang` int(11) NOT NULL,
		  `title` varchar(255) NULL,
		  `link_to` varchar(255) NULL,
		  PRIMARY KEY (`id_pictogram_lang`, `id_pictogram`, `id_lang`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'))
		return false;
		
	if (!Db::getInstance()->Execute(
		'ALTER TABLE `'._DB_PREFIX_.'pureproductpictograms_product_lang` CHANGE `id_pictogram_lang` `id_pictogram_product_lang` INT(11) NOT NULL AUTO_INCREMENT;'))
		return false;
	
	$pictograms = Db::getInstance()->ExecuteS('SELECT id_pictogram, title, link_to
			FROM '._DB_PREFIX_.'pureproductpictograms
			WHERE title IS NOT NULL');
			
	foreach ($pictograms as $pictogram)
		if (!Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."pureproductpictograms_lang (id_pictogram, id_lang, title, link_to)
			VALUES ('" . pSQL($pictogram['id_pictogram']) . "',
					'" . pSQL((int)Configuration::get('PS_LANG_DEFAULT')) . "',
					'" . pSQL($pictogram['title']) . "',
					'" . pSQL($pictogram['link_to']) . "')")
			)
			return false;
	
	if(!Db::getInstance()->Execute('ALTER TABLE ' . _DB_PREFIX_ . 'pureproductpictograms DROP COLUMN `title`'))
		return false;
	
	if(!Db::getInstance()->Execute('ALTER TABLE ' . _DB_PREFIX_ . 'pureproductpictograms DROP COLUMN `link_to`'))
		return false;
	
	return true;
}