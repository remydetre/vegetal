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

class PaygreenFrontModuleFrontController extends ModuleFrontController
{
    /**
     * @param string $name
     * @return object
     * @throws Exception
     */
    protected function getService($name)
    {
        return PGFrameworkContainer::getInstance()->get($name);
    }

    /**
     * @throws Exception
     */
    public function initContent()
    {
        parent::initContent();

        /** @var PGModuleBridgesPrestashopBridge $prestashopBridge */
        $prestashopBridge = $this->getService('bridge.prestashop');

        switch ($prestashopBridge->getPlateformVersion()) {
            case 1.6:
                $src = 'front-layout-una-sextus.tpl';
                break;
            case 1.7:
                $src = 'module:paygreen/views/templates/front/front-layout-una-septimus.tpl';
                break;
            default:
                throw new Exception("PayGreen module only support Prestashop 1.6 or 1.7.");
        }

        $this->setTemplate($src);
    }

    /**
     * @throws Exception
     */
    public function postProcess()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGServerServicesServer $server */
        $server = $this->getService('server.front');

        $logger->debug("Request incoming in front office endpoint.");

        try {
            $server->run();
            $this->buildOutput();
        } catch (Exception $exception) {
            $logger->error("Front controller error : " . $exception->getMessage(), $exception);
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            die();
        }
    }

    /**
     * @throws Exception
     */
    protected function buildOutput()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGFrameworkServicesHandlersOutputHandler $outputHandler */
        $outputHandler = $this->getService('handler.output');

        $logger->debug("Building frontoffice output.");

        $this->insertResources();

        $content = $outputHandler->getContent();

        $this->context->smarty->assign(array(
            'paygreen_content' => $content
        ));
    }

    /**
     * @throws Exception
     */
    protected function insertResources()
    {
        /** @var PGFrameworkServicesHandlersOutputHandler $outputHandler */
        $outputHandler = $this->getService('handler.output');

        /** @var PGModuleBridgesPrestashopBridge $prestashopBridge */
        $prestashopBridge = $this->getService('bridge.prestashop');

        if ($prestashopBridge->getPlateformVersion() >= 1.7) {
            foreach ($outputHandler->getResources('JS') as $filename) {
                $this->registerJavascript(sha1($filename), $filename);
            }

            foreach ($outputHandler->getResources('CSS') as $filename) {
                $this->registerStylesheet(sha1($filename), $filename);
            }
        } else {
            $this->addJS($outputHandler->getResources('JS'));
            $this->addCSS($outputHandler->getResources('CSS'));
        }
    }
}
