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
 * Class PGModuleServicesHooksOrderHook
 */
class PGModuleServicesHooksOrderHook
{
    /** @var PGFrameworkServicesLogger */
    private $logger;

    /** @var PGFrameworkServicesBroadcaster */
    private $broadcaster;

    /** @var PGDomainServicesManagersOrderManager */
    private $orderManager;

    /** @var PGDomainServicesHandlersTokenizeHandler */
    private $tokenizeHandler;

    public function __construct(
        PGFrameworkServicesBroadcaster $broadcaster,
        PGDomainServicesManagersOrderManager $orderManager,
        PGDomainServicesHandlersTokenizeHandler $tokenizeHandler,
        PGFrameworkServicesLogger $logger
    ) {
        $this->broadcaster = $broadcaster;
        $this->orderManager = $orderManager;
        $this->tokenizeHandler = $tokenizeHandler;
        $this->logger = $logger;
    }

    /**
     * Hook When refund total_paid_tax_incl
     * @param array $params
     * @return bool
     */
    public function updateOrderState($params)
    {
        try {
            if (!isset($params['id_order'])) {
                $this->logger->error("No order primary found.", $params);
                return false;
            }

            $id_order = $params['id_order'];

            if (!isset($params['newOrderStatus'])) {
                $this->logger->error("No order status found.", $params);
                return false;
            }

            $order = $this->orderManager->getByPrimary($id_order);

            if ($order === null) {
                $this->logger->error("No order found with ID #$id_order.");
                return false;
            }

            $currentStatePrimary = (int) $params['newOrderStatus']->id;
            $refundStatePrimary = (int) Configuration::get('PS_OS_REFUND');
            $deliveredStatePrimary = (int) Configuration::get('PS_OS_DELIVERED');

            if ($currentStatePrimary === $refundStatePrimary) {
                $this->broadcaster->fire(new PGDomainComponentsEventsRefundEvent($order, $order->getTotalUserAmount()));
            } elseif ($currentStatePrimary === $deliveredStatePrimary) {
                return $this->tokenizeHandler->processTokenizedPayments($order);
            } else {
                $this->logger->debug("No event handler on order state : '$currentStatePrimary'.");
            }
        } catch (Exception $exception) {
            $this->logger->error("Error during PostUpdateOrderStatus hook : " . $exception->getMessage(), $exception);
            return false;
        }

        return true;
    }

    /**
     * Hook for when partial refund
     * @param $params
     * @return bool
     */
    public function partialRefundProcess($params)
    {
        try {
            if (!isset($params['object'])) {
                return false;
            }

            $id_order = $params['object']->id_order;
            $amount = $params['object']->amount + $params['object']->shipping_cost_amount;

            $order = $this->orderManager->getByPrimary($id_order);

            if ($order === null) {
                $this->logger->error("No order found with ID #$id_order.");
                return false;
            }

            if ($amount <= 0) {
                return false;
            }

            $this->broadcaster->fire(new PGDomainComponentsEventsRefundEvent($order, $amount));
        } catch (Exception $exception) {
            $this->logger->error("Error during ActionObjectOrderSlipAddAfter hook : " . $exception->getMessage(), $exception);
        }

        return true;
    }
}
