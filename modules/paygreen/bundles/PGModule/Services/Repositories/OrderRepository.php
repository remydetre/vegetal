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

class PGModuleServicesRepositoriesOrderRepository extends PGModuleFoundationsAbstractPrestashopRepository implements PGDomainInterfacesRepositoriesOrderRepositoryInterface
{
    const ENTITY = 'Order';

    /**
     * @param int $cartId
     * @return OrderCore|null
     */
    public function findByCartPrimary($cartId)
    {
        $id = (int) OrderCore::getOrderByCartId((int) $cartId);

        return $id ? $this->findByPrimary($id) : null;
    }

    /**
     * @param string $ref
     * @return mixed|PGDomainInterfacesEntitiesOrderInterface|null
     * @todo Not used in module. Verify if method is functionnal.
     */
    public function findByReference($ref)
    {
        $id = (int) OrderCore::getByReference($ref);

        return $id ? $this->findByPrimary($id) : null;
    }

    public function wrapEntity($localEntity)
    {
        return new PGModuleEntitiesOrder($localEntity);
    }

    public function findRefundedAmount(PGDomainInterfacesEntitiesOrderInterface $order)
    {
        return Db::getInstance()->getValue(
            'SELECT SUM(`unit_price_tax_incl` * `product_quantity_refunded`) FROM '.
            _DB_PREFIX_.'order_detail WHERE `id_order` = ' . (int) $order->id() . ';'
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function updateOrderState(PGDomainInterfacesEntitiesOrderInterface $order, array $localState)
    {
        /** @var OrderCore $localEntity */
        $localEntity = $order->getLocalEntity();

        $localEntity->setCurrentState($localState['state']);

        return $this->updateLocalEntity($localEntity);
    }
}
