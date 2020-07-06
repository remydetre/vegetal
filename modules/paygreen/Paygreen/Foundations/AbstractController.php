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

abstract class PaygreenFoundationsAbstractController extends PaygreenObject
{
    /** @var array */
    private $data = array();

    public function __construct(array $data)
    {
        $this->data = array();

        foreach ($data as $key => $val) {
            $this->set($key, $val);
        }
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    protected function get($name)
    {
        return $this->has($name) ? $this->data[$name] : null;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    protected function set($name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function has($name)
    {
        return array_key_exists($name, $this->data);
    }

    abstract public function execute();
}
