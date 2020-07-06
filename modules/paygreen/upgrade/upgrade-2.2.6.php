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

function upgrade_module_2_2_6($object)
{
    /** @var PGFrameworkContainer $container */
    $container = PGFrameworkContainer::getInstance();

    $container->get('logger')->warning("Upgrade module 2.2.6");

    $container->reset(array('local.module' => $object));

    /** @var PGLegacyServicesDatabaseHandler $databaseHandler */
    $databaseHandler = $container->get('handler.database');

    $result = $databaseHandler->runScript('upgrades/upgrade-2.2.6.sql');

    $container->get('handler.setup')->runDelayedUpgrade('2.2.6');

    return $result;
}
