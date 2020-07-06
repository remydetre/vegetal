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

class PGFormServicesValidatorsArrayInValidator extends PGFormFoundationsAbstractValidator
{
    const ERROR_TRANSLATION_KEY = 'validator.errors.array_in';

    /** @var PGFrameworkServicesHandlersSelectHandler */
    private $selectHandler;

    public function __construct(PGFrameworkServicesHandlersSelectHandler $selectHandler)
    {
        $this->selectHandler = $selectHandler;
    }

    /**
     * @param string $value
     * @return bool
     * @throws Exception
     */
    protected function test($value)
    {
        return in_array($value, $this->getValues());
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getErrorData()
    {
        $values = array();
        $length = 0;

        foreach ($this->getValues() as $value) {
            if (($length === 0) || ($length < 100)) {
                $length += Tools::strlen($value);
                $values[] = $value;
            } elseif ($length > 0) {
                $values[] = '...';
            }
        }

        return array(
            'values' => implode(', ', $values)
        );
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getValues()
    {
        $values = $this->getDefaultConfig('values');

        if (!is_array($values)) {
            $values = $this->selectHandler->getKeys($values);
        }

        return $values;
    }
}
