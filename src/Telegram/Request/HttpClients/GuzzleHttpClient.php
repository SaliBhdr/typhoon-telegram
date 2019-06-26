<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\HttpClients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramSDKException;

/*** Class GuzzleHttpClient.
 */
class GuzzleHttpClient
{
    /**
     * HTTP client.
     *
     * @var Client
     */
    protected $client;

    /**
     * @var PromiseInterface[]
     */
    private static $promises = [];

    /**
     * Timeout of the request in seconds.
     *
     * @var int
     */
    protected $timeOut = 30;

    /**
     * Connection timeout of the request in seconds.
     *
     * @var int
     */
    protected $connectTimeOut = 10;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?: new Client($this->getClientOptions());
    }

    /**    * gets client extra options
     *
     * @return array
     */
    public function getClientOptions(): array
    {
        $options = [];

        $this->addProxyToOptions($options);
        $this->sslCertificate($options);

        return $options;
    }

    /**    * adds http proxy to guzzle client
     *
     * @param $options
     */
    protected function addProxyToOptions(&$options)
    {
        $httpProxy = config('telegram.guzzle.http-proxy');

        if (!$httpProxy['use-proxy']) {
            return;
        }

        $proxy = '';

        if (isset($httpProxy['username']) && $httpProxy['username'])
            $proxy .= "{$httpProxy['username']}";

        if (isset($httpProxy['password']) && $httpProxy['password'])
            $proxy .= ":{$httpProxy['password']}";

        if ($proxy)
            $proxy .= "@";

        if (isset($httpProxy['ip']) && isset($httpProxy['port']) && $httpProxy['ip'] && $httpProxy['port'])
            $proxy = "{$proxy}{$httpProxy['ip']}:{$httpProxy['port']}";

        if ($proxy)
            $options['proxy'] = $proxy;

    }

    public function sslCertificate(&$options)
    {
        $options['verify'] = config('telegram.guzzle.ssl-certificate');
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
     * Sets HTTP client.
     *
     * @param Client $client
     *
     * @return GuzzleHttpClient
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Gets HTTP client for internal class use.
     *
     * @return Client
     */
    private function getClient()
    {
        return $this->client;
    }

    /**
     * {@inheritdoc}
     * @throws TelegramSDKException
     */
    public function send(
        $url,
        $method,
        array $headers = [],
        array $options = [],
        $timeOut = 30,
        $isAsyncRequest = false,
        $connectTimeOut = 10
    ) {
        $this->timeOut = $timeOut;
        $this->connectTimeOut = $connectTimeOut;

        $body = isset($options['body']) ? $options['body'] : null;
        $options = $this->getOptions($headers, $body, $options, $timeOut, $isAsyncRequest, $connectTimeOut);

        try {
            $response = $this->getClient()->requestAsync($method, $url, $options);

            if ($isAsyncRequest) {
                self::$promises[] = $response;
            } else {
                $response = $response->wait();
            }
        } catch (RequestException $e) {
            $response = $e->getResponse();

            if (!$response instanceof ResponseInterface) {
                throw new TelegramSDKException($e->getMessage(), $e->getCode());
            }
        }

        return $response;
    }

    /**
     * Prepares and returns request options.
     *
     * @param array $headers
     * @param       $body
     * @param       $options
     * @param       $timeOut
     * @param       $isAsyncRequest
     * @param int   $connectTimeOut
     *
     * @return array
     */
    private function getOptions(array $headers, $body, $options, $timeOut, $isAsyncRequest = false, $connectTimeOut = 10)
    {
        $default_options = [
            RequestOptions::HEADERS         => $headers,
            RequestOptions::BODY            => $body,
            RequestOptions::TIMEOUT         => $timeOut,
            RequestOptions::CONNECT_TIMEOUT => $connectTimeOut,
            RequestOptions::SYNCHRONOUS     => !$isAsyncRequest,
        ];

        return array_merge($default_options, $options);
    }

    /**
     * @return int
     */
    public function getTimeOut()
    {
        return $this->timeOut;
    }

    /**
     * @return int
     */
    public function getConnectTimeOut()
    {
        return $this->connectTimeOut;
    }
}
