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

abstract class PaygreenFoundationsAbstractPrestashopRepository extends PaygreenFoundationsAbstractRepository
{
    /**
     * @return Db
     */
    protected function db()
    {
        return Db::getInstance();
    }

    /**
     * @return array
     */
    protected function getConfiguration()
    {
        $entityClass = static::ENTITY;

        return ObjectModel::getDefinition($entityClass);
    }

    /**
     * @return string
     */
    protected function getTable()
    {
        $config = $this->getConfiguration();

        return '`' . _DB_PREFIX_ . $config['table'] . '`';
    }

    /**
     * @return string
     */
    protected function getPrimaryColumn()
    {
        $config = $this->getConfiguration();

        return $config['primary'];
    }

    /**
     * Return new instance of the model.
     * @param int $id
     * @return mixed
     */
    protected function getInstance($id)
    {
        $cookie = Context::getContext()->cookie;

        $entityClass = static::ENTITY;

        return new $entityClass($id, $cookie->id_lang, $cookie->shopContext);
    }

    /**
     * @param int $id
     * @return mixed|null
     */
    public function findByPrimary($id)
    {
        return $this->getInstance($id);
    }

    /**
     * Return first entity corresponding to the 'where' pattern.
     * @param string $where
     * @return mixed|null
     */
    protected function findSingleEntity($where)
    {
        $table = $this->getTable();
        $primaryColumn = $this->getPrimaryColumn();

        $sql = "SELECT $primaryColumn FROM $table WHERE $where;";

        $id = $this->db()->getValue($sql);

        return $id ? $this->getInstance($id) : null;
    }

    /**
     * @param string $where
     * @return array
     */
    protected function findAllEntities($where = null)
    {
        $table = $this->getTable();
        $primaryColumn = $this->getPrimaryColumn();
        $resultSet = array();

        $sql = ($where === null)
            ? "SELECT $primaryColumn FROM $table;"
            : "SELECT $primaryColumn FROM $table WHERE $where;"
        ;

        $result = $this->db()->executeS($sql);

        foreach ($result as $row) {
            $resultSet[] = $this->getInstance($row[$primaryColumn]);
        }

        return $resultSet;
    }
}
