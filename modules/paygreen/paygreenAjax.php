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
 * @version   2.5.4
 */

require_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', '..', 'config', 'config.inc.php'));
require_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', '..', 'init.php'));
require_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), 'paygreen.php'));

$paygreen = new Paygreen();

class API
{
    public function __construct()
    {
        $this->routes();
    }

    protected function getService($name)
    {
        return PGFrameworkContainer::getInstance()->get($name);
    }

    private function routes()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == 'POST') {
            $data = $_POST;
            $this->setClientFingerprint($data);
        }
    }

    private function checkDatas($data)
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

    private function setClientFingerprint($data)
    {
        /** @var PGLegacyServicesFingerPrintHandler $fingerPrintHandler */
        $fingerPrintHandler = $this->getService('handler.fingerprint');

        if (isset($data) && !empty($data) && $this->checkDatas($data) == true) {
            Context::getContext()->cookie->__set('fingerprint', $data['client']);

            $fingerPrintHandler->insertFingerprintData($data);
        } else {
            $datas = array('Error' => 'required parameters not given');
            header('Content-Type: application/json');
            echo json_encode($datas);
        }
    }
}

$api = new API();
