<?php
/**
 * 2014 - 2015 Watt Is It
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PayGreen <contact@paygreen.fr>
 * @copyright 2014-2014 Watt It Is
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop <SA></SA>
 *
 */

class PaygreenContainer
{
    const REGEX_ENV_VAR_MATCH = '/(^|[^\\\\])(?<match>\$\{(?<key>[a-zA-Z0-9_]+)\})/';
    const REGEX_ENV_VAR_REPLACE = '/(^|[^\\\\])(\$\{%s\})/';
    const REGEX_ENV_VAR_CLEANING = '/(\\\\)(\$\{[a-zA-Z0-9_]+\})/';

    const REGEX_PARAMETER_MATCH = '/(^|[^\\\\])(?<match>%\{(?<key>[a-zA-Z0-9_\-\.]+)\})/';
    const REGEX_PARAMETER_REPLACE = '/(^|[^\\\\])(%%\{%s\})/';
    const REGEX_PARAMETER_CLEANING = '/(\\\\)(%\{[a-zA-Z0-9_\-\.]+\})/';

    private $services = array();

    private $serviceDefinitions = array();

    private $delayedCalls = array();

    private $parameters;

    private $underConstructionServices = array();

    private static $instance = null;

    private function __construct()
    {
        $this->parameters = new PaygreenParameters();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($name)
    {
        if (!array_key_exists($name, $this->services)) {
            $this->buildServiceChain($name);
        }

        return $this->services[$name];
    }

    public function set($name, $service)
    {
        if (!array_key_exists($name, $this->serviceDefinitions)) {
            throw new LogicException("Attempt to set a non-defined service : '$name'.");
        }

        $this->services[$name] = $service;

        return $this;
    }

    /**
     * @param $filename
     * @throws Exception
     */
    public function addConfigurationFile($filename)
    {
        $data = json_decode(Tools::file_get_contents($filename), true);

        if (!$data) {
            throw new Exception("Unable to load service definition file : '$filename'.");
        }

        $this->serviceDefinitions = array_merge_recursive($this->serviceDefinitions, $data);

        return $this;
    }

    /**
     * @param $filename
     * @throws Exception
     */
    public function addParametersFile($filename)
    {
        $data = json_decode(Tools::file_get_contents($filename), true);

        if (!$data) {
            throw new Exception("Unable to load parameters file : '$filename'.");
        }

        $this->parameters->merge($data);

        return $this;
    }

    protected function buildServiceChain($name)
    {
        $this->delayedCalls = array();

        $this->buildService($name);

        foreach ($this->delayedCalls as $serviceName => $delayedCalls) {
            foreach ($delayedCalls as $delayedCall) {
                $this->executeCall($serviceName, $delayedCall);
            }
        }
    }

    protected function buildService($name)
    {
        if (array_key_exists($name, $this->services)) {
            return $this->services[$name];
        } elseif (!array_key_exists($name, $this->serviceDefinitions)) {
            throw new LogicException("Call to a non-existant service : '$name'.");
        } elseif (in_array($name, $this->underConstructionServices)) {
            throw new LogicException("Circular reference detected for service : '$name'.");
        }

        $this->underConstructionServices[] = $name;

        $definition = $this->serviceDefinitions[$name];

        if (!array_key_exists('class', $definition)) {
            throw new LogicException("Target service definition has no class name : '$name'.");
        }

        $class = $definition['class'];
        $arguments = array();

        if (array_key_exists('arguments', $definition)) {
            if (!is_array($definition['arguments'])) {
                throw new LogicException("Target service definition has inconsistent argument list : '$name'.");
            }

            foreach ($definition['arguments'] as $argument) {
                $arguments[] = $this->parseArgument($argument);
            }
        }

        if (array_key_exists('calls', $definition)) {
            if (!is_array($definition['calls'])) {
                throw new LogicException("Target service definition has inconsistent call list : '$name'.");
            }

            $this->delayedCalls[$name] = $definition['calls'];
        }

        $reflexionClass = new ReflectionClass($class);

        $service = $reflexionClass->newInstanceArgs($arguments);

        $this->services[$name] = $service;

        $index = array_search($name, $this->underConstructionServices);
        unset($this->underConstructionServices[$index]);

        return $service;
    }

    protected function parseArgument($arg)
    {
        $arg = $this->injectStringParameters($arg);
        $arg = $this->injectConstants($arg);
        $arg = $this->injectService($arg);
        $arg = $this->injectParameter($arg);

        return $arg;
    }

    /**
     * @param string $var
     * @return string
     * @throws Exception
     */
    protected function injectConstants($var)
    {
        if (is_string($var)) {
            while (preg_match(self::REGEX_ENV_VAR_MATCH, $var, $matches)) {
                $key = $matches['key'];

                if (!defined($key)) {
                    throw new Exception("Target constant '$key' is not defined.");
                }

                $pattern = sprintf(self::REGEX_ENV_VAR_REPLACE, preg_quote($key));
                $var = preg_replace($pattern, '${1}' . constant($key), $var);
            }

            $var = preg_replace(self::REGEX_ENV_VAR_CLEANING, '$2', $var);
        }

        return $var;
    }

    /**
     * @param string $var
     * @return string
     * @throws Exception
     */
    protected function injectStringParameters($var)
    {
        if (is_string($var)) {
            while (preg_match(self::REGEX_PARAMETER_MATCH, $var, $matches)) {
                $key = $matches['key'];

                if (!isset($this->parameters[$key])) {
                    throw new Exception("Target parameters '$key' is not defined.");
                }

                $pattern = sprintf(self::REGEX_PARAMETER_REPLACE, preg_quote($key));
                $var = preg_replace($pattern, '${1}' . $this->parameters[$key], $var);
            }

            $var = preg_replace(self::REGEX_PARAMETER_CLEANING, '$2', $var);
        }

        return $var;
    }

    protected function injectService($var)
    {
        if (is_string($var)) {
            if (Tools::substr($var, 0, 1) === '@') {
                $serviceName = Tools::substr($var, 1);
                $var = $this->buildService($serviceName);
            }
        }

        return $var;
    }

    protected function injectParameter($var)
    {
        if (is_string($var)) {
            if (Tools::substr($var, 0, 1) === '%') {
                $key = Tools::substr($var, 1);

                if (!isset($this->parameters[$key])) {
                    throw new Exception("Target parameters '$key' is not defined.");
                }

                $var = $this->parameters[$key];
            }
        }

        return $var;
    }

    protected function executeCall($name, $delayedCall)
    {
        if (!array_key_exists($name, $this->services)) {
            throw new LogicException("Unable to retrieve target service : '$name'.");
        }

        $service = $this->services[$name];

        if (!array_key_exists('method', $delayedCall)) {
            throw new LogicException("Target service call has no method name : '$name'.");
        }

        $method = $delayedCall['method'];
        $arguments = array();

        if (array_key_exists('arguments', $delayedCall)) {
            if (!is_array($delayedCall['arguments'])) {
                throw new LogicException("Target service call has inconsistent argument list : '$name::$method'.");
            }

            foreach ($delayedCall['arguments'] as $argument) {
                $arguments[] = $this->parseArgument($argument);
            }
        }

        call_user_func_array(array($service, $method), $arguments);
    }
}
