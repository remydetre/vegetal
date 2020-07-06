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
 * @version   2.7.6
 */

/**
 * Class PGModuleServicesRepositoriesLockRepository
 * @package PGModule\Services\Repositories
 *
 * @method PGModuleEntitiesLock createWrappedEntity(array $data = array())
 */
class PGModuleServicesRepositoriesLockRepository extends PGModuleFoundationsAbstractPrestashopRepository implements PGDomainInterfacesRepositoriesLockRepositoryInterface
{
    const ENTITY = 'PGLocalEntitiesTransactionLock';

    /**
     * @param PGLocalEntitiesTransactionLock $localEntity
     * @return PGModuleEntitiesLock
     */
    public function wrapEntity($localEntity)
    {
        return new PGModuleEntitiesLock($localEntity);
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function create($pid, DateTime $dateTime)
    {
        $dt = new DateTime();

        $entity = $this->createWrappedEntity(array(
            'pid' => $pid,
            'locked_at' => $dt->getTimestamp()
        ));

        $this->insertLocalEntity($entity->getLocalEntity());

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function findByPid($pid)
    {
        $localEntity = $this->findSingleEntity("pid='$pid'");

        return $localEntity ? $this->wrapEntity($localEntity) : null;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function updateLock(PGDomainInterfacesEntitiesLockInterface $lock, DateTime $lockedDateTime)
    {
        $table = $this->getTable();

        $lockedAt = new DateTime();
        $timestamp = $lockedAt->getTimestamp();
        $lockedTimestamp = $lockedDateTime->getTimestamp();

        $pid = $lock->getPid();

        $query = "UPDATE $table SET `locked_at` = '$timestamp' WHERE `pid` = '$pid' AND `locked_at` < '$lockedTimestamp'";

        $this->db()->query($query);

        if ($this->db()->Affected_Rows() === 1) {
            $lock->setLockedAt($lockedAt);
            return true;
        }
    }
}
