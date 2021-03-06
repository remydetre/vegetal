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

class PaygreenServicesRepositoriesTransactionLockRepository extends PaygreenFoundationsAbstractPrestashopRepository
{
    const ENTITY = 'PaygreenEntitiesTransactionLock';

    /**
     * Find entity with his ID.
     * @param $pid
     * @return PaygreenEntitiesTransactionLock|null
     */
    public function findByPid($pid)
    {
        return $this->findSingleEntity("pid='$pid'");
    }

    /**
     * @param $paygreenTransactionLock PaygreenEntitiesTransactionLock
     * @param $lockedTimestamp int
     * @return bool
     * @throws Exception
     */
    public function updateLock($paygreenTransactionLock, $lockedTimestamp)
    {
        $table = $this->getTable();
        $dt = new DateTime();
        $timestamp = $dt->getTimestamp();
        $pid = $paygreenTransactionLock->pid;

        $sql = "UPDATE $table SET lockedAt = $timestamp WHERE pid = '$pid' AND lockedAt < $lockedTimestamp";

        $this->db()->query($sql);

        if ($this->db()->Affected_Rows() === 1) {
            $paygreenTransactionLock->lockedAt = $timestamp;
            return true;
        }
    }
}
