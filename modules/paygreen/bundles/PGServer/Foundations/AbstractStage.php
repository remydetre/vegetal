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
 * Class PGServerFoundationsAbstractStage
 * @package PGServer\Foundations
 */
abstract class PGServerFoundationsAbstractStage extends PGFrameworkFoundationsAbstractObject
{
    private $config;

    /** @var PGServerComponentsTrigger|null */
    private $trigger;

    /** @var PGFrameworkServicesLogger */
    private $logger;

    public function __construct(array $config, PGServerComponentsTrigger $trigger = null)
    {
        $this->config = $config;
        $this->trigger = $trigger;
    }

    /**
     * @param string|null $key
     * @return array|mixed|null
     */
    protected function getConfig($key = null)
    {
        if ($key === null) {
            return $this->config;
        } elseif (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        } else {
            return null;
        }
    }

    /**
     * @return PGFrameworkServicesLogger
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param PGFrameworkServicesLogger $logger
     */
    public function setLogger(PGFrameworkServicesLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param PGServerFoundationsAbstractResponse $response
     * @return bool
     */
    public function isTriggered(PGServerFoundationsAbstractResponse $response)
    {
        return ($this->trigger === null) ? true : $this->trigger->isTriggered($response);
    }

    /**
     * @param PGServerFoundationsAbstractResponse $response
     * @return mixed
     * @throws Exception
     */
    protected function callService(PGServerFoundationsAbstractResponse $response)
    {
        $stageType = static::STAGE_TYPE;
        $stageMethod = static::STAGE_METHOD;

        $serviceName = $this->getConfig('with');

        if ($serviceName === null) {
            throw new Exception("Server Stage must specify service to perform action.");
        }

        $service = $this->getService($serviceName);

        if (!method_exists($service, $stageMethod)) {
            throw new Exception("@$serviceName is not a valid $stageType. Target service must implements '$stageMethod' method.");
        }

        $this->getLogger()->debug(Tools::ucfirst($stageMethod) . " Response with '@$serviceName'.");

        $arguments = array($response, $this->getConfig('config'));

        return call_user_func_array(array($service, $stageMethod), $arguments);
    }

    /**
     * @param PGServerFoundationsAbstractResponse $response
     * @return PGServerFoundationsAbstractResponse|null
     */
    abstract public function execute(PGServerFoundationsAbstractResponse $response);
}
