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

/**
 * Interface PGDomainInterfacesEntitiesTransactionInterface
 * @package PGDomain\Interfaces\Entities
 */
interface PGDomainInterfacesEntitiesTransactionInterface extends PGFrameworkInterfacesPersistedEntityInterface
{
    /**
     * @return string
     */
    public function getPid();

    /**
     * @return int
     */
    public function getOrderPrimary();

    /**
     * @return PGDomainInterfacesEntitiesOrderInterface
     */
    public function getOrder();

    /**
     * @return string
     */
    public function getOrderState();

    /**
     * @return string
     */
    public function getMode();

    /**
     * @return int
     */
    public function getAmount();

    /**
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * @param $pid
     * @return self
     */
    public function setPid($pid);

    /**
     * @param $id
     * @return self
     */
    public function setOrderPrimary($id);

    /**
     * @param PGDomainInterfacesEntitiesOrderInterface $order
     * @return self
     */
    public function setOrder(PGDomainInterfacesEntitiesOrderInterface $order);

    /**
     * @param $state
     * @return self
     */
    public function setOrderState($state);

    /**
     * @param $mode
     * @return self
     */
    public function setMode($mode);

    /**
     * @param $amount
     * @return self
     */
    public function setAmount($amount);

    /**
     * @param DateTime $createAt
     * @return self
     */
    public function setCreatedAt(DateTime $createAt);
}
