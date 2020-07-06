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
 * Class PGModuleBridgesPrestashopBridge
 *
 * @method string l(string $text, string $module = null)
 * @method string getLocalPath()
 * @method string displayError(string $text)
 * @method string displayNotification(string $text)
 * @method string displayConfirmation(string $text)
 * @method string displayInformation(string $text)
 * @method bool registerHook(string $text)
 * @method bool validateOrder()
 * @method CurrencyCore|CurrencyCore[]|false getCurrency(int $current_id_currency = null)
 *
 * @property mixed $context
 * @property mixed $local_path
 * @property mixed $smarty
 * @property mixed $active
 * @property string $_path
 * @property int $id
 */
abstract class PGModuleBridgesPrestashopBridge extends PaymentModule
{
    /**
     * PGModuleBridgesPrestashopBridge constructor.
     * @throws Exception
     */
    public function __construct()
    {
        PGFrameworkContainer::getInstance()->set('bridge.prestashop', $this);

        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        $logger->debug("Paygreen module initialization.");

        try {
            parent::__construct();

            /** @var PGFrameworkServicesHandlersTranslatorHandler $translator */
            $translator = $this->getService('handler.translator');

            $this->description = $translator->get('module.description');

            $this->confirmUninstall = $translator->get('module.uninstall.confirmation');

            $logger->notice("Paygreen module correctly initialized.");

            /** @var PGFrameworkServicesHandlersSetupHandler $setupHandler */
            $setupHandler = $this->getService('handler.setup');

            $setupHandler->run(PGFrameworkServicesHandlersSetupHandler::UPGRADE);

            if ($setupHandler->isLatest()) {
                $this->warning = $this->verifyConfiguration();
            }
        } catch (Exception $exception) {
            $logger->emergency("Error during module initialization : " . $exception->getMessage(), $exception);

            throw $exception;
        }
    }

    protected function getService($name)
    {
        return PGFrameworkContainer::getInstance()->get($name);
    }

    /**
     * @return ContextCore
     */
    public function getContext()
    {
        return $this->context;
    }

    public function isActive()
    {
        return (bool) $this->active;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getPlateformVersion()
    {
        return PAYGREEN_PLATEFORM_VERSION;
    }

    /**
     * Install PayGreen module
     */
    public function install()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGFrameworkServicesHandlersSetupHandler $setupHandler */
        $setupHandler = $this->getService('handler.setup');

        try {
            if (!parent::install()) {
                return false;
            }

            $logger->info("Running Paygreen install process.");

            $setupHandler->install();

            return true;
        } catch (Exception $exception) {
            $file = $exception->getFile();
            $line = $exception->getLine();
            $message = "Error during Paygreen install ($file#$line) : {$exception->getMessage()}";

            PrestaShopLogger::addLog($message, 4);

            if (isset($logger)) {
                $logger->critical("Error during installation : " . $exception->getMessage(), $exception);
            }

            return false;
        }
    }

    /*
     * Uninstall method
     */
    public function uninstall()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGFrameworkServicesHandlersSetupHandler $setupHandler */
        $setupHandler = $this->getService('handler.setup');

        try {
            $setupHandler->uninstall();

            return parent::uninstall();
        } catch (Exception $exception) {
            $file = $exception->getFile();
            $line = $exception->getLine();
            $message = "Error during Paygreen uninstall ($file#$line) : {$exception->getMessage()}";

            PrestaShopLogger::addLog($message, 4);

            if (isset($logger)) {
                $logger->critical("Error during uninstallation : " . $exception->getMessage(), $exception);
            }

            return false;
        }
    }

    //HOOK SECTION

    /**
     * @param string $hookName
     * @param string $methodName
     * @param array $arguments
     * @return mixed
     * @throws Exception
     */
    private function callHook($hookName, $methodName, array $arguments = array())
    {
        /** @var PGFrameworkServicesHandlersHookHandler $hookHandler */
        $hookHandler = $this->getService('handler.hook');

        return $hookHandler->run($hookName, $methodName, $arguments);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function hookDisplayFooter()
    {
        return $this->callHook('integration', 'displayFooter');
    }

    /**
     * @throws Exception
     */
    public function hookHeader()
    {
        return $this->callHook('integration', 'registerHeader');
    }

    /**
     * @param array $params
     * @return bool
     * @throws Exception
     */
    public function hookPostUpdateOrderStatus($params)
    {
        return $this->callHook('order', 'updateOrderState', array($params));
    }

    /**
     * Hook for when partial refund
     * @param $params
     * @return bool
     * @throws Exception
     */
    public function hookActionObjectOrderSlipAddAfter($params)
    {
        return $this->callHook('order', 'partialRefundProcess', array($params));
    }

    /**
     * Hook for different payment options on 1.5/1.6
     * @param $params
     * @return bool
     * @throws Exception
     */
    public function hookDisplayPayment($params)
    {
        return $this->callHook('checkout', 'unaSextusDisplay', array($params));
    }

    /**
     * Hook for different payment options on 1.7
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function hookPaymentOptions($params)
    {
        return $this->callHook('checkout', 'unaSeptimusDisplay', array($params));
    }

    /**
     * Utilisé par PS 1.6 si absence de hookPaymentReturn.
     * Utilisé par PS 1.6 si présence de hookPaymentReturn.
     * Utilisé par PS 1.7 si absence de hookPaymentReturn.
     * Utilisé par PS 1.7 si présence de hookPaymentReturn.
     * @return string
     * @throws Exception
     */
    public function hookDisplayPaymentReturn()
    {
        return $this->callHook('payment', 'displayPaymentReturn');
    }

    /**
     * @return string Empty string for no error else error string message
     * @throws Exception
     */
    public function verifyConfiguration()
    {
        /** @var PGDomainServicesPaygreenFacade $paygreenFacade */
        $paygreenFacade = $this->getService('paygreen.facade');

        /** @var PGFrameworkServicesHandlersTranslatorHandler $translator */
        $translator = $this->getService('handler.translator');

        $warning = '';

        if (!$paygreenFacade->isConfigured()) {
            $warning = $translator->get('pages.account.notification.needLogin');
        } elseif (!$paygreenFacade->isConnected()) {
            $warning = $translator->get('pages.account.notification.incorrectKey');
        }

        return $warning;
    }

    /**
     * @throws Exception
     */
    public function getContent()
    {
        /** @var PGServerServicesLinker $linker */
        $linker = $this->getService('linker');

        $url = $linker->buildBackOfficeUrl();

        Tools::redirect($url);
    }
}
