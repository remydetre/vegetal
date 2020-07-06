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

class PGModuleServicesControllersConfigModuleController extends PGFrameworkFoundationsAbstractController
{
    public function refreshTopPositionAction()
    {
        /** @var PGModuleServicesListenersInstallHooksListener $installHooksListener */
        $installHooksListener = $this->getService('listener.setup.hooks');

        /** @var PGFrameworkComponentsResponsesChainQualifiedMessagesResponse $response */
        $response = $this->buildChainedResponse();

        if ($installHooksListener->updateHookPositions()) {
            $response->add($response::SUCCESS, 'backoffice.actions.hooks.result.success');
        } else {
            $response->add($response::FAILURE, 'backoffice.actions.hooks.result.failure');
        }

        return $response;
    }

    public function saveModuleConfigurationAction(PGFrameworkComponentsIncomingRequest $request)
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGModuleServicesSettings $settings */
        $settings = $this->getService('settings');

        /** @var Paygreen $localModule */
        $localModule = $this->getService('local.module');

        /** @var PGDomainServicesPaygreenFacade $paygreenFacade */
        $paygreenFacade = $this->getService('paygreen.facade');

        /** @var PGFrameworkComponentsResponsesChainQualifiedMessagesResponse $response */
        $response = $this->buildChainedResponse();

        try {
            $form_values = $localModule->getConfig();

            $validators = $this->getFormValidator();

            foreach (array_keys($form_values) as $key) {
                $fieldValue = trim($request->get($key));

                if (isset($validators[$key])) {
                    foreach ($validators[$key] as $fn => $conf) {
                        if (!$this->{$fn . 'Validator'}($fieldValue, $conf['params'])) {
                            return $response->add($response::FAILURE, $conf['msg']);
                        }
                    }
                }

                $settings->set($key, $fieldValue);
            }

            $paygreenFacade->resetApiFacade();

            $response->add($response::SUCCESS, 'config.result.success');
        } catch (Exception $exception) {
            $logger->error("Error during config form processing : " . $exception->getMessage(), $exception);
            $response->add($response::SUCCESS, 'config.errors.processing_form');
        }

