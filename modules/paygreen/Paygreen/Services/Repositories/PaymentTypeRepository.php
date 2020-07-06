<?php
/**
 * 2014 - 2015 Watt Is It
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PayGreen <contact@paygreen.fr>
 *  @copyright 2014-2014 Watt It Is
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 */

class PaygreenServicesRepositoriesPaymentTypeRepository extends PaygreenFoundationsAbstractPaygreenRepository
{
    const ENTITY = 'PaygreenEntitiesPaygreenPaymentType';

    /**
     * @return array
     * @throws Exception
     */
    public function findAll()
    {
        /** @var PaygreenServicesCacheHandler $cacheHandler */
        $cacheHandler = $this->getService('handler.cache');

        $rawPaymentTypes = $cacheHandler->loadEntry('payment-types');

        if ($rawPaymentTypes === null) {
            $rawPaymentTypes = $this->sendPaygreenRequest('paymentType');

            $cacheHandler->saveEntry('payment-types', $rawPaymentTypes);
        }

        $paymentTypes = array();

        foreach ($rawPaymentTypes as $rawPaymentType) {
            $paymentTypes[] = $this->toModel($rawPaymentType, new PaygreenEntitiesPaygreenPaymentType());
        }

        return $paymentTypes;
    }
}
