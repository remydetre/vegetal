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

class PaygreenServicesLogger extends PaygreenObject
{
    private $handle = null;

    /**
     * @param string $path
     */
    public function openHandle($path)
    {
        if ($this->handle !== null) {
            fclose($this->handle);
            $this->handle = null;
        }

        $this->handle = @fopen($path, 'a');

        $this->debug("Logging channel opened : '$path'.");
    }

    public function error($text, $data = null)
    {
        $this->write('ERROR', $text, $data);
    }

    public function info($text, $data = null)
    {
        $this->write('INFO', $text, $data);
    }

    public function warning($text, $data = null)
    {
        $this->write('WARNING', $text, $data);
    }

    public function debug($text, $data = null)
    {
        if (PAYGREEN_ENV === 'DEV') {
            $this->write('DEBUG', $text, $data);
        }
    }

    private function write($type, $text, $data = null)
    {
        if ($this->handle !== null) {
            $dt = new DateTime();
            $datetime = $dt->format('Y-m-d H:i:s');

            if (!is_string($text)) {
                $data = $text;
                $text = '';
            }

            $logs = array();

            if (!is_scalar($data)) {
                $logs[] = "*$type* | $datetime | $text";

                $formatedData = print_r($data, true);

                if (!empty($formatedData)) {
                    $logs[] = $formatedData . PHP_EOL;
                }
            } elseif (!empty($data)) {
                $logs[] = "*$type* | $datetime | $text | $data";
            } else {
                $logs[] = "*$type* | $datetime | $text";
            }

            foreach ($logs as $log) {
                fwrite($this->handle, $log . PHP_EOL);
            }
        }
    }
}
