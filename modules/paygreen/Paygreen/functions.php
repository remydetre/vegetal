<?php
/**
 * 2014 - 2015 Watt Is It
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
 * @author    PayGreen <contact@paygreen.fr>
 * @copyright 2014-2014 Watt It Is
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop <SA></SA>
 *
 */

function toAbsolutePath($src)
{
    $tokens = array_merge(array(PAYGREEN_MODULE_DIR), explode('/', $src));

    return implode(DIRECTORY_SEPARATOR, $tokens);
}

function pgDump($var)
{
    if (PAYGREEN_ENV === 'DEV') {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}

function pgLog($text, $data = null)
{
    if (PAYGREEN_ENV === 'DEV') {
        if (!is_string($text)) {
            $data = $text;
            $text = null;
        }

        if (class_exists('PaygreenContainer') && class_exists('PaygreenServicesLogger')) {
            PaygreenContainer::getInstance()->get('logger')->debug($text, $data);
        }
    }
}
