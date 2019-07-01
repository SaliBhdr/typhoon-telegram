<?php
/*** User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;


use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\MethodAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Traits\HasFile;


class GetFile extends MethodAbstract
{

    use HasFile;

    public function method() : string
    {
        return 'getFile';
    }

    protected function getRequiredParams() : array
    {
        return [
            'file_id' => $this->fileId,
        ];
    }

    protected function addOptionalParams() : void
    {
        return;
    }

    protected function requiredParams() : array
    {
        return ['file_id'];
    }

}