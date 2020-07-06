<?php
/**
 * 2014 - 2019 Watt Is It
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
 * @copyright 2014 - 2019 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   2.7.6
 */

/**
 * Class PGLocalEntitiesRecurringTransaction
 *
 * @method array getDefinition(string $class, string|null $field)
 */
class PGLocalEntitiesRecurringTransaction extends ObjectModel
{
    public $id;
    public $pid;
    public $id_order;
    public $state;
    public $state_order_before;
    public $state_order_after;
    public $mode;
    public $amount;
    public $rank;
    public $created_at;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'paygreen_recurring_transaction',
        'primary' => 'id',
        'multilang' => false,
        'fields' => array(
            'pid' =>      array(
                'type' => self::TYPE_STRING,
                'validate' => 'isTableOrIdentifier',
                'required' => true,
                'size' => 50
            ),
            'id_order' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => true
            ),
            'state' =>      array(
                'type' => self::TYPE_STRING,
                'validate' => 'isTableOrIdentifier',
                'required' => true,
                'size' => 50
            ),
            'state_order_before' =>      array(
                'type' => self::TYPE_STRING,
                'validate' => 'isTableOrIdentifier',
                'required' => true,
                'size' => 50
            ),
            'state_order_after' =>      array(
                'type' => self::TYPE_STRING,
                'validate' => 'isTableOrIdentifier',
                'required' => false,
                'size' => 50
            ),
            'mode' =>      array(
                'type' => self::TYPE_STRING,
                'validate' => 'isTableOrIdentifier',
                'required' => true,
                'size' => 50
            ),
            'amount' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => true
            ),
            'rank' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => true
            ),
            'created_at' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => true
            )
        ),
    );
}
