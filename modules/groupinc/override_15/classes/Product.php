<?php
/**
* Price increment/reduction by groups, categories and more
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate
*  @copyright 2018 idnovate
*  @license   See above
*/

class Product extends ProductCore
{
    public static function priceCalculation($id_shop, $id_product, $id_product_attribute, $id_country, $id_state, $zipcode, $id_currency,
        $id_group, $quantity, $use_tax, $decimals, $only_reduc, $use_reduc, $with_ecotax, &$specific_price, $use_group_reduction,
        $id_customer = 0, $use_customer_price = true, $id_cart = 0, $real_quantity = 0)
    {
        if (!Module::isEnabled('groupinc')) {
            return parent::priceCalculation($id_shop, $id_product, $id_product_attribute, $id_country, $id_state, $zipcode, $id_currency,
            $id_group, $quantity, $use_tax, $decimals, $only_reduc, $use_reduc, $with_ecotax, $specific_price, $use_group_reduction,
            $id_customer, $use_customer_price, $id_cart, $real_quantity);
        }
        static $address = null;
        static $context = null;
        if ($address === null) {
            $address = new Address();
        }
        if ($context == null) {
            $context = Context::getContext()->cloneContext();
        }
        if ($id_shop !== null && $context->shop->id != (int)$id_shop) {
            $context->shop = new Shop((int)$id_shop);
        }
        if (!$use_customer_price) {
            $id_customer = 0;
        }
        if ($id_product_attribute === null) {
            $id_product_attribute = Product::getDefaultAttribute($id_product);
        }
        $cache_id = (int)$id_product.'-'.(int)$id_shop.'-'.(int)$id_currency.'-'.(int)$id_country.'-'.$id_state.'-'.$zipcode.'-'.(int)$id_group.
            '-'.(int)$quantity.'-'.(int)$id_product_attribute.
            '-'.(int)$with_ecotax.'-'.(int)$id_customer.'-'.(int)$use_group_reduction.'-'.(int)$id_cart.'-'.(int)$real_quantity.
            '-'.($only_reduc?'1':'0').'-'.($use_reduc?'1':'0').'-'.($use_tax?'1':'0').'-'.(int)$decimals;
        $cart = new Cart($id_cart);
        if (!isset(self::$_prices['nb_products'])) {
            self::$_prices['nb_products'] = (int)$cart->nbProducts();
        } else if (self::$_prices['nb_products'] != (int)$cart->nbProducts()) {
            self::$_prices = array();
            self::$_prices['nb_products'] = (int)$cart->nbProducts();
        }

        if (isset(self::$_prices[$cache_id])) {
            if (isset($specific_price['price']) && $specific_price['price'] > 0) {
                $specific_price['price'] = self::$_prices[$cache_id];
            }

            if (isset(self::$_prices['specific_price'])) {
                $specific_price = self::$_prices['specific_price'];
            }
            return self::$_prices[$cache_id];
        }

        $specific_price = SpecificPrice::getSpecificPrice(
            (int)$id_product,
            $id_shop,
            $id_currency,
            $id_country,
            $id_group,
            $quantity,
            $id_product_attribute,
            $id_customer,
            $id_cart,
            $real_quantity
        );

        include_once(_PS_MODULE_DIR_.'groupinc/classes/GroupincConfiguration.php');
        $groupinc = new GroupincConfiguration();

        $configs = $groupinc->getGIConfigurations($id_shop, $id_product, $id_customer, $id_country, $id_state, $id_currency, $context->language->id, true, true, $id_product_attribute, $quantity);

        if (empty($configs)) {
            return parent::priceCalculation($id_shop, $id_product, $id_product_attribute, $id_country, $id_state, $zipcode, $id_currency,
                $id_group, $quantity, $use_tax, $decimals, $only_reduc, $use_reduc, $with_ecotax, $specific_price, $use_group_reduction,
                $id_customer, $use_customer_price, $id_cart, $real_quantity);
        }
        $cache_id_2 = $id_product.'-'.$id_shop;
        if (!isset(self::$_pricesLevel2[$cache_id_2]) || !isset(self::$_pricesLevel2[$cache_id_2][(int)$id_product_attribute]['wholesale_price'])) {
            $sql = new DbQuery();
            if (Combination::isFeatureActive()) {
                $sql->select('product_shop.`price`, product_shop.`wholesale_price`, product_shop.`ecotax`, pa.`wholesale_price` as attr_wholesale_price');
            } else {
                $sql->select('product_shop.`price`, product_shop.`wholesale_price`, product_shop.`ecotax`');
            }
            $sql->from('product', 'p');
            $sql->innerJoin('product_shop', 'product_shop', '(product_shop.id_product=p.id_product AND product_shop.id_shop = '.(int)$id_shop.')');
            $sql->where('p.`id_product` = '.(int)$id_product);
            if (Combination::isFeatureActive()){
                $sql->select('product_attribute_shop.id_product_attribute, product_attribute_shop.`price` AS attribute_price, product_attribute_shop.default_on');
                $sql->leftJoin('product_attribute', 'pa', 'pa.`id_product` = p.`id_product`');
                $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.id_product_attribute = pa.id_product_attribute AND product_attribute_shop.id_shop = '.(int)$id_shop.')');
            } else {
                $sql->select('0 as id_product_attribute');
            }
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if (is_array($res) && count($res)) {
                foreach ($res as $row) {
                    $array_tmp = array(
                        'price' => $row['price'],
                        'ecotax' => $row['ecotax'],
                        'wholesale_price' => $row['wholesale_price'],
                        'attr_wholesale_price' => $row['attr_wholesale_price'],
                        'attribute_price' => (isset($row['attribute_price']) ? $row['attribute_price'] : null)
                    );
                    self::$_pricesLevel2[$cache_id_2][(int)$row['id_product_attribute']] = $array_tmp;
                    if (isset($row['default_on']) && $row['default_on'] == 1) {
                        self::$_pricesLevel2[$cache_id_2][0] = $array_tmp;
                    }
                }
            }
        }
        if (!isset(self::$_pricesLevel2[$cache_id_2][(int)$id_product_attribute])) {
            return;
        }
        $result = self::$_pricesLevel2[$cache_id_2][(int)$id_product_attribute];
        if (!$specific_price || $specific_price['price'] < 0) {
            $price = (float)$result['price'];
        } else {
            $price = (float)$specific_price['price'];
        }
        if (!$specific_price || !($specific_price['price'] >= 0 && $specific_price['id_currency'])) {
            $price = Tools::convertPrice($price, $id_currency);
            if (isset($specific_price['price']) && $specific_price['price'] >= 0) {
                $specific_price['price'] = $price;
            }
        }
        if (is_array($result) && (!$specific_price || !$specific_price['id_product_attribute'] || $specific_price['price'] < 0)) {
            $attribute_price = Tools::convertPrice($result['attribute_price'] !== null ? (float)$result['attribute_price'] : 0, $id_currency);
            if ($id_product_attribute !== false) {
                $price += $attribute_price;
            }
        }

