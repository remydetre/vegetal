<?php
/**
 * 2014 - 2019 Watt Is It
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
 * @copyright 2014 - 2019 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   2.7.6
 */

/**
 * Class PGModuleServicesManagersTransactionManager
 *
 * @method PGModuleServicesRepositoriesTransactionRepository getRepository()
 */
class PGModuleServicesManagersTransactionManager extends PGDomainServicesManagersTransactionManager
{
    /**
     * Return state of transaction by the id order
     * @param $id_order
     * @return false|string state or false if not exists
     */
    private function getPIDByOrder($id_order)
    {
        return Db::getInstance()->getValue(
            'SELECT pid FROM ' . _DB_PREFIX_ . 'paygreen_transactions
            WHERE id_order=' . ((int)$id_order) . ';'
        );
    }

    /**
     * Validate Shipped Payment
     * @param $id_order
     * @return bool
     * @throws Exception
     * @todo revoir la vérification de la réponse Paygreen
     */
    public function paygreenShippedTransaction($id_order)
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGClientServicesApiFacade $apiFacade */
        $apiFacade = $this->getService('paygreen.facade')->getApiFacade();

        /** @var PGDomainServicesOrderStateMapper $orderStateMapper */
        $orderStateMapper = PGFrameworkContainer::getInstance()->get('mapper.order_state');

        $pid = $this->getPIDByOrder($id_order);
        if (empty($pid)) {
            $logger->error("PID not found for order : '$id_order'.");
            return false;
        }

        $apiResult = $apiFacade->validDeliveryPayment($pid);

        if (!$apiResult) {
            $logger->error("Transacton with PID '$pid' is not shipped.");
            return false;
        }

        /** @var OrderHistoryCore $history */
        $history = new OrderHistory();
        $history->id_order = (int) $id_order;

        if ($apiResult->isSuccess()) {
            $localStateValidate = $orderStateMapper->getLocalOrderState('VALIDATE');

            $history->changeIdOrderState($localStateValidate['state'], (int) $id_order);
            $history->add();
        }

//        if ($apiResult->isSuccess()) {
//            if (
//                $apiResult->data->result->status == PGDomainServicesPaygreenFacade::STATUS_SUCCESSED
//                || $apiResult->data->result->status == PGDomainServicesPaygreenFacade::STATUS_WAITING_EXEC
//            ) {
//                $localStateValidate = $orderStateMapper->getLocalOrderState('VALIDATE');
//
//                $history->changeIdOrderState($localStateValidate['state'], (int) $id_order);
//                $history->add();
//            } else {
//                $error = $localModule->l('The order could not be sent. Please try again.');
//                $localModule->getContext()->controller->errors[] = $error;
//
//                $history->changeIdOrderState($settings->get('PS_OS_CANCELED'), (int) $id_order);
//                $history->add();
//
//                return false;
//            }
//        }

        return true;
    }
}
