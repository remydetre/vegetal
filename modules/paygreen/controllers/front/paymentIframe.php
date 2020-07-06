<?php
/**
 * 2007-2016 PrestaShop
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Specific view for insite
 * @since 1.5.0
 */
class PaygreenPaymentIframeModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $cart = $this->context->cart;
        if (!$this->module->checkCurrency($cart)) {
            Tools::redirect('index.php?controller=order');
        }

        $minWidthIframe = empty($_REQUEST['minWidthIframe']) ? '400' : $_REQUEST['minWidthIframe'];
        $minHeightIframe = empty($_REQUEST['minHeightIframe']) ? '400' : $_REQUEST['minHeightIframe'];

        $url = $_REQUEST['url'];
        if (empty($url)) {
            Tools::redirect('index.php?controller=order&step=3&error=1');
        }
        $url .= '?display=insite';
        $this->context->smarty->assign(array(
            'url' => $url,
            'minWidthIframe' => $minWidthIframe,
            'minHeightIframe' => $minHeightIframe,
        ));

        return $this->setTemplate('payment_iframe.tpl');
    }

    /**
     * @return void
     */
    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS($this->module->getPathUri() . '/views/css/1.6/front.css', 'all');
    }
}
