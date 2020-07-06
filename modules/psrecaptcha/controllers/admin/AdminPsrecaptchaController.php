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

class AdminPsRecaptchaController extends ModuleAdminController
{
    public function ajaxProcessSaveConfig()
    {
        $req = Tools::getValue('form_values');

        foreach ($req as $array) {
            if (!Configuration::updateValue($array['name'], $array['value'])) {
                die('false');
            }
        }
        die('true');
    }

    public function ajaxProcessSaveWhitelist()
    {
        $whitelistConfiguration = array();
        $req = Tools::getValue('form_whitelist_values');

        // count element in form on whitelist
        for ($i = 0; $i < count($req); $i++) {
            if (!empty($req[$i]['value'])) {
                $match = preg_match('/\b(?:\d{1,3}\.){3}\d{1,3}\b/m', $req[$i+1]['value'], $matches);
                if ($match == 1) {
                    $rawData = array(
                        $req[$i]['value'] => $matches[0]
                    );
                } else {
                    die("false");
                }
                $i = $i+1;
                $whitelistConfiguration += $rawData;
            }
        }

        if (Configuration::updateValue('RECAPTCHA-WHITELIST', Tools::jsonEncode($whitelistConfiguration))) {
            die('true');
        }

        die('false');
    }

    public function ajaxDeleteUserFromWhitelist()
    {
        $getWhitelist = (array) Tools::jsonDecode(Configuration::get('RECAPTCHA-WHITELIST'));
        $getUser = Tools::getValue("currentIP");
        if (array_key_exists($getUser, $getWhitelist)) {
            unset($getWhitelist[$getUser]);
            Configuration::updateValue('RECAPTCHA-WHITELIST', Tools::jsonEncode($getWhitelist));
            die('true');
        } else {
            die('false');
        }
    }
}
