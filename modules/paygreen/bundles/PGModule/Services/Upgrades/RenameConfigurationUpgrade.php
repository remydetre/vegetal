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

class PGModuleServicesUpgradesRenameConfigurationUpgrade implements PGFrameworkInterfacesUpgradeInterface
{
    /** @var PGFrameworkServicesLogger */
    private $logger;

    public function __construct(PGFrameworkServicesLogger $logger)
    {
        $this->logger =$logger;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function apply(PGFrameworkComponentsUpgradeStage $upgradeStage)
    {
        foreach ($upgradeStage->getConfig('keys') as $oldKey => $newKey) {
            $value = Configuration::get($oldKey);
            if ($value) {
                $this->logger->notice("Rename old configuration '$oldKey' in '$newKey'.");

                Configuration::updateValue($newKey, $value);
                Configuration::deleteByName($oldKey);
            } else {
                $this->logger->notice("Old configuration '$oldKey' not found.");
            }
        }

        return true;
    }
}
