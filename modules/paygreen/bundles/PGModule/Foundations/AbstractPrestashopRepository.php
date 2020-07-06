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

abstract class PGModuleFoundationsAbstractPrestashopRepository extends PGFrameworkFoundationsAbstractRepository implements PGFrameworkInterfacesRepositoryWrappedEntityInterface
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
        $entityClass = $this->getEntityClass();

        return ObjectModel::getDefinition($entityClass);
    }

    /**
     * @return string
     */
    protected function getEntityClass()
    {
        return static::ENTITY;
    }

    /**
     * @return string
     */
    protected function getRawTable()
    {
        $config = $this->getConfiguration();

        return $config['table'];
    }

    /**
     * @return string
     */
    protected function getTable()
    {
        return '`' . _DB_PREFIX_ . $this->getRawTable() . '`';
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
    protected function getInstance($id = null)
    {
        /** @var PGModuleServicesHandlersShopHandler $shopHandler */
        $shopHandler = $this->getService('handler.shop');

        $isMultiShopActivated = $shopHandler->isMultiShopActivated();

        $entityClass = $this->getEntityClass();
        $id_language = ($id !== null) ? $this->getLanguagePrimary() : null;
        $id_shop = $isMultiShopActivated ? $this->getShopPrimary() : null;

        $localEntity = new $entityClass($id, $id_language, $id_shop);

        if (($id !== null) && !($localEntity->id > 0)) {
            $shopMessagePart = $isMultiShopActivated ? ", shop #$id_shop" : '';
            $message = "Unable to retrieve local entity '$entityClass' with primary #$id$shopMessagePart and language #$id_language.";
            $this->getService('logger')->warning($message);
        }

        return $localEntity;
    }

    /**
     * @param array $data
     * @return PGFrameworkInterfacesWrappedEntityInterface
     * @throws Exception
     */
    protected function createWrappedEntity(array $data = array())
    {
        try {
            $localEntity = $this->getInstance(null);

            foreach ($data as $key => $val) {
                $localEntity->$key = $val;
            }

            return $this->wrapEntity($localEntity);
        } catch (Exception $exception) {
            $this->getService('logger')->critical("An error occured during local entity creation : '{$this->getEntityClass()}'.", $exception);

            throw $exception;
        }
    }

    /**
     * @param int $id
     * @return mixed|null
     */
    public function findByPrimary($id)
    {
        /** @var ObjectModel $localEntity */
        $localEntity = $this->getInstance($id);

        return ($localEntity->id > 0) ? $this->wrapEntity($localEntity) : null;
    }

    /**
     * Return first entity corresponding to the 'where' pattern.
     * @param string $where
     * @return mixed|null
     */
    protected function findSingleEntity($where)
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        $table = $this->getTable();
        $primaryColumn = $this->getPrimaryColumn();

        $sql = "SELECT $primaryColumn FROM $table WHERE $where;";

        $logger->debug("Execute SQL query : $sql");

        $id = $this->db()->getValue($sql);

        if ($this->db()->getNumberError() > 0) {
            throw new Exception("An error occurred during execute sql statement : " . $this->db()->getMsgError());
        }

        return $id ? $this->getInstance($id) : null;
    }

    /**
     * @param string $where
     * @return array
     */
    protected function findAllEntities($where = null)
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        $table = $this->getTable();
        $primaryColumn = $this->getPrimaryColumn();
        $resultSet = array();

        $sql = ($where === null)
            ? "SELECT $primaryColumn FROM $table;"
            : "SELECT $primaryColumn FROM $table WHERE $where;"
        ;

        $logger->debug("Execute SQL query : $sql");

        $result = $this->db()->query($sql);

        if ($this->db()->getNumberError() > 0) {
            throw new Exception("An error occurred during execute sql statement : " . $this->db()->getMsgError());
        }

        foreach ($result as $row) {
            $resultSet[] = $this->getInstance($row[$primaryColumn]);
        }

        return $resultSet;
    }

    /**
     * @inheritdoc
     */
    public function wrapEntities($localEntities)
    {
        $entities = array();

        foreach ($localEntities as $localEntity) {
            $entities[] = $this->wrapEntity($localEntity);
        }

        return $entities;
    }

    /**
     * @param ObjectModelCore $localEntity
     * @return bool
     * @throws Exception
     */
    protected function insertLocalEntity(ObjectModel $localEntity)
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        $entityClass = $this->getEntityClass();

        try {
            if ($localEntity->id > 0) {
                throw new Exception("Local entity already exists : '$entityClass#{$localEntity->id}'.");
            }

            $localEntity->add();
        } catch (Exception $exception) {
            $logger->critical("Error during inserting entity : " . $exception->getMessage(), $exception);

            throw $exception;
        }

        return true;
    }

    /**
     * @param ObjectModelCore $localEntity
     * @return bool
     * @throws Exception
     */
    protected function updateLocalEntity(ObjectModel $localEntity)
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        $entityClass = $this->getEntityClass();

        try {
            if (!$localEntity->id) {
                throw new Exception("Local entity never inserted : '$entityClass'.");
            }

            $localEntity->update();
        } catch (Exception $exception) {
            $logger->critical("Error during updating entity : " . $exception->getMessage(), $exception);

            throw $exception;
        }

        return true;
    }

    /**
     * @param ObjectModel $localEntity
     * @return bool
     * @throws Exception
     */
    protected function deleteLocalEntity(ObjectModel $localEntity)
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        $primaryColumn = $this->getPrimaryColumn();
        $entityClass = $this->getEntityClass();

        try {
            if (!$localEntity->$primaryColumn) {
                throw new Exception("Local entity never inserted : '$entityClass'.");
            }

            $localEntity->delete();
        } catch (Exception $exception) {
            $logger->critical("Error during deleting entity : " . $exception->getMessage(), $exception);

            throw $exception;
        }

        return true;
    }

    /**
     * @return int|null
     */
    protected function getLanguagePrimary()
    {
        $definition = $this->getConfiguration();

        if (isset($definition['multilang']) && $definition['multilang']) {
            $id_language = (int) Context::getContext()->language->id;
        } else {
            $id_language = null;
        }

        return $id_language;
    }

    /**
     * @return int
     */
    protected function getShopPrimary()
    {
        return (int) Context::getContext()->shop->id;
    }
}
