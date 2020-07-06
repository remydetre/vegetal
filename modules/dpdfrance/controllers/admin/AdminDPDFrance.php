<?php
/**
 * 2007-2018 PrestaShop
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
 * @author    DPD France S.A.S. <support.ecommerce@dpd.fr>
 * @copyright 2018 DPD France S.A.S.
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class AdminDPDFranceController extends ModuleAdminController
{
    public $identifier = 'DPDFrance';


    public function __construct()
    {
        $this->name = 'DPDFrance';
        $this->bootstrap = true;
        $this->display = 'view';
        $this->meta_title = 'Gestion des expéditions';

        parent::__construct();

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
    }



    /* Converts country ISO code to DPD Station format */
    public static function getIsoCodebyIdCountry($idcountry)
    {
        $sql='
            SELECT `iso_code`
            FROM `'._DB_PREFIX_.'country`
            WHERE `id_country` = \''.pSQL($idcountry).'\'';
        $result=Db::getInstance('_PS_USE_SQL_SLAVE_')->getRow($sql);
        $isops=array('DE', 'AD', 'AT', 'BE', 'BA', 'BG', 'HR', 'DK', 'ES', 'EE', 'FI', 'FR', 'GB', 'GR', 'GG', 'HU', 'IM', 'IE', 'IT', 'JE', 'LV', 'LI', 'LT', 'LU', 'MC', 'NO', 'NL', 'PL', 'PT', 'CZ', 'RO', 'RS', 'SK', 'SI', 'SE', 'CH');
        $isoep=array('D', 'AND', 'A', 'B', 'BA', 'BG', 'CRO', 'DK', 'E', 'EST', 'SF', 'F', 'GB', 'GR', 'GG', 'H', 'IM', 'IRL', 'I', 'JE', 'LET', 'LIE', 'LIT', 'L', 'F', 'N', 'NL', 'PL', 'P', 'CZ', 'RO', 'RS', 'SK', 'SLO', 'S', 'CH');
        if (in_array($result['iso_code'], $isops)) {
            // If the ISO code is in Europe, then convert it to DPD Station format
            $code_iso=str_replace($isops, $isoep, $result['iso_code']);
        } else {
            // If not, then it will be 'INT' (intercontinental)
            $code_iso=str_replace($result['iso_code'], 'INT', $result['iso_code']);
        }
        return $code_iso;
    }

    /* Get all orders but statuses cancelled, delivered, error */
    public static function getAllOrders($id_shop)
    {
        if ($id_shop==0) {
            $id_shop='LIKE "%"';
        } else {
            $id_shop='= '.(int) $id_shop;
        }
        $sql='  SELECT id_order
                FROM '._DB_PREFIX_.'orders O
                WHERE `current_state` NOT IN('.(int) Configuration::get('DPDFRANCE_ETAPE_LIVRE', null, null, (int) $id_shop).',0,5,6,7,8) AND O.id_shop '.$id_shop.'
                ORDER BY id_order DESC
                LIMIT 1000';
        $result=Db::getInstance()->ExecuteS($sql);
        $orders=array();
        if (!empty($result)) {
            foreach ($result as $order) {
                $orders[]=(int) $order['id_order'];
            }
        }
        return $orders;
    }

    /* Formats GSM numbers */
    public static function formatGSM($tel_dest, $code_pays_dest)
    {
        $tel_dest=str_replace(array(' ', '.', '-', ',', ';', '/', '\\', '(', ')'), '', $tel_dest);
        // Chrome autofill fix
        if (Tools::substr($tel_dest, 0, 2)==33) {
            $tel_dest=substr_replace($tel_dest, '0', 0, 2);
        }
        switch ($code_pays_dest) {
            case 'F':
                if (preg_match('/^((\+33|0)[67])(?:[ _.-]?(\d{2})){4}$/', $tel_dest)) {
                    return $tel_dest;
                } else {
                    return false;
                }
                break;

            case 'D':
                if (preg_match('/^(\+|00)49(15|16|17)(\s?\d{7,8})$/', $tel_dest)) {
                    return $tel_dest;
                } else {
                    return false;
                }
                break;

            case 'B':
                if (preg_match('/^(\+|00)324([56789]\d)(\s?\d{6})$/', $tel_dest)) {
                    return $tel_dest;
                } else {
                    return false;
                }
                break;

            case 'AT':
                if (preg_match('/^(\+|00)436([56789]\d)(\s?\d{4})$/', $tel_dest)) {
                    return $tel_dest;
                } else {
                    return false;
                }
                break;

            case 'GB':
                if (preg_match('/^(\+|00)447([3456789]\d)(\s?\d{7})$/', $tel_dest)) {
                    return $tel_dest;
                } else {
                    return false;
                }
                break;

            case 'NL':
                if (preg_match('/^(\+|00)316(\s?\d{8})$/', $tel_dest)) {
                    return $tel_dest;
                } else {
                    return false;
                }
                break;

            case 'P':
                if (preg_match('/^(\+|00)3519(\s?\d{7})$/', $tel_dest)) {
                    return $tel_dest;
                } else {
                    return false;
                }
                break;

            case 'IRL':
                if (preg_match('/^(\+|00)3538(\s?\d{8})$/', $tel_dest)) {
                    return $tel_dest;
                } else {
                    return false;
                }
                break;

            case 'E':
                if (preg_match('/^(\+|00)34(6|7)(\s?\d{8})$/', $tel_dest)) {
                    return $tel_dest;
                } else {
                    return false;
                }
                break;

            case 'I':
                if (preg_match('/^(\+|00)393(\s?\d{9})$/', $tel_dest)) {
                    return $tel_dest;
                } else {
                    return false;
                }
                break;

            default:
                return $tel_dest;
                break;
        }
    }

    /* Get delivery service for a cart ID & checks if id_carrier matches */
    public static function getService($order, $lang_id)
    {
        $sql=Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'dpdfrance_shipping` WHERE `id_cart` = '.(int) $order->id_cart.' AND `id_carrier` = '.(int) $order->id_carrier);
        $service=$sql['service'];
        // Service override, forcing Relais or Predict shipment on eligible orders
        if (!$service) {
            $address_invoice=new Address($order->id_address_invoice, (int) $lang_id);
            $address_delivery=new Address($order->id_address_delivery, (int) $lang_id);
            $code_pays_dest=self::getIsoCodebyIdCountry((int) $address_delivery->id_country);
            $tel_dest=(($address_delivery->phone_mobile)?$address_delivery->phone_mobile:(($address_invoice->phone_mobile)?$address_invoice->phone_mobile:(($address_delivery->phone)?$address_delivery->phone:(($address_invoice->phone)?$address_invoice->phone:''))));
            $mobile=self::formatGSM($tel_dest, $code_pays_dest);
            if (preg_match('/P\d{5}/i', $address_delivery->company)) {
                $service='REL';
            } elseif ($mobile&&$code_pays_dest!='INT'&&$order->id_carrier!=Configuration::get('DPDFRANCE_CLASSIC_CARRIER_ID', null, null, (int) $order->id_shop)) {
                $service='PRE';
            }
        }
        return $service;
    }

    /* Sync order status with parcel status, adds tracking number */
    public function syncShipments($id_employee, $force)
    {
        /* Check if last tracking call is more than 1h old */
        if ((time() - (int)Configuration::get('DPDFRANCE_LAST_TRACKING') < 3600) && $force == 0) {
            die('DPD France parcel tracking update is done once every hour. - Last update on : '.date('d/m/Y - H:i:s', Configuration::get('DPDFRANCE_LAST_TRACKING')));
        }
        Configuration::updateValue('DPDFRANCE_LAST_TRACKING', time());

        $predict_carrier_log = $classic_carrier_log = $relais_carrier_log = $predict_carrier_sql = $classic_carrier_sql = $relais_carrier_sql = '';

        if (Configuration::get('DPDFRANCE_MARKETPLACE_MODE')) {
            $europe_carrier_sql = 'CA.name LIKE \'%%\'';
        } else {
            $europe_carrier_sql = 'CA.name LIKE \'%DPD%\'';
        }

        if (Configuration::get('DPDFRANCE_PREDICT_CARRIER_ID')) {
            $predict_carrier_log = Configuration::get('DPDFRANCE_PREDICT_CARRIER_ID').','.implode(',', array_map('intval', explode('|', Tools::substr(Configuration::get('DPDFRANCE_PREDICT_CARRIER_LOG'), 1))));
            $predict_carrier_sql = 'CA.id_carrier IN ('.$predict_carrier_log.') OR ';
        }
        if (Configuration::get('DPDFRANCE_CLASSIC_CARRIER_ID')) {
            $classic_carrier_log = Configuration::get('DPDFRANCE_CLASSIC_CARRIER_ID').','.implode(',', array_map('intval', explode('|', Tools::substr(Configuration::get('DPDFRANCE_CLASSIC_CARRIER_LOG'), 1))));
            $classic_carrier_sql = 'CA.id_carrier IN ('.$classic_carrier_log.') OR ';
        }
        if (Configuration::get('DPDFRANCE_RELAIS_CARRIER_ID')) {
            $relais_carrier_log = Configuration::get('DPDFRANCE_RELAIS_CARRIER_ID').','.implode(',', array_map('intval', explode('|', Tools::substr(Configuration::get('DPDFRANCE_RELAIS_CARRIER_LOG'), 1))));
            $relais_carrier_sql = 'CA.id_carrier IN ('.$relais_carrier_log.') OR ';
        }

        $sql = 'SELECT  O.reference as reference, O.id_carrier as id_carrier, O.id_order as id_order, O.shipping_number as shipping_number, O.id_shop as id_shop
                FROM    '._DB_PREFIX_.'orders AS O, '._DB_PREFIX_.'carrier AS CA
                WHERE   CA.id_carrier=O.id_carrier AND O.current_state
                NOT IN  ('.(int) Configuration::get('DPDFRANCE_ETAPE_LIVRE').',0,5,6,7,8) AND
                        ('.$predict_carrier_sql.$classic_carrier_sql.$relais_carrier_sql.$europe_carrier_sql.')
                        ORDER BY id_order DESC
                        LIMIT 1000';

        $orderlist=Db::getInstance()->ExecuteS($sql);

        if (!empty($orderlist)) {
            echo 'DPD France - Sync started<br/>';
            foreach ($orderlist as $orderinfos) {
                $statuslist=array();
                if (Validate::isLoadedObject($order = new Order($orderinfos['id_order']))) {
                    $internalref = $order->reference;
                    // Check past order states
                    $past_states = 0;
                    $orderhistory = $order->getHistory($order->id_lang);
                    foreach ($orderhistory as $state) {
                        if ($state['id_order_state'] == (int)Configuration::get('DPDFRANCE_ETAPE_EXPEDIEE', null, null, (int)$order->id_shop)) {
                            $past_states = 1;
                        } else {
                            if ($state['id_order_state'] == (int)Configuration::get('DPDFRANCE_ETAPE_LIVRE', null, null, (int)$order->id_shop)) {
                                $past_states = 2;
                                break;
                            }
                        }
                    }

                    // Exclude already delivered orders from sync
                    if ($past_states == 2) {
                        continue;
                    }

                    // Retrieve DPD service
                    $service=self::getService($order, Context::getContext()->language->id);
                    switch ($service) {
                        case 'PRE':
                            $compte_chargeur=Configuration::get('DPDFRANCE_PREDICT_SHIPPER_CODE', null, null, (int) $order->id_shop);
                            $depot_code=Configuration::get('DPDFRANCE_PREDICT_DEPOT_CODE', null, null, (int) $order->id_shop);
                            break;
                        case 'REL':
                            $compte_chargeur=Configuration::get('DPDFRANCE_RELAIS_SHIPPER_CODE', null, null, (int) $order->id_shop);
                            $depot_code=Configuration::get('DPDFRANCE_RELAIS_DEPOT_CODE', null, null, (int) $order->id_shop);
                            break;
                        default:
                            $compte_chargeur=Configuration::get('DPDFRANCE_CLASSIC_SHIPPER_CODE', null, null, (int) $order->id_shop);
                            $depot_code=Configuration::get('DPDFRANCE_CLASSIC_DEPOT_CODE', null, null, (int) $order->id_shop);
                            break;
                    }
                    if (!$compte_chargeur || !$depot_code) {
                        continue;
                    }

                    $variables=array(   'customer_center'=>'3',
                                        'customer'=>'1064',
                                        'password'=>'Pr2%5sHg',
                                        'reference'=>$internalref,
                                        'shipping_date'=>'',
                                        'shipping_customer_center'=>$depot_code,
                                        'shipping_customer'=>$compte_chargeur,
                                        'searchmode'=>'SearchMode_Equals',
                                        'language'=>'F',
                                    );
                    $serviceurl='http://webtrace.dpd.fr/dpd-webservices/webtrace_service.asmx?WSDL';

                    // Call WS for traces by reference
                    try {
                        $client=new SoapClient($serviceurl, array('connection_timeout'=>5, 'exceptions'=>true));
                        $response=$client->getShipmentTraceByReferenceGlobalWithCenterAsArray($variables);
                        $result=$response->getShipmentTraceByReferenceGlobalWithCenterAsArrayResult->clsShipmentTrace;

                        if (!empty($result->LastError)) {
                            echo 'Order'.' '.$internalref.' - '.'Error: '.$result->LastError.'<br/>';
                        } else {
                            // Only one parcel per reference
                            if (!is_array($result)) {
                                $traces=$result->Traces->clsTrace;
                                $returned_ref=$result->Reference;
                                if ($internalref == $returned_ref) {
                                    // Parcels with only one status
                                    if (!is_array($traces)) {
                                        // Exclude CEDI-only parcels
                                        if ($traces->StatusNumber != 8) {
                                            $statuslist[$result->ShipmentNumber][]=$traces->StatusNumber;
                                        }
                                    } else {
                                        // Parcel with multiple statuses
                                        foreach ($traces as $status) {
                                            $statuslist[$result->ShipmentNumber][]=$status->StatusNumber;
                                        }
                                    }
                                }
                            } else {
                                // Multiple parcels per reference
                                foreach ($result as $shipment) {
                                    $returned_ref=$shipment->Reference;
                                    if ($internalref == $returned_ref) {
                                        $variables2=array(  'customer_center'=>'3',
                                                            'customer'=>'1064',
                                                            'password'=>'Pr2%5sHg',
                                                            'shipmentnumber'=>$shipment->ShipmentNumber
                                                        );
                                        $response2=$client->getShipmentTrace($variables2);
                                        $traces=$response2->getShipmentTraceResult->Traces->clsTrace;
                                        // Parcels with only one status
                                        if (!is_array($traces)) {
                                            // Exclude CEDI-only parcels
                                            if ($traces->StatusNumber == 8) {
                                                continue;
                                            }
                                            $statuslist[$shipment->ShipmentNumber][]=$traces->StatusNumber;
                                        } else {
                                            // Parcel with multiple statuses
                                            foreach ($traces as $status) {
                                                $statuslist[$shipment->ShipmentNumber][]=$status->StatusNumber;
                                            }
                                        }
                                    }
                                    break; // Stop at first parcel
                                }
                            }

                            if (!empty($statuslist)) {
                                // Check delivery state
                                $tracking_number = (key($statuslist));
                                $delivery_state = 0;
                                foreach ($statuslist as $events) {
                                    // Check if en-route event has been applied
                                    if (array_intersect(array(10, 28, 89), $events)) {
                                        $delivery_state = 1;
                                    }
                                    // Check if delivered event has been applied
                                    if (array_intersect(array(40, 400), $events)) {
                                        $delivery_state = 2;
                                    }
                                }

                                // Add tracking number if empty
                                if (!$order->shipping_number && $delivery_state != 0) {
                                    if (Configuration::get('DPDFRANCE_AUTO_UPDATE') == 2) {
                                        $url = 'http://www.dpd.fr/traces_'.$tracking_number;
                                        $order->shipping_number=$tracking_number;
                                    } else {
                                        $url = 'http://www.dpd.fr/tracex_'.$internalref.'_'.$depot_code.$compte_chargeur;
                                        $order->shipping_number=$internalref.'_'.$depot_code.$compte_chargeur;
                                    }
                                    Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'orders SET shipping_number = "' . pSQL($order->shipping_number) . '" WHERE id_order = "' . (int)$order->id . '"');
                                    Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'order_carrier SET tracking_number = "' . pSQL($order->shipping_number) . '" WHERE id_order = "' . (int)$order->id . '"');
                                    $order->update();
                                    echo 'Order' . ' ' . $internalref . ' - ' . 'Tracking number' . ' ' . $tracking_number . ' ' . 'added' . '<br/>';
                                }

                                // Update to delivered status only if parcel is delivered and there is no previous delivered status applied to that order
                                if ($delivery_state == 2 && $past_states != 2) {
                                    $history = new OrderHistory();
                                    $history->id_order = (int)$order->id;
                                    $history->id_employee = (int)$id_employee;
                                    $history->id_order_state = (int)Configuration::get('DPDFRANCE_ETAPE_LIVRE', null, null, (int)$order->id_shop);
                                    $history->changeIdOrderState((int)Configuration::get('DPDFRANCE_ETAPE_LIVRE', null, null, (int)$order->id_shop), $order->id);
                                    $history->addWithemail();
                                    echo 'Order' . ' ' . $internalref . ' - ' . 'Tracking number' . ' ' . $tracking_number . ' ' . 'is delivered' . '<br/>';
                                } else {
                                    // Update to shipped status only if parcel is en route and there are no previous shipped or delivered status applied to that order
                                    if ($delivery_state == 1 && $past_states == 0) {
                                        $customer = new Customer((int)$order->id_customer);
                                        $history = new OrderHistory();
                                        $history->id_order = (int)$order->id;
                                        $history->id_employee = (int)$id_employee;
                                        $history->id_order_state = (int)Configuration::get('DPDFRANCE_ETAPE_EXPEDIEE', null, null, (int)$order->id_shop);
                                        $history->changeIdOrderState((int)Configuration::get('DPDFRANCE_ETAPE_EXPEDIEE', null, null, (int)$order->id_shop), $order->id);
                                        $template_vars = array('{followup}' => $url, '{firstname}' => $customer->firstname, '{lastname}' => $customer->lastname, '{order_name}' => $internalref, '{id_order}' => (int)$order->id);
                                        switch (Language::getIsoById((int)$order->id_lang)) {
                                            case 'fr':
                                                $subject = 'Votre commande sera livrée par DPD';
                                                break;
                                            case 'en':
                                                $subject = 'Your parcel will be delivered by DPD';
                                                break;
                                            case 'es':
                                                $subject = 'Su pedido será enviado por DPD';
                                                break;
                                            case 'it':
                                                $subject = 'Il vostro pacchetto sará trasportato da DPD';
                                                break;
                                            case 'de':
                                                $subject = 'Ihre Bestellung wird per DPD geliefert werden';
                                                break;
                                        }
                                        $history->addWithemail(true, $template_vars);
                                        Mail::Send((int)$order->id_lang, 'in_transit', $subject, $template_vars, $customer->email, $customer->firstname . ' ' . $customer->lastname);
                                        echo 'Order' . ' ' . $internalref . ' - ' . 'Parcel' . ' ' . $tracking_number . ' ' . 'is handled by DPD' . '<br/>';
                                    } else {
                                        echo 'Order' . ' ' . $internalref . ' - ' . 'No update for parcel' . ' ' . $tracking_number . '<br/>';
                                    }
                                }
                            } else {
                                echo 'Order' . ' ' . $internalref . ' - ' . 'Parcel is found, not yet handled by DPD' . '<br/>';
                            }
                        }
                    } catch (SoapFault $e) {
                        echo 'Order' . ' ' . $internalref . ' - ' . 'Error: '.$e->getMessage().'<br/>';
                        continue;
                    }
                }
            }
            echo 'DPD France - Sync complete.';
        } else {
            echo 'DPD France - No orders to update.';
        }
    }

    /* Get eligible orders and builds up display */
    public function renderView()
    {
        $this->fields_form[]['form'] = array();
        $helper = $this->buildHelper();
        $msg = '';
        // RSS stream
        $stream=array();
        $rss=@simplexml_load_string(Tools::file_get_contents('http://www.dpd.fr/extensions/rss/flux_info_dpdfr.xml'));
        if (!empty($rss)) {
            if (empty($rss->channel->item)) {
                $stream['error']=true;
            } else {
                $i=0;
                foreach ($rss->channel->item as $item) {
                    $stream[$i]=array(  'category'=>(string) $item->category,
                                        'title'=>(string) $item->title,
                                        'description'=>(string) $item->description,
                                        'date'=>strtotime((string) $item->pubDate)
                                    );
                    if (strtotime("-30 day", strtotime(date('d-m-Y')))>$stream[$i]['date']) {
                        unset($stream[$i]);
                    }
                    $i++;
                }
            }
            if (empty($stream)) {
                $stream['error']=true;
            }
        } else {
            $stream['error']=true;
        }

        // Update delivered orders
        if (Tools::getIsset('updateDeliveredOrders')) {
            if (Tools::getIsset('checkbox')) {
                $orders=Tools::getValue('checkbox');
                if (is_string($orders)) {
                    $orders = explode(',', $orders);
                }
                if (!empty($orders)) {
                    $sql='SELECT    O.`id_order` AS id_order
                          FROM      '._DB_PREFIX_.'orders AS O,
                                    '._DB_PREFIX_.'carrier AS CA
                          WHERE     CA.id_carrier=O.id_carrier AND
                                    id_order IN ('.implode(',', array_map('intval', $orders)).')';
                    $orderlist=Db::getInstance()->ExecuteS($sql);
                    if (!empty($orderlist)) {
                        // Check if there are DPD orders
                        foreach ($orderlist as $orders) {
                            $id_order=$orders['id_order'];
                            if (Validate::isLoadedObject($order = new Order($id_order))) {
                                $history=new OrderHistory();
                                $history->id_order=(int) $id_order;
                                $history->id_order_state=(int) Configuration::get('DPDFRANCE_ETAPE_LIVRE', null, null, (int) $order->id_shop);
                                $history->changeIdOrderState((int) Configuration::get('DPDFRANCE_ETAPE_LIVRE', null, null, (int) $order->id_shop), $id_order);
                                $history->id_employee=(int) Context::getContext()->employee->id;
                                $history->addWithemail();
                            }
                        }
                        $msg = '<div class="okmsg">'.$this->l('Delivered orders statuses were updated').'</div>';
                    } else {
                        $msg = '<div class="warnmsg">'.$this->l('No DPD trackings to generate.').'</div>';
                    }
                } else {
                    $msg = '<div class="warnmsg">'.$this->l('No order selected.').'</div>';
                }
            } else {
                    $msg = '<div class="warnmsg">'.$this->l('No order selected.').'</div>';
            }
        }

        // Update shipped orders
        if (Tools::getIsset('updateShippedOrders')) {
            if (Tools::getIsset('checkbox')) {
                $orders = Tools::getValue('checkbox');
                if (is_string($orders)) {
                    $orders = explode(',', $orders);
                }
                $sql = 'SELECT  O.`id_order` AS id_order
                        FROM    '._DB_PREFIX_.'orders AS O, 
                                '._DB_PREFIX_.'carrier AS CA 
                        WHERE   CA.id_carrier=O.id_carrier AND 
                                id_order IN ('.implode(',', array_map('intval', $orders)).')';

                $orderlist = Db::getInstance()->ExecuteS($sql);

                // Check if there are DPD orders
                if (!empty($orderlist)) {
                    foreach ($orderlist as $orders) {
                        $id_order = $orders['id_order'];
                        if (Validate::isLoadedObject($order = new Order($id_order))) {
                            $internalref = $order->reference;
                            $service=self::getService($order, Context::getContext()->language->id);
                            switch ($service) {
                                case 'PRE':
                                    $compte_chargeur = Configuration::get('DPDFRANCE_PREDICT_SHIPPER_CODE', null, null, (int)$order->id_shop);
                                    $depot_code = Configuration::get('DPDFRANCE_PREDICT_DEPOT_CODE', null, null, (int)$order->id_shop);
                                    break;
                                case 'REL':
                                    $compte_chargeur = Configuration::get('DPDFRANCE_RELAIS_SHIPPER_CODE', null, null, (int)$order->id_shop);
                                    $depot_code = Configuration::get('DPDFRANCE_RELAIS_DEPOT_CODE', null, null, (int)$order->id_shop);
                                    break;
                                default:
                                    $compte_chargeur = Configuration::get('DPDFRANCE_CLASSIC_SHIPPER_CODE', null, null, (int)$order->id_shop);
                                    $depot_code = Configuration::get('DPDFRANCE_CLASSIC_DEPOT_CODE', null, null, (int)$order->id_shop);
                                    break;
                            }

                            $customer = new Customer((int)$order->id_customer);
                            if (Configuration::get('DPDFRANCE_AUTO_UPDATE') != 2) {
                                $order->shipping_number = $internalref.'_'.$depot_code.$compte_chargeur;
                                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'orders SET shipping_number = "'.pSQL($order->shipping_number).'" WHERE id_order = "'.$id_order.'"');
                                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'order_carrier SET tracking_number = "'.pSQL($order->shipping_number).'" WHERE id_order = "'.$id_order.'"');
                                $order->update();
                            }
                            $history = new OrderHistory();
                            $history->id_order = (int)$id_order;
                            $history->changeIdOrderState(Configuration::get('DPDFRANCE_ETAPE_EXPEDIEE', null, null, (int)$order->id_shop), $id_order);
                            $history->id_employee = (int)Context::getContext()->employee->id;
                            $carrier = new Carrier((int)$order->id_carrier, (int)Context::getContext()->language->id);
                            $url = 'http://www.dpd.fr/tracex_'.$internalref.'_'.$depot_code.$compte_chargeur;
                            $template_vars = array('{followup}' => $url, '{firstname}' => $customer->firstname, '{lastname}' => $customer->lastname, '{order_name}' => $order->reference, '{id_order}' => (int)$order->id);
                            switch (Language::getIsoById((int)$order->id_lang)) {
                                case 'fr':
                                    $subject = 'Votre commande sera livrée par DPD';
                                    break;
                                case 'en':
                                    $subject = 'Your parcel will be delivered by DPD';
                                    break;
                                case 'es':
                                    $subject = 'Su pedido será enviado por DPD';
                                    break;
                                case 'it':
                                    $subject = 'Il vostro pacchetto sará trasportato da DPD';
                                    break;
                                case 'de':
                                    $subject = 'Ihre Bestellung wird per DPD geliefert werden';
                                    break;
                            }
                            if (!$history->addWithemail(true, $template_vars)) {
                                $this->_errors[] = Tools::displayError('an error occurred while changing status or was unable to send e-mail to the customer');
                            }
                            if (!Validate::isLoadedObject($customer) || !Validate::isLoadedObject($carrier)) {
                                die(Tools::displayError());
                            }
                            Mail::Send((int)$order->id_lang, 'in_transit', $subject, $template_vars, $customer->email, $customer->firstname.' '.$customer->lastname);
                        }
                    }
                    $msg = '<div class="okmsg">'.$this->l('Shipped orders statuses were updated and tracking numbers added.').'</div>';
                } else {
                    $msg = '<div class="warnmsg">'.$this->l('No trackings to generate.').'</div>';
                }
            } else {
                $msg = '<div class="warnmsg">'.$this->l('No order selected.').'</div>';
            }
        }

        // Export selected orders
        if (Tools::getIsset('exportOrders')) {
            $fieldlist = array('O.`id_order`', 'AD.`lastname`', 'AD.`firstname`', 'AD.`postcode`', 'AD.`city`', 'CL.`iso_code`', 'C.`email`');
            if (Tools::getIsset('checkbox')) {
                $orders = Tools::getValue('checkbox');
                if (is_string($orders)) {
                    $orders = explode(',', $orders);
                }
                $liste_expeditions = 'O.id_order IN ('.implode(',', array_map('intval', $orders)).')';

                if (!empty($orders)) {
                    $sql = 'SELECT  '.implode(', ', $fieldlist).'
                            FROM    '._DB_PREFIX_.'orders AS O, 
                                    '._DB_PREFIX_.'carrier AS CA, 
                                    '._DB_PREFIX_.'customer AS C, 
                                    '._DB_PREFIX_.'address AS AD, 
                                    '._DB_PREFIX_.'country AS CL
                            WHERE   O.id_address_delivery=AD.id_address AND
                                    C.id_customer=O.id_customer AND 
                                    CL.id_country=AD.id_country AND 
                                    CA.id_carrier=O.id_carrier AND 
                                    ('.$liste_expeditions.')
                            ORDER BY id_order DESC';

                    $orderlist = Db::getInstance()->ExecuteS($sql);

                    if (!empty($orderlist)) {
                        // File creation
                        require_once(_PS_MODULE_DIR_.'dpdfrance/classes/admin/DPDStation.php');
                        $record=new DPDStation();
                        foreach ($orderlist as $order_var) {
                            // Shipper information retrieval
                            $order              = new Order($order_var['id_order']);
                            $nom_exp            = Configuration::get('DPDFRANCE_NOM_EXP', null, null, (int)$order->id_shop);            // Raison sociale expéditeur
                            $address_exp        = Configuration::get('DPDFRANCE_ADDRESS_EXP', null, null, (int)$order->id_shop);        // Adresse
                            $address2_exp       = Configuration::get('DPDFRANCE_ADDRESS2_EXP', null, null, (int)$order->id_shop);       // Complément d'adresse
                            $cp_exp             = Configuration::get('DPDFRANCE_CP_EXP', null, null, (int)$order->id_shop);             // Code postal
                            $ville_exp          = Configuration::get('DPDFRANCE_VILLE_EXP', null, null, (int)$order->id_shop);          // Ville
                            $code_pays_exp      = 'F';                                                                                  // Code pays
                            $tel_exp            = Configuration::get('DPDFRANCE_TEL_EXP', null, null, (int)$order->id_shop);            // Téléphone
                            $email_exp          = Configuration::get('DPDFRANCE_EMAIL_EXP', null, null, (int)$order->id_shop);          // E-mail
                            $gsm_exp            = Configuration::get('DPDFRANCE_GSM_EXP', null, null, (int)$order->id_shop);            // N° GSM
                            $internalref        = $order->reference;
                            $customer           = new Customer($order->id_customer);
                            $address_invoice    = new Address($order->id_address_invoice, (int)Context::getContext()->language->id);
                            $address_delivery   = new Address($order->id_address_delivery, (int)Context::getContext()->language->id);
                            $code_pays_dest     = self::getIsoCodebyIdCountry((int)$address_delivery->id_country);

                            // Ireland override
                            if ($code_pays_dest == 'IRL') {
                                if (stripos($address_delivery->city, 'Dublin') !== false) {
                                    $address_delivery->postcode = 1;
                                } else {
                                    $address_delivery->postcode = 2;
                                }
                            }

                            $instr_liv_cleaned  = '';
                            $order_messages     = Message::getMessagesByOrderId($order->id);
                            if ($order_messages) {
                                foreach ($order_messages as $message) {
                                    $instr_liv_cleaned  = str_replace(array("\r\n", "\n", "\r", "\t"), ' ', html_entity_decode($message['message'], ENT_QUOTES));
                                    break;
                                }
                            }

                            $service            = self::getService($order, Context::getContext()->language->id);
                            $relay_id='';
                            preg_match('/P\d{5}/i', $address_delivery->company, $matches, PREG_OFFSET_CAPTURE);
                            if ($matches) {
                                $relay_id=$matches[0][0];
                            }
                            $tel_dest           = Db::getInstance()->getValue('SELECT gsm_dest FROM '._DB_PREFIX_.'dpdfrance_shipping WHERE id_cart ="'.$order->id_cart.'"');
                            if ($tel_dest == '') {
                                $tel_dest = (($address_delivery->phone_mobile) ? $address_delivery->phone_mobile : (($address_invoice->phone_mobile) ? $address_invoice->phone_mobile : (($address_delivery->phone) ? $address_delivery->phone : (($address_invoice->phone) ? $address_invoice->phone : ''))));
                            }
                            $mobile = self::formatGSM($tel_dest, $code_pays_dest);
                            $poids_all = Tools::getValue('parcelweight');
                            if (Tools::strtolower(Configuration::get('PS_WEIGHT_UNIT', null, null, (int) $order->id_shop))=='kg') {
                                $poids=(int)($poids_all[$order->id]*100);
                            }
                            if (Tools::strtolower(Configuration::get('PS_WEIGHT_UNIT', null, null, (int) $order->id_shop))=='g') {
                                $poids=(int)($poids_all[$order->id]*0.1);
                            }
                            $retour_option=(int)Configuration::get('DPDFRANCE_RETOUR_OPTION', null, null, (int)$order->id_shop); /* 2: Inverse, 3: Sur demande, 4: Préparée */
                            switch ($service) {
                                case 'PRE':
                                    $compte_chargeur = Configuration::get('DPDFRANCE_PREDICT_SHIPPER_CODE', null, null, (int)$order->id_shop);
                                    break;
                                case 'REL':
                                    $compte_chargeur = Configuration::get('DPDFRANCE_RELAIS_SHIPPER_CODE', null, null, (int)$order->id_shop);
                                    break;
                                default:
                                    $compte_chargeur = Configuration::get('DPDFRANCE_CLASSIC_SHIPPER_CODE', null, null, (int)$order->id_shop);
                                    break;
                            }

                            // DPD unified interface file structure
                            $record->add($internalref, 0, 35);                                                          //  Référence client N°1
                            $record->add(str_pad((int)$poids, 8, '0', STR_PAD_LEFT), 37, 8);                            //  Poids du colis sur 8 caractères
                            if ($service == 'REL') {
                                $record->add($address_delivery->lastname, 60, 35);                                      //  Nom du destinataire
                                $record->add($address_delivery->firstname, 95, 35);                                     //  Prénom du destinataire
                            } else {
                                if ($address_delivery->company) {
                                    $record->add($address_delivery->company, 60, 35);                                       //  Nom société
                                    $record->add($address_delivery->lastname.' '.$address_delivery->firstname, 95, 35);     //  Nom et prénom du destinataire
                                } else {
                                    $record->add($address_delivery->lastname.' '.$address_delivery->firstname, 60, 35);     //  Nom et prénom du destinataire
                                }
                            }
                            $record->add($address_delivery->address2, 130, 140);                                        //  Complément d’adresse 2 a 5
                            $record->add($address_delivery->postcode, 270, 10);                                         //  Code postal
                            $record->add($address_delivery->city, 280, 35);                                             //  Ville
                            $record->add($address_delivery->address1, 325, 35);                                         //  Rue
                            $record->add('', 360, 10);                                                                  //  Filler
                            $record->add($code_pays_dest, 370, 3);                                                      //  Code Pays destinataire
                            $record->add($tel_dest, 373, 30);                                                           //  Téléphone
                            $record->add($nom_exp, 418, 35);                                                            //  Nom expéditeur
                            $record->add($address2_exp, 453, 35);                                                       //  Complément d’adresse 1
                            $record->add($cp_exp, 628, 10);                                                             //  Code postal
                            $record->add($ville_exp, 638, 35);                                                          //  Ville
                            $record->add($address_exp, 683, 35);                                                        //  Rue
                            $record->add($code_pays_exp, 728, 3);                                                       //  Code Pays
                            $record->add($tel_exp, 731, 30);                                                            //  Tél.
                            $record->add($instr_liv_cleaned, 761, 140);                                                 //  Instructions de livraison
                            $record->add(date('d/m/Y'), 901, 10);                                                       //  Date d'expédition théorique
                            $record->add(str_pad($compte_chargeur, 8, '0', STR_PAD_LEFT), 911, 8);                      //  N° de compte chargeur DPD
                            $record->add($order->id, 919, 35);                                                          //  Code à barres
                            $record->add($order->id, 954, 35);                                                          //  N° de commande - Id Order Prestashop
                            if (Tools::getIsset('advalorem') && in_array($order->id, Tools::getValue('advalorem'))) {
                                $record->add(str_pad(number_format($order->total_paid, 2, '.', ''), 9, '0', STR_PAD_LEFT), 1018, 9); // Montant valeur colis
                            }
                            $record->add($order->id, 1035, 35);                                                         //  Référence client N°2 - Id Order Prestashop
                            $record->add($email_exp, 1116, 80);                                                         //  E-mail expéditeur
                            $record->add($gsm_exp, 1196, 35);                                                           //  GSM expéditeur
                            $record->add($customer->email, 1231, 80);                                                   //  E-mail destinataire
                            $record->add($mobile, 1311, 35);                                                            //  GSM destinataire
                            if ($service == 'REL') {
                                $record->add($relay_id, 1442, 8);                                                       //  Identifiant relais Pickup
                            }
                            if ($service == 'PRE') {
                                $record->add('+', 1568, 1);                                                             //  Flag Predict
                            }
                            $record->add($address_delivery->lastname, 1569, 35);                                        //  Nom de famille du destinataire
                            if (Tools::getIsset('retour') && in_array($order->id, Tools::getValue('retour')) && $retour_option != 0) {
                                $record->add($retour_option, 1834, 1);                                                  //  Flag Retour
                            }
                            $record->addLine();
                        }
                        $record->download();
                    } else {
                        $msg = '<div class="warnmsg">'.$this->l('No orders to export.').'</div>';
                    }
                } else {
                    $msg = '<div class="warnmsg">'.$this->l('No orders to export.').'</div>';
                }
            } else {
                $msg = '<div class="warnmsg">'.$this->l('No order selected.').'</div>';
            }
        }

        // Display section
        // Error message if shipper info is missing
        if ((Configuration::get('DPDFRANCE_PARAM') == 0)) {
            echo '<div class="warnmsg">'.$this->l('Warning! Your DPD Depot code and contract number are missing. You must configure the DPD module in order to use the export and tracking features.').'</div>';
            exit;
        }
        // Calls function to get orders
        $order_info = array();
        $statuses_array = array();
        $statuses = OrderState::getOrderStates((int)Context::getContext()->language->id);

        foreach ($statuses as $status) {
            $statuses_array[$status['id_order_state']] = $status['name'];
        }
        $fieldlist = array('O.`id_order`', 'O.`id_cart`', 'AD.`lastname`', 'AD.`firstname`', 'AD.`postcode`', 'AD.`city`', 'CL.`iso_code`', 'C.`email`', 'CA.`name`');

        $current_shop = (int)Tools::substr(Context::getContext()->cookie->shopContext, 2);
        $orders = self::getAllOrders($current_shop);
        $liste_expeditions = 'O.id_order IN ('.implode(',', $orders).')';

        $predict_carrier_log = $classic_carrier_log = $relais_carrier_log = $predict_carrier_sql = $classic_carrier_sql = $relais_carrier_sql = '';

        if (Configuration::get('DPDFRANCE_MARKETPLACE_MODE')) {
            $europe_carrier_sql = 'CA.name LIKE \'%%\'';
        } else {
            $europe_carrier_sql = 'CA.name LIKE \'%DPD%\'';
        }

        if ($current_shop == 0 && Shop::isFeatureActive()) {
            $predict_carrier_log=Configuration::get('DPDFRANCE_PREDICT_CARRIER_ID', null, null, null).','.implode(',', array_map('intval', explode('|', Tools::substr(Configuration::get('DPDFRANCE_PREDICT_CARRIER_LOG', null, null, null), 1))));
            $classic_carrier_log=Configuration::get('DPDFRANCE_CLASSIC_CARRIER_ID', null, null, null).','.implode(',', array_map('intval', explode('|', Tools::substr(Configuration::get('DPDFRANCE_CLASSIC_CARRIER_LOG', null, null, null), 1))));
            $relais_carrier_log=Configuration::get('DPDFRANCE_RELAIS_CARRIER_ID', null, null, null).','.implode(',', array_map('intval', explode('|', Tools::substr(Configuration::get('DPDFRANCE_RELAIS_CARRIER_LOG', null, null, null), 1))));

            foreach (Shop::getShops(true) as $shop) {
                if (Configuration::get('DPDFRANCE_PREDICT_CARRIER_ID', null, null, $shop['id_shop'])) {
                    $predict_carrier_log.=Configuration::get('DPDFRANCE_PREDICT_CARRIER_ID', null, null, $shop['id_shop']).','.implode(',', array_map('intval', explode('|', Tools::substr(Configuration::get('DPDFRANCE_PREDICT_CARRIER_LOG', null, null, $shop['id_shop']), 1))));
                    $predict_carrier_sql = 'CA.id_carrier IN ('.$predict_carrier_log.') OR ';
                }
                if (Configuration::get('DPDFRANCE_CLASSIC_CARRIER_ID', null, null, $shop['id_shop'])) {
                    $classic_carrier_log.=Configuration::get('DPDFRANCE_CLASSIC_CARRIER_ID', null, null, $shop['id_shop']).','.implode(',', array_map('intval', explode('|', Tools::substr(Configuration::get('DPDFRANCE_CLASSIC_CARRIER_LOG', null, null, $shop['id_shop']), 1))));
                    $classic_carrier_sql = 'CA.id_carrier IN ('.$classic_carrier_log.') OR ';
                }
                if (Configuration::get('DPDFRANCE_RELAIS_CARRIER_ID', null, null, $shop['id_shop'])) {
                    $relais_carrier_log.=Configuration::get('DPDFRANCE_RELAIS_CARRIER_ID', null, null, $shop['id_shop']).','.implode(',', array_map('intval', explode('|', Tools::substr(Configuration::get('DPDFRANCE_RELAIS_CARRIER_LOG', null, null, $shop['id_shop']), 1))));
                    $relais_carrier_sql = 'CA.id_carrier IN ('.$relais_carrier_log.') OR ';
                }
            }
        } else {
            if (Configuration::get('DPDFRANCE_PREDICT_CARRIER_ID', null, null, $current_shop)) {
                $predict_carrier_log=Configuration::get('DPDFRANCE_PREDICT_CARRIER_ID', null, null, $current_shop).','.implode(',', array_map('intval', explode('|', Tools::substr(Configuration::get('DPDFRANCE_PREDICT_CARRIER_LOG', null, null, $current_shop), 1))));
                $predict_carrier_sql = 'CA.id_carrier IN ('.$predict_carrier_log.') OR ';
            }
            if (Configuration::get('DPDFRANCE_CLASSIC_CARRIER_ID', null, null, $current_shop)) {
                $classic_carrier_log=Configuration::get('DPDFRANCE_CLASSIC_CARRIER_ID', null, null, $current_shop).','.implode(',', array_map('intval', explode('|', Tools::substr(Configuration::get('DPDFRANCE_CLASSIC_CARRIER_LOG', null, null, $current_shop), 1))));
                $classic_carrier_sql = 'CA.id_carrier IN ('.$classic_carrier_log.') OR ';
            }
            if (Configuration::get('DPDFRANCE_RELAIS_CARRIER_ID', null, null, $current_shop)) {
                $relais_carrier_log=Configuration::get('DPDFRANCE_RELAIS_CARRIER_ID', null, null, $current_shop).','.implode(',', array_map('intval', explode('|', Tools::substr(Configuration::get('DPDFRANCE_RELAIS_CARRIER_LOG', null, null, $current_shop), 1))));
                $relais_carrier_sql = 'CA.id_carrier IN ('.$relais_carrier_log.') OR ';
            }
        }

        if (!empty($orders)) {
            $sql = 'SELECT  '.implode(', ', $fieldlist).'
                    FROM    '._DB_PREFIX_.'orders AS O, 
                            '._DB_PREFIX_.'carrier AS CA, 
                            '._DB_PREFIX_.'customer AS C, 
                            '._DB_PREFIX_.'address AS AD, 
                            '._DB_PREFIX_.'country AS CL
                    WHERE   O.id_address_delivery=AD.id_address AND
                            C.id_customer=O.id_customer AND 
                            CL.id_country=AD.id_country AND 
                            CA.id_carrier=O.id_carrier AND 
                            ('.$predict_carrier_sql.$classic_carrier_sql.$relais_carrier_sql.$europe_carrier_sql.') AND
                            ('.$liste_expeditions.')
                    ORDER BY id_order DESC';

            $orderlist = Db::getInstance()->ExecuteS($sql);

            if (!empty($orderlist)) {
                foreach ($orderlist as $order_var) {
                    $order = new Order($order_var['id_order']);
                    $address_delivery = new Address($order->id_address_delivery, (int)Context::getContext()->language->id);
                    $current_state_id = $order->current_state;
                    $current_state_name = $statuses_array[$order->current_state];
                    $internalref = $order->reference;

                    switch ($current_state_id) {
                        default:
                            $dernierstatutcolis = '';
                            break;
                        case Configuration::get('DPDFRANCE_ETAPE_LIVRE', null, null, (int)$order->id_shop):
                            $dernierstatutcolis = '<img src="../modules/dpdfrance/views/img/admin/tracking.png" title="Trace du colis"/>';
                            break;
                        case Configuration::get('DPDFRANCE_ETAPE_EXPEDIEE', null, null, (int)$order->id_shop):
                            $dernierstatutcolis = '<img src="../modules/dpdfrance/views/img/admin/tracking.png" title="Trace du colis"/>';
                    }
                    $weight = number_format($order->getTotalWeight(), 2, '.', '.');
                    $amount = number_format($order->total_paid, 2, '.', '.').' €';
                    $service=self::getService($order, Context::getContext()->language->id);
                    $code_pays_dest = self::getIsoCodebyIdCountry((int)$address_delivery->id_country);

                    switch ($service) {
                        case 'PRE':
                            if ($code_pays_dest !== 'F') {
                                $type = 'Predict Export<img src="../modules/dpdfrance/views/img/admin/service_predict.png" title="Predict Export"/>';
                            } else {
                                $type = 'Predict<img src="../modules/dpdfrance/views/img/admin/service_predict.png" title="Predict"/>';
                            }
                            $compte_chargeur = Configuration::get('DPDFRANCE_PREDICT_SHIPPER_CODE', null, null, (int)$order->id_shop);
                            $depot_code = Configuration::get('DPDFRANCE_PREDICT_DEPOT_CODE', null, null, (int)$order->id_shop);
                            $address = '<a class="popup" href="http://maps.google.com/maps?f=q&hl=fr&geocode=&q='.str_replace(' ', '+', $address_delivery->address1).','.str_replace(' ', '+', $address_delivery->postcode).'+'.str_replace(' ', '+', $address_delivery->city).'&output=embed" target="_blank">'.($address_delivery->company ? $address_delivery->company.'<br/>' : '').$address_delivery->address1.'<br/>'.$address_delivery->postcode.' '.$address_delivery->city.'</a>';
                            break;
                        case 'REL':
                            $type = 'Relais<img src="../modules/dpdfrance/views/img/admin/service_relais.png" title="Relais"/>';
                            $compte_chargeur = Configuration::get('DPDFRANCE_RELAIS_SHIPPER_CODE', null, null, (int)$order->id_shop);
                            $depot_code = Configuration::get('DPDFRANCE_RELAIS_DEPOT_CODE', null, null, (int)$order->id_shop);
                            $relay_id='';
                            preg_match('/P\d{5}/i', $address_delivery->company, $matches, PREG_OFFSET_CAPTURE);
                            if ($matches) {
                                $relay_id=$matches[0][0];
                            }
                            $address = '<a class="popup" href="http://www.dpd.fr/dpdrelais/id_'.$relay_id.'" target="_blank">'.$address_delivery->company.'<br/>'.$address_delivery->postcode.' '.$address_delivery->city.'</a>';
                            break;
                        default:
                            if ($code_pays_dest !== 'F') {
                                $type = 'Classic Export<img src="../modules/dpdfrance/views/img/admin/service_world.png" title="Classic Export"/>';
                            } else {
                                $type = 'Classic<img src="../modules/dpdfrance/views/img/admin/service_dom.png" title="Classic"/>';
                            }
                            $compte_chargeur = Configuration::get('DPDFRANCE_CLASSIC_SHIPPER_CODE', null, null, (int)$order->id_shop);
                            $depot_code = Configuration::get('DPDFRANCE_CLASSIC_DEPOT_CODE', null, null, (int)$order->id_shop);
                            $address = '<a class="popup" href="http://maps.google.com/maps?f=q&hl=fr&geocode=&q='.str_replace(' ', '+', $address_delivery->address1).','.str_replace(' ', '+', $address_delivery->postcode).'+'.str_replace(' ', '+', $address_delivery->city).'&output=embed" target="_blank">'.($address_delivery->company ? $address_delivery->company.'<br/>' : '').$address_delivery->address1.'<br/>'.$address_delivery->postcode.' '.$address_delivery->city.'</a>';
                            break;
                    }

                    $order_info[] = array(
                        'checked'               => ($current_state_id == Configuration::get('DPDFRANCE_ETAPE_EXPEDITION', null, null, (int)$order->id_shop) ? 'checked="checked"' : ''),
                        'id'                    => $order->id,
                        'reference'             => $internalref,
                        'date'                  => date('d/m/Y H:i:s', strtotime($order->date_add)),
                        'nom'                   => $address_delivery->firstname.' '.$address_delivery->lastname,
                        'type'                  => $type,
                        'address'               => $address,
                        'poids'                 => $weight,
                        'weightunit'            => Configuration::get('PS_WEIGHT_UNIT', null, null, (int)$order->id_shop),
                        'prix'                  => $amount,
                        'advalorem_checked'     => (Configuration::get('DPDFRANCE_AD_VALOREM', null, null, (int)$order->id_shop) == 1 ? 'checked="checked"' : ''),
                        'retour_checked'        => (Configuration::get('DPDFRANCE_RETOUR_OPTION', null, null, (int)$order->id_shop) != 0 ? 'checked="checked"' : ''),
                        'statut'                => $current_state_name,
                        'depot_code'            => $depot_code,
                        'shipper_code'          => $compte_chargeur,
                        'dernier_statut_colis'  => $dernierstatutcolis,
                    );
                }
            } else {
                $order_info['error'] = true;
            }
        } else {
            $order_info['error'] = true;
        }

        // Assign smarty variables and fetches template
        Context::getContext()->smarty->assign(array(
            'msg'           => $msg,
            'stream'        => $stream,
            'token'         => $this->token,
            'order_info'    => $order_info,
            'dpdfrance_retour_option' => (int)Configuration::get('DPDFRANCE_RETOUR_OPTION', null, null, (int)Context::getContext()->shop->id),
        ));

        return $helper->generateForm($this->fields_form);
    }

    protected function buildHelper()
    {
        $helper = new HelperForm();

        $helper->module = $this->module;
        $helper->override_folder = 'dpdfrance/';
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('Admin'.$this->name);
        $helper->languages = $this->_languages;
        $helper->currentIndex = $this->context->link->getAdminLink('Admin'.$this->name);
        $helper->default_form_language = $this->default_form_language;
        $helper->allow_employee_form_lang = $this->allow_employee_form_lang;
        $helper->toolbar_scroll = true;
        $helper->toolbar_btn = $this->initToolbar();
        $helper->background_color = 'red';

        return $helper;
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Orders');
        $this->toolbar_title[] = $this->l('DPD deliveries management');
    }

    public function setMedia($isNewTheme = false)
    {
        $this->addJquery();
        $this->addJS(_PS_MODULE_DIR_.'/dpdfrance/views/js/admin/jquery/plugins/fancybox/jquery.fancybox.js');
        $this->addJS(_PS_MODULE_DIR_.'/dpdfrance/views/js/admin/jquery/plugins/marquee/jquery.marquee.min.js');
        $this->addCSS(_PS_MODULE_DIR_.'/dpdfrance/views/js/admin/jquery/plugins/fancybox/jquery.fancybox.css');
        $this->addCSS(_PS_MODULE_DIR_.'/dpdfrance/views/css/admin/AdminDPDFrance.css');
        return parent::setMedia();
    }
}
