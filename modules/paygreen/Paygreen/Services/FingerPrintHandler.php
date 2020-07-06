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

class PaygreenServicesFingerPrintHandler extends PaygreenObject
{
    /**
     * Insert fingerprint, nbImage and useTime in database
     * @param $data
     */
    public function insertFingerprintData($data)
    {
        $query = array();
        $message = '';

        foreach ($data as $key => $value) {
            if ($key != 'client' && $key != 'startAt') {
                $insert = array(
                    'fingerprint' => pSQL($data['client']),
                    'key'         => pSQL($key),
                    'value'       => pSQL($value),
                    'createdAt'   => pSQL(date('Y-m-d H:i:s')),
                    'index'       => pSQL($data['startAt'])
                );

                $query[] = Db::getInstance()->insert('paygreen_fingerprint', $insert);
            }
        }

        foreach ($query as $q) {
            if (!$q) {
                $message = array('Error' => 'Failed query ' . $q);
            } else {
                $message = array('Success' => 'Query success');
            }
        }

        header('Content-Type: application/json');

        echo json_encode($message);
    }

    public function generateFingerprintDatas()
    {
        /** @var Paygreen $moduleFacade */
        $moduleFacade = $this->getService('facade.module');

        /** @var AddressCore $buyerAddress */
        $buyerAddress = new Address($moduleFacade->getContext()->cart->id_address_delivery);
        $fp_obj = array();

        $fp_carrier = $this->getCarrierNameById($moduleFacade->getContext()->cart->id_carrier);
        $fp_fingerprint = $this->getFingerprint();
        $fp_datas = $this->getFingerprintDatas($fp_fingerprint);
        $pageDatas = $this->countPageDatas($fp_datas);
        $packageWeight = $moduleFacade->getContext()->cart->getTotalWeight();
        $products = $moduleFacade->getContext()->cart->getProducts(true);

        foreach ($products as $product) {
            if ($product['weight'] == 0) {
                $packageWeight += 1;
            }
        }

        $fp_obj['deviceType'] = (string)$pageDatas['device'];
        $fp_obj['browser'] = (string)$pageDatas['browser'];
        $fp_obj['nbPage'] = (int)$pageDatas['nbPage'];
        $fp_obj['useTime'] = (float)$pageDatas['useTime'] / 1000;
        $fp_obj['nbImage'] = (int)$pageDatas['nbImage'];
        $fp_obj['carrier'] = (string)$fp_carrier;
        $fp_obj['weight'] = (float)$packageWeight;
        $fp_obj['nbPackage'] = (int)1;
        $fp_obj['fingerprint'] = (int)$fp_fingerprint;
        $fp_obj['clientAddress'] = (string)$buyerAddress->address1 .
            ',' . $buyerAddress->postcode .
            ',' . $buyerAddress->city .
            ',' . $buyerAddress->country;

        foreach ($fp_obj as $key => $value) {
            if (empty($value) || empty($key)) {
                $this->getService('logger')->error('generateFingerprintDatas error key ', $key);

                return false;
            }
        }

        $ret = PaygreenToolsApiClient::getInstance()->payins->ccarbone($fp_obj);

        return $ret;
    }

    private function countPageDatas($datas)
    {
        $obj = array();
        $foundDevice = false;
        $foundBrowser = false;
        $nbPage = 0;
        $useTime = 0;
        $nbImage = 0;
        $browser = '';
        $device = '';

        foreach ($datas as $data) {
            if (strcmp($data['key'], 'useTime') == 0) {
                ++$nbPage;
                $useTime += (int)$data['value'];
            } elseif (strcmp($data['key'], 'nbImage') == 0) {
                $nbImage += (int)$data['value'];
            } elseif ($foundDevice == false && strcmp($data['key'], 'device') == 0) {
                $device = $data['value'];
                $foundDevice = true;
            } elseif ($foundBrowser == false && strcmp($data['key'], 'browser') == 0) {
                $browser = $data['value'];
                $foundBrowser = true;
            }
        }

        $obj['nbPage'] = $nbPage;
        $obj['useTime'] = $useTime;
        $obj['nbImage'] = $nbImage;
        $obj['browser'] = $browser;
        $obj['device'] = $device;

        return $obj;
    }

    private function getFingerprint()
    {
        /** @var Paygreen $moduleFacade */
        $moduleFacade = $this->getService('facade.module');

        $fingerprint = $moduleFacade->getContext()->cookie->fingerprint;

        if (empty($fingerprint)) {
            return 0;
        }

        return $fingerprint;
    }

    private function getCarrierNameById($id_carrier)
    {
        /** @var CarrierCore $carrier */
        $carrier = new Carrier($id_carrier);

        return $carrier->name;
    }

    private function getFingerprintDatas($fingerprint)
    {
        $datas = Db::getInstance()->executeS(
            'SELECT `key`, `value` FROM ' . _DB_PREFIX_ . 'paygreen_fingerprint
            WHERE fingerprint= ' . pSQL($fingerprint) . ';'
        );

        $fpDatas = array();

        foreach ($datas as $data) {
            array_push($fpDatas, array('key' => $data['key'], 'value' => $data['value']));
        }

        return $fpDatas;
    }
}
