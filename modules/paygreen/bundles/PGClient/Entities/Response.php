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
 * Class PGClientEntitiesResponse
 * @package PGClient\Entities
 */
class PGClientEntitiesResponse
{
    /** @var PGClientEntitiesRequest The original request. */
    private $request;

    /** @var bool */
    private $success;

    /** @var int */
    private $code;

    /** @var string */
    private $message = null;

    /** @var object|array Data of the response.*/
    public $data;

    /**
     * PGClientEntitiesResponse constructor.
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        $this->success = (bool) $data->success;
        $this->code = (int) $data->code;
        $this->message = (string) $data->message;

        $this->data = $data->data;
    }

    /**
     * @param PGClientEntitiesRequest $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return PGClientEntitiesRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return null|string
     */
    public function getMessage()
    {
        return $this->message;
    }

    public static function buildFromObject(stdClass $data)
    {
        if (empty($data)
            || !property_exists($data, 'success')
            || !property_exists($data, 'message')
            || !property_exists($data, 'code')
            || !property_exists($data, 'data')
        ) {
            throw new PGClientExceptionsMalformedResponseException("Malformed response.");
        }

        return new PGClientEntitiesResponse($data);
    }
}
