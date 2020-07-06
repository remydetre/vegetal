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

class PGModuleServicesOfficersPostPaymentOfficer extends PGFrameworkFoundationsAbstractObject implements PGDomainInterfacesOfficersPostPaymentOfficerInterface
{
    /**
     * @inheritDoc
     */
    public function getOrder(PGDomainInterfacesPostPaymentProvisionerInterface $provisioner)
    {
        /** @var PGModuleServicesRepositoriesOrderRepository $orderRepository */
        $orderRepository = $this->getService('repository.order');

        /** @var OrderCore|null $order */
        $order = $orderRepository->findByCartPrimary($provisioner->getTransaction()->getMetadata('cart_id'));

        return $order;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function createOrder(PGDomainInterfacesPostPaymentProvisionerInterface $provisioner, $state)
    {
        /** @var PGDomainServicesOrderStateMapper $orderStateMapper */
        $orderStateMapper = PGFrameworkContainer::getInstance()->get('mapper.order_state');

        /** @var PGModuleServicesRepositoriesOrderRepository $orderRepository */
        $orderRepository = $this->getService('repository.order');

        /** @var PGModuleBridgesPrestashopBridge $localModule */
        $localModule = $this->getService('bridge.prestashop');

        $title = 'Transaction Paygreen' . (($provisioner->getTransaction()->isTesting() ? ' de test' : ''));

        $id_cart = (int) $provisioner->getTransaction()->getMetadata('cart_id');

        $message = $localModule->l($title)
            . ' - Cart : ' . $id_cart
            . ' - Amount : ' . $provisioner->getTransaction()->getUserAmount() . ' €'
            . ' - PID : ' . $provisioner->getTransaction()->getId()
        ;

        $paymentData = array_merge(
            $provisioner->getTransaction()->getResult()->getRawData(),
            array(
                'date'           => time(),
                // @todo Tester en envoyant le PID à la place de l'OrderId.
                'transaction_id' => $provisioner->getTransaction()->getPid(),
                'mode'           => $provisioner->getTransaction()->getMode(),
                'amount'         => $provisioner->getTransaction()->getAmount(),
                'currency'       => $provisioner->getTransaction()->getCurrency(),
                'by'             => 'webPayment'
            )
        );

        $localState = $orderStateMapper->getLocalOrderState($state);

        $localModule->validateOrder(
            $id_cart,
            $localState['state'],
            $provisioner->getTransaction()->getUserAmount(),
            $localModule->displayName,
            $message,
            $paymentData,
            null,
            false,
            $this->getSecureKey($id_cart)
        );

        /** @var PGDomainInterfacesEntitiesOrderInterface|null $order */
        $order = $orderRepository->findByCartPrimary($id_cart);

        if ($order === null) {
            throw new Exception("Can't create Order with target cart primary : '$id_cart'.");
        }

        $this->getService('logger')->info('Create new Order : ' . $order->id());

        return $order;
    }

    /**
     * @throws Exception
     */
    private function getSecureKey($id_cart)
    {
        /** @var CartCore $cart */
        $cart = new Cart($id_cart);

        if (!$cart->id) {
            throw new Exception("Cart not found : '$id_cart'.");
        }

        /** @var CustomerCore $customer */
        $customer = new Customer((int) $cart->id_customer);

        if (!$customer->id) {
            throw new Exception("Customer not found : '{$cart->id_customer}'.");
        }

        return $customer->secure_key;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function buildPostPaymentProvisioner($pid, PGClientEntitiesPaygreenTransaction $transaction)
    {
        return new PGModuleProvisionersPostPaymentProvisioner($pid, $transaction);
    }
}
