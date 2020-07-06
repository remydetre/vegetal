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
 * Class PaygreenTransactionLockManager
 *
 * @package PGDomain\Services\Managers
 * @method PGDomainInterfacesRepositoriesLockRepositoryInterface getRepository
 */
class PGDomainServicesManagersLockManager extends PGFrameworkFoundationsAbstractManager
{
    const LOCK_DURATION = 30;
    const LOCK_REPEAT = 3;
    const LOCK_WAITING = 2;

    /**
     * @param $pid
     * @return PGDomainInterfacesEntitiesLockInterface|null
     */
    public function getByPid($pid)
    {
        return $this->getRepository()->findByPid($pid);
    }

    /**
     * @param $pid
     * @return PGDomainInterfacesEntitiesLockInterface
     * @throws Exception
     */
    public function create($pid)
    {
        return $this->getRepository()->create($pid, new DateTime());
    }

    /**
     * @param string $pid
     * @param int $repeat
     * @return bool
     * @throws Exception
     */
    public function isLocked($pid, $repeat = 0)
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        if ($repeat > self::LOCK_REPEAT) {
            return true;
        }

        $lock = $this->getByPid($pid);

        if ($lock === null) {
            try {
                $logger->debug("Lock creating for PID : $pid");

                $this->create($pid);

                $logger->info("Lock created for PID : $pid");

                return false;
            } catch (Exception $exception) {
                $logger->warning("Lock creating error for PID : $pid");

                return $this->waitForUnlocking($pid, $repeat);
            }
        } elseif (($lock->getLockedAt() > $this->getLockTime())) {
            $logger->warning("Lock actif for PID : $pid");

            return $this->waitForUnlocking($pid, $repeat);
        } else {
            $logger->debug("Lock updating for PID : $pid");

            if ($this->getRepository()->updateLock($lock, $this->getLockTime())) {
                $logger->info("Lock updated for PID : $pid");

                return false;
            } else {
                $logger->warning("Lock updating error for PID : $pid");

                return $this->waitForUnlocking($pid, $repeat);
            }
        }
    }

    /**
     * Returns the current time minus the locking time
     * @return DateTime
     * @throws Exception
     */
    protected function getLockTime()
    {
        return new DateTime('-' . self::LOCK_DURATION . ' seconds');
    }

    /**
     * @param string $pid
     * @param int $repeat
     * @return bool
     * @throws Exception
     */
    protected function waitForUnlocking($pid, $repeat)
    {
        sleep(self::LOCK_WAITING);

        return $this->isLocked($pid, ++ $repeat);
    }
}
