<?php
/**
 * User: Salar Bahador
 * Date: 4/28/2019
 * Time: 9:50 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SaliBhdr\TyphoonTelegram\Laravel\Facades\Telegram;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramParamsRequiredException;

/**
 * extend from MethodAbstract if the api method doesn't have chat_id
 *
 * Class MethodAbstract
 * @package SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts
 */
abstract class MethodAbstract
{
    protected $chatId;

    protected $paramsIsSetManually = false;

    protected $params = [];

    /** @var string|int $botName */
    protected $botName;

    abstract protected function getRequiredParams() : array;

    abstract protected function addOptionalParams() : void;

    abstract protected function requiredParams() : array;

    /**
     * BaseAbstract constructor.
     */
    public function __construct()
    {
        $this->botName = config('telegram.default_bot');
        $this->setApiInstance();
    }

    public function setParams(array $params = [])
    {
        $this->params = $params;

        $this->paramsIsSetManually = true;

        return $this;
    }

    protected function isParamsSetManually() : bool
    {
        return $this->paramsIsSetManually;
    }

    /**
     * @param array $requiredParams
     *
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
        return;
    }

    /**
     * @return array
     * @throws \SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramParamsRequiredException
     */
    public function getParams() : array
    {
        if (!$this->isParamsSetManually()) {

            $this->params = $this->getRequiredParams();
            $this->addOptionalParams();
        }

        $this->validateRequired($this->requiredParams());

        $this->extraValidation();

        return $this->params;
    }

    protected function addParam(string $name, $value)
    {
        if (!is_null($value) && !array_key_exists($name, $this->params))
            $this->params[$name] = $value;

        return $this;
    }

    /**    * Handle dynamic static method calls into the method.
     *
     * @param  string $method
     * @param  array $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    /**
     * @param string $botName
     *
     * @return MethodAbstract
     */
    public function bot(string $botName)
    {
        $this->botName = $botName;

        return $this;
    }


    /**
     * @return void
     */
    protected function setApiInstance()
    {
        Telegram::makeRequestInstance('POST', $this->method(), $this->params);
    }

    /**
     * @return Collection
     */
    public function send()
    {
        $this->selectBot();

        return Telegram::send($this);
    }

    /**
     * @return void
     */
    protected function selectBot()
    {
        Telegram::bot($this->botName);
    }

    abstract public function method();

    public function reset(...$params)
    {
        foreach ($params as $param){
            $param = Str::camel($param);

            if(property_exists($this,$param)){
                $this->{$param} = null;
            }
        }
    }
}