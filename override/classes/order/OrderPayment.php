<?php
/**
* This file will override class OrderPaymentCore. Do not modify this file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2017 Globo ., Jsc
* @license   please read license in file license.txt
* @link      http://www.globosoftware.net
*/
class OrderPayment extends OrderPaymentCore
{
    /*
    * module: gwadvancedinvoice
    * date: 2019-04-03 10:59:25
    * version: 1.2.6
    */
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        self::$definition['fields']['order_reference']['size'] = 32;
        parent::__construct($id, $id_lang, $id_shop);
    }
}
