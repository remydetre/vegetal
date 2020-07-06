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
 * Class PGLocalEntitiesCategoryHasPaymentType
 *
 * @method array getDefinition(string $class, string|null $field)
 */
class PGLocalEntitiesCategoryHasPaymentType extends ObjectModel
{
    /** @var int Name */
    public $id;

    /** @var int Name */
    public $id_category;

    /** @var string Name */
    public $payment;

    /** @var int */
    public $id_shop;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'paygreen_categories_has_payments',
        'primary' => 'id',
        'multilang' => false,
        'fields' => array(
            'id_category' =>      array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => true
            ),
            'payment' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isTableOrIdentifier',
                'required' => true,
                'size' => 50
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            )
        ),
    );
}
