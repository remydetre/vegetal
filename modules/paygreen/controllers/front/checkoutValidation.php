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

class PaygreenCheckoutValidationModuleFrontController extends ModuleFrontController
{
    /**
     * @param string $name
     * @return object
     */
    protected function getService($name)
    {
        return PGFrameworkContainer::getInstance()->get($name);
    }

    public function postProcess()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGDomainServicesManagersButtonManager $buttonManager */
        $buttonManager = $this->getService('manager.button');

        /** @var PGDomainServicesPaygreenFacade $paygreenFacade */
        $paygreenFacade = $this->getService('paygreen.facade');

        $logger->debug("[CTRL::CheckoutValidation]");

        try {
            if (!isset($_REQUEST['id'])) {
                throw new Exception("Button primary not found.");
            }

            /** @var PGDomainInterfacesEntitiesButtonInterface $button */
            $button = $buttonManager->getByPrimary($_REQUEST['id']);

            $url = $this->buildPayment($button);

            $insite = (($button->getIntegration() === 'INSITE') && $paygreenFacade->isValidInsite());

            if ($insite) {
                $this->displayInsite($button, $url);
            } else {
                return Tools::redirect($url);
            }
        } catch (Exception $exception) {
            $logger->error("Create payment error : " . $exception->getMessage(), $exception);

            $this->displayError($exception);
        }
    }

    /**
     * @param PGDomainInterfacesEntitiesButtonInterface $button
     * @return string
     * @throws PGClientExceptionsPaymentException
     * @throws PGClientExceptionsPaymentRequestException
     */
    protected function buildPayment(PGDomainInterfacesEntitiesButtonInterface $button)
    {
        /** @var PGDomainServicesHandlersPaymentCreationHandler $paymentCreationHandler */
        $paymentCreationHandler = $this->getService('handler.payment_creation');

        /** @var PGModuleProvisionersPrePaymentProvisioner $prePaymentProvisioner */
        $prePaymentProvisioner = new PGModuleProvisionersPrePaymentProvisioner($this->context->cart);

        /** @var PGClientEntitiesResponse $response */
        $response = $paymentCreationHandler->createPayment($prePaymentProvisioner, $button, array(
            'returned_url' => $this->context->link->getModuleLink(PAYGREEN_MODULE_NAME, 'customerReturn'),
            'notified_url' => $this->context->link->getModuleLink(PAYGREEN_MODULE_NAME, 'paymentNotification')
        ));

        if (!$response->isSuccess()) {
            throw new Exception("Unable to create payment data.");
        }

        return $response->data->url;
    }

    /**
     * @param PGDomainInterfacesEntitiesButtonInterface $button
     * @param string $url
     * @throws Exception
     */
    protected function displayInsite(PGDomainInterfacesEntitiesButtonInterface $button, $url)
    {
        /** @var Paygreen $localModule */
        $localModule = $this->getService('local.module');

        /** @var PGDomainServicesManagersPaymentTypeManager $paymentTypeManager */
        $paymentTypeManager = $this->getService('manager.payment_type');

        $shopInfo = $localModule->infoAccount();

        $iframeSize = $paymentTypeManager->getSizeIFrameFromPayment(
            isset($shopInfo->solidarityType) ? $shopInfo->solidarityType : null,
            $button->getPaymentType(),
            $button->getPaymentMode()
        );

        $this->addCSS($localModule->getPathUri() . '/views/css/1.6/front.css', 'all'); // For insite

        $this->context->smarty->assign(array(
            'id' => $button->id(),
            'title' => $button->getLabel(),
            'url' => $url . '?display=insite',
            'return_url' => $this->context->link->getPageLink('order'),
            'minWidthIframe' => $iframeSize['minWidth'],
            'minHeightIframe' => $iframeSize['minHeight'],
        ));

        if ($localModule->vPresta >= 1.7) {
            $src = 'module:paygreen/views/templates/front/1.7/iframe-payment-page.tpl';
        } else {
            $src = '1.6/iframe-payment-page.tpl';
        }

        $this->setTemplate($src);
    }

    /**
     * @param Exception $exception
     */
    protected function displayError(Exception $exception)
    {
        /** @var Paygreen $localModule */
        $localModule = $this->getService('local.module');

        $this->context->smarty->assign(array(
            'title' => "Une erreur est survenue durant la préparation de votre paiement.",
            'message' => null,
            'errors' => null,
            'url' => array(
                'link' => '/index.php?controller=order',
                'text' => "Rejoindre le tunnel d'achat.",
                'reload' => false,
            ),
            'exceptions' => array($exception),
            'env' => PAYGREEN_ENV
        ));

        if ($localModule->vPresta >= 1.7) {
            $src = 'module:paygreen/views/templates/front/1.7/integrated-message-page.tpl';
        } else {
            $src = '1.6/integrated-message-page.tpl';
        }

        $this->setTemplate($src);
    }
}
