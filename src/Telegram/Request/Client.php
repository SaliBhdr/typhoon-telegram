<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Request;

use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramException;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Request as TelegramRequest;
use SaliBhdr\TyphoonTelegram\Telegram\Request\HttpClients\GuzzleHttpClient;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Response as TelegramResponse;
use SaliBhdr\TyphoonTelegram\Telegram\Request\HttpClients\HttpClientInterface;

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
     * @var HttpClientInterface|null HTTP Client
     */
    protected $httpClientHandler;

    /**
     * Instantiates a new Client object.
     * @param HttpClientInterface|null $httpClientHandler
     */
    public function __construct(HttpClientInterface $httpClientHandler = null)
    {
        $this->httpClientHandler = $httpClientHandler ?: new GuzzleHttpClient();
    }


    /**
     * Returns the base Bot URL.
     * @return string
     */
    public function getBaseBotUrl()
    {
        return static::BASE_BOT_URL;
    }

    /**
     * Send an API request and process the result.
     * @param TelegramRequest $request
     * @throws TelegramException
     * @return TelegramResponse
     */
    public function sendRequest(Request $request)
    {
        $url = $this->getBaseBotUrl() . $request->getAccessToken() . '/' . $request->getEndpoint();

        $options = $request->getMethod() === 'POST'
            ? $request->getPostParams()
            : $request->getParams();

        $rawResponse = $this->httpClientHandler->send(
            $url,
            $request->getMethod(),
            $request->getHeaders(),
            $options,
            $request->isAsyncRequest());

        return new TelegramResponse($request, $rawResponse);
    }

}
