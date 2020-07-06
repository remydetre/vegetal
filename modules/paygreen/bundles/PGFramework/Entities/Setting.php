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

class PGFrameworkEntitiesSetting extends PGFrameworkFoundationsAbstractEntityPersisted implements PGDomainInterfacesEntitiesSettingInterface
{
    /** @var PGDomainInterfacesEntitiesShopInterface */
    private $shop = null;

    public function getName()
    {
        return $this->get('name');
    }

    public function getValue()
    {
        return $this->get('value');
    }

    public function setValue($value)
    {
        return $this->set('value', $value);
    }

    public function getShop()
    {
        if (($this->shop === null) && $this->getShopPrimary()) {
            /** @var PGDomainInterfacesRepositoriesShopRepositoryInterface $shopRepository */
            $shopRepository = $this->getService('repository.shop');

            $this->shop = $shopRepository->findByPrimary($this->getShopPrimary());
        }

        return $this->shop;
    }

    public function getShopPrimary()
    {
        return $this->get('id_shop');
    }
}
