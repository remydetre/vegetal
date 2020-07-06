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
 * Class PGLocalEntitiesTransactionLock
 *
 * @method array getDefinition(string $class, string|null $field)
 */
class PGLocalEntitiesTransactionLock extends ObjectModel
{
    public $id;
    public $pid;
    public $locked_at;

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
            'locked_at' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => true
            )
        ),
    );
}
