<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 4/30/2019
 * Time: 9:52 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;

use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\RequestMethodInvalidException;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\SendAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Finals\ApiRequest;

class SendDynamic extends SendAbstract
{
    protected $method;

    protected $requestMethod;

    /**
     * Send constructor.
     *
     * @param $requestMethod
     * @param $method
     *
     * @throws RequestMethodInvalidException
     * @throws \SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramSDKException
     */
    public function __construct($requestMethod, $method)
    {
        if (!isset($method) && !in_array($method, [ApiRequest::GET, ApiRequest::POST, ApiRequest::MULTIPART]))
            throw new RequestMethodInvalidException();

        $this->method = $method;

        $this->requestMethod = $requestMethod;

        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    protected function getRequiredParams() : array
    {
        return [];
    }

    protected function addOptionalParams() : void
    {
        return;
    }

    public function method() : string
    {
        return $this->method;
    }

    protected function requiredParams() : array
    {
        return [];
    }

}