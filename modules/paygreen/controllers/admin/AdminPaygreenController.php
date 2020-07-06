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

class AdminPaygreenController extends ModuleAdminController
{
    public function __construct()
    {
        $this->display = 'view';
        $this->bootstrap = false;
        $this->lite_display = true;

        parent::__construct();

        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        $logger->debug("Request incoming in back office endpoint.");

        $this->cleanModule();
    }

    protected function getService($name)
    {
        return PGFrameworkContainer::getInstance()->get($name);
    }

    private function cleanModule()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGFrameworkServicesHandlersCacheHandler $cacheHandler */
        $cacheHandler = $this->getService('handler.cache');

        /** @var PGFrameworkServicesHandlersSetupHandler $setupHandler */
        $setupHandler = $this->getService('handler.setup');

        $logger->debug("Cleaning module.");

        $setupHandler->run($setupHandler::UPGRADE);

        $cacheHandler->clearCache();
    }

    public function renderView()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGServerServicesServer $server */
        $server = $this->getService('server.backoffice');

        /** @var APPbackofficeServicesHandlersMenuHandler $menuHandler */
        $menuHandler = $this->getService('handler.menu');

        /** @var PGFrameworkServicesHandlersOutputHandler $outputHandler */
        $outputHandler = $this->getService('handler.output');

        try {
            $logger->debug("Building backoffice output.");

            $outputHandler->addResource('JS', '/js/backoffice.js');
            $outputHandler->addResource('CSS', '/css/backoffice.css');

            $server->getRequestBuilder()->setConfig('default_action', $menuHandler->getDefaultAction());

            /** @var PGServerComponentsResponsesStringResponse $response */
            $server->run();

            $this->addJS($outputHandler->getResources('JS'));
            $this->addCSS($outputHandler->getResources('CSS'));

            $output = $outputHandler->getContent();
        } catch (Exception $exception) {
            $logger->error("Error during backoffice building : " . $exception->getMessage(), $exception);

            $output = $this->buildErrorOutput();
        }

        return $output;
    }

    private function buildErrorOutput()
    {
        /** @var PGModuleBridgesPrestashopBridge $localModule */
        $localModule = $this->getService('bridge.prestashop');

        /** @var PGFrameworkServicesHandlersTranslatorHandler $translator */
        $translator = $this->getService('handler.translator');

        return $localModule->displayError($translator->get('backoffice.errors.interface_construction'));
    }
}
