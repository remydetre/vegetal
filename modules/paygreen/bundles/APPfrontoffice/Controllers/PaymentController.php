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

class APPfrontofficeControllersPaymentController extends PGServerFoundationsAbstractController
{
    /**
     * @return PGServerComponentsResponsesForwardResponse
     * @throws Exception
     */
    public function validatePaymentAction()
    {
        /** @var PGDomainServicesPaygreenFacade $paygreenFacade */
        $paygreenFacade = $this->getService('paygreen.facade');

        /** @var PGDomainServicesHandlersPaymentCreationHandler $paymentCreationHandler */
        $paymentCreationHandler = $this->getService('handler.payment_creation');

        try {
            /** @var PGDomainInterfacesEntitiesButtonInterface $button */
            $button = $this->retrieveButtonFromRequest();

            $insite = (($button->getIntegration() === 'INSITE') && $paygreenFacade->verifyInsiteValidity());

            if ($insite) {
                $target = $paymentCreationHandler->getTarget('insite');
                return $this->forward($target, array('button' => $button));
            } else {
                $target = $paymentCreationHandler->getTarget('external');
                return $this->forward($target, array('button' => $button));
            }
        } catch (Exception $exception) {
            $this->getLogger()->error("Validation payment error : " . $exception->getMessage(), $exception);

            return $this->forward('displayNotification@front.notification', array(
                'title' => 'frontoffice.payment.errors.creation.title',
                'message' => 'frontoffice.payment.errors.creation.message',
                'exceptions' => array($exception)
            ));
        }
    }

    /**
     * @return PGServerComponentsResponsesForwardResponse|PGServerComponentsResponsesRedirectionResponse
     * @throws Exception
     */
    public function redirectAction()
    {
        /** @var PGDomainServicesHandlersPaymentCreationHandler $paymentCreationHandler */
        $paymentCreationHandler = $this->getService('handler.payment_creation');

        try {
            /** @var PGDomainInterfacesEntitiesButtonInterface $button */
            $button = $this->retrieveButtonFromRequest();

            $url = $paymentCreationHandler->buildPayment($button);

            return $this->redirect($url);
        } catch (Exception $exception) {
            $this->getLogger()->error("Create payment error : " . $exception->getMessage(), $exception);

            return $this->forward('displayNotification@front.notification', array(
                'title' => 'frontoffice.payment.errors.redirection.title',
                'message' => 'frontoffice.payment.errors.redirection.message',
                'exceptions' => array($exception)
            ));
        }
    }

    /**
     * @return PGServerComponentsResponsesHTTPResponse
     * @throws Exception
     */
    public function receiveAction()
    {
        /** @var PGDomainServicesProcessorsPaymentValidationProcessor $processor */
        $processor = $this->getService('processor.payment_validation');

        $response = new PGServerComponentsResponsesHTTPResponse($this->getRequest());

        try {
            $pid = $this->getRequest()->get('pid');

            $this->getLogger()->info("Receive IPN for PID : '$pid'.");

            $task = new PGDomainTasksPaymentValidationTask($pid);

            $processor->execute($task);

            switch ($task->getStatus()) {
                case PGDomainTasksPaymentValidationTask::STATE_SUCCESS:
                case PGDomainTasksPaymentValidationTask::STATE_PAYMENT_REFUSED:
                case PGDomainTasksPaymentValidationTask::STATE_PAYMENT_ABORTED:
                    $response->setStatus(200);
                    break;

                case PGDomainTasksPaymentValidationTask::STATE_PID_NOT_FOUND:
                case PGDomainTasksPaymentValidationTask::STATE_PID_LOCKED:
                case PGDomainTasksPaymentValidationTask::STATE_INCONSISTENT_CONTEXT:
                case PGDomainTasksPaymentValidationTask::STATE_FATAL_ERROR:
                case PGDomainTasksPaymentValidationTask::STATE_WORKFLOW_ERROR:
                case PGDomainTasksPaymentValidationTask::STATE_PAYGREEN_UNAVAILABLE:
                default:
                    $statusName = $task->getStatusName($task->getStatus());
                    $this->getLogger()->error("Notification failure. Final state : '$statusName'.'");
                    $response->setStatus(500);
            }
        } catch (Exception $exception) {
            $this->getLogger()->critical("Notification exception : " . $exception->getMessage(), $exception);
            $response->setStatus(500);
        }

        return $response;
    }

    /**
     * @return PGServerComponentsResponsesForwardResponse|PGServerComponentsResponsesTemplateResponse
     * @throws Exception
     */
    public function displayIFramePaymentAction()
    {
        /** @var PGDomainServicesHandlersPaymentCreationHandler $paymentCreationHandler */
        $paymentCreationHandler = $this->getService('handler.payment_creation');

        try {
            /** @var PGDomainInterfacesEntitiesButtonInterface $button */
            $button = $this->retrieveButtonFromRequest();

            $this->getLogger()->debug("Display IFrame payment for button primary '{$button->id()}'.");

            $url = $paymentCreationHandler->buildPayment($button);

            $iframeSize = $this->getIFrameSizes($button);

            return $this->buildTemplateResponse('page-payment-iframe', array(
                'title' => $button->getLabel(),
                'id' => $button->id(),
                'url' => PGFrameworkToolsQuery::addParameters($url, array('display' => 'insite')),
                'minWidthIframe' => $iframeSize['minWidth'],
                'minHeightIframe' => $iframeSize['minHeight'],
                'return_url' => $this->getLinker()->buildLocalUrl('checkout')
            ));
        } catch (Exception $exception) {
            $this->getLogger()->error("Create payment error : " . $exception->getMessage(), $exception);

            return $this->forward('displayNotification@front.notification', array(
                'title' => 'frontoffice.payment.errors.iframe.title',
                'message' => 'frontoffice.payment.errors.iframe.message',
                'exceptions' => array($exception)
            ));
        }
    }

    /**
     * @return PGDomainInterfacesEntitiesButtonInterface
     * @throws Exception
     */
    protected function retrieveButtonFromRequest()
    {
        /** @var PGDomainInterfacesEntitiesButtonInterface $button */
        $button = null;

        if ($this->getRequest()->has('button')) {
            $button = $this->getRequest()->get('button');
        } elseif ($this->getRequest()->has('id')) {
            $id_button = $this->getRequest()->get('id');

            /** @var PGDomainServicesManagersButtonManager $buttonManager */
            $buttonManager = $this->getService('manager.button');

            $button = $buttonManager->getByPrimary($id_button);
        } else {
            throw new Exception("Payment actions require button parameter.");
        }

        if ($button === null) {
            throw new Exception("Payment button not found.");
        }

        return $button;
    }

    /**
     * @param PGDomainInterfacesEntitiesButtonInterface $button
     * @return array
     * @throws PGClientExceptionsPaymentException
     * @throws PGClientExceptionsPaymentRequestException
     * @throws PGDomainExceptionsPaygreenAccountException
     */
    protected function getIFrameSizes(PGDomainInterfacesEntitiesButtonInterface $button)
    {
        /** @var PGDomainServicesManagersPaymentTypeManager $paymentTypeManager */
        $paymentTypeManager = $this->getService('manager.payment_type');

        /** @var PGDomainServicesPaygreenFacade $paygreenFacade */
        $paygreenFacade = $this->getService('paygreen.facade');

        $shopInfo = $paygreenFacade->getAccountInfos();

        return $paymentTypeManager->getSizeIFrameFromPayment(
            isset($shopInfo->solidarityType) ? $shopInfo->solidarityType : null,
            $button->getPaymentType(),
            $button->getPaymentMode()
        );
    }
}
