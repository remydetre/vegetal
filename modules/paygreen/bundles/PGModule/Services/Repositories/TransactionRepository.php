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
 * Class PGModuleServicesRepositoriesTransactionRepository
 * @package PGModule\Services\Repositories
 *
 * @method PGModuleEntitiesTransaction createWrappedEntity(array $data = array())
 */
class PGModuleServicesRepositoriesTransactionRepository extends PGModuleFoundationsAbstractPrestashopRepository implements PGDomainInterfacesRepositoriesTransactionRepositoryInterface
{
    const ENTITY = 'PGLocalEntitiesTransaction';

    /**
     * @inheritdoc
     */
    public function findByPid($pid)
    {
        $localEntity = $this->findSingleEntity("`pid` = '$pid'");

        return $localEntity ? $this->wrapEntity($localEntity) : null;
    }

    public function findByOrderPrimary($id_order)
    {
        $localEntity = $this->findSingleEntity("`id_order` = '$id_order'");

        return $localEntity ? $this->wrapEntity($localEntity) : null;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create()
    {
        return $this->createWrappedEntity();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function insert(PGDomainInterfacesEntitiesTransactionInterface $transaction)
    {
        return $this->insertLocalEntity($transaction->getLocalEntity());
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(PGDomainInterfacesEntitiesTransactionInterface $transaction)
    {
        return $this->updateLocalEntity($transaction->getLocalEntity());
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function delete(PGDomainInterfacesEntitiesTransactionInterface $transaction)
    {
        return $this->deleteLocalEntity($transaction->getLocalEntity());
    }

    /**
     * @inheritDoc
     */
    public function countByOrderPrimary($id_order)
    {
        return Db::getInstance()->getValue(
            'SELECT COUNT(*)
            FROM  `' . _DB_PREFIX_ . 'paygreen_transactions`
            WHERE `id_order` = ' . ((int) $id_order)
        );
    }

    public function wrapEntity($localEntity)
    {
        return new PGModuleEntitiesTransaction($localEntity);
    }
}
