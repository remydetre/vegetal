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

class PaygreenServicesButtonHandler extends PaygreenObject
{
    /**
     * @return mixed List of all buttons
     */
    public function getButtonsList()
    {
        if (Module::isInstalled(PAYGREEN_MODULE_NAME)) {
            return Db::getInstance()->executeS(
                "SELECT * FROM " . _DB_PREFIX_ . 'paygreen_buttons ORDER BY position ASC'
            );
        }

        return array();
    }

    /**
     * check all butons
     * call checkButon for all butons
     * @return string
     * @throws Exception
     */
    public function checkButtons()
    {
        /** @var Paygreen $moduleFacade */
        $moduleFacade = $this->getService('facade.module');

        $warning = '';
        $error_tmp = '';
        $nb_error = 0;

        try {
            $btnList = $this->getButtonsList();
        } catch (Exception $ex) {
            return $moduleFacade->l('Access to database fail');
        }

        foreach ($btnList as $btn) {
            if ($this->checkButton($btn) != '') {
                $nb_error++;
                $error_tmp = $this->checkButton($btn);
            }
        }

        if ($nb_error > 1) {
            $warning .= ' - ' . 'There are '.$nb_error.' errors of button\'s configuration';
        } else {
            $warning .= ($error_tmp == '') ? null : ' - ' . $error_tmp;
        }

        return $moduleFacade->l($warning);
    }

    /**
     * @param $btn array buton
     * @return string
     * @throws Exception
     */
    public function checkButton($btn)
    {
        /** @var Paygreen $moduleFacade */
        $moduleFacade = $this->getService('facade.module');

        $error = '';

        if (!isset($btn['executedAt'])) {
            return $error;
        }
        $executedAt = $btn['executedAt'];

        if (!isset($btn['paymentType'])) {
            return $error;
        }
        $type = $btn['paymentType'];

        if (!isset($btn['nbPayment'])) {
            return $error;
        }
        $nbPayment = $btn['nbPayment'];

        if (!isset($btn['reportPayment'])) {
            return $error;
        }
        $report = $btn['reportPayment'];


        if (!isset($btn['perCentPayment'])) {
            return $error;
        }
        $percent = $btn['perCentPayment'];

        if (!isset($btn['subOption'])) {
            return $error;
        }
        $subOption = $btn['subOption'];

        if (!isset($btn['reductionPayment'])) {
            return $error;
        }
        $reduction = $btn['reductionPayment'];

        if ($moduleFacade->vPresta > 1.6) {
            if ($btn['integration'] == Paygreen::BUTTON_IFRAME) {
                if ($moduleFacade->isConnected()) {
                    $shopInfo = $moduleFacade->infoAccount();

                    if (!empty($shopInfo->modules)) {
                        foreach ($shopInfo->modules as $module) {
                            if (isset($module->name) && $module->name == 'InSite' &&
                                isset($module->enable) && $module->enable != 1
                            ) {
                                $error .= $moduleFacade->l('iFrame payment is only available with the Premium Pack');
                            }
                        }
                    } else {
                        $error .= $moduleFacade->l('Insite Payment is only available with the Premium Pack');
                    }
                }
            }
        }

        if ($nbPayment > 1) {
            if ($executedAt == PaygreenServicesPaymentHandler::CASH_PAYMENT) {
                // Cash payment
                $error .= $moduleFacade->l('The payment cash must be only once');
            } elseif ($executedAt == PaygreenServicesPaymentHandler::DEL_PAYMENT) {
                // At the delivery
                $error .= $moduleFacade->l('The payment at delivery must be only once');
            }
        } else {
            if ($executedAt == PaygreenServicesPaymentHandler::SUB_PAYMENT) {
                // Subscription payment
                $error .= $moduleFacade->l('The subscription payment must have more than one payment due');
            } elseif ($executedAt == PaygreenServicesPaymentHandler::REC_PAYMENT) {
                // Recurring payment
                $error .= $moduleFacade->l('The recurring payment must have more than one payment due');
            }
        }

        if ($report > 0) {
            // Cash payment
            if ($executedAt == PaygreenServicesPaymentHandler::CASH_PAYMENT) {
                $error .= $moduleFacade->l('The cash payment can\'t have a report payment');
            } elseif ($executedAt == PaygreenServicesPaymentHandler::DEL_PAYMENT) {
                $error .= $moduleFacade->l('The payment at the delivery can\'t have a report payment');
            } elseif ($executedAt == PaygreenServicesPaymentHandler::REC_PAYMENT) {
                $error .= $moduleFacade->l('The recurring payment can\'t have a report payment');
            }
        }

        if ($percent != 0) {
            if ($executedAt == PaygreenServicesPaymentHandler::REC_PAYMENT) {
                if (!($percent > 0 && $percent < 100)) {
                    $error .= $moduleFacade->l('The percent must be  between 1 and 99');
                }
            } else {
                $error .= $moduleFacade->l('This option is only for recurring payment');
            }
        }

        if ($subOption == 1 && $executedAt != PaygreenServicesPaymentHandler::SUB_PAYMENT) {
            $error .= $moduleFacade->l('The option is only for subscription payment');
        }

        if ($reduction != 'none') {
            if (!$moduleFacade->checkPromoCode($reduction)) {
                $error .= $moduleFacade->l('The promo code is available');
            }
        }

        if (!in_array($type, $this->getService('manager.payment_type')->getCodes())) {
            $error .= $moduleFacade->l('The payment type is not available');
        }


        return $error;
    }
}
