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
 * Class PGViewServicesBuildersSmartyBuilder
 * @package PGView\Services\Builders
 */
class PGViewServicesBuildersSmartyBuilder implements PGViewInterfacesSmartyBuilderInterface
{
    /** @var PGFrameworkServicesPathfinder */
    private $pathfinder;

    private $config;

    /**
     * PGViewServicesBuildersSmartyBuilder constructor.
     * @param PGFrameworkServicesPathfinder $pathfinder
     * @param array $config
     */
    public function __construct(PGFrameworkServicesPathfinder $pathfinder, array $config)
    {
        $this->pathfinder = $pathfinder;
        $this->config = new PGFrameworkComponentsBag($config);
    }

    /**
     * @return Smarty
     * @throws Exception
     */
    public function build()
    {
        if (!class_exists('Smarty')) {
            if (!$this->config['path']) {
                throw new Exception("Smarty path not found. You must indicate the path to Smarty in the 'smarty.builder.path' parameter.");
            }

            require $this->pathfinder->toAbsolutePath($this->config['path']);
        }

        return new Smarty();
    }
}
