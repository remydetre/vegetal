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

class PGLegacyServicesDiscountHandler extends PGFrameworkFoundationsAbstractObject
{
    public function checkPromoCode($code)
    {
        try {
            $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'cart_rule
            WHERE code=\''.pSQL($code) . '\'';

            return Db::getInstance()->getValue($sql) >= 1;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function idPromocode($code)
    {
        return Db::getInstance()->getValue(
            'SELECT id_cart_rule FROM ' . _DB_PREFIX_ . 'cart_rule
            WHERE code =\'' . pSQL($code) .'\''
        );
    }

    public function resetQuantity($id_cart_rule)
    {
        $update = array();
        $quantity = $this->checkQuantityPerUser($id_cart_rule);

        $update['quantity'] = (int)$quantity + 1;

        return Db::getInstance()->update(
            'cart_rule',
            $update,
            'id_cart_rule = ' . (int)$id_cart_rule
        );
    }

    public function getAllPromoCode()
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'cart_rule
        WHERE highlight=0';

        $array = DB::getInstance()->executeS($sql);

        $n_array = array();
        $n_array[0] = 'No Reduction';

        for ($i=0; $i <count($array); $i++) {
            $n_array[$array[$i]['code']] =  $array[$i]['description'];
        }

        return $n_array;
    }

    private function checkQuantityPerUser($id_cart_rule)
    {
        $sql = 'SELECT quantity_per_user FROM ' . _DB_PREFIX_ . 'cart_rule
        WHERE id_cart_rule = ' . (int) ($id_cart_rule);

        return Db::getInstance()->getValue($sql);
    }
}
