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

class PGModuleServicesRepositoriesCategoryHasPaymentTypeRepository extends PGModuleFoundationsAbstractPrestashopRepository implements PGDomainInterfacesRepositoriesCategoryHasPaymentTypeRepositoryInterface
{
    const ENTITY = 'PGLocalEntitiesCategoryHasPaymentType';

    /**
     * @param PGLocalEntitiesCategoryHasPaymentType $localEntity
     * @return PGModuleEntitiesCategoryHasPaymentType
     */
    public function wrapEntity($localEntity)
    {
        return new PGModuleEntitiesCategoryHasPaymentType($localEntity);
    }

    /**
     * @inheritdoc
     */
    public function findAll()
    {
        return $this->wrapEntities($this->findAllEntities("`id_shop` = {$this->getShopPrimary()}"));
    }

    /**
     * @inheritdoc
     */
    public function findCategoriesByPaymentType($mode)
    {
        $sql = "SELECT id_category FROM {$this->getTable()} WHERE `payment` = '$mode' AND `id_shop` = {$this->getShopPrimary()}";

        $data = $this->db()->executeS($sql);

        $result = array();

        foreach ($data as $row) {
            $result[] = $row['id_category'];
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function truncate()
    {
        $sql = "DELETE FROM {$this->getTable()} WHERE `id_shop` = {$this->getShopPrimary()}";

        return $this->db()->execute($sql);
    }

    /**
     * @inheritdoc
     */
    public function saveAll($data)
    {
        $sql = "INSERT INTO {$this->getTable()} (`id_category`, `payment`, `id_shop`) VALUES ";

        $values = array();

        foreach ($data as $row) {
            $values[] = "('{$row['id_category']}', '{$row['payment']}', {$this->getShopPrimary()})";
        }

        $sql .= implode(', ', $values);

        return !empty($values) ? $this->db()->execute($sql) : true;
    }
}
