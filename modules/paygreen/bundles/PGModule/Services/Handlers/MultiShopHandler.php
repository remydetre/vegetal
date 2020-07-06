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

class PGModuleServicesHandlersMultiShopHandler extends PGFrameworkFoundationsAbstractObject
{
    /** @var PGFrameworkServicesLogger */
    private $logger;

    public function __construct(PGFrameworkServicesLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return bool
     */
    public function isShopContext()
    {
        return (Shop::getContext() == Shop::CONTEXT_SHOP);
    }

    /**
     * @return bool
     */
    public function isActivated()
    {
        return Shop::isFeatureActive();
    }

    public function getShopPrimaries()
    {
        return Shop::getShops(false, null, true);
    }

    /**
     * @param string $title
     * @param string $icon
     * @return PGFrameworkComponentsResponsesTemplateResponse
     */
    public function buildOnlyShopLevelResponse($title, $icon)
    {
        $response = new PGFrameworkComponentsResponsesTemplateResponse();

        $response
            ->setTemplate('views/templates/admin', 'only-shop-level')
            ->addData('title', $title)
            ->addData('icon', $icon)
        ;

        return $response;
    }
}
