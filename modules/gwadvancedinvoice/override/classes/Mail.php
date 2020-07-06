<?php
/**
* This file will override class MailCore. Do not modify this file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2017 Globo ., Jsc
* @license   please read license in file license.txt
* @link      http://www.globosoftware.net
*/

class Mail extends MailCore
{
    public static function Send($id_lang, $template, $subject, $template_vars, $to,
        $to_name = null, $from = null, $from_name = null, $file_attachment = null, $mode_smtp = null,
        $template_path = _PS_MAIL_DIR_, $die = false, $id_shop = null, $bcc = null, $reply_to = null,$reply_to_name = null)
    {
        if(Module::isInstalled('gwadvancedinvoice') && Module::isEnabled('gwadvancedinvoice')){
            if (!empty($file_attachment)) {
                if (isset($file_attachment['invoice']) && isset($file_attachment['invoice']['name'])) {
                    $invoiceObj = Module::getInstanceByName('gwadvancedinvoice');
                    $id_order_invoice = (int)Tools::substr($file_attachment['invoice']['name'], Tools::strlen($file_attachment['invoice']['name'])-10,6);
                    if($id_order_invoice > 0){
                        $OrderInvoiceObj =  new OrderInvoice((int)$id_order_invoice);
                        if(Validate::isLoadedObject($OrderInvoiceObj)){
                            $file_attachment['invoice']['name'] = $invoiceObj->formatNumber('I',$OrderInvoiceObj->number,$OrderInvoiceObj). '.pdf';
                            if (isset($file_attachment['delivery']) && isset($file_attachment['delivery']['name'])) {
                                $file_attachment['delivery']['name'] = $invoiceObj->formatNumber('D',$OrderInvoiceObj->delivery_number,$OrderInvoiceObj). '.pdf';
                            }
                        }
                    }
                }
            }
        }
        return  parent::Send($id_lang, $template, $subject, $template_vars, $to,
        $to_name, $from, $from_name, $file_attachment, $mode_smtp,
        $template_path, $die, $id_shop, $bcc, $reply_to,$reply_to_name);
    }
}