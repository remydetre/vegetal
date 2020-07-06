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
 * @author    PayGreen <contact@paygreen.fr>
 * @copyright 2014-2014 Watt It Is
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop <SA></SA>
 *
 */

abstract class PaygreenFoundationsAbstractEntityRecursiveObject
{
    private $rawData;

    /**
     * PaygreenEntitiesPaygreenTransaction constructor.
     * @param array $data
     * @throws Exception
     */
    public function __construct($data)
    {
        $this->rawData = (array) $data;

        $this->compile();
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getRaw($name)
    {
        if (!$this->hasRaw($name)) {
            throw new Exception("Unknown property : '$name'.");
        }

        return $this->rawData[$name];
    }

    /**
     * @return bool
     */
    protected function hasRaw($name)
    {
        return array_key_exists($name, $this->rawData);
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    abstract protected function compile();

    protected function setScalar($from, $to, $type = 'string', $isRequired = true)
    {
        if ($isRequired && !$this->hasRaw($from)) {
            throw new Exception("$from field is required in Transaction response.");
        }

        $value = $this->getRaw($from);

        switch ($type) {
            case 'int':
                $value = (int) $value;
                break;
            case 'float':
                $value = (float) $value;
                break;
            case 'bool':
                $value = (bool) $value;
                break;
            case 'array':
                $value = (array) $value;
                break;
        }

        $this->{$to} = $value;

        return $this;
    }

    protected function setObject($from, $to, $class, $isRequired = true)
    {
        if ($isRequired && !$this->hasRaw($from)) {
            throw new Exception("$from field is required in Transaction response.");
        }

        $this->{$to} = new $class($this->getRaw($from));

        return $this;
    }
}
