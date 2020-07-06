<?php
/**
 * 2014 - 2015 Watt Is It
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PayGreen <contact@paygreen.fr>
 *  @copyright 2014-2014 Watt It Is
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 */

/**
 * Class PaygreenTransactionLockManager
 *
 * @method PaygreenServicesRepositoriesTransactionLockRepository getRepository
 */
class PaygreenServicesManagersTransactionLockManager extends PaygreenFoundationsAbstractManager
{
    const LOCK_DURATION = 30;
    const LOCK_REPEAT = 3;
    const LOCK_WAITING = 2;

    /**
     * @param $pid
     * @return PaygreenEntitiesTransactionLock|null
     */
    public function getByPid($pid)
    {
        return $this->getRepository()->findByPid($pid);
    }

    /**
     * @param $pid
     * @param $status
     * @return PaygreenEntitiesTransactionLock
     * @throws Exception
     */
    public function create($pid)
    {
        $paygreenTransactionLock = new PaygreenEntitiesTransactionLock();

        $paygreenTransactionLock->pid = $pid;
        $dt = new DateTime();
        $paygreenTransactionLock->lockedAt = $dt->getTimestamp();

        $paygreenTransactionLock->save();

        return $paygreenTransactionLock;
    }

    /**
     * @param string $pid
     * @param int $repeat
     * @return bool
     * @throws Exception
     */
    public function isLocked($pid, $repeat = 0)
    {
        if ($repeat > self::LOCK_REPEAT) {
            return true;
        }

        $paygreenTransactionLock = $this->getByPid($pid);

        if ($paygreenTransactionLock === null) {
            try {
                $dataLog = array('pid' => $pid, 'repeat' => $repeat);
                PaygreenContainer::getInstance()->get('logger')->debug('hasLock - Lock creating', $dataLog);

                $paygreenTransactionLock = $this->create($pid);

                $dataLog = array('pid' => $pid, 'repeat' => $repeat, 'lockTime' => $paygreenTransactionLock->lockedAt);
                PaygreenContainer::getInstance()->get('logger')->info('hasLock - Lock created', $dataLog);

                return false;
            } catch (Exception $exception) {
                $dataLog = array('pid' => $pid, 'repeat' => $repeat);
                PaygreenContainer::getInstance()->get('logger')->error('hasLock - Lock creating error', $dataLog);

                return $this->waitForUnlocking($pid, $repeat);
            }
        } elseif (($paygreenTransactionLock->lockedAt > $this->getLockTime())) {
            $dataLog = array('pid' => $pid, 'repeat' => $repeat, 'lockTime' => $paygreenTransactionLock->lockedAt);
            PaygreenContainer::getInstance()->get('logger')->info('hasLock - Lock actif', $dataLog);

            return $this->waitForUnlocking($pid, $repeat);
        } else {
            $dataLog = array('pid' => $pid, 'repeat' => $repeat, 'lockTime' => $paygreenTransactionLock->lockedAt);
            PaygreenContainer::getInstance()->get('logger')->debug('hasLock - Lock updating', $dataLog);

            if ($this->getRepository()->updateLock($paygreenTransactionLock, $this->getLockTime())) {
                $dataLog = array('pid' => $pid, 'repeat' => $repeat, 'lockTime' => $paygreenTransactionLock->lockedAt);
                PaygreenContainer::getInstance()->get('logger')->info('hasLock - Lock updated', $dataLog);

                return false;
            } else {
                $dataLog = array('pid' => $pid, 'repeat' => $repeat, 'lockTime' => $paygreenTransactionLock->lockedAt);
                PaygreenContainer::getInstance()->get('logger')->error('hasLock - Lock updating error', $dataLog);

                return $this->waitForUnlocking($pid, $repeat);
            }
        }
    }

    /**
     * Returns the current time minus the locking time
     * @return int
     * @throws Exception
     */
    protected function getLockTime()
    {
        $dt = new DateTime('-' . self::LOCK_DURATION . ' seconds');
        return $dt->getTimestamp();
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
