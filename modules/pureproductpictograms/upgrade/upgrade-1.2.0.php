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

function upgrade_module_1_2_0()
{
	if (!Db::getInstance()->Execute(
		'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pure_product_pictogram` (
		  `id_pictogram` int(11) NOT NULL AUTO_INCREMENT,
		  `title` varchar(255) NULL,
		  `file_name` varchar(255) NOT NULL,
		  `link_to` varchar(255) NULL,
		  `active` tinyint(11) NOT NULL DEFAULT 1,
		  PRIMARY KEY (`id_pictogram`),
		  UNIQUE KEY (`file_name`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'))
		return false;
		
	if (!Db::getInstance()->Execute(
		'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pure_product_pictogram_lang` (
		  `id_pictogram_lang` int(11) NOT NULL AUTO_INCREMENT ,
		  `id_pictogram` int(11) NOT NULL ,
		  `id_product` int(11) NOT NULL,
		  `id_lang` int(11) NOT NULL,
		  PRIMARY KEY (`id_pictogram_lang`, `id_pictogram`, `id_product`, `id_lang`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'))
		return false;
	
	// List images in pictograms folder and insert them
	$available_imgs = listPictograms();

	foreach ($available_imgs as $available_img)
		if (!Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'pure_product_pictogram (title, file_name)
			VALUES (\'' . pSQL($available_img) . '\', \'' . pSQL($available_img) . '\')'))
			return false;
				
	$pictograms = Db::getInstance()->ExecuteS('SELECT pureproductpictograms, id_product, id_lang
			FROM '._DB_PREFIX_.'product_lang');
			
	if ($pictograms === false)
		return false;
	
	$pictograms_list = array();
	foreach ($pictograms as $pictogram) {
		if (!empty($pictogram['pureproductpictograms'])) {
			$old_pictograms = explode(",", $pictogram['pureproductpictograms']);
			
			foreach($old_pictograms as $old_pictogram) {
				if (!array_key_exists($old_pictogram, $pictograms_list)) {
					$p_pictograms = Db::getInstance()->ExecuteS('SELECT id_pictogram
						FROM '._DB_PREFIX_.'pure_product_pictogram
						WHERE file_name = \'' . pSQL($old_pictogram) . '\'');
					
					// Should only be one, but you never know, let's take the last one...
					foreach ($p_pictograms as $p_pictogram)
						$pictograms_list[$old_pictogram] = $p_pictogram['id_pictogram'];
				}
				
				if (!Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'pure_product_pictogram_lang
					(id_pictogram, id_product, id_lang)
					VALUES (' . (int)$pictograms_list[$old_pictogram] . ', ' . (int)$pictogram['id_product'] . ', ' . (int)$pictogram['id_lang'] . ')'))
					return false;
			}
		}
	}
	
	if(!Db::getInstance()->Execute('ALTER TABLE ' . _DB_PREFIX_ . 'product_lang DROP COLUMN `pureproductpictograms`'))
		return false;
	
	return true;
}

function listPictograms() {
	$p = glob(".."._PS_IMG_ . "pureproductpictograms/*.{jpg,png,gif}", GLOB_BRACE);
	
	for($i = 0; $i < count($p); $i++){
		$exp = explode('/', $p[$i]);
		$p[$i] = end($exp);
	}
		
	sort($p);
	
	$pictograms = array();
	$i = 0;
	
	foreach ($p as &$value) {
		array_push ($pictograms, utf8_encode($value));
		$i++;
	}

	return $pictograms;
}