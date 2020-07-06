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
 * Interface PGFormInterfacesValidatorInterface
 * @package PGForm\Interfaces
 */
interface PGFormInterfacesValidatorInterface
{
    /**
     * @param mixed $value
     * @return self
     */
    public function validate($value);

    /**
     * @return bool
     */
    public function isValid();

    /**
     * @return bool
     */
    public function isProcessed();

    /**
     * @return array
     */
    public function getErrors();

    /**
     * @param array $config
     * @return self
     */
    public function setConfig(array $config);
}
