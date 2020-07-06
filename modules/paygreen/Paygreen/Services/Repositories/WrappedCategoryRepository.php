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

class PaygreenServicesRepositoriesWrappedCategoryRepository extends PaygreenFoundationsAbstractPrestashopRepository
{
    private $initialized = false;

    private $categories = array();

    public function getAll()
    {
        if (!$this->initialized) {
            $this->loadCategories();
        }

        return $this->categories;
    }

    public function getByPrimary($id)
    {
        if (!$this->initialized) {
            $this->loadCategories();
        }

        /** @var PaygreenEntitiesWrappedCategory $category */
        foreach ($this->categories as $category) {
            if ($category->getId() === $id) {
                return $category;
            }
        }
    }

    protected function loadCategories()
    {
        $categories = $this->getService('repository.category')->findAll();

        /** @var CategoryCore $term */
        foreach ($categories as $category) {
            $this->categories[] = new PaygreenEntitiesWrappedCategory($category);
        }

        $this->initialized = true;

        $this->hierarchizeCategories();
        $this->insertPaymentMode();
    }

    protected function hierarchizeCategories()
    {
        /** @var PaygreenEntitiesWrappedCategory $category */
        foreach ($this->categories as $category) {
            $id_parent = $category->getNativeParentPrimary();

            if ($id_parent > 0) {
                $parentCategory = $this->getByPrimary($id_parent);

                if ($parentCategory !== null) {
                    $parentCategory->addChild($category);
                    $category->setParent($parentCategory);
                }
            }
        }
    }

    protected function insertPaymentMode()
    {
        $categoryPayments = $this->getService('repository.category_payment')->getAll();

        /** @var PaygreenEntitiesCategoryPayment $categoryPayment */
        foreach ($categoryPayments as $categoryPayment) {
            /** @var PaygreenEntitiesWrappedCategory $category */
            $category = $this->getByPrimary((int) $categoryPayment->id_category);

            if ($category !== null) {
                $category->addPaymentMode($categoryPayment->payment);
            }
        }
    }
}
