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
 * Interface PGDomainInterfacesEntitiesCategoryInterface
 * @package PGDomain\Interfaces\Entities
 */
interface PGDomainInterfacesEntitiesCategoryInterface extends PGFrameworkInterfacesWrappedEntityInterface
{
    /**
     * @return mixed
     */
    public function id();

    /**
     * @return int
     */
    public function getName();

    /**
     * @return int
     */
    public function getSlug();

    /**
     * @param PGDomainInterfacesEntitiesCategoryInterface|null $parent
     */
    public function setParent(PGDomainInterfacesEntitiesCategoryInterface $parent = null);

    /**
     * @return PGDomainInterfacesEntitiesCategoryInterface|null
     */
    public function getParent();

    /**
     * @return bool
     */
    public function hasParent();

    /**
     * @return int
     */
    public function getParentId();

    /**
     * @param PGDomainInterfacesEntitiesCategoryInterface $category
     */
    public function addChild(PGDomainInterfacesEntitiesCategoryInterface $category);

    /**
     * @return PGDomainInterfacesEntitiesCategoryInterface[]
     */
    public function getChildren();

    /**
     * @return bool
     */
    public function hasChildren();

    /**
     * @return int
     */
    public function getDepth();

    /**
     * @return array
     */
    public function getPaymentModes();

    public function addPaymentMode($mode);

    public function hasPaymentMode($mode);
}
