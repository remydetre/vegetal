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

class PGDomainServicesListenersRefundListener
{
    /** @var PGDomainServicesHandlersRefundHandler */
    private $refundHandler;

    /** @var PGFrameworkServicesHandlersBehaviorHandler */
    private $behaviorHandler;

    /** @var PGFrameworkServicesLogger */
    private $logger;

    public function __construct(PGDomainServicesHandlersRefundHandler $refundHandler, PGFrameworkServicesHandlersBehaviorHandler $behaviorHandler, PGFrameworkServicesLogger $logger)
    {
        $this->refundHandler = $refundHandler;
        $this->behaviorHandler = $behaviorHandler;
        $this->logger = $logger;
    }

    public function listen(PGDomainComponentsEventsRefundEvent $event)
    {
        $this->logger->debug("Refund event catched.");

        try {
            $isRefundActivated = (bool) $this->behaviorHandler->get('transmission_on_refund');

            if ($isRefundActivated) {
                $this->logger->debug("Online refund activated.");

                $this->refundHandler->refundOrder($event->getOrder(), $event->getAmount());
            }
        } catch (PGDomainExceptionsUnrefundableException $exception) {
            $this->logger->notice("Order #{$event->getOrder()->id()} is not refundable : " . $exception->getMessage());
        } catch (Exception $exception) {
            $this->logger->error("Error during refund order #{$event->getOrder()->id()} : " . $exception->getMessage(), $exception);
        }
    }
}
