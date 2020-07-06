<?php
/**
 * 2014 - 2019 Watt Is It
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons BY-ND 4.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://creativecommons.org/licenses/by-nd/4.0/fr/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@paygreen.fr so we can send you a copy immediately.
 *
 * @author    PayGreen <contact@paygreen.fr>
 * @copyright 2014 - 2019 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   2.7.6
 */


class PGLegacyServicesConnexionHandler extends PGFrameworkFoundationsAbstractObject
{
    /**
     *  Authentication and full private key and unique id
     * @throws Exception
     * @throws OAuthException
     */
    public function auth()
    {
        /** @var PGModuleServicesSettings $settings */
        $settings = $this->getService('settings');

        /** @var PGClientServicesApiFacade $apiFacade */
        $apiFacade = $this->getService('paygreen.facade')->getApiFacade();

        /** @var Paygreen $localModule */
        $localModule = $this->getService('local.module');

        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $email = $localModule->getContext()->cookie->email;

        $name = $settings->get('PS_SHOP_NAME');
        $oauth_access = $settings->get('oauth_access');

        if (empty($oauth_access) || $oauth_access = 'null') {
            $datas = $apiFacade->getOAuthServerAccess(
                $email,
                $name,
                $ipAddress
            );

            $encodedData = json_encode($datas);

            $settings->set('oauth_access', $encodedData);
        }

        $CLIENT_ID = json_decode($settings->get('oauth_access'))->data->accessPublic;
        $CLIENT_SECRET = json_decode($settings->get('oauth_access'))->data->accessSecret;

        $client = new OAuthClient($CLIENT_ID, $CLIENT_SECRET, OAuthClient::AUTH_TYPE_AUTHORIZATION_BASIC);

        if (Tools::getValue('code') == null) {
            $REDIRECT_URI = $settings->get('URL_BASE');

            $auth_url = $client->getAuthenticationUrl(
                $apiFacade->getOAuthAutorizeEndpoint(),
                $REDIRECT_URI
            );

            Tools::redirect($auth_url);
        } else {
            $params = array('code' => Tools::getValue('code'), 'redirect_uri' => $settings->get('URL_BASE'));

            $response = $client->getAccessToken(
                $apiFacade->getOAuthTokenEndpoint(),
                'authorization_code',
                $params
            );

            if ($response['result']['success'] == 1) {
                $settings->set('_PG_CONFIG_PRIVATE_KEY', $response['result']['data']['privateKey']);

                $pp = $localModule->isPreprod() ? 'PP' : '';

                $settings->set('_PG_CONFIG_SHOP_TOKEN', $pp.$response['result']['data']['id']);

                $this->getService('logger')->info('OAuth', 'Login');
            } else {
                $stringError = $this->l(
                    'There is a problem with the module PayGreen'.
                    ', please contact the technical supports support@paygreen.fr'
                );

                $localModule->getContext()->controller->errors[] =  $stringError . ' : ' . $response['result']['message'];
            }

            Configuration::deleteByName('oauth_access');
        }
    }

    /**
     * @throws Exception
     */
    public function logout()
    {
        /** @var PGModuleServicesSettings $settings */
        $settings = $this->getService('settings');

        $settings->set('_PG_CONFIG_PRIVATE_KEY', '');
        $settings->set('_PG_CONFIG_SHOP_TOKEN', '');

        $this->getService('logger')->info('OAuth', 'Logout');
    }

    /**
     * @return bool|string
     * @throws Exception
     */
    public function paygreenValidIds()
    {
        /** @var PGModuleServicesSettings $settings */
        $settings = $this->getService('settings');

        /** @var PGClientServicesApiFacade $apiFacade */
        $apiFacade = $this->getService('paygreen.facade')->getApiFacade();

        //_CONFIG_PAYGREEN_VALID_ID
        $cachedValue = $settings->get(PGModuleServicesSettings::_CONFIG_PAYGREEN_VALID_ID);
        $validIdCache = true;

        if (isset($cachedValue) && !empty($cachedValue)) {
            list($timer, $boolValid) = explode('|', $cachedValue);

            if ($timer > strtotime('-1 hours')) {
                $validIdCache = $boolValid;
            }
        }

        if (empty($validIdCache)) {
            $validID = $apiFacade->validIdShop();

            $settings->set(PGModuleServicesSettings::_CONFIG_PAYGREEN_VALID_ID, time().'|'.$validID);

            $validIdCache = $validID;
        }

        return $validIdCache;
    }
}
