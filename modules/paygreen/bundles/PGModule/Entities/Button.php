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

/**
 * Class PGModuleEntitiesButton
 *
 * @package PGModule\Entities
 * @method PGLocalEntitiesButton getLocalEntity()
 */
class PGModuleEntitiesButton extends PGFrameworkFoundationsAbstractEntityWrapped implements PGDomainInterfacesEntitiesButtonInterface
{
    protected function hydrateFromLocalEntity($localEntity)
    {
        // Do nothing.
    }

    /**
     * @inheritdoc
     */
    public function id()
    {
        return (int) $this->getLocalEntity()->id;
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return (string) $this->getLocalEntity()->label;
    }

    /**
     * @inheritdoc
     */
    public function setLabel($label)
    {
        $this->getLocalEntity()->label = $label;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getImageSrc()
    {
        return (string) $this->getLocalEntity()->image;
    }

    /**
     * @inheritdoc
     */
    public function setImageSrc($image)
    {
        $this->getLocalEntity()->image = $image;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getImageHeight()
    {
        return (int) $this->getLocalEntity()->height;
    }

    /**
     * @inheritdoc
     */
    public function setImageHeight($height)
    {
        $this->getLocalEntity()->height = (int) $height;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPosition()
    {
        return (int) $this->getLocalEntity()->position;
    }

    /**
     * @inheritdoc
     */
    public function setPosition($position)
    {
        $this->getLocalEntity()->position = (int) $position;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDisplayType()
    {
        return (string) $this->getLocalEntity()->displayType;
    }

    /**
     * @inheritdoc
     */
    public function setDisplayType($displayType)
    {
        $this->getLocalEntity()->displayType = $displayType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIntegration()
    {
        return (string) $this->getLocalEntity()->integration;
    }

    /**
     * @inheritdoc
     */
    public function setIntegration($integration)
    {
        $this->getLocalEntity()->integration = $integration;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMaxAmount()
    {
        return (int) $this->getLocalEntity()->maxAmount;
    }

    /**
     * @inheritdoc
     */
    public function setMaxAmount($maxAmount)
    {
        $this->getLocalEntity()->maxAmount = (float) $maxAmount;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMinAmount()
    {
        return (int) $this->getLocalEntity()->minAmount;
    }

    /**
     * @inheritdoc
     */
    public function setMinAmount($minAmount)
    {
        $this->getLocalEntity()->minAmount = (float) $minAmount;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPaymentMode()
    {
        return (string) $this->getLocalEntity()->paymentMode;
    }

    /**
     * @inheritdoc
     */
    public function setPaymentMode($paymentMode)
    {
        $this->getLocalEntity()->paymentMode = $paymentMode;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPaymentType()
    {
        return (string) $this->getLocalEntity()->paymentType;
    }

    /**
     * @inheritdoc
     */
    public function setPaymentType($paymentType)
    {
        $this->getLocalEntity()->paymentType = $paymentType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFirstPaymentPart()
    {
        return (int) $this->getLocalEntity()->firstPaymentPart;
    }

    /**
     * @inheritdoc
     */
    public function setFirstPaymentPart($firstPaymentPart)
    {
        $this->getLocalEntity()->firstPaymentPart = (int) $firstPaymentPart;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPaymentNumber()
    {
        return (int) $this->getLocalEntity()->paymentNumber;
    }

    /**
     * @inheritdoc
     */
    public function setPaymentNumber($paymentNumber)
    {
        $this->getLocalEntity()->paymentNumber = (int) $paymentNumber;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPaymentReport()
    {
        return (string) $this->getLocalEntity()->paymentReport;
    }

    /**
     * @inheritdoc
     */
    public function setPaymentReport($paymentReport)
    {
        $this->getLocalEntity()->paymentReport = $paymentReport;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDiscount()
    {
        return $this->getLocalEntity()->discount;
    }

    /**
     * @inheritdoc
     */
    public function setDiscount($discount)
    {
        $this->getLocalEntity()->discount = (int) $discount;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isOrderRepeated()
    {
        return (bool) $this->getLocalEntity()->orderRepeated;
    }

    /**
     * @inheritdoc
     */
    public function setOrderRepeated($isOrderRepeated)
    {
        $this->getLocalEntity()->orderRepeated = $isOrderRepeated;

        return $this;
    }

    public function toArray()
    {
        return array(
            'id' => $this->id(),
            'label' => $this->getLabel(),
            'image' => $this->getImageSrc(),
            'height' => $this->getImageHeight(),
            'position' => $this->getPosition(),
            'displayType' => $this->getDisplayType(),
            'integration' => $this->getIntegration(),
            'maxAmount' => $this->getMaxAmount(),
            'minAmount' => $this->getMinAmount(),
            'paymentMode' => $this->getPaymentMode(),
            'paymentType' => $this->getPaymentType(),
            'firstPaymentPart' => $this->getFirstPaymentPart(),
            'paymentNumber' => $this->getPaymentNumber(),
            'paymentReport' => $this->getPaymentReport(),
            'discount' => $this->getDiscount(),
            'orderRepeated' => $this->isOrderRepeated(),
        );
    }
}
