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
 * Class PGFrameworkServicesLogger
 * @package PGFramework\Services
 */
class PGFrameworkServicesLogger extends PGFrameworkFoundationsAbstractObject
{
    const DEFAULT_FORMAT = "<datetime> | *<type>* | <text>";

    /** @var SplFileObject|null */
    private $handle;

    /** @var PGFrameworkServicesDumper */
    private $dumper;

    private $target;

    private $format = self::DEFAULT_FORMAT;

    private $detailedLogActivated = null;

    private $detailedLogActivation = false;

    private $logs = array();

    /** @var PGFrameworkServicesHandlersBehaviorHandler */
    private $behaviorHandler;

    public function __construct(PGFrameworkServicesDumper $dumper, $target, $format = null)
    {
        $this->dumper = $dumper;
        $this->target = $target;

        if ($format !== null) {
            $this->format = $format;
        }
    }

    /**
     * @param PGFrameworkServicesHandlersBehaviorHandler $behaviorHandler
     */
    public function setBehaviorHandler(PGFrameworkServicesHandlersBehaviorHandler $behaviorHandler)
    {
        $this->behaviorHandler = $behaviorHandler;
    }

    /**
     * @param $path
     * @return $this
     */
    protected function openHandle($path)
    {
        $this->handle = null;

        try {
            $this->handle = new SplFileObject($path, 'a');

            if (!$this->handle->isWritable()) {
                throw new Exception("Log file is not writable : $path.");
            }

            array_unshift($this->logs, array(
                'NOTICE',
                "Logging channel opened : '$path'.",
                null
            ));
        } catch (Exception $exception) {
            die($exception->getMessage());
        }

        return $this;
    }

    public function emergency($text, $data = null)
    {
        $this->write('EMERGENCY', $text, $data);
    }

    public function alert($text, $data = null)
    {
        $this->write('ALERT', $text, $data);
    }

    public function critical($text, $data = null)
    {
        $this->write('CRITICAL', $text, $data);
    }

    public function error($text, $data = null)
    {
        $this->write('ERROR', $text, $data);
    }

    public function warning($text, $data = null)
    {
        $this->write('WARNING', $text, $data);
    }

    public function notice($text, $data = null)
    {
        $this->write('NOTICE', $text, $data);
    }

    public function info($text, $data = null)
    {
        $this->write('INFO', $text, $data);
    }

    public function debug($text, $data = null)
    {
        $this->write('DEBUG', $text, $data);
    }

    private function isDetailedLogActivated()
    {
        if ($this->detailedLogActivated === null) {
            try {
                $this->detailedLogActivation = true;

                $this->detailedLogActivated = $this->behaviorHandler->get('detailed_logs');

                $this->detailedLogActivation = false;
            } catch (Exception $exception) {
                $this->detailedLogActivation = false;

                $this->detailedLogActivated = true;

                $this->error("An error occurred during detailed logs activation : " . $exception->getMessage(), $exception);
            }
        }

        return (bool) $this->detailedLogActivated;
    }

    private function write($type, $text, $data = null)
    {
        $this->logs[] = array($type, $text, $data);

        if ($this->detailedLogActivation) {
            return;
        }

        if ($this->handle === null) {
            $this->openHandle($this->target);
        }

        while (!empty($this->logs)) {
            list($type, $text, $data) = array_shift($this->logs);

            if (($type !== 'DEBUG') || $this->isDetailedLogActivated()) {
                $formatedLog = $this->formatLog($type, $text, $data);
                $this->handle->fwrite($formatedLog . PHP_EOL);
            }
        }
    }

    protected function formatLog($type, $text, $data = null)
    {
        $dt = new DateTime();
        $datetime = $dt->format('Y-m-d H:i:s');

        if (!is_string($text)) {
            $data = $text;
            $text = '';
        }

        $basicLog = $this->format;

        $basicLog = str_replace('<type>', $type, $basicLog);
        $basicLog = str_replace('<datetime>', $datetime, $basicLog);
        $basicLog = str_replace('<text>', $text, $basicLog);

        $log = $basicLog;

        if (!is_null($data)) {
            $formatedData = $this->dumper->toString($data);

            $log .= " | $formatedData";
        }

        return $log;
    }
}
