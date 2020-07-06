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
 * Class PGFormFoundationsAbstractFormatter
 * @package PGForm\Foundations
 */
abstract class PGFormFoundationsAbstractFormatter implements PGFormInterfacesFormatterInterface
{
    const TEXT = null;

    private $error = false;

    /** @inheritDoc */
    public function format($value)
    {
        $result = null;

        try {
            $result = $this->process($value);
        } catch (Exception $exception) {
            $this->error = static::TEXT;
        }

        return $result;
    }

    abstract protected function process($value);

    /** @inheritDoc */
    public function isValid()
    {
        return ($this->error === false);
    }

    /** @inheritDoc */
    public function getError()
    {
        return $this->error;
    }
}
