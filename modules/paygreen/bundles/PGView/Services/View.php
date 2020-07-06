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

class PGViewServicesView implements PGViewInterfacesViewInterface
{
    /** @var PGViewServicesHandlersViewHandler */
    private $viewHandler;

    private $data = array();

    private $template;

    /**
     * @param PGViewServicesHandlersViewHandler $viewHandler
     */
    public function setViewHandler(PGViewServicesHandlersViewHandler $viewHandler)
    {
        $this->viewHandler = $viewHandler;
    }

    /**
     * @inheritDoc
     */
    public function setData(array $data)
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function getTemplate()
    {
        return $this->template;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function render()
    {
        return $this->viewHandler->renderTemplate(
            $this->getTemplate(),
            $this->getData()
        );
    }

    protected function get($key)
    {
        return $this->has($key) ? $this->data[$key] : null;
    }

    protected function set($key, $val)
    {
        $this->data[$key] = $val;

        return $this;
    }

    protected function rem($key)
    {
        if ($this->has($key)) {
            unset($this->data[$key]);
        }

        return $this;
    }

    protected function has($key)
    {
        return array_key_exists($key, $this->data);
    }
}
