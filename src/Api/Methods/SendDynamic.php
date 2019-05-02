<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 4/30/2019
 * Time: 9:52 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Methods;

use Salibhdr\TyphoonTelegram\Api\Abstracts\SendAbstract;
use Salibhdr\TyphoonTelegram\Api\Finals\ApiRequest;
use Salibhdr\TyphoonTelegram\Api\Interfaces\BaseSendMessageInterface;
use Salibhdr\TyphoonTelegram\Exceptions\RequestMethodInvalidException;

class SendDynamic extends SendAbstract implements BaseSendMessageInterface
{
    protected $method;

    protected $requestMethod;

    /**
     * Send constructor.
     * @param $requestMethod
     * @param $method
     * @throws RequestMethodInvalidException
     */
    public function __construct($requestMethod, $method)
    {
        if (!isset($method) && !in_array($method, [ApiRequest::GET, ApiRequest::POST, ApiRequest::MULTIPART]))
            throw new RequestMethodInvalidException();

        $this->method = $method;

        $this->requestMethod = $requestMethod;
    }

    /**
     * @return mixed
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    protected function addParams(): void {}

    protected function addOptionalParams(): void {}

    public function method(): string
    {
        return $this->method;
    }

    protected function requiredParams(): array
    {
        return [];
    }

}