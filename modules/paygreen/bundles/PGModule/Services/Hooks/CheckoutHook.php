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
 * Class PGModuleServicesHooksCheckoutHook
 */
class PGModuleServicesHooksCheckoutHook
{
    /** @var PGModuleBridgesPrestashopBridge */
    private $prestashopBridge;

    /** @var PGFrameworkServicesLogger */
    private $logger;

    /** @var PGDomainServicesHandlersCheckoutHandler */
    private $checkoutHandler;

    /** @var PGModuleInterfacesPrestashopHandlerInterface */
    private $prestashopHandler;

    /** @var PGViewServicesHandlersViewHandler */
    private $viewHandler;

    /** @var PGDomainServicesManagersButtonManager */
    private $buttonManager;

    /** @var PGServerServicesLinker */
    private $linker;

    public function __construct(
        PGModuleBridgesPrestashopBridge $prestashopBridge,
        PGDomainServicesHandlersCheckoutHandler $checkoutHandler,
        PGModuleInterfacesPrestashopHandlerInterface $prestashopHandler,
        PGViewServicesHandlersViewHandler $viewHandler,
        PGDomainServicesManagersButtonManager $buttonManager,
        PGServerServicesLinker $linker,
        PGFrameworkServicesLogger $logger
    ) {
        $this->prestashopBridge = $prestashopBridge;
        $this->checkoutHandler = $checkoutHandler;
        $this->prestashopHandler = $prestashopHandler;
        $this->viewHandler = $viewHandler;
        $this->buttonManager = $buttonManager;
        $this->linker = $linker;
        $this->logger = $logger;
    }

    /**
     * Hook for different payment options on 1.5/1.6
     * @param $params
     * @return bool
     * @throws Exception
     */
    public function unaSextusDisplay($params)
    {
        $html = '';

        try {
            if (!array_key_exists('cart', $params) && (!$params['cart'] instanceof Cart)) {
                $this->logger->error("Cart not found in DisplayPayment hook.");
            }

            /** @var PGDomainInterfacesCheckoutProvisionerInterface $checkoutProvisioner */
            $checkoutProvisioner = new PGModuleProvisionersCheckoutProvisioner($params['cart']);

            if ($this->checkoutHandler->isCheckoutAvailable($checkoutProvisioner)) {
                $this->logger->debug("Paygreen Checkout is available.");

                $html = $this->viewHandler->renderTemplate('una-sextus/block-checkout-button-list', array(
                    'buttons' => $this->getPaymentOptions($checkoutProvisioner),
                    'action' => $this->linker->buildFrontOfficeUrl('front.payment.validation')
                ));
            } else {
                $this->logger->warning("Paygreen Checkout is not available.");
            }
        } catch (Exception $exception) {
            $this->logger->critical("Error during DisplayPayment hook : " . $exception->getMessage(), $exception);
        }

        return $html;
    }

    /**
     * Hook for different payment options on 1.7
     * @param array $params
     * @return array
     */
    public function unaSeptimusDisplay($params)
    {
        $paymentOptions = array();

        try {
            if (!array_key_exists('cart', $params) && (!$params['cart'] instanceof CartCore)) {
                $this->logger->error("Cart not found in PaymentOptions hook.");
            }

            /** @var PGDomainInterfacesCheckoutProvisionerInterface $checkoutProvisioner */
            $checkoutProvisioner = new PGModuleProvisionersCheckoutProvisioner($params['cart']);

            if ($this->checkoutHandler->isCheckoutAvailable($checkoutProvisioner)) {
                $this->logger->debug("Paygreen Checkout is available.");

                $paymentOptions = $this->getPaymentOptions($checkoutProvisioner);
            } else {
                $this->logger->warning("Paygreen Checkout is not available.");
            }
        } catch (Exception $exception) {
            $this->logger->critical("Error during PaymentOptions hook : " . $exception->getMessage(), $exception);
        }

        return $paymentOptions;
    }

    public function getPaymentOptions(PGDomainInterfacesCheckoutProvisionerInterface $checkoutProvisioner)
    {
        $paymentOptions = array();

        try {
            /** @var CartCore $cart */
            $cart = $this->prestashopBridge->getContext()->cart;

            if (!$this->checkCurrency($cart)) {
                $this->logger->error("Not supported currency.");
            } else {
                $buttons = $this->buttonManager->getValidButtons($checkoutProvisioner);

                foreach ($buttons as $button) {
                    $paymentOptions[] = $this->prestashopHandler->getPaymentOption($button);
                }
            }
        } catch (Exception $exception) {
            $this->logger->alert("Error during payment buttons creation : " . $exception->getMessage(), $exception);
        }

        return $paymentOptions;
    }

    /**
     * check Currency of your cart
     * @param CartCore $cart
     * @return bool
     */
    public function checkCurrency(CartCore $cart)
    {
        /** @var CurrencyCore $currency_order */
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->prestashopBridge->getCurrency($cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }

        return false;
    }
}
