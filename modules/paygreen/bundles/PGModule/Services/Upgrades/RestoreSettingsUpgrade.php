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

class PGModuleServicesUpgradesRestoreSettingsUpgrade implements PGFrameworkInterfacesUpgradeInterface
{
    /** @var PGDomainServicesManagersShopManager */
    private $shopManager;

    /** @var PGFrameworkServicesSettings */
    private $settings;

    /** @var PGDomainServicesManagersSettingManager */
    private $settingManager;

    /** @var PGFrameworkServicesLogger */
    private $logger;

    public function __construct(
        PGFrameworkServicesSettings $settings,
        PGDomainServicesManagersSettingManager $settingManager,
        PGDomainServicesManagersShopManager $shopManager,
        PGFrameworkServicesLogger $logger
    ) {
        $this->settings = $settings;
        $this->settingManager = $settingManager;
        $this->shopManager = $shopManager;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function apply(PGFrameworkComponentsUpgradeStage $upgradeStage)
    {
        $keys = $upgradeStage->getConfig('keys');

        $shops = $this->shopManager->getAll();

        foreach ($keys as $oldKey => $newKey) {
            if ($this->settings->isDefined($newKey)) {
                foreach ($shops as $shop) {
                    $value = $this->getOldValue($oldKey, $shop);

                    if ($value !== null) {
                        if ($this->settings->isSystem($newKey) || $this->settings->isGlobal($newKey)) {
                            $this->restoreGlobalSetting($newKey, $value);
                        } else {
                            $this->restoreShopSetting($newKey, $value, $shop);
                        }
                    }
                }

                Configuration::deleteByName($oldKey);
            }
        }

        return true;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @throws Exception
     */
    protected function restoreGlobalSetting($key, $value)
    {
        $hasSetting = $this->settings->hasValue($key);

        if (!$hasSetting) {
            $this->settings->set($key, $value);
            $this->logger->notice("Successfully restore setting '$key' in global context.");
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param PGDomainInterfacesEntitiesShopInterface $shop
     */
    protected function restoreShopSetting($key, $value, PGDomainInterfacesEntitiesShopInterface $shop)
    {
        $hasSetting = $this->settingManager->hasByShop($key, $shop);

        if (!$hasSetting) {
            $this->settingManager->insert($key, $value, $shop);
            $this->logger->notice("Successfully restore setting '$key' for shop '{$shop->getName()}'.");
        }
    }

    protected function getOldValue($key, PGDomainInterfacesEntitiesShopInterface $shop)
    {
        $value = Configuration::get($key, null, null, $shop->id());

        if ($value) {
            $this->logger->notice("Retrieve old configuration '$key' for shop '{$shop->getName()}'.");
        } else {
            $value = null;
        }

        return $value;
    }
}
