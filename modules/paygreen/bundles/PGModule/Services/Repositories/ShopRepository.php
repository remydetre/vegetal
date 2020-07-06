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
 * Class PGModuleServicesRepositoriesShopRepository
 * @package PGModule\Services\Repositories
 *
 * @method PGModuleEntitiesShop createWrappedEntity(array $data = array())
 */
class PGModuleServicesRepositoriesShopRepository extends PGModuleFoundationsAbstractPrestashopRepository implements PGDomainInterfacesRepositoriesShopRepositoryInterface
{
    const ENTITY = 'Shop';

    public function wrapEntity($localEntity)
    {
        return new PGModuleEntitiesShop($localEntity);
    }

    public function findCurrent()
    {
        $localEntity = Context::getContext()->shop;

        return $this->wrapEntity($localEntity);
    }

    public function findAll()
    {
        $shops = array();

        foreach (Shop::getShops(false, null, true) as $id_shop) {
            $shops[] = $this->findByPrimary($id_shop);
        }

        return $shops;
    }
}
