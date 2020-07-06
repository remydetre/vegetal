<?php
/**
 * 2014 - 2020 Watt Is It
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
 * @copyright 2014 - 2020 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   3.0.1
 */

/**
 * @package PGFramework
 * @param mixed $var
 */
function pgDump($var)
{
    if (PAYGREEN_ENV === 'DEV') {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}

/**
 * @package PGFramework
 * @param mixed $text
 * @param mixed|null $data
 * @throws Exception
 */
function pgLog($text, $data = null)
{
    if (PAYGREEN_ENV === 'DEV') {
        if (is_array($text) || is_object($text)) {
            $data = $text;
            $text = '--DUMP--';
        }

        if (class_exists('PGFrameworkContainer') && class_exists('PGFrameworkServicesLogger')) {
            PGFrameworkContainer::getInstance()->get('logger')->debug($text, $data);
        }
    }
}
