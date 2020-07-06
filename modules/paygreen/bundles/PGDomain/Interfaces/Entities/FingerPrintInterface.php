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
 * Interface PGDomainInterfacesEntitiesFingerPrintInterface
 * @package PGDomain\Interfaces\Entities
 */
interface PGDomainInterfacesEntitiesFingerPrintInterface extends PGFrameworkInterfacesPersistedEntityInterface
{
    /**
     * @return string
     */
    public function getSession();

    /**
     * @return string
     */
    public function getBrowser();

    /**
     * @return string
     */
    public function getDevice();

    /**
     * @return int
     */
    public function getPages();

    /**
     * @return self
     */
    public function addPage();

    /**
     * @return int
     */
    public function getPictures();

    /**
     * @param int $pictures
     * @return self
     */
    public function setPictures($pictures);

    /**
     * @param int $pictures
     * @return self
     */
    public function addPictures($pictures);

    /**
     * @return int
     */
    public function getTime();

    /**
     * @param int $time
     * @return self
     */
    public function setTime($time);

    /**
     * @param int $time
     * @return self
     */
    public function addTime($time);

    /**
     * @return DateTime
     * @throws Exception
     */
    public function getCreatedAt();
}
