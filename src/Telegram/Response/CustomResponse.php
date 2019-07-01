<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 7/2/2019
 * Time: 12:29 AM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Response;


class CustomResponse
{

    /** @var array $body */
    protected $body;

    /** @var int $status */
    protected $status;
    /**
     * @var array $headers
     */
    protected $headers;

    public function __construct(array $body, int $status, $headers = [])
    {
        $this->body = $body;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->status ?? 500;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return json_encode($this->body);
    }


    public function getHeaders()
    {
        return $this->headers;
    }
}