        $wholeWithoutTaxes = (float)$result['wholesale_price'];
        if (isset($result['attr_wholesale_price']) && $result['attr_wholesale_price'] != 0) {
            $wholeWithoutTaxes = (float)$result['attr_wholesale_price'];
        }
        $retailWithoutTaxes = $price;

        $prod = new Product($id_product);
        $supplierWithoutTaxes = ProductSupplier::getProductSupplierPrice($id_product, $id_product_attribute, $prod->id_supplier);

        /*if ($supplierWithoutTaxes == 0) {
            $supplierWithoutTaxes = $retailWithoutTaxes;
        }*/

        if ($wholeWithoutTaxes == 0) {
            $wholeWithoutTaxes = $retailWithoutTaxes;
        }
        $address->id_country = $id_country;
        $address->id_state = $id_state;
        $address->postcode = $zipcode;
        $tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$id_product, $context));
        $product_tax_calculator = $tax_manager->getTaxCalculator();
        $retailWithTaxes = $product_tax_calculator->addTaxes($retailWithoutTaxes);
        $wholeWithTaxes = $product_tax_calculator->addTaxes($wholeWithoutTaxes);
        $priceDisplay = Product::getTaxCalculationMethod((int)$id_customer);
        $groupinc_result = array();
        $showDecimals = false;

        if (!empty($configs)) {
            foreach ($configs as $conf) {
                if ($conf['show_decimals']) {
                    $showDecimals = true;
                    break;
                }
            }
            if (Configuration::get('GROUPINC_PRIORIZE_MIN')) {
                $groupinc_result = $groupinc->getPriceModified($configs, $id_product, $retailWithoutTaxes, $wholeWithoutTaxes, $specific_price, $product_tax_calculator, $priceDisplay, $use_tax, true, $id_group, $use_group_reduction, $supplierWithoutTaxes);
                if ($groupinc_result) {
                    $id_groupinc_configuration_min = array_keys($groupinc_result, min($groupinc_result));
                    $configs_final = $groupinc->getConfig($id_groupinc_configuration_min[0]);
                    if (!empty($configs_final)) {
                        $groupinc_result = $groupinc->getPriceModified($configs_final, $id_product, $retailWithoutTaxes, $wholeWithoutTaxes, $specific_price, $product_tax_calculator, $priceDisplay, $use_tax, false, $id_group, $use_group_reduction, $supplierWithoutTaxes);
                    }
                }
            } else {
                $groupinc_result = $groupinc->getPriceModified($configs, $id_product, $retailWithoutTaxes, $wholeWithoutTaxes, $specific_price, $product_tax_calculator, $priceDisplay, $use_tax, false, $id_group, $use_group_reduction, $supplierWithoutTaxes);
            }
        }
        if ($groupinc_result && !empty($groupinc_result)) {
            if (isset($groupinc_result['id_product'])) {
                $specific_price = $groupinc_result;
                if (!$specific_price || !($specific_price['price'] >= 0 && $specific_price['id_currency'])) {
                    if (isset($specific_price['price'])) {
                        $price = $specific_price['price'];
                    }
                }
            } else {
                $price = $groupinc_result['price'];
                $specific_price = null;
            }
        }
        if ($use_tax) {
            $price = $product_tax_calculator->addTaxes($price);
        }

        if (($result['ecotax'] || isset($result['attribute_ecotax'])) && $with_ecotax) {
            $ecotax = $result['ecotax'];
            if (isset($result['attribute_ecotax']) && $result['attribute_ecotax'] > 0) {
                $ecotax = $result['attribute_ecotax'];
            }
            if ($id_currency) {
                $ecotax = Tools::convertPrice($ecotax, $id_currency);
            }
            if ($use_tax) {
                $tax_manager = TaxManagerFactory::getManager(
                    $address,
                    (int)Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID')
                );
                $ecotax_tax_calculator = $tax_manager->getTaxCalculator();
                $price += $ecotax_tax_calculator->addTaxes($ecotax);
            } else {
                $price += $ecotax;
            }
        }
        $specific_price_reduction = 0;
        if (($only_reduc || $use_reduc) && $specific_price) {
            if ($specific_price['reduction_type'] == 'amount') {
                $reduction_amount = $specific_price['reduction'];
                if (!$specific_price['id_currency']) {
                    $reduction_amount = Tools::convertPrice($reduction_amount, $id_currency);
                }
                $specific_price_reduction = $reduction_amount;
                if (!$use_tax && $specific_price['reduction_tax']) {
                    $specific_price_reduction = $product_tax_calculator->removeTaxes($specific_price_reduction);
                }
                if ($use_tax && !$specific_price['reduction_tax']) {
                    $specific_price_reduction = $product_tax_calculator->addTaxes($specific_price_reduction);
                }
            } else {
                $specific_price_reduction = $price * $specific_price['reduction'];
            }
            if ($specific_price['reduction'] && $specific_price['reduction_type'] == 'percentage') {
                if (!$showDecimals) {
                    $specific_price['reduction'] = Tools::ps_round($specific_price['reduction'], 2);
                }
            }
        }

        if ($use_reduc) {
            $price -= $specific_price_reduction;
        }
        if ($only_reduc) {
            return Tools::ps_round($specific_price_reduction, $decimals);
        }
        $price = Tools::ps_round($price, $decimals);
        if ($price < 0) {
            $price = 0;
        }
        self::$_prices[$cache_id] = $price;
        return self::$_prices[$cache_id];
    }

    public static function getPricesDrop($id_lang, $page_number = 0, $nb_products = 10, $count = false,
        $order_by = null, $order_way = null, $beginning = false, $ending = false, Context $context = null)
    {
        if (!Module::isEnabled('groupinc')) {
            return parent::getPricesDrop($id_lang, $page_number, $nb_products, $count, $order_by, $order_way, $beginning, $ending, $context);
        }
        if (!Validate::isBool($count)) {
            die(Tools::displayError());
        }
        if (!$context) {
            $context = Context::getContext();
        }
        if ($page_number < 0) {
            $page_number = 0;
        }
        if ($nb_products < 1) {
            $nb_products = 10;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'price';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'product_shop';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }
        $current_date = date('Y-m-d H:i:00');
        $ids_product = Product::_getProductIdByDate((!$beginning ? $current_date : $beginning), (!$ending ? $current_date : $ending), $context);
        $tab_id_product = array();
        foreach ($ids_product as $product) {
            if (is_array($product)) {
                $tab_id_product[] = (int)$product['id_product'];
            } else {
                $tab_id_product[] = (int)$product;
            }
        }

        include_once(_PS_MODULE_DIR_.'groupinc/classes/GroupincConfiguration.php');
        $groupinc = new GroupincConfiguration();
        $context = Context::getContext();

        $id_shop = $context->cart->id_shop;
        $id_currency = $context->cart->id_currency;
        $id_customer = $context->cart->id_customer;
        $id_address_delivery = $context->cart->id_address_delivery;
        $address = new Address($id_address_delivery);
        $id_country = $address->id_country;

        if ($id_country == 0) {
            $id_country = $context->country->id;
        }

        $id_state = $address->id_state;

        $configsPricesDrop = $groupinc->getPricesDropConfigurations($id_shop, $id_customer, $id_country, $id_state, $id_currency, $id_lang, 0);
        if (!empty($configsPricesDrop)) {
            foreach ($configsPricesDrop as $conf) {
                $products = $groupinc->getProductsPricesDrop($conf, $id_shop, $id_lang);
                if (!empty($products)) {
                    foreach ($products as $p) {
                        array_push($tab_id_product, (int)$p);
                    }
                }
            }
        }

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }
        $sql_groups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = ' AND EXISTS(SELECT 1 FROM `'._DB_PREFIX_.'category_product` cp
                JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').')
                WHERE cp.`id_product` = p.`id_product`)';
        }
        if ($count) {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT COUNT(DISTINCT p.`id_product`)
            FROM `'._DB_PREFIX_.'product` p
            '.Shop::addSqlAssociation('product', 'p').'
            WHERE product_shop.`active` = 1
            AND product_shop.`show_price` = 1
            '.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
            '.((!$beginning && !$ending) ? 'AND p.`id_product` IN('.((is_array($tab_id_product) && count($tab_id_product)) ? implode(', ', $tab_id_product) : 0).')' : '').'
            '.$sql_groups);
        }
        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by = pSQL($order_by[0]).'.`'.pSQL($order_by[1]).'`';
        }
        $sql = '
        SELECT
            p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`available_now`, pl.`available_later`,
            IFNULL(product_attribute_shop.id_product_attribute, 0) id_product_attribute,
            pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`,
            pl.`name`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
            DATEDIFF(
                p.`date_add`,
                DATE_SUB(
                    "'.date('Y-m-d').' 00:00:00",
                    INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
                )
            ) > 0 AS new
        FROM `'._DB_PREFIX_.'product` p
        '.Shop::addSqlAssociation('product', 'p').'
        LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
            ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')
        '.Product::sqlStock('p', 0, false, $context->shop).'
        LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
            p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
        )
        LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
            ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
        LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
        LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
        WHERE product_shop.`active` = 1
        AND product_shop.`show_price` = 1
        '.($front ? ' AND p.`visibility` IN ("both", "catalog")' : '').'
        '.((!$beginning && !$ending) ? ' AND p.`id_product` IN ('.((is_array($tab_id_product) && count($tab_id_product)) ? implode(', ', $tab_id_product) : 0).')' : '').'
        '.$sql_groups.'
        ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').pSQL($order_by).' '.pSQL($order_way).'
        LIMIT '.(int)($page_number * $nb_products).', '.(int)$nb_products;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (!$result) {
            return false;
        }
        if ($order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
        }
        return Product::getProductsProperties($id_lang, $result);
    }

    public static function getProductProperties($id_lang, $row, Context $context = null)
    {
        if (!Module::isEnabled('groupinc')) {
            return parent::getProductProperties($id_lang, $row, $context);
        }
        if (empty($row) || !isset($row) || !isset($row['id_product'])) {
            return parent::getProductProperties($id_lang, $row, $context);
        }

        include_once(_PS_MODULE_DIR_.'groupinc/classes/GroupincConfiguration.php');
        $groupinc = new GroupincConfiguration();
        $context = Context::getContext();
        $id_shop = $context->cart->id_shop;
        $id_currency = $context->cart->id_currency;
        $id_customer = $context->cart->id_customer;
        $id_address_delivery = $context->cart->id_address_delivery;
        $address = new Address($id_address_delivery);
        $id_country = $address->id_country;
        if ($id_country == 0) {
            $id_country = $context->country->id;
        }
        $id_state = $address->id_state;
        $id_product = $row['id_product'];
        $configs_onsale_show_discounts = $groupinc->getGIConfigurations($id_shop, $id_product, $id_customer, $id_country, $id_state, $id_currency, $id_lang, true, true, 0, 0, false, true);
        $properties = parent::getProductProperties($id_lang, $row, $context);
        if (!empty($configs_onsale_show_discounts)) {
            $properties['on_sale'] = 1;
            return $properties;
        }

        return $properties;
    }
}