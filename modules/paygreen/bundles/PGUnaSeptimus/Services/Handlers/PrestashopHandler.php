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

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class PGUnaSeptimusServicesHandlersPrestashopHandler extends PGModuleFoundationsAbstractPrestashopHandler
{
    /**
     * @inheritDoc
     * @return PaymentOption
     * @throws Exception
     */
    public function getPaymentOption(PGDomainInterfacesEntitiesButtonInterface $button)
    {
        /** @var PGFrameworkServicesHandlersPictureHandler $mediaHandler */
        $mediaHandler = $this->getService('handler.picture');

        /** @var PGServerServicesLinker $linker */
        $linker = $this->getService('linker');

        $displayType = $button->getDisplayType();

        if (!in_array($displayType, array('DEFAULT', 'PICTURE', 'TEXT'))) {
            $displayType = 'DEFAULT';
        }

        $localPaymentOption = new PaymentOption();

        if (in_array($displayType, array('DEFAULT', 'PICTURE'))) {
            $url = $mediaHandler->getButtonFinalUrl($button);
            $localPaymentOption->setLogo($url);
        }

        if (in_array($displayType, array('DEFAULT', 'TEXT'))) {
            $localPaymentOption->setCallToActionText($button->getLabel());
        }

        $action = $linker->buildFrontOfficeUrl('front.payment.validation', array(
            'id' => $button->id()
        ));

        $localPaymentOption->setAction($action);

        return $localPaymentOption;
    }

    public function getButtonDisplayTypes()
    {
        return array(
            'DEFAULT' => 'button.form.fields.display_type.values.all',
            'PICTURE' => 'button.form.fields.display_type.values.picture',
            'TEXT' => 'button.form.fields.display_type.values.text'
        );
    }

    public function getParentMenuName()
    {
        return 'AdminParentPayment';
    }

    public function isPictureHeightUsage()
    {
        return false;
    }
}
