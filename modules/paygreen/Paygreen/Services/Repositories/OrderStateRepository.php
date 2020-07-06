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

class PaygreenServicesRepositoriesOrderStateRepository extends PaygreenFoundationsAbstractPrestashopRepository
{
    const ENTITY = 'OrderState';

    public function findAll()
    {
        return $this->findAllEntities();
    }

    public function create($frName, $enName, $color)
    {
        /** @var OrderStateCore $orderState */
        $orderState = new OrderState();

        $names = array();

        foreach (Language::getLanguages() as $language) {
            if (Tools::strtolower($language['iso_code']) == 'fr') {
                $names[$language['id_lang']] = $frName;
            } else {
                $names[$language['id_lang']] = $enName;
            }
        }

        $orderState->name = $names;
        $orderState->module_name = PAYGREEN_MODULE_NAME;
        $orderState->send_email = false;
        $orderState->color = $color;
        $orderState->hidden = false;
        $orderState->delivery = false;
        $orderState->logable = false;
        $orderState->invoice = false;

        if (!$orderState->add()) {
            throw new Exception("Unable to create order state : '$frName'.");
        }

        return (int) $orderState->id;
    }
}
