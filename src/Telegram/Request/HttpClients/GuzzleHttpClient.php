<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\HttpClients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramConnectionException;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramException;

class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * HTTP client.
     * @var Client
     */
    protected $client;

    /**
     * @var PromiseInterface[]
     */
    private static $promises = [];


    public function __construct(?Client $client = null)
    {
        $this->client = $client ? : new Client(config('telegram.guzzle'));
    }


    /**
     * Unwrap Promises.
     * @throws \Throwable
     */
    public function __destruct()
    {
        Promise\unwrap(self::$promises);
    }

    /**
     * {@inheritdoc}
     * @param $url
     * @param $method
     * @param array $headers
     * @param array $options
     * @param $isAsyncRequest
     * @return PromiseInterface|mixed
     */
    public function send(
        $url,
        $method,
        array $headers,
        array $options,
        $isAsyncRequest
    )
    {
        $response = $this->client->requestAsync($method, $url, $options);

        if ($isAsyncRequest)
            self::$promises[] = $response;
        else
            $response = $response->wait();

        return $response;
    }
}
