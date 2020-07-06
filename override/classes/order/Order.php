<?php

class Order extends OrderCore{
    /*
    * module: gwadvancedinvoice
    * date: 2019-04-03 10:59:25
    * version: 1.2.6
    */
    public static function setLastInvoiceNumber($order_invoice_id, $id_shop)
    {
        if (!$order_invoice_id) {
            return false;
        }
        if(Module::isInstalled('gwadvancedinvoice') && Module::isEnabled('gwadvancedinvoice'))
        {
            $invoiceObj = Module::getInstanceByName('gwadvancedinvoice');
            $object = new OrderInvoice($order_invoice_id);
            $result = $invoiceObj->customizeNumber('I',$object);
            if(!$result) return parent::setLastInvoiceNumber($order_invoice_id, $id_shop);
            else return true;
        }
        else {
            return parent::setLastInvoiceNumber($order_invoice_id, $id_shop);
        }
    }
    /*
    * module: gwadvancedinvoice
    * date: 2019-04-03 10:59:25
    * version: 1.2.6
    */
    public function setDeliveryNumber($order_invoice_id, $id_shop)
    {
        if (!$order_invoice_id) {
            return false;
        }
        if(Module::isInstalled('gwadvancedinvoice') && Module::isEnabled('gwadvancedinvoice'))
        {
            $invoiceObj = Module::getInstanceByName('gwadvancedinvoice');
            $object = new OrderInvoice($order_invoice_id);
            $result = $invoiceObj->customizeNumber('D',$object);
            if(!$result) return parent::setDeliveryNumber($order_invoice_id, $id_shop);
            else return true;
        }
        else {
            return parent::setDeliveryNumber($order_invoice_id, $id_shop);
        }
    }
    /*
    * module: mpm_orderdelete
    * date: 2019-07-23 12:23:30
    * version: 1.0.0
    */
    public function delete()
    {
        if (parent::delete()) {
            return $this->deleteRelatedOrdersTables();
        }
        return false;
    }
    /*
    * module: mpm_orderdelete
    * date: 2019-07-23 12:23:30
    * version: 1.0.0
    */
    public function deleteRelatedOrdersTables()
    {
        $order_id = $this->id;
        $cart_id = $this->id_cart;
        $related_to_orders = array(
            'customer_thread' => array(
                'id' => Db::getInstance()->getValue("SELECT id_customer_thread 
                                                          FROM " . _DB_PREFIX_ . "customer_thread 
                                                          WHERE id_order=" . (int)$order_id),
                'class_name' => 'CustomerThread',
            ),
            'order_carrier' => array(
                'id' => Db::getInstance()->getValue("SELECT id_order_carrier 
                                                          FROM " . _DB_PREFIX_ . "order_carrier 
                                                          WHERE id_order=" . (int)$order_id),
                'class_name' => 'OrderCarrier',
            ),
            'order_detail' => array(
                'id' => Db::getInstance()->executeS("SELECT id_order_detail 
                                                          FROM " . _DB_PREFIX_ . "order_detail 
                                                          WHERE id_order=" . (int)$order_id),
                'class_name' => 'OrderDetail',
            ),
            'order_history' => array(
                'id' => Db::getInstance()->executeS("SELECT id_order_history 
                                                          FROM " . _DB_PREFIX_ . "order_history 
                                                          WHERE id_order=" . (int)$order_id),
                'class_name' => 'OrderHistory',
            ),
            'order_invoice' => array(
                'id' => Db::getInstance()->getValue("SELECT id_order_invoice 
                                                          FROM " . _DB_PREFIX_ . "order_invoice 
                                                          WHERE id_order=" . (int)$order_id),
                'class_name' => 'OrderInvoice',
            ),
            'order_return' => array(
                'id' => Db::getInstance()->getValue("SELECT id_order_return 
                                                          FROM " . _DB_PREFIX_ . "order_return 
                                                          WHERE id_order=" . (int)$order_id),
                'class_name' => 'OrderReturn',
            ),
            'order_slip' => array(
                'id' => Db::getInstance()->getValue("SELECT id_order_slip 
                                                          FROM " . _DB_PREFIX_ . "order_slip 
                                                          WHERE id_order=" . (int)$order_id),
                'class_name' => 'OrderSlip',
            ),
            'stock_mvt' => array(
                'id' => Db::getInstance()->getValue("SELECT id_stock_mvt 
                                                          FROM " . _DB_PREFIX_ . "stock_mvt 
                                                          WHERE id_order=" . (int)$order_id),
                'class_name' => 'StockMvt',
            ),
            'cart' => array(
                'id' => $cart_id,
                'class_name' => 'Cart',
            ),
            'message' => array(
                'id' => Db::getInstance()->getValue("SELECT id_message 
                                                          FROM " . _DB_PREFIX_ . "message 
                                                          WHERE id_cart=" . (int)$cart_id),
                'class_name' => 'Message',
            )
        );
        foreach ($related_to_orders as $order_relative) {
            if (!empty($order_relative['id']) && class_exists($order_relative['class_name'])) {
                if ($order_relative['class_name'] === 'OrderHistory') {
                    foreach ($order_relative['id'] as $order_history) {
                        $obj = new OrderHistory($order_history['id_order_history']);
                        $obj->delete();
                    }
                } else if ($order_relative['class_name'] === 'OrderDetail') {
                    foreach ($order_relative['id'] as $order_detail) {
                        $obj = new OrderDetail($order_detail['id_order_detail']);
                        $obj->delete();
                    }
                } else {
                    $obj = new $order_relative['class_name']((int)$order_relative['id']);
                    $obj->delete();
                }
            }
        }
        return true;
    }
}