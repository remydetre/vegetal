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
 * Class PGFrameworkServicesBehaviorsDetailedLogsBehavior
 * @package PGFramework\Services\Behaviors
 */
class PGFrameworkServicesBehaviorsDetailedLogsBehavior extends PGFrameworkFoundationsAbstractObject
{
    /** @var PGFrameworkServicesSettings */
    private $settings;

    public function __construct(PGFrameworkServicesSettings $settings)
    {
        $this->settings = $settings;
    }

    public function isDetailedLogActivated()
    {
        $detailedLogActivated = $this->settings->get('behavior_detailed_logs');
        $lastUpdate = $this->settings->get('last_update');

        if ($lastUpdate !== PAYGREEN_MODULE_VERSION) {
            $detailedLogActivated = true;
        }

        return (bool) $detailedLogActivated;
    }
}
