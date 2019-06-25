<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:46 PM
 */

namespace SaliBhdr\TyphoonTelegram\Api\Interfaces;


interface GetFileInterface
{

    public function fileId(string $fileId);
    public function getFileId() : ?string;

}