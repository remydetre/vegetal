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
 * @author    PayGreen <contact@paygreen.fr>
 * @copyright 2014-2014 Watt It Is
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop <SA></SA>
 *
 */

class PaygreenServicesManagersCategoryPaymentManager extends PaygreenFoundationsAbstractManager
{
    private $paymentModes = array();

    public function saveCategoryPayments($categoryPayments)
    {
        $categoryPaymentRows = array();
        foreach ($categoryPayments as $id_category => $categoryPayment) {
            foreach ($categoryPayment as $mode) {
                $categoryPaymentRows[] = array(
                    'id_category' => $id_category,
                    'payment' => $mode
                );
            }
        }

        /** @var PaygreenServicesRepositoriesCategoryPaymentRepository $repository */
        $repository = $this->getService('repository.category_payment');

        $repository->truncate();
        $repository->saveAll($categoryPaymentRows);

        $this->paymentModes = array();
    }

    public function isEligibleCategory($id_category, $mode)
    {
        if (!array_key_exists($mode, $this->paymentModes)) {
            $this->preloadPaymentMode($mode);
        }

        return (empty($this->paymentModes[$mode]) || in_array($id_category, $this->paymentModes[$mode]));
    }

    protected function preloadPaymentMode($mode)
    {
        /** @var PaygreenServicesRepositoriesCategoryPaymentRepository $categoryPaymentRepository */
        $categoryPaymentRepository = $this->getService('repository.category_payment');

        $this->paymentModes[$mode] = $categoryPaymentRepository->getCategoriesByPayment($mode);
    }
}
