<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 2:46 PM
 */

namespace Salibhdr\TyphoonTelegram\Api\Interfaces;


interface GetFileIdInterface
{

    public function fileId(string $fileId);
    public function getFileId() : ?string;

}