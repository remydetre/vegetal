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
 * Class PGViewServicesHandlersSmartyHandler
 * @package PGView\Services\Handlers
 */
class PGViewServicesHandlersSmartyHandler extends PGFrameworkFoundationsAbstractObject
{
    /** @var Smarty */
    private $smarty = null;

    public function __construct(
        PGViewInterfacesSmartyBuilderInterface $smartyBuilder,
        PGFrameworkServicesPathfinder $pathfinder
    ) {
        $this->smarty = $smartyBuilder->build();

        $paths = $pathfinder->reviewVendorPaths('/_resources/templates');
        $this->smarty->setTemplateDir(array_reverse($paths));

        $path = $pathfinder->toAbsolutePath('var:/smarty/compiled');
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        $this->smarty->setCompileDir($path);

        $path = $pathfinder->toAbsolutePath('var:/smarty/cache');
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        $this->smarty->setCompileDir($path);

        $this->smarty->caching = Smarty::CACHING_OFF;
        $this->smarty->force_compile = false;
    }

    /**
     * @param object $service
     * @param string $modifierName
     * @param string $method
     * @param string $type
     * @throws SmartyException
     * @throws Exception
     */
    public function installPlugin($service, $modifierName, $method, $type = 'modifier')
    {
        if (!in_array($type, array('function', 'block', 'compiler', 'modifier'))) {
            throw new Exception("Smarty handler only recognise 'function', 'block', 'compiler' and 'modifier' Smarty plugin. '$type' plugin is not allowed.'");
        }

        $this->smarty->registerPlugin($type, $modifierName, array($service, $method));
    }

    /**
     * @return Smarty
     */
    public function getSmarty()
    {
        return $this->smarty;
    }

    /**
     * @param $src
     * @param array $data
     * @return string
     * @throws SmartyException
     */
    public function compileTemplate($src, array $data = array())
    {
        $this->smarty->clearAllAssign();

        $this->smarty->assign($data);

        return $this->smarty->fetch($src);
    }
}
