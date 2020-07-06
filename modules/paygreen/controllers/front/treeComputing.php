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

class PaygreenTreeComputingModuleFrontController extends ModuleFrontController
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
    public function postProcess()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        $logger->debug("[CTRL::TreeComputing]");

        try {
            if (empty($_POST)) {
                $logger->error("Tree computing controller receive empty data.");
            } elseif (!$this->checkData($_POST)) {
                $logger->error("Tree computing controller receive invalid data.");
            } else {
                $this->setClientFingerprint($_POST);
                die();
            }
        } catch (Exception $exception) {
            $logger->error("Tree computing error : " . $exception->getMessage(), $exception);
        }

        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        die();
    }

    /**
     * @param array $data
     * @return bool
     */
    private function checkData($data)
    {
        if (isset($data['client']) && !empty($data['client']) &&
            isset($data['startAt']) && !empty($data['startAt']) &&
            isset($data['useTime']) && !empty($data['useTime']) &&
            isset($data['nbImage']) && !empty($data['nbImage']) &&
            isset($data['device']) && !empty($data['device']) &&
            isset($data['browser']) && !empty($data['browser'])
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param array $data
     * @throws Exception
     */
    private function setClientFingerprint($data)
    {
        /** @var PGLegacyServicesFingerPrintHandler $fingerPrintHandler */
        $fingerPrintHandler = $this->getService('handler.fingerprint');

        Context::getContext()->cookie->__set('fingerprint', $data['client']);

        $fingerPrintHandler->insertFingerprintData($data);
    }
}
