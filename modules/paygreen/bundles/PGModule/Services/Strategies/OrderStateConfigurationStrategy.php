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

class PGModuleServicesStrategiesOrderStateConfigurationStrategy extends PGDomainFoundationsAbstractOrderStateMapperStrategy
{
    /**
     * @param array $localState
     * @return string|null
     * @throws PGFrameworkExceptionsConfigurationException
     */
    public function getState(array $localState)
    {
        if (!array_key_exists('state', $localState)) {
            throw new Exception("localState must contains 'state' field.");
        }

        /**
         * @var string $state
         * @var array $definition
         */
        foreach ($this->getDefinitions() as $state => $definition) {
            if (!array_key_exists('name', $definition)) {
                $message = "Parameter 'name' not found in orderState definition : '$state'.";
                throw new PGFrameworkExceptionsConfigurationException($message);
            }

            $id_searched_state = (int) $localState['state'];
            $parameter = $definition['name'];

            $id_finded_state = (int) Configuration::get($parameter);

            if ($id_searched_state === $id_finded_state) {
                return $state;
            }
        }

        return null;
    }

    public function getLocalState($state)
    {
        $definitions = $this->getDefinitions();

        if (!array_key_exists($state, $definitions)) {
            $message = "OrderState definition not found : '$state'.";
            throw new Exception($message);
        } elseif (!array_key_exists('name', $definitions[$state])) {
            $message = "Parameter 'name' not found in orderState definition : '$state'.";
            throw new PGFrameworkExceptionsConfigurationException($message);
        }

        $parameter = $definitions[$state]['name'];

        return array(
            'state' => Configuration::get($parameter)
        );
    }

    public function isRecognizedLocalState(array $localState)
    {
        return ($this->getState($localState) !== null);
    }
}
