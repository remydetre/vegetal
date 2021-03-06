<?php
/**
 * 2014 - 2019 Watt Is It
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
 * @copyright 2014 - 2019 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   2.7.6
 */
namespace OAuth2\GrantType;

use OAuth2\InvalidArgumentException;

/**
 * Password Parameters
 */
class Password implements IGrantType
{
    /**
     * Defines the Grant Type
     *
     * @var string  Defaults to 'password'.
     */
    const GRANT_TYPE = 'password';

    /**
     * Adds a specific Handling of the parameters
     *
     * @return array of Specific parameters to be sent.
     * @param  mixed  $parameters the parameters array (passed by reference)
     */
    public function validateParameters(&$parameters)
    {
        if (!isset($parameters['username'])) {
            throw new InvalidArgumentException(
                'The \'username\' parameter must be defined for the Password grant type',
                InvalidArgumentException::MISSING_PARAMETER
            );
        } elseif (!isset($parameters['password'])) {
            throw new InvalidArgumentException(
                'The \'password\' parameter must be defined for the Password grant type',
                InvalidArgumentException::MISSING_PARAMETER
            );
        }
    }
}
