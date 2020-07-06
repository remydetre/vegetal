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

class PGServerServicesRenderersTransformersPaygreenModuleToArrayTransformer
{
    /** @var PGFrameworkServicesNotifier */
    private $notifier;

    public function __construct(PGFrameworkServicesNotifier $notifier)
    {
        $this->notifier = $notifier;
    }

    /**
     * @param PGServerComponentsResponsesPaygreenModuleResponse $response
     * @return PGServerComponentsResponsesArrayResponse
     * @throws Exception
     */
    public function process(PGServerComponentsResponsesPaygreenModuleResponse $response)
    {
        $newResponse = new PGServerComponentsResponsesArrayResponse($response);

        $newResponse->setData(array(
            'success' => $response->isSuccess(),
            'data' => $response->getData(),
            'notices' => $this->notifier->collect()
        ));

        return $newResponse;
    }
}
