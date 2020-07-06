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
 * Class PGModuleServicesOfficersSettingsOfficer
 * @package PGModule\Services\Officers
 */
class PGModuleServicesOfficersSettingsOfficer extends PGFrameworkFoundationsAbstractObject implements PGFrameworkInterfacesOfficersSettingsOfficerInterface
{
    /**
     * @inheritDoc
     */
    public function getOption($name, $defaultValue)
    {
        return Configuration::get($name, null, null, null, $defaultValue);
    }

    /**
     * @inheritDoc
     */
    public function setOption($name, $value)
    {
        Configuration::updateValue($name, $value, false, null, null);
    }

    public function setDefaultSettings()
    {
        /** @var Paygreen $module */
        $module = $this->getService('local.module');

        $this->setOption(PGModuleServicesSettings::_CONFIG_PRIVATE_KEY, '');
        $this->setOption(PGModuleServicesSettings::_CONFIG_SHOP_TOKEN, '');

        $this->setOption(PGModuleServicesSettings::_CONFIG_SHOP_INPUT_METHOD, 'POST');
        $this->setOption(PGModuleServicesSettings::_CONFIG_VISIBLE, 1);

        $this->setOption(PGModuleServicesSettings::_CONFIG_PAIEMENT_ACCEPTED, $module->l('Your payment was accepted'));
        $this->setOption(PGModuleServicesSettings::_CONFIG_PAIEMENT_REFUSED, $module->l('Your payment was unsuccessful'));
        $this->setOption(PGModuleServicesSettings::_CONFIG_PAYMENT_REFUND, 0);

        // Footer Config
        $this->setOption(PGModuleServicesSettings::_CONFIG_FOOTER_DISPLAY, 1);
        $this->setOption(PGModuleServicesSettings::_CONFIG_FOOTER_LOGO_COLOR, 'white');
        $this->setOption(PGModuleServicesSettings::_CONFIG_VERIF_ADULT, 0);

        //Security curl
        $this->setOption('_PG_ACTIVATE_DEBUG_LOGS', false);
        $this->setOption(PGModuleServicesSettings::_CONFIG_SECURITY_CURL, 0);
        $this->setOption('oauth_access', '');
    }

    public function removeSettings()
    {
        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_PRIVATE_KEY);
        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_SHOP_TOKEN);

        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_SHOP_INPUT_METHOD);
        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_VISIBLE);

        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_PAIEMENT_ACCEPTED);
        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_PAIEMENT_REFUSED);
        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_PAYMENT_REFUND);

        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_FOOTER_DISPLAY);
        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_FOOTER_LOGO_COLOR);
        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_VERIF_ADULT);

        Configuration::deleteByName('_PG_ACTIVATE_DEBUG_LOGS');
        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_SECURITY_CURL);
        Configuration::deleteByName('oauth_access');
        Configuration::deleteByName('last_update');
    }
}
