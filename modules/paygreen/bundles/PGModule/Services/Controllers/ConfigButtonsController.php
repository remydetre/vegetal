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

class PGModuleServicesControllersConfigButtonsController extends PGFrameworkFoundationsAbstractController
{
    protected $model_buttons = array(
        'id' => 0,
        'label' => null,
        'paymentType' => 'CB',
        'image' => PGFrameworkServicesHandlersPictureHandler::DEFAULT_PICTURE,
        'position' => null,
        'height' => 60,
        'displayType' => 'DEFAULT',
        'integration' => 'EXTERNAL',
        'firstPaymentPart' => null,
        'orderRepeated' => false,
        'discount' => 0,
        'paymentNumber' => 1,
        'paymentReport' => 0,
        'minAmount' => null,
        'maxAmount' => null,
        'paymentMode' => 'CASH',
        'errors' => array(),
        'imageUrl' => null
    );

    public function saveButtonAction(PGFrameworkComponentsIncomingRequest $request)
    {
        /** @var PGDomainServicesManagersButtonManager $buttonManager */
        $buttonManager = $this->getService('manager.button');

        /** @var PGFrameworkComponentsResponsesChainQualifiedMessagesResponse $response */
        $response = $this->buildChainedResponse();

        $id = (int) $request->get('id');

        if ($id) {
            $button = $buttonManager->getByPrimary($id);

            if ($button === null) {
                throw new Exception("Button not found : #$id.");
            }
        } else {
            $button = $buttonManager->getNew();
        }

        if ((bool) $request->get('defaultimg', false)) {
            $button->setImageSrc(null);
        } elseif (array_key_exists('image', $_FILES) && is_array($_FILES['image'])) {
            if ($_FILES['image']['error'] === 0) {
                /** @var PGFrameworkServicesHandlersPictureHandler $mediaHandler */
                $mediaHandler = $this->getService('handler.picture');

                $picture = $mediaHandler->store($_FILES['image']['tmp_name'], $_FILES['image']['name']);

                $button->setImageSrc($picture->getFilename());

                $response->add($response::SUCCESS, "button.form.result.success.picture");
            } elseif ($_FILES['image']['error'] !== 4) {
                $response->add($response::FAILURE, "button.form.errors.upload_picture_error");
            }
        }

        $button
            ->setLabel($request->get('label'))
            ->setImageHeight($request->get('height', 60))
            ->setMinAmount($request->get('minAmount', 0))
            ->setMaxAmount($request->get('maxAmount', 0))
            ->setIntegration($request->get('integration', 'INSITE'))
            ->setDisplayType($request->get('displayType', 'DEFAULT'))
            ->setPosition($request->get('position'))
            ->setPaymentMode($request->get('paymentMode'))
            ->setPaymentType($request->get('paymentType'))
            ->setPaymentNumber($request->get('paymentNumber', 1))
            ->setFirstPaymentPart($request->get('firstPaymentPart', 0))
            ->setOrderRepeated($request->get('orderRepeated', false))
            ->setPaymentReport($request->get('paymentReport', 0))
            ->setDiscount($request->get('discount', 0))
        ;

        if (!$button->getPosition()) {
            $button->setPosition($buttonManager->count() + 1);
        }

        foreach ($buttonManager->check($button) as $error) {
            $response->add($response::FAILURE, $error);
        }

        if ($response->count($response::FAILURE) === 0) {
            $buttonManager->save($button);

            $response->add($response::SUCCESS, "button.form.result.success.button");
        }

        return $response;
    }

