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

class PGModuleServicesControllersConfigAccountController extends PGFrameworkFoundationsAbstractController
{
    public function toggleAccountActivationAction(PGFrameworkComponentsIncomingRequest $request)
    {
        /** @var PGClientServicesApiFacade $apiFacade */
        $apiFacade = $this->getService('paygreen.facade')->getApiFacade();

        /** @var PGFrameworkComponentsResponsesChainQualifiedMessagesResponse $response */
        $response = $this->buildChainedResponse();

        $activate = (bool) $request->get('PS_PG_activate_account');

        /** @var PGClientEntitiesResponse $apiResponse */
        $apiResponse = $apiFacade->activateShop($activate);

        if ($apiResponse->isSuccess()) {
            $response->add($response::SUCCESS, 'account.actions.toggle.result.success');
        } else {
            $response->add($response::FAILURE, 'account.actions.toggle.result.failure');
        }

        return $response;
    }

    public function displayAccountHeaderAction()
    {
        /** @var PGModuleServicesHandlersMultiShopHandler $multiShopHandler */
        $multiShopHandler = $this->getService('handler.multi_shop');

        if ($multiShopHandler->isShopContext()) {
            $response = $this->buildAccountHeaderResponse();
        } else {
            $response = $multiShopHandler->buildOnlyShopLevelResponse(
                'Configuration du compte PayGreen',
                'user'
            );
        }

        return $response;
    }

    private function buildAccountHeaderResponse()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGModuleServicesSettings $settings */
        $settings = $this->getService('settings');

        /** @var Paygreen $localModule */
        $localModule = $this->getService('local.module');

        /** @var PGDomainServicesPaygreenFacade $paygreenFacade */
        $paygreenFacade = $this->getService('paygreen.facade');

        try {
            $response = new PGFrameworkComponentsResponsesTemplateResponse();

            $infoShop = '';
            $infoAccount = '';

            if ($paygreenFacade->isConnected()) {
                /** @var stdClass $infoShop */
                $infoShop = $paygreenFacade->getStatusShop();
                $infoAccount = $paygreenFacade->getAccountInfos();
            }

            // remove query pollution
            $query = $_GET;
            unset($query['controllerUri'], $query['controller']);

            $baseUrl = $localModule->getContext()->link->getAdminLink('AdminModules', false) . '&';
            $queryConnect = $queryDeconnect = $query;

            $queryDeconnect['deconnect'] = 'true';
            $queryConnect['connect'] = 'true';

            unset($queryConnect['deconnect']);
            unset($queryDeconnect['connect'], $queryDeconnect['code']);

            $urlBaseConnect = $baseUrl . http_build_query($queryConnect);
            $urlBaseDeconnect = $baseUrl . http_build_query($queryDeconnect);

            $response
                ->setTemplate('views/templates/admin/' . $localModule->vPresta, 'connectApi')
                ->addData('prestashop', $localModule->vPresta)
                ->addData('connected', $paygreenFacade->isConnected())
                ->addData('urlBase', $urlBaseConnect)
                ->addData('urlBaseDeconnect', $urlBaseDeconnect)
                ->addData('infoShop', $infoShop)
                ->addData('infoAccount', $infoAccount)
                ->addData('imgdir', $localModule->getImgDirectory('', true))
                ->addData('allowRefund', $settings->get(PGModuleServicesSettings::_CONFIG_PAYMENT_REFUND))
            ;

            if ($localModule->vPresta == 1.6) {
                $response->addResource('css', 'views/css/1.6/config-account.css');
            }
        } catch (PGDomainExceptionsPaygreenAccountException $exception) {
            $logger->error("Account error during account header building : " . $exception->getMessage(), $exception);

            $response = new PGFrameworkComponentsResponsesChainQualifiedMessagesResponse();

            $translation_key = 'account.errors.' . Tools::strtolower($exception->getCodeName());

            $response->add($response::FAILURE, $translation_key);
        } catch (Exception $exception) {
            $logger->error("Error during account header building : " . $exception->getMessage(), $exception);

            $response = new PGFrameworkComponentsResponsesChainQualifiedMessagesResponse();

            $response->add($response::FAILURE, 'account.backoffice.errors.header');
        }

        return $response;
    }
}
