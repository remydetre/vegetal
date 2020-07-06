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

class PGModuleServicesDiagnosticsPrimaryShopDiagnostic extends PGFrameworkFoundationsAbstractDiagnostic
{
    /** @var PGFrameworkServicesSettings */
    private $settings;

    /** @var PGFrameworkServicesLogger */
    private $logger;

    public function __construct(PGFrameworkServicesSettings $settings, PGFrameworkServicesLogger $logger)
    {
        $this->settings = $settings;
        $this->logger = $logger;
    }

    public function isValid()
    {
        $primaryShop = $this->settings->get('shop_identifier');

        return !empty($primaryShop);
    }

    /**
     * @throws Exception
     */
    public function resolve()
    {
        $primaryShop = $this->buildPrimaryShop();

        $this->settings->set('shop_identifier', $primaryShop);

        $this->logger->notice("Primary shop successfully created.");

        return true;
    }

    public function buildPrimaryShop()
    {
        $pool = array_merge(range(0, 9), range('A', 'Z'));

        $key = null;
        for ($i = 0; $i < 4; $i++) {
            $key .= $pool[mt_rand(0, count($pool) - 1)];
        }

        return $key;
    }
}
