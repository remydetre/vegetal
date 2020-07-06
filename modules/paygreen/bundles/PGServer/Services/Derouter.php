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

class PGServerServicesDerouter
{
    /** @var PGServerServicesAggregatorsDeflectorAggregator */
    private $deflectorAggregator;

    /** @var PGFrameworkServicesLogger */
    private $logger;

    public function __construct(
        PGServerServicesAggregatorsDeflectorAggregator $deflectorAggregator,
        PGFrameworkServicesLogger $logger
    ) {
        $this->deflectorAggregator = $deflectorAggregator;
        $this->logger = $logger;
    }

    /**
     * @param PGServerFoundationsAbstractRequest $request
     * @param array $deflectorNames
     * @return PGServerInterfacesDeflectorInterface|null
     */
    public function getMatchingDeflector(PGServerFoundationsAbstractRequest $request, array $deflectorNames)
    {
        try {
            foreach ($deflectorNames as $deflectorName) {
                /** @var PGServerInterfacesDeflectorInterface $deflector */
                $deflector = $this->deflectorAggregator->getDeflector($deflectorName);

                if ($deflector->isMatching($request)) {
                    $this->logger->debug("Found matching deflector : '$deflectorName'.");
                    return $deflector;
                }
            }
        } catch (Exception $exception) {
            $list = implode(', ', $deflectorNames);
            $this->logger->error("An error occurred when select matching deflector in list [$list] : " . $exception->getMessage(), $exception);
        }

        return null;
    }

    /**
     * @param PGServerFoundationsAbstractRequest $request
     * @param array $deflectorNames
     * @return PGServerFoundationsAbstractResponse
     * @throws Exception
     */
    public function preprocess(PGServerFoundationsAbstractRequest $request, array $deflectorNames)
    {
        foreach ($deflectorNames as $deflectorName) {
            /** @var PGServerInterfacesDeflectorInterface $deflector */
            $deflector = $this->deflectorAggregator->getDeflector($deflectorName);

            if ($deflector instanceof PGServerInterfacesDeflectorInterface) {
                $response = $deflector->process($request);
            } else {
                $class = get_class($deflector);
                $this->logger->error("Service '$deflectorName' is not a valid Preprocessor : '$class'.");
            }
        }

        return $response;
    }
}
