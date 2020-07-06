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

abstract class PaygreenSettings extends PaygreenObject
{
    const SHIPPING_PAYMENTS = '_PG_CONFIG_SHIPPING_PAYMENTS';
    const _CONFIG_ORDER_AUTH = '_PG_ORDER_AUTH_OK';
    const _CONFIG_ORDER_VERIFY = '_PG_ORDER_VERIFY';
    const _CONFIG_ORDER_TEST = '_PG_ORDER_AUTH_TEST';
    const _CONFIG_ORDER_WAIT = '_PG_ORDER_WAIT';
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
    const _CONFIG_BACKLINK_SECURE = '_PG_CONFIG_BACKLING_SEC';
    const _CONFIG_PAYGREEN_VALID_ID = '_PG_CONFIG_PAYGREEN_VALID_ID';
    const _CONFIG_SECURITY_CURL = '_PG_CONFIG_SECURITY_CURL';

    private $settings = array();

    private $options = array(
        self::OPTION_SHOP_UNIQUE_ID => array('type' => 'string'),
        self::_CONFIG_ORDER_AUTH => array('type' => 'string'),
        self::_CONFIG_ORDER_VERIFY => array('type' => 'string'),
        self::_CONFIG_ORDER_TEST => array('type' => 'string'),
        self::_CONFIG_ORDER_WAIT => array('type' => 'string'),

        self::_CONFIG_PRIVATE_KEY => array('type' => 'string'),
        self::_CONFIG_SHOP_TOKEN => array('type' => 'string'),
        self::_CONFIG_SHOP_INPUT_METHOD => array('type' => 'string'),
        self::_CONFIG_PAIEMENT_ACCEPTED => array('type' => 'string'),
        self::_CONFIG_PAIEMENT_REFUSED => array('type' => 'string'),
        self::_CONFIG_REFUSE_ACTION => array('type' => 'int'),
        self::_CONFIG_VISIBLE => array('type' => 'int'),
        self::_CONFIG_PAYMENT_REFUND => array('type' => 'int'),
        self::_CONFIG_FOOTER_DISPLAY => array('type' => 'int'),
        self::_CONFIG_FOOTER_LOGO_COLOR => array('type' => 'string'),
        self::_CONFIG_VERIF_ADULT => array('type' => 'int'),
        self::_CONFIG_BACKLINK_SECURE => array('type' => 'string'),
        self::_CONFIG_PAYGREEN_VALID_ID => array('type' => 'string'),
        self::_CONFIG_SECURITY_CURL => array('type' => 'int'),

        self::SHIPPING_PAYMENTS => array(
            'type' => 'array',
            'default' => array()
        ),

        'URL_BASE' => array('type' => 'string'),
        'oauth_access' => array('type' => 'string'),
    );

    public function get($name)
    {
        if (!array_key_exists($name, $this->settings)) {
            if (array_key_exists($name, $this->options)) {
                if ($this->hasOption($name)) {
                    $value = $this->getOption($name);

                    if ($this->isArray($name)) {
                        $value = unserialize(($value));
                    }
                } else {
                    $value = $this->getDefault($name);
                }
            } else {
                $value = $this->getOption($name);
            }

            $this->settings[$name] = $value;
        }

        return $this->settings[$name];
    }

    public function set($name, $value)
    {
        if (array_key_exists($name, $this->options)) {
            $this->setOption($name, $this->isArray($name) ? serialize($value) : $value);
        } else {
            $this->setOption($name, $value);
        }



        $this->settings[$name] = $value;
    }

    public function reset($name)
    {
        $this->set($name, $this->getDefault($name));
    }

    protected function getDefault($name)
    {
        return array_key_exists('default', $this->options[$name]) ? $this->options[$name]['default'] : null;
    }

    protected function isArray($name)
    {
        return (array_key_exists('type', $this->options[$name]) && ($this->options[$name]['type'] === 'array'));
    }

    abstract protected function getOption($name);

    abstract protected function setOption($name, $value);

    abstract protected function hasOption($name);
}
