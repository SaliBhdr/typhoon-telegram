<?php

namespace SaliBhdr\TyphoonTelegram\Telegram\Response;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Request as TelegramRequest;

/**
 * Class Response.
 *
 * Handles the response from Telegram API.
 */
class Response
{
    /**
     * @var null|int The HTTP status code response from API.
     */
    protected $httpStatusCode;

    /**
     * @var array The headers returned from API request.
     */
    protected $headers;

    /**
     * @var string The raw body of the response from API request.
     */
    protected $body;

    /**
     * @var array The decoded body of the API response.
     */
    protected $decodedBody = [];

    /**
     * @var string API Endpoint used to make the request.
     */
    protected $endPoint;

    /**
     * @var TelegramRequest The original request that returned this response.
     */
    protected $request;

    /**
     * Gets the relevant data from the Http client.
     *
     * @param TelegramRequest $request
     * @param ResponseInterface|PromiseInterface $response
     */
    public function __construct(TelegramRequest $request, $response)
    {
        if ($response instanceof ResponseInterface) {
            $this->httpStatusCode = $response->getStatusCode();
            $this->body = $response->getBody();
            $this->headers = $response->getHeaders();

            $this->decodeBody();
        } elseif ($response instanceof PromiseInterface) {
            $this->httpStatusCode = NULL;
        } else {
            throw new \InvalidArgumentException(
                'Second constructor argument "response" must be instance of ResponseInterface or PromiseInterface'
            );
        }

        $this->request = $request;
        $this->endPoint = (string) $request->getEndpoint();
    }

    /**
     * Return the original request that returned this response.
     *
     * @return TelegramRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Gets the HTTP status code.
     * Returns NULL if the request was asynchronous since we are not waiting for the response.
     *
     * @return null|int
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * Gets the Request Endpoint used to get the response.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endPoint;
    }

    /**
     * Return the bot access token that was used for this request.
     *
     * @return string|null
     */
    public function getAccessToken()
    {
        return $this->request->getAccessToken();
    }

    /**
     * Return the HTTP headers for this response.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Return the raw body response.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Return the decoded body response.
     *
     * @return array
     */
    public function getDecodedBody()
    {
        return $this->decodedBody;
    }

    /**
     * Checks if response is an error.
     *
     * @return bool
     */
    public function isError()
    {
        return isset($this->decodedBody['ok']) && ($this->decodedBody['ok'] === FALSE);
    }

    /**
     * Converts raw API response to proper decoded response.
     */
    public function decodeBody()
    {
        $this->decodedBody = json_decode($this->body, TRUE);

        if ($this->decodedBody === NULL) {
            $this->decodedBody = [];
            parse_str($this->body, $this->decodedBody);
        }

        if (!is_array($this->decodedBody)) {
            $this->decodedBody = [];
        }
    }
}
