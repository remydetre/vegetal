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
 * Class PGModuleServicesOfficersSetupOfficer
 * @package PGModule\Services\Officers
 */
class PGModuleServicesOfficersSetupOfficer implements PGFrameworkInterfacesOfficersSetupOfficerInterface
{
    /** @var PGFrameworkServicesHandlersDatabaseHandler */
    private $databaseHandler;

    public function __construct(PGFrameworkServicesHandlersDatabaseHandler $databaseHandler)
    {
        $this->databaseHandler = $databaseHandler;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function retrieveOldInstallation()
    {
        $lastUpdate = null;

        if (!$this->isValidRequest("SELECT 1 FROM `%{database.entities.button.table}` LIMIT 1;")) {
            return null;
        }

        if (!$this->isValidRequest("SELECT 1 FROM `%{database.entities.transaction.table}` LIMIT 1;")) {
            if (!$this->isValidRequest("SELECT `reportPayment` FROM `%{database.entities.button.table}` LIMIT 1;")) {
                return "1.0.0";
            } else {
                return "1.2.8";
            }
        }

        if (!$this->isValidRequest("SELECT 1 FROM `%{database.entities.recurring_transaction.table}` LIMIT 1;")) {
            if (!$this->isValidRequest("SELECT `executedAt` FROM `%{database.entities.button.table}` LIMIT 1;")) {
                return "1.3.0";
            } else {
                return "1.3.1";
            }
        }

        if (!$this->isValidRequest("SELECT 1 FROM `%{database.entities.lock.table}` LIMIT 1;")) {
            if (!$this->isValidRequest("SELECT `perCentPayment` FROM `%{database.entities.button.table}` LIMIT 1;")) {
                return "2.0.0";
            } else {
                return "2.1.0";
            }
        }

        if (!$this->isValidRequest("SELECT 1 FROM `%{database.entities.category_has_payment.table}` LIMIT 1;")) {
            if (!$this->isValidRequest("SELECT `paymentType` FROM `%{database.entities.button.table}` LIMIT 1;")) {
                return "2.2.6";
            } else {
                return "2.2.8";
            }
        }

        if ($this->isValidRequest("SELECT `subOption` FROM `%{database.entities.button.table}` LIMIT 1;")) {
            return "2.3.0";
        }

        $lastUpdate = "2.5.0";

        $lastUpdateConfiguration = $this->databaseHandler->fetchValue("SELECT `value` FROM `%{db.var.prefix}configuration` WHERE `name`='last_update';");
        if ($lastUpdateConfiguration) {
            $lastUpdate = $lastUpdateConfiguration;
        }

        return $lastUpdate;
    }

    /**
     * @param string $sql
     * @return bool
     * @throws Exception
     */
    protected function isValidRequest($sql)
    {
        $sql = $this->databaseHandler->parseQuery($sql);

        try {
            $db = Db::getInstance();

            $db->query($sql);

            $result = ($db->getNumberError() === 0);
        } catch (Exception $exception) {
            $result = false;
        }

        return $result;
    }
}
