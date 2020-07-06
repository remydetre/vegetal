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

class PaygreenEntitiesWrappedCategory
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $slug;

    /** @var CategoryCore */
    private $nativeCategory;

    /** @var array */
    private $children = array();

    /** @var null|PaygreenEntitiesWrappedCategory */
    private $parent = null;

    /** @var array */
    private $paymentModes = array();

    /**
     * PaygreenEntitiesWrappedCategory constructor.
     * @param CategoryCore $nativeCategory
     */
    public function __construct($nativeCategory)
    {
        $this->nativeCategory = $nativeCategory;
        $this->id = (int) $nativeCategory->id_category;
        $this->name = $nativeCategory->name;
        $this->slug = $this->slugify($nativeCategory->name);
    }

    private function slugify($string, $delimiter = '-')
    {
        $oldLocale = setlocale(LC_ALL, '0');
        setlocale(LC_ALL, 'en_US.UTF-8');
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = Tools::strtolower($clean);
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
        $clean = trim($clean, $delimiter);
        setlocale(LC_ALL, $oldLocale);
        return $clean;
    }

    public function getNativeParentPrimary()
    {
        return (int) $this->nativeCategory->id_parent;
    }

    public function clean()
    {
        $this->nativeCategory = null;

        /** @var self $child */
        foreach ($this->getChildren() as $child) {
            $child->clean();
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return CategoryCore
     */
    public function getNativeCategory()
    {
        return $this->nativeCategory;
    }

    /**
     * @param PaygreenEntitiesWrappedCategory|null $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return PaygreenEntitiesWrappedCategory|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function hasParent()
    {
        return ($this->parent !== null);
    }

    /**
     * @param PaygreenEntitiesWrappedCategory $category
     */
    public function addChild($category)
    {
        $this->children[] = $category;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function hasChildren()
    {
        return !empty($this->children);
    }

    public function getDepth()
    {
        $depth = 0;

        $current = $this;
        while (($current = $current->getParent()) !== null) {
            $depth ++;
        }

        return $depth;
    }

    /**
     * @return array
     */
    public function getPaymentModes()
    {
        return $this->paymentModes;
    }

    public function addPaymentMode($mode)
    {
        $this->paymentModes[] = $mode;
    }

    public function hasPaymentMode($mode)
    {
        return in_array($mode, $this->paymentModes);
    }
}
