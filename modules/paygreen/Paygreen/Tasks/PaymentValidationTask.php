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

class PaygreenTasksPaymentValidationTask extends PaygreenFoundationsAbstractTask
{
    const STATE_PID_LOCKED = 11;
    const STATE_PAYMENT_CANCELED = 12;
    const STATE_PAYMENT_REFUSED = 13;
    const STATE_ORDER_CANCELED = 14;
    const STATE_INCONSISTENT_CONTEXT = 15;
    const STATE_PROVIDER_UNAVAILABLE = 16;
    const STATE_WORKFLOW_ERROR = 17;

    /** @var string */
    private $pid;

    /** @var CartCore|null  */
    private $cart = null;

    /** @var CustomerCore|null */
    private $customer = null;

    /** @var PaygreenEntitiesPaygreenTransaction */
    private $transaction;

    /** @var OrderCore|null  */
    private $order = null;

    public function __construct($pid)
    {
        $this->pid = $pid;
    }

    public function getName()
    {
        return 'PaymentValidation';
    }

    /**
     * @return string
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return PaygreenEntitiesPaygreenTransaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param PaygreenEntitiesPaygreenTransaction $transaction
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @return CartCore|null
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param CartCore|null $cart
     * @return self
     */
    public function setCart($cart = null)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * @return CustomerCore|null
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param CustomerCore|null $customer
     * @return self
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
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
}
