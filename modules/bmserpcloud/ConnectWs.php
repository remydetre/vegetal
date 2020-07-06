<?php
/**
 * 2007-2019 boostmyshop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class ConnectWs
{
    protected static $_token;


    public static function initToken()
    {
        $params = [];
        $params['username'] = self::getLogin();
        $params['password'] = self::getPassword();

        $result = self::callWs("integration/admin/token", "POST", $params);
        if (is_string($result))
            self::$_token = $result;
        else
        {
            if (isset($result->message))
                throw new \Exception($result->message);
            else
                throw new \Exception("Unable to connect");
        }
    }

    public static function callWs($route, $mode = "GET", $params = null)
    {
        $curl = curl_init(self::getEndPoint().'/index.php/rest/V1/'.$route);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $mode);
        if ((($mode == 'POST') || ($mode == 'PUT')) && $params)
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if ($params)
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . Tools::strlen(json_encode($params))));
        if (self::$_token)
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . self::$_token));

        $result = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        switch($httpcode)
        {
            case '401':
                throw new \Exception('Unhautorized (401)');
            case '403':
                throw new \Exception('Forbidden (403)');
        }

        if (!$result)
        {
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            throw new \Exception('Unable to reach route '.$route.' (http code : '.$httpCode.')');
        }
        $result = json_decode($result);
        return $result;
    }

    protected static function getEndPoint()
    {
        return Configuration::get('bmserpcloud_account_server');
    }

    protected static function getLogin()
    {
        return Configuration::get('bmserpcloud_account_login');
    }

    protected static function getPassword()
    {
        return Configuration::get('bmserpcloud_account_password');
    }

}