    public function deleteButtonAction(PGFrameworkComponentsIncomingRequest $request)
    {
        /** @var PGDomainServicesManagersButtonManager $buttonManager */
        $buttonManager = $this->getService('manager.button');

        /** @var PGFrameworkComponentsResponsesChainQualifiedMessagesResponse $response */
        $response = $this->buildChainedResponse();

        $id = (int) $request->get('id');

        if (!$id) {
            $response->add($response::FAILURE, "button.actions.delete.errors.id_not_found");
        } else {
            $button = $buttonManager->getByPrimary($id);

            if ($button === null) {
                $response->add($response::FAILURE, "button.actions.delete.errors.button_not_found");
            } elseif ($buttonManager->delete($button)) {
                $response->add($response::SUCCESS, "button.actions.delete.result.success");
            } else {
                $response->add($response::FAILURE, "button.actions.delete.result.failure");
            }
        }

        return $response;
    }

    public function displayFormAction()
    {
        /** @var PGModuleServicesHandlersMultiShopHandler $multiShopHandler */
        $multiShopHandler = $this->getService('handler.multi_shop');

        if ($multiShopHandler->isShopContext()) {
            $response = $this->buildFormResponse();
        } else {
            $response = $multiShopHandler->buildOnlyShopLevelResponse(
                'Configuration des boutons de paiement',
                'credit-card'
            );
        }

        return $response;
    }

    private function buildFormResponse()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGDomainServicesPaygreenFacade $paygreenFacade */
        $paygreenFacade = $this->getService('paygreen.facade');

        /** @var PGDomainServicesManagersButtonManager $buttonManager */
        $buttonManager = $this->getService('manager.button');

        /** @var PGFrameworkServicesHandlersPictureHandler $mediaHandler */
        $mediaHandler = $this->getService('handler.picture');

        /** @var PGLocalServicesPrestashopHandler $prestashopHandler */
        $prestashopHandler = $this->getService('handler.prestashop');

        /** @var PGLegacyServicesDiscountHandler $discountHandler */
        $discountHandler = $this->getService('handler.discount');

        /** @var PGDomainServicesSelectorsPaymentTypeSelector $paymentTypeSelector */
        $paymentTypeSelector = $this->getService('selector.payment_type');

        /** @var PGDomainServicesSelectorsPaymentModeSelector $paymentModeSelector */
        $paymentModeSelector = $this->getService('selector.payment_mode');

        $buttonsList = array();

        try {
            if ($paygreenFacade->isConnected()) {
                $buttons = $buttonManager->getAll();

                /**
                 * @var int $key
                 * @var PGModuleEntitiesButton $button
                 */
                foreach ($buttons as $key => $button) {
                    $buttonsList[$key] = $button->toArray();

                    $buttonsList[$key]['errors'] = $buttonManager->check($button);
                    $buttonsList[$key]['imageUrl'] = $mediaHandler->getUrl($button->getImageSrc());
                }

                array_push($buttonsList, $this->model_buttons);

                $response = new PGFrameworkComponentsResponsesTemplateResponse('buttonTab');

                $response
                    ->setTemplate('views/templates/admin', 'configureButtons')
                    ->addData('buttons', $buttonsList)
                    ->addData('formConfig', array(
                        'displayTypes' => $prestashopHandler->getButtonDisplayTypes(),
                        'isHeightFieldDisplayed' => ($prestashopHandler->getVersion() < 1.7),
                        'paymentTypes' => $paymentTypeSelector->getChoices(),
                        'paymentModes' => $paymentModeSelector->getChoices(),
                        'promoCode' => $discountHandler->getAllPromoCode()
                    ))
                    ->addResource('js', 'views/js/buttons.js')
                ;
            } else {
                $response = new PGFrameworkComponentsResponsesTemplateResponse();

                $response
                    ->setTemplate('views/templates/admin', 'tabMessage')
                    ->addData('title', 'button.form.title')
                    ->addData('text', 'backoffice.errors.authentication_required')
                ;
            }
        } catch (Exception $exception) {
            $logger->error("Error during button forms building : " . $exception->getMessage(), $exception);

            $response = new PGFrameworkComponentsResponsesChainQualifiedMessagesResponse();

            $response->add($response::FAILURE, "button.form.errors.display_error");
        }

        return $response;
    }
}
