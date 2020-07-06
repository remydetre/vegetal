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

/**
 * Class PaygreenProductManager
 *
 * @method PaygreenServicesRepositoriesOrderStateRepository getRepository()
 */
class PaygreenServicesManagersOrderStateManager extends PaygreenFoundationsAbstractManager
{
    public function create($frName, $enName, $color, $filename)
    {
        /** @var OrderStateCore $orderState */
        $orderStatePrimary = $this->getRepository()->create($frName, $enName, $color, $filename);

        if (file_exists(_PS_MODULE_DIR_ . PAYGREEN_MODULE_NAME . '/views/img/rsx/' . $filename)) {
            @copy(
                _PS_MODULE_DIR_ . PAYGREEN_MODULE_NAME . '/views/img/rsx/' . $filename,
                _PS_IMG_DIR_ . 'os/' . $orderStatePrimary . '.gif'
            );
        }

        return $this->getRepository()->findByPrimary($orderStatePrimary);
    }

    /**
     * @return boolean
     */
    public function installOrderStatuses()
    {
        /** @var PaygreenRuntimePrestashopServicesSettings $settings */
        $settings = $this->getService('settings');

        try {
            if (!$settings->get(PaygreenSettings::_CONFIG_ORDER_AUTH)) {
                $orderState = $this->create(
                    'Paiement autorisé PAYGREEN',
                    'PAYGREEN authorized payment',
                    '#337ab7',
                    'order_auth.gif'
                );

                $settings->set(PaygreenSettings::_CONFIG_ORDER_AUTH, $orderState->id);
            }

            if (!$settings->get(PaygreenSettings::_CONFIG_ORDER_TEST)) {
                $orderState = $this->create(
                    'TEST - Paiement accepté',
                    'TEST - Accepted payment',
                    '#D4EA62',
                    'order_test.gif'
                );

                $settings->set(PaygreenSettings::_CONFIG_ORDER_TEST, $orderState->id);
            }

            if (!$settings->get(PaygreenSettings::_CONFIG_ORDER_VERIFY)) {
                $orderState = $this->create(
                    'Paiement à vérifier',
                    'Payment to confirm',
                    '#FF3300',
                    'order_test.gif'
                );

                $settings->set(PaygreenSettings::_CONFIG_ORDER_VERIFY, $orderState->id);
            }

            if (!$settings->get(PaygreenSettings::_CONFIG_ORDER_WAIT)) {
                $orderState = $this->create(
                    'Dossier de paiement validé',
                    'Validated payment record',
                    '#337ab7',
                    'order_auth.gif'
                );

                $settings->set(PaygreenSettings::_CONFIG_ORDER_WAIT, $orderState->id);
            }
        } catch (Exception $exception) {
            $this->getService('logger')->error($exception->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param $from
     * @param $to
     * @return bool
     * @todo Créer plusieurs pseudo machines a états en fonction du mode de paiement
     */
    public function isAllowedTransition($from, $to)
    {
        /** @var PaygreenSettings $settings */
        $settings = $this->getService('settings');

        $from = (string) $from;
        $to = (string) $to;

        $allowedTransitions = array(
            array(
                'from' => (string) $settings->get(Paygreen::_CONFIG_ORDER_AUTH),
                'to' => (string) $settings->get('PS_OS_ERROR'),
            ),
            array(
                'from' => (string) $settings->get(Paygreen::_CONFIG_ORDER_AUTH),
                'to' => (string) $settings->get('PS_OS_PAYMENT'),
            ),
            array(
                'from' => (string) $settings->get(Paygreen::_CONFIG_ORDER_AUTH),
                'to' => (string) $settings->get(Paygreen::_CONFIG_ORDER_TEST),
            ),
            array(
                'from' => (string) $settings->get(Paygreen::_CONFIG_ORDER_AUTH),
                'to' => (string) $settings->get(Paygreen::_CONFIG_ORDER_VERIFY),
            ),
            array(
                'from' => (string) $settings->get(Paygreen::_CONFIG_ORDER_WAIT),
                'to' => (string) $settings->get('PS_OS_ERROR'),
            ),
            array(
                'from' => (string) $settings->get(Paygreen::_CONFIG_ORDER_WAIT),
                'to' => (string) $settings->get('PS_OS_PAYMENT'),
            ),
            array(
                'from' => (string) $settings->get(Paygreen::_CONFIG_ORDER_WAIT),
                'to' => (string) $settings->get(Paygreen::_CONFIG_ORDER_TEST),
            ),
            array(
                'from' => (string) $settings->get('PS_OS_PAYMENT'),
                'to' => (string) $settings->get('PS_OS_ERROR'),
            ),
            array(
                'from' => (string) $settings->get('PS_OS_ERROR'),
                'to' => (string) $settings->get('PS_OS_PAYMENT'),
            ),
            array(
                'from' => (string) $settings->get(Paygreen::_CONFIG_ORDER_VERIFY),
                'to' => (string) $settings->get('PS_OS_ERROR'),
            ),
            array(
                'from' => (string) $settings->get(Paygreen::_CONFIG_ORDER_TEST),
                'to' => (string) $settings->get('PS_OS_ERROR'),
            )
        );

        foreach ($allowedTransitions as $transition) {
            if (($transition['from'] === $from) && ($transition['to'] === $to)) {
                return true;
            }
        }
    }
}
