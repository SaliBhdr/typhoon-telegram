<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/28/2019
 * Time: 1:29 PM
 */

namespace Salibhdr\TyphoonTelegram\Laravel\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class LaravelExceptionHandler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, \Exception $exception)
    {
        return parent::render($request, $exception);
    }
}