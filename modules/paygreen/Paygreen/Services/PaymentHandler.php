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

class PaygreenServicesPaymentHandler extends PaygreenObject
{
    const CASH_PAYMENT = 0;
    const SUB_PAYMENT = 1;
    const DEL_PAYMENT = -1;
    const REC_PAYMENT = 3;

    /**
     * Create payment with data
     * @param $executedAt
     * @param $payment
     * @param $displayType
     * @return |null
     * @throws Exception
     */
    public function createPayment($executedAt, $payment, $displayType)
    {
        /** @var PaygreenServicesButtonHandler $settings */
        $buttonHandler = $this->getService('handler.button');

        /** @var Paygreen $moduleFacade */
        $moduleFacade = $this->getService('facade.module');

        /** @var PaygreenServicesFingerPrintHandler $fingerPrintHandler */
        $fingerPrintHandler = $this->getService('handler.fingerprint');

        /** @var PaygreenSettings $settings */
        $settings = $this->getService('settings');

        $this->getService('logger')->debug('CreatePayment paiement', $payment);

        $buttons = $buttonHandler->getButtonsList();

        foreach ($buttons as $butn) {
            if ($butn['id'] == $payment['dataBtn']) {
                $btn = $butn;
                continue;
            }
        }

        if (PAYGREEN_ENV === 'DEV') {
            $idShop = $moduleFacade->generateUniqueIdShop();
            $settings->set('_ID_UNIQUE_SHOP', $idShop);
        }

        $orderId = $payment['cart_id'].'-'.$settings->get('_ID_UNIQUE_SHOP');

        $paymentData = $this->generatePaiementData(
            $orderId,
            $btn['nbPayment'],
            $payment['amount'],
            $payment['currency'],
            $btn['executedAt'],
            $btn['reportPayment'],
            $btn['perCentPayment']
        );

        $paymentData->customer(
            $payment['cust_id'],
            $payment['cust_lastName'],
            $payment['cust_firstName'],
            $payment['cust_email']
        );

        /** @var AddressCore $address */
        $address = new Address($moduleFacade->getContext()->cart->id_address_delivery);

        $paymentData->shippingTo(
            $address->lastname,
            $address->firstname,
            $address->address1,
            $address->address2,
            $address->company,
            $address->postcode,
            $address->city,
            $address->country
        );

        $paymentData->cart_id = $payment['cart_id'];

        if ($moduleFacade->vPresta >= 1.7) {
            $shopInfo = $moduleFacade->infoAccount();

            $payment['solidarityType'] = $shopInfo->solidarityType;

            $carbon = null;

            if ($shopInfo->solidarityType == 'CCARBONE') {
                $carbon = $fingerPrintHandler->generateFingerprintDatas();
            }

            if (!empty($carbon) && $carbon != false) {
                if (isset($carbon->data) && $carbon->data->idFingerprint) {
                    $paymentData->fingerprint($carbon->data);
                }
            }
        }

        $moduleFacade->getUrlReturn($payment['dataBtn'], $paymentData);

        $paymentModeMethod = $this->getPaiementModeMethod($executedAt);

        $requestData = $this->setPaymentData($paymentData, $displayType, $paymentModeMethod);

        $requestData['paymentType'] = $btn['paymentType'];

        /** @var PaygreenServicesManagersOrderManager $orderManager */
        $orderManager = PaygreenContainer::getInstance()->get('manager.order');

        /** @var CartCore $cart */
        $cart = $moduleFacade->getContext()->cart;

        $eligible_amount = (int) $orderManager->getEligibleAmount($cart, $btn['paymentType']);

        $requestData['eligibleAmount'] = array(
            $btn['paymentType'] => $eligible_amount
        );

        $this->getService('logger')->debug('create new payment data : ', $requestData);

        $result = PaygreenToolsApiClient::getInstance()->payins->transaction->$paymentModeMethod($requestData);

        if ($result->success === false) {
            $this->getService('logger')->error('error '.$paymentModeMethod.' result : ', $result);
        }

        $this->getService('logger')->info('end create payment '.$paymentModeMethod.' result', $result);

        return $result->success == false ? null : $result;
    }

