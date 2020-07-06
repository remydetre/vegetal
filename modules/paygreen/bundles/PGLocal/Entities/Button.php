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
 * Class PGLocalEntitiesButton
 *
 * @method array getDefinition(string $class, string|null $field)
 */
class PGLocalEntitiesButton extends ObjectModel
{
    public $id;
    public $label;
    public $image;
    public $height;
    public $position;
    public $displayType;
    public $paymentNumber;
    public $minAmount;
    public $maxAmount;
    public $integration;
    public $paymentMode;
    public $paymentType;
    public $paymentReport;
    public $orderRepeated;
    public $firstPaymentPart;
    public $discount = 0;
    public $id_shop;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'paygreen_buttons',
        'primary' => 'id',
        'multilang' => false,
        'multishop' => true,
        'fields' => array(
            'label' =>      array(
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
                'required' => true,
                'size' => 100
            ),
            'image' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
                'required' => false,
                'size' => 100
            ),
            'height' =>      array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => false
            ),
            'position' =>      array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => false
            ),
            'displayType' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isTableOrIdentifier',
                'required' => true,
                'size' => 10
            ),
            'paymentNumber' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => false
            ),
            'minAmount' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice'
            ),
            'maxAmount' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice'
            ),
            'integration' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isTableOrIdentifier',
                'required' => false,
                'size' => 10
            ),
            'paymentMode' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isTableOrIdentifier',
                'required' => false,
                'size' => 10
            ),
            'paymentType' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isTableOrIdentifier',
                'required' => true,
                'size' => 10
            ),
            'paymentReport' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isTableOrIdentifier',
                'required' => false,
                'size' => 50
            ),
            'orderRepeated' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false
            ),
            'firstPaymentPart' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => false
            ),
            'discount' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => true
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            )
        ),
    );
}
