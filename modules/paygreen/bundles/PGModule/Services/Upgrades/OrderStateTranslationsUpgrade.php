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

class PGModuleServicesUpgradesOrderStateTranslationsUpgrade implements PGFrameworkInterfacesUpgradeInterface
{
    /** @var PGDomainServicesManagersShopManager */
    private $shopManager;

    /** @var PGFrameworkComponentsParameters */
    private $parameters;

    public function __construct(
        PGDomainServicesManagersShopManager $shopManager,
        PGFrameworkComponentsParameters $parameters
    ) {
        $this->shopManager = $shopManager;
        $this->parameters = $parameters;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function apply(PGFrameworkComponentsUpgradeStage $upgradeStage)
    {
        $definitions = $this->parameters["order.states"];

        $shops = $this->shopManager->getAll();

        foreach ($definitions as $definition) {
            if (($definition['source']['type'] === 'settings') && array_key_exists('create', $definition) && ($definition['create'] === true)) {
                $names = array();

                foreach (Language::getLanguages() as $language) {
                    $iso = Tools::strtolower($language['iso_code']);
                    $id_lang = $language['id_lang'];

                    if ($iso === 'fr') {
                        $names[$id_lang] = $definition['name'];
                    } elseif ($iso === 'en') {
                        $names[$id_lang] = $definition['metadata']['en'];
                    }
                }

                foreach ($shops as $shop) {
                    $id_order_state = Configuration::get($definition['source']['name'], null, null, $shop->id());

                    if ($id_order_state > 0) {
                        /** @var OrderStateCore $localOrderState */
                        $localOrderState = new OrderState($id_order_state, null, $shop->id());

                        if ($localOrderState->id > 0) {
                            $localOrderState->name = $names;
                            $localOrderState->update();
                        }
                    }
                }
            }
        }

        return true;
    }
}
