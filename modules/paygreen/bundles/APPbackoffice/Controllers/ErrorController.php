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

class APPbackofficeControllersErrorController extends APPbackofficeFoundationsAbstractBackofficeController
{
    public function displayExceptionAction()
    {
        $exceptions = array();

        /** @var Exception $exception */
        $exception = $this->getRequest()->get('exception');

        while ($exception !== null) {
            $exceptions[] = array(
                'type' => get_class($exception),
                'text' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'traces' => $this->formatTraces($exception->getTrace(), $exception->getFile(), $exception->getLine())
            );

            $exception = $exception->getPrevious();
        }

        return $this->buildTemplateResponse('page-error')
            ->addData('exceptions', $exceptions)
        ;
    }

    protected function formatTraces(array $traces, $firstFile, $firstLine)
    {
        $formatedTraces = array();

        foreach ($traces as $trace) {
            if (array_key_exists('class', $trace)) {
                $call = $trace['class'] . $trace['type'] . $trace['function'];
            } else {
                $call = $trace['function'];
            }

            if (empty($formatedTraces)) {
                $formatedTraces[] = array(
                    'file' => $firstFile,
                    'line' => $firstLine,
                    'call' => $call
                );
            } else {
                $formatedTraces[] = array(
                    'file' => isset($trace['file']) ? $trace['file'] : '',
                    'line' => isset($trace['line']) ? $trace['line'] : '',
                    'call' => $call
                );
            }
        }

        krsort($formatedTraces);

        return $formatedTraces;
    }
}
