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
 * Class PGDomainServicesRepositoriesLockRepository
 * @package PGModule\Services\Repositories
 */
class PGDomainServicesRepositoriesLockRepository extends PGFrameworkFoundationsAbstractRepositoryDatabase implements PGDomainInterfacesRepositoriesLockRepositoryInterface
{
    /**
     * @inheritdoc
     * @return PGDomainInterfacesEntitiesLockInterface
     * @throws Exception
     */
    public function create($pid, DateTime $dateTime)
    {
        $dt = new DateTime();

        /** @var PGDomainInterfacesEntitiesLockInterface $entity */
        $entity = $this->wrapEntity(array(
            'pid' => $pid,
            'locked_at' => $dt->getTimestamp()
        ));

        $this->insertEntity($entity);

        return $entity;
    }

    /**
     * @inheritdoc
     * @return PGDomainInterfacesEntitiesLockInterface|null
     * @throws Exception
     */
    public function findByPid($pid)
    {
        /** @var PGDomainInterfacesEntitiesLockInterface $result */
        $result = $this->findOneEntity("pid='$pid'");

        return $result;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function updateLock(PGDomainInterfacesEntitiesLockInterface $lock, DateTime $lockedDateTime)
    {
        $lockedAt = new DateTime();
        $timestamp = $lockedAt->getTimestamp();
        $lockedTimestamp = $lockedDateTime->getTimestamp();

        $where = "`pid` = '{$lock->getPid()}' AND `locked_at` < '$lockedTimestamp'";

        $query = "UPDATE `%{database.entities.lock.table}` SET `locked_at` = '$timestamp' WHERE $where";

        if ($this->getRequester()->execute($query)) {
            $lock->setLockedAt($lockedAt);

            return $this->updateEntity($lock);
        }

        return false;
    }
}
