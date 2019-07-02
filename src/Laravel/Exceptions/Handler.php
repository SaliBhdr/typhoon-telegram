<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 7/1/2019
 * Time: 11:09 PM
 */

namespace SaliBhdr\TyphoonTelegram\Laravel\Exceptions;

use Illuminate\Http\Request;
use SaliBhdr\TyphoonTelegram\Telegram\Api;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramException;
use SaliBhdr\TyphoonTelegram\Telegram\Response\CustomResponse;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Response;

class Handler
{
    /** @var Request $request */
    protected $request;

    /** @var TelegramException $e */
    protected $e;

    protected function __construct(Request $request, TelegramException $e)
    {
        $this->request = $request;
        $this->e = $e;
    }

    public static function init($request, TelegramException $e)
    {
        return new static($request, $e);
    }


    /**
     * @return Response
     * @throws TelegramException
     */
    public function render()
    {
        $api = Api::init();
dump($api->getLastRequest()->getParams());
        return new Response(
            $api->getLastRequest(),
            $this->makeResponse()
        );


    }

    private function makeResponse()
    {
        return new CustomResponse(
            $this->getBody(),
            $this->e->getCode(),
            $this->getHeaders()
        );
    }

    private function getBody()
    {
        return [
            'ok'          => false,
            'error_code'  => $this->e->getCode(),
            'description' => $this->e->getMessage(),
            'file'        => $this->e->getFile(),
            'line'        => $this->e->getLine()
        ];
    }

    private function getHeaders()
    {
        return ['Content-Type' => 'application/json'];
    }

}