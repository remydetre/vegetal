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


class PGLegacyServicesBackofficeHandler extends PGFrameworkFoundationsAbstractObject
{
    private $viewActions = array(
        'displayAccountHeader@configAccount',
        'displayConfigForm@configModule',
        'displayForm@configButtons',
        'displayCategoryPaymentsForm@configEligibleAmounts',
        'displayShippingPaymentsForm@configEligibleAmounts',
        'displayTopPositionForm@configModule',
        'displayData@system'
    );

    private $formActions = array(
        'submitPaygreenModuleHook' => 'refreshTopPosition@configModule',
        'submitPaygreenModuleAccount' => 'toggleAccountActivation@configAccount',
        'submitPaygreenModule' => 'saveModuleConfiguration@configModule',
        'submitPaygreenModuleButton' => 'saveButton@configButtons',
        'submitPaygreenModuleButtonDelete' => 'deleteButton@configButtons',
        'submitCategoryPayments' => 'saveCategoryPayments@configEligibleAmounts',
        'submitShippingPayments' => 'saveShippingPayments@configEligibleAmounts',
        'downloadLogFile' => 'downloadLogFile@system',
        'deleteLogFile' => 'deleteLogFile@system'
    );

    /**
     * Load the configuration form
     * @return string
     * @throws Exception
     * @throws OAuthException
     */
    public function getContent()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGLegacyServicesConnexionHandler $connexionHandler */
        $connexionHandler = $this->getService('handler.connexion');

        /** @var Paygreen $localModule */
        $localModule = $this->getService('local.module');

        /** @var PGLocalServicesSmartyTranslator $smartyTranslator */
        $smartyTranslator = $this->getService('smarty.translator');

        /** @var PGFrameworkServicesHandlersCacheHandler $cacheHandler */
        $cacheHandler = $this->getService('handler.cache');

        /** @var PGFrameworkServicesHandlersTranslatorHandler $translator */
        $translator = $this->getService('handler.translator');

        /** @var PGFrameworkServicesHandlersSetupHandler $setupHandler */
        $setupHandler = $this->getService('handler.setup');

        $output = '';

        try {
            $localModule
                ->getContext()
                ->smarty
                ->registerPlugin('modifier', 'pgtrans', array($smartyTranslator, 'pgtrans'))
                ->registerPlugin('modifier', 'pgtranslines', array($smartyTranslator, 'pgtranslines'));
            $localModule
                ->getContext()
                ->controller
                ->addCSS($localModule->getLocalPath() . 'views/css/back.css');

            $setupHandler->run($setupHandler::UPGRADE);

            $this->saveBaseUrl();

            try {
                if (Tools::getValue('connect') == 'true' || Tools::getValue('code') != '') {
                    $connexionHandler->auth();
                }

                if (Tools::getValue('deconnect')) {
                    if (Tools::getValue('deconnect') == 'true') {
                        $connexionHandler->logout();
                    }
                }
            } catch (Exception $exception) {
                $logger->critical("Error during connexion management : " . $exception->getMessage(), $exception);
                $output .= $localModule->displayError($translator->get('backoffice.errors.connection'));
            }

            $cacheHandler->clearCache();

            $request = new PGFrameworkComponentsIncomingRequest($_POST);

            $output .= $this->buildFormProcessingOutput($request);
            $output .= $this->buildFormDisplayingOutput($request);
        } catch (Exception $exception) {
            $logger->critical("Error during form computing : " . $exception->getMessage(), $exception);
            $output .= $localModule->displayError($translator->get('backoffice.errors.interface_global'));
        }

        return $output;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function buildFormProcessingOutput(PGFrameworkComponentsIncomingRequest $request)
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var Paygreen $localModule */
        $localModule = $this->getService('local.module');

        /** @var PGLegacyServicesOutputHandler $outputHandler */
        $outputHandler = $this->getService('handler.output');

        /** @var PGFrameworkServicesDispatcher $dispatcher */
        $dispatcher = $this->getService('dispatcher');

        /** @var PGFrameworkServicesHandlersTranslatorHandler $translator */
        $translator = $this->getService('handler.translator');

        $output = '';

        try {
            foreach ($this->formActions as $form => $localization) {
                if (Tools::isSubmit($form)) {
                    /** @var PGFrameworkComponentsResponsesChainQualifiedMessagesResponse $response */
                    $response = $dispatcher->dispatch($request, $localization);
                    $output = $outputHandler->buildMessagesOutput($response);
                    break;
                }
            }
        } catch (Exception $exception) {
            $logger->error("Error during form processing : " . $exception->getMessage(), $exception);

            $output = $localModule->displayError($translator->get('backoffice.errors.processing'));
        }

        return $output;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function buildFormDisplayingOutput(PGFrameworkComponentsIncomingRequest $request)
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var Paygreen $localModule */
        $localModule = $this->getService('local.module');

        /** @var PGLegacyServicesOutputHandler $outputHandler */
        $outputHandler = $this->getService('handler.output');

        /** @var PGFrameworkServicesDispatcher $dispatcher */
        $dispatcher = $this->getService('dispatcher');

        /** @var PGFrameworkServicesHandlersTranslatorHandler $translator */
        $translator = $this->getService('handler.translator');

        $output = '';

        try {
            foreach ($this->viewActions as $localization) {
                /** @var mixed $response */
                $response = $dispatcher->dispatch($request, $localization);

                switch (true) {
                    case ($response instanceof PGFrameworkComponentsResponsesTemplateResponse):
                        $output .= $outputHandler->buildTemplateOutput($response);
                        break;
                    case ($response instanceof PGFrameworkComponentsResponsesChainQualifiedMessagesResponse):
                        $output .= $outputHandler->buildMessagesOutput($response);
                        break;
                    case ($response instanceof PGFrameworkComponentsResponsesHTMLResponse):
                        $output .= $outputHandler->buildHTMLOutput($response);
                        break;
                    default:
                        $class = get_class($response);
                        throw new LogicException("Unrecognized response type : '$class'.");
                }
            }
        } catch (Exception $exception) {
            $logger->error("Error during backoffice building : " . $exception->getMessage(), $exception);

            $output .= $localModule->displayError($translator->get('backoffice.errors.interface_construction'));
        }

        return $output;
    }

    private function saveBaseUrl()
    {
        /** @var PGModuleServicesSettings $settings */
        $settings = $this->getService('settings');

        $PaygreenAdminPanel = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        if ($settings->get('URL_BASE') == null) {
            $settings->set('URL_BASE', $PaygreenAdminPanel);
        }

        if (strcmp($settings->get('URL_BASE'), $PaygreenAdminPanel) !== 0) {
            $settings->set('URL_BASE', $PaygreenAdminPanel);
        }
    }
}
