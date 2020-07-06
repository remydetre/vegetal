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

/**
 * Class Paygreen_Services_Managers_PaymentType
 *
 * @method PaygreenServicesRepositoriesPaymentTypeRepository getRepository
 */
class PaygreenServicesManagersPaymentTypeManager extends PaygreenFoundationsAbstractManager
{
    /** @var array  */
    private $paymentTypes = array();

    /** @var array  */
    private $codes = array();

    /** @var array  */
    private $iframeSizes = array();

    /**
     * @return array
     */
    public function getAll()
    {
        if (empty($this->paymentTypes)) {
            $this->paymentTypes = $this->getRepository()->findAll();
        }

        return $this->paymentTypes;
    }

    /**
     * @return array
     */
    public function getCodes()
    {
        if (empty($this->codes)) {
            /** @var $codes */
            $codes = array();

            /** @var PaygreenEntitiesPaygreenPaymentType $paymentType */
            foreach ($this->getAll() as $paymentType) {
                $codes[] = $paymentType->type;
            }

            $this->codes = array_unique($codes);
        }

        return $this->codes;
    }

    /**
     * get Iframe Size ordered by payment Type
     *
     * @return array
     */
    public function getIframeSizeOrderByType()
    {
        if (empty($this->iframeSizes)) {
            /** @var $iframeSizes */
            $this->iframeSizes = array();

            /** @var PaygreenEntitiesPaygreenPaymentType $paymentType */
            foreach ($this->getAll() as $paymentType) {
                $this->iframeSizes[$paymentType->type] = $paymentType->iframe;
            }
        }

        return $this->iframeSizes;
    }
}
