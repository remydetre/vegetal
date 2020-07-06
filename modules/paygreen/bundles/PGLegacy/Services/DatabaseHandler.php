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

class PGLegacyServicesDatabaseHandler extends PGFrameworkFoundationsAbstractObject
{
    /** @var PGFrameworkComponentsParser */
    private $parser;

    public function __construct(PGFrameworkComponentsParser $parser)
    {
        $this->parser = $parser;
    }

    public function runScript($script)
    {
        /** @var PGFrameworkServicesPathfinder $pathfinder */
        $pathfinder = $this->getService('pathfinder');

        $src = $pathfinder->toAbsolutePath('module-resources', "/sql/$script");

        $this->getService('logger')->info("Run SQL script : $src");

        $sql = Tools::file_get_contents($src);
        $sql = $this->parseQuery($sql);

        $this->executeQuery($sql);

        return true;
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

    public function executeQuery($sql)
    {
        /** @var DbCore $db */
        $db = Db::getInstance();

        $this->getService('logger')->notice("Execute SQL query", PHP_EOL . $sql);

        $result = $db->execute($sql, false);

        if ($result === false) {
            $message = $db->getMsgError() . ' - Error on query : ' . $sql;
            throw new Exception($message, $db->getNumberError());
        }

        return $result;
    }
}
