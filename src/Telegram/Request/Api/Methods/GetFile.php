<?php
/*** User: Salar Bahador
 * Date: 4/26/2019
 * Time: 4:22 PM
 */

namespace SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Methods;


use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Abstracts\GetAbstract;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Api\Interfaces\GetFileInterface;

class GetFile extends GetAbstract implements GetFileInterface
{

    protected $fileId;

    public function method(): string
    {
        return 'getFile';
    }



    protected function addParams(): void
    {
        $this->params = [
            'file_id' => $this->getFileId(),
        ];
    }

    protected function addOptionalParams(): void {}

    protected function requiredParams(): array
    {
        return ['file_id'];
    }

    /**
     * @param mixed $fileId
     * @return GetFile
     */
    public function fileId(string $fileId)
    {
        $this->fileId = $fileId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFileId(): ?string
    {
        return $this->fileId;
    }
}