    public function getPaiementModeMethod($executedAt)
    {
        switch ($executedAt) {
            case self::CASH_PAYMENT:
                return 'cash';

            case self::DEL_PAYMENT:
                return 'tokenize';

            case self::REC_PAYMENT:
                return 'xtime';

            case self::SUB_PAYMENT:
                return 'subscription';

            default:
                return 'cash';
        }
    }

    private function setPaymentData(PaygreenToolsClient $payment, $displayType, $paymentModeMethod = 'cash')
    {
        $dataPaiement = $payment->toArray();

        $data = array(
            'orderId' => $dataPaiement['transaction_id'],
            'amount' => $dataPaiement['amount'],
            'currency' => $dataPaiement['currency'],
            'returned_url' => urldecode($dataPaiement['return_url']),
            'notified_url' => urldecode($dataPaiement['return_callback_url']),
            'metadata' => array(
                'cart_id' => $dataPaiement['cart_id'],
                'paiement_btn' => $dataPaiement['paiement_btn'],
                'display' => $displayType,
            ),
            'buyer' => array(
                'id' => $dataPaiement['customer_email'],
                'lastName' => $dataPaiement['customer_last_name'],
                'firstName' => $dataPaiement['customer_first_name'],
                'email' => $dataPaiement['customer_email'],
            )
        );

        if (isset($dataPaiement['idFingerprint']) && !empty($dataPaiement['idFingerprint'])) {
            $data['idFingerprint'] = $dataPaiement['idFingerprint'];
        }

        if ($paymentModeMethod === 'subscription' || $paymentModeMethod === 'xtime') {
            $data['orderDetails'] = array();

            $orderDetails = array(
                'cycle' => $dataPaiement['reccuringMode'],
                'count' => $dataPaiement['reccuringDueCount']
            );

            if (isset($dataPaiement['reccuringFirstAmount'])) {
                $orderDetails['firstAmount'] = $dataPaiement['reccuringFirstAmount'];
            }

            if (isset($dataPaiement['reccuringStartAt'])) {
                $orderDetails['startAt'] = $dataPaiement['reccuringStartAt'];
            }

            foreach ($orderDetails as $key => $value) {
                $data['orderDetails'][$key] = $value;
            }
        }

        return $data;
    }

    /**
     * @param $transactionId
     * @param $nbPaiement
     * @param $amount
     * @param $currency
     * @param $executedAt
     * @param $reportPayment
     * @param null $percent
     * @return PaygreenToolsClient
     * @throws Exception
     */
    private function generatePaiementData(
        $transactionId,
        $nbPaiement,
        $amount,
        $currency,
        $executedAt,
        $reportPayment,
        $percent = null
    ) {
        $payment = $this->getCaller();

        $payment->transaction(
            $transactionId,
            round($amount * 100),
            $currency
        );

        if ($nbPaiement > 1) {
            if ($executedAt == 1) {
                $startAtReportPayment = ($reportPayment == 0) ? null : strtotime($reportPayment);

                $payment->subscribtionPaiement(
                    PaygreenToolsClient::RECURRING_MONTHLY,
                    $nbPaiement,
                    date('d'),
                    $startAtReportPayment
                );
            } elseif ($executedAt == 3) {
                if ($percent != null && $percent > 0 && $percent < 100) {
                    $payment->setFirstAmount(ceil(round($amount * 100) * $percent / 100));
                }

                $payment->xTimePaiement($nbPaiement);
            }
        }

        return $payment;
    }

    /**
     * getCaller() call getPaygreenConfig()
     * @return PaygreenToolsClient instance of PaygreenClient
     * @throws Exception
     */
    public function getCaller()
    {
        /** @var Paygreen $moduleFacade */
        $moduleFacade = $this->getService('facade.module');

        $config = $moduleFacade->getPaygreenConfig();

        $payment = new PaygreenToolsClient($config['privateKey'], $config['host']);

        $payment->setToken($config['token']);

        return $payment;
    }
}
