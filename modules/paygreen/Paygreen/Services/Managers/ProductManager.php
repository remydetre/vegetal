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

/**
 * Class PaygreenProductManager
 *
 * @method PaygreenServicesRepositoriesProductRepository getRepository()
 */
class PaygreenServicesManagersProductManager extends PaygreenFoundationsAbstractManager
{
    /**
     * @param int $id_product
     * @package string $mode
     * @return bool
     */
    public function isEligibleProduct($id_product, $mode)
    {
        /** @var ProductCore $product */
        $product = $this->getRepository()->findByPrimary($id_product);

        /** @var PaygreenServicesManagersCategoryPaymentManager $categoryPaymentManager */
        $categoryPaymentManager = $this->getService('manager.category_payment');

        /** @var array $categories */
        $id_categories = $product->getCategories();

        $is_eligible = false;

        /** @var CategoryCore $category */
        foreach ($id_categories as $id_category) {
            if ($categoryPaymentManager->isEligibleCategory((int) $id_category, $mode)) {
                $is_eligible = true;
                break;
            }
        }

        return $is_eligible;
    }
}
