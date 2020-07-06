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

function upgrade_module_2_7_0($object)
{
    /** @var PGFrameworkContainer $container */
    $container = PGFrameworkContainer::getInstance();

    $container->get('logger')->warning("Upgrade module 2.7.0");

    $container->reset(array('local.module' => $object));

    /** @var PGLegacyServicesDatabaseHandler $databaseHandler */
    $databaseHandler = $container->get('handler.database');

    $id_shop = (int) Context::getContext()->shop->id;

    $databaseHandler->runScript('upgrades/upgrade-2.7.0.sql');

    $sql = $databaseHandler->parseQuery("UPDATE %{db.var.prefix}paygreen_buttons SET `id_shop` = $id_shop");
    $databaseHandler->executeQuery($sql);

    $sql = $databaseHandler->parseQuery("UPDATE %{db.var.prefix}paygreen_categories_has_payments SET `id_shop` = $id_shop");
    $databaseHandler->executeQuery($sql);

    $result = $databaseHandler->runScript('upgrades/upgrade-2.7.0-bis.sql');

    $container->get('handler.setup')->runDelayedUpgrade('2.7.0');

    return $result;
}
