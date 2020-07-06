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

use Symfony\Component\HttpFoundation\Session\Session;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

/**
 * Class PGUnaSeptimusServicesHandlersSessionHandler
 * @package PGUnaSeptimus\Services\Handlers
 */
class PGUnaSeptimusServicesHandlersSessionHandler extends PGFrameworkServicesHandlersSessionHandler
{
    /** @var Session */
    private $session = null;

    public function __construct(PGFrameworkServicesLogger $logger)
    {
        parent::__construct($logger);

        $container = SymfonyContainer::getInstance();

        if ($container !== null) {
            $this->session = $container->get('session');
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function get($var)
    {
        if ($this->has($var)) {
            return $this->session ? $this->session->get($var) : parent::get($var);
        }

        return null;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function set($var, $value)
    {
        $this->session ? $this->session->set($var, $value) : parent::set($var, $value);

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function rem($var)
    {
        if ($this->has($var)) {
            $this->session ? $this->session->remove($var) : parent::rem($var);
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function raz()
    {
        $this->session ? $this->session->clear() : parent::raz();

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function has($var)
    {
        return $this->session ? $this->session->has($var) : parent::has($var);
    }
}
