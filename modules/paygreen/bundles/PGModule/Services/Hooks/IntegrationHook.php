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

/**
 * Class PGModuleServicesHooksIntegrationHook
 * @todo Utiliser les Views internes pour générer la vue du bouton.
 */
class PGModuleServicesHooksIntegrationHook
{
    /** @var PGFrameworkServicesLogger */
    private $logger;

    /** @var PGFrameworkServicesSettings */
    private $settings;

    /** @var PGDomainServicesPaygreenFacade */
    private $paygreenFacade;

    /** @var PGModuleBridgesPrestashopBridge */
    private $prestashopBridge;

    /** @var PGFrameworkInterfacesModuleFacadeInterface */
    private $moduleFacade;

    /** @var PGServerServicesLinker */
    private $linker;

    /** @var PGFrameworkServicesHandlersStaticFileHandler */
    private $staticFileHandler;

    /** @var PGViewServicesHandlersViewHandler */
    private $viewHandler;

    /** @var string */
    private $backlink;

    public function __construct(
        PGFrameworkInterfacesModuleFacadeInterface $moduleFacade,
        PGModuleBridgesPrestashopBridge $prestashopBridge,
        PGDomainServicesPaygreenFacade $paygreenFacade,
        PGFrameworkServicesSettings $settings,
        PGServerServicesLinker $linker,
        PGFrameworkServicesHandlersStaticFileHandler $staticFileHandler,
        PGViewServicesHandlersViewHandler $viewHandler,
        PGFrameworkServicesLogger $logger,
        $backlink
    ) {
        $this->moduleFacade = $moduleFacade;
        $this->prestashopBridge = $prestashopBridge;
        $this->paygreenFacade = $paygreenFacade;
        $this->settings = $settings;
        $this->linker = $linker;
        $this->staticFileHandler = $staticFileHandler;
        $this->viewHandler = $viewHandler;
        $this->logger = $logger;
        $this->backlink = $backlink;
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     * @throws Exception
     */
    public function registerHeader()
    {
        try {
            if (!$this->moduleFacade->isActive()) {
                return;
            }

            switch ($this->prestashopBridge->getPlateformVersion()) {
                case 1.6:
                    $this->prestashopBridge->getContext()->controller->addJS($this->staticFileHandler->getUrl('/js/frontoffice-una-sextus.js'));
                    $this->prestashopBridge->getContext()->controller->addCSS($this->staticFileHandler->getUrl('/css/frontoffice-una-sextus.css'));
                    break;
                case 1.7:
                    $this->prestashopBridge->getContext()->controller->addJS($this->staticFileHandler->getUrl('/js/frontoffice-una-septimus.js'));
                    $this->prestashopBridge->getContext()->controller->addCSS($this->staticFileHandler->getUrl('/css/frontoffice-una-septimus.css'));
                    break;
                default:
                    throw new Exception("PayGreen module only support Prestashop 1.6 or 1.7.");
            }

            if ($this->prestashopBridge->getPlateformVersion() >= 1.7 && $this->paygreenFacade->isConnected()) {
                $shopInfo = $this->paygreenFacade->getAccountInfos();

                if (isset($shopInfo->solidarityType) && ($shopInfo->solidarityType == 'CCARBONE')) {
                    Media::addJsDef(array(
                        'paygreen_tree_computing_url' => $this->linker->buildFrontOfficeUrl('front.tree.save')
                    ));
                }
            }
        } catch (Exception $exception) {
            $this->logger->critical("Error during Header hook : " . $exception->getMessage(), $exception);
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function displayFooter()
    {
        /** @var FrontControllerCore $controller */
        $controller = $this->prestashopBridge->getContext()->controller;

        $pageName = ($this->prestashopBridge->getPlateformVersion() >= 1.7) ? $controller->getPageName() : $controller->php_self;

        $result = '';

        try {
            if ($pageName === 'index') {
                $isFooterDisplayed = ((int) $this->settings->get('footer_display') === 1);

                if ($isFooterDisplayed && $this->moduleFacade->isActive() && $this->paygreenFacade->isConnected()) {
                    $result = $this->viewHandler->renderTemplate('footer', array(
                        'color' => $this->settings->get('footer_color'),
                        'backlink' => $this->backlink
                    ));
                }
            }
        } catch (Exception $exception) {
            $this->logger->error("Error during DisplayFooter hook : " . $exception->getMessage(), $exception);
        }

        return $result;
    }
}
