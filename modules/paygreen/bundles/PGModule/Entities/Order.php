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
 * Class PGModuleEntitiesOrder
 *
 * @package PGModule\Entities
 * @method OrderCore getLocalEntity()
 */
class PGModuleEntitiesOrder extends PGFrameworkFoundationsAbstractEntityWrapped implements PGDomainInterfacesEntitiesOrderInterface
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
        return $this->getLocalEntity()->id;
    }

    /**
     * @inheritdoc
     */
    public function getReference()
    {
        return $this->getLocalEntity()->getUniqReference();
    }

    /**
     * @inheritdoc
     */
    public function getTotalAmount()
    {
        return PGDomainToolsPrice::toInteger($this->getLocalEntity()->getTotalPaid());
    }

    /**
     * @inheritdoc
     */
    public function getTotalUserAmount()
    {
        return PGDomainToolsPrice::fixFloat($this->getLocalEntity()->getTotalPaid());
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return $this->getLocalEntity()->id_customer;
    }

    /**
     * @inheritdoc
     */
    public function getCustomer()
    {
        /** @var PGDomainServicesManagersCustomerManager $customerManager */
        $customerManager = $this->getService('manager.customer');

        $customer = null;

        if ($this->getCustomerId() > 0) {
            $customer = $customerManager->getByPrimary($this->getCustomerId());
        }

        return $customer;
    }

    /**
     * @inheritdoc
     */
    public function getBillingAddress()
    {
        /** @var PGDomainServicesManagersAddressManager $addressManager */
        $addressManager = $this->getService('manager.address');

        $localBillingAddressId = $this->getLocalEntity()->id_address_invoice;

        return $localBillingAddressId
            ? $addressManager->getByPrimary($localBillingAddressId)
            : null;
    }

    public function getCustomerMail()
    {
        return $this->getCustomer() ? $this->getCustomer()->getMail() : null;
    }

    public function getCurrency()
    {
        /** @var PGModuleServicesRepositoriesCurrencyRepository $currencyRepository */
        $currencyRepository = $this->getService('repository.currency');

        $id_currency = $this->getLocalEntity()->id_currency;

        /** @var PGModuleEntitiesCurrency $currency */
        $currency = $currencyRepository->findByPrimary($id_currency);

        return $currency ? $currency->getCode() : null;
    }

    public function getState()
    {
        /** @var PGDomainServicesOrderStateMapper $orderStateMapper */
        $orderStateMapper = $this->getService('mapper.order_state');

        return $orderStateMapper->getOrderState(array('state' => $this->getLocalEntity()->current_state));
    }
}
