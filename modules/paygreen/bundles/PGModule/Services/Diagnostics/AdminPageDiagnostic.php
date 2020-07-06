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

class PGModuleServicesDiagnosticsAdminPageDiagnostic extends PGFrameworkFoundationsAbstractDiagnostic
{
    /** @var PGModuleServicesHandlersAdminMenuHandler */
    private $adminMenuHandler;

    /** @var PGFrameworkServicesLogger */
    private $logger;

    public function __construct(PGModuleServicesHandlersAdminMenuHandler $adminMenuHandler, PGFrameworkServicesLogger $logger)
    {
        $this->adminMenuHandler = $adminMenuHandler;
        $this->logger = $logger;
    }

    public function isValid()
    {
        try {
            $result = $this->adminMenuHandler->isValidBackoffice();
        } catch (Exception $exception) {
            $this->logger->error("Critical error during admin page diagnostic : " . $exception->getMessage());

            $result = false;
        }

        return $result;
    }

    public function resolve()
    {
        try {
            $this->logger->info("Re-install Paygreen admin page.");

            $this->adminMenuHandler->removeBackoffice();

            $this->adminMenuHandler->insertBackoffice();

            return true;
        } catch (Exception $exception) {
            $this->logger->error("Error during Paygreen admin page re-installation : " . $exception->getMessage(), $exception);
        }

        return false;
    }
}
