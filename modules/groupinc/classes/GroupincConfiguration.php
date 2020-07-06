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

class GroupincConfiguration extends ObjectModel
{
    public $id_groupinc_configuration;
    public $name;
    public $type;
    public $mode;
    public $price_calculation;
    public $price_application;
    public $fix;
    public $percentage;
    public $min_result_price;
    public $max_result_price;
    public $threshold_min_price;
    public $threshold_max_price;
    public $threshold_price;
    public $skip_discounts = false;
    public $override_discounts = false;
    public $groups;
    public $customers;
    public $countries;
    public $zones;
    public $categories;
    public $products;
    public $manufacturers;
    public $suppliers;
    public $currencies;
    public $languages;
    public $active = true;
    public $show_as_discount = false;
    public $backoffice = false;
    public $priority;
    public $first_condition = false;
    public $id_shop;
    public $date_add;
    public $date_upd;
    public $date_from;
    public $date_to;
    public $features;
    public $attributes;
    public $product_qty;
    public $show_on_sale = false;
    public $show_prices_drop = false;
    public $show_decimals = false;
    public $filter_prices;
    public $filter_store;
    public $filter_stock;
    public $filter_weight;
    public $min_stock;
    public $max_stock;
    public $min_weight;
    public $max_weight;

    public $schedule;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'groupinc_configuration',
        'primary' => 'id_groupinc_configuration',
        'multilang' => false,
        'fields' => array(
            'name' =>                   array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 100),
            'type' =>                   array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'mode' =>                   array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'price_calculation' =>      array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'price_application' =>      array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'fix' =>                    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'percentage' =>             array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'min_result_price' =>       array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'max_result_price' =>       array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'threshold_min_price' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'threshold_max_price' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'threshold_price' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'product_qty' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'skip_discounts' =>         array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'override_discounts' =>     array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'groups' =>                 array('type' => self::TYPE_STRING),
            'countries' =>              array('type' => self::TYPE_STRING),
            'products' =>               array('type' => self::TYPE_STRING),
            'customers' =>              array('type' => self::TYPE_STRING),
            'zones' =>                  array('type' => self::TYPE_STRING),
            'categories' =>             array('type' => self::TYPE_STRING),
            'manufacturers' =>          array('type' => self::TYPE_STRING),
            'currencies' =>             array('type' => self::TYPE_STRING),
            'languages' =>              array('type' => self::TYPE_STRING),
            'suppliers' =>              array('type' => self::TYPE_STRING),
            'features' =>               array('type' => self::TYPE_STRING),
            'attributes' =>             array('type' => self::TYPE_STRING),
            'active' =>                 array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'show_as_discount' =>       array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'show_on_sale' =>           array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'show_prices_drop' =>       array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'show_decimals' =>          array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'backoffice' =>             array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'first_condition' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'priority' =>               array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'filter_prices' =>          array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'filter_store' =>           array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'filter_stock' =>           array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'min_stock' =>              array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'max_stock' =>              array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'id_shop' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'date_add' =>               array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' =>               array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_from' =>              array('type' => self::TYPE_DATE, 'copy_post' => false),
            'date_to' =>                array('type' => self::TYPE_DATE, 'copy_post' => false),
            'schedule' =>               array('type' => self::TYPE_STRING),
            'min_weight' =>             array('type' => self::TYPE_FLOAT, 'copy_post' => false),
            'max_weight' =>             array('type' => self::TYPE_FLOAT, 'copy_post' => false),
            'filter_weight' =>          array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
        ),
    );

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }

    public function add($autodate = true, $null_values = true)
    {
        $this->id_shop = ($this->id_shop) ? $this->id_shop : Context::getContext()->shop->id;
        $success = parent::add($autodate, $null_values);
        return $success;
    }

    public function toggleStatus()
    {
        parent::toggleStatus();
        return Db::getInstance()->execute('
        UPDATE `'._DB_PREFIX_.bqSQL($this->def['table']).'`
        SET `date_upd` = NOW()
        WHERE `'.bqSQL($this->def['primary']).'` = '.(int)$this->id);
    }

    public function delete()
    {
        if (parent::delete()) {
            return $this->deleteImage();
        }
    }

    public static function getAdminGIconfigurations($id_shop, $id_product)
    {
        $today = date("Y-m-d H:i:s");

        $query = '
                SELECT gi.* FROM `'._DB_PREFIX_.'groupinc_configuration` gi WHERE gi.`id_shop` = '
                .(int)$id_shop.' AND gi.`active` = 1 AND gi.`backoffice` = 1
                AND (date_from <= "'.$today. '" OR date_from = "0000-00-00 00:00:00")
                AND (date_to >= "'.$today.'" OR date_to = "0000-00-00 00:00:00")
                ORDER BY gi.`priority`,gi.`id_groupinc_configuration`';

        $categories = Product::getProductCategories($id_product);
        $product = new Product($id_product);
        $id_manufacturer = $product->id_manufacturer;
        $product_suppliers_array = ProductSupplier::getSupplierCollection($id_product);

        $configs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        if ($configs === false) {
            return false;
        }

        $array_configurations_result = array();
        foreach ($configs as $conf) {
            /* retrocompatibility with old rules which have the value 'all' in the database */
            if ($conf['products'] == 'all') {
                $conf['products'] = '';
            }
            if ($conf['categories'] == 'all') {
                $conf['categories'] = '';
            }
            if ($conf['manufacturers'] == 'all') {
                $conf['manufacturers'] = '';
            }
            if ($conf['suppliers'] == 'all') {
                $conf['suppliers'] = '';
            }

            if ($conf['filter_stock']) {
                $stock = Product::getQuantity($id_product);

                if ($stock < $conf['min_stock'] || $stock > $conf['max_stock']) {
                    continue;
                }
            }

            if ($conf['products'] == '' && $conf['categories'] == '' && $conf['manufacturers'] == '' && $conf['suppliers'] == '') {
                $array_configurations_result[] = $conf;
                if ($conf['first_condition']) {
                    break;
                } else {
                    continue;
                }
            }

            $filter_categories = true;
            $filter_products = true;

            if (@unserialize($conf['categories']) !== false) {
                $categories_array = unserialize($conf['categories']);
            } else {
                $categories_array = explode(';', $conf['categories']);
            }

            if ($conf['categories'] !== '' && $conf['products'] == '') {
                foreach ($categories as $category) {
                    if (in_array($category, $categories_array)) {
                        $filter_categories = true;
                        $filter_products = true;
                        break;
                    } else {
                        $filter_categories = false;
                    }
                }
                if (!$filter_categories) {
                    $filter_products = false;
                }
            } else if ($conf['categories'] == '' && $conf['products'] !== '') {
                $products_array = explode(';', $conf['products']);
                if (!in_array($id_product, $products_array)) {
                    $filter_products = false;
                    $filter_categories = true;
                }
            } else if ($conf['categories'] !== '' && $conf['products'] !== '') {
                foreach ($categories as $category) {
                    if (!in_array($category, $categories_array)) {
                        $filter_categories = false;
                    } else {
                        $filter_categories = true;
                        break;
                    }
                }
                if (!$filter_categories) {
                    $products_array = explode(';', $conf['products']);
                    if (!in_array($id_product, $products_array)) {
                        $filter_products = false;
                    } else {
                        $filter_products = true;
                    }
                } else {
                    $products_array = explode(';', $conf['products']);
                    if (!in_array($id_product, $products_array)) {
                        $filter_products = false;
                    }
                }
            }

            $filter_manufacturers = true;
            if ($conf['manufacturers'] !== '') {
                $manufacturers_array = explode(';', $conf['manufacturers']);
                if (!in_array($id_manufacturer, $manufacturers_array)) {
                    $filter_manufacturers = false;
                }
            }
            $filter_suppliers = true;
            if ($conf['suppliers'] !== '') {
                $filter_suppliers = false;
                $suppliers_array = explode(';', $conf['suppliers']);
                if (!empty($product_suppliers_array)) {
                    foreach ($product_suppliers_array as $ps) {
                        if (in_array($ps->id_supplier, $suppliers_array)) {
                            $filter_suppliers = true;
                            break;
                        }
                    }
                }
            }

            if ($filter_categories && $filter_products && $filter_manufacturers && $filter_suppliers) {
                $array_configurations_result[] = $conf;
                if ($conf['first_condition']) {
                    break;
                } else {
                    continue;
                }
            }
        }
        /*var_dump($filter_groups);
        var_dump($filter_customers);
        var_dump($filter_countries);
        var_dump($filter_zones);
        var_dump($filter_categories);
        var_dump($filter_products);
        var_dump($filter_manufacturers);
        var_dump($filter_suppliers);*/
        //d($array_configurations_result);

        if (count($array_configurations_result) > 0) {
            return $array_configurations_result;
        } else {
            return false;
        }
    }

    public static function getConfig($id_config)
    {
        $query = '
                 SELECT gi.* FROM `'._DB_PREFIX_.'groupinc_configuration` gi WHERE gi.`id_groupinc_configuration` = '.(int)$id_config;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    public function getPricesDropConfigurations($id_shop = 0, $id_customer = 0, $id_country = 0, $id_state = 0, $id_currency = 0, $id_lang = 0, $id_product_attribute = 0)
    {
        $query = '';
        $today = date("Y-m-d H:i:s");
        $array_configurations_result = array();

        $query = '
                 SELECT gi.* FROM `'._DB_PREFIX_.'groupinc_configuration` gi
                 WHERE gi.`id_shop` = '.(int)$id_shop.'
                 AND gi.show_prices_drop = 1
                 AND gi.`active` = 1 ';

        $datefilters = ' AND (date_from <= "'.$today. '" OR date_from = "0000-00-00 00:00:00") AND (date_to >= "'.$today.'" OR date_to = "0000-00-00 00:00:00")';

        $query = $query.$datefilters;

        $orderby = ' ORDER BY gi.`priority`, gi.`id_groupinc_configuration` ASC';

        $query = $query.$orderby;

        $configs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        if ($configs === false) {
            return false;
        }

        $customer = new Customer($id_customer);
        $customer_groups = $customer->getGroupsStatic($customer->id);
        $country = new Country($id_country);
        $zone = 0;

        /*if ($id_state > 0) {
            $zone = State::getIdZone($id_state);
        } else */
        if ($id_country != null and $id_country > 0) {
            $zone = $country->getIdZone($id_country);
        }

        foreach ($configs as $conf) {
            if ($conf['currencies'] == 'all') {
                $conf['currencies'] = '';
            }
            if ($conf['languages'] == 'all') {
                $conf['languages'] = '';
            }
            if ($conf['groups'] == 'all') {
                $conf['groups'] = '';
            }
            if ($conf['customers'] == 'all') {
                $conf['customers'] = '';
            }
            if ($conf['countries'] == 'all') {
                $conf['countries'] = '';
            }
            if ($conf['zones'] == 'all') {
                $conf['zones'] = '';
            }

            if ($conf['currencies'] == '' && $conf['languages'] == '' && $conf['groups'] == '' && $conf['customers'] == '' && $conf['countries'] == '' && $conf['zones'] == '' && $conf['manufacturers'] == '' && $conf['suppliers'] == '') {
                $array_configurations_result[] = $conf;
                if ($conf['first_condition']) {
                    break;
                } else {
                    continue;
                }
            }

            $filter_currencies = true;
            if ($conf['currencies'] !== '') {
                $currencies_array = explode(';', $conf['currencies']);
                if (!in_array($id_currency, $currencies_array)) {
                    $filter_currencies = false;
                }
            }
            $filter_languages = true;
            if ($conf['languages'] !== '') {
                $languages_array = explode(';', $conf['languages']);
                if (!in_array($id_lang, $languages_array)) {
                    $filter_languages = false;
                }
            }

            $filter_groups = true;
            $filter_customers = true;
            if ($conf['groups'] !== '' && $conf['customers'] == '') {
                $groups_array = explode(';', $conf['groups']);
                foreach ($customer_groups as $group) {
                    if (!in_array($group, $groups_array)) {
                        $filter_groups = false;
                    } else {
                        $filter_groups = true;
                        break;
                    }
                }
                if (!$filter_groups) {
                    $filter_customers = false;
                }
            } else if ($conf['groups'] == '' && $conf['customers'] !== '') {
                $customers_array = explode(';', $conf['customers']);
                if (!in_array($id_customer, $customers_array)) {
                    $filter_customers = false;
                }
            } else if ($conf['groups'] !== '' && $conf['customers'] !== '') {
                $groups_array = explode(';', $conf['groups']);
                foreach ($customer_groups as $group) {
                    if (!in_array($group, $groups_array)) {
                        $filter_groups = false;
                    } else {
                        $filter_groups = true;
                    }
                }
                if (!$filter_groups) {
                    $customers_array = explode(';', $conf['customers']);
                    if (!in_array($id_customer, $customers_array)) {
                        $filter_customers = false;
                    } else {
                        $filter_customers = true;
                    }
                } else {
                    $customers_array = explode(';', $conf['customers']);
                    if (!in_array($id_customer, $customers_array)) {
                        $filter_customers = false;
                    }
                }
            }

            $filter_countries = true;
            if ($conf['countries'] !== '') {
                $countries_array = explode(';', $conf['countries']);

                if (!in_array($country->id, $countries_array)) {
                    $filter_countries = false;
                }
            }

            $filter_zones = true;
            if ($conf['zones'] !== '') {
                $zones_array = explode(';', $conf['zones']);
                if (!in_array($zone, $zones_array)) {
                    $filter_zones = false;
                }
            }

            if ($filter_currencies && $filter_languages && $filter_groups && $filter_customers && $filter_countries && $filter_zones) {
                $array_configurations_result[] = $conf;
                if ($conf['first_condition']) {
                    break;
                } else {
                    continue;
                }
            }
        }

        if (count($array_configurations_result) > 0) {
            return $array_configurations_result;
        } else {
            return false;
        }
    }

    public static function getGIConfigurations($id_shop = 0, $id_product = 0, $id_customer = 0, $id_country = 0, $id_state = 0, $id_currency = 0, $id_lang = 0, $taxes = false, $discounts = false, $id_product_attribute = 0, $quantity = 0, $qd = false, $onsale = false)
    {
        if (empty($id_customer)) {
            $id_customer = 0;
        }

        $context = Context::getContext();

        $id_shop = $context->shop->id;
        if (isset($context->cart)) {
            $id_address_delivery = $context->cart->id_address_delivery;
            $address = new Address($id_address_delivery);
            $id_country = $address->id_country;
            $id_state = $address->id_state;
        }

        if ((int)$id_country == 0) {
            $id_country = $context->country->id;
        }

        $customer = new Customer($id_customer);
        $customer_groups = $customer->getGroupsStatic($customer->id);
        $categories = Product::getProductCategories($id_product);
        $product = new Product($id_product);
        $id_manufacturer = $product->id_manufacturer;
        $product_suppliers_array = ProductSupplier::getSupplierCollection($id_product);
        $id_manufacturer = $product->id_manufacturer;
        $country = new Country($id_country);
        $zone = 0;

        if ($id_state > 0) {
            $zone = State::getIdZone($id_state);
        } else if ($id_country != null and $id_country > 0) {
            $zone = $country->getIdZone($id_country);
        }

        $query = '';
        $today = date("Y-m-d H:i:s");

        $query = '
                 SELECT gi.* FROM `'._DB_PREFIX_.'groupinc_configuration` gi ';

        $datefilters = ' WHERE (date_from <= "'.$today. '" OR date_from = "0000-00-00 00:00:00") AND (date_to >= "'.$today.'" OR date_to = "0000-00-00 00:00:00")';

        $query = $query.$datefilters;

        $query = $query.' AND gi.`id_shop` = '.(int)$id_shop.' AND gi.`active` = 1 ';

        if ($qd) {
            $query .= ' AND product_qty > 1 ';
        }

        if ($onsale) {
            $query .= ' AND show_on_sale = 1 ';
        }

        /*$sql_customers = ' AND (gi.customers = "" OR FIND_IN_SET('.$id_customer.', gi.customers) > 0)';

        $sql_groups = ' AND (gi.groups = "" ';

        foreach ($customer_groups as $cgroup) {
            $sql_groups = $sql_groups. ' OR FIND_IN_SET('.$cgroup.', gi.groups) > 0';
        }
        $sql_groups = $sql_groups.')';

        $sql_products = ' AND (gi.products = "" OR FIND_IN_SET('.$id_product.', gi.products) > 0)';

        if ($id_manufacturer) {
            $sql_manufacturers = ' AND (gi.manufacturers = "" OR FIND_IN_SET('.$id_manufacturer.', gi.manufacturers) > 0)';
            $query = $query.$sql_manufacturers;
        }

        $sql_suppliers = ' AND (gi.suppliers = "" ';

        foreach ($product_suppliers_array as $supplier) {
            $sql_suppliers = $sql_suppliers. ' OR FIND_IN_SET('.$supplier->id_supplier.', gi.suppliers) > 0';
        }
        $sql_suppliers = $sql_suppliers.')';

        $sql_currencies = ' AND (gi.currencies = "" OR FIND_IN_SET('.$id_currency.', gi.currencies) > 0)';
        $sql_languages = ' AND (gi.languages = "" OR FIND_IN_SET('.$id_lang.', gi.languages) > 0)';

        $sql_zones = ' AND (gi.zones = "" OR FIND_IN_SET('.$zone.', gi.zones) > 0)';
        $sql_countries = ' AND (gi.countries = "" OR FIND_IN_SET('.$id_country.', gi.countries) > 0) LIMIT 10';

        $query = $query.$sql_customers;
        $query = $query.$sql_groups;
        $query = $query.$sql_products;

        $query = $query.$sql_suppliers;
        $query = $query.$sql_currencies;
        $query = $query.$sql_languages;
        $query = $query.$sql_zones;
        $query = $query.$sql_countries;*/

        $configs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (empty($configs) || $configs === false) {
            return false;
        }

        foreach ($configs as $key => $row)
        {
            $configs_priority[$key] = $row['priority'];
        }
        array_multisort($configs_priority, SORT_ASC, $configs);

        $array_configurations_result = array();
        $products = array();
        foreach ($configs as $conf) {
            if (!GroupincConfiguration::isShowableBySchedule($conf)) {
                continue;
            }

            if (!$qd) {
                if ($conf['product_qty'] > 1) {
                    if (Tools::getValue('quantity_wanted') && Tools::getValue('quantity_wanted') > 0) {
                        if ($conf['product_qty'] > Tools::getValue('quantity_wanted')) {
                            continue;
                        }
                    } else {
                        if (!GroupincConfiguration::isProductItemsCart($conf, $quantity, $id_product, $id_product_attribute))  {
                            continue;
                        } else {
                            if (Context::getContext()->controller->php_self == 'product') {
                                if ($conf['first_condition']) {
                                    break;
                                } else {
                                    continue;
                                }
                            }
                        }
                    }
                }
            }

            if ($conf['filter_stock']) {
                if (!$product->hasAttributes()) {
                    $stock = Product::getQuantity($id_product);
                } else if ($conf['attributes'] != '') {
                    $stock = StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute);
                } else {
                    $stock = Product::getQuantity($id_product);
                }

                if ($stock < $conf['min_stock'] || ($conf['max_stock'] > 0 && $stock > $conf['max_stock'])) {
                    continue;
                }
            }

            if ($conf['filter_weight']) {
                $weight = $product->weight;
                if ($product->hasAttributes()) {
                    $combination = new Combination($id_product_attribute);
                    $weight += $combination->weight;
                }

                if ($weight < $conf['min_weight'] || ($conf['max_weight'] > 0 && $weight > $conf['max_weight'])) {
                    continue;
                }
            }

            if (!$conf['filter_store']) {
                $array_configurations_result[] = $conf;
                if ($conf['first_condition']) {
                    break;
                } else {
                    continue;
                }
            }

            /* retrocompatibility with old rules which have the value 'all' in the database */
                        /* retrocompatibility with old rules which have the value 'all' in the database */
            if ($conf['currencies'] == 'all') {
                $conf['currencies'] = '';
            }
            if ($conf['languages'] == 'all') {
                $conf['languages'] = '';
            }
            if ($conf['groups'] == 'all') {
                $conf['groups'] = '';
            }
            if ($conf['products'] == 'all') {
                $conf['products'] = '';
            }
            if ($conf['customers'] == 'all') {
                $conf['customers'] = '';
            }
            if ($conf['countries'] == 'all') {
                $conf['countries'] = '';
            }
            if ($conf['zones'] == 'all') {
                $conf['zones'] = '';
            }
            if ($conf['categories'] == 'all') {
                $conf['categories'] = '';
            }
            if ($conf['manufacturers'] == 'all') {
                $conf['manufacturers'] = '';
            }
            if ($conf['suppliers'] == 'all') {
                $conf['suppliers'] = '';
            }
            if ($conf['features'] == 'all') {
                $conf['features'] = '';
            }
            if ($conf['attributes'] == 'all') {
                $conf['attributes'] = '';
            }

            if ($conf['attributes'] == '' && $conf['features'] == '' && $conf['currencies'] == '' && $conf['languages'] == '' && $conf['groups'] == '' && $conf['products'] == '' && $conf['customers'] == '' && $conf['countries'] == '' && $conf['zones'] == '' && $conf['categories'] == '' && $conf['manufacturers'] == '' && $conf['suppliers'] == '') {
                $array_configurations_result[] = $conf;
                if ($conf['first_condition']) {
                    break;
                } else {
                    continue;
                }
            }

            if ($qd) {
                $filter_attributes = true;
                $filter_features = true;
            } else {
                $filter_features = false;
                $array_features_selected = Tools::jsonDecode($conf['features'], true);
                $product_features = Product::getFeaturesStatic((int)$id_product);

                $flag_features = 0;
                if (!empty($array_features_selected) && count($array_features_selected) > 0) {
                    foreach ($product_features as $pf) {
                        if (isset($array_features_selected[$pf['id_feature']])) {
                            $array_f = explode(";", $array_features_selected[$pf['id_feature']]);
                            if (in_array($pf['id_feature_value'], $array_f)) {
                                $flag_features++;
                                continue;
                            }
                        }
                    }
                } else {
                    $filter_features = true;
                }

                if ($flag_features > 0) {
                    $filter_features = true;
                }

                $filter_attributes = false;
                $array_attributes_selected = json_decode($conf['attributes'], true);
                if (!empty($array_attributes_selected)) {
                    $product_attribute_combinations = $product->getAttributeCombinationsById($id_product_attribute, $id_lang);
                    foreach ($product_attribute_combinations as $key => $prod_attr_comb) {
                        if (isset($array_attributes_selected[(int)$prod_attr_comb['id_attribute_group']])) {
                            $array_a = explode(";", $array_attributes_selected[(int)$prod_attr_comb['id_attribute_group']]);
                            if (in_array((int)$prod_attr_comb['id_attribute'], $array_a)) {
                                $filter_attributes = true;
                                break;
                            } else {
                                $filter_attributes = false;
                            }
                        }
                    }
                } else {
                    $filter_attributes = true;
                }
            }

            if ($id_product_attribute == 0 && empty($conf['attributes'])) {
                $filter_attributes = true;
            }

            $filter_currencies = true;
            if ($conf['currencies'] !== '') {
                $currencies_array = explode(';', $conf['currencies']);
                if (!in_array($id_currency, $currencies_array)) {
                    $filter_currencies = false;
                }
            }
            $filter_languages = true;
            if ($conf['languages'] !== '') {
                $languages_array = explode(';', $conf['languages']);
                if (!in_array($id_lang, $languages_array)) {
                    $filter_languages = false;
                }
            }

            $filter_groups = true;
            $filter_customers = true;
            if ($conf['groups'] !== '' && $conf['customers'] == '') {
                $groups_array = explode(';', $conf['groups']);
                foreach ($customer_groups as $group) {
                    if (!in_array($group, $groups_array)) {
                        $filter_groups = false;
                    } else {
                        $filter_groups = true;
                        break;
                    }
                }
                if (!$filter_groups) {
                    $filter_customers = false;
                }
            } else if ($conf['groups'] == '' && $conf['customers'] !== '') {
                $customers_array = explode(';', $conf['customers']);
                if (!in_array($id_customer, $customers_array)) {
                    $filter_customers = false;
                }
            } else if ($conf['groups'] !== '' && $conf['customers'] !== '') {
                $groups_array = explode(';', $conf['groups']);
                foreach ($customer_groups as $group) {
                    if (!in_array($group, $groups_array)) {
                        $filter_groups = false;
                    } else {
                        $filter_groups = true;
                    }
                }
                if (!$filter_groups) {
                    $customers_array = explode(';', $conf['customers']);
                    if (!in_array($id_customer, $customers_array)) {
                        $filter_customers = false;
                    } else {
                        $filter_customers = true;
                    }
                } else {
                    $customers_array = explode(';', $conf['customers']);
                    if (!in_array($id_customer, $customers_array)) {
                        $filter_customers = false;
                    }
                }
            }
            $filter_countries = true;
            if ($conf['countries'] !== '') {
                $countries_array = explode(';', $conf['countries']);

                if (!in_array($country->id, $countries_array)) {
                    $filter_countries = false;
                }
            }

            $filter_zones = true;
            if ($conf['zones'] !== '') {
                $zones_array = explode(';', $conf['zones']);
                if (!in_array($zone, $zones_array)) {
                    $filter_zones = false;
                }
            }
            $filter_categories = true;
            $filter_products = true;

            if (@unserialize($conf['categories']) !== false) {
                $categories_array = unserialize($conf['categories']);
            } else {
                $categories_array = explode(';', $conf['categories']);
            }

            if ($conf['categories'] !== '' && $conf['products'] == '') {
                foreach ($categories as $category) {
                    if (in_array($category, $categories_array)) {
                        $filter_categories = true;
                        $filter_products = true;
                        break;
                    } else {
                        $filter_categories = false;
                    }
                }
                if (!$filter_categories) {
                    $filter_products = false;
                }
            } else if ($conf['categories'] == '' && $conf['products'] !== '') {

                $products_array = explode(';', $conf['products']);
                if (!in_array($id_product, $products_array)) {
                    $filter_products = false;
                    $filter_categories = true;
                }
            } else if ($conf['categories'] !== '' && $conf['products'] !== '') {
                foreach ($categories as $category) {
                    if (!in_array($category, $categories_array)) {
                        $filter_categories = false;
                    } else {
                        $filter_categories = true;
                        break;
                    }
                }
                if (!$filter_categories) {
                    $products_array = explode(';', $conf['products']);
                    if (!in_array($id_product, $products_array)) {
                        $filter_products = false;
                    } else {
                        $filter_products = true;
                    }
                } else {
                    $products_array = explode(';', $conf['products']);
                    if (!in_array($id_product, $products_array)) {
                        $filter_products = false;
                    }
                }
            }

            if ($id_product == -1 && $discounts) {
                $filter_products = true;
                $filter_categories = true;
            }

            $filter_manufacturers = true;
            if ($conf['manufacturers'] !== '') {
                $manufacturers_array = explode(';', $conf['manufacturers']);
                if (!in_array($id_manufacturer, $manufacturers_array)) {
                    $filter_manufacturers = false;
                }
            }
            $filter_suppliers = true;
            if ($conf['suppliers'] !== '') {
                $filter_suppliers = false;
                $suppliers_array = explode(';', $conf['suppliers']);
                if (!empty($product_suppliers_array)) {
                    foreach ($product_suppliers_array as $ps) {
                        if (in_array($ps->id_supplier, $suppliers_array)) {
                            $filter_suppliers = true;
                            break;
                        }
                    }
                }
            }

            if ($id_product == -1 && $discounts) {
                $filter_products = true;
                $filter_categories = true;
            }
/*
$logger->logDebug("filter_groups: ".print_r($filter_groups, true));
$logger->logDebug("filter_customers: ".print_r($filter_customers, true));
$logger->logDebug("filter_countries: ".print_r($filter_countries, true));
$logger->logDebug("filter_zones: ".print_r($filter_zones, true));
$logger->logDebug("filter_categories: ".print_r($filter_categories, true));
$logger->logDebug("filter_products: ".print_r($filter_products, true));
$logger->logDebug("filter_manufacturers: ".print_r($filter_manufacturers, true));
$logger->logDebug("filter_suppliers: ".print_r($filter_suppliers, true));
$logger->logDebug("filter_attributes: ".print_r($filter_attributes, true));
$logger->logDebug("filter_features: ".print_r($filter_features, true));
$logger->logDebug("filter_currencies: ".print_r($filter_currencies, true));
$logger->logDebug("filter_languages: ".print_r($filter_languages, true));
*/
            if ($filter_currencies && $filter_languages && $filter_attributes && $filter_features && $filter_groups && $filter_customers && $filter_countries && $filter_zones && $filter_categories && $filter_products && $filter_manufacturers && $filter_suppliers) {
                $array_configurations_result[] = $conf;
                if ($conf['first_condition']) {
                    break;
                } else {
                    continue;
                }
            }
        }


        if (count($array_configurations_result) > 0) {
            return $array_configurations_result;
        } else {
            return false;
        }
    }

    public function getQuantityDiscounts($configs, $id_product, $quantity_discounts, $retailWithTaxes, $retailWithoutTaxes, $ptc, $id_product_attribute = 0)
    {
        $groupinc_specific_price = array();
        $groupinc_quantity_fix = 0;
        $groupinc_quantity_percent = 0;
        $price_to_compare = 0;
        $price_modified_return = 0;

        if (empty($configs)) {
            return array();
        }

        $product = new Product($id_product);
        $wholeWithoutTaxes = $product->wholesale_price;
        $context = Context::getContext();
        $priceDisplay = Product::getTaxCalculationMethod((int)$context->customer->id);

        $supplierWithoutTaxes = ProductSupplier::getProductSupplierPrice($id_product, $id_product_attribute, $product->id_supplier);

        $wholeWithTaxes = $ptc->addTaxes($wholeWithoutTaxes);
        $supplierWithTaxes = $ptc->addTaxes($supplierWithoutTaxes);

        foreach ($configs as $gi) {
            $giconfig = new GroupincConfiguration($gi['id_groupinc_configuration']);

            if ($giconfig->skip_discounts && !empty($quantity_discounts)) {
                continue;
            }

            if ((float)$giconfig->threshold_min_price > 0 || (float)$giconfig->threshold_max_price > 0) {
                if ($giconfig->threshold_price == 0) {
                    $price_to_compare = $wholeWithoutTaxes;
                } else if ($giconfig->threshold_price == 1) {
                    $price_to_compare = $retailWithoutTaxes;
                } else if ($giconfig->threshold_price == 2) {
                    $price_to_compare = $wholeWithTaxes;
                } else if ($giconfig->threshold_price == 3) {
                    $price_to_compare = $retailWithTaxes;
                } else if ($giconfig->threshold_price == 4) {
                    $price_to_compare = $supplierWithoutTaxes;
                } else if ($giconfig->threshold_price == 5) {
                    $price_to_compare = $supplierWithTaxes;
                }
            }

            $tax = 1;
            if ($giconfig->price_application == 0) {
                $price_modified_return = $wholeWithoutTaxes;
                $tax = 0;
            } else if ($giconfig->price_application == 1) {
                $price_modified_return = $retailWithoutTaxes;
                $tax = 0;
            } else if ($giconfig->price_application == 2) {
                $price_modified_return = $wholeWithTaxes;
            } else if ($giconfig->price_application == 3) {
                $price_modified_return = $retailWithTaxes;
            }

            if ((float)$giconfig->threshold_min_price > 0 && (float)$giconfig->threshold_min_price >= $price_to_compare) {
                continue;
            }

            if ((float)$giconfig->threshold_max_price > 0 && (float)$giconfig->threshold_max_price <= $price_to_compare) {
                continue;
            }

            $groupinc_specific_price['id_product'] = $id_product;
            $groupinc_specific_price['id_shop'] = Context::getContext()->shop->id;
            $groupinc_specific_price['reduction_tax'] = $tax;
            $groupinc_specific_price['id_currency'] = Context::getContext()->cart->id_currency;
            $groupinc_specific_price['id_product_attribute'] = $id_product_attribute;
            $groupinc_specific_price['price'] = "-1.000000";
            $groupinc_specific_price['from_quantity'] = $giconfig->product_qty;
            $groupinc_specific_price['from'] = $giconfig->date_from;
            $groupinc_specific_price['to'] = $giconfig->date_to;
            $groupinc_specific_price['id_specific_price_rule'] = "0";
            $groupinc_specific_price['id_shop_group'] = "0";
            $groupinc_specific_price['label'] = $giconfig->name;

            $dec = 4;

            if ($giconfig->mode == 2) {
                $groupinc_specific_price['reduction_type'] = 'percentage';
                if ($price_modified_return > $giconfig->fix) {
                    $groupinc_specific_price['reduction'] = Tools::ps_round(1 - ($giconfig->fix / $price_modified_return), $dec);
                } else {
                    if ($price_modified_return > 0) {
                        $groupinc_specific_price['reduction'] = Tools::ps_round((($giconfig->fix - $price_modified_return) / $price_modified_return) * -1, $dec);
                    }
                }
            } else if ($giconfig->mode == 0) {
/*                if ($giconfig->price_application == 0 || $giconfig->price_application == 1) {
                    $price_modified_return = $retailWithoutTaxes;
                } else {
                    $price_modified_return = $retailWithTaxes;
                }*/

                $priceIncremented = $this->getPriceIncremented($giconfig, $price_modified_return, $retailWithTaxes, $retailWithoutTaxes, $wholeWithTaxes, $wholeWithoutTaxes, $supplierWithoutTaxes, $supplierWithTaxes);

                $groupinc_specific_price['reduction_type'] = 'percentage';

                if (!$priceDisplay) {
                    $groupinc_specific_price['price'] = $retailWithTaxes;
                } else {
                    $groupinc_specific_price['price'] = $retailWithoutTaxes;
                }

                if ($price_modified_return > $priceIncremented) {
                    $groupinc_specific_price['reduction'] = Tools::ps_round(1 - ($priceIncremented / $price_modified_return), $dec);
                } else {
                    if ($price_modified_return > 0) {
                        $groupinc_specific_price['reduction'] = 0;
                    }
                }
            } else {
                if ($giconfig->type == 0) {
                    $groupinc_specific_price['reduction_type'] = 'amount';
                    $groupinc_specific_price['reduction'] = $giconfig->fix;
                } else if ($giconfig->type == 1) {
                    $groupinc_specific_price['reduction_type'] = 'percentage';
                    $groupinc_specific_price['reduction'] = $giconfig->percentage / 100;
                    $price_modif = $price_modified_return * $groupinc_specific_price['reduction'];
                } else if ($giconfig->type == 2) {
                    $groupinc_specific_price['reduction_type'] = 'percentage';
                    $priceTemp = $price_modified_return * ($giconfig->percentage / 100);
                    $priceTemp = $priceTemp - $giconfig->fix;
                    $percTemp = Tools::ps_round(($priceTemp / $price_modified_return) * 100, 4);
                    $groupinc_specific_price['reduction'] = strval($percTemp / 100);

                    $price_modif = $price_modified_return * $groupinc_specific_price['reduction'];
                }

                if ($giconfig->type == 0) {
                    $price_modif = $price_modified_return - $groupinc_specific_price['reduction'];
                } else {
                    $price_modif = $price_modified_return * (1 - $groupinc_specific_price['reduction']);
                }
            }

            if ($giconfig->min_result_price > 0) {
                if ($price_modif < $giconfig->min_result_price) {
                    $groupinc_specific_price['reduction'] = Tools::ps_round(1 - ($giconfig->min_result_price / $price_modified_return), $dec);
                }
            }

            if ($giconfig->max_result_price > 0) {
                if ($price_modif > $giconfig->max_result_price) {
                    $groupinc_specific_price['reduction'] = Tools::ps_round(1 - ($giconfig->max_result_price / $price_modified_return), $dec);
                }
            }

            if (!empty($giconfig->attributes) && count($giconfig->attributes) > 0) {
                $array_attributes_selected = json_decode($giconfig->attributes, true);
                foreach ($array_attributes_selected as $key => $attr) {
                    $idProductAttribute = $this->getIdProductAttribute($id_product, $attr);
                    if (isset($idProductAttribute['id_product_attribute'])) {
                        $id_pa = $idProductAttribute['id_product_attribute'];
                    } else {
                        $id_pa = 0;
                    }
                    if ($giconfig->price_application == 0) {
                        //$groupinc_specific_price['price'] = $this->getWholesalePrice($id_product, $idProductAttribute);
                        $price_modified_return = $this->getWholesalePrice($id_product, $id_pa);
                    } else if ($giconfig->price_application == 2) {
                        //$groupinc_specific_price['price'] = $ptc->addTaxes($this->getWholesalePrice($id_product, $idProductAttribute));
                        $price_modified_return = $ptc->addTaxes($this->getWholesalePrice($id_product, $id_pa));
                    }

                    if ($giconfig->price_application == 1) {
                        //$groupinc_specific_price['price'] = $retailWithoutTaxes + $this->getImpact($id_product, $idProductAttribute);
                        $price_modified_return = $retailWithoutTaxes + $this->getImpact($id_product, $id_pa);
                    } else if ($giconfig->price_application == 3) {
                        //$groupinc_specific_price['price'] = $retailWithTaxes + $this->getImpact($id_product, $idProductAttribute);
                        $price_modified_return = $retailWithTaxes + $this->getImpact($id_product, $id_pa);
                    }
                    foreach ($idProductAttribute as $a) {
                        $groupinc_specific_price['id_product_attribute'] = $a['id_product_attribute'];
                        $quantity_discounts[] = $groupinc_specific_price;
                    }
                }
                //$price_modified_return = $groupinc_specific_price['price'];
            } else {
                if ($giconfig->override_discounts) {
                    $quantity_discounts = array();
                    $quantity_discounts[] = $groupinc_specific_price;
                } else {
                    $quantity_discounts[] = $groupinc_specific_price;
                }
            }

            if ($giconfig->first_condition) {
                break;
            }
        }
        if (!$quantity_discounts) {
            $quantity_discounts = array();
        }
        return $quantity_discounts;
    }

    public function getPriceModified($configurations, $id_product, $retailWithoutTaxes, $wholeWithoutTaxes, $specific_price, $product_tax_calculator, $priceDisplay, $use_tax, $analyze, $id_group, $use_group_reduction, $supplierWithoutTaxes)
    {
        $price_modified_return = 0;
        $groupinc_quantity_fix = 0;
        $groupinc_quantity_percent = 0;
        $price_to_compare = 0;
        $groupinc_specific_price = array();
        $count = 1;
        $pricesReturnToCompare = array();

        $wholeWithTaxes = $product_tax_calculator->addTaxes($wholeWithoutTaxes);
        $retailWithTaxes = $product_tax_calculator->addTaxes($retailWithoutTaxes);
        $supplierWithTaxes = $product_tax_calculator->addTaxes($supplierWithoutTaxes);

        foreach ($configurations as $conf) {
            $giconfig = new GroupincConfiguration($conf['id_groupinc_configuration']);
            if ($giconfig->filter_prices) {
                if ((float)$giconfig->threshold_min_price > 0 || (float)$giconfig->threshold_max_price > 0) {
                    if ($giconfig->threshold_price == 0) {
                        $price_to_compare = $wholeWithoutTaxes;
                    } else if ($giconfig->threshold_price == 1) {
                        $price_to_compare = $retailWithoutTaxes;
                    } else if ($giconfig->threshold_price == 2) {
                        $price_to_compare = $wholeWithTaxes;
                    } else if ($giconfig->threshold_price == 3) {
                        $price_to_compare = $retailWithTaxes;
                    } else if ($giconfig->threshold_price == 4) {
                        $price_to_compare = $supplierWithoutTaxes;
                    } else if ($giconfig->threshold_price == 5) {
                        $price_to_compare = $supplierWithTaxes;
                    }
                }

                $threshold_min = Tools::convertPriceFull((float)$giconfig->threshold_min_price, new Currency(Configuration::get('PS_CURRENCY_DEFAULT')), Context::getContext()->currency);
                $threshold_max = Tools::convertPriceFull((float)$giconfig->threshold_max_price, new Currency(Configuration::get('PS_CURRENCY_DEFAULT')), Context::getContext()->currency);

                if ((float)$giconfig->threshold_min_price == 0 && (float)$giconfig->threshold_max_price == 0 && $price_to_compare > 0) {
                    continue;
                } else {
                    if ((float)$giconfig->threshold_min_price > 0 && $threshold_min > $price_to_compare) {
                        continue;
                    }

                    if ((float)$giconfig->threshold_max_price > 0 && $threshold_max < $price_to_compare) {
                        continue;
                    }
                }
            }

            if ($giconfig->price_application == 0) {
                $price_modified_return = $wholeWithoutTaxes;
            } else if ($giconfig->price_application == 1) {
                $price_modified_return = $retailWithoutTaxes;
            } else if ($giconfig->price_application == 2) {
                $price_modified_return = $wholeWithTaxes;
            } else if ($giconfig->price_application == 3) {
                $price_modified_return = $retailWithTaxes;
            } else if ($giconfig->price_application == 4) {
                $price_modified_return = $supplierWithoutTaxes;
            } else if ($giconfig->price_application == 5) {
                $price_modified_return = $supplierWithTaxes;
            }

            if ($price_modified_return == 0) {
                continue;
            }

            $dec = 4;

            // Group reduction
            $group_reduction = 0;
            if ($use_group_reduction) {
                $reduction_from_category = GroupReduction::getValueForProduct($id_product, $id_group);
                if ($reduction_from_category !== false) {
                    $group_reduction = $price_modified_return * (float)$reduction_from_category;
                } else { // apply group reduction if there is no group reduction for this category
                    $group_reduction = (($reduc = Group::getReductionByIdGroup($id_group)) != 0) ? ($price_modified_return * $reduc / 100) : 0;
                }
            }

            if ((float)$giconfig->threshold_min_price > 0 && (float)$giconfig->threshold_min_price > $price_to_compare) {
                if ($analyze) {
                    $pricesReturnToCompare[$giconfig->id_groupinc_configuration] = $price_modified_return;
                }
                continue;
            }

            if ((float)$giconfig->threshold_max_price > 0 && (float)$giconfig->threshold_max_price < $price_to_compare) {
                if ($analyze) {
                    $pricesReturnToCompare[$giconfig->id_groupinc_configuration] = $price_modified_return;
                }
                continue;
            }

            if (!$giconfig->override_discounts) {
                if ($group_reduction > 0) {
                    $price_modified_return -= $group_reduction;
                }
            }

            if ($giconfig->show_as_discount) { /* show as discount rule */
                if ($giconfig->mode == 1) {
                    $groupinc_quantity_reduction = 0;
                    if ($giconfig->price_calculation == 0) {
                        $groupinc_quantity_reduction = $wholeWithoutTaxes * $giconfig->percentage / 100;
                    } else if ($giconfig->price_calculation == 1) {
                        $groupinc_quantity_reduction = $retailWithoutTaxes * $giconfig->percentage / 100;
                    } else if ($giconfig->price_calculation == 2) {
                        $groupinc_quantity_reduction = $wholeWithTaxes * $giconfig->percentage / 100;
                    } else if ($giconfig->price_calculation == 3) {
                        $groupinc_quantity_reduction = $retailWithTaxes * $giconfig->percentage / 100;
                    } else if ($giconfig->price_calculation == 4) {
                        $groupinc_quantity_reduction = $supplierWithoutTaxes * $giconfig->percentage / 100;
                    } else if ($giconfig->price_calculation == 5) {
                        $groupinc_quantity_reduction = $supplierWithTaxes * $giconfig->percentage / 100;
                    }

                    if ($giconfig->price_calculation == 6) {
                        if ($retailWithoutTaxes > $wholeWithoutTaxes) {
                            $groupinc_quantity_reduction = ($retailWithoutTaxes - $wholeWithoutTaxes) * $giconfig->percentage / 100;
                        }
                    }
                    else if ($giconfig->price_calculation == 7) {
                        if ($retailWithTaxes > $wholeWithTaxes) {
                            $groupinc_quantity_reduction = ($retailWithTaxes - $wholeWithTaxes) * $giconfig->percentage / 100;
                        }
                    }
                    if ($groupinc_quantity_reduction > 0) {
                        $price_modified_return_reduced = $price_modified_return - $groupinc_quantity_reduction;
                        $giconfig->percentage = Tools::ps_round((1 - ($price_modified_return_reduced / $price_modified_return)) * 100, $dec);
                    }
                }

                $groupinc_specific_price['id_product'] = $id_product;
                $groupinc_specific_price['id_specific_price_rule'] = "0";
                $groupinc_specific_price['id_shop'] = "0";
                $groupinc_specific_price['id_shop_group'] = "0";

                $groupinc_specific_price['from_quantity'] = 1;
                if ($giconfig->product_qty > 1) {
                    $groupinc_specific_price['from_quantity'] = $giconfig->product_qty;
                }

                if ($giconfig->price_application == 2 || $giconfig->price_application == 3 || $giconfig->price_application == 5) {
                    $groupinc_specific_price['reduction_tax'] = 1;
                } else {
                    $groupinc_specific_price['reduction_tax'] = 0;
                }

                $groupinc_specific_price['id_currency'] = 0;
                $groupinc_specific_price['id_product_attribute'] = 0;
                $groupinc_specific_price['label'] = $giconfig->name;
                $groupinc_specific_price['from'] = $giconfig->date_from;
                $groupinc_specific_price['to'] = $giconfig->date_to;

                //if (!isset($groupinc_specific_price['price'])) {
                    $groupinc_specific_price['price'] = $price_modified_return;
                //}

                if ($giconfig->mode == 1) {
                    if ($giconfig->override_discounts && !$giconfig->skip_discounts) {
                        if ($giconfig->type == 0) {
                            $groupinc_specific_price['reduction_type'] = 'amount';
                            $groupinc_specific_price['reduction'] = $giconfig->fix;
                        } else {
                            $groupinc_specific_price['reduction_type'] = 'percentage';
                            $groupinc_specific_price['reduction'] = $giconfig->percentage / 100;
                        }
                    } else if (!$giconfig->skip_discounts && $specific_price) {
                        if (isset($specific_price['price']) && $specific_price['price'] > 0) {
                            $groupinc_specific_price['price'] = $specific_price['price'];
                        }
                        if ($giconfig->type == 0) {
                            if ($specific_price['reduction_type'] == 'amount') {
                                $groupinc_specific_price['reduction_type'] = 'amount';
                                $groupinc_specific_price['reduction'] = $specific_price['reduction'] + $giconfig->fix;
                            } else {
                                $groupinc_specific_price['reduction_type'] = 'amount';
                                $new_red = Tools::ps_round($specific_price['price'] * $specific_price['reduction'], 6);
                                $groupinc_specific_price['reduction'] = $giconfig->fix + $new_red;
                                /*$groupinc_specific_price['reduction_type'] = 'percentage';
                                $new_red = Tools::ps_round(1 - (($specific_price['price'] - $giconfig->fix) / $specific_price['price']), 6);
                                $groupinc_specific_price['reduction'] = $specific_price['reduction'] + $new_red;*/
                            }
                        }

                        if ($giconfig->type == 1) {
                            if ($specific_price['reduction'] > 0) {
                                if ($specific_price['reduction_type'] == 'amount') {
                                    $groupinc_specific_price = $specific_price;
                                    $priceToGetDiscount = $price_modified_return;
                                    /*if ($specific_price['reduction_tax'] == 0) {
                                        $priceToGetDiscount = $product_tax_calculator->removeTaxes($price_modified_return);
                                    }*/

                                    $pr = $priceToGetDiscount - $specific_price['reduction'];
                                    $discount_amount = $pr * ($giconfig->percentage / 100);
                                    $groupinc_specific_price['reduction'] = $groupinc_specific_price['reduction'] + $discount_amount;
                                } else {
                                    $groupinc_specific_price['reduction_type'] = 'percentage';
                                    $groupinc_specific_price['reduction'] = $specific_price['reduction'] + $giconfig->percentage / 100;
                                }
                            } else {
                                $groupinc_specific_price['reduction_type'] = 'percentage';
                                $groupinc_specific_price['reduction'] = $specific_price['reduction'] + $giconfig->percentage / 100;
                            }
                        }

                        if ($giconfig->type == 2) {
                            if ($specific_price['reduction'] > 0) {
                                if ($specific_price['reduction_type'] == 'amount') {
                                    $groupinc_specific_price = $specific_price;
                                    $priceToGetDiscount = $price_modified_return;
                                    /*if ($specific_price['reduction_tax'] == 0) {
                                        $priceToGetDiscount = $product_tax_calculator->removeTaxes($price_modified_return);
                                    }*/

                                    $pr = $priceToGetDiscount - $specific_price['reduction'];
                                    $discount_amount = $pr * ($giconfig->percentage / 100);
                                    $groupinc_specific_price['reduction'] = $groupinc_specific_price['reduction'] + $discount_amount;
                                } else {
                                    $groupinc_specific_price['reduction_type'] = 'percentage';
                                    $groupinc_specific_price['reduction'] = $specific_price['reduction'] + $giconfig->percentage / 100;
                                }
                            } else {
                                $groupinc_specific_price['reduction_type'] = 'percentage';
                                $priceTemp = $price_modified_return - $giconfig->fix;
                                $priceTemp = $priceTemp - ($priceTemp * ($giconfig->percentage / 100));
                                $percTemp = Tools::ps_round(($priceTemp / $price_modified_return) * 100, 2);
                                $groupinc_specific_price['reduction'] = strval(1 - ($percTemp)/ 100);

                                $price_modif = $price_modified_return * $groupinc_specific_price['reduction'];
                            }
                        }

                    } else if ($giconfig->skip_discounts && ($specific_price && (isset($specific_price['reduction']) && $specific_price['reduction'] > 0))) {
                        $groupinc_specific_price = $specific_price;
                    } else {
                        if ($giconfig->type == 0) {
                            $groupinc_specific_price['reduction_type'] = 'amount';
                            $groupinc_specific_price['reduction'] = $giconfig->fix;
                        } else if ($giconfig->type == 1) {
                            $groupinc_specific_price['reduction_type'] = 'percentage';
                            $groupinc_specific_price['reduction'] = $giconfig->percentage / 100;
                            $price_modif = $price_modified_return * $groupinc_specific_price['reduction'];
                        } else if ($giconfig->type == 2) {
                            $groupinc_specific_price['reduction_type'] = 'percentage';
                            $priceTemp = $price_modified_return - $giconfig->fix;
                            $priceTemp = $priceTemp - ($priceTemp * ($giconfig->percentage / 100));
                            $percTemp = Tools::ps_round(($priceTemp / $price_modified_return) * 100, 2);
                            $groupinc_specific_price['reduction'] = strval(1 - ($percTemp)/ 100);

                            $price_modif = $price_modified_return * $groupinc_specific_price['reduction'];
                        }
                    }

                    if ($giconfig->type == 0) {
                        $price_modif = $price_modified_return - $groupinc_specific_price['reduction'];
                    } else {
                        $price_modif = $price_modified_return * (1 - $groupinc_specific_price['reduction']);
                    }

                    if ($giconfig->min_result_price > 0) {
                        if ($price_modif < $giconfig->min_result_price) {
                            $groupinc_specific_price['reduction'] = Tools::ps_round(1 - ($giconfig->min_result_price / $price_modified_return), $dec);
                        }
                    }

                    if ($giconfig->max_result_price > 0) {

                        if ($price_modif > $giconfig->max_result_price) {
                            $groupinc_specific_price['reduction'] = Tools::ps_round(1 - ($giconfig->max_result_price / $price_modified_return), $dec);
                        }
                    }
                } else if ($giconfig->mode == 2) {
                    $groupinc_specific_price['reduction_type'] = 'percentage';

                    if ($giconfig->price_application == 2 || $giconfig->price_application == 3 || $giconfig->price_application == 5) {
                        $giconfig->fix = $product_tax_calculator->removeTaxes($giconfig->fix);
                    }
                    if ($price_modified_return > $giconfig->fix) {
                        $groupinc_specific_price['reduction'] = Tools::ps_round(1 - ($giconfig->fix / $price_modified_return), $dec);
                    } else {
                        if ($price_modified_return > 0) {
                            $groupinc_specific_price['reduction'] = (($giconfig->fix - $price_modified_return) / $price_modified_return) * -1;
                        }
                    }
                } else if ($giconfig->mode == 0) {
                    $priceIncremented = $this->getPriceIncremented($giconfig, $price_modified_return, $retailWithTaxes, $retailWithoutTaxes, $wholeWithTaxes, $wholeWithoutTaxes, $supplierWithoutTaxes, $supplierWithTaxes);

                    if ($giconfig->price_application == 3 || $giconfig->price_application == 2 || $giconfig->price_application == 5) {
                        $price_modified_return = $retailWithTaxes;
                    } else {
                        $price_modified_return = $retailWithoutTaxes;
                    }

                    if ($price_modified_return > $priceIncremented) {
                        $groupinc_specific_price['reduction_type'] = 'percentage';
                        $groupinc_specific_price['reduction'] = 1 - (Tools::ps_round($priceIncremented / $price_modified_return, 6));
                    } else {
                        if ($price_modified_return > 0) {
                            $groupinc_specific_price['reduction_type'] = 'percentage';
                            $groupinc_specific_price['reduction'] = 0;
                            $price_modified_return = $priceIncremented;
                        }
                    }
                }

                if ($giconfig->price_application == 2 || $giconfig->price_application == 3 || $giconfig->price_application == 5) {
                    $price_modified_return = $product_tax_calculator->removeTaxes($price_modified_return);
                }

                if (!$analyze) {
                    if ($giconfig->price_application == 0) {
                        $wholeWithoutTaxes = $price_modified_return;
                    } else if ($giconfig->price_application == 1) {
                        $retailWithoutTaxes = $price_modified_return;
                    } else if ($giconfig->price_application == 2) {
                        $wholeWithTaxes = $price_modified_return;
                    } else if ($giconfig->price_application == 3) {
                        $retailWithTaxes = $price_modified_return;
                    }
                }

                if ($analyze) {
                    $pricesReturnToCompare[$giconfig->id_groupinc_configuration] = $price_modified_return * (1 - $groupinc_specific_price['reduction']);
                } else {
                    $groupinc_specific_price['price'] = $price_modified_return;
                }
            } else {
                $price_modified_return = $this->getPriceIncremented($giconfig, $price_modified_return, $retailWithTaxes, $retailWithoutTaxes, $wholeWithTaxes, $wholeWithoutTaxes, $supplierWithoutTaxes, $supplierWithTaxes);

                if ($giconfig->override_discounts) {
                    $groupinc_specific_price = array();
                } else {
                    if (!empty($specific_price)) {
                        $groupinc_specific_price = $specific_price;
                        $groupinc_specific_price['price'] = $price_modified_return;
                    }
                }

                if (!$analyze) {
                    if ($giconfig->price_application == 0) {
                        $wholeWithoutTaxes = $price_modified_return;
                    } else if ($giconfig->price_application == 1) {
                        $retailWithoutTaxes = $price_modified_return;
                    } else if ($giconfig->price_application == 2) {
                        $wholeWithTaxes = $price_modified_return;
                    } else if ($giconfig->price_application == 3) {
                        $retailWithTaxes = $price_modified_return;
                    }
                }

                if ($giconfig->price_application == 2 || $giconfig->price_application == 3 || $giconfig->price_application == 5) {
                    $price_modified_return = $product_tax_calculator->removeTaxes($price_modified_return);
                }

                if ($analyze) {
                    $pricesReturnToCompare[$giconfig->id_groupinc_configuration] = $price_modified_return;
                } else {
                    $groupinc_specific_price['price'] = $price_modified_return;
                }
            }
            if (!$analyze) {
                $specific_price = $groupinc_specific_price;
            }
            $count++;
        }

        if ($analyze) {
            return $pricesReturnToCompare;
        } else {
            if (isset($groupinc_specific_price['reduction'])) {
                $groupinc_specific_price['reduction'] = strval($groupinc_specific_price['reduction']);
            }
            if (isset($groupinc_specific_price['price'])) {
                //if (!$priceDisplay) {
                    //$groupinc_specific_price['price'] = $product_tax_calculator->removeTaxes($groupinc_specific_price['price']);
                //}
            }
            return $groupinc_specific_price;
        }
    }

    public function getPriceIncremented($giconfig, $price_modified_return, $retailWithTaxes, $retailWithoutTaxes, $wholeWithTaxes, $wholeWithoutTaxes, $supplierWithoutTaxes = 0, $supplierWithTaxes = 0)
    {
        $groupinc_quantity_fix = 0;
        $price_modified_return = 0;
        $giconfig->fix = Tools::convertPriceFull($giconfig->fix, new Currency(Configuration::get('PS_CURRENCY_DEFAULT')), Context::getContext()->currency);

        if ($giconfig->mode == 2) { // fixed price import
            $price_modified_return = $giconfig->fix;
        } else {
            if ($giconfig->type == 0) { // fix mode
                if ($giconfig->mode == 0) {
                    $groupinc_quantity_fix += $giconfig->fix;
                } else {
                    $groupinc_quantity_fix -= $giconfig->fix;
                }
                if ($giconfig->price_application == 0) {
                    $price_modified_return = $wholeWithoutTaxes + $groupinc_quantity_fix;
                } else if ($giconfig->price_application == 1) {
                    $price_modified_return = $retailWithoutTaxes + $groupinc_quantity_fix;
                } else if ($giconfig->price_application == 2) {
                    $price_modified_return = $wholeWithTaxes + $groupinc_quantity_fix;
                } else if ($giconfig->price_application == 3) {
                    $price_modified_return = $retailWithTaxes + $groupinc_quantity_fix;
                } else if ($giconfig->price_application == 4) {
                    $price_modified_return = $supplierWithoutTaxes + $groupinc_quantity_fix;
                } else if ($giconfig->price_application == 5) {
                    $price_modified_return = $supplierWithTaxes + $groupinc_quantity_fix;
                }
            } else if ($giconfig->type == 1) { // percentage mode
                if ($giconfig->price_calculation == 0) {
                    $groupinc_quantity_percent = $wholeWithoutTaxes * $giconfig->percentage / 100;
                } else if ($giconfig->price_calculation == 1) {
                    $groupinc_quantity_percent = $retailWithoutTaxes * ($giconfig->percentage / 100);
                } else if ($giconfig->price_calculation == 2) {
                    $groupinc_quantity_percent = $wholeWithTaxes * $giconfig->percentage / 100;
                } else if ($giconfig->price_calculation == 3) {
                    $groupinc_quantity_percent = $retailWithTaxes * $giconfig->percentage / 100;
                } else if ($giconfig->price_calculation == 4) {
                    $groupinc_quantity_percent = $supplierWithoutTaxes * $giconfig->percentage / 100;
                } else if ($giconfig->price_calculation == 5) {
                    $groupinc_quantity_percent = $supplierWithTaxes * $giconfig->percentage / 100;
                } else if ($giconfig->price_calculation == 6) {
                    $groupinc_quantity_percent = ($retailWithoutTaxes - $wholeWithoutTaxes) * $giconfig->percentage / 100;
                } else if ($giconfig->price_calculation == 7) {
                    $groupinc_quantity_percent = ($retailWithTaxes - $wholeWithTaxes) * $giconfig->percentage / 100;
                }
                if ($giconfig->price_application == 0) {
                    if ($giconfig->mode == 0) {
                        $price_modified_return = $wholeWithoutTaxes + $groupinc_quantity_percent;
                    } else {
                        $price_modified_return = $wholeWithoutTaxes - $groupinc_quantity_percent;
                    }
                } else if ($giconfig->price_application == 1) {
                    if ($giconfig->mode == 0) {
                        $price_modified_return = $retailWithoutTaxes + $groupinc_quantity_percent;
                    } else {
                        $price_modified_return = $retailWithoutTaxes - $groupinc_quantity_percent;
                    }
                } else if ($giconfig->price_application == 2) {
                    if ($giconfig->mode == 0) {
                        $price_modified_return = $wholeWithTaxes + $groupinc_quantity_percent;
                    } else {
                        $price_modified_return = $wholeWithTaxes - $groupinc_quantity_percent;
                    }
                } else if ($giconfig->price_application == 3) {
                    if ($giconfig->mode == 0) {
                        $price_modified_return = $retailWithTaxes + $groupinc_quantity_percent;
                    } else {
                        $price_modified_return = $retailWithTaxes - $groupinc_quantity_percent;
                    }
                } else if ($giconfig->price_application == 4) {
                    if ($giconfig->mode == 0) {
                        $price_modified_return = $supplierWithoutTaxes + $groupinc_quantity_percent;
                    } else {
                        $price_modified_return = $supplierWithoutTaxes - $groupinc_quantity_percent;
                    }
                } else if ($giconfig->price_application == 5) {
                    if ($giconfig->mode == 0) {
                        $price_modified_return = $supplierWithTaxes + $groupinc_quantity_percent;
                    } else {
                        $price_modified_return = $supplierWithTaxes - $groupinc_quantity_percent;
                    }
                }
            } else if ($giconfig->type == 2) { // fix + percentage mode
                if ($giconfig->price_calculation == 0) {
                    $groupinc_quantity_percent = $wholeWithoutTaxes * $giconfig->percentage / 100;
                } else if ($giconfig->price_calculation == 1) {
                    $groupinc_quantity_percent = $retailWithoutTaxes * $giconfig->percentage / 100;
                } else if ($giconfig->price_calculation == 2) {
                    $groupinc_quantity_percent = $wholeWithTaxes * $giconfig->percentage / 100;
                } else if ($giconfig->price_calculation == 3) {
                    $groupinc_quantity_percent = $retailWithTaxes * $giconfig->percentage / 100;
                } else if ($giconfig->price_calculation == 4) {
                    $groupinc_quantity_percent = $supplierWithoutTaxes * $giconfig->percentage / 100;
                } else if ($giconfig->price_calculation == 5) {
                    $groupinc_quantity_percent = $supplierWithTaxes * $giconfig->percentage / 100;
                } else if ($giconfig->price_calculation == 6) {
                    $groupinc_quantity_percent = ($retailWithoutTaxes - $wholeWithoutTaxes) * $giconfig->percentage / 100;
                } else if ($giconfig->price_calculation == 7) {
                    $groupinc_quantity_percent = ($retailWithTaxes - $wholeWithTaxes) * $giconfig->percentage / 100;
                }

                if ($giconfig->price_application == 0) {
                    if ($giconfig->mode == 0) {
                        $price_modified_return = $wholeWithoutTaxes + $giconfig->fix + $groupinc_quantity_percent;
                    } else {
                        $price_modified_return = $wholeWithoutTaxes - $giconfig->fix - $groupinc_quantity_percent;
                    }
                } else if ($giconfig->price_application == 1) {
                    if ($giconfig->mode == 0) {
                        $price_modified_return = $retailWithoutTaxes + $giconfig->fix + $groupinc_quantity_percent;
                    } else {
                        $price_modified_return = $retailWithoutTaxes - $giconfig->fix - $groupinc_quantity_percent;
                    }
                } else if ($giconfig->price_application == 2) {
                    if ($giconfig->mode == 0) {
                        $price_modified_return = $wholeWithTaxes + $giconfig->fix + $groupinc_quantity_percent;
                    } else {
                        $price_modified_return = $wholeWithTaxes - $giconfig->fix - $groupinc_quantity_percent;
                    }
                } else if ($giconfig->price_application == 3) {
                    if ($giconfig->mode == 0) {
                        $price_modified_return = $retailWithTaxes + $giconfig->fix + $groupinc_quantity_percent;
                    } else {
                        $price_modified_return = $retailWithTaxes - $giconfig->fix - $groupinc_quantity_percent;
                    }
                } else if ($giconfig->price_application == 4) {
                    if ($giconfig->mode == 0) {
                        $price_modified_return = $supplierWithoutTaxes + $giconfig->fix + $groupinc_quantity_percent;
                    } else {
                        $price_modified_return = $supplierWithoutTaxes - $giconfig->fix - $groupinc_quantity_percent;
                    }
                } else if ($giconfig->price_application == 5) {
                    if ($giconfig->mode == 0) {
                        $price_modified_return = $supplierWithTaxes + $giconfig->fix + $groupinc_quantity_percent;
                    } else {
                        $price_modified_return = $supplierWithTaxes - $giconfig->fix - $groupinc_quantity_percent;
                    }
                }

            }
        }
        if ($giconfig->min_result_price > 0) {
            if ($price_modified_return < $giconfig->min_result_price) {
                $price_modified_return = $giconfig->min_result_price;
            }
        }

        if ($giconfig->max_result_price > 0) {
            if ($price_modified_return > $giconfig->max_result_price) {
                $price_modified_return = $giconfig->max_result_price;
            }
        }
        return $price_modified_return;
    }

    public function getProductsLite($id_lang, $only_active = false, $front = false, Context $context = null, $manufacturers = false, $suppliers = false, $categories = false)
    {
        if (!$context)
            $context = Context::getContext();

        $sql = 'SELECT p.`id_product`, CONCAT(p.`reference`, " - ", pl.`name`) as name FROM `'._DB_PREFIX_.'product` p
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')'.
                ($categories ? ' LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product` ' : '').'
                WHERE pl.`id_lang` = '.(int)$id_lang.
                    ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
                    ($manufacturers ? ' AND p.`id_manufacturer` IN ('.$manufacturers.')' : '').
                    ($suppliers ? ' AND p.`id_supplier` IN ('.$suppliers.')' : '').
                    ($categories ? ' AND cp.`id_category` IN ('.$categories.')' : '').
                    ($only_active ? ' AND product_shop.`active` = 1' : '');

        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return ($rq);
    }

    public static function getProductsFromIdCart($id_cart)
    {
        $sql =
            'SELECT * FROM `'._DB_PREFIX_.'cart_product` WHERE id_cart = '.$id_cart;
        return Db::getInstance()->executeS($sql);
    }

    public function getIdProductAttribute($id_product, $id_attribute)
    {
        $sql =
            'SELECT pac.id_product_attribute
                FROM `'._DB_PREFIX_.'product_attribute_combination` pac
                LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
                WHERE pa.id_product = '.$id_product.' AND pac.id_attribute = '.$id_attribute;
        $result = Db::getInstance()->executeS($sql);

        if (!empty($result)) {
            return $result;
        } else {
            return 0;
        }
    }

    public function getWholesalePrice($id_product, $id_product_attribute)
    {
        $sql =
            'SELECT pa.wholesale_price
                FROM `'._DB_PREFIX_.'product_attribute` pa
                WHERE pa.id_product = '.$id_product.' AND pa.id_product_attribute = '.$id_product_attribute;
        $result = Db::getInstance()->getRow($sql);
        if (!empty($result)) {
            return $result['wholesale_price'];
        } else {
            return 0;
        }
    }

    public function getImpact($id_product, $id_product_attribute)
    {
        $sql =
            'SELECT pa.price
                FROM `'._DB_PREFIX_.'product_attribute` pa
                WHERE pa.id_product = '.$id_product.' AND pa.id_product_attribute = '.$id_product_attribute;
        $result = Db::getInstance()->getRow($sql);
        if (!empty($result)) {
            return $result['price'];
        } else {
            return 0;
        }
    }

    public function getProductsPricesDrop($conf, $id_shop, $id_lang)
    {
        $productsReturn = array();

        if ($conf['products'] == '' && $conf['manufacturers'] == '' && $conf['categories'] == '' && $conf['suppliers'] == '' && $conf['attributes'] && $conf['features'] && !$conf['filter_stock'] && !$conf['filter_prices']) {
            return $this->getProductsLite($id_lang, true, true);
        }

        $categories = false;
        $manufacturers = false;
        $suppliers = false;

        if ($conf['products'] != '') {
            $products = explode(";", $conf['products']);
        } else {
            if ($conf['categories'] != '') {
                if (@unserialize($conf['categories']) !== false) {
                    $categories = implode(',', unserialize($conf['categories']));
                } else {
                    $categories = implode(',', explode(';', $conf['categories']));
                }
            }

            if ($conf['manufacturers'] != '') {
                $manufacturers = implode(',',explode(";", $conf['manufacturers']));
            }

            if ($conf['suppliers']) {
                $suppliers = implode(',',explode(";", $conf['suppliers']));
            }

            $products = $this->getProductsLite($id_lang, true, true, null, $manufacturers, $suppliers, $categories);

            if ($conf['features']) {
                $array_features_selected = Tools::jsonDecode($conf['features'], true);
                $array_features_string = implode(',', $array_features_selected);

                $query = '
                    SELECT id_product FROM `'._DB_PREFIX_.'feature_product`
                    WHERE id_feature_value in ('.$array_features_string.')';


                $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

            }
            foreach ($products as $p) {
                $prods[] = $p['id_product'];
            }
            $products = $prods;

        }

        foreach ($products as $p) {
            if ($conf['filter_stock']) {
                $stock = Product::getQuantity($p['id_product']);

                if ($conf['min_stock'] == 0 && $conf['max_stock'] == 0 && $stock > 0) {
                    continue;
                } else {
                    if ($stock < $conf['min_stock'] || $stock > $conf['max_stock']) {
                        continue;
                    }
                }
            }

            if ($conf['filter_prices']) {
                $pr = new Product($p);

                $price = Product::getPriceStatic((int)$pr->id, false, 0, 6, null, false, false, 1, false, Context::getContext()->customer->id);
                $price_withtax = Product::getPriceStatic((int)$pr->id, true, 0, 6, null, false, false, 1, false, Context::getContext()->customer->id);

                if ((float)$conf['threshold_min_price'] > 0 || (float)$conf['threshold_max_price'] > 0) {
                    if ($conf['threshold_price'] == 0) {
                        $price_to_compare = $pr->wholesale_price;
                    } else if ($conf['threshold_price'] == 1) {
                        $price_to_compare = $price;
                    } else if ($conf['threshold_price'] == 2) {
                        $price_to_compare = $pr->wholesale_price;
                    } else if ($conf['threshold_price'] == 3) {
                        $price_to_compare = $price_withtax;
                    }
                }
                if ($price_to_compare < (float)$conf['threshold_min_price'] || $price_to_compare > (float)$conf['threshold_max_price']) {
                    continue;
                }
            }
            $productsReturn[] = (int)$p;
        }
        return $productsReturn;
    }

    public static function isShowableBySchedule($configuration)
    {
        $schedule = Tools::jsonDecode($configuration['schedule']);
        $dayOfWeek = date('w') - 1;
        if ($dayOfWeek < 0) {
            $dayOfWeek = 6;
        }
        if (is_array($schedule)) {
            if (is_object($schedule[$dayOfWeek]) && $schedule[$dayOfWeek]->isActive === true) {
                if ($schedule[$dayOfWeek]->timeFrom <= date('H:i') && $schedule[$dayOfWeek]->timeTill > date('H:i')) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public static function isProductItemsCart($configuration, $quantity, $id_product, $id_product_attribute)
    {
        if (!empty(Context::getContext()->cart) && Context::getContext()->cart->id) {
            $products = GroupincConfiguration::getProductsFromIdCart(Context::getContext()->cart->id);
        }
        $product_count_flag = false;

        if ($quantity > 1 && $quantity >= $configuration['product_qty']) {
            $product_count_flag = true;
        } else if (!empty($products)) {
            foreach ($products as $p) {
                if ($p['id_product'] == $id_product && $p['id_product_attribute'] == $id_product_attribute) {
                    if ((int)$p['quantity'] >= (int)$configuration['product_qty']) {
                        $product_count_flag = true;
                        break;
                    } else {
                        $product_count_flag = false;
                    }
                } else {
                    $product_count_flag = false;
                }
            }
        }
        return $product_count_flag;
    }

    public static function getProductProperties($id_lang, $row, $context)
    {
        $id_shop = $id_customer = $id_country = $id_state = $id_currency = 0;
        $configs_onsale_show_discounts = GroupincConfiguration::getGIConfigurations($id_shop, $row['id_product'], $id_customer, $id_country, $id_state, $id_currency, $id_lang, true, true, 0, 0, false, true);
        if (!empty($configs_onsale_show_discounts)) {
            $row['on_sale'] = 1;
        }
        return $row;
    }
}