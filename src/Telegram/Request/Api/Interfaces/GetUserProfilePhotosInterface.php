<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:46 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Interfaces;


interface GetUserProfilePhotosInterface
{

    public function userId(int $userId);
    public function getUserId() : ?int;

    //optionals ->
    public function offset(int $offset);
    public function getOffset() : ?int;

    public function limit(int $limit);
    public function getLimit() : ?int;
}