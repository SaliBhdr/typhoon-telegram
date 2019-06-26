<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:50 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts;

use SaliBhdr\TyphoonTelegram\Telegram\Api;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramParamsRequiredException;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Interfaces\BaseInterface;

abstract class BaseAbstract implements BaseInterface
{

    protected $chatId;

    protected $paramsIsSetManually = false;

    protected $params = [];

    /** @var Api $apiInstance */
    protected $apiInstance;

    protected $botName;

    abstract protected function addParams(): void;

    abstract protected function addOptionalParams(): void;

    abstract protected function requiredParams(): array;

    /**
     * BaseAbstract constructor.
     * @throws \SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramSDKException
     */
    public function __construct()
    {
        $this->setApiInstance();
    }

    public function setParams(array $params = [])
    {
        $this->params = $params;

        $this->paramsIsSetManually = true;

        return $this;
    }

    protected function isParamsSetManually(): bool
    {
        return $this->paramsIsSetManually;
    }

    /**
     * @param array $requiredParams
     * @throws TelegramParamsRequiredException
     */
    protected function validateRequired(array $requiredParams)
    {
        $paramsWithError = [];

        foreach ($requiredParams as $requiredParam) {
            if (!isset($this->params[$requiredParam]))
                $paramsWithError[] = $requiredParam;
        }

        if (!empty($paramsWithError) || (!isset($this->params) || empty($this->params)))
            throw new TelegramParamsRequiredException($paramsWithError);
    }

    protected function extraValidation()
    {
    }

    /**
     * @return array
     * @throws \SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramParamsRequiredException
     */
    public function getParams(): array
    {
        if (!$this->isParamsSetManually()) {

            $this->addParams();
            $this->addOptionalParams();
        }

        $this->validateRequired($this->requiredParams());

        $this->extraValidation();

        return $this->params;
    }

    public function extraParam(string $name, $value)
    {
        if (!is_null($value) && !array_key_exists($name, $this->params))
            $this->params[$name] = $value;

        return $this;
    }

    public function extraParams(array $extraParams)
    {
        if (!empty($extraParams)) {
            $this->params = array_merge($extraParams, $this->params);
        }

        return $this;
    }

    /**    * Handle dynamic static method calls into the method.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    /**
     * @param string $botName
     */
    public function bot(string $botName)
    {
        $this->botName = $botName;
    }


    /**
     * @throws \SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramSDKException
     */
    protected function setApiInstance()
    {
        $this->apiInstance = Api::init();
    }

    /**
     * @return mixed
     * @throws TelegramParamsRequiredException
     * @throws \SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramSDKException
     */
    public function send()
    {
        return $this->selectBot()->send($this);
    }

    /**
     * @return Api
     */
    private function selectBot()
    {
        if (is_null($this->botName))
            $this->apiInstance->setAccessToken(config('telegram.default_bot_token', false));
        else
            $this->apiInstance->bot($this->botName);

        return $this->apiInstance;
    }
}