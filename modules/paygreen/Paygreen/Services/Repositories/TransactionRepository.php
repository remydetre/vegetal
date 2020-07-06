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

class PaygreenServicesRepositoriesTransactionRepository extends PaygreenFoundationsAbstractRepository
{
    public function hasPidForCart($id_cart, $pid)
    {
        $transacPid = Db::getInstance()->getValue(
            'SELECT pid FROM ' . _DB_PREFIX_ . 'paygreen_transactions
            WHERE id_cart=' . ((int) $id_cart) . ';'
        );

        return ($transacPid === $pid);
    }

    /**
     * @param int $id_order
     * @param int $id_cart
     * @param string $pid
     * @param string $mode
     * @param string $state
     * @return bool
     * @throws Exception
     */
    public function insert($id_order, $id_cart, $pid, $mode, $state)
    {
        $date = new DateTime();

        return Db::getInstance()->insert('paygreen_transactions', array(
            'id_cart'    => (int) $id_cart,
            'pid'        => pSQL($pid),
            'id_order'   => (int) $id_order,
            'state'      => pSQL($state),
            'type'       => pSQL($mode),
            'created_at' => pSQL($date->getTimestamp())
        ));
    }

    /**
     * Return state of transaction by the id order
     * @param $id_order
     * @return false|string state or false if not exists
     */
    public function getStateTransactionByIdOrder($id_order)
    {
        return Db::getInstance()->getValue(
            'SELECT state FROM ' . _DB_PREFIX_ . 'paygreen_transactions
            WHERE id_order=' . ((int) $id_order) . ';'
        );
    }

    /**
     * @param $pid
     * @param $mode
     * @return false|string|null
     */
    public function updateMode($pid, $mode)
    {
        return Db::getInstance()->update(
            'paygreen_transactions',
            array('state' => $mode),
            'pid=`' . pSQL($pid) . '`'
        );
    }
}
