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

class PGViewServicesPluginsSmartyViewInjecter
{
    /** @var PGViewServicesHandlersViewHandler */
    private $viewHandler;

    public function __construct(PGViewServicesHandlersViewHandler $viewHandler)
    {
        $this->viewHandler = $viewHandler;
    }

    public function insertView(array $params)
    {
        if (!array_key_exists('name', $params)) {
            throw new Exception("SmartyViewInjecter require 'name' parameter.");
        }

        $name = $params['name'];
        unset($params['name']);

        $data = array_key_exists('data', $params) ? $params['data'] : $params;

        return $this->viewHandler->renderView($name, $data);
    }

    public function insertTemplate(array $params)
    {
        if (!array_key_exists('name', $params)) {
            throw new Exception("SmartyTemplateInjecter require 'name' parameter.");
        }

        $name = $params['name'];
        unset($params['name']);

        $data = array_key_exists('data', $params) ? $params['data'] : $params;

        return $this->viewHandler->renderTemplate($name, $data);
    }
}
