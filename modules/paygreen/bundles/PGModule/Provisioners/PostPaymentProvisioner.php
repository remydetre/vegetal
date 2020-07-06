<?php
/**
 * 2014 - 2020 Watt Is It
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
 * @copyright 2014 - 2020 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   3.0.1
 */

class PGModuleProvisionersPostPaymentProvisioner extends PGFrameworkFoundationsAbstractObject implements PGDomainInterfacesPostPaymentProvisionerInterface
{
    /** @var string */
    private $pid;

    /** @var CartCore|null  */
    private $cart = null;

    /** @var CustomerCore|null */
    private $customer = null;

    /** @var PGClientEntitiesPaygreenTransaction */
    private $transaction;

    /**
     * PGModuleProvisionersPostPaymentProvisioner constructor.
     * @param string $pid
     * @param PGClientEntitiesPaygreenTransaction $transaction
     * @throws Exception
     */
    public function __construct($pid, PGClientEntitiesPaygreenTransaction $transaction)
    {
        $this->pid = $pid;
        $this->transaction = $transaction;

        $this->loadCart();
        $this->loadCustomer();
    }

    /**
     * @throws Exception
     */
    protected function loadCart()
    {
        $id = (int) $this->getTransaction()->getMetadata('cart_id');

        $this->cart = new Cart($id);

        if (!$this->cart->id) {
            throw new Exception('Cart not found.');
        }
    }

    /**
     * @throws Exception
     */
    protected function loadCustomer()
    {
        $id = (int) $this->cart->id_customer;

        $this->customer = new Customer($id);

        if (!$this->customer->id) {
            throw new Exception('Customer not found.');
        }
    }

    /**
     * @return string
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return PGClientEntitiesPaygreenTransaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @return float
     * @throws Exception
     */
    public function getUserAmount()
    {
        return $this->cart->getOrderTotal(true);
    }
}
