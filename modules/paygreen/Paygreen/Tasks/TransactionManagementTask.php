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

class PaygreenTasksTransactionManagementTask extends PaygreenFoundationsAbstractTask
{
    const STATE_PAYMENT_CANCELED = 10;
    const STATE_PAYMENT_REFUSED = 11;
    const STATE_ORDER_CANCELED = 12;
    const STATE_ORDER_NOT_FOUND = 13;
    const STATE_WORKFLOW_ERROR = 14;

    /** @var PaygreenTasksPaymentValidationTask */
    private $paymentValidationTask;

    /** @var OrderCore|null  */
    private $order = null;

    /** @var string|null */
    private $orderStatus = null;

    public function __construct(PaygreenTasksPaymentValidationTask $paymentValidationTask)
    {
        $this->paymentValidationTask = $paymentValidationTask;
    }

    public function getName()
    {
        return 'TransactionManagement';
    }

    /**
     * @return string
     */
    public function getPid()
    {
        return $this->paymentValidationTask->getPid();
    }

    /**
     * @return PaygreenEntitiesPaygreenTransaction
     */
    public function getTransaction()
    {
        return $this->paymentValidationTask->getTransaction();
    }

    /**
     * @return CartCore|null
     */
    public function getCart()
    {
        return $this->paymentValidationTask->getCart();
    }

    /**
     * @return CustomerCore|null
     */
    public function getCustomer()
    {
        return $this->paymentValidationTask->getCustomer();
    }

    /**
     * @return OrderCore|null
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param OrderCore|null $order
     * @return self
     */
    public function setOrder($order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }

    /**
     * @param string|null $orderStatus
     * @return self
     */
    public function setOrderStatus($orderStatus)
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }
}
