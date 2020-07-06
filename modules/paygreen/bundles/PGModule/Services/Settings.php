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

class PGModuleServicesSettings extends PGFrameworkServicesSettings
{
    const SHIPPING_PAYMENTS = 'shipping_deactivated_payment_modes';
    const OPTION_SHOP_UNIQUE_ID = '_ID_UNIQUE_SHOP';

    const _CONFIG_PRIVATE_KEY = '_PG_CONFIG_PRIVATE_KEY';
    const _CONFIG_SHOP_TOKEN = '_PG_CONFIG_SHOP_TOKEN';
    const _CONFIG_SHOP_INPUT_METHOD = '_PG_CONFIG_SHOP_INPUT_METHOD';
    const _CONFIG_PAIEMENT_ACCEPTED = '_PG_PAIEMENT_ACCEPTED';
    const _CONFIG_PAIEMENT_REFUSED = '_PG_PAIEMENT_REFUSED';
    const _CONFIG_REFUSE_ACTION = '_PG_REFUSE_ACTION';
    const _CONFIG_VISIBLE = '_PG_VISIBLE';
    const _CONFIG_PAYMENT_REFUND = '_PG_CONFIG_PAYMENT_REFUND';
    const _CONFIG_FOOTER_DISPLAY = '_PG_CONFIG_FOOTER_DISPLAY';
    const _CONFIG_FOOTER_LOGO_COLOR = '_PG_CONFIG_FOOTER_LOGO_COLOR';
    const _CONFIG_VERIF_ADULT = '_PG_CONFIG_VERIF_ADULT';
    const _CONFIG_PAYGREEN_VALID_ID = '_PG_CONFIG_PAYGREEN_VALID_ID';
    const _CONFIG_SECURITY_CURL = '_PG_CONFIG_SECURITY_CURL';

    protected $definitions = array(
        self::OPTION_SHOP_UNIQUE_ID => array('type' => 'string'),
        '_PG_ORDER_AUTH_OK' => array('type' => 'string'),
        '_PG_ORDER_VERIFY' => array('type' => 'string'),
        '_PG_ORDER_AUTH_TEST' => array('type' => 'string'),
        '_PG_ORDER_WAIT' => array('type' => 'string'),

        self::_CONFIG_PRIVATE_KEY => array('type' => 'string'),
        self::_CONFIG_SHOP_TOKEN => array('type' => 'string'),
        self::_CONFIG_SHOP_INPUT_METHOD => array('type' => 'string'),
        self::_CONFIG_PAIEMENT_ACCEPTED => array('type' => 'string'),
        self::_CONFIG_PAIEMENT_REFUSED => array('type' => 'string'),
        self::_CONFIG_REFUSE_ACTION => array('type' => 'int'),
        self::_CONFIG_VISIBLE => array('type' => 'int'),
        self::_CONFIG_PAYMENT_REFUND => array('type' => 'int'),
        '_PG_CONFIG_DELIVERY_CONFIRMATION' => array('type' => 'int'),
        self::_CONFIG_FOOTER_DISPLAY => array('type' => 'int'),
        self::_CONFIG_FOOTER_LOGO_COLOR => array('type' => 'string'),
        self::_CONFIG_VERIF_ADULT => array('type' => 'int'),
        self::_CONFIG_PAYGREEN_VALID_ID => array('type' => 'string'),
        self::_CONFIG_SECURITY_CURL => array('type' => 'int'),

        self::SHIPPING_PAYMENTS => array(
            'type' => 'array',
            'default' => array()
        ),

        'URL_BASE' => array('type' => 'string'),
        'oauth_access' => array('type' => 'string'),
    );
}
