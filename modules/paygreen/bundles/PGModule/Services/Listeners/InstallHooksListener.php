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

class PGModuleServicesListenersInstallHooksListener
{
    private $hooks = array(
        'header',
        'displayPaymentReturn',
        'ActionObjectOrderSlipAddAfter',
        'postUpdateOrderStatus',
        'displayFooter'
    );

    /** @var PGModuleBridgesPrestashopBridge */
    private $module;

    /** @var PGFrameworkServicesLogger */
    private $logger;

    public function __construct(PGModuleBridgesPrestashopBridge $localModule, PGFrameworkServicesLogger $logger)
    {
        $this->module = $localModule;
        $this->logger = $logger;

        if ($localModule->getPlateformVersion() === 1.6) {
            $this->hooks[] = 'displayBackOfficeHeader';
            $this->hooks[] = 'displayPayment';
        } elseif ($localModule->getPlateformVersion() === 1.7) {
            $this->hooks[] = 'paymentOptions';
        }
    }

    /**
     * @throws Exception
     */
    public function registerHooks()
    {
        foreach ($this->hooks as $hook) {
            $this->logger->debug("Registering hook : '$hook'.");

            if (!$this->module->registerHook($hook)) {
                throw new Exception("Installation failed for target Hook : '$hook'.");
            }
        }

        $this->logger->notice("Hooks successfully registered.");
    }

    /**
     * @throws Exception
     */
    public function unregisterHooks()
    {
        foreach ($this->hooks as $hook) {
            $this->module->unregisterHook($hook);
        }

        $this->logger->notice("Hooks successfully unregistered.");
    }

    /**
     * set at 1st position all hook present in $listhook
     */
    public function updateHookPositions()
    {
        try {
            $idPaygreen = (int) Db::getInstance()->getValue(
                'SELECT id_module FROM ' . _DB_PREFIX_ . 'module
            WHERE name = \'paygreen\''
            );

            foreach ($this->hooks as $hook) {
                $idHook = (int) Db::getInstance()->getValue(
                    'SELECT id_hook FROM ' . _DB_PREFIX_ . 'hook
                WHERE name = \'' . pSQL($hook) . '\''
                );

                $idModule = (int) Db::getInstance()->getValue(
                    'SELECT id_module FROM ' . _DB_PREFIX_ . 'hook_module
                WHERE position = 1 AND id_hook = ' . $idHook
                );

                $positionPaygreen = (int) Db::getInstance()->getValue(
                    'SELECT position FROM ' . _DB_PREFIX_ . 'hook_module
                WHERE id_hook = ' . $idHook . ' AND id_module = ' . $idPaygreen
                );

                $updateModulePosition = Db::getInstance()->update(
                    'hook_module',
                    array('position' => $positionPaygreen),
                    'id_module = ' . $idModule . ' AND id_hook = ' . $idHook
                );

                if ($updateModulePosition === false) {
                    $this->logger->error('Query FAILED.');

                    throw new Exception("Unable to update hook position : '$hook'.");
                }

                $updateModulePosition = Db::getInstance()->update(
                    'hook_module',
                    array('position' => 1),
                    'id_module = ' . (int)$idPaygreen . ' AND id_hook = ' . $idHook
                );

                if ($updateModulePosition === false) {
                    $this->logger->error('Query FAILED.');
                }
            }

            $this->logger->notice("Hook positions successfully updated.");

            return true;
        } catch (Exception $exception) {
            $this->logger->error("Error during refreshing hooks : " . $exception->getMessage(), $exception);
        }
    }
}
