<?php
/**
 * 2014 - 2019 Watt Is It
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
 * @copyright 2014 - 2019 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   2.7.6
 */

/**
 * Class PGModuleEntitiesTransaction
 *
 * @package PGModule\Entities
 * @method PGLocalEntitiesTransaction getLocalEntity()
 */
class PGModuleEntitiesTransaction extends PGDomainFoundationsEntitiesAbstractTransaction
{
    /**
     * @inheritdoc
     */
    protected function hydrateFromLocalEntity($localEntity)
    {
        // Do nothing.
    }

    /**
     * @inheritdoc
     */
    public function id()
    {
        return (int) $this->getLocalEntity()->id;
    }

    /**
     * @inheritdoc
     */
    public function getPid()
    {
        return (string) $this->getLocalEntity()->pid;
    }

    /**
     * @inheritdoc
     */
    public function setPid($pid)
    {
        $this->getLocalEntity()->pid = (string) $pid;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOrderPrimary()
    {
        return (int) $this->getLocalEntity()->id_order;
    }

    /**
     * @inheritdoc
     */
    public function setOrderPrimary($id)
    {
        $this->getLocalEntity()->id_order = (int) $id;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOrderState()
    {
        return (string) $this->getLocalEntity()->state;
    }

    /**
     * @inheritdoc
     */
    public function setOrderState($state)
    {
        $this->getLocalEntity()->state = (string) $state;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        return (int) $this->getLocalEntity()->amount;
    }

    /**
     * @inheritdoc
     */
    public function setAmount($amount)
    {
        $this->getLocalEntity()->amount = (int) $amount;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMode()
    {
        return (string) $this->getLocalEntity()->mode;
    }

    /**
     * @inheritdoc
     */
    public function setMode($mode)
    {
        $this->getLocalEntity()->mode = (string) $mode;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        $dt = new DateTime();

        $dt->setTimestamp((int) $this->getLocalEntity()->created_at);

        return $dt;
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt(DateTime $createAt)
    {
        $this->getLocalEntity()->created_at = (int) $createAt->getTimestamp();

        return $this;
    }
}
