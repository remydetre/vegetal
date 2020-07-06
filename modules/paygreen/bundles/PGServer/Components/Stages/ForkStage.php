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

class PGServerComponentsStagesForkStage extends PGServerFoundationsAbstractStage
{
    /** @var PGServerFoundationsAbstractStage[] */
    private $stages = array();

    /** @var PGServerServicesFactoriesStageFactory */
    private $stageFactory;

    /**
     * @param PGServerServicesFactoriesStageFactory $stageFactory
     */
    public function setStageFactory(PGServerServicesFactoriesStageFactory $stageFactory)
    {
        $this->stageFactory = $stageFactory;
    }

    /**
     * @param PGServerFoundationsAbstractResponse $response
     * @return PGServerFoundationsAbstractStage[]
     * @throws Exception
     */
    public function execute(PGServerFoundationsAbstractResponse $response)
    {
        if (empty($this->stages)) {
            $this->buildStages();
        }

        reset($this->stages);

        return $this->stages;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function follow()
    {
        $follow = Tools::strtoupper($this->getConfig('with'));

        if (empty($follow)) {
            $follow = 'RETURN';
        }

        if (!in_array($follow, array('RETURN', 'RESTART', 'CONTINUE'))) {
            throw new Exception("Unknown follow type: '$follow'.");
        }

        return $follow;
    }

    /**
     * @throws Exception
     */
    protected function buildStages()
    {
        $stageDefinitions = $this->getConfig('with');

        if (empty($stageDefinitions)) {
            throw new Exception("Fork stage require sub-stages configuration in 'with' key.");
        }

        foreach ($stageDefinitions as $stageDefinition) {
            $this->stages[] = $this->stageFactory->buildStage($stageDefinition);
        }

        $this->getLogger()->debug("Fork of rendering stages successfully built.");
    }
}
