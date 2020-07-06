<?php
/**
 * 2014 - 2020 Watt Is It
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons BY-ND 4.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://creativecommons.org/licenses/by-nd/4.0/fr/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@paygreen.fr so we can send you a copy immediately.
 *
 * @author    PayGreen <contact@paygreen.fr>
 * @copyright 2014 - 2020 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   3.0.1
 */

require_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', '..', 'config', 'config.inc.php'));
require_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', '..', 'init.php'));
require_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), 'paygreen.php'));

$o_paygreen = new Paygreen();

/** @var PGClientServicesApiFacade $apiFacade */
$apiFacade = PGFrameworkContainer::getInstance()
    ->get('paygreen.facade')
    ->getApiFacade();

if (preg_match("#^[0-9a-z]{34}$#", Tools::getValue('paiementToken'))) {
    $datas = array(
        'paymentToken' => Tools::getValue('paiementToken'),
    );
    if (Tools::getValue('getInfo') == true) {
        $result = $apiFacade->getTransactionInfo($datas);
        echo $result->success;
    } elseif (Tools::getValue('getRounding') == true) {
        $result = $apiFacade->getRoundingInfo($datas);
        echo json_encode($result);
    } elseif (Tools::getValue('cancelRounding') == true) {
        $result = $apiFacade->refundRounding($datas);
        echo json_encode($result);
    } elseif (Tools::getValue('associationId') && Tools::getValue('amount')) {
        if (Tools::getValue('associationId') > 0 && Tools::getValue('amount') > 0) {
            $datas['content'] = array(
                "associationId" => Tools::getValue('associationId'),
                "type"          => "rounding",
                "amount"        => Tools::getValue('amount') * 100
            );
            $result = $apiFacade->validateRounding($datas);
            echo json_encode($result);
        }
    } else {
        echo '{"success":false,"message":"requestApi"}';
    }
} else {
    echo '{"success":false, "message": "paiementToken"}';
}
