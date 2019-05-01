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
    protected $sendMethod;

    protected $requestMethod;

    /**
     * Send constructor.
     * @param $requestMethod
     * @param $sendMethod
     * @throws RequestMethodInvalidException
     */
    public function __construct($requestMethod, $sendMethod)
    {
        if (!isset($sendMethod) && !in_array($sendMethod, [ApiRequest::GET, ApiRequest::POST, ApiRequest::MULTIPART]))
            throw new RequestMethodInvalidException();

        $this->sendMethod = $sendMethod;

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

    public function sendMethod(): string
    {
        return $this->sendMethod;
    }

    protected function requiredParams(): array
    {
        return [];
    }

}