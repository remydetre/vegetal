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

class PGFrameworkServicesRepositoriesSettingRepository extends PGFrameworkFoundationsAbstractRepositoryDatabase implements PGDomainInterfacesRepositoriesSettingRepositoryInterface
{
    /**
     * @inheritDoc
     * @return PGDomainInterfacesEntitiesSettingInterface[]
     * @throws Exception
     */
    public function findAllByPrimaryShop($id_shop = null)
    {
        if ($id_shop === null) {
            $where = "`id_shop` IS NULL";
        } else {
            $where = "`id_shop` = $id_shop";
        }

        /** @var PGDomainInterfacesEntitiesSettingInterface[] $result */
        $result = $this->findAllEntities($where);

        return $result;
    }

    /**
     * @inheritDoc
     * @return PGDomainInterfacesEntitiesSettingInterface
     * @throws Exception
     */
    public function findOneByNameAndPrimaryShop($name, $id_shop = null)
    {
        $name = $this->getRequester()->quote($name);

        if ($id_shop === null) {
            $where = "`name` = '$name' AND `id_shop` IS NULL";
        } else {
            $where = "`name` = '$name' AND `id_shop` = $id_shop";
        }

        /** @var PGDomainInterfacesEntitiesSettingInterface $result */
        $result = $this->findOneEntity($where);

        return $result;
    }

    /**
     * @inheritDoc
     * @return PGDomainInterfacesEntitiesSettingInterface
     */
    public function create($name, $id_shop = null)
    {
        /** @var PGDomainInterfacesEntitiesSettingInterface $result */
        $result = $this->wrapEntity(array(
            'name' => $name,
            'id_shop' => $id_shop
        ));

        return $result;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(PGDomainInterfacesEntitiesSettingInterface $setting)
    {
        return $this->updateEntity($setting);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function insert(PGDomainInterfacesEntitiesSettingInterface $setting)
    {
        return $this->insertEntity($setting);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function delete(PGDomainInterfacesEntitiesSettingInterface $setting)
    {
        return $this->deleteEntity($setting);
    }
}
