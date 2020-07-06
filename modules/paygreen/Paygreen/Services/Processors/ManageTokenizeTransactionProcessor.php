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

class PaygreenServicesProcessorsManageTokenizeTransactionProcessor extends PaygreenFoundationsProcessorsAbstractTransactionManagementProcessor
{
    const PROCESSOR_NAME = 'TokenizeTransaction';

    protected function defaultStep(PaygreenTasksTransactionManagementTask $task)
    {
        /** @var PaygreenServicesManagersOrderManager $orderManager */
        $orderManager = $this->getService('manager.order');

        /** @var OrderCore|null $order */
        $order = $orderManager->getByCartPrimary($task->getCart()->id);

        $task->setOrder($order);

        if ($order === null) {
            $this->pushStep('creationWorkflow');
        } else {
            $this->pushStep('confirmationWorkflow');
        }
    }

    protected function creationWorkflowStep(PaygreenTasksTransactionManagementTask $task)
    {
        /** @var PaygreenSettings $settings */
        $settings = $this->getService('settings');

        switch ($task->getTransaction()->getResult()->getStatus()) {
            case PaygreenToolsClient::STATUS_REFUSED:
                $this->pushStep('refusedPayment');
                break;

            case PaygreenToolsClient::STATUS_CANCELLING:
                $task->setStatus($task::STATE_PAYMENT_CANCELED);
                break;

            case PaygreenToolsClient::STATUS_PENDING_EXEC:
                $this->pushSteps(array(
                    array('setOrderStatus', array(
                        $settings->get(PaygreenSettings::_CONFIG_ORDER_AUTH)
                    )),
                    'saveOrder',
                    'insertTransaction',
                    array('setStatus', array(
                        $task::STATE_SUCCESS
                    ))
                ));

                break;

            default:
                $task->setStatus($task::STATE_WORKFLOW_ERROR);
        }
    }

    protected function confirmationWorkflowStep(PaygreenTasksTransactionManagementTask $task)
    {
        /** @var PaygreenSettings $settings */
        $settings = $this->getService('settings');

        switch ($task->getTransaction()->getResult()->getStatus()) {
            case PaygreenToolsClient::STATUS_REFUSED:
                $this->pushSteps(array(
                    array('setOrderStatus', array(
                        $settings->get('PS_OS_ERROR')
                    )),
                    'updateOrderState',
                    'updateTransaction',
                    array('setStatus', array(
                        $task::STATE_PAYMENT_REFUSED
                    ))
                ));

                break;

            case PaygreenToolsClient::STATUS_SUCCESSED:
                $this->pushSteps(array(
                    array('setOrderStatus', array(
                        $settings->get('PS_OS_PAYMENT')
                    )),
                    'checkCartValidity',
                    'checkTestingMode',
                    'updateOrderState',
                    'updateTransaction',
                    array('setStatus', array(
                        $task::STATE_SUCCESS
                    ))
                ));

                break;

            default:
                $task->setStatus($task::STATE_WORKFLOW_ERROR);
        }
    }

    protected function updateTransactionStep(PaygreenTasksTransactionManagementTask $task)
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
                $task->getOrder()->current_state
            );
        } catch (Exception $exception) {
            $this->addException($exception);
            $logger->error('Error on update transaction :' . $exception->getMessage());
        }
    }
}
