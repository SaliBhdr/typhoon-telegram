<?php
/**
 * Created by PhpStorm.
 * User: s.bahador
 * Date: 6/30/2019
 * Time: 1:04 AM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits;


trait HasFile
{
    protected $fileId;


    public function fileId(string $fileId)
    {
        $this->fileId = $fileId;

        return $this;
    }
}