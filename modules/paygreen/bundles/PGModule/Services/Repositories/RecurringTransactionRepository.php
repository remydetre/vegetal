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
 * Class PGModuleServicesRepositoriesRecurringTransactionRepository
 * @package PGModule\Services\Repositories
 *
 * @method PGModuleEntitiesRecurringTransaction createWrappedEntity(array $data = array())
 */
class PGModuleServicesRepositoriesRecurringTransactionRepository extends PGModuleFoundationsAbstractPrestashopRepository implements PGDomainInterfacesRepositoriesRecurringTransactionRepositoryInterface
{
    const ENTITY = 'PGLocalEntitiesRecurringTransaction';

    /**
     * @inheritdoc
     */
    public function findByPid($pid)
    {
        $localEntity = $this->findSingleEntity("`pid` = '$pid'");

        return $localEntity ? $this->wrapEntity($localEntity) : null;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function insert($pid, $id_order, $state, $stateOrderBefore, $mode, $amount, $rank)
    {
        $dt = new DateTime();

        $entity = $this->createWrappedEntity(array(
            'pid' => $pid,
            'id_order' => $id_order,
            'state' => $state,
            'state_order_before' => $stateOrderBefore,
            'mode' => $mode,
            'amount' => $amount,
            'rank' => $rank,
            'created_at' => $dt->getTimestamp()
        ));

        $this->insertLocalEntity($entity->getLocalEntity());

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function updateState(PGDomainInterfacesEntitiesRecurringTransactionInterface $transaction, $stateOrderAfter)
    {
        /** @var PGLocalEntitiesRecurringTransaction $localEntity */
        $localEntity = $transaction->getLocalEntity();

        $localEntity->state_order_after = $stateOrderAfter;

        $localEntity->save();

        return true;
    }

    /**
     * @param PGLocalEntitiesRecurringTransaction $localEntity
     * @return PGDomainInterfacesEntitiesRecurringTransactionInterface
     */
    public function wrapEntity($localEntity)
    {
        return new PGModuleEntitiesRecurringTransaction($localEntity);
    }
}
