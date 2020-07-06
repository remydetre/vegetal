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
 * Class PGFrameworkServicesHandlersRequirementHandler
 * @package PGFramework\Services\Handlers
 */
class PGFrameworkServicesHandlersRequirementHandler
{
    /** @var PGFrameworkServicesAggregatorsRequirementAggregator */
    private $requirementAggregator;

    public function __construct(PGFrameworkServicesAggregatorsRequirementAggregator $requirementAggregator)
    {
        $this->requirementAggregator = $requirementAggregator;
    }

    /**
     * @param string $name
     * @param mixed $arguments
     * @return bool
     * @throws Exception
     */
    public function isFulfilled($name, $arguments)
    {
        /** @var PGFrameworkInterfacesRequirementInterface $requirement */
        $requirement = $this->requirementAggregator->getRequirement($name);

        return $requirement->isFulfilled($arguments);
    }

    /**
     * @param array $requirements
     * @return bool
     * @throws Exception
     */
    public function areFulfilled(array $requirements)
    {
        $result = true;

        foreach ($requirements as $name => $arguments) {
            if (!$this->isFulfilled($name, $arguments)) {
                $result = false;
                break;
            }
        }

        return $result;
    }
}
