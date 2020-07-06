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

class PaygreenParameters implements arrayaccess
{
    private $tree = array();

    public function __construct(array $data = array())
    {
        $this->tree = $data;
    }


    // ###################################################################
    // ###       fonctions publiques
    // ###################################################################

    public function get($adresse)
    {
        return $this->searchData($adresse);
    }


    public function toArray()
    {
        return $this->tree;
    }


    public function merge(array $data)
    {
        $this->tree = array_merge_recursive($this->tree, $data);
    }


    // ###################################################################
    // ###       sous-fonctions d'accès par tableau
    // ###################################################################

    public function offsetSet($var, $value)
    {
        throw new Exception('Un arbre de donnée ne peut être modifié.');
    }
    public function offsetExists($var)
    {
        return ($this->searchData($var) !== false);
    }
    public function offsetUnset($var)
    {
        throw new Exception('Un arbre de donnée ne peut être modifié.');
    }
    public function offsetGet($var)
    {
        return $this->get($var);
    }



    // ###################################################################
    // ###       sous-fonctions utilitaires
    // ###################################################################

    private function searchData($key = false, &$data = false)
    {
        if (!$data) {
            $data =& $this->tree;
        }

        if ($key === false) {
            return $data;
        }

        $all_keys = explode('.', $key);
        $first_key = array_shift($all_keys);

        if (is_array($data) and isset($data[$first_key])) {
            $data =& $data[$first_key];
        } else {
            return false;
        }

        if (!empty($all_keys)) {
            $key = implode('.', $all_keys);
            return $this->searchData($key, $data);
        } else {
            return $data;
        }
    }
}
