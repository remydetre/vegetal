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

class PaygreenServicesProcessorsPaymentValidationProcessor extends PaygreenFoundationsProcessorsAbstractProcessor
{
    const PROCESSOR_NAME = 'PaymentValidation';

    public function __construct()
    {
        $this->setSteps(array(
            'verifyModuleActivation',
            'unlock',
            'paygreenCall',
            'loadCart',
            'loadCustomer',
            'switchPaymentMode'
        ));
    }

    protected function verifyModuleActivationStep(PaygreenTasksPaymentValidationTask $task)
    {
        /** @var PaygreenServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var Paygreen $moduleFacade */
        $moduleFacade = $this->getService('facade.module');

        if (!$moduleFacade->active) {
            $logger->error('Paygreen module is deactivated.');
            $task->setStatus($task::STATE_INCONSISTENT_CONTEXT);
        }
    }

    protected function unlockStep(PaygreenTasksPaymentValidationTask $task)
    {
        /** @var PaygreenServicesLogger $logger */
        $logger = $this->getService('logger');

        if ($this->getService('manager.transaction_lock')->isLocked($task->getPid())) {
            $logger->error("Payment validation - PID locked : '{$task->getPid()}'.");
            $task->setStatus($task::STATE_PID_LOCKED);
        }
    }

    protected function paygreenCallStep(PaygreenTasksPaymentValidationTask $task)
    {
        /** @var PaygreenServicesLogger $logger */
        $logger = $this->getService('logger');

        try {
            /** @var stdClass */
            $response = PaygreenToolsApiClient::getInstance()->payins->transaction($task->getPid());

            if ($response->success != true) {
                throw new Exception("Paygreen request fail.");
            }

            /** @var PaygreenEntitiesPaygreenTransaction $transaction */
            $task->setTransaction(new PaygreenEntitiesPaygreenTransaction((array) $response->data));
        } catch (Exception $exception) {
            $logger->error("Paygreen API error : '{$exception->getMessage()}'.");

            $this->addException($exception);

            $task->setStatus($task::STATE_PROVIDER_UNAVAILABLE);
        }
    }

    protected function loadCartStep(PaygreenTasksPaymentValidationTask $task)
    {
        /** @var PaygreenServicesLogger $logger */
        $logger = $this->getService('logger');

        $id = (int) $task->getTransaction()->getMetadata('cart_id');

        /** @var CartCore $cart */
        $cart = new Cart($id);

        if (!$cart->id) {
            $logger->error('Cart not found.');
            $task->setStatus($task::STATE_INCONSISTENT_CONTEXT);
        } else {
            $task->setCart($cart);
        }
    }

    protected function loadCustomerStep(PaygreenTasksPaymentValidationTask $task)
    {
        /** @var PaygreenServicesLogger $logger */
        $logger = $this->getService('logger');

        $id = (int) $task->getCart()->id_customer;

        /** @var CustomerCore $customer */
        $customer = new Customer($id);

        if (!$customer->id) {
            $logger->error('Customer not found.');
            $task->setStatus($task::STATE_INCONSISTENT_CONTEXT);
        } else {
            $task->setCustomer($customer);
        }
    }

    protected function switchPaymentModeStep(PaygreenTasksPaymentValidationTask $task)
    {
        /** @var PaygreenTasksTransactionManagementTask $subTask */
        $subTask = new PaygreenTasksTransactionManagementTask($task);

        /** @var PaygreenFoundationsProcessorsAbstractTransactionManagementProcessor|null $processor */
        $processor = null;

        $paymentMode = $task->getTransaction()->getMode();

        switch ($paymentMode) {
            case 'CASH':
                $processor = $this->getService('processor.transaction_management.cash');
                break;
            case 'TOKENIZE':
                $processor = $this->getService('processor.transaction_management.tokenize');
                break;
            case 'XTIME':
                $processor = $this->getService('processor.transaction_management.xtime');
                break;
            case 'RECURRING':
                $processor = $this->getService('processor.transaction_management.recurring');
                break;
            default:
                $task->setStatus($task::STATE_WORKFLOW_ERROR);
        }

        if ($processor !== null) {
            $processor->execute($subTask);

            switch ($subTask->getStatus()) {
                case $subTask::STATE_SUCCESS:
                    $task->setStatus($task::STATE_SUCCESS);
                    $task->setOrder($subTask->getOrder());
                    break;

                case $subTask::STATE_PAYMENT_CANCELED:
                    $task->setStatus($task::STATE_PAYMENT_CANCELED);
                    break;

                case $subTask::STATE_PAYMENT_REFUSED:
                    $task->setStatus($task::STATE_PAYMENT_REFUSED);
                    break;

                case $subTask::STATE_ORDER_CANCELED:
                    $task->setStatus($task::STATE_ORDER_CANCELED);
                    $task->setOrder($subTask->getOrder());
                    break;

                case $subTask::STATE_ORDER_NOT_FOUND:
                    $task->setStatus($task::STATE_INCONSISTENT_CONTEXT);
                    break;

                default:
                    $task->setStatus($task::STATE_FATAL_ERROR);
            }
        }
    }
}
