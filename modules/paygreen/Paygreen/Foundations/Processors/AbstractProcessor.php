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
 *  @author    PayGreen <contact@paygreen.fr>
 *  @copyright 2014-2014 Watt It Is
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 */

abstract class PaygreenFoundationsProcessorsAbstractProcessor extends PaygreenObject
{
    const PROCESSOR_NAME = 'UndefinedTask';

    private $exceptions = array();

    private $steps = array();

    /**
     * @param array $steps
     */
    public function setSteps(array $steps)
    {
        $this->steps = $steps;
    }

    public function pushStep($step)
    {
        array_push($this->steps, $step);
    }

    public function pushSteps(array $steps)
    {
        foreach ($steps as $step) {
            $this->pushStep($step);
        }
    }

    public function addStep($step)
    {
        array_unshift($this->steps, $step);
    }

    public function addSteps(array $steps)
    {
        foreach (array_reverse($steps) as $step) {
            $this->addStep($step);
        }
    }

    protected function addException(Exception $exception)
    {
        $this->exceptions[] = $exception;
    }

    /**
     * @return array
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    public function execute(PaygreenInterfacesTaskInterface $task)
    {
        /** @var PaygreenServicesLogger $logger */
        $logger = $this->getService('logger');

        $taskName = static::PROCESSOR_NAME;

        if ($task->getStatus() === null) {
            $step = array_shift($this->steps);

            if ($step !== null) {
                try {
                    $parameters = array($task);

                    if (is_array($step)) {
                        list($method, $arguments) = $step;
                        $parameters = array_merge($parameters, $arguments);
                        $logger->debug("[TASK-$taskName] Running step : '$method'.");
                    } else {
                        $method = $step;
                        $logger->debug("[TASK-$taskName] Running step : '$step'.");
                    }

                    call_user_func_array(array($this, $method . 'Step'), $parameters);
                } catch (Exception $exception) {
                    $logger->error("Catched error : '{$exception->getMessage()}'.");

                    $this->addException($exception);

                    $task->setStatus($task::STATE_FATAL_ERROR);
                }

                $this->execute($task);
            }
        } else {
            $status = $task->getStatusName($task->getStatus());
            $logger->info("[TASK-$taskName] Status : '$status'.");
        }
    }

    protected function setStatusStep(PaygreenInterfacesTaskInterface $task, $status)
    {
        $task->setStatus($status);
    }
}
