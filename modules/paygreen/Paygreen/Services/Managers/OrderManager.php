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

/**
 * Class PaygreenServicesManagersOrderManager
 *
 * @method PaygreenServicesRepositoriesOrderRepository getRepository()
 */
class PaygreenServicesManagersOrderManager extends PaygreenFoundationsAbstractManager
{
    /**
     * @param int $id
     * @return OrderCore|null
     */
    public function getByPrimary($id)
    {
        return $this->getRepository()->findByPrimary($id);
    }

    /**
     * @param int $cartId
     * @return OrderCore|null
     */
    public function getByCartPrimary($cartId)
    {
        $id = (int) Order::getOrderByCartId($cartId);

        return $id ? $this->getByPrimary($id) : null;
    }

    public function create()
    {
    }

    /**
     * Return eligible amount in cents.
     * @param CartCore $cart
     * @param string $mode
     * @return int
     * @throws Exception
     */
    public function getEligibleAmount($cart, $mode)
    {
        /** @var PaygreenServicesManagersProductManager $productManager */
        $productManager = $this->getService('manager.product');

        $eligible_amount = 0;

        /** @var array $item */
        foreach ($cart->getProducts() as $item) {
            if ($productManager->isEligibleProduct($item['id_product'], $mode)) {
                $user_amount = (isset($item['total_wt']) && ($item['total_wt'] > 0))
                    ? (float) $item['total_wt']
                    : (float) $item['total']
                ;

                $amount = (int) round($user_amount * 100);

                $eligible_amount += $amount;
            }
        }

        if ($eligible_amount > 0) {
            $shippingCost = $this->getShippingEligibleAmount($cart->getTotalShippingCost(), $mode);
            $eligible_amount += (int) round($shippingCost * 100);
        }

        $cart_amount = (int) round($cart->getOrderTotal() * 100);

        if ($eligible_amount > $cart_amount) {
            $eligible_amount = $cart_amount;
        }

        return $eligible_amount;
    }

    protected function getShippingEligibleAmount($shippingAmount, $mode)
    {
        $shippingDeactivatedPaymentModes = $this->getService('settings')->get(PaygreenSettings::SHIPPING_PAYMENTS);

        $isEligibleShipping = !in_array($mode, $shippingDeactivatedPaymentModes);

        return $isEligibleShipping ? $shippingAmount : 0;
    }

    public function createOrder($o_cart, $status, $f_amount, $message, $a_vars, $o_customer)
    {
        /** @var PaymentModuleCore $moduleFacade */
        $moduleFacade = $this->getService('facade.module');

        $moduleFacade->validateOrder(
            $o_cart->id,
            $status,
            $f_amount,
            $moduleFacade->displayName,
            $message,
            $a_vars,
            null,
            false,
            $o_customer->secure_key
        );

        $orderId = (int) Order::getOrderByCartId((int) $o_cart->id);

        $this->getService('logger')->info('createOrder', $orderId);

        return $this->getByPrimary($orderId);
    }

    /**
     * @param OrderCore $order
     * @param string $status
     * @return bool
     */
    public function updateOrderStatus(OrderCore $order, $status)
    {
        /** @var PaygreenServicesManagersOrderStateManager $orderStateManager */
        $orderStateManager = $this->getService('manager.order_state');

        if ($orderStateManager->isAllowedTransition($order->current_state, $status)) {
            $this->getService('logger')->debug(
                'updateOrderStatus',
                $order->current_state . ' TO ' . $status
            );

            $order->setCurrentState($status);
            $order->save();

            return true;
        } elseif ($order->current_state === $status) {
            $this->getService('logger')->info('Unnecessary transition : ' . $order->current_state . ' -> ' . $status);

            return true;
        } else {
            $this->getService('logger')->error('Not allowed transition : ' . $order->current_state . ' -> ' . $status);
        }
    }
}
