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

/**
 * Class PaygreenServicesManagersTransactionManager
 *
 * @method PaygreenServicesRepositoriesTransactionRepository getRepository()
 */
class PaygreenServicesManagersTransactionManager extends PaygreenFoundationsAbstractManager
{
    /**
     * @param int $id_order
     * @param int $id_cart
     * @param string $pid
     * @param string $mode
     * @param string $state
     * @return bool
     */
    public function insertTransaction($id_order, $id_cart, $pid, $mode, $state)
    {
        return $this->getRepository()->insert($id_order, $id_cart, $pid, $mode, $state);
    }

    /**
     * @param int $id_order
     * @param int $id_cart
     * @param string $pid
     * @param string $mode
     * @param string $state
     * @return bool
     */
    public function updateTransaction($pid, $state)
    {
        return $this->getRepository()->updateMode($pid, $state);
    }

    /**
     * @param int $id_cart
     * @param string $pid
     * @param string $state
     * @param int $amount
     * @return bool
     */
    public function insertRecurringTransaction($id_cart, $pid, $state, $amount)
    {
        /** @var PaygreenServicesRepositoriesRecurringTransactionRepository $recurringTransactionRepository */
        $recurringTransactionRepository = $this->getService('repository.recurring_transaction');

        return $recurringTransactionRepository->insert($id_cart, $pid, $state, $amount);
    }

    /**
     * Update status of paygreen transaction
     * @param int $id_order
     * @param string $state
     * @throws Exception
     */
    private function updatePaygreenTransactionStatus($id_order, $state)
    {
        $date = new DateTime();
        $transaction = array();
        $transaction['state'] = pSQL($state);
        $transaction['updated_at'] = pSQL($date->format('Y-m-d H:i:s'));

        Db::getInstance()->update(
            'paygreen_transactions',
            $transaction,
            'id_order=' . (int) $id_order
        );
    }

    /**
     * Check if an order is already refunded
     * @param $id_order
     * @return true or false
     */
    private function isRefunded($id_order)
    {
        $stateTransac = $this->getRepository()->getStateTransactionByIdOrder($id_order);

        // 7 for refund
        if ($stateTransac == 7 || $stateTransac == null) {
            return true;
        }

        return false;
    }

    /**
     * Check if a payment was done with Paygreen
     * @param $pid
     * @return true or false
     */
    private function isPaygreenPayment($pid)
    {
        $paygreen_pid = Db::getInstance()->getValue(
            'SELECT `pid`
            FROM  `' . _DB_PREFIX_ . 'paygreen_transactions`
            WHERE `pid` = "' . pSQL($pid) . '"'
        );

        return $paygreen_pid == $pid ? true : false;
    }

    /**
     * Return state of transaction by the id order
     * @param $id_order
     * @return false|string state or false if not exists
     */
    private function getPIDByOrder($id_order)
    {
        return Db::getInstance()->getValue(
            'SELECT pid FROM ' . _DB_PREFIX_ . 'paygreen_transactions
            WHERE id_order=' . ((int)$id_order) . ';'
        );
    }

    /**
     * @param $id_order
     * @param null $amount
     * @return bool
     * @throws Exception
     */
    public function paygreenRefundTransaction($id_order, $amount = null)
    {
        /** @var PaygreenSettings $settings */
        $settings = $this->getService('settings');

        /** @var PaygreenServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var Paygreen $moduleFront */
        $moduleFront = $this->getService('facade.module');

        if ($settings->get(Paygreen::_CONFIG_PAYMENT_REFUND) != 1) {
            return false;
        }

        $pid = $this->getPIDByOrder($id_order);
        if (empty($pid)) {
            return false;
        }

        if (!$this->isPaygreenPayment($pid)) {
            return false;
        }

        if ($this->isRefunded($id_order)) {
            return false;
        }

        $logger->info('REFUND', array($pid, $amount));

        $refundStatus = PaygreenToolsApiClient::getInstance()->refundOrder(
            $pid,
            round($amount, 2)
        );

        $moduleFront->errorPaygreenApiClient($refundStatus);

        if (!$refundStatus) {
            $logger->info(
                'PaygreenTRansaction update State ',
                'Transacton '. $pid .' NOT refunded'
            );

            return false;
        }

        if (isset($refundStatus->success)) {
            if (!$refundStatus->success) {
                $logger->info(
                    'PaygreenTRansaction update State ',
                    'Transacton '. $pid .' NOT refunded'
                );

                return false;
            }
        }

        if (isset($amount)) {
            /** @var OrderCore $order */
            $order = new Order($id_order);

            if (round($this->getTotalRefundByIdOrder($id_order), 2)>=$order->total_paid) {
                $this->updatePaygreenTransactionStatus($id_order, 7);
            }
        } else {
            $this->updatePaygreenTransactionStatus($id_order, 7);
        }

        return true;
    }

    public function getTotalRefundByIdOrder($id_order)
    {
        if (!(int)$id_order) {
            return false;
        }

        return Db::getInstance()->getValue(
            'SELECT SUM(`unit_price_tax_incl` * `product_quantity_refunded`) FROM '.
            _DB_PREFIX_.'order_detail WHERE `id_order` = ' . (int)$id_order . ';'
        );
    }

    /**
     * Validate Shipped Payment
     * @param $id_order
     * @return bool
     * @throws Exception
     */
    public function paygreenShippedTransaction($id_order)
    {
        /** @var PaygreenSettings $settings */
        $settings = $this->getService('settings');

        /** @var Paygreen $moduleFront */
        $moduleFront = $this->getService('facade.module');

        $pid = $this->getPIDByOrder($id_order);
        if (empty($pid)) {
            return false;
        }

        if (!$this->isPaygreenPayment($pid)) {
            return false;
        }

        $shippedStatus = PaygreenToolsApiClient::getInstance()->validDeliveryPayment($pid);

        $moduleFront->errorPaygreenApiClient($shippedStatus);

        if (!$shippedStatus) {
            $this->getService('logger')->info(
                'PaygreenTransaction update State ',
                'Transacton '. $pid .' NOT shipped'
            );

            return false;
        }

        /** @var OrderHistoryCore $history */
        $history = new OrderHistory();
        $history->id_order = (int)($id_order);

        if (isset($shippedStatus->success)) {
            if ($shippedStatus->success &&
                (
                    $shippedStatus->data->result->status == PaygreenToolsClient::STATUS_SUCCESSED
                    || $shippedStatus->data->result->status == PaygreenToolsClient::STATUS_WAITING_EXEC
                )
            ) {
                $history->changeIdOrderState($settings->get('PS_OS_PAYMENT'), (int)($id_order));
                $history->add();
            } else {
                $error = $moduleFront->l('The order could not be sent. Please try again.');
                $moduleFront->getContext()->controller->errors[] = $error;

                $history->changeIdOrderState($settings->get('PS_OS_CANCELED'), (int)($id_order));
                $history->add();

                return false;
            }
        }

        return true;
    }

    public function getByOrderPrimary($orderId)
    {
        $sql = 'SELECT type 
                FROM '._DB_PREFIX_.'paygreen_transactions
                WHERE id_order=' . (int) $orderId;

        return Db::getInstance()->getValue($sql);
    }
}
