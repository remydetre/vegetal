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
 * Class PGModuleServicesRepositoriesButtonRepository
 * @package PGModule\Services\Repositories
 *
 * @method PGModuleEntitiesButton createWrappedEntity(array $data = array())
 */
class PGModuleServicesRepositoriesButtonRepository extends PGModuleFoundationsAbstractPrestashopRepository implements PGDomainInterfacesRepositoriesButtonRepositoryInterface
{
    const ENTITY = 'PGLocalEntitiesButton';

    public function wrapEntity($localEntity)
    {
        return new PGModuleEntitiesButton($localEntity);
    }

    /**
     * @inheritDoc
     */
    public function findAll()
    {
        return $this->wrapEntities($this->findAllEntities("`id_shop` = {$this->getShopPrimary()}"));
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create()
    {
        return $this->createWrappedEntity(array(
            'paymentNumber' => 1,
            'displayType' => 'DEFAULT',
            'integration' => 'EXTERNAL',
            'paymentType' => 'CB',
            'paymentMode' => 'CASH',
            'height' => 60,
            'id_shop' => $this->getShopPrimary()
        ));
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function insert(PGDomainInterfacesEntitiesButtonInterface $button)
    {
        return $this->insertLocalEntity($button->getLocalEntity());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function update(PGDomainInterfacesEntitiesButtonInterface $button)
    {
        return $this->updateLocalEntity($button->getLocalEntity());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function delete(PGDomainInterfacesEntitiesButtonInterface $button)
    {
        return $this->deleteLocalEntity($button->getLocalEntity());
    }

    public function count()
    {
        return (int) $this->db()->getValue("SELECT COUNT(*) AS value FROM {$this->getTable()}");
    }
}
