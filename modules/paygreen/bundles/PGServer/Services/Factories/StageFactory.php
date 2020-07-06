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

class PGServerServicesFactoriesStageFactory extends PGFrameworkFoundationsAbstractObject
{
    /** @var PGServerServicesFactoriesTriggerFactory */
    private $triggerFactory;

    /** @var PGFrameworkServicesLogger */
    private $logger;

    public function __construct(PGServerServicesFactoriesTriggerFactory $triggerFactory, PGFrameworkServicesLogger $logger)
    {
        $this->triggerFactory = $triggerFactory;
        $this->logger = $logger;
    }

    /**
     * @param array $config
     * @return PGServerComponentsStage
     * @throws Exception
     */
    public function buildStage(array $config)
    {
        /** @var PGServerComponentsTrigger|null $trigger */
        $trigger = null;

        if (array_key_exists('if', $config)) {
            $trigger = $this->triggerFactory->buildTrigger($config['if']);
        }

        if (!array_key_exists('do', $config)) {
            throw new Exception("Server Stage definition must contains 'do' key.");
        }

        /** @var PGServerComponentsStage $stage */
        $stage = new PGServerComponentsStage($config, $trigger);

        $stage->setLogger($this->logger);

        return $stage;
    }
}
