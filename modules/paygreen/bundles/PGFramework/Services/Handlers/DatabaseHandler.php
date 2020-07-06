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

class PGFrameworkServicesHandlersDatabaseHandler
{
    /** @var PGFrameworkInterfacesOfficersDatabaseOfficerInterface */
    private $databaseOfficer;

    /** @var PGFrameworkComponentsParser */
    private $parser;

    /** @var PGFrameworkServicesPathfinder */
    private $pathfinder;

    /** @var PGFrameworkServicesLogger */
    private $logger;

    public function __construct(
        PGFrameworkInterfacesOfficersDatabaseOfficerInterface $databaseOfficer,
        PGFrameworkComponentsParser $parser,
        PGFrameworkServicesPathfinder $pathfinder,
        PGFrameworkServicesLogger $logger
    ) {
        $this->databaseOfficer = $databaseOfficer;
        $this->parser = $parser;
        $this->pathfinder = $pathfinder;
        $this->logger = $logger;
    }

    /**
     * @param string $script
     * @return mixed
     * @throws Exception
     */
    public function runScript($script)
    {
        $sql = $this->loadScript($script);

        $this->logger->notice("Run SQL script : $script");

        $queries = explode(';' . PHP_EOL, $sql);

        foreach ($queries as $query) {
            $query = trim($query);

            if (!empty($query) && !$this->execute($query)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $script
     * @return string
     * @throws Exception
     */
    public function loadScript($script)
    {
        if (strstr($script, ':') !== false) {
            list($base, $file) = explode(':', $script, 2);
        } else {
            $file = $script;
            $base = 'PGModule';

            $this->logger->warning("*DEPRECATION* Script must respect this format : 'bundle:filename'. PGModule selected by default.");
        }

        $src = $this->pathfinder->toAbsolutePath($base, "/_resources/sql/$file");

        if (!file_exists($src)) {
            throw new Exception("Script not found : $src");
        }

        return Tools::file_get_contents($src);
    }

    /**
     * @param string $value
     * @return string
     * @throws Exception
     */
    public function quote($value)
    {
        try {
            return $this->databaseOfficer->quote($value);
        } catch (Exception $exception) {
            $this->logger->error("Error during quoting data : " . $value, $exception);
            throw $exception;
        }
    }

    /**
     * @param $sql
     * @return string
     * @throws Exception
     */
    public function parseQuery($sql)
    {
        $sql = $this->parser->parseStringParameters($sql);
        $sql = $this->parser->parseConstants($sql);

        return $sql;
    }

    /**
     * @param string $sql
     * @return bool
     * @throws Exception
     */
    public function execute($sql)
    {
        try {
            $sql = $this->parseQuery($sql);

            $this->logger->debug("Execute query :", PHP_EOL . $sql);

            return $this->databaseOfficer->execute($sql);
        } catch (Exception $exception) {
            $this->logger->error("Error during query execution : " . $sql, $exception);
            throw $exception;
        }
    }

    /**
     * @param string $sql
     * @return int
     * @throws Exception
     */
    public function insert($sql)
    {
        try {
            $sql = $this->parseQuery($sql);

            $this->logger->debug("Execute insertion query :", PHP_EOL . $sql);

            return $this->databaseOfficer->insert($sql);
        } catch (Exception $exception) {
            $this->logger->error("Error during insertion query execution : " . $sql, $exception);
            throw $exception;
        }
    }

    /**
     * @param string $sql
     * @return array
     * @throws Exception
     */
    public function fetchArray($sql)
    {
        try {
            $sql = $this->parseQuery($sql);

            $this->logger->debug("Fetch array with query :", PHP_EOL . $sql);

            return $this->databaseOfficer->fetch($sql);
        } catch (Exception $exception) {
            $this->logger->error("Error when fetching array from SQL query : " . $sql, $exception);
            throw $exception;
        }
    }

    /**
     * @param string $sql
     * @return array
     * @throws Exception
     */
    public function fetchColumn($sql)
    {
        try {
            $sql = $this->parseQuery($sql);

            $this->logger->debug("Fetch column with query :", PHP_EOL . $sql);

            $data = $this->databaseOfficer->fetch($sql);

            $result = array();

            foreach ($data as $line) {
                $result[] = array_shift($line);
            }

            return $result;
        } catch (Exception $exception) {
            $this->logger->error("Error when fetching column from SQL query : " . $sql, $exception);
            throw $exception;
        }
    }

    /**
     * @param string $sql
     * @return mixed
     * @throws Exception
     */
    public function fetchValue($sql)
    {
        try {
            $sql = $this->parseQuery($sql);

            $this->logger->debug("Fetch value with query :", PHP_EOL . $sql);

            $data = $this->databaseOfficer->fetch($sql);
            $data = array_shift($data);
            return empty($data) ? null : array_shift($data);
        } catch (Exception $exception) {
            $this->logger->error("Error when fetching value from SQL query : " . $sql, $exception);
            throw $exception;
        }
    }

    /**
     * @param string $sql
     * @return array
     * @throws Exception
     */
    public function fetchLine($sql)
    {
        try {
            $sql = $this->parseQuery($sql);

            $this->logger->debug("Fetch line with query :", PHP_EOL . $sql);

            $data = $this->databaseOfficer->fetch($sql);
            return array_shift($data);
        } catch (Exception $exception) {
            $this->logger->error("Error when fetching line from SQL query : " . $sql, $exception);
            throw $exception;
        }
    }
}
