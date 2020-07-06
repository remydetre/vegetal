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
 * Class PGDomainEntitiesPaymentType
 * @package PGDomain\Entities
 */
class PGDomainEntitiesPaymentType extends PGFrameworkFoundationsAbstractEntityArray
{
    /**
     * @return string
     */
    public function getCurrency()
    {
        return (string) $this->get('currency');
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return (string) $this->get('paymentType');
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return (int) $this->get('status');
    }

    /**
     * @return array
     */
    public function getIframeConfiguration()
    {
        return $this->get('iframe');
    }
}
