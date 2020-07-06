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

function upgrade_module_2_7_3($object)
{
    /** @var PGFrameworkContainer $container */
    $container = PGFrameworkContainer::getInstance();

    $container->get('logger')->warning("Upgrade module 2.7.3");

    try {
        $container->reset(array('local.module' => $object));

        /** @var PGFrameworkComponentsParameters $parameters */
        $parameters = $container->get('parameters');

        /** @var PGModuleServicesHandlersMultiShopHandler $shopHandler */
        $shopHandler = $container->get('handler.multi_shop');

        $definitions = $parameters["order.states"];

        $shopPrimaries = $shopHandler->getShopPrimaries();

        foreach ($definitions as $name => $definition) {
            if (($definition['source']['type'] === 'settings') && array_key_exists('create', $definition) && ($definition['create'] === true)) {
                $names = array();

                foreach (Language::getLanguages() as $language) {
                    $iso = Tools::strtolower($language['iso_code']);
                    $id_lang = $language['id_lang'];

                    if ($iso === 'fr') {
                        $names[$id_lang] = $definition['name'];
                    } elseif ($iso === 'en') {
                        $names[$id_lang] = $definition['metadata']['en'];
                    }
                }

                foreach ($shopPrimaries as $id_shop) {
                    $id_order_state = ConfigurationCore::get($definition['source']['name'], null, null, $id_shop);

                    if ($id_order_state > 0) {
                        /** @var OrderStateCore $localOrderState */
                        $localOrderState = new OrderState($id_order_state, null, $id_shop);

                        if ($localOrderState->id > 0) {
                            $localOrderState->name = $names;
                            $localOrderState->update();
                        }
                    }
                }
            }
        }

        $container->get('handler.setup')->runDelayedUpgrade('2.7.3');

        return true;
    } catch (Exception $exception) {
        $container->get('logger')->critical("Error during upgrade process 2.7.3 : " . $exception->getMessage(), $exception);

        return false;
    }
}
