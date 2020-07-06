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
 * Class PGModuleEntitiesCartItem
 *
 * @package PGModule\Entities
 * @method array getLocalEntity()
 */
class PGModuleEntitiesCartItem extends PGDomainFoundationsEntitiesAbstractCartItem
{
    protected function hydrateFromLocalEntity($localEntity)
    {
        // Do nothing.
    }

    /**
     * @inheritdoc
     */
    public function getCost()
    {
        $data = $this->getLocalEntity();

        return PGDomainToolsPrice::toInteger($data['total_wt']);
    }

    /**
     * @inheritdoc
     */
    public function getQuantity()
    {
        $data = $this->getLocalEntity();

        return $data['cart_quantity'];
    }

    /**
     * @inheritdoc
     */
    protected function preloadProduct()
    {
        /** @var PGDomainInterfacesRepositoriesProductRepositoryInterface $productRepository */
        $productRepository = $this->getService('repository.product');

        $data = $this->getLocalEntity();

        $product = $productRepository->findByPrimary($data['id_product']);

        if ($product === null) {
            $this->getService('logger')->warning("Unable to retrieve cart product with primary #{$data['id_product']}.");
        }

        return $product;
    }
}
