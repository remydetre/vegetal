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

class PaygreenServicesRepositoriesCategoryPaymentRepository extends PaygreenFoundationsAbstractPrestashopRepository
{
    const ENTITY = 'PaygreenEntitiesCategoryPayment';

    public function getAll()
    {
        return $this->findAllEntities();
    }

    public function getCategoriesByPayment($mode)
    {
        $sql = "SELECT id_category FROM {$this->getTable()} WHERE payment = '$mode'";

        $data = $this->db()->executeS($sql);

        $result = array();

        foreach ($data as $row) {
            $result[] = $row['id_category'];
        }

        return $result;
    }

    public function truncate()
    {
        $sql = "TRUNCATE {$this->getTable()}";

        return $this->db()->execute($sql);
    }

    public function saveAll($data)
    {
        $sql = "INSERT INTO {$this->getTable()} (`id_category`, `payment`) VALUES ";

        $values = array();
        foreach ($data as $row) {
            $values[] = "('{$row['id_category']}', '{$row['payment']}')";
        }

        $sql .= implode(', ', $values);

        return !empty($values) ? $this->db()->execute($sql) : true;
    }
}
