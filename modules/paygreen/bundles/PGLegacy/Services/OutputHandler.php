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

class PGLegacyServicesOutputHandler extends PGFrameworkFoundationsAbstractObject
{
    private $localModule;

    public function __construct(Paygreen $localModule)
    {
        $this->localModule = $localModule;
    }

    /**
     * @param PGFrameworkComponentsResponsesHTMLResponse $response
     * @return string
     * @throws SmartyException
     */
    public function buildHTMLOutput(PGFrameworkComponentsResponsesHTMLResponse $response)
    {
        foreach ($response->getLinks() as $link) {
            switch ($link['type']) {
                case 'JS':
                    $this->getContext()->controller->addJS($this->getLocalPath() . $link['src']);
                    break;
                case 'CSS':
                    $this->getContext()->controller->addCSS($this->getLocalPath() . $link['src']);
                    break;
                default:
                    throw new LogicException("Unrecognized link type : '{$link['type']}'.");
            }
        }

        return $response->getContent();
    }

    /**
     * @param PGFrameworkComponentsResponsesTemplateResponse $response
     * @return string
     * @throws SmartyException
     */
    public function buildTemplateOutput(PGFrameworkComponentsResponsesTemplateResponse $response)
    {
        /** @var Smarty $smarty */
        $smarty = $this->getContext()->smarty;

        if ($response->getNamespace()) {
            $smarty->assign($response->getNamespace(), $response->getData());
        } else {
            foreach ($response->getData() as $key => $val) {
                $smarty->assign($key, $val);
            }
        }


        foreach ($response->getResources() as $link) {
            switch ($link['type']) {
                case 'JS':
                    $this->getContext()->controller->addJS($this->getLocalPath() . $link['src']);
                    break;
                case 'CSS':
                    $this->getContext()->controller->addCSS($this->getLocalPath() . $link['src']);
                    break;
                default:
                    throw new LogicException("Unrecognized link type : '{$link['type']}'.");
            }
        }

        $filename = $response->getTemplateName() . '.tpl';
        $src = $this->getLocalPath() . $response->getTemplatePath() . '/' . $filename;

        return $smarty->fetch($src);
    }

    /**
     * @param PGFrameworkComponentsResponsesChainQualifiedMessagesResponse $response
     * @return string
     * @throws Exception
     */
    public function buildMessagesOutput(PGFrameworkComponentsResponsesChainQualifiedMessagesResponse $response)
    {
        /** @var PGFrameworkServicesHandlersTranslatorHandler $translator */
        $translator = $this->getService('handler.translator');

        $output = '';

        foreach ($response->getMessages() as $message) {
            switch ($message['type']) {
                case PGFrameworkComponentsResponsesChainQualifiedMessagesResponse::FAILURE:
                    $output .= $this->localModule->displayError($translator->get($message['text']));
                    break;

                case PGFrameworkComponentsResponsesChainQualifiedMessagesResponse::SUCCESS:
                    $output .= $this->localModule->displayConfirmation($translator->get($message['text']));
                    break;

                case PGFrameworkComponentsResponsesChainQualifiedMessagesResponse::NOTICE:
                    $output .= $this->localModule->displayInformation($translator->get($message['text']));
                    break;

                default:
                    throw new Exception("Unrecognized result type : '{$message['type']}'.");
            }
        }

        return $output;
    }

    private function getContext()
    {
        return $this->localModule->getContext();
    }

    private function getLocalPath()
    {
        return $this->localModule->getLocalPath();
    }
}
