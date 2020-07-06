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

function upgrade_module_1_5_0()
{
	if (!Db::getInstance()->Execute(
		"ALTER TABLE "._DB_PREFIX_."pureproductpictograms
			ADD show_when_stock TINYINT(1) NOT NULL DEFAULT '1' AFTER file_name,
			ADD show_when_no_stock TINYINT(1) NOT NULL DEFAULT '1' AFTER show_when_stock"))
		return false;
	
	return true;
}