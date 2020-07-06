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
 * @version   2.5.4
 */


class PGLegacyServicesInstallHandler extends PGFrameworkFoundationsAbstractObject
{
    private $hooks = array(
        'header',
        'displayPaymentReturn',
        'ActionObjectOrderSlipAddAfter',
        'postUpdateOrderStatus',
        'displayFooter'
    );

    /** @var Paygreen */
    private $module;

    public function __construct(Paygreen $localModule)
    {
        $this->module = $localModule;

        if ($localModule->vPresta == 1.6) {
            $this->hooks[] = 'displayBackOfficeHeader';
            $this->hooks[] = 'displayPayment';
        } elseif ($localModule->vPresta == 1.7) {
            $this->hooks[] = 'paymentOptions';
        }
    }

    /**
     * Install PayGreen module
     */
    public function install()
    {
        /** @var PGDomainServicesManagersOrderStateManager $orderStateManager */
        $orderStateManager = $this->getService('manager.order_state');

        /** @var PGModuleServicesSettings $settings */
        $settings = $this->getService('settings');

        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        $logger->info('Install', 'Paygreen installation : BEGIN.');

        try {
            if (!$this->module->checkServerConfig()) {
                $error = 'To install this module, ' .
                    'you have to enable the cURL extension on your server, ' .
                    'or allow url fopen.';

                throw new Exception($error);
            }

            $iso_code = Country::getIsoById($settings->get('PS_COUNTRY_DEFAULT'));

            if (in_array($iso_code, $this->module->getLimitedCountries()) == false) {
                throw new Exception('This module is not available in your country');
            }

            $this->insertDatabaseObjects();
            $this->setDefaultSettings();
            $this->generateUniqueIdShop();
            $this->insertAdminPage();
            $this->insertDefaultButton();
            $this->moveDefaultButtonImage();
            $this->registerHooks();
            $this->updatePositionHook();

            return true;
        } catch (Exception $exception) {
            $logger->error("Error during installation : " . $exception->getMessage(), $exception);

            return false;
        }
    }

    /**
     * @throws Exception
     */
    private function registerHooks()
    {
        foreach ($this->hooks as $hook) {
            if (!$this->module->registerHook($hook)) {
                throw new Exception("Installation failed for target Hook : '$hook'.");
            }
        }
    }

    /**
     * @throws Exception
     */
    public function insertDatabaseObjects()
    {
        /** @var PGLegacyServicesDatabaseHandler $databaseHandler */
        $databaseHandler = $this->getService('handler.database');

        $databaseHandler->runScript('schema.sql');
    }

