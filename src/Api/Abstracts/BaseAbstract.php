<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:50 PM
 */

namespace SaliBhdr\TyphoonTelegram\Api\Abstracts;

use SaliBhdr\TyphoonTelegram\Api\Interfaces\BaseInterface;
use SaliBhdr\TyphoonTelegram\Exceptions\TelegramParamsRequiredException;
use SaliBhdr\TyphoonTelegram\Facades\Telegram;

abstract class BaseAbstract implements BaseInterface
{

    protected $chatId;

    protected $paramsIsSetManually = false;

    protected $params = [];

    abstract protected function addParams(): void;

    abstract protected function addOptionalParams(): void;

    abstract protected function requiredParams(): array;


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

    protected function extraValidation() {}

    /**
     * @return array
     * @throws \SaliBhdr\TyphoonTelegram\Exceptions\TelegramParamsRequiredException
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
        if (!is_null($value) && !array_key_exists($name,$this->params))
            $this->params[$name] = $value;

        return $this;
    }

    public function extraParams(array $extraParams)
    {
        if (!empty($extraParams)) {
            $this->params = array_merge($extraParams,$this->params);
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

    public function send()
    {
        return Telegram::send($this);
    }
}