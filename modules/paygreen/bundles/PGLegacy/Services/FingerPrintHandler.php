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

class PGLegacyServicesFingerPrintHandler extends PGDomainServicesHandlersFingerprintHandler
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
        /** @var Paygreen $localModule */
        $localModule = $this->getService('local.module');

        /** @var PGClientServicesApiFacade $apiFacade */
        $apiFacade = $this->getService('paygreen.facade')->getApiFacade();

        /** @var AddressCore $buyerAddress */
        $buyerAddress = new Address($localModule->getContext()->cart->id_address_delivery);
        $fp_obj = array();

        $fp_carrier = $this->getCarrierNameById($localModule->getContext()->cart->id_carrier);
        $fp_fingerprint = $this->getFingerprint();
        $fp_datas = $this->getFingerprintDatas($fp_fingerprint);
        $pageDatas = $this->countPageDatas($fp_datas);
        $packageWeight = $localModule->getContext()->cart->getTotalWeight();
        $products = $localModule->getContext()->cart->getProducts(true);

        foreach ($products as $product) {
            if ($product['weight'] == 0) {
                $packageWeight += 1;
            }
        }

        $fp_obj['deviceType'] = (string) $pageDatas['device'];
        $fp_obj['browser'] = (string) $pageDatas['browser'];
        $fp_obj['nbPage'] = (int) $pageDatas['nbPage'];
        $fp_obj['useTime'] = (float) $pageDatas['useTime'] / 1000;
        $fp_obj['nbImage'] = (int) $pageDatas['nbImage'];
        $fp_obj['carrier'] = (string) $fp_carrier;
        $fp_obj['weight'] = (float) $packageWeight;
        $fp_obj['nbPackage'] = (int) 1;
        $fp_obj['fingerprint'] = (int) $fp_fingerprint;
        $fp_obj['clientAddress'] = (string) $buyerAddress->address1 .
            ',' . $buyerAddress->postcode .
            ',' . $buyerAddress->city .
            ',' . $buyerAddress->country;

        foreach ($fp_obj as $key => $value) {
            if (empty($value) || empty($key)) {
                $this->getService('logger')->error('generateFingerprintDatas error key ', $key);

                return false;
            }
        }

        $ret = $apiFacade->sendFingerprintDatas($fp_obj);

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
        /** @var Paygreen $localModule */
        $localModule = $this->getService('local.module');

        $fingerprint = $localModule->getContext()->cookie->fingerprint;

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
