<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/28/2019
 * Time: 1:29 PM
 */

namespace SaliBhdr\TyphoonTelegram\Laravel\Exceptions;

use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Exceptions\Handler as ExceptionHandler;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class TelegramExceptionHandler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \Exception|HttpExceptionInterface $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function render($request, $e)
    {
        if (Str::contains($request->getUri(), '/webhook') || $e instanceof TelegramException){
            $data = [
                'ok'          => false,
                'error_code'  => $e->getCode(),
                'description' => $e->getMessage(),
                'exception'   => get_class($e),
                'file'        => $e->getFile(),
                'line'        => $e->getLine()
            ];

            return new JsonResponse(
                $data,
                $this->isHttpException($e) ? $e->getStatusCode() : 500,
                $this->isHttpException($e) ? $e->getHeaders() : [],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return parent::render($request, $e);
    }
}