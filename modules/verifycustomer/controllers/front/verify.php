<?php
/**
 * Module to verify new customers and hide prices for not authorized customers.
 * 
 * @author    Singleton software <info@singleton-software.com>
 * @copyright 2017 Singleton software
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class VerifyCustomerVerifyModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $name = 'verifycustomer';

	public function init(){
		parent::init();
    }
	public function setMedia()
    {
        parent::setMedia();
    }
	public function initContent()
	{
		parent::initContent();
		$verifyCustomerConfigData = Tools::jsonDecode(Configuration::get($this->name), true);
		$psVersion17 = version_compare(_PS_VERSION_, '1.7', '>');
		$this->context->smarty->assign(
			array(
				'psVersion17' => $psVersion17,
				'willBeNotificated' => ((int)$verifyCustomerConfigData['approve_customer'] == 1 && (int)$verifyCustomerConfigData['send_mail_after_approve_to_customer'] == 1) 
			)
		);
		if (!$psVersion17) {
			$this->setTemplate('ps16/verify.tpl');
		} else {
			$this->setTemplate('module:verifycustomer/views/templates/front/ps17/verify.tpl');
		}
	}
}
