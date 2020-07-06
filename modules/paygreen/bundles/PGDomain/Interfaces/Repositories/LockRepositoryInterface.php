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
 * Interface PGDomainInterfacesRepositoriesLockRepositoryInterface
 * @package PGDomain\Interfaces\Repositories
 */
interface PGDomainInterfacesRepositoriesLockRepositoryInterface extends PGFrameworkInterfacesRepositoryInterface
{
    /**
     * @param string $pid
     * @param DateTime $datetime
     * @return PGDomainInterfacesEntitiesLockInterface
     */
    public function create($pid, DateTime $datetime);

    /**
     * Find entity with his ID.
     * @param string $pid
     * @return PGDomainInterfacesEntitiesLockInterface|null
     */
    public function findByPid($pid);

    /**
     * @param PGDomainInterfacesEntitiesLockInterface $lock
     * @param DateTime $lockedTimestamp
     * @return bool
     */
    public function updateLock(PGDomainInterfacesEntitiesLockInterface $lock, DateTime $lockedTimestamp);
}
