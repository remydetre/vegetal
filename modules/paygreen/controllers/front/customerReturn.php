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

class PaygreenCustomerReturnModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $src = 'module:paygreen/views/templates/front/1.7/integrated-message-page.tpl'; // PS 1.7

        if ($this->module->vPresta < 1.7) {
            $src = '1.6/integrated-message-page.tpl'; // PS 1.6
        }

        $this->setTemplate($src);
    }

    /**
     * @param string $name
     * @return object
     * @throws Exception
     */
    protected function getService($name)
    {
        return PGFrameworkContainer::getInstance()->get($name);
    }

    /**
     * @throws Exception
     */
    public function postProcess()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        $logger->debug("[CTRL::CustomerReturn]");

        $pid = Tools::getValue('pid');

        $logger->info("Customer validation for PID : '$pid'.");

        $task = new PGDomainTasksPaymentValidationTask($pid);

        $processor = $this->getService('processor.payment_validation');

        $processor->execute($task);

        switch ($task->getStatus()) {
            case $task::STATE_SUCCESS:
                $this->dispatchByOrderState($task->getOrder());
                break;

            case $task::STATE_PAYMENT_ABORTED:
            case $task::STATE_PID_NOT_FOUND:
                $this->module->redirectToCheckoutPage();
                break;

            case $task::STATE_PAYMENT_REFUSED:
                /** @var PGModuleServicesSettings $settings */
                $settings = $this->getService('settings');

                $this->paygreenRedirection(
                    $settings->get($settings::_CONFIG_PAIEMENT_REFUSED),
                    array(
                        'link' => $this->context->link->getPageLink('order', null, null, array('step' => 3)),
                        'text' => "Choisir un autre moyen de paiement.",
                        'reload' => true,
                    )
                );

                break;

            case $task::STATE_PID_LOCKED:
                Tools::redirect('history');
                break;

            case $task::STATE_INCONSISTENT_CONTEXT:
            case $task::STATE_FATAL_ERROR:
            case $task::STATE_WORKFLOW_ERROR:
            case $task::STATE_PAYGREEN_UNAVAILABLE:
                $this->paygreenRedirection(
                    'Mince, nous avons rencontré une erreur... :(',
                    array(
                        'link' => __PS_BASE_URI__,
                        'text' => "Rejoindre la page d'accueil.",
                        'reload' => true,
                    ),
                    $processor->getExceptions(),
                    array('Impossible de confirmer votre paiement auprès de notre prestataire.')
                );

                break;

            default:
        }
    }

    /**
     * @param string $title
     * @param array|null $url
     * @param array $exceptions
     * @param array|null $errors
     */
    protected function paygreenRedirection(
        $title,
        array $url = null,
        array $exceptions = array(),
        array $errors = null
    ) {
        $data = array(
            'title' => $title,
            'message' => null,
            'errors' => $errors,
            'url' => $url,
            'exceptions' => $exceptions,
            'env' => PAYGREEN_ENV
        );

        $this->context->smarty->assign($data);
    }

    /**
     * @param PGDomainInterfacesEntitiesOrderInterface $order
     */
    protected function dispatchByOrderState(PGDomainInterfacesEntitiesOrderInterface $order)
    {
        switch ($order->getState()) {
            case 'VALIDATE':
            case 'TEST':
            case 'VERIFY':
            case 'AUTH':
            case 'WAIT':
                $query = array(
                    'id_module' => $this->module->id,
                    'id_cart' => $order->getLocalEntity()->id_cart,
                    'id_order' => $order->id(),
                    'key' => $order->getLocalEntity()->secure_key,
                );

                Tools::redirectLink(__PS_BASE_URI__ . 'order-confirmation.php?' . http_build_query($query));
                break;

            case 'CANCEL':
                $this->paygreenRedirection(
                    'Votre commande a été annulée.',
                    array(
                        'link' => $this->context->link->getPageLink('order-detail', null, null, array(
                            'id_order' => $order->id()
                        )),
                        'text' => "Voir les détails de votre commande.",
                        'reload' => true,
                    )
                );

                break;

            case 'ERROR':
                /** @var PGModuleServicesSettings $settings */
                $settings = $this->getService('settings');

                $this->paygreenRedirection(
                    $settings->get($settings::_CONFIG_PAIEMENT_REFUSED),
                    array(
                        'link' => $this->context->link->getPageLink('order', null, null, array('step' => 3)),
                        'text' => "Choisir un autre moyen de paiement.",
                        'reload' => true,
                    )
                );

                break;

            case 'NEW':
                Tools::redirect('history');
                break;

            default:
                Tools::redirect('history');
        }
    }
}