        return $response;
    }

    private function getFormValidator()
    {
        return array(
            Paygreen::_CONFIG_PRIVATE_KEY => array(
                'regexp' => array(
                    'params' => '^[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{12}$',
                    'msg' => 'config.errors.private_key_bad_format'
                ),
            ),
            Paygreen::_CONFIG_SHOP_TOKEN => array(
                'regexp' => array(
                    'params' => '^(PP|LC)?[a-f0-9]{32}$',
                    'msg' => 'config.errors.identifier_bad_format'
                ),
            ),
        );
    }

    private function regexpValidator($value, $args)
    {
        preg_match("/$args/i", $value, $matches);

        return (($matches !== null) && (count($matches) > 0));
    }

    public function displayConfigFormAction()
    {
        /** @var PGModuleServicesHandlersMultiShopHandler $multiShopHandler */
        $multiShopHandler = $this->getService('handler.multi_shop');

        if ($multiShopHandler->isShopContext()) {
            $response = $this->buildConfigFormResponse();
        } else {
            $response = $multiShopHandler->buildOnlyShopLevelResponse(
                'Configuration du système de paiement',
                'cogs'
            );
        }

        return $response;
    }

    public function buildConfigFormResponse()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        try {
            $response = new PGFrameworkComponentsResponsesHTMLResponse();

            $response->setContent($this->renderForm());
        } catch (Exception $exception) {
            $logger->error("Error during config form building : " . $exception->getMessage(), $exception);

            $response = new PGFrameworkComponentsResponsesChainQualifiedMessagesResponse();

            $response->add($response::FAILURE, 'config.errors.display_form');
        }

        return $response;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     * @return mixed
     * @throws Exception
     */
    private function renderForm()
    {
        /** @var PGModuleServicesSettings $settings */
        $settings = $this->getService('settings');

        /** @var Paygreen $localModule */
        $localModule = $this->getService('local.module');

        /** @var HelperFormCore $helper */
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $localModule->getTable();
        $helper->module = $localModule;
        $helper->default_form_language = $localModule->getContext()->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $localModule->getIdentifier();
        $helper->submit_action = 'submitPaygreenModule';
        $helper->currentIndex = $localModule->getContext()->link->getAdminLink('AdminModules', false)
            .'&configure='.PAYGREEN_MODULE_NAME
            .'&tab_module='.$localModule->getTable()
            .'&module_name='.PAYGREEN_MODULE_NAME;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $config = $localModule->getConfig();

        $helper->tpl_vars = array(
            'fields_value' => $config, /* Add values for your inputs */
            'languages' => $localModule->getContext()->controller->getLanguages(),
            'id_language' => $localModule->getContext()->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    private function getConfigForm()
    {
        /** @var PGFrameworkServicesHandlersTranslatorHandler $translator */
        $translator = $this->getService('handler.translator');

        $config_cancel_action = $this->createRadio(
            PGModuleServicesSettings::_CONFIG_REFUSE_ACTION,
            'config.fields.behavior_payment_refused.label',
            'config.fields.behavior_payment_refused.values.yes.label',
            'config.fields.behavior_payment_refused.values.yes.help',
            'config.fields.behavior_payment_refused.values.no.label',
            'config.fields.behavior_payment_refused.values.no.help',
            'cancel_action_'
        );

        $config_visible = $this->createRadio(
            PGModuleServicesSettings::_CONFIG_VISIBLE,
            'config.fields.visibility.label',
            'config.fields.visibility.values.yes.label',
            'config.fields.visibility.values.yes.help',
            'config.fields.visibility.values.no.label',
            'config.fields.visibility.values.no.help',
            'config_visible'
        );

        $config_payment_refund = $this->createSwitch(
            PGModuleServicesSettings::_CONFIG_PAYMENT_REFUND,
            'config.fields.behavior_transmit_refund.label',
            'config.fields.behavior_transmit_refund.help',
            'active'
        );

        $config_delivery_validation = $this->createSwitch(
            '_PG_CONFIG_DELIVERY_CONFIRMATION',
            'config.fields.behavior_transmit_delivering.label',
            'config.fields.behavior_transmit_delivering.help',
            'active'
        );

        $config_footer_display = $this->createSwitch(
            PGModuleServicesSettings::_CONFIG_FOOTER_DISPLAY,
            'config.fields.behavior_display_footer.label',
            'config.fields.behavior_display_footer.help',
            'logo'
        );

        $config_security_curl = $this->createSwitch(
            PGModuleServicesSettings::_CONFIG_SECURITY_CURL,
            'config.fields.behavior_use_ssl.label',
            'config.fields.behavior_use_ssl.help',
            'security_curl'
        );

        $config_debug_log = $this->createSwitch(
            '_PG_ACTIVATE_DEBUG_LOGS',
            'config.fields.behavior_debug_log.label',
            'config.fields.behavior_debug_log.help',
            'security_curl'
        );

        $colors = array(
            array(
                'id_option' => 'white',
                'name' => $translator->get('config.fields.display_footer_color.values.white')
            ),
            array(
                'id_option' => 'green',
                'name' => $translator->get('config.fields.display_footer_color.values.green')
            ),
            array(
                'id_option' => 'black',
                'name' => $translator->get('config.fields.display_footer_color.values.black')
            )
        );

        return array(
            'form' => array(
                'legend' => array(
                    'title' => $translator->get('config.title'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $translator->get('config.fields.shop_token.label'),
                        'name' => PGModuleServicesSettings::_CONFIG_SHOP_TOKEN,
                        'size' => 33,
                        'required' => true,
                        'placeholder' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                        'class' => 'fixed-width-xxl'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $translator->get('config.fields.private_key.label'),
                        'name' => PGModuleServicesSettings::_CONFIG_PRIVATE_KEY,
                        'size' => 28,
                        'required' => true,
                        'placeholder' => 'xxxx-xxxx-xxxx-xxxxxxxxxxxx',
                        'class' => 'fixed-width-xxl'
                    ),
                    $config_visible,
                    $config_cancel_action,
                    array(
                        'type' => 'text',
                        'label' => $translator->get('config.fields.success_payment_text.label'),
                        'name' => PGModuleServicesSettings::_CONFIG_PAIEMENT_ACCEPTED,
                        'size' => 150,
                        'required' => false,
                        'placeholder' => $translator->get('config.fields.success_payment_text.placeholder'),
                        'class' => 'fixed-width-xxl'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $translator->get('config.fields.failure_payment_text.label'),
                        'name' => PGModuleServicesSettings::_CONFIG_PAIEMENT_REFUSED,
                        'size' => 150,
                        'required' => false,
                        'placeholder' => $translator->get('config.fields.failure_payment_text.placeholder'),
                        'class' => 'fixed-width-xxl'
                    ),
                    $config_footer_display,
                    array(
                        'type' => 'select',
                        'label' => $translator->get('config.fields.display_footer_color.label'),
                        'desc' => $translator->get('config.fields.display_footer_color.help'),
                        'name' => PGModuleServicesSettings::_CONFIG_FOOTER_LOGO_COLOR,
                        'options' => array(
                            'query' => $colors,
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ),
                    $config_payment_refund,
                    $config_delivery_validation,
                    $config_security_curl,
                    $config_debug_log,
                ),
                'submit' => array(
                    'title' => $translator->get('config.buttons.save'),
                    'class' => 'btn btn-default pull-right button'
                )
            ),
        );
    }

    /**
     * @param $name string id of your switch
     * @param $label
     * @param $desc
     * @param $id
     * @return array switch button
     */
    private function createSwitch($name, $label, $desc, $id)
    {
        /** @var PGFrameworkServicesHandlersTranslatorHandler $translator */
        $translator = $this->getService('handler.translator');

        return array(
            'type' => 'switch',
            'label' => $translator->get($label),
            'name' => $name,
            'is_bool' => true,
            'desc' => $translator->get($desc),
            'values' => array(
                array(
                    'id' => $id . '_on',
                    'value' => 1,
                    'label' => $translator->get('config.buttons.enabled')
                ),
                array(
                    'id' => $id . '_off',
                    'value' => 0,
                    'label' => $translator->get('config.buttons.disabled')
                )
            ),
        );
    }

    /**
     * @param $name string id of your switch
     * @param $label
     * @param $label_yes
     * @param $desc_yes
     * @param $label_no
     * @param $desc_no
     * @param $id
     * @return array radio button
     */
    private function createRadio($name, $label, $label_yes, $desc_yes, $label_no, $desc_no, $id)
    {
        /** @var PGFrameworkServicesHandlersTranslatorHandler $translator */
        $translator = $this->getService('handler.translator');

        return array(
            'type' => 'radio',
            'label' => $translator->get($label),
            'name' => $name,
            'values' => array(
                array(
                    'id' => $id . '_yes',
                    'label' => $translator->get($label_yes),
                    'value' => 1,
                    'p' => $translator->get($desc_yes),
                ),
                array(
                    'id' => $id . '_no',
                    'label' => $translator->get($label_no),
                    'value' => 0,
                    'p' => $translator->get($desc_no),
                )
            ),
            'class' => 'fixed-width-xxl'
        );
    }

    public function displayTopPositionFormAction()
    {
        /** @var PGModuleServicesHandlersMultiShopHandler $multiShopHandler */
        $multiShopHandler = $this->getService('handler.multi_shop');

        if ($multiShopHandler->isShopContext()) {
            $response = $this->buildTopPositionFormResponse();
        } else {
            $response = $multiShopHandler->buildOnlyShopLevelResponse(
                'Action PayGreen',
                'cogs'
            );
        }

        return $response;
    }

    public function buildTopPositionFormResponse()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var Paygreen $localModule */
        $localModule = $this->getService('local.module');

        try {
            $response = new PGFrameworkComponentsResponsesTemplateResponse('shippingTab');

            $response
                ->setTemplate('views/templates/admin/' . $localModule->vPresta, 'configureHook')
                ->addResource('css', 'views/css/configure-hook.css')
            ;
        } catch (Exception $exception) {
            $logger->error("Error during configure hooks form building : " . $exception->getMessage(), $exception);

            $response = new PGFrameworkComponentsResponsesChainQualifiedMessagesResponse();

            $response->add($response::FAILURE, 'backoffice.actions.hooks.errors.display_form');
        }

        return $response;
    }
}
