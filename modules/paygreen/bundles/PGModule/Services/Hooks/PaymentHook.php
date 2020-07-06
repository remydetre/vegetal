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
 * Class PGModuleServicesHooksPaymentHook
 */
class PGModuleServicesHooksPaymentHook
{
    /** @var PGViewServicesHandlersViewHandler */
    private $viewHandler;

    /** @var PGFrameworkServicesLogger */
    private $logger;

    /** @var PGFrameworkServicesSettings */
    private $settings;

    /** @var PGServerServicesLinker */
    private $linker;

    public function __construct(
        PGViewServicesHandlersViewHandler $viewHandler,
        PGServerServicesLinker $linker,
        PGFrameworkServicesSettings $settings,
        PGFrameworkServicesLogger $logger
    ) {
        $this->viewHandler = $viewHandler;
        $this->linker = $linker;
        $this->settings = $settings;
        $this->logger = $logger;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function displayPaymentReturn()
    {
        $html = '';

        try {
            $url = $this->linker->buildLocalUrl('order', array('id_order' => Tools::getValue('id_order')));

            $html = $this->viewHandler->renderTemplate('block-payment-confirmation', array(
                'message' => $this->settings->get('notice_payment_accepted'),
                'url' => array(
                    'link' => $url,
                    'text' => 'frontoffice.payment.results.order.validate.link'
                )
            ));
        } catch (Exception $exception) {
            $this->logger->critical("Error during DisplayPaymentReturn hook : " . $exception->getMessage(), $exception);
        }

        return $html;
    }
}
