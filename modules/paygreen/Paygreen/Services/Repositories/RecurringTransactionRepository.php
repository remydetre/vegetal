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

class PaygreenServicesRepositoriesRecurringTransactionRepository extends PaygreenFoundationsAbstractRepository
{
    /**
     * @param $id_cart
     * @param $pid
     * @param $state
     * @param $amount
     * @return bool
     * @throws Exception
     */
    public function insert($id_cart, $pid, $state, $amount)
    {
        $date = new DateTime();

        $rank = $this->countRecurringTransactionsByCart($id_cart);

        return Db::getInstance()->insert('paygreen_recurring_transaction', array(
            'id'           => (int) $id_cart,
            'rank'         => (int) $rank,
            'amount'       => (int) $amount,
            'pid'          => pSQL($pid),
            'state'        => pSQL($state),
            'date_payment' => pSQL($date->format('Y-m-d H:i:s'))
        ));
    }

    public function countRecurringTransactionsByCart($id_cart)
    {
        try {
            return Db::getInstance()->getValue(
                'SELECT COUNT(rank) FROM ' . _DB_PREFIX_ . 'paygreen_recurring_transaction
                WHERE id=' . (int) $id_cart
            );
        } catch (Exception $exception) {
            return 0;
        }
    }
}
