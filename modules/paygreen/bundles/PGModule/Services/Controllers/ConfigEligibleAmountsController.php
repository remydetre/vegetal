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

class PGModuleServicesControllersConfigEligibleAmountsController extends PGFrameworkFoundationsAbstractController
{
    public function saveCategoryPaymentsAction(PGFrameworkComponentsIncomingRequest $request)
    {
        /** @var PGDomainServicesManagersCategoryHasPaymentTypeManager $categoryPaymentManager */
        $categoryPaymentManager = $this->getService('manager.category_has_payment_type');

        $category_payments = $request->get('wcpaygreen_category_payments', array());

        $categoryPaymentManager->saveCategoryPayments($category_payments);

        return $this->buildChainedResponse('eligible_amounts.actions.save_category_payments.result.success');
    }

    public function saveShippingPaymentsAction(PGFrameworkComponentsIncomingRequest $request)
    {
        /** @var PGModuleServicesSettings $settings */
        $settings = $this->getService('settings');

        $deactivated_payment_modes = $request->get('paygreen_shipping_deactivated_payment_modes', array());

        $settings->set(PGModuleServicesSettings::SHIPPING_PAYMENTS, $deactivated_payment_modes);

        return $this->buildChainedResponse('eligible_amounts.actions.save_shipping_payments.result.success');
    }

    public function displayCategoryPaymentsFormAction()
    {
        /** @var PGModuleServicesHandlersMultiShopHandler $multiShopHandler */
        $multiShopHandler = $this->getService('handler.multi_shop');

        if ($multiShopHandler->isShopContext()) {
            $response = $this->buildCategoryPaymentsFormResponse();
        } else {
            $response = $multiShopHandler->buildOnlyShopLevelResponse(
                'Activation des moyens de paiement par catégorie',
                'tags'
            );
        }

        return $response;
    }

    public function buildCategoryPaymentsFormResponse()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGDomainServicesPaygreenFacade $paygreenFacade */
        $paygreenFacade = $this->getService('paygreen.facade');

        /** @var PGDomainServicesManagersPaymentTypeManager $paymentTypeManager */
        $paymentTypeManager = $this->getService('manager.payment_type');

        /** @var PGDomainServicesManagersCategoryManager $categoryManager */
        $categoryManager = $this->getService('manager.category');

        try {
            if ($paygreenFacade->isConnected()) {
                $codes = $paymentTypeManager->getCodes();
                $categories = $categoryManager->getRootCategories();

                $response = new PGFrameworkComponentsResponsesTemplateResponse('categoryPaymentsTab');

                $response
                    ->setTemplate('views/templates/admin', 'configureCategoryPayments')
                    ->addData('paymentTypes', $codes)
                    ->addData('categories', $categories)
                    ->addResource('js', 'views/js/eligible-amounts-management.js')
                    ->addResource('css', 'views/css/eligible-amounts.css')
                ;
            } else {
                $response = new PGFrameworkComponentsResponsesTemplateResponse();

                $response
                    ->setTemplate('views/templates/admin', 'tabMessage')
                    ->addData('title', 'eligible_amounts.actions.save_category_payments.title')
                    ->addData('text', 'backoffice.errors.authentication_required')
                ;
            }
        } catch (Exception $exception) {
            $logger->error("Error during category payments forms building : " . $exception->getMessage(), $exception);

            $response = new PGFrameworkComponentsResponsesChainQualifiedMessagesResponse();

            $response->add($response::FAILURE, 'eligible_amounts.actions.save_category_payments.errors.display_form');
        }

        return $response;
    }

    public function displayShippingPaymentsFormAction()
    {
        /** @var PGModuleServicesHandlersMultiShopHandler $multiShopHandler */
        $multiShopHandler = $this->getService('handler.multi_shop');

        if ($multiShopHandler->isShopContext()) {
            $response = $this->buildShippingPaymentsFormResponse();
        } else {
            $response = $multiShopHandler->buildOnlyShopLevelResponse(
                'Gestion des frais de livraison par moyen de paiement',
                'truck'
            );
        }

        return $response;
    }

    public function buildShippingPaymentsFormResponse()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGDomainServicesPaygreenFacade $paygreenFacade */
        $paygreenFacade = $this->getService('paygreen.facade');

        /** @var PGDomainServicesManagersPaymentTypeManager $paymentTypeManager */
        $paymentTypeManager = $this->getService('manager.payment_type');

        /** @var PGFrameworkServicesSettings $settings */
        $settings = $this->getService('settings');

        try {
            if ($paygreenFacade->isConnected()) {
                $codes = $paymentTypeManager->getCodes();
                $shippingPayments = $settings->get(PGModuleServicesSettings::SHIPPING_PAYMENTS);

                $response = new PGFrameworkComponentsResponsesTemplateResponse('shippingTab');

                $response
                    ->setTemplate('views/templates/admin', 'configureShipping')
                    ->addData('paymentTypes', $codes)
                    ->addData('shipping_payments', $shippingPayments)
                    ->addResource('css', 'views/css/eligible-amounts.css')
                ;
            } else {
                $response = new PGFrameworkComponentsResponsesTemplateResponse();

                $response
                    ->setTemplate('views/templates/admin', 'tabMessage')
                    ->addData('title', 'eligible_amounts.actions.save_shipping_payments.title')
                    ->addData('text', 'backoffice.errors.authentication_required')
                ;
            }
        } catch (Exception $exception) {
            $logger->error("Error during shipping payments forms building : " . $exception->getMessage(), $exception);

            $response = new PGFrameworkComponentsResponsesChainQualifiedMessagesResponse();

            $response->add($response::FAILURE, 'eligible_amounts.actions.save_shipping_payments.errors.display_form');
        }

        return $response;
    }
}
