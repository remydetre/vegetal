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

class PGServerServicesDispatcher extends PGFrameworkFoundationsAbstractObject
{
    private $controllerNames = array();

    /** @var PGFrameworkServicesLogger */
    private $logger;

    /** @var PGFrameworkServicesBroadcaster */
    private $broadcaster;

    public function __construct(
        PGFrameworkServicesLogger $logger,
        PGFrameworkServicesBroadcaster $broadcaster
    ) {
        $this->logger = $logger;
        $this->broadcaster = $broadcaster;
    }

    public function addControllerName($serviceName, $controllerName = null)
    {
        if ($controllerName === null) {
            if (preg_match('/^controller\.(?P<name>.+)/', $serviceName, $result)) {
                $controllerName = $result['name'];
            } else {
                throw new Exception("Unable to automatically determine the controller name with the service name : '$serviceName'.");
            }
        }

        $this->controllerNames[$controllerName] = $serviceName;
    }

    /**
     * @param PGServerFoundationsAbstractRequest $request
     * @param string $localization
     * @return PGServerFoundationsAbstractResponse
     * @throws Exception
     */
    public function dispatch(PGServerFoundationsAbstractRequest $request, $localization)
    {
        list($actionName, $controllerName) = explode('@', $localization, 2);

        /** @var PGServerFoundationsAbstractController $controller */
        $controller = $this->getController($controllerName);

        $controller->setRequest($request);

        $action = $actionName . 'Action';
        $class = get_class($controller);

        if (!method_exists($controller, $action)) {
            throw new Exception("Target controller '$class' has no action method '$action'.");
        }

        $event = new PGServerComponentsActionEvent($request, $controller, $controllerName, $actionName);
        $this->broadcaster->fire($event);

        $this->logger->debug("Execute method '$action' on '$class'.");

        /** @var PGServerFoundationsAbstractResponse $response */
        $response = call_user_func(array($controller, $action));

        $this->logger->debug("Response successfully built.");

        return $response;
    }

    /**
     * @param string $name
     * @return PGServerFoundationsAbstractController
     */
    protected function getController($name)
    {
        if (!array_key_exists($name, $this->controllerNames)) {
            throw new LogicException("Unknown controller name : '$name'.");
        }

        /** @var PGServerFoundationsAbstractController $controller */
        $controller = $this->getService($this->controllerNames[$name]);

        return $controller;
    }
}
