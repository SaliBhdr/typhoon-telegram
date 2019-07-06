<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/28/2019
 * Time: 1:29 PM
 */

namespace SaliBhdr\TyphoonTelegram\Laravel\Exceptions;

use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramException;

class LumenExceptionHandler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \Exception $e
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     * @throws TelegramException
     */
    public function render($request, \Exception $e)
    {
        if ($e instanceof TelegramException)
            return Handler::init($request, $e)->render();

        return parent::render($request, $e);
    }
}