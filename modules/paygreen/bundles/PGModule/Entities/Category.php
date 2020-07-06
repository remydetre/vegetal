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
 * Class PGModuleEntitiesCategory
 *
 * @method CategoryCore getLocalEntity()
 */
class PGModuleEntitiesCategory extends PGDomainFoundationsEntitiesAbstractCategory
{
    /** @var string */
    private $slug;

    /**
     * @param CategoryCore $localEntity
     */
    protected function hydrateFromLocalEntity($localEntity)
    {
        $this->slug = $this->slugify($localEntity->name);
    }

    /**
     * @inheritdoc
     */
    public function id()
    {
        return (int) $this->getLocalEntity()->id_category;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return (string) $this->getLocalEntity()->name;
    }

    /**
     * @inheritdoc
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @inheritdoc
     */
    public function getParentId()
    {
        return (int) $this->getLocalEntity()->id_parent;
    }
}
