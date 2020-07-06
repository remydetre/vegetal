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
 * Class PGDomainServicesHandlersTokenizeHandler
 * @package PGFramework\Services\Handlers
 */
class PGDomainServicesHandlersTokenizeHandler extends PGFrameworkFoundationsAbstractObject
{
    /** @var PGFrameworkServicesBroadcaster */
    private $broadcaster;

    /** @var PGFrameworkServicesHandlersBehaviorHandler */
    private $behaviorHandler;

    /** @var PGDomainServicesManagersTransactionManager */
    private $transactionManager;

    /** @var PGClientServicesApiFacade */
    private $apiFacade;

    /** @var PGFrameworkServicesLogger */
    private $logger;

    public function __construct(
        PGFrameworkServicesBroadcaster $broadcaster,
        PGFrameworkServicesLogger $logger
    ) {
        $this->broadcaster = $broadcaster;
        $this->logger = $logger;
    }

    /**
     * @param PGDomainServicesManagersTransactionManager $transactionManager
     */
    public function setTransactionManager($transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param PGDomainServicesPaygreenFacade $paygreenFacade
     */
    public function setPaygreenFacade(PGDomainServicesPaygreenFacade $paygreenFacade)
    {
        $this->apiFacade = $paygreenFacade->getApiFacade();
    }

    /**
     * @param PGFrameworkServicesHandlersBehaviorHandler $behaviorHandler
     */
    public function setBehaviorHandler($behaviorHandler)
    {
        $this->behaviorHandler = $behaviorHandler;
    }

    /**
     * @param PGDomainInterfacesEntitiesOrderInterface $order
     * @return bool
     * @throws Exception
     */
    public function processTokenizedPayments(PGDomainInterfacesEntitiesOrderInterface $order)
    {
        $this->logger->debug("Confirm waiting payments for order : '{$order->id()}'.");

        $isTransmissionBehaviorActivated = (bool) $this->behaviorHandler->get('transmission_on_delivery_confirmation');

        if ($isTransmissionBehaviorActivated) {
            if (!$this->transactionManager->hasTransaction($order->id())) {
                $this->logger->warning("No associated transaction found for order '{$order->id()}'.");
                return true;
            }

            $transaction = $this->transactionManager->getByOrderPrimary($order->id());

            if ($transaction->getMode() === PGDomainData::MODE_TOKENIZE) {
                $this->logger->debug("Tokenized payment validation is running.");

                $pid = $transaction->getPid();

                $result = $this->apiFacade
                    ->validDeliveryPayment($pid)
                    ->isSuccess()
                    ;

                if ($result) {
                    $this->broadcaster->fire(new PGDomainComponentsEventsTokenizeConfirmationEvent($order, array($transaction)));
                }

                return $result;
            }
        }

        return true;
    }
}