    private function setDefaultSettings()
    {
        /** @var PGModuleServicesSettings $settings */
        $settings = $this->getService('settings');

        $settings->set(PGModuleServicesSettings::_CONFIG_PRIVATE_KEY, '');
        $settings->set(PGModuleServicesSettings::_CONFIG_SHOP_TOKEN, '');
        $settings->set(PGModuleServicesSettings::_CONFIG_SHOP_INPUT_METHOD, 'POST');
        $settings->set(PGModuleServicesSettings::_CONFIG_VISIBLE, 1);

        $settings->set(PGModuleServicesSettings::_CONFIG_PAIEMENT_ACCEPTED, $this->module->l('Your payment was accepted'));
        $settings->set(PGModuleServicesSettings::_CONFIG_PAIEMENT_REFUSED, $this->module->l('Your payment was unsuccessful'));

        // Allow to refund with Paygreen
        $settings->set(PGModuleServicesSettings::_CONFIG_PAYMENT_REFUND, 0);

        // Footer Config
        $settings->set(PGModuleServicesSettings::_CONFIG_FOOTER_DISPLAY, 1);
        $settings->set(PGModuleServicesSettings::_CONFIG_FOOTER_LOGO_COLOR, 'white');
        $settings->set(PGModuleServicesSettings::_CONFIG_VERIF_ADULT, 0);
        $settings->set('oauth_access', '');

        //Security curl
        $settings->set(PGModuleServicesSettings::_CONFIG_SECURITY_CURL, 0);
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function moveDefaultButtonImage()
    {
        /** @var PGFrameworkServicesPathfinder $pathfinder */
        $pathfinder = $this->getService('pathfinder');

        /** @var PGFrameworkServicesHandlersPictureHandler $mediaHandler */
        $mediaHandler = $this->getService('handler.picture');

        $defaultButtonFilename = $mediaHandler::DEFAULT_PICTURE;
        $defaultButtonSrc = $pathfinder->toAbsolutePath('bundles-media', "/$defaultButtonFilename");

        if (is_file($defaultButtonSrc) and !$mediaHandler->isStored($defaultButtonFilename)) {
            $mediaHandler->store($defaultButtonSrc, $defaultButtonFilename);
        }
    }

    /**
     * @return bool
     */
    private function insertAdminPage()
    {
        // Add button paygreen in menu
        $tab_parent_id = Tab::getIdFromClassName('AdminParentModules');

        /** @var TabCore $tab */
        $tab = new Tab();

        $tab->class_name = 'AdminPaygreen';
        $tab->name[$this->module->getContext()->language->id] = $this->module->l('Paygreen');
        $tab->id_parent = $tab_parent_id;
        $tab->module = PAYGREEN_MODULE_NAME;

        if (!$tab->add()) {
            throw new Exception("Unable to create backoffice tab.");
        }
    }

    /**
     * insert first button.
     * @return bool
     * @throws Exception
     */
    private function insertDefaultButton()
    {
        /** @var PGDomainServicesManagersButtonManager $buttonManager */
        $buttonManager = $this->getService('manager.button');

        $countButtons = $buttonManager->count();

        if ($countButtons === 0) {
            /** @var PGModuleServicesRepositoriesButtonRepository $buttonRepository */
            $buttonRepository = $this->getService('repository.button');

            $button = $buttonRepository->create()
                ->setLabel($this->module->l('Pay by bank card'))
                ->setPaymentType('CB')
                ->setPosition(1)
                ->setImageHeight(60)
                ->setDisplayType('DEFAULT')
                ->setPaymentNumber(1)
                ->setDiscount(0)
            ;

            if (!$buttonRepository->insert($button)) {
                throw new Exception("Unable to create default button.");
            }
        }
    }

    /**
     * @throws Exception
     */
    public function uninstall()
    {
        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_PRIVATE_KEY);
        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_SHOP_TOKEN);

        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_SHOP_INPUT_METHOD);
        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_VISIBLE);

        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_PAIEMENT_ACCEPTED);
        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_PAIEMENT_REFUSED);
        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_PAYMENT_REFUND);

        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_FOOTER_DISPLAY);
        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_FOOTER_LOGO_COLOR);
        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_VERIF_ADULT);

        Configuration::deleteByName(PGModuleServicesSettings::_CONFIG_SECURITY_CURL);

        Db::getInstance()->delete('tab_lang', 'name=\'Paygreen\'');
        Db::getInstance()->delete('tab', 'class_name=\'Paygreen\'');

        $this->removeDatabaseObjects();

        $id_tab = (int) Tab::getIdFromClassName('AdminPaygreen');

        /** @var TabCore $tab */
        $tab = new Tab($id_tab);

        $tab->delete();

        return true;
    }

    public function removeDatabaseObjects()
    {
        /** @var PGLegacyServicesDatabaseHandler $databaseHandler */
        $databaseHandler = $this->getService('handler.database');

        $databaseHandler->runScript('clean.sql');
    }

    public function generateUniqueIdShop()
    {
        /** @var PGModuleServicesSettings $settings */
        $settings = $this->getService('settings');

        $pool = array_merge(range(0, 9), range('A', 'Z'));

        $key = null;
        for ($i=0; $i < 4; $i++) {
            $key .= $pool[mt_rand(0, count($pool) - 1)];
        }

        $settings->set('_ID_UNIQUE_SHOP', $key);
    }

    /**
     * set at 1st position all hook present in $listhook
     * @return bool
     */
    public function updatePositionHook()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        $result = false;

        try {
            $idPaygreen = (int)Db::getInstance()->getValue(
                'SELECT id_module FROM ' . _DB_PREFIX_ . 'module
            WHERE name = \'paygreen\''
            );

            foreach ($this->hooks as $hook) {
                $idHook = (int)Db::getInstance()->getValue(
                    'SELECT id_hook FROM ' . _DB_PREFIX_ . 'hook
                WHERE name = \'' . pSQL($hook) . '\''
                );

                $idModule = (int)Db::getInstance()->getValue(
                    'SELECT id_module FROM ' . _DB_PREFIX_ . 'hook_module
                WHERE position = 1 AND id_hook = ' . $idHook
                );

                $positionPaygreen = (int)Db::getInstance()->getValue(
                    'SELECT position FROM ' . _DB_PREFIX_ . 'hook_module
                WHERE id_hook = ' . $idHook . ' AND id_module = ' . $idPaygreen
                );

                $updateModulePosition = Db::getInstance()->update(
                    'hook_module',
                    array('position' => $positionPaygreen),
                    'id_module = ' . $idModule . ' AND id_hook = ' . $idHook
                );

                if ($updateModulePosition === false) {
                    $this->getService('logger')->error('Query FAILED.');

                    throw new Exception("Unable to update hook position : '$hook'.");
                }

                $updateModulePosition = Db::getInstance()->update(
                    'hook_module',
                    array('position' => 1),
                    'id_module = ' . (int)$idPaygreen . ' AND id_hook = ' . $idHook
                );

                if ($updateModulePosition === false) {
                    $this->getService('logger')->error('Query FAILED.');
                }
            }

            $result = true;
        } catch (Exception $exception) {
            $logger->error("Error during refreshing hooks : " . $exception->getMessage(), $exception);
        }

        return $result;
    }
}
