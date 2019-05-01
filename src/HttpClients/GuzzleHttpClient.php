<?php

namespace Salibhdr\TyphoonTelegram\HttpClients;

use GuzzleHttp\Client;
use Telegram\Bot\HttpClients\GuzzleHttpClient as BaseGuzzleHttpClient;

/*** Class GuzzleHttpClient.
 */
class GuzzleHttpClient extends BaseGuzzleHttpClient
{
    public function __construct(?Client $client = null)
    {
        $client = $client ?: new Client($this->getClientOptions());

        parent::__construct($client);
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
}
