<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/28/2019
 * Time: 1:29 PM
 */

namespace Salibhdr\TyphoonTelegram\Laravel\Exceptions;

use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;

class LumenExceptionHandler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, \Exception $exception)
    {
        return parent::render($request, $exception);
    }
}