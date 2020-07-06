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

/**
 * Class PaygreenEntitiesTransactionLock
 *
 * @method array getDefinition(string $class, string|null $field)
 */
class PaygreenEntitiesTransactionLock extends ObjectModel
{
    public $id;
    public $pid;
    public $lockedAt;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'paygreen_transaction_locks',
        'primary' => 'id',
        'multilang' => false,
        'fields' => array(
            'pid' =>      array(
                'type' => self::TYPE_STRING,
                'validate' => 'isTableOrIdentifier',
                'required' => true,
                'size' => 64
            ),
            'lockedAt' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => true
            )
        ),
    );
}
