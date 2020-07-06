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

class PGModuleServicesOfficersDatabaseOfficer implements PGFrameworkInterfacesOfficersDatabaseOfficerInterface
{
    public function quote($value)
    {
        return $this->db()->escape($value);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function execute($sql)
    {
        $this->db()->query($sql);

        $this->verifyErrors();

        return true;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function insert($sql)
    {
        $this->db()->query($sql);

        $this->verifyErrors();

        $id = $this->db()->Insert_ID();

        if (!$id) {
            throw new Exception("Unable to retrieve inserted ID for request : $sql");
        }

        return $id;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function fetch($sql)
    {
        /** @var mixed|bool|array|PDOStatement $result */
        $result = $this->db()->query($sql);

        $this->verifyErrors();

        if (class_exists('PDOStatement') && ($result instanceof PDOStatement)) {
            $result = $result->fetchAll(PDO::FETCH_BOTH);
        } elseif (empty($result)) {
            $result = array();
        } elseif (!is_array($result)) {
            throw new Exception("Request result cannot be converted in array.");
        }

        return $result;
    }

    /**
     * @return Db
     */
    protected function db()
    {
        return Db::getInstance();
    }

    /**
     * @throws Exception
     */
    protected function verifyErrors()
    {
        if ($this->db()->getNumberError() > 0) {
            $text ="An error occurred during execute sql statement : " . $this->db()->getMsgError();
            $code = $this->db()->getNumberError();
            throw new Exception($text, $code);
        }
    }
}
