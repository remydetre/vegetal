<?php
/**
* 2007-2018 PrestaShop
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
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PaygreenDatabase
{
    public static function createTable()
    {
        $sql = array();

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paygreen_buttons` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `label` VARCHAR(100) NULL,
            `paymentType` VARCHAR(50) DEFAULT \'CB\',
            `image` VARCHAR(45) NULL,
            `height` INT NULL,
            `position` INT NULL DEFAULT 1,
            `displayType` VARCHAR(45) NULL DEFAULT \'default \',
            `integration` INT NOT NULL DEFAULT 0,
            `nbPayment` INT NOT NULL DEFAULT 1,
            `perCentPayment` INT NULL,
            `subOption` INT DEFAULT 0,
            `reductionPayment` VARCHAR(45) DEFAULT \'none\',
            `minAmount` DECIMAL(10,2) NULL,
            `maxAmount` DECIMAL(10,2) NULL,
            `executedAt` INT NULL DEFAULT 0,
            `reportPayment` VARCHAR(15) DEFAULT NULL,
            `defaultimg` INT DEFAULT 0,
            PRIMARY KEY (`id`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        $sql[] = ' CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paygreen_transactions` (
            `id_cart` int(11) NOT NULL,
            `pid` varchar(250) NOT NULL,
            `id_order` int(11) NOT NULL,
            `state` varchar(50) NOT NULL,
            `type` varchar(50) NOT NULL,
            `created_at` int NOT NULL,
            `updated_at` int NOT NULL,
            PRIMARY KEY (`id_cart`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        $sql[] = ' CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paygreen_recurring_transaction` (
            `id` int(11) NOT NULL,
            `rank` int(11) NOT NULL,
            `pid` varchar(250) NOT NULL,
            `amount` int(11) NOT NULL,
            `state` varchar(50) NOT NULL,
            `type` varchar(50) NOT NULL,
            `date_payment` date NOT NULL,
            PRIMARY KEY (`id`, `rank`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        $sql[] = ' CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paygreen_fingerprint` (
            `fingerprint` varchar(100) NOT NULL,
            `key` varchar(255) NOT NULL,
            `value` varchar(255) NOT NULL,
            `createdAt` datetime NOT NULL,
            `index` varchar(255) NOT NULL
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        $sql[] = ' CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paygreen_transaction_locks` (
            `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `pid` varchar(100) NOT NULL,
            `lockedAt` INT NULL DEFAULT NULL
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        $sql[] = ' ALTER TABLE `'._DB_PREFIX_.'paygreen_transaction_locks` ADD UNIQUE (`pid`)';

        $sql[] = ' CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paygreen_categories_has_payments` (
            `id` int (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `id_category` int (11) NOT NULL,
            `payment` varchar(50) NOT NULL
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        /** @var DbCore $db */
        $db = Db::getInstance();

        foreach ($sql as $query) {
            if ($db->execute($query) === false) {
                $message = $db->getMsgError() . ' - Error on request : ' . $query;
                throw new Exception($message, $db->getNumberError());
            }
        }

        return true;
    }
}
