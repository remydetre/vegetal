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

class PGModuleServicesLinkersOrderLinker extends PGModuleFoundationsAbstractLocalLinker
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function buildUrl(array $data = array())
    {
        if (array_key_exists('id_order', $data)) {
            $id_order = $data['id_order'];
        } elseif (!array_key_exists('order', $data)) {
            throw new Exception("Building order confirmation URL require order entity or order primary.");
        } elseif (!$data['order'] instanceof PGDomainInterfacesEntitiesOrderInterface) {
            throw new Exception("Building order confirmation URL require PGDomainInterfacesEntitiesOrderInterface entity.");
        } else {
            /** @var PGDomainInterfacesEntitiesOrderInterface $order */
            $order = $data['order'];

            $id_order = $order->id();
        }

        return $this->getPrestashopLinker()->getPageLink('order-detail', null, null, array(
            'id_order' => $id_order
        ));
    }
}
