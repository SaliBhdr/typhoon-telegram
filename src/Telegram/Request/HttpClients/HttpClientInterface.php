<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\HttpClients;

/**
 * Interface HttpClientInterface.
 */
interface HttpClientInterface
{
    /**
     * @param            $url
     * @param            $method
     * @param array $headers
     * @param array $options
     * @param bool|false $isAsyncRequest
     * @return mixed
     */
    public function send(
        $url,
        $method,
        array $headers,
        array $options,
        $isAsyncRequest
    );
}
