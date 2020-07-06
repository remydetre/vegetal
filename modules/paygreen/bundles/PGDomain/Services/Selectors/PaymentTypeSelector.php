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
 * Class PGDomainServicesSelectorsPaymentTypeSelector
 * @package PGFramework\Services\Selectors
 */
class PGDomainServicesSelectorsPaymentTypeSelector extends PGFrameworkFoundationsAbstractSelector
{
    /** @var PGDomainServicesManagersPaymentTypeManager */
    private $paymentTypeManager;

    /**
     * @param PGDomainServicesManagersPaymentTypeManager $paymentTypeManager
     */
    public function setPaymentTypeManager($paymentTypeManager)
    {
        $this->paymentTypeManager = $paymentTypeManager;
    }

    /**
     * @return array
     * @throws PGClientExceptionsPaymentRequestException
     */
    protected function buildChoices()
    {
        $choices = array();

        $codes = $this->paymentTypeManager->getCodes();

        foreach ($codes as $code) {
            $choices[$code] = $this->translate($code);
        }

        return $choices;
    }

    protected function getTranslationRoot()
    {
        return 'data.payment_types';
    }
}
