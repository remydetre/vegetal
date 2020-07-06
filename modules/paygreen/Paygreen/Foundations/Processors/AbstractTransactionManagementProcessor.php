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
 *  @author    PayGreen <contact@paygreen.fr>
 *  @copyright 2014-2014 Watt It Is
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 */

class PaygreenFoundationsProcessorsAbstractTransactionManagementProcessor extends PaygreenFoundationsProcessorsAbstractProcessor
{
    public function __construct()
    {
        $this->setSteps(array(
            'default'
        ));
    }

    protected function refusedPaymentStep(PaygreenTasksTransactionManagementTask $task)
    {
        /** @var PaygreenSettings $settings */
        $settings = $this->getService('settings');

        if ($settings->get(PaygreenSettings::_CONFIG_REFUSE_ACTION) == 0) {
            $task->setStatus($task::STATE_PAYMENT_REFUSED);
        } else {
            $task->setOrderStatus($settings->get('PS_OS_CANCELED'));

            $this->pushSteps(array(
                'saveOrder',
                array('setStatus', array(
                    $task::STATE_ORDER_CANCELED
                ))
            ));
        }
    }

    protected function insertTransactionStep(PaygreenTasksTransactionManagementTask $task)
    {
        /** @var PaygreenServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PaygreenServicesManagersTransactionManager $transactionManager */
        $transactionManager = $this->getService('manager.transaction');

        try {
            $transactionManager->insertTransaction(
                $task->getOrder()->id,
                $task->getCart()->id,
                $task->getTransaction()->getId(),
                $task->getTransaction()->getMode(),
                $task->getOrderStatus()
            );
        } catch (Exception $exception) {
            $this->addException($exception);
            $logger->error('Error on insert transaction :' . $exception->getMessage());
        }
    }

    protected function checkCartValidityStep(PaygreenTasksTransactionManagementTask $task)
    {
        /** @var PaygreenSettings $settings */
        $settings = $this->getService('settings');

        /** @var PaygreenServicesLogger $logger */
        $logger = $this->getService('logger');

        if ($task->getTransaction()->getUserAmount() !== $task->getCart()->getOrderTotal(true)) {
            $logger->error(
                'Paygreen fraud check notice',
                array(
                    'paygreen-amount' => $task->getTransaction()->getUserAmount(),
                    'cart-amount' => $task->getCart()->getOrderTotal(true)
                )
            );

            $task->setOrderStatus($settings->get(PaygreenSettings::_CONFIG_ORDER_VERIFY));
        }
    }

    protected function saveOrderStep(PaygreenTasksTransactionManagementTask $task)
    {
        /** @var PaygreenServicesManagersOrderManager $orderManager */
        $orderManager = $this->getService('manager.order');

        /** @var OrderCore|null $order */
        $order = $orderManager->getByCartPrimary($task->getCart()->id);

        if ($order !== null) {
            $orderManager->updateOrderStatus($order, $task->getOrderStatus());
        } else {
            $order = $this->createOrder($task);
        }

        $task->setOrder($order);
    }

    private function createOrder(PaygreenTasksTransactionManagementTask $task)
    {
        /** @var PaygreenServicesManagersOrderManager $orderManager */
        $orderManager = $this->getService('manager.order');

        /** @var Paygreen $moduleFacade */
        $moduleFacade = $this->getService('facade.module');

        $title = 'Transaction Paygreen' . (($task->getTransaction()->isTesting() ? ' de test' : ''));

        $message = $moduleFacade->l($title)
            . ' Cart : ' . $task->getCart()->id
            . ' Button : ' . $task->getTransaction()->getMetadata('paiement_btn')
            . ' PID : ' . $task->getTransaction()->getId()
        ;

        $paymentData = array_merge(
            $task->getTransaction()->getResult()->getRawData(),
            array(
                'date'           => time(),
                // @todo Tester en envoyant le PID à la place de l'OrderId.
                'transaction_id' => $task->getTransaction()->getOrderPrimary(),
                'mode'           => $task->getTransaction()->getMode(),
                'amount'         => $task->getTransaction()->getAmount(),
                'currency'       => $task->getTransaction()->getCurrency(),
                'by'             => 'webPayment'
            )
        );

        /** @var OrderCore|null $order */
        $order = $orderManager->createOrder(
            $task->getCart(),
            $task->getOrderStatus(),
            $task->getTransaction()->getUserAmount(),
            $message,
            $paymentData,
            $task->getCustomer()
        );

        return $order;
    }

    protected function checkTestingModeStep(PaygreenTasksTransactionManagementTask $task)
    {
        /** @var PaygreenSettings $settings */
        $settings = $this->getService('settings');

        if ($task->getTransaction()->isTesting()) {
            $task->setOrderStatus($settings->get(PaygreenSettings::_CONFIG_ORDER_TEST));
        }
    }

    protected function updateOrderStateStep(PaygreenTasksTransactionManagementTask $task)
    {
        /** @var PaygreenServicesManagersOrderManager $orderManager */
        $orderManager = $this->getService('manager.order');

        $orderManager->updateOrderStatus($task->getOrder(), $task->getOrderStatus());
    }

    protected function setOrderStatusStep(PaygreenTasksTransactionManagementTask $task, $status)
    {
        $task->setOrderStatus($status);
    }
}
