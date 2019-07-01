<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Request;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramException;
use SaliBhdr\TyphoonTelegram\Telegram\Request\HttpClients\GuzzleHttpClient;
use SaliBhdr\TyphoonTelegram\Telegram\Request\HttpClients\HttpClientInterface;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Response as TelegramResponse;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Request as TelegramRequest;

/**
 * Class Client.
 */
class Client
{
    /**
     * @const string Telegram Bot API URL.
     */
    const BASE_BOT_URL = 'https://api.telegram.org/bot';

    /**
     * @const int The timeout in seconds for a request that contains file uploads.
     */
    const DEFAULT_FILE_UPLOAD_REQUEST_TIMEOUT = 3600;

    /**
     * @const int The timeout in seconds for a request that contains video uploads.
     */
    const DEFAULT_VIDEO_UPLOAD_REQUEST_TIMEOUT = 7200;

    /**
     * @var HttpClientInterface|null HTTP Client
     */
    protected $httpClientHandler;

    /**
     * Instantiates a new Client object.
     *
     * @param HttpClientInterface|null $httpClientHandler
     */
    public function __construct(HttpClientInterface $httpClientHandler = null)
    {
        $this->httpClientHandler = $httpClientHandler ?: new GuzzleHttpClient();
    }

    /**
     * Sets the HTTP client handler.
     *
     * @param HttpClientInterface $httpClientHandler
     */
    public function setHttpClientHandler(HttpClientInterface $httpClientHandler)
    {
        $this->httpClientHandler = $httpClientHandler;
    }

    /**
     * Returns the HTTP client handler.
     *
     * @return HttpClientInterface
     */
    public function getHttpClientHandler()
    {
        return $this->httpClientHandler;
    }

    /**
     * Returns the base Bot URL.
     *
     * @return string
     */
    public function getBaseBotUrl()
    {
        return static::BASE_BOT_URL;
    }

    /**
     * Prepares the API request for sending to the client handler.
     *
     * @param TelegramRequest $request
     *
     * @return array
     */
    public function prepareRequest(TelegramRequest $request)
    {
        $url = $this->getBaseBotUrl().$request->getAccessToken().'/'.$request->getEndpoint();

        return [
            $url,
            $request->getMethod(),
            $request->getHeaders(),
            $request->isAsyncRequest(),
        ];
    }

    /**
     * Send an API request and process the result.
     *
     * @param TelegramRequest $request
     *
     * @throws TelegramException
     *
     * @return TelegramResponse
     */
    public function sendRequest(Request $request)
    {
        list($url, $method, $headers, $isAsyncRequest) = $this->prepareRequest($request);

        $timeOut = $request->getTimeOut();
        $connectTimeOut = $request->getConnectTimeOut();

        if ($method === 'POST') {
            $options = $request->getPostParams();
        } else {
            $options = ['query' => $request->getParams()];
        }

        $rawResponse = $this->httpClientHandler->send($url, $method, $headers, $options, $timeOut, $isAsyncRequest, $connectTimeOut);

        return $this->getResponse($request, $rawResponse);
    }

    /**
     * Creates response object.
     *
     * @param Request                    $request
     * @param ResponseInterface|PromiseInterface $response
     *
     * @return TelegramResponse
     */
    protected function getResponse(Request $request, $response)
    {
        return new TelegramResponse($request, $response);
    }
}
