<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 7/6/2019
 * Time: 11:54 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Response\Models;


use SaliBhdr\TyphoonTelegram\Telegram\Response\Response;

class ModelDecorator
{
    /** @var BaseModel $model */
    protected $model;

    /**
     * ModelDecorator constructor.
     *
     * @param Response $response
     * @param $model
     */
    protected function __construct($response,$model)
    {
        if ($response->isError())
            $this->model = new TelegramCollection($response->getDecodedBody());
        else
            $this->model = new $model($response->getDecodedBody());

    }

    /**
     * @param $response
     * @param $model
     *
     * @return ModelDecorator
     */
    public static function make($response,$model)
    {
        return new static($response, $model);
    }

    /**
     * @return BaseModel
     */
    public function respond() : BaseModel
    {
        return $this->model;
    }
}