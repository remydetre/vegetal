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

class PGLocalServicesPrestashopHandler extends PGFrameworkFoundationsAbstractObject implements PGLocalInterfacesPrestashopHandlerInterface
{
    /** @var PGLocalInterfacesPrestashopHandlerInterface */
    private $handler;

    /** @var string */
    private $version;

    public function __construct()
    {
        $this->version = Tools::substr(_PS_VERSION_, 0, 3);
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    public function addHandler($handlerServiceName, $version)
    {
        if ($version === $this->version) {
            $this->handler = $this->getService($handlerServiceName);
        }
    }

    /**
     * @return PGLocalInterfacesPrestashopHandlerInterface
     * @throws Exception
     */
    public function getHandler()
    {
        if ($this->handler === null) {
            $message = "Unable to find handler for current Prestashop version : '{$this->version}'.";
            throw new Exception($message);
        }

        return $this->handler;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getPaymentOption(PGDomainInterfacesEntitiesButtonInterface $button)
    {
        return $this->getHandler()->getPaymentOption($button);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getButtonDisplayTypes()
    {
        return $this->getHandler()->getButtonDisplayTypes();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getParentMenuName()
    {
        return $this->getHandler()->getParentMenuName();
    }
}
