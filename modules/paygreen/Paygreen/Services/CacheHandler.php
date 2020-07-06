<?php
/**
 * 2007-2018 PrestaShop
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2018 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class PaygreenServicesCacheHandler
{
    const DEFAULT_TTL = 60;

    /** @var array */
    private $entries = array();

    /** @var array */
    private $config = array();

    /** @var PaygreenServicesLogger */
    private $logger;

    public function __construct(array $parameters, PaygreenServicesLogger $logger)
    {
        $this->config = $parameters['config'];
        $this->entries = $parameters['entries'];

        $this->logger = $logger;

        $this->logger->debug("Cache handler initialized.");
    }

    public function isActivate()
    {
        return ($this->config['activate'] === true);
    }

    public function loadEntry($name)
    {
        $path = $this->getPath($name);
        $data = null;

        if (!isset($this->entries[$name])) {
            $this->logger->warning("Undefined entry cache : '$name'.");
            return $data;
        }

        if ($this->isActivate() && $this->hasValidEntry($name)) {
            $this->logger->debug("Reading entry '$name' in '$path'.");

            $content = Tools::file_get_contents($path);

            $format = isset($this->entries[$name]['format']) ? $this->entries[$name]['format'] : 'array';

            switch ($format) {
                case 'array':
                    $data = json_decode($content, true);
                    break;
                case 'object':
                    $data = json_decode($content);
                    break;
                default:
                    $this->logger->warning("Unknown entry cache format : '$format'.");
            }
        }

        return $data;
    }

    public function saveEntry($name, $data)
    {
        $path = $this->getPath($name);

        if (!isset($this->entries[$name])) {
            throw new Exception("Undefined entry cache : '$name'.");
        }

        if ($this->isActivate()) {
            $this->logger->debug("Saving entry '$name' in '$path'.");

            if ($this->hasEntry($name, $path)) {
                unlink($path);
            }

            $content = json_encode($data);

            file_put_contents($path, $content);
        }
    }

    public function clearCache()
    {
        foreach (array_keys($this->entries) as $name) {
            $path = $this->getPath($name);

            if ($this->hasEntry($name, $path)) {
                unlink($path);
            }
        }
    }

    /**
     * @param $name
     * @return bool
     * @todo Implements and add ttl management
     */
    protected function hasValidEntry($name)
    {
        $path = $this->getPath($name);

        return $this->hasEntry($name, $path) && !$this->isExpiredEntry($name, $path);
    }

    protected function hasEntry($name, $path = null)
    {
        $path = ($path !== null) ? $path : $this->getPath($name);

        return file_exists($path);
    }

    protected function isExpiredEntry($name, $path = null)
    {
        $ttl = isset($this->entries[$name]['ttl']) ? $this->entries[$name]['ttl'] : self::DEFAULT_TTL;

        $dt = new DateTime("-$ttl seconds");

        return filemtime($path) < $dt->getTimestamp();
    }

    protected function getPath($name)
    {
        return PAYGREEN_MODULE_VAR_DIR . DIRECTORY_SEPARATOR . 'entry.' . $name . '.cache.php';
    }
}
