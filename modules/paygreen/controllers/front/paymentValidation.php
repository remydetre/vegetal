<?php
/**
* 2007-2018 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PaygreenPaymentValidationModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $src = 'module:paygreen/views/templates/front/integrated-message-page.tpl'; // PS 1.7

        if ($this->module->vPresta < 1.7) {
            $src = '1.6/integrated-message-page.tpl'; // PS 1.6
        }

        $this->setTemplate($src);
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS($this->module->getPathUri() . 'views/js/1.7/jquery-1.12.3.min.js', 'all');
    }

    protected function getService($name)
    {
        return PaygreenContainer::getInstance()->get($name);
    }

    public function postProcess()
    {
        /** @var PaygreenServicesLogger $logger */
        $logger = $this->getService('logger');

        $pid = Tools::getValue('pid');

        $logger->info("Customer validation for PID : '$pid'.");

        /** @var PaygreenServicesLogger $logger */
        $logger = $this->getService('logger');

        $cart = $this->context->cart;

        if (empty($pid)) {
            $this->module->redirectToCheckoutPage();
        } elseif ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0) {
            $logger->error('Panier invalide');
            $this->module->redirectToCheckoutPage();
        } else {
            $this->validatePaymentProcess($pid);
        }
    }

    private function validatePaymentProcess($pid)
    {
        $task = new PaygreenTasksPaymentValidationTask($pid);

        $processor = $this->getService('processor.payment_validation');

        $processor->execute($task);

        switch ($task->getStatus()) {
            case $task::STATE_SUCCESS:
                $this->module->redirectToConfirmationPage($task->getOrder());
                break;

            case $task::STATE_ORDER_CANCELED:
                $this->paygreenRedirection(
                    'Votre commande a été annulée.',
                    $processor,
                    null,
                    array(
                        'link' => $this->context->link->getPageLink('order-detail', null, null, array(
                            'id_order' => $task->getOrder()->id
                        )),
                        'text' => "Voir les détails de votre commande.",
                        'reload' => true,
                    )
                );

                break;

            case $task::STATE_PID_LOCKED:
                Tools::redirect('history');
                break;

            case $task::STATE_PAYMENT_CANCELED:
                $this->module->redirectToCheckoutPage();
                break;

            case $task::STATE_PAYMENT_REFUSED:
                $this->paygreenRedirection(
                    'Votre moyen de paiement a été refusé.',
                    $processor,
                    null,
                    array(
                        'link' => $this->context->link->getPageLink('order'),
                        'text' => "Choisir un autre moyen de paiement.",
                        'reload' => true,
                    )
                );

                break;

            case $task::STATE_INCONSISTENT_CONTEXT:
            case $task::STATE_FATAL_ERROR:
            case $task::STATE_WORKFLOW_ERROR:
            case $task::STATE_PROVIDER_UNAVAILABLE:
                $this->paygreenRedirection(
                    'Mince, nous avons rencontré une erreur... :(',
                    $processor,
                    array('Impossible de confirmer votre paiement auprès de notre prestataire.'),
                    array(
                        'link' => __PS_BASE_URI__,
                        'text' => "Rejoindre la page d'accueil.",
                        'reload' => true,
                    )
                );

                break;

            default:
        }
    }

    protected function paygreenRedirection(
        $title,
        PaygreenServicesProcessorsPaymentValidationProcessor $processor,
        array $errors = null,
        array $url = null
    ) {
        $data = array(
            'title' => $title,
            'errors' => $errors,
            'url' => $url,
            'exceptions' => $processor->getExceptions(),
            'env' => PAYGREEN_ENV
        );

        $this->context->smarty->assign($data);
    }
}
