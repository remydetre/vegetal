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

class PaygreenValidationModuleFrontController extends ModuleFrontController
{
    protected function getService($name)
    {
        return PaygreenContainer::getInstance()->get($name);
    }

    public function postProcess()
    {
        /** @var PaygreenServicesPaymentHandler $paymentHandler */
        $paymentHandler = $this->getService('handler.payment');

        /** @var CartCore $cart */
        $cart = $this->context->cart;
        if ($cart->id_customer == 0
            || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0
            || !$this->module->active
        ) {
            Tools::redirect('index.php?controller=order&step=1');
        }
        // Check that this payment option is still available
        //in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'paygreen') {
                $authorized = true;
                break;
            }
        }
        if (!$authorized) {
            die($this->module->l('This payment method is not available.', 'validation'));
        }

        $conf = $this->module->getPaygreenConfig();
        $API = PaygreenToolsApiClient::getInstance($conf['token'], $conf['privateKey'], $conf['host']);

        if (isset($_REQUEST['pid'])) {
            $payment = $API->getTransactionInfo($_REQUEST['pid']);
        } else {
            $executedAt = $_REQUEST['executedAt'];
            $displayType = $_REQUEST['displayType'];

            $dataCart = $cart->getSummaryDetails();

            /** @var CurrencyCore $currency */
            $currency = new Currency((int)$cart->id_currency);

            /** @var CustomerCore $dataCustomer */
            $dataCustomer = new Customer((int)$dataCart['invoice']->id_customer);

            $uniqueId = $_REQUEST['shopUiD'];
            $orderId = $cart->id.'-'.$uniqueId;

            $paymentData = array(
                'orderId' => $orderId,
                'amount' => $dataCart['total_price'],
                'currency' => $currency->iso_code,
                'cart_id' => $cart->id,
                'display' => $displayType,
                'id' => $dataCart['invoice']->id_customer,
                'lastName' => $dataCart['invoice']->lastname,
                'firstName' => $dataCart['invoice']->firstname,
                'country' => $dataCart['invoice']->country,
                'cust_id' => $dataCustomer->id,
                'cust_lastName' => $dataCustomer->lastname,
                'cust_firstName' => $dataCustomer->firstname,
                'cust_email' => $dataCustomer->email
            );
            $paymentData['dataBtn'] = $_REQUEST['paymentBtn'];

            $payment = $paymentHandler->createPayment($executedAt, $paymentData, $displayType);
        }

        PaygreenContainer::getInstance()->get('logger')->info('Validation.php payment : ', $payment);
        if ($payment == null || (isset($payment->success) && $payment->success != true)) {
            Tools::redirect('index.php?controller=order&step=3&error=1');
        } elseif (isset($payment) && $payment->success &&
            ($payment->data->result->status != 'SUCCESSED' || $payment->data->result->status != 'CANCELLED')) {
            // display insite
            if ((int) $displayType === 1) {
                $minWidthIframe = empty($_REQUEST['minWidth']) ? '400' : $_REQUEST['minWidth'] ;
                $minHeightIframe = empty($_REQUEST['minHeight']) ? '400' : $_REQUEST['minHeight'] ;

                $action = $this->context->link->getModuleLink(
                    PAYGREEN_MODULE_NAME,
                    'paymentIframe',
                    array(
                        'url' => $payment->data->url,
                        'minWidthIframe' => $minWidthIframe,
                        'minHeightIframe' => $minHeightIframe,
                    ),
                    true
                );
                Tools::redirect($action);
            } else {
                Tools::redirect($payment->data->url);
            }
        } else {
            $o_cart = new Cart($payment->data->metadata->cart_id);
            $n_order_id = (int)Order::getOrderByCartId($o_cart->id);
            $o_order = new Order($n_order_id);
            $this->module->redirectToConfirmationPage($o_order);
        }
    }
}
