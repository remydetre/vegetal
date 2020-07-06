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
 * Class PGModuleProvisionersPrePaymentProvisioner
 * @package PGModule\Provisioners
 */
class PGModuleProvisionersPrePaymentProvisioner extends PGFrameworkFoundationsAbstractObject implements PGDomainInterfacesPrePaymentProvisionerInterface
{
    /** @var PGModuleBridgesPrestashopBridge */
    private $localModule;

    /** @var PGDomainInterfacesEntitiesCartInterface */
    private $cart;

    /** @var CurrencyCore */
    private $currency;

    /** @var CustomerCore */
    private $customer;

    /** @var AddressCore */
    private $address;

    /** @var CountryCore */
    private $country;

    /** @var CarrierCore */
    private $carrier;

    public function __construct(PGModuleBridgesPrestashopBridge $localModule)
    {
        $this->localModule = $localModule;

        /** @var CartCore $localCart */
        $localCart = $localModule->getContext()->cart;

        $this->cart = new PGModuleEntitiesCart($localCart);

        $this->currency = new Currency($localCart->id_currency);
        $this->customer = new Customer($localCart->id_customer);
        $this->address = new Address($localCart->id_address_delivery);
        $this->carrier = new Carrier($localCart->id_carrier);

        $this->country = new Country($this->address->id_country);
    }

    public function getReference()
    {
        /** @var PGFrameworkServicesSettings $settings */
        $settings = $this->getService('settings');

        $suffix = (PAYGREEN_ENV === 'DEV') ? '-' . mt_rand(10000, 99999) : '-' . $settings->get('shop_identifier');

        return $this->cart->id() . $suffix;
    }

    public function getCurrency()
    {
        return $this->currency->iso_code;
    }

    public function getTotalAmount()
    {
        return $this->cart->getTotalCost();
    }

    public function getShippingAmount()
    {
        return $this->cart->getShippingCost();
    }

    public function getShippingName()
    {
        return $this->carrier->name;
    }

    public function getShippingWeight()
    {
        return $this->cart->getLocalEntity()->getTotalWeight();
    }

    public function getMail()
    {
        return $this->customer->email;
    }

    public function getCountry()
    {
        return $this->country->iso_code;
    }

    public function getAddressLineOne()
    {
        return $this->address->address1;
    }

    public function getAddressLineTwo()
    {
        return $this->address->address2;
    }

    public function getCity()
    {
        return $this->address->city;
    }

    public function getZipCode()
    {
        return $this->address->postcode;
    }

    public function getCustomerId()
    {
        return $this->customer->id;
    }

    public function getFirstName()
    {
        return $this->customer->firstname;
    }

    public function getLastName()
    {
        return $this->customer->lastname;
    }

    /**
     * @return PGDomainInterfacesEntitiesCartItemInterface[]
     */
    public function getItems()
    {
        return $this->cart->getItems();
    }

    /**
     * @inheritDoc
     */
    public function getMetadata()
    {
        return array(
            'cart_id' => $this->cart->id()
        );
    }
}
